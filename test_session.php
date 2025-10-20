<?php
session_start();
// Set up a fake publisher session for testing
$_SESSION['user'] = [
    'user_id' => 4,
    'type' => 'publisher',
    'email' => 'abc@gmail.com',
    'society_name' => 'abc'
];

echo "Test session set up. You can now test the contact form.\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";
?>