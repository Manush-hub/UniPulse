<?php

class Signindebug extends Controller{

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function index($a = '', $b = '' , $c = ''){
        error_log("Signindebug::index called with method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("Raw input: " . file_get_contents('php://input'));
        
        // If user is already logged in, redirect to appropriate dashboard
        if (AuthService::isLoggedIn()) {
            $currentUser = AuthService::getCurrentUser();
            $this->authService->redirectToDashboard($currentUser['type']);
        }

        // Handle POST request (login form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            // Show login form
            echo "<h1>Debug Signin Page</h1>";
            echo "<p>Authentication system is working. Use the simple test form.</p>";
            echo "<a href='/simple_signin_test.html'>Go to Simple Test Form</a>";
        }
    }

    private function handleLogin() {
        header('Content-Type: application/json');
        error_log("=== LOGIN ATTEMPT DEBUG ===");
        
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Fallback to POST data if JSON input is empty
            if (empty($input)) {
                $input = $_POST;
            }

            error_log("Input data: " . print_r($input, true));

            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';

            error_log("Email: $email, Password: [HIDDEN]");

            // Validate input
            if (empty($email) || empty($password)) {
                error_log("Missing email or password");
                echo json_encode([
                    'success' => false,
                    'message' => 'Email and password are required'
                ]);
                exit();
            }

            // Authenticate user
            error_log("Starting authentication...");
            $authResult = $this->authService->authenticate($email, $password);
            error_log("Authentication result: " . print_r($authResult, true));
            
            if ($authResult) {
                // Start session
                $this->authService->startSession($authResult);
                
                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'redirectUrl' => $authResult['dashboard'],
                    'user' => [
                        'id' => $authResult['user']->id,
                        'email' => $authResult['user']->email,
                        'type' => $authResult['type']
                    ]
                ];
                
                error_log("Success response: " . print_r($response, true));
                echo json_encode($response);
            } else {
                error_log("Authentication failed");
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred during login: ' . $e->getMessage()
            ]);
        }
        
        exit();
    }
}
