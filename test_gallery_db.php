<?php

/**
 * Direct database test for gallery update
 * Visit: /unipulse/test_gallery_db.php (while logged in)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/Core/init.php';

if (!AuthService::isLoggedIn()) {
    die('Not authenticated');
}

$u = AuthService::getCurrentUser();
echo "<h1>Gallery Database Test</h1>";
echo "<p><strong>User ID:</strong> {$u['id']}</p>";
echo "<p><strong>User Type:</strong> {$u['type']}</p>";

$userId = $u['id'];
$userType = $u['type'] ?? 'university';
$tableName = ($userType === 'public') ? 'public_users' : 'university_users';

echo "<p><strong>Table:</strong> $tableName</p>";

// Test 1: Check if user exists
echo "<h2>Test 1: Check if user exists</h2>";
$db = new Database();
$conn = $db->connect();

$checkQuery = "SELECT id, email FROM $tableName WHERE id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->execute([$userId]);
$user = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p style='color: green;'>✓ User found: ID={$user['id']}, Email={$user['email']}</p>";
} else {
    echo "<p style='color: red;'>✗ User NOT found!</p>";
}

// Test 2: Check gallery column
echo "<h2>Test 2: Check gallery column</h2>";
$checkColQuery = "SHOW COLUMNS FROM $tableName LIKE 'gallery'";
$checkColStmt = $conn->prepare($checkColQuery);
$checkColStmt->execute();
$column = $checkColStmt->fetch(PDO::FETCH_ASSOC);

if ($column) {
    echo "<p style='color: green;'>✓ Gallery column exists: Type=" . $column['Type'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ Gallery column NOT found!</p>";
}

// Test 3: Try to UPDATE with test data
echo "<h2>Test 3: Try to UPDATE with test data</h2>";
$testData = json_encode([
    'id' => time(),
    'title' => 'Test Album ' . date('H:i:s'),
    'description' => 'Test Description',
    'images' => ['data:image/png;base64,iVBORw0KGgo=']
]);

echo "<p>Test data: " . htmlspecialchars($testData) . "</p>";
echo "<p>Data length: " . strlen($testData) . " bytes</p>";

$updateQuery = "UPDATE $tableName SET gallery = ? WHERE id = ?";
$updateStmt = $conn->prepare($updateQuery);

try {
    $result = $updateStmt->execute([$testData, $userId]);
    $rowCount = $updateStmt->rowCount();

    echo "<p style='color: green;'>✓ UPDATE executed successfully</p>";
    echo "<p>Execute result: " . ($result ? 'true' : 'false') . "</p>";
    echo "<p>Rows affected: $rowCount</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ UPDATE failed: " . $e->getMessage() . "</p>";
}

// Test 4: Read back the data
echo "<h2>Test 4: Read back the data</h2>";
$selectQuery = "SELECT gallery FROM $tableName WHERE id = ?";
$selectStmt = $conn->prepare($selectQuery);
$selectStmt->execute([$userId]);
$row = $selectStmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $gallery = $row['gallery'];
    echo "<p>Raw gallery value from DB:</p>";
    echo "<pre>" . htmlspecialchars(var_export($gallery, true)) . "</pre>";

    if ($gallery) {
        echo "<p style='color: green;'>✓ Data is in database!</p>";
        echo "<p>Length: " . strlen($gallery) . " bytes</p>";
        $decoded = json_decode($gallery, true);
        echo "<p>Decoded: " . htmlspecialchars(json_encode($decoded)) . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Gallery column is NULL/empty</p>";
    }
} else {
    echo "<p style='color: red;'>✗ No row found after update!</p>";
}

// Test 5: Direct SQL to verify
echo "<h2>Test 5: Direct SQL verification</h2>";
$verifyQuery = "SELECT id, CHAR_LENGTH(gallery) as gallery_length, LEFT(gallery, 50) as gallery_preview FROM $tableName WHERE id = ?";
$verifyStmt = $conn->prepare($verifyQuery);
$verifyStmt->execute([$userId]);
$verifyRow = $verifyStmt->fetch(PDO::FETCH_ASSOC);

if ($verifyRow) {
    echo "<pre>" . htmlspecialchars(json_encode($verifyRow, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p>No row found</p>";
}

echo "<hr>";
echo "<p><a href='/unipulse/public/user/profile'>Back to Profile</a></p>";
