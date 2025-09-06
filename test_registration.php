<?php
/**
 * Test Registration API
 */

// Test registration
$testData = [
    'username' => 'testuser',
    'email' => 'test@example.com',
    'password' => 'TestPass123!',
    'firstName' => 'Test',
    'lastName' => 'User',
    'userType' => 'student'
];

// Simulate POST request
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Start output buffering to capture response
ob_start();
include __DIR__ . '/backend/api/register.php';
$response = ob_get_clean();

echo "Registration API Response:\n";
echo $response . "\n";

// Test with existing email
echo "\n--- Testing duplicate email ---\n";
ob_start();
include __DIR__ . '/backend/api/register.php';
$response2 = ob_get_clean();

echo "Second Registration Response:\n";
echo $response2 . "\n";