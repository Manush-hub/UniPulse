<?php

class AdminMessages extends Controller {

    /**
     * Main chatbox page – list all conversations with moderators
     */
    public function index($a = '', $b = '', $c = '') {
        $currentUser = AuthService::getCurrentUser();

        if (!$currentUser || $currentUser['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        try {
            $messageModel   = new Message();
            $moderatorModel = new Moderator();
            $supportMessageModel = new SupportMessage();

            // All current conversations this admin has
            $conversations = $messageModel->getConversations($currentUser['id'], 'admin');

            // Unread count
            $unreadCount = $messageModel->getUnreadCount($currentUser['id'], 'admin');

            // All active moderators the admin can contact
            $availableModerators = $moderatorModel->where(['is_active' => 1]);
            if (!$availableModerators) {
                $availableModerators = [];
            }

            $contactReaches = [];
            try {
                $contactReaches = $supportMessageModel->getRecentForAdmin(15);
            } catch (Exception $e) {
                error_log('AdminMessages::index contact reaches error: ' . $e->getMessage());
                $contactReaches = [];
            }

            $data = [
                'user'                => $currentUser,
                'conversations'       => $conversations,
                'unread_count'        => $unreadCount,
                'available_moderators' => $availableModerators,
                'contact_reaches'     => $contactReaches,
                'page_title'          => 'Messages',
            ];

            parent::view('Admin/messages', $data);

        } catch (Exception $e) {
            error_log("AdminMessages::index error: " . $e->getMessage());

            $data = [
                'user'                => $currentUser,
                'conversations'       => [],
                'unread_count'        => 0,
                'available_moderators' => [],
                'contact_reaches'     => [],
                'page_title'          => 'Messages',
                'error'               => 'Failed to load messages',
            ];

            parent::view('Admin/messages', $data);
        }
    }

    /**
     * AJAX – fetch messages for a specific conversation
     */
    public function conversation($contactId = '', $contactType = '') {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            if (empty($contactId) || empty($contactType)) {
                echo json_encode(['success' => false, 'message' => 'Contact ID and type are required']);
                exit();
            }

            $messageModel = new Message();
            $messages = $messageModel->getConversationMessages(
                $currentUser['id'], 'admin',
                $contactId, $contactType
            );

            // Mark received messages as read
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (!$msg->is_read && $msg->to_user_id == $currentUser['id']) {
                        $messageModel->markAsRead($msg->id, $currentUser['id'], 'admin');
                    }
                }
            }

            echo json_encode([
                'success'  => true,
                'messages' => $messages ?: [],
            ]);

        } catch (Exception $e) {
            error_log("AdminMessages::conversation error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load conversation: ' . $e->getMessage()]);
        }
        exit();
    }

    /**
     * AJAX – send a message to a moderator
     */
    public function send($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit();
            }

            $toUserId   = trim($_POST['to_user_id']   ?? '');
            $toUserType = trim($_POST['to_user_type'] ?? 'moderator');
            $subject    = trim($_POST['subject']      ?? 'Message from Admin');
            $content    = trim($_POST['message']      ?? '');

            if (empty($toUserId) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Recipient and message are required']);
                exit();
            }

            $messageModel = new Message();
            $messageId = $messageModel->sendMessage([
                'from_user_id'   => $currentUser['id'],
                'from_user_type' => 'admin',
                'to_user_id'     => $toUserId,
                'to_user_type'   => $toUserType,
                'subject'        => $subject,
                'message'        => $content,
            ]);

            if ($messageId) {
                $newMessage = $messageModel->getMessageById($messageId);
                echo json_encode([
                    'success'      => true,
                    'message'      => 'Message sent',
                    'message_data' => $newMessage,
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }

        } catch (Exception $e) {
            error_log("AdminMessages::send error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()]);
        }
        exit();
    }

    /**
     * AJAX – unread message count badge
     */
    public function unreadCount($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            $messageModel = new Message();
            $count = $messageModel->getUnreadCount($currentUser['id'], 'admin');
            echo json_encode(['success' => true, 'count' => $count]);

        } catch (Exception $e) {
            error_log("AdminMessages::unreadCount error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to get count']);
        }
        exit();
    }
}
