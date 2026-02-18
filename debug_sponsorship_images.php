<?php
require_once __DIR__ . '/app/Core/Database.php';

$db = Database::getInstance()->getConnection();

// Check sponsorship events and their images
$sql = "SELECT e.id, e.title, e.cover_image, e.image_url, e.featured_image,
        COUNT(DISTINCT esp.id) as package_count
        FROM events e
        INNER JOIN event_sponsorship_packages esp ON e.id = esp.event_id
        WHERE e.accepts_sponsorships = 1 
        AND e.is_deleted = 0
        AND esp.is_active = 1
        GROUP BY e.id
        LIMIT 5";

$stmt = $db->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Sponsorship Events Debug</h2>";
echo "<p>Found " . count($events) . " events with sponsorships</p>";

foreach ($events as $event) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h3>Event ID: {$event['id']} - {$event['title']}</h3>";
    echo "<p><strong>Package Count:</strong> {$event['package_count']}</p>";
    echo "<p><strong>cover_image:</strong> " . ($event['cover_image'] ?? 'NULL') . "</p>";
    echo "<p><strong>image_url:</strong> " . ($event['image_url'] ?? 'NULL') . "</p>";
    echo "<p><strong>featured_image:</strong> " . ($event['featured_image'] ?? 'NULL') . "</p>";
    
    // Determine which image to use
    $displayImage = null;
    if (!empty($event['cover_image'])) {
        $displayImage = $event['cover_image'];
        echo "<p><strong>Using:</strong> cover_image</p>";
    } elseif (!empty($event['featured_image'])) {
        $displayImage = $event['featured_image'];
        echo "<p><strong>Using:</strong> featured_image</p>";
    } elseif (!empty($event['image_url'])) {
        $displayImage = $event['image_url'];
        echo "<p><strong>Using:</strong> image_url</p>";
    } else {
        echo "<p><strong>Using:</strong> No image available</p>";
    }
    
    if ($displayImage) {
        echo "<img src='{$displayImage}' style='max-width: 300px; height: auto;' />";
    }
    echo "</div>";
}
?>
