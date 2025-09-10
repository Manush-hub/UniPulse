<?php
require_once 'app/Core/init.php';
require_once 'app/Core/AuthService.php';

// Test moderator authentication
$email = 'moderator@unipulse.com';
$password = 'moderator123';

echo "Testing moderator authentication...\n";
echo "Email: $email\n";
echo "Password: $password\n\n";

$authService = new AuthService();

// Test the authentication
$result = $authService->authenticate($email, $password);

if ($result) {
    echo "✅ Authentication successful!\n";
    echo "User type: " . $result['type'] . "\n";
    echo "Dashboard: " . $result['dashboard'] . "\n";
    echo "User ID: " . $result['user']->id . "\n";
    echo "User name: " . $result['user']->full_name . "\n";
} else {
    echo "❌ Authentication failed!\n";
}

// Test admin authentication
echo "\n" . str_repeat("-", 50) . "\n";
echo "Testing admin authentication...\n";

$adminEmail = 'admin@unipulse.com';
$adminPassword = 'admin123';

echo "Email: $adminEmail\n";
echo "Password: $adminPassword\n\n";

$adminResult = $authService->authenticate($adminEmail, $adminPassword);

if ($adminResult) {
    echo "✅ Admin authentication successful!\n";
    echo "User type: " . $adminResult['type'] . "\n";
    echo "Dashboard: " . $adminResult['dashboard'] . "\n";
    echo "User ID: " . $adminResult['user']->id . "\n";
    echo "User name: " . $adminResult['user']->full_name . "\n";
} else {
    echo "❌ Admin authentication failed!\n";
}
?>
