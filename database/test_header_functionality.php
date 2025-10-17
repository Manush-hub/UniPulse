<?php
/**
 * Test Script for Header User Information Display
 * This script tests the enhanced user display functionality
 */

require_once __DIR__ . '/../app/Core/init.php';
require_once __DIR__ . '/../app/Core/AuthService.php';
require_once __DIR__ . '/../app/Core/BaseUserController.php';

echo "=== Header User Information Test ===\n\n";

// Test 1: Get current user details method
echo "Test 1: AuthService getCurrentUserDetails() method\n";
echo "=================================================\n";

// First, let's test the method exists
if (method_exists('AuthService', 'getCurrentUserDetails')) {
    echo "✅ getCurrentUserDetails() method exists\n";
} else {
    echo "❌ getCurrentUserDetails() method does not exist\n";
}

// Test 2: Get university name mapping
echo "\nTest 2: University Name Mapping\n";
echo "================================\n";

$testUniversities = [
    'university-of-colombo',
    'university-of-moratuwa', 
    'university-of-peradeniya'
];

foreach ($testUniversities as $uni) {
    $name = AuthService::getUserUniversityName($uni);
    echo "- $uni -> $name\n";
}

// Test 3: Test database query for user details
echo "\nTest 3: Database Query Test\n";
echo "===========================\n";

// Query some test users from database
$authService = new AuthService();

// Test university users
$universityQuery = "SELECT id, full_name, university FROM university_users LIMIT 3";
$universityUsers = $authService->query($universityQuery);

if ($universityUsers) {
    echo "University users found:\n";
    foreach ($universityUsers as $user) {
        $universityName = AuthService::getUserUniversityName($user->university);
        echo "- ID: {$user->id}, Name: {$user->full_name}, University: {$universityName}\n";
    }
} else {
    echo "No university users found in database\n";
}

// Test public users
$publicQuery = "SELECT id, full_name FROM public_users LIMIT 3";
$publicUsers = $authService->query($publicQuery);

if ($publicUsers) {
    echo "\nPublic users found:\n";
    foreach ($publicUsers as $user) {
        echo "- ID: {$user->id}, Name: {$user->full_name}\n";
    }
} else {
    echo "\nNo public users found in database\n";
}

// Test 4: BaseUserController class
echo "\nTest 4: BaseUserController Class\n";
echo "================================\n";

if (class_exists('BaseUserController')) {
    echo "✅ BaseUserController class exists\n";
    
    $reflection = new ReflectionClass('BaseUserController');
    $methods = $reflection->getMethods();
    
    echo "Available methods:\n";
    foreach ($methods as $method) {
        if ($method->class === 'BaseUserController') {
            echo "- {$method->name}()\n";
        }
    }
} else {
    echo "❌ BaseUserController class does not exist\n";
}

echo "\n=== Test Complete ===\n";
?>