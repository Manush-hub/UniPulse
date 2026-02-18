<?php
/**
 * Quick script to enable sponsorships on ALL upcoming events
 */

require_once 'app/Core/config.php';

echo "<h1>Quick Fix: Enable Sponsorships</h1>";

try {
    // Direct database connection using config constants
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    
    // Helper function to run queries
    $runQuery = function($sql, $params = []) use ($pdo) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    };
    
    // Get ALL upcoming events
    $sql = "SELECT id, title, event_date, status, visibility, accepts_sponsorships, is_deleted 
            FROM events 
            WHERE is_deleted = 0 
            AND event_date >= CURDATE()
            ORDER BY event_date ASC";
    
    $events = $runQuery($sql);
    
    if (!$events || count($events) == 0) {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
        echo "<h3>❌ No upcoming events found</h3>";
        echo "<p>You need to create some events with future dates first.</p>";
        echo "</div>";
        exit;
    }
    
    echo "<p>Found <strong>" . count($events) . "</strong> upcoming events</p>";
    
    // Count how many need updating
    $needUpdate = 0;
    foreach ($events as $e) {
        if ($e->accepts_sponsorships == 0) {
            $needUpdate++;
        }
    }
    
    if ($needUpdate == 0) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
        echo "<h3>✅ All events already accept sponsorships!</h3>";
        echo "<p>But they might not be showing because of status or visibility issues.</p>";
        echo "</div>";
    }
    
    // Show current status
    echo "<h3>Current Events:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th><th>Accepts Sponsorships</th><th>Issues</th></tr>";
    
    foreach ($events as $e) {
        $issues = [];
        if ($e->accepts_sponsorships == 0) $issues[] = "Sponsorships disabled";
        if (!in_array($e->status, ['upcoming', 'ongoing'])) $issues[] = "Wrong status: " . $e->status;
        if (!in_array($e->visibility, ['public', 'university-only'])) $issues[] = "Wrong visibility: " . $e->visibility;
        
        $rowStyle = !empty($issues) ? 'background: #fff3cd;' : 'background: #d4edda;';
        
        echo "<tr style='$rowStyle'>";
        echo "<td>{$e->id}</td>";
        echo "<td>" . htmlspecialchars($e->title) . "</td>";
        echo "<td>" . date('M d, Y', strtotime($e->event_date)) . "</td>";
        echo "<td><strong>{$e->status}</strong></td>";
        echo "<td>{$e->visibility}</td>";
        echo "<td style='text-align: center;'>" . ($e->accepts_sponsorships ? '✓ Yes' : '✗ No') . "</td>";
        echo "<td>" . (empty($issues) ? '✓ OK' : implode('<br>', $issues)) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Quick fix button
    if ($needUpdate > 0 || true) {
        echo "<form method='POST' style='margin: 2rem 0;'>";
        echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 1.5rem; border-radius: 8px;'>";
        echo "<h3>🔧 Quick Fix Options:</h3>";
        
        echo "<div style='margin: 1rem 0;'>";
        echo "<button type='submit' name='action' value='enable_all' style='background: #28a745; color: white; padding: 1rem 2rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-right: 1rem;'>";
        echo "✓ Enable Sponsorships on ALL Events";
        echo "</button>";
        
        echo "<button type='submit' name='action' value='fix_all' style='background: #667eea; color: white; padding: 1rem 2rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer;'>";
        echo "🔧 Fix ALL Issues (Sponsorships + Status + Visibility)";
        echo "</button>";
        echo "</div>";
        
        echo "<p style='color: #856404; margin: 0.5rem 0;'><small>This will update all upcoming events to accept sponsorships and have proper status/visibility.</small></p>";
        echo "</div>";
        echo "</form>";
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'enable_all') {
            // Just enable sponsorships
            $updateSql = "UPDATE events 
                         SET accepts_sponsorships = 1 
                         WHERE is_deleted = 0 
                         AND event_date >= CURDATE()";
            $result = $pdo->exec($updateSql);
            
            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;'>";
            echo "<h3>✅ SUCCESS!</h3>";
            echo "<p>Enabled sponsorships on all upcoming events.</p>";
            echo "<p><a href='/unipulse/public/sponsor/events' style='background: #667eea; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>→ View Sponsor Events Page</a></p>";
            echo "</div>";
            
        } elseif ($action === 'fix_all') {
            // Fix everything
            $updateSql = "UPDATE events 
                         SET accepts_sponsorships = 1,
                             status = CASE 
                                 WHEN status NOT IN ('upcoming', 'ongoing') THEN 'upcoming'
                                 ELSE status 
                             END,
                             visibility = CASE 
                                 WHEN visibility NOT IN ('public', 'university-only') THEN 'public'
                                 ELSE visibility 
                             END
                         WHERE is_deleted = 0 
                         AND event_date >= CURDATE()";
            $result = $pdo->exec($updateSql);
            
            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;'>";
            echo "<h3>✅ SUCCESS!</h3>";
            echo "<p>Fixed all issues on upcoming events:</p>";
            echo "<ul>";
            echo "<li>✓ Enabled sponsorships</li>";
            echo "<li>✓ Set status to 'upcoming' where needed</li>";
            echo "<li>✓ Set visibility to 'public' where needed</li>";
            echo "</ul>";
            echo "<p><a href='/unipulse/public/sponsor/events' style='background: #667eea; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold; margin-top: 1rem;'>→ View Sponsor Events Page</a></p>";
            echo "</div>";
        }
        
        // Refresh data
        echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
    }
    
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
table { margin: 1rem 0; }
th, td { padding: 0.75rem; text-align: left; }
th { background: #f8f9fa; }
</style>
