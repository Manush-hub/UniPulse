<?php
/**
 * Simple Backend Test - No Sessions
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/backend/config/database.php';

try {
    echo "Testing database connection...\n";
    
    $db = DatabaseConfig::getInstance();
    echo "✓ Database connection successful!\n";
    
    // Test User class
    echo "\nTesting User class...\n";
    require_once __DIR__ . '/backend/classes/User.php';
    
    $user = new User();
    echo "✓ User class loaded successfully!\n";
    
    // Test password hashing
    $testPassword = 'TestPass123!';
    $hashedPassword = $user->hashPassword($testPassword);
    $isValid = $user->verifyPassword($testPassword, $hashedPassword);
    
    if ($isValid) {
        echo "✓ Password hashing test successful!\n";
    } else {
        echo "✗ Password hashing test failed!\n";
    }
    
    // Check existing users
    $result = $db->fetchAll("SELECT username, email, user_type FROM users LIMIT 5");
    
    if ($result) {
        echo "\nExisting users:\n";
        foreach ($result as $user) {
            echo "- {$user['username']} ({$user['email']}) - {$user['user_type']}\n";
        }
    } else {
        echo "\nNo users found in database.\n";
    }
    
    // Test login functionality
    echo "\nTesting login functionality...\n";
    $userObj = new User();
    $loginResult = $userObj->login('demo@unipulse.lk', 'password123');
    
    if ($loginResult['success']) {
        echo "✓ Login test successful!\n";
        echo "User data: " . json_encode($loginResult['user']) . "\n";
    } else {
        echo "✗ Login test failed: " . $loginResult['message'] . "\n";
    }
    
    echo "\n✓ Basic backend tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}