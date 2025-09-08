<?php

class SessionMiddleware {
    
    /**
     * Check if user is authenticated
     */
    public static function requireAuth($requiredUserType = null) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // If specific user type is required, check it
        if ($requiredUserType !== null) {
            $currentUser = AuthService::getCurrentUser();
            if ($currentUser['type'] !== $requiredUserType) {
                // Redirect to appropriate dashboard
                $authService = new AuthService();
                $authService->redirectToDashboard($currentUser['type']);
            }
        }
        
        return true;
    }
    
    /**
     * Redirect authenticated users away from login/signup pages
     */
    public static function redirectIfAuthenticated() {
        if (AuthService::isLoggedIn()) {
            $currentUser = AuthService::getCurrentUser();
            $authService = new AuthService();
            $authService->redirectToDashboard($currentUser['type']);
        }
    }
}
