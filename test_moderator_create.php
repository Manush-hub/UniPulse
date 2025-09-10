<?php
// Test script to verify the moderator_create.view.php fix
require_once './app/Core/config.php';
require_once './app/Core/functions.php';
require_once './app/Core/Database.php';
require_once './app/Core/Model.php';
require_once './app/Core/Controller.php';

// Mock user data
$user = [
    'id' => 1,
    'name' => 'Test Administrator',
    'email' => 'admin@unipulse.com',
    'type' => 'admin'
];

// Include the view file
ob_start();
include './app/views/Admin/moderator_create.view.php';
$output = ob_get_clean();

// Check if the output contains the expected user name without warnings
if (strpos($output, 'Test Administrator') !== false) {
    echo "SUCCESS: User name is properly displayed\n";
} else {
    echo "ERROR: User name not found in output\n";
}

// Check for PHP warnings/errors
if (strpos($output, 'Warning:') !== false || strpos($output, 'Deprecated:') !== false) {
    echo "ERROR: PHP warnings/errors detected\n";
} else {
    echo "SUCCESS: No PHP warnings/errors detected\n";
}

echo "Test completed.\n";
?>
