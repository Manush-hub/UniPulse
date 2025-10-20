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
        
        $this->view('Moderator/dashboard', $data);
    }
}
