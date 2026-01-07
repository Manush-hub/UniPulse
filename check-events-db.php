<?php
require_once 'app/Core/init.php';

echo "<h2>Database Diagnostic</h2>";

// Small helper that uses the framework's Database trait
class DbProbe { use Database; }
$db = new DbProbe();

// Get all upcoming events
$result = $db->query("SELECT id, title, status FROM events WHERE status = 'upcoming' ORDER BY id ASC") ?: [];

echo "<p><strong>Total upcoming events in database:</strong> " . count($result) . "</p>";
echo "<hr>";

if (count($result) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th></tr>";
    
    $titles = [];
    foreach ($result as $event) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($event->id) . "</td>";
        echo "<td>" . htmlspecialchars($event->title) . "</td>";
        echo "<td>" . htmlspecialchars($event->status) . "</td>";
        echo "</tr>";
        
        $titles[$event->title] = ($titles[$event->title] ?? 0) + 1;
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Title Count Summary:</h3>";
    foreach ($titles as $title => $count) {
        $color = $count > 1 ? 'red' : 'green';
        echo "<p style='color: $color;'><strong>" . htmlspecialchars($title) . ":</strong> " . intval($count) . " occurrence(s)</p>";
    }
} else {
    echo "<p style='color: red;'>No upcoming events found!</p>";
}

echo "<hr>";
echo "<p><a href='/unipulse/public/sponsor/events' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>View Browse Events (Hard Refresh: Ctrl+Shift+R) →</a></p>";
?>
