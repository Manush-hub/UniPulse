<?php
/**
 * Add preferences/tags column to publisher_profiles table
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
    
    echo "Adding preferences column to publisher_profiles table...\n";
    
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM publisher_profiles LIKE 'preferences'");
    if ($stmt->rowCount() > 0) {
        echo "Column 'preferences' already exists.\n";
    } else {
        // Add preferences column (will store as JSON)
        $alterQuery = "ALTER TABLE publisher_profiles 
                       ADD COLUMN preferences TEXT NULL COMMENT 'Organization focus areas/tags stored as JSON'";
        $pdo->exec($alterQuery);
        echo "✓ Column 'preferences' added successfully.\n";
    }
    
    echo "\nMigration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
