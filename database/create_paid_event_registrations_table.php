<?php

/**
 * Database Migration: Create paid_event_registrations table
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

    echo "Creating paid_event_registrations table...\n\n";

    $sql = "CREATE TABLE IF NOT EXISTS paid_event_registrations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        publisher_id INT NULL,
        payment_record_id INT NULL,
        event_title_snapshot VARCHAR(255) NOT NULL,
        publisher_name_snapshot VARCHAR(255) NULL,
        registered_user_id INT NOT NULL,
        registered_user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
        registered_user_name_snapshot VARCHAR(255) NOT NULL,
        registered_user_email_snapshot VARCHAR(255) NULL,
        registered_user_phone_snapshot VARCHAR(25) NULL,
        order_number VARCHAR(40) NOT NULL,
        ticket_tier_name VARCHAR(100) NOT NULL DEFAULT 'General',
        ticket_quantity INT NOT NULL DEFAULT 1,
        unit_price DECIMAL(12,2) NOT NULL,
        currency_code CHAR(3) NOT NULL DEFAULT 'LKR',
        subtotal_amount DECIMAL(12,2) NOT NULL,
        discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        service_fee_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        total_amount DECIMAL(12,2) NOT NULL,
        payment_status ENUM('unpaid', 'pending', 'paid', 'failed', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
        payment_method VARCHAR(50) NULL,
        payment_transaction_id VARCHAR(100) NULL,
        payment_gateway VARCHAR(50) NULL,
        paid_at DATETIME NULL,
        registration_status ENUM('reserved', 'confirmed', 'cancelled', 'checked_in', 'no_show') NOT NULL DEFAULT 'reserved',
        checked_in_at DATETIME NULL,
        cancelled_at DATETIME NULL,
        cancellation_reason VARCHAR(255) NULL,
        refund_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        refunded_at DATETIME NULL,
        registration_source ENUM('web', 'mobile', 'admin', 'import') NOT NULL DEFAULT 'web',
        metadata JSON NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        UNIQUE KEY uq_paid_order_number (order_number),
        KEY idx_paid_event_id (event_id),
        KEY idx_paid_publisher_id (publisher_id),
        KEY idx_paid_user (registered_user_id, registered_user_type),
        KEY idx_paid_payment_status (payment_status),
        KEY idx_paid_registration_status (registration_status),
        KEY idx_paid_paid_at (paid_at),
        KEY idx_paid_transaction (payment_transaction_id),

        CONSTRAINT fk_paid_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        CONSTRAINT fk_paid_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL,
        CONSTRAINT fk_paid_payment FOREIGN KEY (payment_record_id) REFERENCES payments(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);

    echo "✓ paid_event_registrations table created successfully.\n";
    echo "✅ Migration completed.\n";
} catch (Exception $e) {
    echo "✗ Error creating paid_event_registrations table: " . $e->getMessage() . "\n";
    exit(1);
}
