<?php
require_once 'app/Core/config.php';

try {
    $conn = new PDO("mysql:host=localhost;dbname=unipulse", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query("SELECT id, title, cover_image, image_url FROM events WHERE is_deleted = 0 AND status = 'upcoming' AND CONCAT(event_date, ' ', event_time) >= NOW() ORDER BY event_date ASC, event_time ASC LIMIT 3");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    echo "Events for More Events section:\n\n";
    foreach ($events as $event) {
        echo "ID: " . $event['id'] . "\n";
        echo "Title: " . $event['title'] . "\n";
        echo "cover_image: " . ($event['cover_image'] ?: 'NULL') . "\n";
        echo "image_url: " . ($event['image_url'] ?: 'NULL') . "\n";
        
        if ($event['cover_image']) {
            $imagePath = "public/uploads/event-covers/" . $event['cover_image'];
            echo "Image path: " . $imagePath . "\n";
            echo "File exists: " . (file_exists($imagePath) ? 'YES' : 'NO') . "\n";
        }
        echo "\n---\n\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
