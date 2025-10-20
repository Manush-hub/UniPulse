<?php
require_once __DIR__ . '/app/Core/config.php';

try {
    $pdo = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🧪 Testing Comment System Database Setup\n\n";
    
    // Check if tables exist
    $tables = ['event_comments', 'notifications'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists\n";
            
            // Check table structure
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "   Columns: " . count($columns) . "\n";
            
            // Check if table has any data
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "   Records: $count\n";
        } else {
            echo "❌ Table '$table' does not exist\n";
        }
        echo "\n";
    }
    
    // Check events table for completed events
    echo "📊 Checking for completed events...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events WHERE status = 'completed'");
    $completedEvents = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   Completed events: $completedEvents\n\n";
    
    if ($completedEvents == 0) {
        echo "⚠️  No completed events found. Creating a test completed event...\n";
        
        // Update one event to completed status for testing
        $stmt = $pdo->prepare("UPDATE events SET status = 'completed' WHERE id = 1");
        if ($stmt->execute()) {
            echo "✅ Updated event ID 1 to completed status\n";
        } else {
            echo "❌ Failed to update event status\n";
        }
    }
    
    echo "\n🎉 Comment system database check complete!\n";
    
} catch(PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>