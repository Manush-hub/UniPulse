<?php
// Direct test of message details functionality

require_once '../app/Core/init.php';

header('Content-Type: application/json');

try {
    // Check authentication
    $currentUser = AuthService::getCurrentUser();
    
    if (!$currentUser || $currentUser['type'] !== 'publisher') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized - not logged in as publisher']);
        exit();
    }
    
    // Get message ID from URL parameter
    $messageId = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (empty($messageId)) {
        echo json_encode(['success' => false, 'message' => 'Message ID is required']);
        exit();
    }
    
    // Try to get the message
    $message = new Message();
    $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
    
    if (!$messageData) {
        echo json_encode(['success' => false, 'message' => 'Message not found or access denied']);
        exit();
    }
    
    // Add current user ID to help determine if this is a sent or received message
    $messageData->current_user_id = $currentUser['id'];
    
    echo json_encode([
        'success' => true,
        'message' => $messageData,
        'debug_info' => [
            'user_id' => $currentUser['id'],
            'user_type' => $currentUser['type'],
            'message_id' => $messageId
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>