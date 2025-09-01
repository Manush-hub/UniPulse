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
        $this->view('sponsor_dashboard', $data);
    } 
    
    public function campaigns($a = '', $b = '' , $c = ''){
        // Manage sponsorship campaigns
        $this->view('sponsors_campaigns');
    }

    public function reports($a = '', $b = '' , $c = ''){
        // Sponsorship reports
        $this->view('sponsor_reports');
    }
    
    public function settings($a = '', $b = '' , $c = ''){
        // Sponsor account settings
        $this->view('sponsor_settings');
    }       
}
