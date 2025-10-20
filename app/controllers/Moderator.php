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
        $data = [
            'title' => 'Moderator Dashboard',
            'page' => 'dashboard'
        ];
        
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
     * Events moderation page
     */
    public function events() {
        $data = [
            'title' => 'Events Moderation',
            'page' => 'events'
        ];
        
        $this->view('Moderator/events', $data);
    }
    
    /**
     * Publishers management page
     */
    public function publishers() {
        $data = [
            'title' => 'Publishers Management',
            'page' => 'publishers'
        ];
        
        $this->view('Moderator/publishers', $data);
    }
}
?>