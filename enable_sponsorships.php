<?php
/**
 * Quick script to enable sponsorships on existing events
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1>Enable Sponsorships on Events</h1>";

try {
    $db = new Database();
    
    // Get upcoming events that don't have sponsorships enabled
    $sql = "SELECT id, title, event_date, status, accepts_sponsorships 
            FROM events 
            WHERE is_deleted = 0 
            AND status IN ('upcoming', 'ongoing')
            AND event_date >= CURDATE()
            ORDER BY event_date ASC
            LIMIT 10";
    
    $events = $db->query($sql);
    
    if (!$events || count($events) == 0) {
        echo "<p style='color: red;'>No upcoming events found. Please create some events first.</p>";
        exit;
    }
    
    echo "<p>Found " . count($events) . " upcoming events</p>";
    
    // If form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_ids'])) {
        $eventIds = $_POST['event_ids'];
        $updated = 0;
        
        foreach ($eventIds as $eventId) {
            $updateSql = "UPDATE events SET accepts_sponsorships = 1 WHERE id = ?";
            $result = $db->query($updateSql, [$eventId]);
            if ($result !== false) {
                $updated++;
            }
        }
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; margin: 1rem 0; border-radius: 5px;'>";
        echo "<strong>✓ Success!</strong> Updated $updated event(s) to accept sponsorships.<br>";
        echo "<a href='/unipulse/public/sponsor/events' style='color: #155724; font-weight: bold;'>→ View Sponsor Events Page</a>";
        echo "</div>";
        
        // Refresh the list
        $events = $db->query($sql);
    }
    
    // Display form
    echo "<form method='POST'>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th>Select</th><th>Event ID</th><th>Title</th><th>Date</th><th>Status</th><th>Accepts Sponsorships</th>";
    echo "</tr>";
    
    foreach ($events as $event) {
        $checked = $event->accepts_sponsorships == 1 ? 'checked disabled' : '';
        $rowStyle = $event->accepts_sponsorships == 1 ? 'background: #d4edda;' : '';
        
        echo "<tr style='$rowStyle'>";
        echo "<td style='text-align: center;'>";
        if ($event->accepts_sponsorships == 0) {
            echo "<input type='checkbox' name='event_ids[]' value='{$event->id}'>";
        } else {
            echo "✓";
        }
        echo "</td>";
        echo "<td>{$event->id}</td>";
        echo "<td>" . htmlspecialchars($event->title) . "</td>";
        echo "<td>" . date('M d, Y', strtotime($event->event_date)) . "</td>";
        echo "<td><span style='padding: 0.25rem 0.5rem; background: #667eea; color: white; border-radius: 4px; font-size: 0.85rem;'>{$event->status}</span></td>";
        echo "<td style='text-align: center;'>";
        echo $event->accepts_sponsorships == 1 ? 
            "<span style='color: green; font-weight: bold;'>Yes</span>" : 
            "<span style='color: #999;'>No</span>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check if there are any events that can be updated
    $canUpdate = false;
    foreach ($events as $event) {
        if ($event->accepts_sponsorships == 0) {
            $canUpdate = true;
            break;
        }
    }
    
    if ($canUpdate) {
        echo "<div style='margin-top: 1rem;'>";
        echo "<button type='submit' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem 2rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer;'>";
        echo "Enable Sponsorships for Selected Events";
        echo "</button>";
        echo "</div>";
    } else {
        echo "<p style='color: green; margin-top: 1rem;'><strong>All upcoming events already accept sponsorships!</strong></p>";
        echo "<p><a href='/unipulse/public/sponsor/events' style='color: #667eea; font-weight: bold;'>→ View Sponsor Events Page</a></p>";
    }
    
    echo "</form>";
    
    // Show summary
    $sponsorshipCount = 0;
    foreach ($events as $event) {
        if ($event->accepts_sponsorships == 1) {
            $sponsorshipCount++;
        }
    }
    
    echo "<div style='background: #f8f9fa; padding: 1rem; margin-top: 2rem; border-radius: 8px;'>";
    echo "<h3>Summary</h3>";
    echo "<ul>";
    echo "<li>Total upcoming events: " . count($events) . "</li>";
    echo "<li>Events accepting sponsorships: $sponsorshipCount</li>";
    echo "<li>Events not accepting sponsorships: " . (count($events) - $sponsorshipCount) . "</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}
table {
    margin: 1rem 0;
}
th {
    text-align: left;
    padding: 0.75rem;
}
td {
    padding: 0.75rem;
}
</style>
