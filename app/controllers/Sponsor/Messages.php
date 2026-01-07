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
        
        // Get all messages for this sponsor
        $messages = $message->getUserMessages($currentUser['id'], 'sponsor', 'received');
        $unreadCount = $message->getUnreadCount($currentUser['id'], 'sponsor');
        
        // Prepare data for view
        $data = [
            'user' => $currentUser,
            'messages' => $messages,
            'unread_count' => $unreadCount,
            'page_title' => 'Messages'
        ];
        
        parent::view('Sponsor/messages', $data);
    }
    //Newly added method for dashboard
    public function index() {
    $currentUser = AuthService::getCurrentUser();
    if (!$currentUser || $currentUser['type'] !== 'sponsor') {
        header('Location: /unipulse/public/signin');
        exit();
    }
    
    // Get sponsor statistics
    $sponsorshipModel = new Sponsorship();
    $donationModel = new Donation();
    
    $data = [
        'user' => $currentUser,
        'recent_messages' => $recentMessages,
        'unread_count' => $unreadCount,
        'stats' => [
            'total_sponsorships' => $sponsorshipModel->getCountBySponsor($currentUser['id']),
            'active_sponsorships' => $sponsorshipModel->getActiveBySponsor($currentUser['id']),
            'total_donations' => $donationModel->getTotalBySponsor($currentUser['id']),
            'upcoming_events' => $sponsorshipModel->getUpcomingEvents($currentUser['id'])
        ],
        'page_title' => 'Dashboard'
    ];
    
    $this->view('Sponsor/dashboard', $data);
    } 
    //close newly added method
    
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