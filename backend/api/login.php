<?php
/**
 * User Login API Endpoint
 * Handles user authentication with session management
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
    $requiredFields = ['email', 'password'];
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
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    // Rate limiting check (simple implementation)
    if (!checkRateLimit($data['email'])) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many login attempts. Please try again later.'
        ]);
        exit();
    }
    
    // Attempt login
    $user = new User();
    $result = $user->login(strtolower(trim($data['email'])), $data['password']);
    
    if ($result['success']) {
        // Start session
        $sessionManager = SessionManager::getInstance();
        $loggedInUser = User::getUserByEmail($data['email']);
        
        if ($loggedInUser && $sessionManager->login($loggedInUser)) {
            // Clear any failed login attempts
            clearFailedAttempts($data['email']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => $result['message'],
                'user' => $result['user'],
                'session' => $sessionManager->getSessionData(),
                'redirectUrl' => determineRedirectUrl($result['user']['user_type'])
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Session creation failed. Please try again.'
            ]);
        }
    } else {
        // Record failed attempt
        recordFailedAttempt($data['email']);
        
        http_response_code(401);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    error_log("Login API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}

/**
 * Determine redirect URL based on user type
 */
function determineRedirectUrl($userType) {
    switch ($userType) {
        case 'admin':
            return 'admin-dashboard';
        case 'organizer':
            return 'organizer-dashboard';
        case 'student':
        default:
            return 'dashboard';
    }
}

/**
 * Simple rate limiting - check failed attempts
 */
function checkRateLimit($email) {
    // Check session for failed attempts
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = [];
    }
    
    $emailKey = md5($email);
    $now = time();
    
    // Clean old attempts (older than 15 minutes)
    if (isset($_SESSION['failed_attempts'][$emailKey])) {
        $_SESSION['failed_attempts'][$emailKey] = array_filter(
            $_SESSION['failed_attempts'][$emailKey],
            function($timestamp) use ($now) {
                return ($now - $timestamp) < 900; // 15 minutes
            }
        );
    }
    
    // Check if too many attempts
    $attemptCount = isset($_SESSION['failed_attempts'][$emailKey]) ? 
                   count($_SESSION['failed_attempts'][$emailKey]) : 0;
    
    return $attemptCount < 5; // Allow 5 attempts per 15 minutes
}

/**
 * Record failed login attempt
 */
function recordFailedAttempt($email) {
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = [];
    }
    
    $emailKey = md5($email);
    
    if (!isset($_SESSION['failed_attempts'][$emailKey])) {
        $_SESSION['failed_attempts'][$emailKey] = [];
    }
    
    $_SESSION['failed_attempts'][$emailKey][] = time();
}

/**
 * Clear failed login attempts for email
 */
function clearFailedAttempts($email) {
    if (isset($_SESSION['failed_attempts'])) {
        $emailKey = md5($email);
        unset($_SESSION['failed_attempts'][$emailKey]);
    }
}