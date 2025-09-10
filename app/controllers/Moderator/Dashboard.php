<?php

class ModeratorDashboard extends Controller {

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get moderator permissions
        $moderatorModel = new Moderator();
        $data['permissions'] = $moderatorModel->getPermissions($data['user']['id']);
        
        $this->view('Moderator/dashboard', $data);
    }
}
