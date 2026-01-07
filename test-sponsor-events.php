<?php
require_once 'app/Core/init.php';

echo "=== SPONSOR EVENTS DIAGNOSTIC TEST ===\n\n";

// Test 1: Check database connection
echo "1. Database Connection: ";
try {
    class DbProbe { use Database; }
    $db = new DbProbe();
    echo "✓ Connected\n";
} catch (Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Check events table exists
echo "2. Events Table Exists: ";
$result = $db->query("SHOW TABLES LIKE 'events'");
echo ($result ? "✓ Yes\n" : "✗ No\n");

// Test 3: Count total events
echo "3. Total Events in Database: ";
$row = $db->getRow("SELECT COUNT(*) as total FROM events");
echo ($row ? $row->total : 0) . " events\n";

// Test 4: Count upcoming events
echo "4. Upcoming Events (status='upcoming'): ";
$upcomingRow = $db->getRow("SELECT COUNT(*) as total FROM events WHERE status = 'upcoming'");
echo ($upcomingRow ? $upcomingRow->total : 0) . " events\n";

// Test 5: Show sample upcoming events
echo "5. Sample Upcoming Events:\n";
$sampleEvents = $db->query("SELECT id, title, status, event_date FROM events WHERE status = 'upcoming' LIMIT 3") ?: [];
if ($sampleEvents && count($sampleEvents) > 0) {
    foreach ($sampleEvents as $event) {
        echo "   - ID: {$event->id}, Title: {$event->title}, Date: {$event->event_date}\n";
    }
} else {
    echo "   No upcoming events found!\n";
    echo "\n   ACTION REQUIRED: Create some test events with status='upcoming'\n";
}

// Test 6: Test getEventsSeekingSponsors method
echo "\n6. Testing getEventsSeekingSponsors() method:\n";
try {
    $eventModel = new Event();
    $events = $eventModel->getEventsSeekingSponsors(['status' => 'upcoming', 'limit' => 5, 'offset' => 0]);
    echo "   ✓ Method works - Found " . count($events) . " events\n";
    if (count($events) > 0) {
        echo "   First event: " . $events[0]->title . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Method error: " . $e->getMessage() . "\n";
}

// Test 7: Check controller exists
echo "\n7. SponsorEvents Controller: ";
echo (file_exists('app/controllers/Sponsor/Events.php') ? "✓ Exists\n" : "✗ Not found\n");

// Test 8: Check view exists
echo "8. Browse Events View: ";
echo (file_exists('app/views/Sponsor/browse-events.view.php') ? "✓ Exists\n" : "✗ Not found\n");

echo "\n=== END DIAGNOSTIC ===\n";
?>
