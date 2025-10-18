<?php

class PublisherDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Require publisher authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];
        
        $this->view('Publisher/dashboard', $data);
    } 
}
