<?php
// Quick test to see if events controller is working
session_start();

// Simulate being logged in as user
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'User';

// Include necessary files
require_once 'app/Core/Controller.php';
require_once 'app/Core/Database.php';
require_once 'app/Core/AuthService.php';
require_once 'app/models/Event.php';

// Create Event model instance
$eventModel = new Event();

// Test getting all events
try {
    $events = $eventModel->getAllEvents([], null);
    echo "✅ Successfully retrieved " . count($events) . " events\n";
    echo "\n";
    
    if (count($events) > 0) {
        echo "Sample event data:\n";
        echo "ID: " . $events[0]->id . "\n";
        echo "Title: " . $events[0]->title . "\n";
        echo "Date: " . $events[0]->event_date . "\n";
        echo "Category: " . $events[0]->category . "\n";
    }
    
    // Test JSON encoding
    $serverData = [
        'events' => $events,
        'currentPage' => 1,
        'totalPages' => 1
    ];
    
    echo "\n✅ JSON encoding successful\n";
    echo "JSON length: " . strlen(json_encode($serverData)) . " bytes\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
