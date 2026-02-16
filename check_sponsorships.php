<?php
require_once 'app/Core/init.php';

$eventModel = new Event();

echo "<h2>Checking Sponsorship Events</h2>\n";

$sql = "SELECT e.id, e.title, e.accepts_sponsorships, e.status, e.event_date,
        COUNT(esp.id) as package_count,
        SUM(esp.available_slots - esp.filled_slots) as available_slots
        FROM events e
        LEFT JOIN event_sponsorship_packages esp ON e.id = esp.event_id AND esp.is_active = 1
        WHERE e.accepts_sponsorships = 1 
        AND e.is_deleted = 0
        GROUP BY e.id
        LIMIT 5";

$results = $eventModel->query($sql);

if ($results) {
    echo "<pre>";
    foreach ($results as $event) {
        echo "ID: " . $event->id . "\n";
        echo "Title: " . $event->title . "\n";
        echo "Status: " . $event->status . "\n";
        echo "Event Date: " . $event->event_date . "\n";
        echo "Packages: " . $event->package_count . "\n";
        echo "Available Slots: " . ($event->available_slots ?: 0) . "\n";
        echo "---\n";
    }
    echo "</pre>";
} else {
    echo "<p>No sponsorship events found.</p>";
    echo "<p>To create test data, run the SQL in SPONSORSHIP_TEST_GUIDE.md</p>";
}
