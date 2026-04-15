<?php

class ModeratorMessages extends Controller {
    
    public function index($a = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // --- Step 1: fetch moderator profile (always needed for header) ---
        $moderatorData = null;
        try {
            $moderatorModel = new Moderator();
            $moderatorData  = $moderatorModel->findById($currentUser['id']);
        } catch (Exception $e) {
            error_log("ModeratorMessages: could not load moderator profile: " . $e->getMessage());
        }

        // --- Step 2: fetch available publishers for this moderator's university ---
        $availablePublishers = [];
        try {
            if ($moderatorData && !empty($moderatorData->university)) {
                $publisherModel      = new Publisher();
                $availablePublishers = $publisherModel->getApprovedByUniversity($moderatorData->university);
                if (!$availablePublishers) {
                    $availablePublishers = [];
                }
            }
        } catch (Exception $e) {
            error_log("ModeratorMessages: could not load publishers: " . $e->getMessage());
        }

        // --- Step 3: fetch all active admins (pinned for every moderator) ---
        $availableAdmins = [];
        try {
            $adminModel      = new Admin();
            $availableAdmins = $adminModel->getActiveAdmins();
            if (!$availableAdmins) {
                $availableAdmins = [];
            }
        } catch (Exception $e) {
            error_log("ModeratorMessages: could not load admins: " . $e->getMessage());
        }

        // --- Step 4: fetch conversations and unread count ---
        $conversations = [];
        $unreadCount   = 0;
        try {
            $message       = new Message();
            $conversations = $message->getConversations($currentUser['id'], 'moderator');
            $unreadCount   = $message->getUnreadCount($currentUser['id'], 'moderator');
            if (!$conversations) {
                $conversations = [];
            }
        } catch (Exception $e) {
            error_log("ModeratorMessages: could not load conversations: " . $e->getMessage());
        }

        $data = [
            'user'                => $currentUser,
            'moderator'           => $moderatorData,
            'conversations'       => $conversations,
            'unread_count'        => $unreadCount,
            'available_publishers' => $availablePublishers,
            'available_admins'    => $availableAdmins,
            'page_title'          => 'Messages',
        ];

        parent::view('Moderator/messages', $data);
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
