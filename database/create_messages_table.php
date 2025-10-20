<?php
/**
 * Migration script to create messages table for publisher-sponsor communication
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
    
    echo "Creating messages table...\n";
    
    // Create messages table
    $messagesTable = "
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_user_id INT NOT NULL,
            from_user_type ENUM('publisher', 'sponsor', 'admin', 'moderator') NOT NULL,
            to_user_id INT NOT NULL,
            to_user_type ENUM('publisher', 'sponsor', 'admin', 'moderator') NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_from_user (from_user_id, from_user_type),
            INDEX idx_to_user (to_user_id, to_user_type),
            INDEX idx_is_read (is_read),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($messagesTable);
    echo "Table 'messages' created successfully.\n";
    
    echo "Messages system setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error creating messages table: " . $e->getMessage() . "\n";
    exit(1);
}
?>