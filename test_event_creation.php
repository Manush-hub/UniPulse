<?php
require_once __DIR__ . '/app/Core/config.php';
require_once __DIR__ . '/app/Core/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/models/Event.php';

echo "Testing Event Creation\n";
echo "=====================\n\n";

try {
    $event = new Event();
    
    // Test data
    $testData = [
        'title' => 'Test Event',
        'description' => 'This is a test event description',
        'category' => 'technology',
        'event_date' => '2025-01-15',
        'event_time' => '14:00:00',
        'location' => 'Main Auditorium',
        'university' => 'university-of-colombo',
        'university_name' => 'University of Colombo',
        'organizer' => 'Test Society',
        'organizer_email' => 'test@example.com',
        'max_participants' => 100,
        'status' => 'upcoming',
        'participants' => 0,
        'created_by' => 1,
        'created_by_type' => 'publisher',
        'visibility' => 'public'
    ];
    
    echo "Testing createEvent method...\n";
    $result = $event->createEvent($testData);
    
    if ($result['success']) {
        echo "✅ Event creation successful!\n";
        echo "Message: " . $result['message'] . "\n\n";
        
        // Check if event was actually inserted
        $events = $event->query("SELECT * FROM events WHERE title = 'Test Event'");
        if ($events) {
            echo "✅ Event found in database:\n";
            $createdEvent = $events[0];
            echo "  - ID: {$createdEvent->id}\n";
            echo "  - Title: {$createdEvent->title}\n";
            echo "  - Category: {$createdEvent->category}\n";
            echo "  - Date: {$createdEvent->event_date}\n";
            echo "  - Location: {$createdEvent->location}\n";
            
            // Clean up - remove test event
            echo "\nCleaning up test event...\n";
            $event->query("DELETE FROM events WHERE id = :id", ['id' => $createdEvent->id]);
            echo "✅ Test event removed\n";
        }
    } else {
        echo "❌ Event creation failed\n";
        if (isset($result['errors'])) {
            echo "Errors:\n";
            foreach ($result['errors'] as $field => $error) {
                echo "  - {$field}: {$error}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>