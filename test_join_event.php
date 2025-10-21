<?php
/**
 * Test script for join event functionality
 * Tests the incrementParticipants method
 */

require_once __DIR__ . '/app/Core/config.php';
require_once __DIR__ . '/app/Core/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/models/Event.php';

$eventModel = new Event();

// Test with event ID 5 (Community Service Day)
$testEventId = 5;

echo "Testing Join Event Functionality\n";
echo "================================\n\n";

// Get initial state
$event = $eventModel->getEventById($testEventId);
echo "Initial State:\n";
echo "Event: {$event->title}\n";
echo "Current Participants: {$event->current_participants}\n";
echo "Max Participants: {$event->max_participants}\n";
echo "Available Spots: " . ($event->max_participants - $event->current_participants) . "\n\n";

// Test 1: Increment participants
echo "Test 1: Incrementing participants...\n";
if ($eventModel->incrementParticipants($testEventId)) {
    $event = $eventModel->getEventById($testEventId);
    echo "✓ Success! Current participants: {$event->current_participants}\n";
    echo "  Available spots: " . ($event->max_participants - $event->current_participants) . "\n\n";
} else {
    echo "✗ Failed to increment\n\n";
}

// Test 2: Increment again
echo "Test 2: Incrementing participants again...\n";
if ($eventModel->incrementParticipants($testEventId)) {
    $event = $eventModel->getEventById($testEventId);
    echo "✓ Success! Current participants: {$event->current_participants}\n";
    echo "  Available spots: " . ($event->max_participants - $event->current_participants) . "\n\n";
} else {
    echo "✗ Failed to increment\n\n";
}

// Test 3: Check hasAvailableSpots
echo "Test 3: Checking available spots...\n";
if ($eventModel->hasAvailableSpots($testEventId)) {
    $available = $eventModel->getAvailableSpots($testEventId);
    echo "✓ Event has available spots: {$available}\n\n";
} else {
    echo "✗ Event is full\n\n";
}

// Test 4: Decrement
echo "Test 4: Decrementing participants...\n";
if ($eventModel->decrementParticipants($testEventId)) {
    $event = $eventModel->getEventById($testEventId);
    echo "✓ Success! Current participants: {$event->current_participants}\n";
    echo "  Available spots: " . ($event->max_participants - $event->current_participants) . "\n\n";
} else {
    echo "✗ Failed to decrement\n\n";
}

// Test 5: Test with unlimited event (create one temporarily)
echo "Test 5: Testing unlimited capacity event...\n";
try {
    $pdo = new PDO('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DBNAME, DBUSER, DBPASS);
    
    // Update event 65 to have unlimited capacity
    $stmt = $pdo->prepare("UPDATE events SET max_participants = NULL, current_participants = 5 WHERE id = 65");
    $stmt->execute();
    
    $unlimitedEvent = $eventModel->getEventById(65);
    echo "Unlimited Event: {$unlimitedEvent->title}\n";
    echo "Current Participants: {$unlimitedEvent->current_participants}\n";
    echo "Max Participants: " . ($unlimitedEvent->max_participants === null ? 'Unlimited' : $unlimitedEvent->max_participants) . "\n";
    
    if ($eventModel->hasAvailableSpots(65)) {
        echo "✓ Has available spots (unlimited)\n";
        
        if ($eventModel->incrementParticipants(65)) {
            $unlimitedEvent = $eventModel->getEventById(65);
            echo "✓ Incremented! Current participants: {$unlimitedEvent->current_participants}\n";
        }
    }
    
    // Restore the event
    $stmt = $pdo->prepare("UPDATE events SET max_participants = 190, current_participants = 0 WHERE id = 65");
    $stmt->execute();
    echo "✓ Test event restored\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Final state
$event = $eventModel->getEventById($testEventId);
echo "Final State:\n";
echo "Event: {$event->title}\n";
echo "Current Participants: {$event->current_participants}\n";
echo "Max Participants: {$event->max_participants}\n";
echo "Available Spots: " . ($event->max_participants - $event->current_participants) . "\n";

echo "\n✅ All tests completed!\n";
?>
