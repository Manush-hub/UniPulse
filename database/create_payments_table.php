<?php
/**
 * Creates the payments table for storing payment transactions
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to MySQL server
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    // Create payments table
    $query = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_type ENUM('ticket', 'boost') DEFAULT 'ticket',
        transaction_id VARCHAR(100) UNIQUE NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        event_id INT NULL,
        publisher_id INT NULL,
        commission_amount DECIMAL(10, 2) DEFAULT 0.00,
        organizer_amount DECIMAL(10, 2) DEFAULT 0.00,
        description TEXT,
        metadata JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_transaction_id (transaction_id),
        INDEX idx_status (status),
        INDEX idx_payment_type (payment_type),
        INDEX idx_event_id (event_id),
        INDEX idx_publisher_id (publisher_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($query);
    
    echo "✓ Payments table created successfully!\n";
    
    // Verify table was created
    $result = $pdo->query("SHOW TABLES LIKE 'payments'")->fetch();
    if ($result) {
        echo "✓ Table verification: payments table exists\n";
    }
    
    // Show table structure
    echo "\nTable structure:\n";
    $columns = $pdo->query("DESCRIBE payments")->fetchAll();
    if ($columns) {
        foreach ($columns as $column) {
            echo " - {$column['Field']} ({$column['Type']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error creating payments table: " . $e->getMessage() . "\n";
    exit(1);
}
