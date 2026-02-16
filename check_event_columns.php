<?php
require_once 'app/Core/config.php';

try {
    $conn = new PDO("mysql:host=localhost;dbname=unipulse", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get column information
    $stmt = $conn->query("SHOW COLUMNS FROM events LIKE '%image%'");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Image-related columns in events table:</h3>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // Get sample event data
    $stmt = $conn->query("SELECT * FROM events WHERE is_deleted = 0 AND status = 'upcoming' LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Sample event data (first upcoming event):</h3>";
    echo "<pre>";
    print_r($event);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
