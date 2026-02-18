<?php

class ModeratorDashboard extends Controller {

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get moderator permissions and dashboard data
        $moderatorModel = new Moderator();
        $dashboardStats = $moderatorModel->getDashboardStats($data['user']['id']);
        
        if (!$dashboardStats) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data['permissions'] = $dashboardStats['permissions'];
        $data['moderator'] = $dashboardStats['moderator'];
        $data['publisher_stats'] = $dashboardStats['publisher_stats'];
        
        // Get recent pending publishers if moderator has permission
        if (isset($data['permissions']['approve_publishers']) && $data['permissions']['approve_publishers']) {
            $publisherModel = new Publisher();
            $pendingPublishers = $publisherModel->getPendingByUniversity($data['moderator']->university);
            $data['recent_pending_publishers'] = $pendingPublishers ?: [];
            // Limit to 5 most recent
            if (is_array($data['recent_pending_publishers']) && count($data['recent_pending_publishers']) > 5) {
                $data['recent_pending_publishers'] = array_slice($data['recent_pending_publishers'], 0, 5);
            }
        }
        
        // Get recent moderation activities (show all activities, not just current moderator's)
        // Fetch more records (50) so we have enough for "View Full Log"
        $eventModel = new Event();
        $data['recent_activities'] = $eventModel->getRecentModerationActivities(null, 50);
        
        // Calculate moderation stats using direct PDO connection
        try {
            $string = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $conn = new PDO($string, DBUSER, DBPASS, $options);
            
            // Count ALL hidden events by all moderators
            $stmt = $conn->query("SELECT COUNT(*) as count FROM events WHERE is_deleted = 1");
            $data['moderation_stats']['hidden_events'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Count ALL approved publishers across all universities
            $stmt = $conn->query("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'approved'");
            $data['moderation_stats']['approved_publishers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Count ALL rejected publishers across all universities
            $stmt = $conn->query("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'rejected'");
            $data['moderation_stats']['rejected_publishers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Total moderation actions
            $data['moderation_stats']['total_actions'] = $data['moderation_stats']['hidden_events'] + 
                                                          $data['moderation_stats']['approved_publishers'] + 
                                                          $data['moderation_stats']['rejected_publishers'];
        } catch (PDOException $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
            // Set default values if query fails
            $data['moderation_stats']['hidden_events'] = 0;
            $data['moderation_stats']['approved_publishers'] = 0;
            $data['moderation_stats']['rejected_publishers'] = 0;
            $data['moderation_stats']['total_actions'] = 0;
        }
        
        $this->view('Moderator/dashboard', $data);
    }
    
    /**
     * API endpoint to get recent moderation activities
     */
    public function getActivities() {
        header('Content-Type: application/json');
        
        // Check authentication
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            $eventModel = new Event();
            $activities = $eventModel->getRecentModerationActivities($currentUser['id'], 20);
            
            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            error_log("getActivities error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load activities'
            ]);
        }
        
        exit;
    }
}
