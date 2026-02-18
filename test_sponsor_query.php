<?php
/**
 * Test script to debug sponsorship query
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1>Debug Sponsorship Query</h1>";

try {
    $db = new Database();
    
    // Check events with accepts_sponsorships = 1
    echo "<h2>1. Events with accepts_sponsorships = 1</h2>";
    $sql1 = "SELECT id, title, event_date, status, visibility, is_deleted, accepts_sponsorships 
             FROM events 
             WHERE accepts_sponsorships = 1";
    $result1 = $db->query($sql1);
    
    echo "Found: " . count($result1) . " events<br>";
    if ($result1 && count($result1) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th><th>Deleted</th></tr>";
        foreach ($result1 as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>{$e->title}</td>";
            echo "<td>{$e->event_date}</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->visibility}</td>";
            echo "<td>" . ($e->is_deleted ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check if sponsorship packages table exists and has data
    echo "<h2>2. Sponsorship Packages</h2>";
    $sql2 = "SELECT * FROM event_sponsorship_packages";
    $result2 = $db->query($sql2);
    echo "Found: " . count($result2) . " packages<br>";
    if ($result2 && count($result2) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Event ID</th><th>Type</th><th>Available</th><th>Filled</th><th>Active</th></tr>";
        foreach ($result2 as $p) {
            echo "<tr>";
            echo "<td>{$p->id}</td>";
            echo "<td>{$p->event_id}</td>";
            echo "<td>{$p->package_type}</td>";
            echo "<td>{$p->available_slots}</td>";
            echo "<td>{$p->filled_slots}</td>";
            echo "<td>" . ($p->is_active ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No packages found! Run setup_test_sponsorships.php first.</p>";
    }
    
    // Test the actual controller query WITH packages
    echo "<h2>3. Controller Query (WITH packages requirement)</h2>";
    $sql3 = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
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
    
    $result3 = $db->query($sql3);
    echo "Found: " . count($result3) . " events<br>";
    if ($result3 && count($result3) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Packages</th><th>Slots</th></tr>";
        foreach ($result3 as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>{$e->title}</td>";
            echo "<td>{$e->event_date}</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->package_count}</td>";
            echo "<td>{$e->total_slots_available}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No events found with the INNER JOIN query.</p>";
        echo "<p>Testing simpler query without package requirement...</p>";
    }
    
    // Test simpler query WITHOUT package requirement
    echo "<h2>4. Simpler Query (WITHOUT packages requirement)</h2>";
    $sql4 = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
            e.event_end_time, e.venue, e.location, e.university, e.university_name, 
            e.faculty, e.organizer, e.image_url, e.cover_image, e.featured_image,
            e.status, e.visibility, e.created_by, e.created_by_type, e.accepts_sponsorships,
            e.requires_registration, e.is_deleted
            FROM events e
            WHERE e.accepts_sponsorships = 1 
            AND e.is_deleted = 0
            AND (e.visibility = 'public' OR e.visibility = 'university-only')
            AND e.status IN ('upcoming', 'ongoing')
            AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
            LIMIT 12";
    
    $result4 = $db->query($sql4);
    echo "Found: " . count($result4) . " events<br>";
    if ($result4 && count($result4) > 0) {
        echo "<p style='color: green;'><strong>✓ Events exist that accept sponsorships!</strong></p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th></tr>";
        foreach ($result4 as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>{$e->title}</td>";
            echo "<td>{$e->event_date}</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->visibility}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='background: #fff3cd; padding: 1rem; border: 1px solid #ffc107;'>";
        echo "<strong>Recommendation:</strong> Use this simpler query in the controller. ";
        echo "The INNER JOIN with packages is too restrictive and requires packages to be set up first.";
        echo "</p>";
    }
    
    echo "<h2>Summary</h2>";
    echo "<ul>";
    echo "<li>Events accepting sponsorships: " . count($result1) . "</li>";
    echo "<li>Sponsorship packages: " . count($result2) . "</li>";
    echo "<li>Events matching complex query (with packages): " . count($result3) . "</li>";
    echo "<li>Events matching simple query (without packages): " . count($result4) . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
