<?php
// Test script to check comments moderation functionality
require_once 'app/Core/init.php';

echo "<h1>Comments Moderation Test</h1>";

// Test database connection
try {
    $db = new Database();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test if comments table exists
try {
    $pdo = $db->getPDO();
    $stmt = $pdo->query("SHOW TABLES LIKE 'comments'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Comments table exists</p>";
        
        // Test if we can fetch comments
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM comments");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total comments in database: " . $result['count'] . "</p>";
        
        // Show sample comment structure
        $stmt = $pdo->query("DESCRIBE comments");
        echo "<h3>Comments Table Structure:</h3>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li><strong>" . $row['Field'] . "</strong> - " . $row['Type'] . "</li>";
        }
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>✗ Comments table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking comments table: " . $e->getMessage() . "</p>";
}

// Test routing
echo "<h3>Routing Test:</h3>";
echo "<p><a href='/moderator/comments_moderation' target='_blank'>Test Comments Moderation Page</a></p>";
echo "<p><a href='/moderator/dashboard' target='_blank'>Test Moderator Dashboard</a></p>";

// Test session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h3>Session Information:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p>Logged in as User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No user session found - you may need to log in as a moderator</p>";
}

echo "<p><small>Test completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>