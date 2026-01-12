<?php

class UserDashboard extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        // Require authentication - allow both public and university users
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/dashboard', $data);
    }

    /**
     * API endpoint to get current user data
     */
    public function getUserData()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $currentUser['name'] ?? 'User',
                'email' => $currentUser['email'] ?? '',
                'type' => $currentUser['type'] ?? 'user',
                'university' => $currentUser['university'] ?? ''
            ]
        ]);
    }
}
