<?php
/**
 * Migration script to create sponsor_profiles table for extended sponsor profile data
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
    
    echo "Creating sponsor_profiles table...\n";
    
    // Create sponsor_profiles table
    $sponsorProfilesTable = "
        CREATE TABLE IF NOT EXISTS sponsor_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sponsor_id INT NOT NULL UNIQUE,
            sponsor_type VARCHAR(50) NULL COMMENT 'company, individual, organization, foundation',
            industry VARCHAR(100) NULL,
            company_size VARCHAR(50) NULL COMMENT '1-10, 11-50, 51-200, 201-500, 501-1000, 1000+',
            address TEXT NULL,
            headline VARCHAR(255) NULL,
            about TEXT NULL,
            mission TEXT NULL,
            interests JSON NULL COMMENT 'Array of sponsorship focus areas',
            website VARCHAR(255) NULL,
            facebook VARCHAR(255) NULL,
            instagram VARCHAR(255) NULL,
            linkedin VARCHAR(255) NULL,
            twitter VARCHAR(255) NULL,
            youtube VARCHAR(255) NULL,
            logo_url VARCHAR(500) NULL,
            cover_photo_url VARCHAR(500) NULL,
            is_verified TINYINT(1) DEFAULT 0,
            verified_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_sponsor_id (sponsor_id),
            FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sponsorProfilesTable);
    echo "Table 'sponsor_profiles' created successfully.\n";
    
    echo "Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
