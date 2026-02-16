<?php
require_once 'app/Core/config.php';

$dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
$pdo = new PDO($dsn, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h1>Enabling Sponsorships...</h1>";

// Update all upcoming events
$sql = "UPDATE events 
        SET accepts_sponsorships = 1,
            visibility = CASE 
                WHEN visibility = 'private' THEN 'public'
                ELSE visibility 
            END
        WHERE is_deleted = 0 
        AND event_date >= CURDATE()";

$affected = $pdo->exec($sql);

echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 2rem; border-radius: 8px; margin: 2rem 0;'>";
echo "<h2 style='color: #155724; margin-top: 0;'>✅ SUCCESS!</h2>";
echo "<p style='color: #155724; font-size: 1.1rem;'>Updated <strong>$affected</strong> event(s)</p>";
echo "<ul style='color: #155724;'>";
echo "<li>✓ Enabled sponsorships on all upcoming events</li>";
echo "<li>✓ Changed private events to public (so sponsors can see them)</li>";
echo "</ul>";
echo "</div>";

// Verify the changes
echo "<h2>Verification - Events Now Available for Sponsorship:</h2>";

$verifySql = "SELECT e.id, e.title, e.event_date, e.status, e.visibility, e.accepts_sponsorships
              FROM events e
              WHERE e.accepts_sponsorships = 1 
              AND e.is_deleted = 0
              AND (e.visibility = 'public' OR e.visibility = 'university-only')
              AND e.status IN ('upcoming', 'ongoing')
              AND e.event_date >= CURDATE()
              ORDER BY e.event_date ASC";

$stmt = $pdo->query($verifySql);
$events = $stmt->fetchAll(PDO::FETCH_OBJ);

if (count($events) > 0) {
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
    echo "<p style='color: #0c5460;'><strong>Found " . count($events) . " event(s) that will show to sponsors!</strong></p>";
    echo "</div>";
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%; margin: 1rem 0;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Title</th><th>Date</th><th>Status</th><th>Visibility</th><th>Sponsorships</th></tr>";
    
    foreach ($events as $e) {
        echo "<tr style='background: #d4edda;'>";
        echo "<td><strong>{$e->id}</strong></td>";
        echo "<td>" . htmlspecialchars($e->title) . "</td>";
        echo "<td>" . date('M d, Y', strtotime($e->event_date)) . "</td>";
        echo "<td><span style='background: #667eea; color: white; padding: 0.25rem 0.75rem; border-radius: 4px;'>{$e->status}</span></td>";
        echo "<td>{$e->visibility}</td>";
        echo "<td style='text-align: center;'><span style='color: green; font-weight: bold;'>✓ Yes</span></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;'>";
    echo "<h3 style='margin-top: 0;'>🎉 Ready to Test!</h3>";
    echo "<ol style='font-size: 1.05rem;'>";
    echo "<li><strong>Log in as a Sponsor</strong> user</li>";
    echo "<li><strong>Visit:</strong> <a href='/unipulse/public/sponsor/events' style='color: #667eea; font-weight: bold; text-decoration: none; padding: 0.25rem 0.5rem; background: #e7e9fc; border-radius: 4px;'>/unipulse/public/sponsor/events</a></li>";
    echo "<li><strong>You should see</strong> the \"Sponsorship Opportunities\" section with " . count($events) . " event(s)</li>";
    echo "</ol>";
    echo "<p style='margin-bottom: 0;'><a href='/unipulse/public/sponsor/events' style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 1rem;'>→ Go to Sponsor Events Page</a></p>";
    echo "</div>";
    
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 8px;'>";
    echo "<p style='color: #721c24;'><strong>⚠️ No events found!</strong> This shouldn't happen. Check the database.</p>";
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
</style>
