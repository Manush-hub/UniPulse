<?php
require_once __DIR__ . '/../app/Core/Database.php';

/**
 * Create password_resets table
 * This table stores password reset tokens and their expiration times
 */

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Create password_resets table
    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        user_type ENUM('admin', 'moderator', 'public', 'university', 'sponsor', 'publisher') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NOT NULL,
        used TINYINT(1) DEFAULT 0,
        used_at TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_token (token),
        INDEX idx_expires_at (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "✅ Password resets table created successfully!\n";
    echo "   - Stores reset tokens with expiration times\n";
    echo "   - Tracks token usage\n";
    echo "   - Supports all user types\n";
    
} catch (PDOException $e) {
    echo "❌ Error creating password_resets table: " . $e->getMessage() . "\n";
    exit(1);
}
