<?php
/**
 * Database Migration: Add current_participants column to events table
 * 
 * This migration adds a new column to track the actual number of participants
 * who have registered or purchased tickets for an event.
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Starting migration: Adding current_participants column...\n\n";
    
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM events LIKE 'current_participants'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "Column 'current_participants' already exists in events table.\n";
    } else {
        // Add current_participants column
        $sql = "ALTER TABLE events 
                ADD COLUMN current_participants INT DEFAULT 0 NOT NULL 
                AFTER participants";
        
        $pdo->exec($sql);
        echo "✓ Added 'current_participants' column to events table.\n";
    }
    
    // Make max_participants nullable (optional field)
    $stmt = $pdo->query("SHOW COLUMNS FROM events WHERE Field = 'max_participants'");
    $column = $stmt->fetch();
    
    if ($column && $column['Null'] === 'NO') {
        $sql = "ALTER TABLE events 
                MODIFY COLUMN max_participants INT NULL";
        
        $pdo->exec($sql);
        echo "✓ Modified 'max_participants' to be nullable (optional).\n";
    } else {
        echo "Column 'max_participants' is already nullable.\n";
    }
    
    // Add index for performance
    try {
        $pdo->exec("CREATE INDEX idx_current_participants ON events(current_participants)");
        echo "✓ Added index on 'current_participants' column.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "Index 'idx_current_participants' already exists.\n";
        } else {
            throw $e;
        }
    }
    
    echo "\n✅ Migration completed successfully!\n\n";
    echo "Summary:\n";
    echo "- current_participants: Tracks actual registrations and ticket purchases\n";
    echo "- max_participants: Now optional (NULL = unlimited participants)\n";
    echo "- When max_participants is NULL, participant count won't be displayed in UI\n";
    echo "- When max_participants is set, it shows: current_participants / max_participants\n";
    
} catch(PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
