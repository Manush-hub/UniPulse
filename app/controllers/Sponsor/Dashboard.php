<?php
     
class SponsorDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get messaging data
        $message = new Message();
        $recentMessages = $message->getUserMessages($currentUser['id'], 'sponsor', 'received');
        $unreadCount = $message->getUnreadCount($currentUser['id'], 'sponsor');
        
        // Ensure recentMessages is an array and limit to recent 5 messages for dashboard display
        $recentMessages = is_array($recentMessages) ? array_slice($recentMessages, 0, 5) : [];
        
        // Pass user data and messages to view
        $data = [
            'user' => $currentUser,
            'recent_messages' => $recentMessages,
            'unread_count' => $unreadCount,
            'page_title' => 'Dashboard'
        ];
        
        
        $this->view('Sponsor/dashboard', $data);
    }
    
    /**
     * API endpoint to get user profile data
     */
    public function getUserProfile() {
        // Clean output buffer
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'companyName' => $currentUser['company_name'] ?? 'Sponsor',
                'email' => $currentUser['email'] ?? '',
                'type' => 'sponsor'
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getUserProfile: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load profile data'
            ]);
        }
        
        exit;
    }
    
    /**
     * API endpoint to get notifications
     */
    public function getNotifications() {
        // Clean output buffer
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]);
                exit;
            }
            
            // For now, return empty notifications
            // TODO: Implement notification system for sponsors
            echo json_encode([
                'success' => true,
                'notifications' => []
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getNotifications: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load notifications',
                'notifications' => []
            ]);
        }
        
        exit;
    }
} 