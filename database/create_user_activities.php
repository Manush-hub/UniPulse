<?php

/**
 * Database Migration: Create user_activities table
 * 
 * This table tracks user activities like event registrations, volunteer applications, etc.
 * Activities are automatically deleted after 7 days (1 week) based on activity_type retention.
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

    echo "Creating user_activities table...\n\n";

    // Create user_activities table
    $sql = "CREATE TABLE IF NOT EXISTS user_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
        activity_type ENUM('event_registration', 'volunteer_registration', 'event_cancellation', 'profile_update', 'badge_earned') NOT NULL,
        event_id INT NULL,
        event_title VARCHAR(255) NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        icon VARCHAR(50) DEFAULT 'calendar',
        activity_data JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        
        INDEX idx_user_id (user_id),
        INDEX idx_user_type (user_type),
        INDEX idx_activity_type (activity_type),
        INDEX idx_created_at (created_at),
        INDEX idx_expires_at (expires_at),
        INDEX idx_event_id (event_id),
        INDEX idx_user_activity (user_id, user_type, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✓ Created 'user_activities' table successfully.\n\n";

    echo "Table Structure:\n";
    echo "- id: Primary key\n";
    echo "- user_id: ID of the user who performed the activity\n";
    echo "- user_type: Type of user (university/public/publisher/sponsor)\n";
    echo "- activity_type: Type of activity (event_registration, volunteer_registration, etc.)\n";
    echo "- event_id: Reference to events table (if applicable)\n";
    echo "- event_title: Title of the event (for context)\n";
    echo "- title: Activity title to display\n";
    echo "- description: Activity description to display\n";
    echo "- icon: Icon type for display (calendar, plus, bell, award, etc.)\n";
    echo "- activity_data: JSON data for storing additional activity details\n";
    echo "- created_at: When the activity occurred\n";
    echo "- expires_at: When the activity should be removed from the 'recent' list (1 week)\n\n";

    echo "Activity Types:\n";
    echo "- event_registration: User registered for an event\n";
    echo "- volunteer_registration: User applied as a volunteer\n";
    echo "- event_cancellation: User cancelled event registration\n";
    echo "- profile_update: User updated their profile\n";
    echo "- badge_earned: User earned a badge/achievement\n\n";

    echo "✅ Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
