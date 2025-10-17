<?php

/**
 * Base User Controller
 * Provides common functionality for all user controllers
 */
class BaseUserController extends Controller {
    
    protected $currentUser;
    protected $userDetails;
    protected $userUniversity;
    protected $userUniversityName;
    
    public function __construct() {
        parent::__construct();
        $this->initializeUserData();
    }
    
    /**
     * Initialize user data for all user controllers
     */
    protected function initializeUserData() {
        // Get current user from session
        $this->currentUser = AuthService::getCurrentUser();
        
        // Require authentication for all user controllers
        if (!$this->currentUser || !in_array($this->currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get complete user details from database
        $this->userDetails = AuthService::getCurrentUserDetails();
        
        // Get university information for university users
        if ($this->currentUser['type'] === 'university') {
            $event = new Event();
            $this->userUniversity = $event->getUserUniversity(
                $this->currentUser['id'], 
                $this->currentUser['type'], 
                $this->currentUser['table']
            );
            
            if ($this->userUniversity) {
                $this->userUniversityName = AuthService::getUserUniversityName($this->userUniversity);
            }
        }
    }
    
    /**
     * Get base data array for views
     */
    protected function getBaseViewData() {
        return [
            'user' => $this->currentUser,
            'userDetails' => $this->userDetails,
            'userUniversity' => $this->userUniversity,
            'userUniversityName' => $this->userUniversityName
        ];
    }
    
    /**
     * Enhanced view method that includes base user data
     */
    protected function userView($view, $data = []) {
        $baseData = $this->getBaseViewData();
        $data = array_merge($baseData, $data);
        $this->view($view, $data);
    }
}