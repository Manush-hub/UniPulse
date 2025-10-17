<?php
/**
 * Test Script for Profile-Based Database Management
 * This script tests the new event filtering functionality
 */

require_once __DIR__ . '/../app/Core/init.php';
require_once __DIR__ . '/../app/models/Event.php';

echo "=== Profile-Based Database Management Test ===\n\n";

// Test 1: Get all public events (accessible to everyone)
echo "Test 1: Public Events (accessible to all users)\n";
echo "================================================\n";
$event = new Event();
$publicEvents = $event->getEventsForUser('public', null);

foreach ($publicEvents as $e) {
    echo "- {$e->title} ({$e->university_name}) - Visibility: " . ($e->visibility ?? 'public') . "\n";
}
echo "\nTotal public events: " . count($publicEvents) . "\n\n";

// Test 2: Get events for University of Moratuwa users
echo "Test 2: Events for University of Moratuwa Users\n";
echo "===============================================\n";
$moratuwaEvents = $event->getEventsForUser('university', 'university-of-moratuwa');

foreach ($moratuwaEvents as $e) {
    echo "- {$e->title} ({$e->university_name}) - Visibility: " . ($e->visibility ?? 'public') . "\n";
}
echo "\nTotal events for Moratuwa users: " . count($moratuwaEvents) . "\n\n";

// Test 3: Get events for University of Colombo users
echo "Test 3: Events for University of Colombo Users\n";
echo "==============================================\n";
$colomboEvents = $event->getEventsForUser('university', 'university-of-colombo');

foreach ($colomboEvents as $e) {
    echo "- {$e->title} ({$e->university_name}) - Visibility: " . ($e->visibility ?? 'public') . "\n";
}
echo "\nTotal events for Colombo users: " . count($colomboEvents) . "\n\n";

// Test 4: Test filtering by category
echo "Test 4: Technology Events for Public Users\n";
echo "==========================================\n";
$techEvents = $event->getEventsForUser('public', null, ['category' => 'technology']);

if ($techEvents === false || $techEvents === null) {
    echo "No technology events found or query error\n";
    $techEvents = [];
}

foreach ($techEvents as $e) {
    echo "- {$e->title} ({$e->university_name}) - Category: {$e->category}\n";
}
echo "\nTotal tech events for public: " . count($techEvents) . "\n\n";

// Test 5: Test search functionality
echo "Test 5: Search for 'workshop' for University Users\n";
echo "==================================================\n";
$workshopEvents = $event->getEventsForUser('university', 'university-of-moratuwa', ['search' => 'workshop']);

if ($workshopEvents === false || $workshopEvents === null) {
    echo "No workshop events found or query error\n";
    $workshopEvents = [];
}

foreach ($workshopEvents as $e) {
    echo "- {$e->title} ({$e->university_name}) - Matches: workshop\n";
}
echo "\nTotal workshop events for Moratuwa: " . count($workshopEvents) . "\n\n";

// Test 6: Verify access control
echo "Test 6: Access Control Verification\n";
echo "===================================\n";

// Get all events
$allEvents = $event->getAllEvents();
echo "Total events in database: " . count($allEvents) . "\n";

// Count by visibility
$publicCount = 0;
$universityCount = 0;

foreach ($allEvents as $e) {
    if (!isset($e->visibility) || $e->visibility === 'public') {
        $publicCount++;
    } else {
        $universityCount++;
    }
}

echo "Public events: $publicCount\n";
echo "University-only events: $universityCount\n\n";

// Verify that public users get fewer events than university users
$publicUserEvents = count($event->getEventsForUser('public', null));
$universityUserEvents = count($event->getEventsForUser('university', 'university-of-moratuwa'));

echo "Events visible to public users: $publicUserEvents\n";
echo "Events visible to Moratuwa university users: $universityUserEvents\n";

if ($universityUserEvents >= $publicUserEvents) {
    echo "✅ Access control working correctly - university users see same or more events\n";
} else {
    echo "❌ Access control issue - university users see fewer events than public users\n";
}

echo "\n=== Test Complete ===\n";
?>