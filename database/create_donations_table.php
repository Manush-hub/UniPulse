<?php

require_once __DIR__ . '/../app/Core/Database.php';

/**
 * Create donations table for tracking user donations to events
 */

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Create donations table
    $sql = "CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('public', 'university') NOT NULL DEFAULT 'public',
        event_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        currency VARCHAR(3) DEFAULT 'LKR',
        payment_method VARCHAR(50),
        payment_id VARCHAR(100),
        transaction_reference VARCHAR(100),
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        donor_name VARCHAR(100),
        donor_email VARCHAR(100),
        donor_phone VARCHAR(20),
        is_anonymous BOOLEAN DEFAULT FALSE,
        message TEXT,
        receipt_sent BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user (user_id, user_type),
        INDEX idx_event (event_id),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $conn->exec($sql);
    echo "✓ Donations table created successfully\n";
} catch (PDOException $e) {
    echo "Error creating donations table: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDonations table setup complete!\n";
