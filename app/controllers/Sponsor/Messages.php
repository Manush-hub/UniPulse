<?php

class SponsorMessages extends Controller {
    
    public function index($a = '', $b = '', $c = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $message = new Message();
        
        // Get all conversations for this sponsor
        $conversations = $message->getConversations($currentUser['id'], 'sponsor');
        $unreadCount = $message->getUnreadCount($currentUser['id'], 'sponsor');
        
        // Prepare data for view
        $data = [
            'user' => $currentUser,
            'conversations' => $conversations,
            'unread_count' => $unreadCount,
            'page_title' => 'Messages'
        ];
        
        parent::view('Sponsor/messages', $data);
    }
    
    /**
     * Get conversation messages via AJAX
     */
    public function conversation($contactId = '', $contactType = '') {
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            if (empty($contactId) || empty($contactType)) {
                echo json_encode(['success' => false, 'message' => 'Contact ID and type are required']);
                exit();
            }
            
            $message = new Message();
            $messages = $message->getConversationMessages(
                $currentUser['id'], 
                'sponsor', 
                $contactId, 
                $contactType
            );
            
            // Mark all unread messages in this conversation as read
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg->is_read && $msg->to_user_id == $currentUser['id']) {
                        $message->markAsRead($msg->id, $currentUser['id'], 'sponsor');
                    }
                }
            }
            
            echo json_encode([
                'success' => true, 
                'messages' => $messages ? $messages : []
            ]);
            
        } catch (Exception $e) {
            error_log('Get conversation error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to load conversation: ' . $e->getMessage()
            ]);
        }
        exit();
    }
    
    /**
     * Send a new message in a conversation
     */
    public function send($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit();
            }
            
            $toUserId = trim($_POST['to_user_id'] ?? '');
            $toUserType = trim($_POST['to_user_type'] ?? '');
            $subject = trim($_POST['subject'] ?? 'Message');
            $messageContent = trim($_POST['message'] ?? '');
            
            if (empty($toUserId) || empty($toUserType) || empty($messageContent)) {
                echo json_encode(['success' => false, 'message' => 'Recipient and message are required']);
                exit();
            }
            
            $message = new Message();
            $messageId = $message->sendMessage([
                'from_user_id' => $currentUser['id'],
                'from_user_type' => 'sponsor',
                'to_user_id' => $toUserId,
                'to_user_type' => $toUserType,
                'subject' => $subject,
                'message' => $messageContent
            ]);
            
            if ($messageId) {
                // Get the sent message details
                $sentMessage = $message->getMessageById($messageId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $sentMessage
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
            
        } catch (Exception $e) {
            error_log('Send message error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        exit();
    }
    
    public function details($messageId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($messageId)) {
            header('Location: /unipulse/public/sponsor/messages');
            exit();
        }
        
        $message = new Message();
        $messageData = $message->getMessageById($messageId, $currentUser['id'], 'sponsor');

        if (!$messageData) {
            // Message not found or not authorized
            header('Location: /unipulse/public/sponsor/messages');
            exit();
        }
        
        // Mark as read if it's addressed to this sponsor
        if ($messageData->to_user_id == $currentUser['id'] && $messageData->to_user_type == 'sponsor') {
            $message->markAsRead($messageId, $currentUser['id'], 'sponsor');
        }        // Prepare data for view
        $data = [
            'user' => $currentUser,
            'message' => $messageData,
            'page_title' => 'Message Details'
        ];
        
        parent::view('Sponsor/message-details', $data);
    }
    
    public function reply($messageId = '') {
        // Handle reply form submission
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = trim($_POST['subject'] ?? '');
            $messageContent = trim($_POST['message'] ?? '');
            $originalMessageId = $_POST['original_message_id'] ?? '';
            
            // Validation
            if (empty($subject) || empty($messageContent) || empty($originalMessageId)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit();
            }
            
            // Get original message to find sender
            $message = new Message();
            $originalMessage = $message->getMessageById($originalMessageId, $currentUser['id'], 'sponsor');
            
            if (!$originalMessage) {
                echo json_encode(['success' => false, 'message' => 'Original message not found']);
                exit();
            }
            
            // Send reply
            $replyId = $message->sendMessage([
                'from_user_id' => $currentUser['id'],
                'from_user_type' => 'sponsor',
                'to_user_id' => $originalMessage->from_user_id,
                'to_user_type' => $originalMessage->from_user_type,
                'subject' => $subject,
                'message' => $messageContent
            ]);
            
            if ($replyId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Reply sent successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send reply. Please try again.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }
    
    public function delete($messageId = '') {
        // Handle message deletion
        // Clean any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($messageId)) {
                $message = new Message();
                
                // First check if the message exists and belongs to this user
                $messageData = $message->getMessageById($messageId, $currentUser['id'], 'sponsor');
                
                if (!$messageData) {
                    echo json_encode(['success' => false, 'message' => 'Message not found or access denied']);
                    exit();
                }
                
                // Delete the message
                $result = $message->deleteMessage($messageId, $currentUser['id'], 'sponsor');
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Message not found or you do not have permission to delete it']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method or missing message ID']);
            }
        } catch (Exception $e) {
            error_log('Delete message error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
        }
        exit();
    }
    
    public function markRead($messageId = '') {
        // Mark message as read
        // Clean any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($messageId)) {
                $message = new Message();
                
                // First check if the message exists and is addressed to this user
                $messageData = $message->getMessageById($messageId, $currentUser['id'], 'sponsor');
                
                if (!$messageData) {
                    echo json_encode(['success' => false, 'message' => 'Message not found or access denied']);
                    exit();
                }
                
                // Check if message is already read
                if ($messageData->is_read) {
                    echo json_encode(['success' => true, 'message' => 'Message already marked as read']);
                    exit();
                }
                
                // Mark as read
                $result = $message->markAsRead($messageId, $currentUser['id'], 'sponsor');
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Message not found or already marked as read']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method or missing message ID']);
            }
        } catch (Exception $e) {
            error_log('Mark read error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
        }
        exit();
    }
}