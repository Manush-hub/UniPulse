<?php
// Test the complete fix for comments moderation view loading
require_once 'app/Core/init.php';

echo "<h1>View Loading Test - After Fix</h1>";

// Start session and simulate moderator login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Moderator';
$_SESSION['logged_in'] = true;

echo "<h3>Testing File Structure:</h3>";

// Check if view file exists with correct name
$viewPath = "/Applications/MAMP/htdocs/unipulse/app/views/Moderator/comments_moderation.view.php";
if (file_exists($viewPath)) {
    echo "<p style='color: green;'>✓ View file exists at correct path: comments_moderation.view.php</p>";
} else {
    echo "<p style='color: red;'>✗ View file not found at expected path</p>";
}

// Test the controller view resolution logic
echo "<h3>Testing View Resolution Logic:</h3>";

// Simulate the Controller's view resolution
$viewRole = 'Moderator';
$name = 'comments_moderation';

// Try role-specific view first (this should work now)
$filename = "../app/views/{$viewRole}/" . $name . ".view.php";
echo "<p>Checking role-specific path: <code>$filename</code></p>";

if (file_exists($filename)) {
    echo "<p style='color: green;'>✓ Role-specific view found!</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Role-specific view not found, would fallback to general views</p>";
    
    // Fallback to general views
    $filename = "../app/views/" . $name . ".view.php";
    echo "<p>Checking fallback path: <code>$filename</code></p>";
    
    if (file_exists($filename)) {
        echo "<p style='color: green;'>✓ Fallback view found!</p>";
    } else {
        echo "<p style='color: red;'>✗ No view found - would show 404</p>";
    }
}

echo "<h3>Testing Controller Methods:</h3>";

try {
    require_once 'app/controllers/Moderator/Comments.php';
    echo "<p style='color: green;'>✓ ModeratorComments controller loaded successfully</p>";
    
    $controller = new ModeratorComments();
    echo "<p style='color: green;'>✓ ModeratorComments controller instantiated successfully</p>";
    
    // Test the view loading (capture output)
    echo "<p>Testing index() method (this should now work)...</p>";
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    if (strlen($output) > 100) {
        echo "<p style='color: green;'>✓ index() method executed successfully!</p>";
        echo "<p>Output length: " . strlen($output) . " characters</p>";
        echo "<p>Output preview: " . htmlspecialchars(substr($output, 0, 200)) . "...</p>";
    } else {
        echo "<p style='color: red;'>✗ index() method produced minimal output (" . strlen($output) . " chars)</p>";
        if ($output) {
            echo "<p>Output: " . htmlspecialchars($output) . "</p>";
        }
    }
    
} catch (Error $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

echo "<h3>Direct Navigation Test:</h3>";
echo "<p><a href='http://localhost:8080/moderator/comments_moderation' target='_blank' style='color: #1E3A8A; font-weight: bold;'>🔗 Test Comments Moderation Page</a></p>";

echo "<p><small>Test completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>