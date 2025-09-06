<?php
// Test registration via direct POST simulation
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'username' => 'testuser3',
    'email' => 'test3@example.com',
    'password' => 'TestPass123!',
    'firstName' => 'Test',
    'lastName' => 'User',
    'userType' => 'student'
];

ob_start();
include __DIR__ . '/backend/api/register.php';
$response = ob_get_clean();

echo "Registration Response:\n$response\n";

// Test login
echo "\n--- Testing Login ---\n";
$_POST = [
    'email' => 'test3@example.com',
    'password' => 'TestPass123!'
];

ob_start();
include_once __DIR__ . '/backend/api/login.php';
$loginResponse = ob_get_clean();

echo "Login Response:\n$loginResponse\n";