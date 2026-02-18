<?php

class ModeratorContentmoderation extends Controller {
    
    public function __construct() {
        // Ensure moderator is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            // Redirect to login or show unauthorized page
            header('Location: /unipulse/public/signin');
            exit();
        }
    }
    
    /**
     * Content moderation page
     */
    public function index() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            
            // Get events pending review for this university
            $eventModel = new Event();
            $pendingEvents = $eventModel->getPendingEventsForUniversity($moderator->university, 20);
            
            // Get moderation statistics
            $moderationStats = $eventModel->getModerationStatsForUniversity($moderator->university);
            
            // Get moderator permissions
            $permissions = json_decode($moderator->permissions ?? '{}', true);
            
            $data = [
                'title' => 'Content Moderation',
                'page' => 'content_moderation',
                'moderator' => $moderator,
                'user' => $currentUser,
                'pendingEvents' => $pendingEvents,
                'moderationStats' => $moderationStats,
                'permissions' => $permissions
            ];
            
        } catch (Exception $e) {
            error_log("Error loading content moderation page: " . $e->getMessage());
            
            // Fallback data
            $data = [
                'title' => 'Content Moderation',
                'page' => 'content_moderation',
                'moderator' => (object) ['full_name' => 'Moderator'],
                'pendingEvents' => [],
                'moderationStats' => (object) [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'reviewed_today' => 0
                ],
                'permissions' => []
            ];
        }
        
        $this->view('Moderator/content_moderation', $data);
    }
    
    /**
     * Approve an event
     */
    public function approve($eventId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($eventId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Event ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $eventModel = new Event();
            
            $result = $eventModel->approve($eventId, $moderatorData['id']);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event approved successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to approve event'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Reject an event
     */
    public function reject($eventId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($eventId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Event ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $eventModel = new Event();
            
            // Get rejection reason from POST data
            $reason = $_POST['reason'] ?? '';
            
            $result = $eventModel->reject($eventId, $moderatorData['id'], $reason);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event rejected successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to reject event'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get event details for review
     */
    public function details($eventId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($eventId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Event ID is required']);
            exit();
        }

        try {
            $eventModel = new Event();
            $event = $eventModel->findById($eventId);
            
            if ($event) {
                echo json_encode([
                    'success' => true,
                    'event' => $event
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Event not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
?>