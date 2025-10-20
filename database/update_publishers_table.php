<?php
/**
 * Migration script to update existing publishers table to match the approval system requirements
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
    
    echo "Updating publishers table structure...\n";
    
    // Check if approval_status column exists
    $checkColumn = "SHOW COLUMNS FROM publishers LIKE 'approval_status'";
    $result = $pdo->query($checkColumn)->fetch();
    
    if (!$result) {
        echo "Adding approval_status column...\n";
        
        // Rename verification_status to approval_status and update enum values
        $renameColumn = "ALTER TABLE publishers 
                        CHANGE COLUMN verification_status approval_status 
                        ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'";
        $pdo->exec($renameColumn);
        
        // Rename verification_notes to rejection_reason
        $renameNotes = "ALTER TABLE publishers 
                       CHANGE COLUMN verification_notes rejection_reason TEXT NULL";
        $pdo->exec($renameNotes);
        
        echo "Columns renamed successfully.\n";
    } else {
        echo "approval_status column already exists.\n";
    }
    
    // Check if approved_by column exists
    $checkApprovedBy = "SHOW COLUMNS FROM publishers LIKE 'approved_by'";
    $result = $pdo->query($checkApprovedBy)->fetch();
    
    if (!$result) {
        echo "Adding approved_by column...\n";
        
        // Add approved_by column
        $addApprovedBy = "ALTER TABLE publishers 
                         ADD COLUMN approved_by INT NULL AFTER rejection_reason";
        $pdo->exec($addApprovedBy);
        
        echo "approved_by column added.\n";
    } else {
        echo "approved_by column already exists.\n";
    }
    
    // Check if approved_at column exists
    $checkApprovedAt = "SHOW COLUMNS FROM publishers LIKE 'approved_at'";
    $result = $pdo->query($checkApprovedAt)->fetch();
    
    if (!$result) {
        echo "Adding approved_at column...\n";
        
        // Add approved_at column
        $addApprovedAt = "ALTER TABLE publishers 
                         ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by";
        $pdo->exec($addApprovedAt);
        
        echo "approved_at column added.\n";
    } else {
        echo "approved_at column already exists.\n";
    }
    
    // Check if foreign key exists for moderators
    $checkFK = "SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = '".DBNAME."' 
                AND TABLE_NAME = 'publishers' 
                AND COLUMN_NAME = 'approved_by' 
                AND REFERENCED_TABLE_NAME = 'moderators'";
    $result = $pdo->query($checkFK)->fetch();
    
    if (!$result) {
        echo "Adding foreign key constraint for approved_by...\n";
        try {
            // Add foreign key constraint
            $addFK = "ALTER TABLE publishers 
                     ADD CONSTRAINT fk_publishers_approved_by 
                     FOREIGN KEY (approved_by) REFERENCES moderators(id) ON DELETE SET NULL";
            $pdo->exec($addFK);
            echo "Foreign key constraint added.\n";
        } catch (PDOException $e) {
            echo "Warning: Could not add foreign key constraint: " . $e->getMessage() . "\n";
            echo "This might be because the moderators table doesn't exist or has different structure.\n";
        }
    } else {
        echo "Foreign key constraint already exists.\n";
    }
    
    // Update confirmation_document column to be longer
    echo "Updating confirmation_document column length...\n";
    $updateDocColumn = "ALTER TABLE publishers 
                       MODIFY COLUMN confirmation_document VARCHAR(500) NULL";
    $pdo->exec($updateDocColumn);
    
    // Create publisher_approval_notifications table if it doesn't exist
    echo "Creating publisher_approval_notifications table...\n";
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
            FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($notificationsTable);
    echo "publisher_approval_notifications table created/verified.\n";
    
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
    
    // Show final table structure
    echo "\nFinal publishers table structure:\n";
    $stmt = $pdo->query('DESCRIBE publishers');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>