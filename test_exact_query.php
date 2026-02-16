<?php
require_once 'app/Core/config.php';

$dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
$pdo = new PDO($dsn, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h1>Testing Sponsorship Query (Exact Same as Controller)</h1>";

// The EXACT query from the controller
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

try {
    $stmt = $pdo->query($sql);
    $events = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<div style='background: " . (count($events) > 0 ? "#d4edda" : "#f8d7da") . "; padding: 1.5rem; border-radius: 8px; margin: 1rem 0;'>";
    echo "<h2>Query Result: " . count($events) . " event(s) found</h2>";
    echo "</div>";
    
    if (count($events) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th><th>Accepts Sponsorships</th></tr>";
        
        foreach ($events as $e) {
            echo "<tr>";
            echo "<td><strong>{$e->id}</strong></td>";
            echo "<td>" . htmlspecialchars($e->title) . "</td>";
            echo "<td>" . date('M d, Y', strtotime($e->event_date)) . "</td>";
            echo "<td>{$e->status}</td>";
            echo "<td>{$e->visibility}</td>";
            echo "<td style='text-align: center;'><span style='color: green;'>✓ {$e->accepts_sponsorships}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; margin: 2rem 0;'>";
        echo "<p><strong>✅ These events SHOULD be showing on the sponsor page!</strong></p>";
        echo "<p>If they're not showing, the issue is in how the data is passed to the view.</p>";
        echo "</div>";
    } else {
        // Break down the conditions
        echo "<h2>Breaking Down the Query Conditions:</h2>";
        
        $tests = [
            "Events with accepts_sponsorships = 1" => 
                "SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1",
            "+ Not deleted" => 
                "SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0",
            "+ Correct visibility" => 
                "SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only')",
            "+ Correct status" => 
                "SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only') AND status IN ('upcoming', 'ongoing')",
            "+ Future date" => 
                "SELECT COUNT(*) as cnt FROM events WHERE accepts_sponsorships = 1 AND is_deleted = 0 AND (visibility = 'public' OR visibility = 'university-only') AND status IN ('upcoming', 'ongoing') AND event_date >= CURDATE()"
        ];
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f8f9fa;'><th>Condition</th><th>Count</th></tr>";
        
        foreach ($tests as $label => $testSql) {
            $stmt = $pdo->query($testSql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $count = $result->cnt;
            $color = $count > 0 ? '#d4edda' : '#f8d7da';
            
            echo "<tr style='background: $color;'>";
            echo "<td><strong>$label</strong></td>";
            echo "<td style='text-align: center; font-size: 1.2rem;'><strong>$count</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show the actual event that should match
        echo "<h2>Event ID 118 Details:</h2>";
        $checkSql = "SELECT * FROM events WHERE id = 118";
        $stmt = $pdo->query($checkSql);
        $event = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($event) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Value</th><th>Expected</th><th>Match?</th></tr>";
            
            $checks = [
                ['accepts_sponsorships', $event->accepts_sponsorships, '1', $event->accepts_sponsorships == 1],
                ['is_deleted', $event->is_deleted, '0', $event->is_deleted == 0],
                ['visibility', $event->visibility, 'public or university-only', in_array($event->visibility, ['public', 'university-only'])],
                ['status', $event->status, 'upcoming or ongoing', in_array($event->status, ['upcoming', 'ongoing'])],
                ['event_date', $event->event_date, '>= ' . date('Y-m-d'), strtotime($event->event_date) >= strtotime(date('Y-m-d'))]
            ];
            
            foreach ($checks as $check) {
                $color = $check[3] ? '#d4edda' : '#f8d7da';
                echo "<tr style='background: $color;'>";
                echo "<td><strong>{$check[0]}</strong></td>";
                echo "<td>{$check[1]}</td>";
                echo "<td>{$check[2]}</td>";
                echo "<td style='text-align: center;'>" . ($check[3] ? '✓ Yes' : '✗ No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 8px; margin: 2rem 0;'>";
    echo "<h3>Current Date Info:</h3>";
    $dateStmt = $pdo->query("SELECT CURDATE() as today, NOW() as now");
    $dateInfo = $dateStmt->fetch(PDO::FETCH_OBJ);
    echo "<p><strong>Database CURDATE():</strong> {$dateInfo->today}</p>";
    echo "<p><strong>Database NOW():</strong> {$dateInfo->now}</p>";
    echo "<p><strong>PHP date():</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px;'>";
    echo "<p style='color: #721c24;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
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
</style>
