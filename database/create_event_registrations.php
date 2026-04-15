<?php
/**
 * Database Migration: Create event_registrations table
 * 
 * This table tracks which users have registered for which events
 * to prevent duplicate registrations and manage participant lists.
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
    
    echo "Creating event_registrations table...\n\n";
    
    // Create event_registrations table
    $sql = "CREATE TABLE IF NOT EXISTS event_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        user_id INT NOT NULL,
        user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
        registration_type ENUM('free', 'paid') DEFAULT 'free',
        status ENUM('registered', 'cancelled', 'attended') DEFAULT 'registered',
        notes TEXT NULL,
        payment_id VARCHAR(255) NULL,
        amount_paid DECIMAL(10, 2) NULL DEFAULT 0.00,
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        cancelled_at TIMESTAMP NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_event_id (event_id),
        INDEX idx_user_id (user_id),
        INDEX idx_user_type (user_type),
        INDEX idx_status (status),
        INDEX idx_registration_type (registration_type),
        
        UNIQUE KEY unique_registration (event_id, user_id, user_type),
        
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✓ Created 'event_registrations' table successfully.\n\n";
    
    echo "Table Structure:\n";
    echo "- id: Primary key\n";
    echo "- event_id: Reference to events table\n";
    echo "- user_id: ID of the user who registered\n";
    echo "- user_type: Type of user (university/public/publisher/sponsor)\n";
    echo "- registration_type: free or paid\n";
    echo "- status: registered, cancelled, or attended\n";
    echo "- notes: Optional participant notes\n";
    echo "- payment_id: For paid registrations\n";
    echo "- amount_paid: Amount paid for tickets\n";
    echo "- registered_at: When registration occurred\n";
    echo "- cancelled_at: When registration was cancelled (if applicable)\n\n";
    
    echo "Unique Constraint:\n";
    echo "- One user can only register once per event (enforced at database level)\n\n";
    
    echo "✅ Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
