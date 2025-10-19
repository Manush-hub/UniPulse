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
            
            // Get sent messages for the publisher
            $sentMessages = $message->getUserMessages($currentUser['id'], 'publisher', 'sent');
            
            // Get received messages for the publisher  
            $receivedMessages = $message->getUserMessages($currentUser['id'], 'publisher', 'received');
            
            // Get unread count
            $unreadCount = $message->getUnreadCount($currentUser['id'], 'publisher');
            
            $data = [
                'user' => $currentUser,
                'sent_messages' => $sentMessages ?: [],
                'received_messages' => $receivedMessages ?: [],
                'unread_count' => $unreadCount,
                'page_title' => 'Messages'
            ];
            
            parent::view('Publisher/messages', $data);
            
        } catch (Exception $e) {
            error_log("Error in PublisherMessages::index: " . $e->getMessage());
            
            $data = [
                'user' => $currentUser,
                'sent_messages' => [],
                'received_messages' => [],
                'unread_count' => 0,
                'page_title' => 'Messages',
                'error' => 'Failed to load messages'
            ];
            
            parent::view('Publisher/messages', $data);
        }
    }
    
    public function details($messageId = '', $b = '', $c = '') {
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
        
        try {
            $message = new Message();
            $messageData = $message->getMessageById($messageId, $currentUser['id'], 'publisher');
            
            if (!$messageData) {
                header('Location: /unipulse/public/publisher/messages?error=Message not found');
                exit();
            }
            
            // Mark as read if it's a received message
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

    private function sendJsonResponse($success, $message) {
        $response = ['success' => $success, 'message' => $message];
        echo json_encode($response);
        exit();
    }
}