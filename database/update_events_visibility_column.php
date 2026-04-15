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

    // Update visibility column to new ENUM values
    // New categories: Faculty Only, University Only, All Universities, Public
    // ENUM values: 'faculty-only', 'university-only', 'all-universities', 'public'

    echo "Updating visibility column with new categories...\n";

    try {
        // First, update existing data to match new values
        // Map old values to new equivalents
        $dataUpdates = [
            "UPDATE events SET visibility = 'public' WHERE visibility = 'public'",
            "UPDATE events SET visibility = 'university-only' WHERE visibility = 'university-only'",
            "UPDATE events SET visibility = 'faculty-only' WHERE visibility = 'private'"
        ];

        foreach ($dataUpdates as $update) {
            try {
                $stmt = $pdo->exec($update);
                echo "✓ Data migration: " . substr($update, 0, 60) . "...\n";
            } catch (PDOException $e) {
                echo "⚠ Data update: " . $e->getMessage() . "\n";
            }
        }

        // Now alter the column to use new ENUM values
        $alterQuery = "ALTER TABLE events MODIFY COLUMN visibility ENUM('faculty-only', 'university-only', 'all-universities', 'public') DEFAULT 'public'";

        $pdo->exec($alterQuery);
        echo "✓ Successfully updated visibility column to new ENUM values\n";
        echo "  New values: faculty-only, university-only, all-universities, public\n";
    } catch (PDOException $e) {
        echo "✗ Error updating visibility column: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Verify the change
    try {
        $checkQuery = "SHOW COLUMNS FROM events LIKE 'visibility'";
        $stmt = $pdo->query($checkQuery);
        $column = $stmt->fetch();

        echo "\n✓ Verification - Current visibility column definition:\n";
        echo "  Type: " . $column['Type'] . "\n";
        echo "  Default: " . $column['Default'] . "\n";
    } catch (PDOException $e) {
        echo "⚠ Verification failed: " . $e->getMessage() . "\n";
    }

    echo "\n✅ Visibility column update completed successfully!\n";
    echo "New visibility categories:\n";
    echo "  1. Faculty Only (faculty-only)\n";
    echo "  2. University Only (university-only)\n";
    echo "  3. All Universities (all-universities)\n";
    echo "  4. Public (public)\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
