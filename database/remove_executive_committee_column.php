<?php
/**
 * Migration script to remove executive_committee column from publisher_profiles table
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Removing executive_committee column from publisher_profiles table...\n";
    
    // Check if column exists before dropping
    $checkColumn = "SHOW COLUMNS FROM publisher_profiles LIKE 'executive_committee'";
    $result = $pdo->query($checkColumn)->fetchAll();
    
    if (count($result) > 0) {
        // Drop the column
        $dropColumn = "ALTER TABLE publisher_profiles DROP COLUMN executive_committee";
        $pdo->exec($dropColumn);
        echo "✓ Column 'executive_committee' removed successfully.\n";
    } else {
        echo "ℹ Column 'executive_committee' does not exist. Nothing to remove.\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
