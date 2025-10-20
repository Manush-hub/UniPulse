<?php
// Test the Moderator Comments functionality after fixing findById issue
require_once 'app/Core/init.php';

echo "<h1>Moderator Comments Controller Test</h1>";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up a test session (simulate logged in moderator)
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Moderator';
$_SESSION['logged_in'] = true;

echo "<h3>Testing Moderator Model Methods:</h3>";

try {
    $moderator = new Moderator();
    
    // Test the find method (was causing the error)
    echo "<p>Testing Moderator->find() method...</p>";
    $testResult = $moderator->find(1);
    if ($testResult) {
        echo "<p style='color: green;'>✓ Moderator->find() method works correctly</p>";
        echo "<p>Found moderator: " . ($testResult['full_name'] ?? 'No name') . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No moderator found with ID 1 (this is normal if no moderators exist)</p>";
    }
    
    // Test the Model trait methods
    echo "<p>Testing available Model methods:</p>";
    $reflection = new ReflectionClass($moderator);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    echo "<ul>";
    foreach ($methods as $method) {
        if (!$method->isConstructor() && !$method->isDestructor()) {
            echo "<li>" . $method->getName() . "()</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error testing Moderator model: " . $e->getMessage() . "</p>";
}

echo "<h3>Testing Comments Controller Access:</h3>";

// Test if we can instantiate the ModeratorComments controller
try {
    require_once 'app/controllers/Moderator/Comments.php';
    $commentsController = new ModeratorComments();
    echo "<p style='color: green;'>✓ ModeratorComments controller can be instantiated</p>";
    
    // Test if the index method can be called
    echo "<p>Testing index() method...</p>";
    ob_start();
    $commentsController->index();
    $output = ob_get_clean();
    
    if (strlen($output) > 0) {
        echo "<p style='color: green;'>✓ index() method executed successfully</p>";
        echo "<p>Output length: " . strlen($output) . " characters</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ index() method executed but produced no output</p>";
    }
    
} catch (Error $e) {
    echo "<p style='color: red;'>✗ Error with ModeratorComments controller: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception with ModeratorComments controller: " . $e->getMessage() . "</p>";
}

echo "<h3>Direct URL Tests:</h3>";
echo "<p><a href='http://localhost:8080/moderator/comments_moderation' target='_blank'>Test Comments Moderation Page</a></p>";
echo "<p><a href='http://localhost:8080/moderator/dashboard' target='_blank'>Test Moderator Dashboard</a></p>";

echo "<p><small>Test completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>