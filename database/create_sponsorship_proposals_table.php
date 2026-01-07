<?php
/**
 * Create sponsorship_proposals table for sponsors to propose custom terms
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
    
    // Create sponsorship_proposals table
    $sql = "CREATE TABLE IF NOT EXISTS sponsorship_proposals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        sponsor_id INT NOT NULL,
        sponsor_name VARCHAR(255) NOT NULL,
        
        -- Sponsorship Terms
        proposal_type ENUM('monetary', 'in-kind', 'service', 'mixed') DEFAULT 'mixed',
        title VARCHAR(255) NOT NULL COMMENT 'e.g., Gold Sponsor Package',
        description LONGTEXT NOT NULL,
        
        -- Monetary Sponsorship
        monetary_amount DECIMAL(10, 2),
        currency VARCHAR(10) DEFAULT 'USD',
        payment_schedule VARCHAR(500) COMMENT 'e.g., 50% upfront, 50% at event',
        
        -- In-Kind Sponsorship
        in_kind_items LONGTEXT COMMENT 'JSON: list of items/services provided',
        estimated_value DECIMAL(10, 2),
        
        -- Services
        service_description LONGTEXT,
        service_duration VARCHAR(255),
        
        -- Deliverables & Benefits
        deliverables LONGTEXT COMMENT 'JSON: what sponsor will provide',
        expected_benefits LONGTEXT COMMENT 'JSON: what sponsor expects (booth size, logo placement, etc)',
        
        -- Contact & Details
        contact_person VARCHAR(255),
        contact_phone VARCHAR(20),
        contact_email VARCHAR(255),
        
        -- Status & Workflow
        status ENUM('draft', 'submitted', 'under_review', 'accepted', 'rejected', 'negotiating') DEFAULT 'draft',
        rejection_reason LONGTEXT,
        
        -- Admin Review
        reviewed_by INT,
        reviewed_at TIMESTAMP NULL,
        
        -- Sponsorship Agreement / Contract
        contract_url VARCHAR(500) COMMENT 'Path to signed contract',
        agreement_status ENUM('pending', 'signed', 'declined') DEFAULT 'pending',
        
        -- Analytics
        views_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewed_by) REFERENCES admin(id) ON DELETE SET NULL,
        
        INDEX idx_event_status (event_id, status),
        INDEX idx_sponsor_status (sponsor_id, status),
        INDEX idx_created_at (created_at),
        INDEX idx_agreement_status (agreement_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conn->exec($sql);
    
    echo "✓ sponsorship_proposals table created successfully\n";
    
    // Verify table exists
    $checkTable = $conn->query("DESCRIBE sponsorship_proposals");
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
