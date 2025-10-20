<?php
require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to database
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Connected to database successfully.\n";
    
    // Fix ENUM columns
    $fixQueries = [
        // Update visibility ENUM to match what we want
        "UPDATE events SET visibility = 'public' WHERE visibility = 'university'",
        "ALTER TABLE events MODIFY COLUMN visibility ENUM('public', 'university-only', 'private') DEFAULT 'public'",
        
        // Update created_by_type ENUM to match possible values
        "ALTER TABLE events MODIFY COLUMN created_by_type ENUM('admin', 'moderator', 'publisher', 'sponsor', 'university', 'public') DEFAULT NULL"
    ];
    
    foreach ($fixQueries as $query) {
        try {
            $pdo->exec($query);
            echo "✓ Executed: " . substr($query, 0, 50) . "...\n";
        } catch (PDOException $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "Query: " . $query . "\n";
        }
    }
    
    echo "\nDatabase fixes completed!\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>