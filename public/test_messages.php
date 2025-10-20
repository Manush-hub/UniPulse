<?php

require_once '../app/Core/init.php';

try {
    $message = new Message();
    
    echo "Testing message sending...\n";
    
    $data = [
        'from_user_id' => 4,
        'from_user_type' => 'publisher',
        'to_user_id' => 1,
        'to_user_type' => 'sponsor',
        'subject' => 'Test Message',
        'message' => 'This is a test message from CLI'
    ];
    
    $result = $message->sendMessage($data);
    
    if ($result) {
        echo "SUCCESS! Message sent with ID: " . $result . "\n";
    } else {
        echo "FAILED to send message\n";
    }
    
    // Test retrieving messages for sponsor
    echo "\nTesting message retrieval for sponsor (user_id=1)...\n";
    $messages = $message->getUserMessages(1, 'sponsor', 'received');
    echo "Found " . count($messages) . " messages\n";
    
    if (count($messages) > 0) {
        $latest = $messages[0];
        echo "Latest message subject: " . $latest->subject . "\n";
        echo "From user: " . $latest->from_user_id . " (" . $latest->from_user_type . ")\n";
    }
    
    // Let's also check what's in the messages table
    echo "\nChecking all messages in database...\n";
    $allMessages = $message->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 3");
    foreach ($allMessages as $msg) {
        echo "ID: " . $msg->id . " | From: " . $msg->from_user_id . "(" . $msg->from_user_type . ") | To: " . $msg->to_user_id . "(" . $msg->to_user_type . ") | Subject: " . $msg->subject . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}