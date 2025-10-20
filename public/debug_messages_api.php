<?php
// Debug the messages API endpoint

echo "<h1>Messages API Debug</h1>";

// Check if we can access the database
try {
    require_once '../app/Core/init.php';
    
    echo "<h2>1. Database Connection Test</h2>";
    $message = new Message();
    echo "✓ Message model loaded successfully<br>";
    
    echo "<h2>2. Check Authentication</h2>";
    $currentUser = AuthService::getCurrentUser();
    if ($currentUser) {
        echo "✓ User authenticated: " . $currentUser['type'] . " (ID: " . $currentUser['id'] . ")<br>";
    } else {
        echo "❌ No user authentication found<br>";
        echo "Please login first<br>";
        exit;
    }
    
    echo "<h2>3. Test Getting Messages</h2>";
    $sentMessages = $message->getUserMessages($currentUser['id'], $currentUser['type'], 'sent');
    echo "Sent messages count: " . (is_array($sentMessages) ? count($sentMessages) : 0) . "<br>";
    
    $receivedMessages = $message->getUserMessages($currentUser['id'], $currentUser['type'], 'received');
    echo "Received messages count: " . (is_array($receivedMessages) ? count($receivedMessages) : 0) . "<br>";
    
    // Get a sample message ID for testing
    $allMessages = array_merge($sentMessages ?: [], $receivedMessages ?: []);
    if (!empty($allMessages)) {
        $testMessageId = $allMessages[0]->id;
        echo "<h2>4. Test Message Details API</h2>";
        echo "Testing with message ID: " . $testMessageId . "<br>";
        
        // Test the getMessageById method directly
        $messageData = $message->getMessageById($testMessageId, $currentUser['id'], $currentUser['type']);
        if ($messageData) {
            echo "✓ Message data retrieved successfully<br>";
            echo "Subject: " . htmlspecialchars($messageData->subject) . "<br>";
            echo "Sender: " . htmlspecialchars($messageData->sender_name) . "<br>";
            echo "Recipient: " . htmlspecialchars($messageData->recipient_name) . "<br>";
        } else {
            echo "❌ Failed to retrieve message data<br>";
        }
        
        echo "<h2>5. Test AJAX API Call</h2>";
        echo "Testing URL: /unipulse/public/publisher/messages/details/" . $testMessageId . "<br>";
        
        // Create a test AJAX request
        echo "<script>
        function testAPI() {
            fetch('/unipulse/public/publisher/messages/details/" . $testMessageId . "', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                document.getElementById('apiResult').innerHTML = '<pre>' + data + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('apiResult').innerHTML = 'Error: ' + error.message;
            });
        }
        </script>";
        
        echo "<button onclick='testAPI()'>Test API Call</button><br>";
        echo "<div id='apiResult' style='margin-top: 20px; border: 1px solid #ccc; padding: 10px;'></div>";
    } else {
        echo "<h2>4. No Messages Found</h2>";
        echo "❌ No messages found to test with<br>";
        echo "Please create some test messages first<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>