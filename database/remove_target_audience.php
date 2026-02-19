<?php
/**
 * Migration: Remove target_audience column from events table
 * Date: 2026-02-19
 * Description: Removes the target_audience column as the feature is no longer needed
 */

require_once __DIR__ . '/../app/Core/Database.php';

$db = new Database();
$conn = $db->connect();

try {
    echo "Starting migration to remove target_audience column...\n";
    
    // Check if column exists before removing
    $checkQuery = "SHOW COLUMNS FROM events LIKE 'target_audience'";
    $result = $conn->query($checkQuery);
    
    if ($result->rowCount() > 0) {
        // Column exists, remove it
        $sql = "ALTER TABLE events DROP COLUMN target_audience";
        $conn->exec($sql);
        echo "✓ Successfully removed target_audience column from events table\n";
    } else {
        echo "ℹ target_audience column does not exist in events table\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    die();
}
