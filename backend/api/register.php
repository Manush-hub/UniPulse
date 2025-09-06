<?php
/**
 * User Registration API Endpoint
 * Handles user registration with JSON input/output
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/session.php';

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if JSON is valid
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Fallback to $_POST data
        $data = $_POST;
    }
    
    // Validate required fields
    $requiredFields = ['username', 'email', 'password'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        ]);
        exit();
    }
    
    // Additional validation
    $errors = [];
    
    // Email validation
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Username validation
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $data['username'])) {
        $errors[] = 'Username must be 3-50 characters and contain only letters, numbers, and underscores';
    }
    
    // Password strength validation
    if (!validatePasswordStrength($data['password'])) {
        $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number and special character';
    }
    
    // User type validation
    $validUserTypes = ['student', 'organizer'];
    if (isset($data['userType']) && !in_array($data['userType'], $validUserTypes)) {
        $errors[] = 'Invalid user type';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    // Prepare user data
    $userData = [
        'username' => trim($data['username']),
        'email' => strtolower(trim($data['email'])),
        'password' => $data['password'],
        'first_name' => isset($data['firstName']) ? trim($data['firstName']) : '',
        'last_name' => isset($data['lastName']) ? trim($data['lastName']) : '',
        'user_type' => isset($data['userType']) ? $data['userType'] : 'student'
    ];
    
    // Create new user
    $user = new User();
    $result = $user->register($userData);
    
    if ($result['success']) {
        // Start session for the newly registered user
        $sessionManager = SessionManager::getInstance();
        $newUser = User::getUserById($result['user']['id']);
        
        if ($newUser && $sessionManager->login($newUser)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => $result['message'],
                'user' => $result['user'],
                'redirectUrl' => '/unipulse/public/dashboard.php'
            ]);
        } else {
            // Registration successful but session creation failed
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => $result['message'] . ' Please sign in.',
                'user' => $result['user'],
                'redirectUrl' => '/unipulse/public/signin.php'
            ]);
        }
    } else {
        // Registration failed
        http_response_code(400);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    error_log("Registration API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    // Minimum 8 characters
    if (strlen($password) < 8) {
        return false;
    }
    
    // Must contain uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Must contain lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Must contain number
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    // Must contain special character
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return false;
    }
    
    return true;
}