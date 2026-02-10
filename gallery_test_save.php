<?php

/**
 * Quick diagnostic for gallery save issues
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log all details
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$log = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $method,
    'uri' => $uri,
    'path' => $path,
    'logged_in' => !empty($_SESSION['user_id']),
    'user_id' => $_SESSION['user_id'] ?? null,
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
];

// If POST, show input
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $log['input_keys'] = array_keys($input ?? []);
    $log['input_valid'] = is_array($input);
}

// Log to file
error_log('GALLERY_SAVE_TEST: ' . json_encode($log));

// Return diagnostic
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Diagnostic logged. Check PHP error logs.',
    'diagnostic' => $log
]);
