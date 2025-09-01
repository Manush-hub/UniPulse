<?php

class Dashboard extends Controller{

    public function __construct() {
        parent::__construct();
        // Ensure only sponsors can access this controller
        $this->requireRole('Sponsor');
    }

    public function index($a = '', $b = '' , $c = ''){
        // Sponsor-specific dashboard
        $data = [
            'title' => 'Sponsor Dashboard',
            'user_role' => $this->userRole
        ];
        $this->view('dashboard', $data);
    } 
    
    public function users($a = '', $b = '' , $c = ''){
        // Manage sponsor-related users
        $this->view('sponsors');
    }
    
    public function settings($a = '', $b = '' , $c = ''){
        // Sponsor account settings
        $this->view('settings');
    }
    
    public function reports($a = '', $b = '' , $c = ''){
        // Reports for sponsor activity
        $this->view('reports');
    }
}
