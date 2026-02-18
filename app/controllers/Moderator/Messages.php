<?php

class ModeratorMessages extends Controller {

    public function index($a = '', $b = '', $c = '') {
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $currentUser = AuthService::getCurrentUser();
        $data['user'] = $currentUser;

        // Get moderator data
        $moderatorModel = new Moderator();
        $moderator = $moderatorModel->findById($currentUser['id']);
        $data['moderator'] = $moderator;

        // Get all messages sent by this moderator
        $messageModel = new Message();
        $messages = $messageModel->getUserMessages($currentUser['id'], 'moderator', 'sent');
        $data['messages'] = $messages;

        $this->view('Moderator/messages', $data);
    }

    /**
     * Display form to send a new message to a publisher
     */
    public function compose($publisherId = '', $b = '', $c = '') {
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $currentUser = AuthService::getCurrentUser();
        $data['user'] = $currentUser;

        // Get moderator data
        $moderatorModel = new Moderator();
        $moderator = $moderatorModel->findById($currentUser['id']);
        $data['moderator'] = $moderator;

        // Get list of publishers from moderator's university
        $publisherModel = new Publisher();
        
        if ($publisherId && is_numeric($publisherId)) {
            // Get specific publisher
            $publisher = $publisherModel->getPublisherById($publisherId);
            if ($publisher) {
                $data['selected_publisher'] = $publisher;
            }
        }

        // Get all approved publishers from moderator's university
        $publishers = $publisherModel->getApprovedByUniversity($moderator->university);
        $data['publishers'] = $publishers;

        $this->view('Moderator/send-message', $data);
    }

    /**
     * Handle sending message to publisher
     */
    public function send($a = '', $b = '', $c = '') {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // Don't display, but log
        
        // Set JSON header first thing
        header('Content-Type: application/json');
        
        // Log the request for debugging
        error_log("ModeratorMessages::send called");
        error_log("POST data: " . print_r($_POST, true));
        
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            error_log("Auth failed");
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Not POST method");
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            error_log("Current user: " . print_r($currentUser, true));
            
            $errors = [];

            // Validate input
            $publisherId = $_POST['publisher_id'] ?? '';
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            error_log("Publisher ID: $publisherId, Subject: $subject");

            if (empty($publisherId) || !is_numeric($publisherId)) {
                $errors[] = 'Please select a publisher';
            }

            if (empty($subject)) {
                $errors[] = 'Subject is required';
            } elseif (strlen($subject) > 200) {
                $errors[] = 'Subject must not exceed 200 characters';
            }

            if (empty($message)) {
                $errors[] = 'Message is required';
            } elseif (strlen($message) > 2000) {
                $errors[] = 'Message must not exceed 2000 characters';
            }

            // Verify publisher exists and belongs to moderator's university
            if (empty($errors)) {
                $moderatorModel = new Moderator();
                $moderator = $moderatorModel->findById($currentUser['id']);

                error_log("Moderator: " . print_r($moderator, true));

                if (!$moderator) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Moderator profile not found'
                    ]);
                    exit();
                }

                $publisherModel = new Publisher();
                $publisher = $publisherModel->getPublisherById($publisherId);

                error_log("Publisher: " . print_r($publisher, true));

                if (!$publisher) {
                    $errors[] = 'Publisher not found';
                } elseif ($publisher->university !== $moderator->university) {
                    $errors[] = 'You can only send messages to publishers from your university';
                }
            }

            if (!empty($errors)) {
                error_log("Validation errors: " . implode(', ', $errors));
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errors)
                ]);
                exit();
            }

            // Send message
            $messageModel = new Message();
            $messageData = [
                'from_user_id' => $currentUser['id'],
                'from_user_type' => 'moderator',
                'to_user_id' => $publisherId,
                'to_user_type' => 'publisher',
                'subject' => $subject,
                'message' => $message
            ];

            error_log("Sending message: " . print_r($messageData, true));

            $messageId = $messageModel->sendMessage($messageData);

            error_log("Message ID: $messageId");

            if ($messageId) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Message sent successfully to publisher',
                    'message_id' => $messageId
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send message. Please try again.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Exception in ModeratorMessages::send - " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    /**
     * View message details
     */
    public function details($messageId = '', $b = '', $c = '') {
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        if (!$messageId || !is_numeric($messageId)) {
            header('Location: /unipulse/public/moderator/messages');
            exit();
        }

        $currentUser = AuthService::getCurrentUser();
        $data['user'] = $currentUser;

        // Get moderator data
        $moderatorModel = new Moderator();
        $moderator = $moderatorModel->findById($currentUser['id']);
        $data['moderator'] = $moderator;

        // Get message
        $messageModel = new Message();
        $message = $messageModel->getMessageById($messageId, $currentUser['id'], 'moderator');

        if (!$message) {
            header('Location: /unipulse/public/moderator/messages');
            exit();
        }

        $data['message'] = $message;

        $this->view('Moderator/message-details', $data);
    }
}
