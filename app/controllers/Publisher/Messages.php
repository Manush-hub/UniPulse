<?php

class PublisherMessages extends Controller {
    
    public function index($a = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        try {
            $message = new Message();
            
            // Get all conversations for this publisher
            $conversations = $message->getConversations($currentUser['id'], 'publisher');
            
            // Get unread count
            $unreadCount = $message->getUnreadCount($currentUser['id'], 'publisher');
            
            // Get publisher details to get their university
            $publisherModel = new Publisher();
            $publisherData = $publisherModel->findById($currentUser['id']);
            
            // Get available sponsors
            $sponsorModel = new Sponsor();
            $availableSponsors = $sponsorModel->getAllSponsors();
            
            // Get moderators for publisher's university
            $moderatorModel = new Moderator();
            $availableModerators = [];
            if ($publisherData && !empty($publisherData->university)) {
                $availableModerators = $moderatorModel->getByUniversity($publisherData->university);
            }
            
            $data = [
                'user' => $currentUser,
                'conversations' => $conversations,
                'unread_count' => $unreadCount,
                'available_sponsors' => $availableSponsors,
                'available_moderators' => $availableModerators,
                'page_title' => 'Messages'
            ];
            
            parent::view('Publisher/messages', $data);
            
        } catch (Exception $e) {
            error_log("Error in PublisherMessages::index: " . $e->getMessage());
            
            $data = [
                'user' => $currentUser,
                'conversations' => [],
                'unread_count' => 0,
                'available_sponsors' => [],
                'available_moderators' => [],
                'page_title' => 'Messages',
                'error' => 'Failed to load messages'
            ];
            
            parent::view('Publisher/messages', $data);
        }
    }
    
    /**
     * Get conversation messages via AJAX
     */
    public function conversation($contactId = '', $contactType = '') {
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
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
                'publisher', 
                $contactId, 
                $contactType
            );
            
            // Mark all unread messages in this conversation as read
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg->is_read && $msg->to_user_id == $currentUser['id']) {
                        $message->markAsRead($msg->id, $currentUser['id'], 'publisher');
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
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
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
                'from_user_type' => 'publisher',
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
    
    public function details($messageId = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            // Check if this is an AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($messageId)) {
            // Check if this is an AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Message ID is required']);
                exit();
            }
            header('Location: /unipulse/public/publisher/messages');
            exit();
        }
        
        try {
            $message = new Message();
            $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
            
            if (!$messageData) {
                // Check if this is an AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Message not found']);
                    exit();
                }
                header('Location: /unipulse/public/publisher/messages?error=Message not found');
                exit();
            }
            
            // Check if this is an AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                // Add current user ID to help determine if this is a sent or received message
                $messageData->current_user_id = $currentUser['id'];
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $messageData
                ]);
                exit();
            }
            
            // Mark as read if it's a received message (only for non-AJAX requests)
            if ($messageData->to_user_id == $currentUser['id'] && $messageData->to_user_type == 'publisher' && !$messageData->is_read) {
                $message->markAsRead($messageId, $currentUser['id'], 'publisher');
            }
            
            $data = [
                'user' => $currentUser,
                'message' => $messageData,
                'page_title' => 'Message Details - ' . htmlspecialchars($messageData->subject)
            ];
            
            parent::view('Publisher/message-details', $data);
            
        } catch (Exception $e) {
            error_log("Error in PublisherMessages::details: " . $e->getMessage());
            
            // Check if this is an AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to load message']);
                exit();
            }
            
            header('Location: /unipulse/public/publisher/messages?error=Failed to load message');
            exit();
        }
    }
    
    public function edit($messageId = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($messageId)) {
            header('Location: /unipulse/public/publisher/messages');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditMessage($messageId, $currentUser);
        } else {
            $this->showEditForm($messageId, $currentUser);
        }
    }
    
    private function showEditForm($messageId, $currentUser) {
        try {
            $message = new Message();
            $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
            
            if (!$messageData) {
                header('Location: /unipulse/public/publisher/messages?error=Message not found');
                exit();
            }
            
            // Check if user can edit this message
            if (!$message->canEditMessage($messageId, $currentUser['id'], 'publisher')) {
                header('Location: /unipulse/public/publisher/messages?error=Cannot edit this message');
                exit();
            }
            
            // Get full publisher data including society_name
            $publisher = new Publisher();
            $publisherData = $publisher->findById($currentUser['id']);
            
            // Merge current user with full publisher data
            $fullUser = $currentUser;
            if ($publisherData) {
                $fullUser['society_name'] = $publisherData->society_name;
                $fullUser['university'] = $publisherData->university;
                $fullUser['faculty'] = $publisherData->faculty;
            }
            
            $data = [
                'user' => $fullUser,
                'message' => $messageData,
                'page_title' => 'Edit Message - ' . htmlspecialchars($messageData->subject)
            ];
            
            parent::view('Publisher/edit-message', $data);
            
        } catch (Exception $e) {
            error_log("Error in PublisherMessages::showEditForm: " . $e->getMessage());
            header('Location: /unipulse/public/publisher/messages?error=Failed to load message for editing');
            exit();
        }
    }
    
    private function handleEditMessage($messageId, $currentUser) {
        // Clean any output buffer completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers before any output
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            // Get form data
            $subject = trim($_POST['subject'] ?? '');
            $messageContent = trim($_POST['message'] ?? '');
            
            // Validation
            if (empty($subject) || empty($messageContent)) {
                $this->sendJsonResponse(false, 'Subject and message are required');
                return;
            }
            
            $message = new Message();
            $result = $message->updateMessage($messageId, $currentUser['id'], 'publisher', $subject, $messageContent);
            
            if ($result['success']) {
                $this->sendJsonResponse(true, $result['message']);
            } else {
                $this->sendJsonResponse(false, $result['message']);
            }
            
        } catch (Exception $e) {
            error_log('Exception in handleEditMessage: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'An error occurred while updating the message.');
        }
    }
    
    public function canEdit($messageId = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        if (empty($messageId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message ID is required']);
            exit();
        }
        
        try {
            $message = new Message();
            $canEdit = $message->canEditMessage($messageId, $currentUser['id'], 'publisher');
            
            echo json_encode(['success' => true, 'canEdit' => $canEdit]);
            
        } catch (Exception $e) {
            error_log('Exception in canEdit: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
    }
    
    public function delete($messageId = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($messageId)) {
            header('Location: /unipulse/public/publisher/messages');
            exit();
        }
        
        // Clean any output buffer completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers before any output
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $message = new Message();
            
            // First check if the message exists and belongs to this user
            $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
            
            if (!$messageData) {
                $this->sendJsonResponse(false, 'Message not found or access denied');
                return;
            }
            
            // Check if message can be deleted (only unread messages sent by this publisher)
            if ($messageData->from_user_id != $currentUser['id'] || $messageData->from_user_type != 'publisher') {
                $this->sendJsonResponse(false, 'You can only delete messages you sent');
                return;
            }
            
            if ($messageData->is_read) {
                $this->sendJsonResponse(false, 'Cannot delete messages that have been read');
                return;
            }
            
            // Perform the deletion
            $result = $message->deleteMessage($messageId, $currentUser['id'], 'publisher');
            
            if ($result) {
                $this->sendJsonResponse(true, 'Message deleted successfully');
            } else {
                $this->sendJsonResponse(false, 'Failed to delete message');
            }
            
        } catch (Exception $e) {
            error_log('Exception in deleteMessage: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'An error occurred while deleting the message.');
        }
    }

    public function markRead($messageId = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        if (empty($messageId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Message ID is required']);
            exit();
        }
        
        try {
            $message = new Message();
            
            // Get the message to verify it exists and is for this user
            $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
            
            if (!$messageData) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Message not found']);
                exit();
            }
            
            // Only mark as read if this is a received message for this user
            if ($messageData->to_user_id == $currentUser['id'] && $messageData->to_user_type == 'publisher') {
                $result = $message->markAsRead($messageId, $currentUser['id'], 'publisher');
                
                if ($result) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to mark message as read']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Cannot mark this message as read']);
            }
            
        } catch (Exception $e) {
            error_log('Error in markRead: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
        
        exit();
    }

    public function unreadCount($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            $message = new Message();
            $count = $message->getUnreadCount($currentUser['id'], 'publisher');
            echo json_encode(['success' => true, 'count' => $count]);
        } catch (Exception $e) {
            error_log('Get unread count error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to get unread count']);
        }
        exit();
    }

    private function sendJsonResponse($success, $message) {
        $response = ['success' => $success, 'message' => $message];
        echo json_encode($response);
        exit();
    }
}