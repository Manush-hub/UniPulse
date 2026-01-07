<?php
/**
 * Create sponsor_posts table for featured sponsor promotional posts on events
 */

$host = 'localhost';
$dbname = 'unipulse_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create sponsor_posts table
    $sql = "CREATE TABLE IF NOT EXISTS sponsor_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        sponsor_id INT NOT NULL,
        sponsor_name VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        image_url VARCHAR(500),
        brand_logo_url VARCHAR(500),
        website_url VARCHAR(500),
        call_to_action_text VARCHAR(255),
        call_to_action_url VARCHAR(500),
        approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        rejection_reason LONGTEXT,
        approved_by INT,
        approved_at TIMESTAMP NULL,
        display_priority INT DEFAULT 0 COMMENT 'Higher priority posts displayed first',
        views_count INT DEFAULT 0,
        clicks_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
        FOREIGN KEY (approved_by) REFERENCES admin(id) ON DELETE SET NULL,
        
        INDEX idx_event_approval (event_id, approval_status),
        INDEX idx_sponsor_status (sponsor_id, approval_status),
        INDEX idx_created_at (created_at),
        INDEX idx_priority (display_priority DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conn->exec($sql);
    
    echo "✓ sponsor_posts table created successfully\n";
    
    // Verify table exists
    $checkTable = $conn->query("DESCRIBE sponsor_posts");
    $columns = $checkTable->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nTable columns:\n";
    foreach ($columns as $column) {
        echo "  - " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
