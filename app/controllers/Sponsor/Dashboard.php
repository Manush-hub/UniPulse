<?php

class Dashboard extends Controller{

    public function __construct() {
        parent::__construct();
        // Ensure only admins can access this controller
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
        // User management
        $this->view('users');
    }
    
    public function settings($a = '', $b = '' , $c = ''){
        // System settings
        $this->view('settings');
    }
    
    public function reports($a = '', $b = '' , $c = ''){
        // System reports
        $this->view('reports');
    }
}
