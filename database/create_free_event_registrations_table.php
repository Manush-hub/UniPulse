<?php

/**
 * Database Migration: Create free_event_registrations table
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);

    echo "Creating free_event_registrations table...\n\n";

    $sql = "CREATE TABLE IF NOT EXISTS free_event_registrations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        publisher_id INT NULL,
        event_title_snapshot VARCHAR(255) NOT NULL,
        publisher_name_snapshot VARCHAR(255) NULL,
        registered_user_id INT NOT NULL,
        registered_user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
        registered_user_name_snapshot VARCHAR(255) NOT NULL,
        registered_user_email_snapshot VARCHAR(255) NULL,
        registered_user_phone_snapshot VARCHAR(25) NULL,
        registration_source ENUM('web', 'mobile', 'admin', 'import') NOT NULL DEFAULT 'web',
        status ENUM('registered', 'waitlisted', 'cancelled', 'checked_in', 'no_show') NOT NULL DEFAULT 'registered',
        registration_notes TEXT NULL,
        cancellation_reason VARCHAR(255) NULL,
        registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        checked_in_at DATETIME NULL,
        cancelled_at DATETIME NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        UNIQUE KEY uq_free_event_user (event_id, registered_user_id, registered_user_type),
        KEY idx_free_event_id (event_id),
        KEY idx_free_publisher_id (publisher_id),
        KEY idx_free_user (registered_user_id, registered_user_type),
        KEY idx_free_status (status),
        KEY idx_free_registered_at (registered_at),

        CONSTRAINT fk_free_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        CONSTRAINT fk_free_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);

    echo "✓ free_event_registrations table created successfully.\n";
    echo "✅ Migration completed.\n";
} catch (Exception $e) {
    echo "✗ Error creating free_event_registrations table: " . $e->getMessage() . "\n";
    exit(1);
}
