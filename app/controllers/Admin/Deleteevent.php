<?php

class AdminDeleteevent extends Controller{

    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index($eventId = '', $b = '' , $c = ''){
        // Check if user is logged in and is an admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            // For AJAX requests, return JSON error instead of redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'You must be logged in as an admin to delete events. Please login and try again.'],
                    'redirect' => '/unipulse/public/signin'
                ]);
                exit();
            }
            
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get current user
        $currentUser = AuthService::getCurrentUser();
        
        // Get event ID from URL parameter or JSON body
        if (empty($eventId)) {
            // Try to get from JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['id'])) {
                $eventId = $input['id'];
            }
        }
        
        // Check if event ID is provided and valid
        if (empty($eventId) || !is_numeric($eventId)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'Invalid event ID']
                ]);
                exit();
            }
            
            header('Location: /unipulse/public/admin/allevents?error=Invalid event ID');
            exit();
        }
        
        // Only allow POST requests for deletion
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'Invalid request method']
                ]);
                exit();
            }
            
            header('Location: /unipulse/public/admin/allevents?error=Invalid request method');
            exit();
        }
        
        // More robust AJAX detection
        $isAjax = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
            (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (isset($_POST['ajax']) && $_POST['ajax'] == '1')
        );
        
        // Set JSON header early for AJAX requests
        if ($isAjax) {
            header('Content-Type: application/json');
        }
        
        try {
            // Admin can delete any event
            $result = $this->eventModel->deleteEventAdmin($eventId);
            
            if ($result['success']) {
                // Return JSON response for AJAX requests
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Event deleted successfully!'
                    ]);
                    exit();
                } else {
                    // Redirect with success message for regular form submission
                    header('Location: /unipulse/public/admin/allevents?success=Event deleted successfully');
                    exit();
                }
            } else {
                // Return error response
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'errors' => $result['errors'] ?? ['general' => 'Unknown error occurred']
                    ]);
                    exit();
                } else {
                    header('Location: /unipulse/public/admin/allevents?error=' . urlencode($result['errors']['general'] ?? 'Unknown error occurred'));
                    exit();
                }
            }
        } catch (Exception $e) {
            error_log("Error in AdminDeleteevent::index: " . $e->getMessage());
            
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'An error occurred while deleting the event. Please try again.']
                ]);
                exit();
            } else {
                header('Location: /unipulse/public/admin/allevents?error=An error occurred while deleting the event');
                exit();
            }
        }
    }
}