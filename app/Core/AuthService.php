<?php

class AuthService {
    
    use Model;
    
    /**
     * Authenticate user across all user tables
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password) {
        error_log("AuthService::authenticate called for email: $email");
        
        $userTables = [
            'public_users' => [
                'model' => 'PublicUser',
                'dashboard' => '/unipulse/public/user/dashboard',
                'type' => 'public'
            ],
            'university_users' => [
                'model' => 'UniversityUser', 
                'dashboard' => '/unipulse/public/user/dashboard',
                'type' => 'university'
            ],
            'sponsors' => [
                'model' => 'Sponsor',
                'dashboard' => '/unipulse/public/sponsor/dashboard', 
                'type' => 'sponsor'
            ],
            'publishers' => [
                'model' => 'Publisher',
                'dashboard' => '/unipulse/public/publisher/dashboard',
                'type' => 'publisher'
            ]
        ];
        
        foreach ($userTables as $table => $config) {
            error_log("Checking table: $table");
            $user = $this->findUserInTable($table, $email, $password);
            if ($user) {
                error_log("User found in table: $table");
                return [
                    'user' => $user,
                    'type' => $config['type'],
                    'dashboard' => $config['dashboard'],
                    'table' => $table
                ];
            }
        }
        
        error_log("User not found in any table");
        return false;
    }
    
    /**
     * Find user in specific table and verify password
     */
    private function findUserInTable($table, $email, $password) {
        error_log("Searching in table $table for email: $email");
        
        $query = "SELECT * FROM $table WHERE email = :email LIMIT 1";
        $user = $this->getRow($query, ['email' => $email]);
        
        if ($user) {
            error_log("User found in $table, verifying password");
            if ($this->verifyPassword($password, $user->password_hash)) {
                error_log("Password verification successful for $table");
                return $user;
            } else {
                error_log("Password verification failed for $table");
            }
        } else {
            error_log("No user found in $table");
        }
        
        return false;
    }
    
    /**
     * Verify password against hash
     */
    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Start user session
     */
    public function startSession($userData) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $userData['user']->id;
        $_SESSION['user_email'] = $userData['user']->email;
        $_SESSION['user_type'] = $userData['type'];
        $_SESSION['user_table'] = $userData['table'];
        $_SESSION['logged_in'] = true;
        
        // Set user name based on user type
        switch ($userData['type']) {
            case 'public':
            case 'university':
                $_SESSION['user_name'] = $userData['user']->full_name;
                break;
            case 'sponsor':
                $_SESSION['user_name'] = $userData['user']->company_name;
                break;
            case 'publisher':
                $_SESSION['user_name'] = $userData['user']->society_name;
                break;
        }
        
        return true;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user data
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'type' => $_SESSION['user_type'],
            'table' => $_SESSION['user_table']
        ];
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Redirect to appropriate dashboard based on user type
     */
    public function redirectToDashboard($userType) {
        $dashboards = [
            'public' => '/unipulse/public/user/dashboard',
            'university' => '/unipulse/public/user/dashboard', 
            'sponsor' => '/unipulse/public/sponsor/dashboard',
            'publisher' => '/unipulse/public/publisher/dashboard'
        ];
        
        $dashboard = $dashboards[$userType] ?? '/unipulse/public/user/dashboard';
        header('Location: ' . $dashboard);
        exit();
    }
}
