<?php
/**
 * Migration script to add university and university_name columns to moderators table
 * Run this if you have an existing moderators table without university columns
 */

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
    
    // Check if university column exists
    $checkColumn = "SHOW COLUMNS FROM moderators LIKE 'university'";
    $result = $pdo->query($checkColumn)->fetch();
    
    if (!$result) {
        echo "Adding university column to moderators table...\n";
        
        // Add university column
        $addUniversityColumn = "ALTER TABLE moderators ADD COLUMN university VARCHAR(100) NOT NULL DEFAULT 'university-of-moratuwa' AFTER phone";
        $pdo->exec($addUniversityColumn);
        
        // Add university_name column
        $addUniversityNameColumn = "ALTER TABLE moderators ADD COLUMN university_name VARCHAR(255) NOT NULL DEFAULT 'University of Moratuwa' AFTER university";
        $pdo->exec($addUniversityNameColumn);
        
        // Add index for university
        $addIndex = "ALTER TABLE moderators ADD INDEX idx_university (university)";
        $pdo->exec($addIndex);
        
        echo "University columns added successfully!\n";
        echo "Note: All existing moderators have been assigned to 'University of Moratuwa' by default.\n";
        echo "You may want to update their university assignments manually.\n";
        
    } else {
        echo "University column already exists in moderators table.\n";
    }
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>