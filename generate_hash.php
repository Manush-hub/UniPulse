<?php
// Generate password hash for testing
require_once __DIR__ . '/backend/classes/User.php';

$user = new User();
$password = 'password123';
$hash = $user->hashPassword($password);

echo "Password: $password\n";
echo "Hash: $hash\n";

// Test verification
$isValid = $user->verifyPassword($password, $hash);
echo "Verification: " . ($isValid ? 'PASS' : 'FAIL') . "\n";