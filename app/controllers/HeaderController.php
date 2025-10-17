<?php

/**
 * Header Controller
 * Handles loading user information for header display across different user types
 */
class HeaderController extends Controller {
    
    private $currentUser;
    private $userDetails;
    
    public function __construct() {
        parent::__construct();
        $this->loadUserData();
    }
    
    /**
     * Load user data for header display
     */
    private function loadUserData() {
        $this->currentUser = AuthService::getCurrentUser();
        
        if ($this->currentUser) {
            $this->userDetails = AuthService::getCurrentUserDetails();
        }
    }
    
    /**
     * Get header data for public users
     */
    public function getPublicUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'public') {
            return null;
        }
        
        return [
            'user_type' => 'public',
            'full_name' => $this->userDetails ? $this->userDetails->full_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'display_label' => 'Public User',
            'avatar_url' => '/unipulse/public/assets/images/default-avatar.png',
            'dashboard_url' => '/unipulse/public/user/dashboard',
            'profile_url' => '/unipulse/public/user/profile'
        ];
    }
    
    /**
     * Get header data for university users
     */
    public function getUniversityUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'university') {
            return null;
        }
        
        $universityName = null;
        if ($this->userDetails && isset($this->userDetails->university)) {
            $universityName = AuthService::getUserUniversityName($this->userDetails->university);
        }
        
        return [
            'user_type' => 'university',
            'full_name' => $this->userDetails ? $this->userDetails->full_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'university' => $this->userDetails ? $this->userDetails->university : null,
            'university_name' => $universityName,
            'faculty' => $this->userDetails ? $this->userDetails->faculty : null,
            'student_staff_id' => $this->userDetails ? $this->userDetails->student_staff_id : null,
            'display_label' => $universityName ?: 'University User',
            'avatar_url' => '/unipulse/public/assets/images/default-avatar.png',
            'dashboard_url' => '/unipulse/public/user/dashboard',
            'profile_url' => '/unipulse/public/user/profile'
        ];
    }
    
    /**
     * Get header data for admin users
     */
    public function getAdminUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'admin') {
            return null;
        }
        
        return [
            'user_type' => 'admin',
            'full_name' => $this->userDetails ? $this->userDetails->full_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'display_label' => 'Administrator',
            'avatar_url' => '/unipulse/public/assets/images/admin-avatar.png',
            'dashboard_url' => '/unipulse/public/admin/dashboard',
            'profile_url' => '/unipulse/public/admin/profile'
        ];
    }
    
    /**
     * Get header data for moderator users
     */
    public function getModeratorUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'moderator') {
            return null;
        }
        
        return [
            'user_type' => 'moderator',
            'full_name' => $this->userDetails ? $this->userDetails->full_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'display_label' => 'Moderator',
            'avatar_url' => '/unipulse/public/assets/images/moderator-avatar.png',
            'dashboard_url' => '/unipulse/public/moderator/dashboard',
            'profile_url' => '/unipulse/public/moderator/profile'
        ];
    }
    
    /**
     * Get header data for sponsor users
     */
    public function getSponsorUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'sponsor') {
            return null;
        }
        
        return [
            'user_type' => 'sponsor',
            'full_name' => $this->userDetails ? $this->userDetails->company_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'company_name' => $this->userDetails ? $this->userDetails->company_name : null,
            'display_label' => 'Sponsor',
            'avatar_url' => '/unipulse/public/assets/images/sponsor-avatar.png',
            'dashboard_url' => '/unipulse/public/sponsor/dashboard',
            'profile_url' => '/unipulse/public/sponsor/profile'
        ];
    }
    
    /**
     * Get header data for publisher users
     */
    public function getPublisherUserHeaderData() {
        if (!$this->currentUser || $this->currentUser['type'] !== 'publisher') {
            return null;
        }
        
        return [
            'user_type' => 'publisher',
            'full_name' => $this->userDetails ? $this->userDetails->society_name : $this->currentUser['name'],
            'email' => $this->userDetails ? $this->userDetails->email : $this->currentUser['email'],
            'society_name' => $this->userDetails ? $this->userDetails->society_name : null,
            'display_label' => 'Event Publisher',
            'avatar_url' => '/unipulse/public/assets/images/publisher-avatar.png',
            'dashboard_url' => '/unipulse/public/publisher/dashboard',
            'profile_url' => '/unipulse/public/publisher/profile'
        ];
    }
    
    /**
     * Get consolidated header data for current user
     */
    public function getCurrentUserHeaderData() {
        if (!$this->currentUser) {
            return [
                'user_type' => 'guest',
                'full_name' => 'Guest',
                'display_label' => 'Not Signed In',
                'is_authenticated' => false,
                'signin_url' => '/unipulse/public/signin',
                'signup_url' => '/unipulse/public/signup'
            ];
        }
        
        $headerData = null;
        
        switch ($this->currentUser['type']) {
            case 'public':
                $headerData = $this->getPublicUserHeaderData();
                break;
            case 'university':
                $headerData = $this->getUniversityUserHeaderData();
                break;
            case 'admin':
                $headerData = $this->getAdminUserHeaderData();
                break;
            case 'moderator':
                $headerData = $this->getModeratorUserHeaderData();
                break;
            case 'sponsor':
                $headerData = $this->getSponsorUserHeaderData();
                break;
            case 'publisher':
                $headerData = $this->getPublisherUserHeaderData();
                break;
            default:
                $headerData = [
                    'user_type' => $this->currentUser['type'],
                    'full_name' => $this->currentUser['name'],
                    'email' => $this->currentUser['email'],
                    'display_label' => ucfirst($this->currentUser['type']) . ' User',
                    'avatar_url' => '/unipulse/public/assets/images/default-avatar.png',
                    'dashboard_url' => '/unipulse/public/user/dashboard',
                    'profile_url' => '/unipulse/public/user/profile'
                ];
        }
        
        if ($headerData) {
            $headerData['is_authenticated'] = true;
            $headerData['logout_url'] = '/unipulse/public/logout';
        }
        
        return $headerData;
    }
    
    /**
     * Get navigation items based on user type
     */
    public function getNavigationItems() {
        if (!$this->currentUser) {
            return [
                ['label' => 'Home', 'url' => '/unipulse/public/', 'key' => 'home'],
                ['label' => 'Find Events', 'url' => '/unipulse/public/find_events', 'key' => 'events'],
                ['label' => 'About', 'url' => '/unipulse/public/about', 'key' => 'about']
            ];
        }
        
        switch ($this->currentUser['type']) {
            case 'public':
            case 'university':
                return [
                    ['label' => 'Home', 'url' => '/unipulse/public/user/landing', 'key' => 'home'],
                    ['label' => 'Find Events', 'url' => '/unipulse/public/find_events', 'key' => 'events'],
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/user/dashboard', 'key' => 'dashboard']
                ];
                
            case 'admin':
                return [
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/admin/dashboard', 'key' => 'dashboard'],
                    ['label' => 'Users', 'url' => '/unipulse/public/admin/users', 'key' => 'users'],
                    ['label' => 'Events', 'url' => '/unipulse/public/admin/events', 'key' => 'events'],
                    ['label' => 'Moderators', 'url' => '/unipulse/public/admin/moderators', 'key' => 'moderators']
                ];
                
            case 'moderator':
                return [
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/moderator/dashboard', 'key' => 'dashboard'],
                    ['label' => 'Events', 'url' => '/unipulse/public/moderator/events', 'key' => 'events'],
                    ['label' => 'Reports', 'url' => '/unipulse/public/moderator/reports', 'key' => 'reports']
                ];
                
            case 'sponsor':
                return [
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/sponsor/dashboard', 'key' => 'dashboard'],
                    ['label' => 'Events', 'url' => '/unipulse/public/sponsor/events', 'key' => 'events'],
                    ['label' => 'Sponsorships', 'url' => '/unipulse/public/sponsor/sponsorships', 'key' => 'sponsorships']
                ];
                
            case 'publisher':
                return [
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/publisher/dashboard', 'key' => 'dashboard'],
                    ['label' => 'My Events', 'url' => '/unipulse/public/publisher/events', 'key' => 'events'],
                    ['label' => 'Create Event', 'url' => '/unipulse/public/publisher/create', 'key' => 'create']
                ];
                
            default:
                return [
                    ['label' => 'Dashboard', 'url' => '/unipulse/public/user/dashboard', 'key' => 'dashboard']
                ];
        }
    }
}