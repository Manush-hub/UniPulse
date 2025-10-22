<?php
// Debug script to check event data structure
require_once __DIR__ . '/../app/Core/config.php';

// Check if we can connect to database
try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get a few events to check structure
    $stmt = $pdo->query("SELECT id, title, created_by, created_by_type, organizer FROM events LIMIT 5");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Events from Database:</h2>";
    echo "<pre>";
    foreach ($events as $event) {
        print_r($event);
        echo "\n" . str_repeat("-", 50) . "\n";
    }
    echo "</pre>";
    
    // Check publishers table structure
    $stmt = $pdo->query("SELECT id, society_name, email FROM publishers LIMIT 3");
    $publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Publishers from Database:</h2>";
    echo "<pre>";
    foreach ($publishers as $publisher) {
        print_r($publisher);
        echo "\n" . str_repeat("-", 30) . "\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>