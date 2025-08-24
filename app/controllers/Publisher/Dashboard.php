<?php

class Dashboard extends Controller{

    public function __construct() {
        parent::__construct();
        // Ensure only publishers can access this controller
        $this->requireRole('Publisher');
    }

    public function index($a = '', $b = '' , $c = ''){
        // Publisher-specific dashboard
        $data = [
            'title' => 'Publisher Dashboard',
            'user_role' => $this->userRole
        ];
        $this->view('dashboard', $data);
    } 
    
    public function events($a = '', $b = '' , $c = ''){
        // Publisher events management
        $this->view('events');
    }
    
    public function analytics($a = '', $b = '' , $c = ''){
        // Publisher analytics
        $this->view('analytics');
    }
}
