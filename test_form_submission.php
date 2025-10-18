<?php
session_start();

// Simulate being logged in as a publisher
$_SESSION['USER'] = (object)[
    'id' => 1,
    'name' => 'Test Publisher',
    'email' => 'test@publisher.com',
    'role' => 'publisher',
    'status' => 'approved'
];

// Simulate POST data from the form
$_POST = [
    'title' => 'Sample Technology Conference',
    'description' => 'A comprehensive technology conference featuring the latest innovations in software development, AI, and machine learning.',
    'category' => 'technology',
    'event_date' => '2025-02-15',
    'event_time' => '09:00',
    'location' => 'Main Auditorium, University of Colombo',
    'university' => 'university-of-colombo',
    'university_name' => 'University of Colombo',
    'organizer' => 'Computer Society of UOC',
    'organizer_email' => 'compsoc@uoc.lk',
    'max_participants' => 200,
    'requirements' => 'Basic knowledge of programming'
];

// Set up the environment
require_once __DIR__ . '/app/Core/config.php';
require_once __DIR__ . '/app/Core/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/AuthService.php';
require_once __DIR__ . '/app/models/Event.php';
require_once __DIR__ . '/app/controllers/Publisher/Createevent.php';

echo "Testing Event Creation Form Submission\n";
echo "=====================================\n\n";

try {
    // Create an instance of the Createevent controller
    $controller = new Createevent();
    
    echo "Form data:\n";
    foreach ($_POST as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
    echo "\n";
    
    echo "Simulating form submission...\n";
    
    // Capture output
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Controller response:\n";
    echo $output . "\n";
    
    // Check if event was created
    $event = new Event();
    $events = $event->query("SELECT * FROM events WHERE title = 'Sample Technology Conference'");
    
    if ($events && count($events) > 0) {
        echo "✅ Event successfully created in database!\n";
        $createdEvent = $events[0];
        echo "Event details:\n";
        echo "  - ID: {$createdEvent->id}\n";
        echo "  - Title: {$createdEvent->title}\n";
        echo "  - Category: {$createdEvent->category}\n";
        echo "  - Date: {$createdEvent->event_date} {$createdEvent->event_time}\n";
        echo "  - Location: {$createdEvent->location}\n";
        echo "  - Organizer: {$createdEvent->organizer}\n";
        echo "  - Max Participants: {$createdEvent->max_participants}\n";
        echo "  - Status: {$createdEvent->status}\n";
        
        // Clean up - remove test event
        echo "\nCleaning up test event...\n";
        $event->query("DELETE FROM events WHERE id = :id", ['id' => $createdEvent->id]);
        echo "✅ Test event removed\n";
    } else {
        echo "❌ Event was not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>