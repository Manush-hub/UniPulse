<?php

class AdminController extends Controller {
    
    public function __construct() {
        // Ensure admin is logged in
        SessionMiddleware::requireAuth('admin');
    }
    
    /**
     * Admin dashboard
     */
    public function index() {
        $data = [
            'title' => 'Admin Dashboard',
            'page' => 'dashboard'
        ];
        
        $this->view('Admin/dashboard', $data);
    }
    
    /**
     * Comments management page
     */
    public function comments() {
        $data = [
            'title' => 'Comments Management',
            'page' => 'comments'
        ];
        
        $this->view('Admin/comments', $data);
    }
    
    /**
     * Users management page
     */
    public function users() {
        $data = [
            'title' => 'Users Management',
            'page' => 'users'
        ];
        
        $this->view('Admin/users', $data);
    }
    
    /**
     * Events management page
     */
    public function events() {
        $data = [
            'title' => 'Events Management',
            'page' => 'events'
        ];
        
        $this->view('Admin/events', $data);
    }
    
    /**
     * System settings page
     */
    public function settings() {
        $data = [
            'title' => 'System Settings',
            'page' => 'settings'
        ];
        
        $this->view('Admin/settings', $data);
    }
}
?>