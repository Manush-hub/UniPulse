<?php
require_once __DIR__ . '/app/Core/config.php';

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking Events Database\n";
    echo "=======================\n\n";
    
    // Check if events table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'events'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Events table exists\n\n";
        
        // Get table structure
        echo "Table structure:\n";
        $stmt = $pdo->query("DESCRIBE events");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']}\n";
        }
        echo "\n";
        
        // Count total events
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Total events in database: {$count}\n\n";
        
        if ($count > 0) {
            echo "Existing events:\n";
            $stmt = $pdo->query("SELECT id, title, category, event_date, status, created_by, created_by_type FROM events ORDER BY event_date ASC LIMIT 10");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "  - ID: {$row['id']}, Title: {$row['title']}, Category: {$row['category']}, Date: {$row['event_date']}, Status: {$row['status']}, Created by: {$row['created_by']} ({$row['created_by_type']})\n";
            }
        }
        
    } else {
        echo "❌ Events table does not exist\n";
        
        echo "Available tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "  - {$row[0]}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>