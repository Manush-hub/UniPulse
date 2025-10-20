<?php
session_start();

// Check if we want to create a test session
if (isset($_GET['create_test_session'])) {
    // Set session data as AuthService expects
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 7; // Use existing moderator ID
    $_SESSION['user_email'] = 'vinuja@gmail.com';
    $_SESSION['user_name'] = 'Vinuja';
    $_SESSION['user_type'] = 'moderator';
    $_SESSION['user_table'] = 'moderators';
    
    // Legacy session keys for backward compatibility  
    $_SESSION['moderator_id'] = 7;
    $_SESSION['moderator_email'] = 'vinuja@gmail.com';
    $_SESSION['moderator_name'] = 'Vinuja';
    $_SESSION['moderator_university'] = 'university-of-colombo';
    
    echo "Test moderator session created!<br>";
    echo "Session data: " . print_r($_SESSION, true) . "<br>";
}

// Display current session
echo "<h1>Current Session Status</h1>";
echo "Session ID: " . session_id() . "<br>";
echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h2>Test Links</h2>";
echo "<a href='?create_test_session=1'>Create Test Moderator Session</a><br>";
echo "<a href='/unipulse/moderator/content-moderation'>Test Content Moderation</a><br>";
echo "<a href='/unipulse/moderator/user-reports'>Test User Reports</a><br>";

?>