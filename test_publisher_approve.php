<?php
require_once '../app/Core/config.php';
require_once '../app/Core/Database.php';
require_once '../app/models/Publisher.php';

// Simulate approving a publisher
$publisher = new Publisher();

try {
    // Check if there are any pending publishers
    $pending = $publisher->getAllPending();
    
    echo "<h2>Pending Publishers:</h2>";
    echo "<pre>";
    print_r($pending);
    echo "</pre>";
    
    if (!empty($pending)) {
        $testPublisherId = $pending[0]->id;
        $testModeratorId = 1; // Admin ID
        
        echo "<h3>Testing Approve Method:</h3>";
        echo "Publisher ID: $testPublisherId<br>";
        echo "Moderator ID: $testModeratorId<br><br>";
        
        // Test approve
        $result = $publisher->approve($testPublisherId, $testModeratorId);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Approve method succeeded</p>";
        } else {
            echo "<p style='color: red;'>❌ Approve method failed</p>";
        }
    } else {
        echo "<p>No pending publishers to test with</p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
