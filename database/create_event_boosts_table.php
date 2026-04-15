<?php
require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Creating event_boosts table...\n";
    
    // Create event_boosts table to track boosted events
    $createTable = "
        CREATE TABLE IF NOT EXISTS event_boosts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            publisher_id INT NOT NULL,
            boost_start_date DATETIME NOT NULL,
            boost_end_date DATETIME NOT NULL,
            duration_days INT NOT NULL,
            amount_paid DECIMAL(10, 2) NOT NULL,
            payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            payment_method VARCHAR(50) NULL,
            transaction_id VARCHAR(100) NULL,
            boost_status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
            priority_level INT DEFAULT 1,
            impressions INT DEFAULT 0,
            clicks INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE,
            INDEX idx_event_id (event_id),
            INDEX idx_publisher_id (publisher_id),
            INDEX idx_boost_dates (boost_start_date, boost_end_date),
            INDEX idx_boost_status (boost_status),
            INDEX idx_payment_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createTable);
    echo "Table 'event_boosts' created successfully.\n";
    
    // Create boost_pricing table for pricing configuration
    $createPricingTable = "
        CREATE TABLE IF NOT EXISTS boost_pricing (
            id INT AUTO_INCREMENT PRIMARY KEY,
            duration_days INT NOT NULL UNIQUE,
            price_per_day DECIMAL(10, 2) NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            discount_percentage DECIMAL(5, 2) DEFAULT 0,
            priority_multiplier DECIMAL(5, 2) DEFAULT 1.0,
            is_active BOOLEAN DEFAULT TRUE,
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_duration (duration_days),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createPricingTable);
    echo "Table 'boost_pricing' created successfully.\n";
    
    // Insert default pricing tiers
    $defaultPricing = [
        ['duration_days' => 1, 'price_per_day' => 500.00, 'total_price' => 500.00, 'discount_percentage' => 0, 'description' => '1 Day Boost'],
        ['duration_days' => 3, 'price_per_day' => 450.00, 'total_price' => 1350.00, 'discount_percentage' => 10, 'description' => '3 Days Boost - 10% discount'],
        ['duration_days' => 7, 'price_per_day' => 400.00, 'total_price' => 2800.00, 'discount_percentage' => 20, 'description' => '1 Week Boost - 20% discount'],
        ['duration_days' => 14, 'price_per_day' => 350.00, 'total_price' => 4900.00, 'discount_percentage' => 30, 'description' => '2 Weeks Boost - 30% discount'],
        ['duration_days' => 30, 'price_per_day' => 300.00, 'total_price' => 9000.00, 'discount_percentage' => 40, 'description' => '1 Month Boost - 40% discount']
    ];
    
    $insertPricing = "INSERT INTO boost_pricing (duration_days, price_per_day, total_price, discount_percentage, description) 
                      VALUES (:duration_days, :price_per_day, :total_price, :discount_percentage, :description)
                      ON DUPLICATE KEY UPDATE 
                      price_per_day = VALUES(price_per_day),
                      total_price = VALUES(total_price),
                      discount_percentage = VALUES(discount_percentage),
                      description = VALUES(description)";
    
    $stmt = $pdo->prepare($insertPricing);
    
    foreach ($defaultPricing as $pricing) {
        $stmt->execute($pricing);
    }
    
    echo "Default pricing tiers inserted successfully.\n";
    
    // Add boost columns to events table if they don't exist
    echo "Updating events table...\n";
    
    // Check if columns exist before adding them
    $checkColumns = "SHOW COLUMNS FROM events LIKE 'is_boosted'";
    $result = $pdo->query($checkColumns);
    
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE events ADD COLUMN is_boosted BOOLEAN DEFAULT FALSE");
        echo "Added is_boosted column to events table.\n";
    }
    
    $checkColumns = "SHOW COLUMNS FROM events LIKE 'boost_expires_at'";
    $result = $pdo->query($checkColumns);
    
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE events ADD COLUMN boost_expires_at DATETIME NULL");
        echo "Added boost_expires_at column to events table.\n";
    }
    
    $checkColumns = "SHOW COLUMNS FROM events LIKE 'boost_priority'";
    $result = $pdo->query($checkColumns);
    
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE events ADD COLUMN boost_priority INT DEFAULT 0");
        echo "Added boost_priority column to events table.\n";
    }
    
    echo "\n✅ Event boosting system setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
