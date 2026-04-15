<?php
/**
 * Database Migration: Add suspension fields to user tables
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
    
    echo "<h1>Adding Suspension Fields to User Tables</h1>";
    
    // Tables to update
    $tables = ['university_users', 'public_users', 'publishers', 'sponsors'];
    
    foreach ($tables as $table) {
        echo "<h2>Updating {$table}</h2>";
        
        // Check if columns already exist
        $checkColumn = $pdo->query("SHOW COLUMNS FROM {$table} LIKE 'is_suspended'");
        if ($checkColumn->rowCount() == 0) {
            // Add is_suspended column
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN is_suspended BOOLEAN DEFAULT FALSE AFTER email");
            echo "✓ Added is_suspended column<br>";
        } else {
            echo "- is_suspended column already exists<br>";
        }
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM {$table} LIKE 'suspension_reason'");
        if ($checkColumn->rowCount() == 0) {
            // Add suspension_reason column
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN suspension_reason TEXT NULL AFTER is_suspended");
            echo "✓ Added suspension_reason column<br>";
        } else {
            echo "- suspension_reason column already exists<br>";
        }
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM {$table} LIKE 'suspended_at'");
        if ($checkColumn->rowCount() == 0) {
            // Add suspended_at column
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN suspended_at TIMESTAMP NULL AFTER suspension_reason");
            echo "✓ Added suspended_at column<br>";
        } else {
            echo "- suspended_at column already exists<br>";
        }
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM {$table} LIKE 'suspended_by'");
        if ($checkColumn->rowCount() == 0) {
            // Add suspended_by column (admin ID)
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN suspended_by INT NULL AFTER suspended_at");
            echo "✓ Added suspended_by column<br>";
        } else {
            echo "- suspended_by column already exists<br>";
        }
        
        echo "<br>";
    }
    
    // Create suspension appeals table
    echo "<h2>Creating suspension_appeals table</h2>";
    $createAppealsTable = "
        CREATE TABLE IF NOT EXISTS suspension_appeals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
            appeal_message TEXT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            admin_response TEXT NULL,
            reviewed_by INT NULL,
            reviewed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user (user_id, user_type),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createAppealsTable);
    echo "✓ suspension_appeals table created/verified<br>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✓ Migration completed successfully!</h2>";
    echo "<p><a href='/unipulse/public/admin'>Back to Admin Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
