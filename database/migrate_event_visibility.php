<?php
/**
 * Database Migration: Add visibility field to events table
 * This migration adds profile-based access control to events
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to database
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Connected to database successfully.\n";
    
    // Check if visibility column already exists
    $checkColumn = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = 'events' AND COLUMN_NAME = 'visibility'";
    $stmt = $pdo->prepare($checkColumn);
    $stmt->execute(['dbname' => DBNAME]);
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add visibility column to events table
        $addVisibilityColumn = "ALTER TABLE events 
                               ADD COLUMN visibility ENUM('public', 'university') DEFAULT 'public' 
                               AFTER university_name";
        $pdo->exec($addVisibilityColumn);
        echo "Added 'visibility' column to events table.\n";
        
        // Add index for better performance
        $addIndex = "ALTER TABLE events ADD INDEX idx_visibility (visibility)";
        $pdo->exec($addIndex);
        echo "Added index on 'visibility' column.\n";
        
        // Update existing events to set default visibility
        // Public events (inter-university or general events) remain public
        // University-specific events get university visibility
        $updateVisibility = "UPDATE events SET visibility = 'university' 
                           WHERE university IS NOT NULL AND university != ''";
        $pdo->exec($updateVisibility);
        echo "Updated visibility for existing events.\n";
        
        // Set some sample events as public (inter-university events)
        $publicEvents = [
            'Inter-University Cricket Championship',
            'Academic Research Symposium'
        ];
        
        foreach ($publicEvents as $eventTitle) {
            $updatePublic = "UPDATE events SET visibility = 'public' WHERE title = :title";
            $stmt = $pdo->prepare($updatePublic);
            $stmt->execute(['title' => $eventTitle]);
        }
        echo "Set inter-university events as public.\n";
        
    } else {
        echo "Visibility column already exists in events table.\n";
    }
    
    // Check if created_by column exists for tracking event creators
    $checkCreatedBy = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = 'events' AND COLUMN_NAME = 'created_by'";
    $stmt = $pdo->prepare($checkCreatedBy);
    $stmt->execute(['dbname' => DBNAME]);
    $createdByExists = $stmt->fetch();
    
    if (!$createdByExists) {
        // Add created_by fields for tracking
        $addCreatedBy = "ALTER TABLE events 
                        ADD COLUMN created_by INT NULL AFTER organizer_email,
                        ADD COLUMN created_by_type ENUM('admin', 'moderator', 'publisher', 'sponsor', 'university', 'public') NULL AFTER created_by";
        $pdo->exec($addCreatedBy);
        echo "Added 'created_by' and 'created_by_type' columns to events table.\n";
        
        // Add index for created_by
        $addCreatedByIndex = "ALTER TABLE events ADD INDEX idx_created_by (created_by, created_by_type)";
        $pdo->exec($addCreatedByIndex);
        echo "Added index on 'created_by' columns.\n";
    } else {
        echo "Created_by columns already exist in events table.\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
    // Display current events with their visibility settings
    echo "\nCurrent events visibility settings:\n";
    echo "===================================\n";
    $showEvents = "SELECT title, university_name, visibility FROM events ORDER BY title";
    $stmt = $pdo->query($showEvents);
    $events = $stmt->fetchAll();
    
    foreach ($events as $event) {
        echo "- {$event['title']} ({$event['university_name']}) - {$event['visibility']}\n";
    }
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}