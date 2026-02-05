<?php
require_once '../app/Core/init.php';

// Test database connection and events table structure
$db = new Database();

try {
    // Check if cover_image column exists
    $query = "SHOW COLUMNS FROM events LIKE 'cover_image'";
    $result = $db->query($query);
    
    echo "<h2>Database Column Check</h2>";
    if ($result && count($result) > 0) {
        echo "<p style='color: green;'>✓ cover_image column EXISTS in events table</p>";
        echo "<pre>";
        print_r($result[0]);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ cover_image column DOES NOT exist in events table</p>";
        echo "<p>Running migration...</p>";
        
        // Try to add the column
        $alterQuery = "ALTER TABLE events ADD COLUMN cover_image VARCHAR(500) NULL AFTER image_url";
        try {
            $db->query($alterQuery);
            echo "<p style='color: green;'>✓ cover_image column added successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>Note: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check a sample event
    echo "<h2>Sample Event Data Check</h2>";
    $eventQuery = "SELECT id, title, cover_image, image_url FROM events WHERE created_by_type='publisher' ORDER BY id DESC LIMIT 5";
    $events = $db->query($eventQuery);
    
    if ($events && count($events) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Title</th><th>cover_image</th><th>image_url</th></tr>";
        foreach ($events as $event) {
            echo "<tr>";
            echo "<td>{$event->id}</td>";
            echo "<td>{$event->title}</td>";
            echo "<td>" . ($event->cover_image ?? 'NULL') . "</td>";
            echo "<td>" . ($event->image_url ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No events found</p>";
    }
    
    // Check all columns in events table
    echo "<h2>All Events Table Columns</h2>";
    $columnsQuery = "SHOW COLUMNS FROM events";
    $columns = $db->query($columnsQuery);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        $highlight = ($col->Field == 'cover_image' || $col->Field == 'image_url') ? "style='background-color: yellow;'" : "";
        echo "<tr $highlight>";
        echo "<td><strong>{$col->Field}</strong></td>";
        echo "<td>{$col->Type}</td>";
        echo "<td>{$col->Null}</td>";
        echo "<td>" . ($col->Key ?? '') . "</td>";
        echo "<td>" . ($col->Default ?? 'NULL') . "</td>";
        echo "<td>" . ($col->Extra ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
