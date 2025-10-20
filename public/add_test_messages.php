<?php
// Add test message for debugging

require_once '../app/Core/init.php';

try {
    // Check current user
    $currentUser = AuthService::getCurrentUser();
    if (!$currentUser || $currentUser['type'] !== 'publisher') {
        echo "Please login as a publisher first<br>";
        exit;
    }
    
    echo "<h1>Add Test Message</h1>";
    echo "Current user: " . $currentUser['email'] . " (ID: " . $currentUser['id'] . ")<br>";
    
    // Check if we have any sponsors
    $db = new Database();
    $sponsors = $db->query("SELECT * FROM sponsors LIMIT 5");
    
    if (empty($sponsors)) {
        echo "❌ No sponsors found in database. Creating a test sponsor...<br>";
        
        // Create a test sponsor
        $testSponsor = [
            'company_name' => 'Test Corp',
            'email' => 'test@testcorp.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'contact_person' => 'John Doe',
            'phone' => '1234567890',
            'industry' => 'Technology',
            'website' => 'https://testcorp.com'
        ];
        
        $result = $db->query("INSERT INTO sponsors (company_name, email, password, contact_person, phone, industry, website) VALUES (?, ?, ?, ?, ?, ?, ?)", 
            array_values($testSponsor));
        
        if ($result) {
            $sponsorId = $db->lastInsertId();
            echo "✓ Test sponsor created with ID: " . $sponsorId . "<br>";
        } else {
            echo "❌ Failed to create test sponsor<br>";
            exit;
        }
    } else {
        $sponsorId = $sponsors[0]->id;
        echo "✓ Using existing sponsor: " . $sponsors[0]->company_name . " (ID: " . $sponsorId . ")<br>";
    }
    
    // Create a test message
    $message = new Message();
    
    // Add a sent message
    $sentMessageData = [
        'from_user_id' => $currentUser['id'],
        'from_user_type' => 'publisher',
        'to_user_id' => $sponsorId,
        'to_user_type' => 'sponsor',
        'subject' => 'Test Message - Sponsorship Inquiry',
        'message' => 'Hello! This is a test message for debugging the modal popup functionality. We are interested in potential sponsorship opportunities for our upcoming tech conference.'
    ];
    
    $sentMessageId = $message->sendMessage($sentMessageData);
    if ($sentMessageId) {
        echo "✓ Test sent message created with ID: " . $sentMessageId . "<br>";
    } else {
        echo "❌ Failed to create sent message<br>";
    }
    
    // Add a received message
    $receivedMessageData = [
        'from_user_id' => $sponsorId,
        'from_user_type' => 'sponsor',
        'to_user_id' => $currentUser['id'],
        'to_user_type' => 'publisher',
        'subject' => 'Re: Sponsorship Inquiry - Interested!',
        'message' => 'Thank you for reaching out! We are very interested in sponsoring your tech conference. Please let us know more details about the sponsorship packages available.'
    ];
    
    $receivedMessageId = $message->sendMessage($receivedMessageData);
    if ($receivedMessageId) {
        echo "✓ Test received message created with ID: " . $receivedMessageId . "<br>";
    } else {
        echo "❌ Failed to create received message<br>";
    }
    
    echo "<br><h2>Test Complete!</h2>";
    echo "You can now test the modal popup with these message IDs:<br>";
    echo "- Sent message ID: " . $sentMessageId . "<br>";
    echo "- Received message ID: " . $receivedMessageId . "<br>";
    echo "<br><a href='/unipulse/public/publisher/messages'>Go to Messages Page</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>