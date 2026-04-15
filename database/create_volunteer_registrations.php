<?php

/**
 * Database Migration: Create volunteer_registrations table
 * 
 * This table tracks which users have registered as volunteers for which events
 * and stores their volunteer information.
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

    echo "Creating volunteer_registrations table...\n\n";

    // Create volunteer_registrations table
    $sql = "CREATE TABLE IF NOT EXISTS volunteer_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
        event_id INT NOT NULL,
        volunteer_position VARCHAR(255) NOT NULL,
        availability VARCHAR(100) NOT NULL,
        experience TEXT NOT NULL,
        motivation TEXT NOT NULL,
        skills TEXT NOT NULL,
        have_transportation TINYINT(1) DEFAULT 0,
        commitment_understanding TINYINT(1) DEFAULT 0,
        receive_updates TINYINT(1) DEFAULT 0,
        terms_accepted TINYINT(1) DEFAULT 1,
        status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_event_id (event_id),
        INDEX idx_user_id (user_id),
        INDEX idx_user_type (user_type),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at),
        
        UNIQUE KEY unique_volunteer_registration (event_id, user_id, user_type),
        
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✓ Created 'volunteer_registrations' table successfully.\n\n";

    echo "Table Structure:\n";
    echo "- id: Primary key\n";
    echo "- user_id: ID of volunteer user\n";
    echo "- user_type: Type of user (university/public/publisher/sponsor)\n";
    echo "- event_id: Reference to events table\n";
    echo "- volunteer_position: Position they're volunteering for\n";
    echo "- availability: Availability timeframe (full-day, morning-shift, etc.)\n";
    echo "- experience: Relevant volunteer experience\n";
    echo "- motivation: Why they want to volunteer\n";
    echo "- skills: Special skills they have\n";
    echo "- have_transportation: Whether they have transportation\n";
    echo "- commitment_understanding: Whether they understand commitment\n";
    echo "- receive_updates: Whether they want to receive updates\n";
    echo "- terms_accepted: Whether they accepted terms\n";
    echo "- status: pending, accepted, rejected, or withdrawn\n";
    echo "- created_at: When they registered as volunteer\n";
    echo "- updated_at: Last update timestamp\n\n";

    echo "✅ Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
