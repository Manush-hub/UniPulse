<?php
/**
 * Migration script to add public/university user types to messages table enums.
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $conn = new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4",
        DBUSER,
        DBPASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    echo "Updating messages table user type enums...\n";

    $sql = "
        ALTER TABLE messages
        MODIFY COLUMN from_user_type ENUM('publisher', 'sponsor', 'admin', 'moderator', 'public', 'university') NOT NULL,
        MODIFY COLUMN to_user_type ENUM('publisher', 'sponsor', 'admin', 'moderator', 'public', 'university') NOT NULL
    ";

    $conn->exec($sql);
    echo "Messages table user type enums updated successfully.\n";
} catch (PDOException $e) {
    echo "Error updating messages table user type enums: " . $e->getMessage() . "\n";
    exit(1);
}
