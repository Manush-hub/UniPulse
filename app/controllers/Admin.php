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
        // Get recent registrations from all user types
        $universityUser = new UniversityUser();
        $publicUser = new PublicUser();
        $publisher = new Publisher();
        $sponsor = new Sponsor();
        
        // Get recent registrations (limit 5 from each)
        $universityRegistrations = $universityUser->getRecentRegistrations(5);
        $publicRegistrations = $publicUser->getRecentRegistrations(5);
        $publisherRegistrations = $publisher->getRecentRegistrations(5);
        $sponsorRegistrations = $sponsor->getRecentRegistrations(5);
        
        // Debug logging
        error_log("University: " . (is_array($universityRegistrations) ? count($universityRegistrations) : 'false'));
        error_log("Public: " . (is_array($publicRegistrations) ? count($publicRegistrations) : 'false'));
        error_log("Publisher: " . (is_array($publisherRegistrations) ? count($publisherRegistrations) : 'false'));
        error_log("Sponsor: " . (is_array($sponsorRegistrations) ? count($sponsorRegistrations) : 'false'));
        
        // Merge all registrations
        $recentRegistrations = array_merge(
            is_array($universityRegistrations) ? $universityRegistrations : [],
            is_array($publicRegistrations) ? $publicRegistrations : [],
            is_array($publisherRegistrations) ? $publisherRegistrations : [],
            is_array($sponsorRegistrations) ? $sponsorRegistrations : []
        );
        
        error_log("Total merged: " . count($recentRegistrations));
        
        // Sort by created_at descending
        usort($recentRegistrations, function($a, $b) {
            $timeA = is_object($a) ? strtotime($a->created_at) : strtotime($a['created_at']);
            $timeB = is_object($b) ? strtotime($b->created_at) : strtotime($b['created_at']);
            return $timeB - $timeA;
        });
        
        // Get only top 10
        $recentRegistrations = array_slice($recentRegistrations, 0, 10);
        
        error_log("Final count: " . count($recentRegistrations));
        
        $data = [
            'title' => 'Admin Dashboard',
            'page' => 'dashboard',
            'recent_registrations' => $recentRegistrations
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