<?php

class UserMessages extends Controller
{
    public function index($a = '', $b = '', $c = '')
    {
        $currentUser = AuthService::getCurrentUser();

        if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'], true)) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        try {
            $message = new Message();

            $conversations = $message->getConversations((int)$currentUser['id'], (string)$currentUser['type']);
            $unreadCount = $message->getUnreadCount((int)$currentUser['id'], (string)$currentUser['type']);

            $publisherModel = new Publisher();
            $availablePublishers = $publisherModel->getAllForMessaging();

            $data = [
                'user' => $currentUser,
                'conversations' => is_array($conversations) ? $conversations : [],
                'unread_count' => (int)$unreadCount,
                'available_publishers' => is_array($availablePublishers) ? $availablePublishers : [],
                'page_title' => 'Messages'
            ];

            parent::view('User/messages', $data);
        } catch (Exception $e) {
            error_log('Error in UserMessages::index: ' . $e->getMessage());

            $data = [
                'user' => $currentUser,
                'conversations' => [],
                'unread_count' => 0,
                'available_publishers' => [],
                'page_title' => 'Messages',
                'error' => 'Failed to load messages'
            ];

            parent::view('User/messages', $data);
        }
    }

    public function conversation($contactId = '', $contactType = '')
    {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'], true)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            if (empty($contactId) || empty($contactType)) {
                echo json_encode(['success' => false, 'message' => 'Contact ID and type are required']);
                exit();
            }

            $message = new Message();
            $messages = $message->getConversationMessages(
                (int)$currentUser['id'],
                (string)$currentUser['type'],
                $contactId,
                $contactType
            );

            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg->is_read && (int)$msg->to_user_id === (int)$currentUser['id']) {
                        $message->markAsRead((int)$msg->id, (int)$currentUser['id'], (string)$currentUser['type']);
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'messages' => $messages ?: []
            ]);
        } catch (Exception $e) {
            error_log('User conversation error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load conversation: ' . $e->getMessage()
            ]);
        }

        exit();
    }

    public function send($a = '', $b = '', $c = '')
    {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'], true)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit();
            }

            $toUserId = trim((string)($_POST['to_user_id'] ?? ''));
            $toUserType = trim((string)($_POST['to_user_type'] ?? ''));
            $subject = trim((string)($_POST['subject'] ?? 'Message'));
            $messageContent = trim((string)($_POST['message'] ?? ''));

            if ($toUserId === '' || $toUserType === '' || $messageContent === '') {
                echo json_encode(['success' => false, 'message' => 'Recipient and message are required']);
                exit();
            }

            $message = new Message();
            $messageId = $message->sendMessage([
                'from_user_id' => (int)$currentUser['id'],
                'from_user_type' => (string)$currentUser['type'],
                'to_user_id' => $toUserId,
                'to_user_type' => $toUserType,
                'subject' => $subject,
                'message' => $messageContent
            ]);

            if ($messageId) {
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
            error_log('User send message error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }

        exit();
    }
}
