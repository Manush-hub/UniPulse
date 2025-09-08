<?php

class SponsorDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];
        
        $this->view('Sponsor/dashboard', $data);
    } 
}
