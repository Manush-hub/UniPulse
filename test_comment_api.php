<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include the application
require_once 'app/Core/init.php';

echo "<h1>Comment API Test</h1>\n";

// Test 1: Check if UserComments controller can be loaded
try {
    require_once 'app/controllers/User/Comments.php';
    $controller = new UserComments();
    echo "<p style='color: green;'>✅ UserComments controller loaded successfully</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading UserComments controller: " . $e->getMessage() . "</p>\n";
    exit;
}

// Test 2: Check database connection
try {
    $comment = new Comment();
    echo "<p style='color: green;'>✅ Comment model loaded successfully</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading Comment model: " . $e->getMessage() . "</p>\n";
    exit;
}

// Test 3: Check if event exists and is completed
try {
    $event = new Event();
    $eventData = $event->getEventById(5);
    if ($eventData) {
        echo "<p style='color: green;'>✅ Event found - ID: {$eventData->id}, Title: {$eventData->title}, Status: {$eventData->status}</p>\n";
        if ($eventData->status === 'completed') {
            echo "<p style='color: green;'>✅ Event is completed - comments allowed</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ Event status is: {$eventData->status} - only completed events allow comments</p>\n";
        }
    } else {
        echo "<p style='color: red;'>❌ Event with ID 5 not found</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking event: " . $e->getMessage() . "</p>\n";
}

// Test 4: Test getting comments for the event
try {
    $comments = $comment->getEventComments(5);
    echo "<p style='color: green;'>✅ Successfully retrieved " . count($comments) . " comments for event 5</p>\n";
    
    if (count($comments) > 0) {
        echo "<h3>Existing Comments:</h3>\n";
        foreach ($comments as $c) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>\n";
            echo "<strong>User:</strong> {$c->user_name} ({$c->user_type})<br>\n";
            echo "<strong>Comment:</strong> {$c->comment_text}<br>\n";
            echo "<strong>Rating:</strong> " . ($c->rating ? $c->rating . "/5" : "No rating") . "<br>\n";
            echo "<strong>Date:</strong> {$c->created_at}<br>\n";
            echo "</div>\n";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error getting comments: " . $e->getMessage() . "</p>\n";
}

// Test 5: Check if there's a user logged in
echo "<h3>Session Information:</h3>\n";
echo "<p>Session ID: " . session_id() . "</p>\n";
echo "<p>Session data: " . print_r($_SESSION, true) . "</p>\n";

$isLoggedIn = AuthService::isLoggedIn();
echo "<p>Is logged in: " . ($isLoggedIn ? 'YES' : 'NO') . "</p>\n";

if ($isLoggedIn) {
    $currentUser = AuthService::getCurrentUser();
    echo "<p>Current user: " . print_r($currentUser, true) . "</p>\n";
} else {
    echo "<p style='color: orange;'>⚠️ No user logged in - this could be why comments aren't working</p>\n";
}
