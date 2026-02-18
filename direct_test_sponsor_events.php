<?php
/**
 * Direct test of the sponsor events page data
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1>Debug: Sponsor Events Page Data</h1>";

try {
    $db = new Database();
    
    // Check 1: Do we have events with accepts_sponsorships = 1?
    echo "<h2>1. Events with accepts_sponsorships = 1</h2>";
    $sql1 = "SELECT id, title, event_date, status, visibility, is_deleted, accepts_sponsorships 
             FROM events 
             WHERE accepts_sponsorships = 1 
             AND is_deleted = 0";
    $result1 = $db->query($sql1);
    
    echo "Total: " . count($result1) . " events<br>";
    if ($result1 && count($result1) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th></tr>";
        foreach ($result1 as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>{$e->title}</td>";
            echo "<td>{$e->event_date}</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->visibility}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'><strong>NO EVENTS FOUND!</strong></p>";
        echo "<p>You need to enable accepts_sponsorships on some events first.</p>";
        echo "<p><a href='/unipulse/enable_sponsorships.php'>→ Click here to enable sponsorships</a></p>";
    }
    
    // Check 2: Test the exact query used by controller
    echo "<h2>2. Test Controller Query (Exact Copy)</h2>";
    $sql2 = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
            e.event_end_time, e.venue, e.location, e.university, e.university_name, 
            e.faculty, e.organizer, e.image_url, e.cover_image, e.featured_image,
            e.status, e.visibility, e.created_by, e.created_by_type, e.accepts_sponsorships,
            e.requires_registration, e.is_deleted, e.max_participants, e.participants
            FROM events e
            WHERE e.accepts_sponsorships = 1 
            AND e.is_deleted = 0
            AND (e.visibility = 'public' OR e.visibility = 'university-only')
            AND e.status IN ('upcoming', 'ongoing')
            AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
            LIMIT 12";
    
    $result2 = $db->query($sql2);
    
    echo "Results: " . count($result2) . " events<br>";
    if ($result2 && count($result2) > 0) {
        echo "<p style='color: green;'><strong>✓ QUERY WORKS! Events found:</strong></p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th></tr>";
        foreach ($result2 as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>{$e->title}</td>";
            echo "<td>{$e->event_date}</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->visibility}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; margin: 1rem 0; border-radius: 5px;'>";
        echo "<strong>✓ The query is working correctly!</strong><br>";
        echo "The issue might be in how the controller passes data to the view.<br>";
        echo "Check the browser console and page source for debug comments.";
        echo "</div>";
    } else {
        echo "<p style='color: orange;'><strong>Query returned 0 results</strong></p>";
        echo "<p>Checking which condition is failing...</p>";
        
        // Break down conditions
        echo "<h3>Condition Breakdown:</h3>";
        
        echo "<strong>A. Events with accepts_sponsorships = 1 AND not deleted:</strong><br>";
        $testA = $db->query("SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0");
        echo "Count: " . $testA[0]->cnt . "<br><br>";
        
        echo "<strong>B. + visibility check:</strong><br>";
        $testB = $db->query("SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only')");
        echo "Count: " . $testB[0]->cnt . "<br><br>";
        
        echo "<strong>C. + status check:</strong><br>";
        $testC = $db->query("SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only') AND status IN ('upcoming', 'ongoing')");
        echo "Count: " . $testC[0]->cnt . "<br><br>";
        
        echo "<strong>D. + date check:</strong><br>";
        $testD = $db->query("SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only') AND status IN ('upcoming', 'ongoing') AND event_date >= CURDATE()");
        echo "Count: " . $testD[0]->cnt . "<br><br>";
        
        // Show current date
        $currentDate = $db->query("SELECT CURDATE() as today");
        echo "<strong>Current Date (CURDATE()):</strong> " . $currentDate[0]->today . "<br><br>";
        
        // Show all events with dates
        echo "<strong>All events with accepts_sponsorships = 1 (showing dates):</strong><br>";
        $allEvents = $db->query("SELECT id, title, event_date, status, visibility FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0");
        if ($allEvents && count($allEvents) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th></tr>";
            foreach ($allEvents as $e) {
                $isPast = strtotime($e->event_date) < strtotime($currentDate[0]->today);
                $rowStyle = $isPast ? 'background: #ffcccc;' : '';
                echo "<tr style='$rowStyle'>";
                echo "<td>{$e->id}</td>";
                echo "<td>{$e->title}</td>";
                echo "<td>{$e->event_date}" . ($isPast ? ' (PAST)' : '') . "</td>";
                echo "<td>{$e->status}</td>";
                echo "<td>{$e->visibility}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p style='color: red;'><strong>Issue:</strong> Events exist but are either in the past, have wrong status, or wrong visibility.</p>";
        }
    }
    
    // Check 3: Look at the actual controller file
    echo "<h2>3. Checking Controller File</h2>";
    $controllerPath = 'app/controllers/Sponsor/Events.php';
    if (file_exists($controllerPath)) {
        echo "<p style='color: green;'>✓ Controller file exists</p>";
        
        // Check if getEventsWithSponsorships method exists
        $controllerContent = file_get_contents($controllerPath);
        if (strpos($controllerContent, 'getEventsWithSponsorships') !== false) {
            echo "<p style='color: green;'>✓ getEventsWithSponsorships method exists</p>";
        } else {
            echo "<p style='color: red;'>✗ getEventsWithSponsorships method NOT found!</p>";
        }
        
        // Check if method is being called
        if (strpos($controllerContent, '$this->getEventsWithSponsorships') !== false) {
            echo "<p style='color: green;'>✓ Method is being called</p>";
        } else {
            echo "<p style='color: red;'>✗ Method is NOT being called!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Controller file NOT found!</p>";
    }
    
    echo "<h2>Quick Fix Options</h2>";
    echo "<ol>";
    echo "<li><a href='/unipulse/enable_sponsorships.php'><strong>Enable sponsorships on events</strong></a> - If events don't have accepts_sponsorships = 1</li>";
    echo "<li>Update event dates to be in the future</li>";
    echo "<li>Update event status to 'upcoming' or 'ongoing'</li>";
    echo "<li>Update event visibility to 'public' or 'university-only'</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}
table {
    margin: 1rem 0;
    border-collapse: collapse;
}
th {
    background: #f8f9fa;
    text-align: left;
    padding: 0.75rem;
}
td {
    padding: 0.75rem;
}
</style>
