<?php

class ModeratorMessages extends Controller {
    
    public function index($a = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        try {
            $message = new Message();
            
            // Get all conversations for this moderator
            $conversations = $message->getConversations($currentUser['id'], 'moderator');
            
            // Get unread count
            $unreadCount = $message->getUnreadCount($currentUser['id'], 'moderator');
            
            // Get moderator details to get their university
            $moderatorModel = new Moderator();
            $moderatorData = $moderatorModel->findById($currentUser['id']);
            
            // Get available publishers from moderator's university
            $publisherModel = new Publisher();
            $availablePublishers = [];
            if ($moderatorData && !empty($moderatorData->university)) {
                error_log("ModeratorMessages: Moderator university = " . $moderatorData->university);
                $availablePublishers = $publisherModel->getApprovedByUniversity($moderatorData->university);
                error_log("ModeratorMessages: Found " . (is_array($availablePublishers) ? count($availablePublishers) : 0) . " publishers");
                if (is_array($availablePublishers) && count($availablePublishers) > 0) {
                    error_log("ModeratorMessages: Publishers = " . json_encode(array_map(function($p) { 
                        return ['id' => $p->id, 'name' => $p->society_name, 'approval' => $p->approval_status ?? 'unknown']; 
                    }, $availablePublishers)));
                }
            } else {
                error_log("ModeratorMessages: No moderator data or university not set");
            }
            
            $data = [
                'user' => $currentUser,
                'moderator' => $moderatorData,
                'conversations' => $conversations,
                'unread_count' => $unreadCount,
                'available_publishers' => $availablePublishers,
                'page_title' => 'Messages'
            ];
            
            parent::view('Moderator/messages', $data);
            
        } catch (Exception $e) {
            error_log("Error in ModeratorMessages::index: " . $e->getMessage());
            
            $data = [
                'user' => $currentUser,
                'moderator' => $moderatorData ?? null,
                'conversations' => [],
                'unread_count' => 0,
                'available_publishers' => [],
                'page_title' => 'Messages',
                'error' => 'Failed to load messages'
            ];
            
            parent::view('Moderator/messages', $data);
        }
    }
    
    /**
     * Get conversation messages via AJAX
     */
    public function conversation($contactId = '', $contactType = '') {
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'moderator') {
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
                'moderator', 
                $contactId, 
                $contactType
            );
            
            // Mark all unread messages in this conversation as read
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg->is_read && $msg->to_user_id == $currentUser['id']) {
                        $message->markAsRead($msg->id, $currentUser['id'], 'moderator');
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
            if (!$currentUser || $currentUser['type'] !== 'moderator') {
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
                'from_user_type' => 'moderator',
                'to_user_id' => $toUserId,
                'to_user_type' => $toUserType,
                'subject' => $subject,
                'message' => $messageContent
            ]);
            
            if ($messageId) {
                // Get the newly created message
                $newMessage = $message->getMessageById($messageId);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent successfully',
                    'message_data' => $newMessage
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
            
        } catch (Exception $e) {
            error_log('Send message error: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to send message: ' . $e->getMessage()
            ]);
        }
        exit();
    }
    
    /**
     * Get unread message count
     */
    public function unreadCount($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'moderator') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $message = new Message();
            $count = $message->getUnreadCount($currentUser['id'], 'moderator');
            
            echo json_encode(['success' => true, 'count' => $count]);
            
        } catch (Exception $e) {
            error_log('Get unread count error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to get unread count']);
        }
        exit();
    }
}
