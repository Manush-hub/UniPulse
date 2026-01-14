<?php

/**
 * Database Migration: Add gallery column to university_users and public_users tables
 * This allows users to save their photo gallery albums
 */

require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = new Database();

    echo "Starting migration: Add gallery column to user tables...\n";

    $tables = ['university_users', 'public_users'];

    foreach ($tables as $table) {
        echo "\nChecking table: {$table}\n";

        // Check if column already exists
        $checkQuery = "SHOW COLUMNS FROM {$table} LIKE 'gallery'";
        $exists = $db->query($checkQuery);

        if ($exists) {
            echo "⊘ Column 'gallery' already exists in {$table}.\n";
        } else {
            // Add gallery column
            // Use LONGTEXT so multiple base64 images are not truncated
            $alterQuery = "ALTER TABLE {$table} ADD COLUMN gallery LONGTEXT NULL COMMENT 'User photo gallery albums stored as JSON'";
            $db->query($alterQuery);
            echo "✓ Added 'gallery' column to {$table}.\n";
        }
    }

    echo "\nMigration completed successfully!\n";
    echo "Users can now save and persist their photo gallery albums.\n";
} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}
