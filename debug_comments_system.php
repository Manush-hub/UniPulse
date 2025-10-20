<?php
session_start();
require_once __DIR__ . '/app/Core/init.php';

// Check if user is logged in
echo "=== User Session Debug ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Data: " . print_r($_SESSION, true) . "\n";
echo "Is Logged In: " . (AuthService::isLoggedIn() ? 'YES' : 'NO') . "\n";

if (AuthService::isLoggedIn()) {
    $user = AuthService::getCurrentUser();
    echo "Current User: " . print_r($user, true) . "\n";
}

// Check events and their statuses
echo "\n=== Events Status Debug ===\n";
$eventModel = new Event();
$events = $eventModel->getAllEvents();

foreach ($events as $event) {
    echo "Event ID: {$event['id']}\n";
    echo "Title: {$event['title']}\n";
    echo "Status: {$event['status']}\n";
    echo "Start Date: {$event['start_date']}\n";
    echo "End Date: {$event['end_date']}\n";
    echo "---\n";
}

// Test completed event specifically (ID 5)
echo "\n=== Testing Event ID 5 ===\n";
$eventId = 5;
$event = $eventModel->getEventById($eventId);
if ($event) {
    echo "Event Found: YES\n";
    echo "Status: {$event->status}\n";
    echo "Is Completed: " . ($event->status === 'completed' ? 'YES' : 'NO') . "\n";
} else {
    echo "Event Found: NO\n";
}

// Test the endpoint URL
echo "\n=== Testing Endpoint URLs ===\n";
echo "Comments API URL: /unipulse/public/user/comments/getComments?event_id=5\n";
echo "Check User Comment URL: /unipulse/public/user/comments/checkUserComment/5\n";

// Test if we can reach the comments controller
echo "\n=== Testing Comments Controller ===\n";
try {
    require_once __DIR__ . '/app/controllers/User/Comments.php';
    $commentsController = new Comments();
    echo "Comments Controller: LOADED\n";
} catch (Exception $e) {
    echo "Comments Controller ERROR: " . $e->getMessage() . "\n";
}

// Test direct comment check
if (AuthService::isLoggedIn()) {
    echo "\n=== Direct Comment Check ===\n";
    require_once __DIR__ . '/app/models/Comment.php';
    $commentModel = new Comment();
    $currentUser = AuthService::getCurrentUser();
    
    $hasCommented = $commentModel->hasUserCommented(5, $currentUser['id'], $currentUser['type']);
    echo "Has User Commented on Event 5: " . ($hasCommented ? 'YES' : 'NO') . "\n";
}

echo "\n=== Browser Debug Info ===\n";
echo "Visit these URLs in your browser:\n";
echo "1. Debug Page: http://localhost/unipulse/debug_comments.html\n";
echo "2. Event 5 Direct: http://localhost/unipulse/public/user/eventview?id=5\n";
echo "3. Comments API Test: http://localhost/unipulse/public/user/comments/checkUserComment/5\n";
?>