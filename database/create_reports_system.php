<?php

/**
 * Database setup for Reports system
 * Creates the reports table for user reports functionality
 */

require_once __DIR__ . '/../app/Core/init.php';

try {
    // Use the Database trait by creating a temporary class
    $tempClass = new class {
        use Database;
        public function getConnection() {
            return $this->connect();
        }
    };
    
    $pdo = $tempClass->getConnection();
    
    echo "Creating reports table...\n";
    
    // Create reports table
    $reportsTableSQL = "
    CREATE TABLE IF NOT EXISTS reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reporter_id INT NOT NULL,
        reported_content_type ENUM('event', 'comment', 'user', 'message') NOT NULL,
        reported_content_id INT NOT NULL,
        report_type ENUM('inappropriate', 'spam', 'misinformation', 'harassment', 'other') NOT NULL,
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        description TEXT NOT NULL,
        university VARCHAR(100) NOT NULL,
        status ENUM('pending', 'in_progress', 'resolved', 'dismissed') DEFAULT 'pending',
        assigned_moderator_id INT NULL,
        resolution TEXT NULL,
        action_taken TEXT NULL,
        resolved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_university (university),
        INDEX idx_status (status),
        INDEX idx_report_type (report_type),
        INDEX idx_priority (priority),
        INDEX idx_assigned_moderator (assigned_moderator_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($reportsTableSQL);
    echo "✓ Reports table created successfully\n";
    
    // Create event moderation notifications table
    echo "Creating event moderation notifications table...\n";
    
    $notificationsTableSQL = "
    CREATE TABLE IF NOT EXISTS event_moderation_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        moderator_id INT NOT NULL,
        notification_type ENUM('approved', 'rejected', 'needs_revision') NOT NULL,
        message TEXT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        INDEX idx_event_id (event_id),
        INDEX idx_moderator_id (moderator_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($notificationsTableSQL);
    echo "✓ Event moderation notifications table created successfully\n";
    
    // Add moderation columns to events table if they don't exist
    echo "Adding moderation columns to events table...\n";
    
    $addModerationColumnsSQL = [
        "ALTER TABLE events ADD COLUMN moderated_by INT NULL",
        "ALTER TABLE events ADD COLUMN moderated_at TIMESTAMP NULL",
        "ALTER TABLE events ADD COLUMN moderation_reason TEXT NULL"
    ];
    
    foreach ($addModerationColumnsSQL as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Column might already exist, continue
            if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
                strpos($e->getMessage(), 'column already exists') !== false) {
                echo "  ↳ Column already exists, skipping...\n";
                continue;
            }
        }
    }
    
    // Add indexes separately
    $addIndexesSQL = [
        "ALTER TABLE events ADD INDEX idx_moderated_by (moderated_by)",
        "ALTER TABLE events ADD INDEX idx_moderated_at (moderated_at)"
    ];
    
    foreach ($addIndexesSQL as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Index might already exist, continue
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "  ↳ Index already exists, skipping...\n";
                continue;
            }
        }
    }
    
    echo "✓ Moderation columns added to events table\n";
    
    // Insert sample reports for testing
    echo "Inserting sample reports...\n";
    
    $sampleReportsSQL = "
    INSERT IGNORE INTO reports (
        id, reporter_id, reported_content_type, reported_content_id, 
        report_type, priority, description, university, status, assigned_moderator_id
    ) VALUES 
    (1, 1, 'event', 1, 'inappropriate', 'high', 'Event description contains inappropriate language', 'university-of-colombo', 'pending', NULL),
    (2, 2, 'comment', 1, 'spam', 'medium', 'User posting spam comments repeatedly', 'university-of-colombo', 'in_progress', 1),
    (3, 3, 'event', 2, 'misinformation', 'high', 'Event contains misleading information about requirements', 'university-of-colombo', 'resolved', 1),
    (4, 4, 'user', 5, 'inappropriate', 'medium', 'User profile contains inappropriate content', 'university-of-colombo', 'pending', NULL)
    ";
    
    $pdo->exec($sampleReportsSQL);
    echo "✓ Sample reports inserted\n";
    
    echo "\n✅ Reports system database setup completed successfully!\n";
    echo "📊 Tables created:\n";
    echo "   - reports\n";
    echo "   - event_moderation_notifications\n";
    echo "   - events (moderation columns added)\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Setup completed! You can now use:\n";
echo "   - Content Moderation: /unipulse/public/moderator/contentmoderation\n";
echo "   - User Reports: /unipulse/public/moderator/userreports\n";
?>