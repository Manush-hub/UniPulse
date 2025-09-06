<?php
/**
 * User Logout API Endpoint
 * Handles user logout and session cleanup
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

require_once __DIR__ . '/../includes/session.php';

try {
    $sessionManager = SessionManager::getInstance();
    
    if ($sessionManager->isLoggedIn()) {
        $sessionManager->logout();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Logout successful',
            'redirectUrl' => '/unipulse/public/index.php'
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'No active session',
            'redirectUrl' => '/unipulse/public/index.php'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Logout API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}