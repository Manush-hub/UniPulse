<?php

class Logout extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to signin page with logout message
        header('Location: /unipulse/public/signin?message=logout_success');
        exit();
    }
}
