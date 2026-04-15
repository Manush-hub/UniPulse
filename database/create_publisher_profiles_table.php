<?php
/**
 * Migration script to create publisher_profiles table for extended profile data
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
    
    echo "Creating publisher_profiles table...\n";
    
    // Create publisher_profiles table
    $publisherProfilesTable = "
        CREATE TABLE IF NOT EXISTS publisher_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            publisher_id INT NOT NULL UNIQUE,
            org_type VARCHAR(50) NULL COMMENT 'student-org, academic-club, sports-club, cultural-club, professional-org',
            address TEXT NULL,
            established_year INT NULL,
            member_count INT NULL,
            headline VARCHAR(255) NULL,
            bio TEXT NULL,
            mission TEXT NULL,
            website VARCHAR(255) NULL,
            facebook VARCHAR(255) NULL,
            instagram VARCHAR(255) NULL,
            linkedin VARCHAR(255) NULL,
            twitter VARCHAR(255) NULL,
            discord VARCHAR(255) NULL,
            youtube VARCHAR(255) NULL,
            logo_url VARCHAR(500) NULL,
            cover_photo_url VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_publisher_id (publisher_id),
            FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($publisherProfilesTable);
    echo "Table 'publisher_profiles' created successfully.\n";
    
    echo "Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
