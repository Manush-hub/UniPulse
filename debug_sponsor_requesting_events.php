<?php
/**
 * Debug script to check why sponsor requesting events are not showing
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1>Debug: Sponsor Requesting Events</h1>";

try {
    $db = new Database();
    
    // Check if event_sponsorship_packages table exists
    echo "<h2>1. Check if event_sponsorship_packages table exists</h2>";
    $result = $db->query("SHOW TABLES LIKE 'event_sponsorship_packages'");
    if ($result && count($result) > 0) {
        echo "✓ Table exists<br>";
    } else {
        echo "✗ Table does NOT exist<br>";
        echo "<strong>Solution: Run database/create_event_sponsorships.php</strong><br>";
    }
    
    // Check table structure
    echo "<h2>2. Check table structure</h2>";
    $structure = $db->query("DESCRIBE event_sponsorship_packages");
    echo "<pre>";
    print_r($structure);
    echo "</pre>";
    
    // Check if there are any sponsorship packages
    echo "<h2>3. Check if there are any sponsorship packages</h2>";
    $packages = $db->query("SELECT * FROM event_sponsorship_packages");
    echo "Total packages: " . count($packages) . "<br>";
    if ($packages && count($packages) > 0) {
        echo "<pre>";
        print_r($packages);
        echo "</pre>";
    } else {
        echo "No packages found<br>";
    }
    
    // Check events with accepts_sponsorships = 1
    echo "<h2>4. Check events that accept sponsorships</h2>";
    $events = $db->query("SELECT id, title, accepts_sponsorships, status, is_deleted, event_date FROM events WHERE accepts_sponsorships = 1");
    echo "Total events accepting sponsorships: " . count($events) . "<br>";
    if ($events && count($events) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Deleted</th><th>Date</th></tr>";
        foreach ($events as $event) {
            echo "<tr>";
            echo "<td>" . $event->id . "</td>";
            echo "<td>" . $event->title . "</td>";
            echo "<td>" . $event->status . "</td>";
            echo "<td>" . $event->is_deleted . "</td>";
            echo "<td>" . $event->event_date . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No events accepting sponsorships<br>";
    }
    
    // Check the actual query used in the controller
    echo "<h2>5. Test the actual query from controller</h2>";
    $sql = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
            e.event_end_time, e.venue, e.location, e.university, e.university_name, 
            e.faculty, e.organizer, e.image_url, e.cover_image, e.featured_image,
            e.status, e.visibility, e.created_by, e.created_by_type, e.accepts_sponsorships,
            e.requires_registration, e.is_deleted,
            COUNT(DISTINCT esp.id) as package_count,
            SUM(esp.available_slots - esp.filled_slots) as total_slots_available
            FROM events e
            INNER JOIN event_sponsorship_packages esp ON e.id = esp.event_id
            WHERE e.accepts_sponsorships = 1 
            AND e.is_deleted = 0
            AND esp.is_active = 1
            AND (esp.available_slots - esp.filled_slots) > 0
            AND (e.visibility = 'public' OR e.visibility = 'university-only')
            AND e.status IN ('upcoming', 'ongoing')
            AND e.event_date >= CURDATE()
            GROUP BY e.id
            ORDER BY e.event_date ASC
            LIMIT 12";
    
    $sponsorEvents = $db->query($sql);
    echo "Events found by controller query: " . count($sponsorEvents) . "<br>";
    
    if ($sponsorEvents && count($sponsorEvents) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Date</th><th>Packages</th><th>Slots</th></tr>";
        foreach ($sponsorEvents as $event) {
            echo "<tr>";
            echo "<td>" . $event->id . "</td>";
            echo "<td>" . $event->title . "</td>";
            echo "<td>" . $event->status . "</td>";
            echo "<td>" . $event->event_date . "</td>";
            echo "<td>" . $event->package_count . "</td>";
            echo "<td>" . $event->total_slots_available . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<strong>No events found matching the criteria</strong><br>";
        echo "<br>Possible reasons:<br>";
        echo "- No events have accepts_sponsorships = 1<br>";
        echo "- No active sponsorship packages exist<br>";
        echo "- All packages are fully booked (filled_slots >= available_slots)<br>";
        echo "- No upcoming/ongoing events<br>";
        echo "- Events are not public or university-only<br>";
    }
    
    // Check individual conditions
    echo "<h2>6. Break down the conditions</h2>";
    
    echo "<h3>Events with accepts_sponsorships = 1 AND not deleted:</h3>";
    $test1 = $db->query("SELECT id, title FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0");
    echo "Count: " . count($test1) . "<br>";
    
    echo "<h3>Active packages with available slots:</h3>";
    $test2 = $db->query("SELECT event_id, package_type, available_slots, filled_slots FROM event_sponsorship_packages WHERE is_active = 1 AND (available_slots - filled_slots) > 0");
    echo "Count: " . count($test2) . "<br>";
    if ($test2 && count($test2) > 0) {
        echo "<pre>";
        print_r($test2);
        echo "</pre>";
    }
    
    echo "<h3>Events with correct visibility:</h3>";
    $test3 = $db->query("SELECT id, title, visibility FROM events WHERE (visibility = 'public' OR visibility = 'university-only') AND is_deleted = 0");
    echo "Count: " . count($test3) . "<br>";
    
    echo "<h3>Upcoming/ongoing events:</h3>";
    $test4 = $db->query("SELECT id, title, status, event_date FROM events WHERE status IN ('upcoming', 'ongoing') AND event_date >= CURDATE() AND is_deleted = 0");
    echo "Count: " . count($test4) . "<br>";
    if ($test4 && count($test4) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Date</th></tr>";
        foreach ($test4 as $e) {
            echo "<tr><td>{$e->id}</td><td>{$e->title}</td><td>{$e->status}</td><td>{$e->event_date}</td></tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
