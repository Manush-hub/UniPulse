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
            
            // Check for password reset success message
            if (isset($_GET['message']) && $_GET['message'] === 'password_reset_success') {
                $data['success'] = '✅ Password reset successful! You can now sign in with your new password.';
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
            
            if ($authResult === 'suspended') {
                // Account is suspended - show suspension info and appeal option
                $suspensionInfo = $_SESSION['suspension_info'] ?? null;
                if ($suspensionInfo) {
                    $data = [
                        'suspended' => true,
                        'suspension_reason' => $suspensionInfo['reason'],
                        'user_email' => $suspensionInfo['email'],
                        'user_id' => $suspensionInfo['user_id'],
                        'user_type' => $suspensionInfo['user_type']
                    ];
                    $this->view('signin', $data);
                } else {
                    $this->view('signin', ['error' => 'Your account has been suspended. Please contact support.']);
                }
                return;
            }
            
            if ($authResult) {
                // Login successful - start session
                $this->authService->startSession($authResult);
                
                // Redirect based on user type
                switch ($authResult['type']) {
                    case 'admin':
                        header('Location: /unipulse/public/admin/dashboard');
                        break;
                    case 'moderator':
                        header('Location: /unipulse/public/moderator/dashboard');
                        break;
                    case 'public':
                        header('Location: /unipulse/public/user/landing');
                        break;
                    case 'university':
                        header('Location: /unipulse/public/user/landing');
                        break;
                    case 'sponsor':
                        header('Location: /unipulse/public/sponsor/profile');
                        break;
                    case 'publisher':
                        header('Location: /unipulse/public/publisher/profile');
                        break;
                    default:
                        header('Location: /unipulse/public/user/landing');
                }
                exit();
            } else {
                // Check if this is a publisher waiting for approval
                $publisherModel = new Publisher();
                $publisher = $publisherModel->findByEmail($email);
                
                if ($publisher && password_verify($password, $publisher->password_hash)) {
                    if ($publisher->approval_status === 'pending') {
                        $this->view('signin', ['error' => 'Your publisher account is pending approval by university moderators. Please wait for approval before signing in.']);
                    } elseif ($publisher->approval_status === 'rejected') {
                        $rejectionReason = $publisher->rejection_reason ? ' Reason: ' . $publisher->rejection_reason : '';
                        $this->view('signin', ['error' => 'Your publisher account has been rejected.' . $rejectionReason]);
                    } else {
                        $this->view('signin', ['error' => 'Your publisher account is not active. Please contact support.']);
                    }
                } else {
                    $this->view('signin', ['error' => 'Invalid email or password']);
                }
            }
            
        } catch (Exception $e) {
            $this->view('signin', ['error' => 'Login error: ' . $e->getMessage()]);
        }
    }
}
