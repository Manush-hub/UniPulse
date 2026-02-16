<?php
/**
 * Final comprehensive test after fixing the venue column issue
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1 style='color: green;'>✓ Column Issue Fixed!</h1>";
echo "<p>The 'venue' column error has been fixed. The query now uses 'location' instead.</p>";

try {
    $db = new Database();
    
    // Test the fixed query
    echo "<h2>Testing the Fixed Query</h2>";
    $sql = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
            e.event_end_time, e.location, e.university, e.university_name, 
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
    
    $result = $db->query($sql);
    
    if ($result && count($result) > 0) {
        echo "<div style='background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;'>";
        echo "<h3 style='margin-top: 0;'>🎉 SUCCESS! Query is working!</h3>";
        echo "<p><strong>Found " . count($result) . " sponsorship events</strong></p>";
        echo "</div>";
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>ID</th><th>Title</th><th>Date</th><th>Location</th><th>Status</th><th>Visibility</th>";
        echo "</tr>";
        foreach ($result as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>" . htmlspecialchars($e->title) . "</td>";
            echo "<td>" . date('M d, Y', strtotime($e->event_date)) . "</td>";
            echo "<td>" . htmlspecialchars($e->location) . "</td>";
            echo "<td><span style='background: #667eea; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;'>{$e->status}</span></td>";
            echo "<td>{$e->visibility}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 1rem; margin: 2rem 0; border-radius: 8px;'>";
        echo "<h3>✅ Next Steps:</h3>";
        echo "<ol>";
        echo "<li><strong>Log in as a Sponsor</strong> user</li>";
        echo "<li><strong>Visit</strong>: <a href='/unipulse/public/sponsor/events' style='color: #667eea; font-weight: bold;'>/unipulse/public/sponsor/events</a></li>";
        echo "<li><strong>You should now see</strong> the \"Sponsorship Opportunities\" section with these " . count($result) . " event(s)</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; margin: 1rem 0; border-radius: 8px;'>";
        echo "<h3>⚠️ Query works but no events found</h3>";
        echo "<p>The query is syntactically correct now, but returned 0 results.</p>";
        echo "<p><strong>Possible reasons:</strong></p>";
        echo "<ul>";
        echo "<li>No events have <code>accepts_sponsorships = 1</code></li>";
        echo "<li>All sponsorship events are in the past</li>";
        echo "<li>Events have wrong status (need 'upcoming' or 'ongoing')</li>";
        echo "<li>Events have wrong visibility (need 'public' or 'university-only')</li>";
        echo "</ul>";
        echo "</div>";
        
        // Show what's available
        echo "<h3>Events with accepts_sponsorships = 1 (all dates):</h3>";
        $allSponsorship = $db->query("SELECT id, title, event_date, status, visibility FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0");
        if ($allSponsorship && count($allSponsorship) > 0) {
            echo "<p>Found " . count($allSponsorship) . " total event(s) accepting sponsorships:</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th><th>Issue</th></tr>";
            foreach ($allSponsorship as $e) {
                $isPast = strtotime($e->event_date) < time();
                $wrongStatus = !in_array($e->status, ['upcoming', 'ongoing']);
                $wrongVis = !in_array($e->visibility, ['public', 'university-only']);
                
                $issues = [];
                if ($isPast) $issues[] = "Past date";
                if ($wrongStatus) $issues[] = "Wrong status";
                if ($wrongVis) $issues[] = "Wrong visibility";
                
                $rowStyle = !empty($issues) ? 'background: #ffcccc;' : 'background: #ccffcc;';
                
                echo "<tr style='$rowStyle'>";
                echo "<td>{$e->id}</td>";
                echo "<td>{$e->title}</td>";
                echo "<td>{$e->event_date}</td>";
                echo "<td>{$e->status}</td>";
                echo "<td>{$e->visibility}</td>";
                echo "<td>" . (empty($issues) ? '✓ OK' : implode(', ', $issues)) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p><a href='/unipulse/enable_sponsorships.php' style='background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; display: inline-block; margin-top: 1rem;'>→ Manage Sponsorship Events</a></p>";
        } else {
            echo "<p style='color: red;'><strong>No events have sponsorships enabled!</strong></p>";
            echo "<p><a href='/unipulse/enable_sponsorships.php' style='background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; display: inline-block; margin-top: 1rem;'>→ Enable Sponsorships on Events</a></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; margin: 1rem 0; border-radius: 8px;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
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
}
th {
    text-align: left;
    padding: 0.75rem;
}
td {
    padding: 0.75rem;
}
code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
