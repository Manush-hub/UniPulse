<?php
/**
 * Test Login API
 */

// Test login
$testData = [
    'email' => 'demo@unipulse.lk',
    'password' => 'password123'
];

// Simulate POST request
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Start output buffering to capture response
ob_start();
include __DIR__ . '/backend/api/login.php';
$response = ob_get_clean();

echo "Login API Response:\n";
echo $response . "\n";

// Test with invalid credentials
echo "\n--- Testing invalid credentials ---\n";
$_POST = [
    'email' => 'demo@unipulse.lk',
    'password' => 'wrongpassword'
];

ob_start();
include __DIR__ . '/backend/api/login.php';
$response2 = ob_get_clean();

echo "Invalid Login Response:\n";
echo $response2 . "\n";