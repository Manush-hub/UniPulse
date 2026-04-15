<?php
// Add sponsorship_proposal column to events table

require_once __DIR__ . '/../app/Core/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME,
        DBUSER,
        DBPASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Adding sponsorship_proposal column to events table...\n\n";
    
    // Check if column already exists
    $checkColumn = "SHOW COLUMNS FROM events LIKE 'sponsorship_proposal'";
    $result = $pdo->query($checkColumn);
    
    if ($result->rowCount() == 0) {
        // Add sponsorship_proposal column
        $sql = "ALTER TABLE events ADD COLUMN sponsorship_proposal VARCHAR(500) NULL AFTER accepts_sponsorships";
        $pdo->exec($sql);
        echo "✓ Added 'sponsorship_proposal' column to events table successfully.\n";
    } else {
        echo "ℹ Column 'sponsorship_proposal' already exists in events table.\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
