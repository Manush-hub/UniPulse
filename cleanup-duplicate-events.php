<?php
require_once 'app/Core/init.php';

echo "<h2>Database Cleanup: Remove Duplicate Events</h2>";
// Use a small helper class to access Database trait methods
class DbProbe { use Database; }
$db = new DbProbe();

// Get all upcoming events
$allEvents = $db->query("SELECT id, title FROM events WHERE status = 'upcoming' ORDER BY created_at ASC") ?: [];

echo "<p>Total upcoming events: " . count($allEvents) . "</p>";

// Find duplicates by title
$titleMap = [];
$duplicateIds = [];

foreach ($allEvents as $event) {
    if (isset($titleMap[$event->title])) {
        $duplicateIds[] = $event->id;
        echo "<p style='color: orange;'>Found duplicate: <strong>" . $event->title . "</strong> (ID: " . $event->id . ")</p>";
    } else {
        $titleMap[$event->title] = $event->id;
    }
}

if (empty($duplicateIds)) {
    echo "<p style='color: green;'><strong>No duplicates found!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>Found " . count($duplicateIds) . " duplicate events to remove:</strong></p>";
    
    // Check which related tables exist
    $tables = [];
    $tableCheck = $db->query("SHOW TABLES") ?: [];
    foreach ($tableCheck as $row) {
        $tableName = array_values((array)$row)[0];
        $tables[$tableName] = true;
    }
    
    // Delete duplicates
    foreach ($duplicateIds as $id) {
        // First delete any related records (only if tables exist)
        if (isset($tables['sponsor_posts'])) {
            $db->query("DELETE FROM sponsor_posts WHERE event_id = :id", ['id' => $id]);
        }
        if (isset($tables['sponsorship_proposals'])) {
            $db->query("DELETE FROM sponsorship_proposals WHERE event_id = :id", ['id' => $id]);
        }
        if (isset($tables['event_registrations'])) {
            $db->query("DELETE FROM event_registrations WHERE event_id = :id", ['id' => $id]);
        }
        
        // Then delete the event
        $db->query("DELETE FROM events WHERE id = :id", ['id' => $id]);
        
        echo "<p style='color: green;'>✓ Deleted event ID: " . $id . "</p>";
    }
    
    echo "<p style='color: green;'><strong>Cleanup complete! Database is now clean.</strong></p>";
}

echo "<hr>";
echo "<p><a href='/unipulse/public/sponsor/events' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Browse Events →</a></p>";
?>
