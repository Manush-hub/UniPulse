<?php
require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to database
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);

    echo "Connected to database successfully.\n";

    // Add new columns to events table
    $alterQueries = [
        // Location related fields
        "ALTER TABLE events ADD COLUMN location_type ENUM('inside-university', 'outside-university') DEFAULT 'inside-university' AFTER location",
        "ALTER TABLE events ADD COLUMN venue_name VARCHAR(255) NULL AFTER location_type",
        "ALTER TABLE events ADD COLUMN street_address VARCHAR(255) NULL AFTER venue_name",
        "ALTER TABLE events ADD COLUMN city VARCHAR(100) NULL AFTER street_address",
        "ALTER TABLE events ADD COLUMN district_province VARCHAR(100) NULL AFTER city",
        "ALTER TABLE events ADD COLUMN faculty_department VARCHAR(255) NULL AFTER district_province",

        // Audience and participation fields
        "ALTER TABLE events ADD COLUMN target_audience ENUM('university-students', 'public-users', 'both') DEFAULT 'university-students' AFTER max_participants",

        // Ticket related fields
        "ALTER TABLE events ADD COLUMN ticket_type ENUM('free-all', 'paid-all', 'mixed') DEFAULT 'free-all' AFTER target_audience",
        "ALTER TABLE events ADD COLUMN registration_limit INT NULL AFTER ticket_type",
        "ALTER TABLE events ADD COLUMN registration_start_date DATE NULL AFTER registration_limit",
        "ALTER TABLE events ADD COLUMN registration_start_time TIME NULL AFTER registration_start_date",
        "ALTER TABLE events ADD COLUMN registration_end_date DATE NULL AFTER registration_start_time",
        "ALTER TABLE events ADD COLUMN registration_end_time TIME NULL AFTER registration_end_date",
        "ALTER TABLE events ADD COLUMN ticket_types JSON NULL AFTER registration_end_time",

        // Custom fields
        "ALTER TABLE events ADD COLUMN custom_fields JSON NULL AFTER ticket_types",

        // Volunteer related fields
        "ALTER TABLE events ADD COLUMN needs_volunteers BOOLEAN DEFAULT FALSE AFTER custom_fields",
        "ALTER TABLE events ADD COLUMN volunteer_sources JSON NULL AFTER needs_volunteers",
        "ALTER TABLE events ADD COLUMN volunteers_needed INT NULL AFTER volunteer_sources",
        "ALTER TABLE events ADD COLUMN volunteer_positions JSON NULL AFTER volunteers_needed",

        // Donation field
        "ALTER TABLE events ADD COLUMN accepts_donations BOOLEAN DEFAULT FALSE AFTER volunteer_positions",

        // Additional fields for better tracking
        "ALTER TABLE events ADD COLUMN created_by INT NULL AFTER accepts_donations",
        "ALTER TABLE events ADD COLUMN created_by_type ENUM('admin', 'moderator', 'publisher', 'sponsor') NULL AFTER created_by",
        "ALTER TABLE events ADD COLUMN visibility ENUM('faculty-only', 'university-only', 'all-universities', 'public') DEFAULT 'public' AFTER created_by_type",
        "ALTER TABLE events ADD COLUMN cover_image VARCHAR(500) NULL AFTER image_url",

        // End time for events
        "ALTER TABLE events ADD COLUMN event_end_time TIME NULL AFTER event_time"
    ];

    foreach ($alterQueries as $query) {
        try {
            $pdo->exec($query);
            echo "✓ Executed: " . substr($query, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // If column already exists, continue
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠ Column already exists: " . substr($query, 0, 50) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "Query: " . $query . "\n";
            }
        }
    }

    // Update category enum to include new categories
    try {
        $updateCategoryEnum = "ALTER TABLE events MODIFY COLUMN category ENUM('academic', 'sports', 'cultural', 'technology', 'social', 'workshop', 'business', 'music') NOT NULL";
        $pdo->exec($updateCategoryEnum);
        echo "✓ Updated category enum with new options\n";
    } catch (PDOException $e) {
        echo "⚠ Category enum update: " . $e->getMessage() . "\n";
    }

    echo "\nDatabase update completed successfully!\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
