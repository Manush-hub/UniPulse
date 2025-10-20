<?php
/**
 * Migration script to create publishers table and update users table for publisher support
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
    
    echo "Creating publishers table...\n";
    
    // Create publishers table
    $publishersTable = "
        CREATE TABLE IF NOT EXISTS publishers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            society_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            country_code VARCHAR(5) NOT NULL DEFAULT '+94',
            password_hash VARCHAR(255) NOT NULL,
            university VARCHAR(100) NOT NULL,
            faculty VARCHAR(100) NOT NULL,
            confirmation_document VARCHAR(500) NULL,
            approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            approved_by INT NULL,
            approved_at TIMESTAMP NULL,
            rejection_reason TEXT NULL,
            is_active BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_university (university),
            INDEX idx_faculty (faculty),
            INDEX idx_approval_status (approval_status),
            FOREIGN KEY (approved_by) REFERENCES moderators(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($publishersTable);
    echo "Table 'publishers' created successfully.\n";
    
    // Check if user_type enum needs to be updated to include 'publisher'
    $checkUserTypes = "SHOW COLUMNS FROM users LIKE 'user_type'";
    $result = $pdo->query($checkUserTypes)->fetch();
    
    if ($result) {
        $type = $result['Type'];
        if (strpos($type, 'publisher') === false) {
            echo "Updating users table to support publisher user type...\n";
            
            // Update user_type enum to include publisher
            $updateUserType = "ALTER TABLE users MODIFY COLUMN user_type ENUM('university', 'public', 'publisher', 'admin', 'moderator') NOT NULL";
            $pdo->exec($updateUserType);
            
            echo "Users table updated successfully.\n";
        } else {
            echo "Users table already supports publisher user type.\n";
        }
    }
    
    // Create publisher_approval_notifications table for tracking approval notifications
    $notificationsTable = "
        CREATE TABLE IF NOT EXISTS publisher_approval_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            publisher_id INT NOT NULL,
            moderator_id INT NOT NULL,
            notification_type ENUM('pending_approval', 'approved', 'rejected') NOT NULL,
            message TEXT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_publisher_id (publisher_id),
            INDEX idx_moderator_id (moderator_id),
            INDEX idx_notification_type (notification_type),
            FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE,
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($notificationsTable);
    echo "Table 'publisher_approval_notifications' created successfully.\n";
    
    echo "Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>