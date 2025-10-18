<?php
// This file should be accessed through the browser while logged in
session_start();
require_once __DIR__ . '/app/Core/init.php';

header('Content-Type: application/json');

$response = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'is_logged_in' => AuthService::isLoggedIn(),
    'current_user' => AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>