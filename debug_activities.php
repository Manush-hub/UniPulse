<?php
require_once 'app/Core/Database.php';
require_once 'app/Core/Model.php';
require_once 'app/models/Event.php';

echo "<h2>Debug Recent Moderation Activities</h2>";

// Test 1: Check if events exist with is_deleted = 1
echo "<h3>Test 1: Check deleted events in database</h3>";
$event = new Event();
$conn = $event->connect();
$stmt = $conn->query("SELECT id, title, is_deleted, deleted_by, deleted_at, deletion_reason FROM events WHERE is_deleted = 1");
$deletedEvents = $stmt->fetchAll(PDO::FETCH_OBJ);
echo "<p>Found " . count($deletedEvents) . " deleted events</p>";
if (!empty($deletedEvents)) {
    echo "<pre>";
    print_r($deletedEvents);
    echo "</pre>";
}

// Test 2: Test getRecentModerationActivities without moderator filter
echo "<h3>Test 2: getRecentModerationActivities (no filter)</h3>";
$activities = $event->getRecentModerationActivities(null, 10);
echo "<p>Found " . count($activities) . " activities</p>";
if (!empty($activities)) {
    echo "<pre>";
    print_r($activities);
    echo "</pre>";
}

// Test 3: Test with moderator ID 21 (from your screenshot)
echo "<h3>Test 3: getRecentModerationActivities (moderator ID = 21)</h3>";
$activities = $event->getRecentModerationActivities(21, 10);
echo "<p>Found " . count($activities) . " activities</p>";
if (!empty($activities)) {
    echo "<pre>";
    print_r($activities);
    echo "</pre>";
}

// Test 4: Check moderators table
echo "<h3>Test 4: Check moderators table</h3>";
$stmt = $conn->query("SELECT id, full_name, university FROM moderators");
$moderators = $stmt->fetchAll(PDO::FETCH_OBJ);
echo "<pre>";
print_r($moderators);
echo "</pre>";
