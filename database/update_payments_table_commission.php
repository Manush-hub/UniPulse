<?php
/**
 * Update payments table to add commission tracking fields
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
    
    echo "Connected to database...\n";
    
    // Check if payment_type column exists
    $checkQuery = "SHOW COLUMNS FROM payments LIKE 'payment_type'";
    $result = $pdo->query($checkQuery)->fetch();
    
    if (!$result) {
        echo "Adding payment_type column...\n";
        $pdo->exec("ALTER TABLE payments ADD COLUMN payment_type ENUM('ticket', 'boost') DEFAULT 'ticket' AFTER payment_method");
        echo "✓ payment_type column added\n";
    } else {
        echo "✓ payment_type column already exists\n";
    }
    
    // Check if event_id column exists
    $checkQuery = "SHOW COLUMNS FROM payments LIKE 'event_id'";
    $result = $pdo->query($checkQuery)->fetch();
    
    if (!$result) {
        echo "Adding event_id column...\n";
        $pdo->exec("ALTER TABLE payments ADD COLUMN event_id INT NULL AFTER status");
        echo "✓ event_id column added\n";
    } else {
        echo "✓ event_id column already exists\n";
    }
    
    // Check if publisher_id column exists
    $checkQuery = "SHOW COLUMNS FROM payments LIKE 'publisher_id'";
    $result = $pdo->query($checkQuery)->fetch();
    
    if (!$result) {
        echo "Adding publisher_id column...\n";
        $pdo->exec("ALTER TABLE payments ADD COLUMN publisher_id INT NULL AFTER event_id");
        echo "✓ publisher_id column added\n";
    } else {
        echo "✓ publisher_id column already exists\n";
    }
    
    // Check if commission_amount column exists
    $checkQuery = "SHOW COLUMNS FROM payments LIKE 'commission_amount'";
    $result = $pdo->query($checkQuery)->fetch();
    
    if (!$result) {
        echo "Adding commission_amount column...\n";
        $pdo->exec("ALTER TABLE payments ADD COLUMN commission_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER publisher_id");
        echo "✓ commission_amount column added\n";
    } else {
        echo "✓ commission_amount column already exists\n";
    }
    
    // Check if organizer_amount column exists
    $checkQuery = "SHOW COLUMNS FROM payments LIKE 'organizer_amount'";
    $result = $pdo->query($checkQuery)->fetch();
    
    if (!$result) {
        echo "Adding organizer_amount column...\n";
        $pdo->exec("ALTER TABLE payments ADD COLUMN organizer_amount DECIMAL(10, 2) DEFAULT 0.00 AFTER commission_amount");
        echo "✓ organizer_amount column added\n";
    } else {
        echo "✓ organizer_amount column already exists\n";
    }
    
    // Add indexes if they don't exist
    echo "Adding indexes...\n";
    
    try {
        $pdo->exec("ALTER TABLE payments ADD INDEX idx_payment_type (payment_type)");
        echo "✓ idx_payment_type index added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✓ idx_payment_type index already exists\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE payments ADD INDEX idx_event_id (event_id)");
        echo "✓ idx_event_id index added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✓ idx_event_id index already exists\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE payments ADD INDEX idx_publisher_id (publisher_id)");
        echo "✓ idx_publisher_id index added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✓ idx_publisher_id index already exists\n";
        } else {
            throw $e;
        }
    }
    
    echo "\n✅ Payments table successfully updated with commission tracking fields!\n";
    
    // Show updated table structure
    echo "\nUpdated table structure:\n";
    $columns = $pdo->query("DESCRIBE payments")->fetchAll();
    foreach ($columns as $column) {
        echo " - {$column['Field']} ({$column['Type']})\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error updating payments table: " . $e->getMessage() . "\n";
    exit(1);
}
