<?php
/**
 * Migration script to create publisher_profiles_gallery table
 * Single table for publisher photo galleries with JSON storage for images
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
    
    echo "Dropping old tables if they exist...\n";
    
    // Drop old tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS photo_gallery_images");
    $pdo->exec("DROP TABLE IF EXISTS photo_galleries");
    echo "Old tables dropped.\n";
    
    echo "Creating publisher_profiles_gallery table...\n";
    
    // Create publisher_profiles_gallery table
    $publisherProfilesGalleryTable = "
        CREATE TABLE IF NOT EXISTS publisher_profiles_gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            publisher_id INT NOT NULL,
            title VARCHAR(50) NOT NULL,
            description VARCHAR(150) NULL,
            images JSON NOT NULL COMMENT 'Array of image URLs (max 10)',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_publisher_id (publisher_id),
            FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($publisherProfilesGalleryTable);
    echo "Table 'publisher_profiles_gallery' created successfully.\n";
    
    echo "Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
