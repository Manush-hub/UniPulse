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
}
