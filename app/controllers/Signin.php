<?php

class Signin extends Controller{

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function index($a = '', $b = '' , $c = ''){
        // Handle POST request for login submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            // Prepare data for the view
            $data = [];
            
            // Check if user is already logged in, redirect to appropriate dashboard
            if (AuthService::isLoggedIn()) {
                $currentUser = AuthService::getCurrentUser();
                $this->authService->redirectToDashboard($currentUser['type']);
            }
            
            // Check for logout message
            if (isset($_GET['message']) && $_GET['message'] === 'logout_success') {
                $data['success'] = 'You have been successfully logged out.';
            }
            
            // Check for registration success message
            if (isset($_SESSION['registration_success'])) {
                $data['success'] = $_SESSION['registration_success'];
                unset($_SESSION['registration_success']);
            }
            
            // Show login form
            $this->view('signin', $data);
        }
    }

    private function handleLogin() {
        try {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $this->view('signin', ['error' => 'Email and password are required']);
                return;
            }
            
            // Authenticate user across all user tables
            $authResult = $this->authService->authenticate($email, $password);
            
            if ($authResult) {
                // Login successful - start session
                $this->authService->startSession($authResult);
                
                // Redirect based on user type
                switch ($authResult['type']) {
                    case 'public':
                        header('Location: /unipulse/public/user/dashboard');
                        break;
                    case 'university':
                        header('Location: /unipulse/public/user/dashboard');
                        break;
                    case 'sponsor':
                        header('Location: /unipulse/public/sponsor/dashboard');
                        break;
                    case 'publisher':
                        header('Location: /unipulse/public/publisher/dashboard');
                        break;
                    default:
                        header('Location: /unipulse/public/user/dashboard');
                }
                exit();
            } else {
                $this->view('signin', ['error' => 'Invalid email or password']);
            }
            
        } catch (Exception $e) {
            $this->view('signin', ['error' => 'Login error: ' . $e->getMessage()]);
        }
    }
}
