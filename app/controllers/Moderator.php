<?php

class ModeratorController extends Controller {
    
    public function __construct() {
        // Ensure moderator is logged in
        SessionMiddleware::requireAuth('moderator');
    }
    
    /**
     * Moderator dashboard
     */
    public function index() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            
            // Get publisher statistics
            $publisherModel = new Publisher();
            $publisher_stats = $publisherModel->getStatsByUniversity($moderator->university);
            
            // Get recent pending publishers for this university
            $recent_pending_publishers = $publisherModel->getRecentPendingForUniversity($moderator->university, 5);
            
            // Get moderator permissions (with defaults)
            $permissions = $moderatorModel->getPermissions($moderator->id);
            
            $data = [
                'title' => 'Moderator Dashboard',
                'page' => 'dashboard',
                'moderator' => $moderator,
                'publisher_stats' => $publisher_stats,
                'recent_pending_publishers' => $recent_pending_publishers,
                'permissions' => $permissions
            ];
            
        } catch (Exception $e) {
            error_log("Error loading moderator dashboard: " . $e->getMessage());
            
            // Fallback data
            $data = [
                'title' => 'Moderator Dashboard',
                'page' => 'dashboard',
                'moderator' => (object) ['full_name' => 'Moderator'],
                'publisher_stats' => (object) ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0],
                'recent_pending_publishers' => [],
                'permissions' => ['approve_publishers' => true]
            ];
        }
        
        $this->view('Moderator/dashboard', $data);
    }
    
    /**
     * Comments management page
     */
    public function comments() {
        $data = [
            'title' => 'University Comments',
            'page' => 'comments'
        ];
        
        $this->view('Moderator/comments', $data);
    }

    /**
     * Comments moderation page
     */
    public function comments_moderation() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderator = new Moderator();
            $moderatorData = $moderator->findById($currentUser['id']);
            
            // Get comments stats for the moderator's university
            $commentsController = new ModeratorComments();
            
            $data = [
                'title' => 'Comments Moderation',
                'page' => 'comments_moderation',
                'moderator' => $moderatorData,
                'user' => $currentUser
            ];
            
            $this->view('Moderator/comments_moderation', $data);
            
        } catch (Exception $e) {
            error_log("Error loading comments moderation page: " . $e->getMessage());
            
            $data = [
                'title' => 'Comments Moderation',
                'page' => 'comments_moderation',
                'error' => 'Unable to load comments data'
            ];
            
            $this->view('Moderator/comments_moderation', $data);
        }
    }
    
    /**
     * Events moderation page
     */
    public function events() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderator = new Moderator();
            $moderatorData = $moderator->findById($currentUser['id']);
            
            $data = [
                'title' => 'Events Moderation',
                'page' => 'events',
                'moderator' => $moderatorData,
                'user' => $currentUser
            ];
        } catch (Exception $e) {
            error_log("Error loading events page: " . $e->getMessage());
            
            $data = [
                'title' => 'Events Moderation',
                'page' => 'events',
                'moderator' => (object) ['full_name' => 'Moderator']
            ];
        }
        
        $this->view('Moderator/events', $data);
    }
    
    /**
     * Publishers management page
     */
    public function publishers() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderator = new Moderator();
            $moderatorData = $moderator->findById($currentUser['id']);
            
            $data = [
                'title' => 'Publishers Management',
                'page' => 'publishers',
                'moderator' => $moderatorData,
                'user' => $currentUser
            ];
        } catch (Exception $e) {
            error_log("Error loading publishers page: " . $e->getMessage());
            
            $data = [
                'title' => 'Publishers Management',
                'page' => 'publishers',
                'moderator' => (object) ['full_name' => 'Moderator']
            ];
        }
        
        $this->view('Moderator/publishers', $data);
    }
    
    /**
     * Publisher approval page
     */
    public function publisherapproval() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            
            // Get pending publishers for this university
            $publisherModel = new Publisher();
            $pendingPublishers = $publisherModel->getPendingByUniversity($moderator->university);
            
            $data = [
                'title' => 'Publisher Approvals',
                'page' => 'publisher_approval',
                'moderator' => $moderator,
                'user' => $currentUser,
                'pendingPublishers' => $pendingPublishers
            ];
        } catch (Exception $e) {
            error_log("Error loading publisher approval page: " . $e->getMessage());
            
            $data = [
                'title' => 'Publisher Approvals',
                'page' => 'publisher_approval',
                'moderator' => (object) ['full_name' => 'Moderator'],
                'pendingPublishers' => []
            ];
        }
        
        $this->view('Moderator/publisher_approval', $data);
    }
}
?>