<?php
require_once dirname(__DIR__) . '/app/Core/config.php';

try {
    $pdo = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create event_comments table
    $createCommentsTable = "
        CREATE TABLE IF NOT EXISTS event_comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
            user_table ENUM('university_users', 'public_users', 'publishers', 'sponsors') NOT NULL,
            comment_text TEXT NOT NULL,
            rating INT NULL DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
            is_edited BOOLEAN DEFAULT FALSE,
            is_deleted BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            INDEX idx_event_id (event_id),
            INDEX idx_user_id (user_id),
            INDEX idx_user_type (user_type),
            INDEX idx_created_at (created_at),
            INDEX idx_active_comments (event_id, is_deleted),
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createCommentsTable);
    echo "✅ Event comments table created successfully!\n";
    
    // Create notifications table for publisher notifications
    $createNotificationsTable = "
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient_id INT NOT NULL,
            recipient_type ENUM('publisher', 'admin', 'moderator') NOT NULL,
            type ENUM('new_comment', 'comment_edited', 'comment_deleted', 'event_comment') NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            related_id INT NULL,
            related_type ENUM('event', 'comment', 'user') NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_recipient (recipient_id, recipient_type),
            INDEX idx_unread (recipient_id, recipient_type, is_read),
            INDEX idx_type (type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createNotificationsTable);
    echo "✅ Notifications table created successfully!\n";
    
    echo "\n📊 Tables created:\n";
    echo "- event_comments: For storing user comments on completed events\n";
    echo "- notifications: For publisher notifications about new comments\n";
    echo "\n🎉 Comment system database setup complete!\n";
    
} catch(PDOException $e) {
    echo "❌ Error creating tables: " . $e->getMessage() . "\n";
    exit(1);
}
?>