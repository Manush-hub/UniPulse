<?php

class Moderators_list extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get all moderators
        $moderatorModel = new Moderator();
        $data['moderators'] = $moderatorModel->findAll();
        
        $this->view('Admin/moderators_list', $data);
    } 
}