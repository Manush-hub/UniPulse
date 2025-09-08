<?php

class UserDashboard extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Require authentication - allow both public and university users
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];
        
        $this->view('User/dashboard', $data);
    } 
}