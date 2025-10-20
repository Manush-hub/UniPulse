<?php
session_start();

echo "Session Debug Info:\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Data: " . print_r($_SESSION, true) . "\n";

// Check cookies
echo "\nCookies:\n";
echo print_r($_COOKIE, true);

// Simulate login for testing
if (isset($_GET['login']) && $_GET['login'] === 'test') {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@example.com';
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_type'] = 'public';
    $_SESSION['user_table'] = 'public_users';
    echo "\nTest login set!\n";
}

// Check if AuthService works
require_once __DIR__ . '/app/Core/init.php';
echo "\nAuthService Test:\n";
echo "Is Logged In: " . (AuthService::isLoggedIn() ? 'YES' : 'NO') . "\n";
if (AuthService::isLoggedIn()) {
    $user = AuthService::getCurrentUser();
    echo "Current User: " . print_r($user, true) . "\n";
}
?>