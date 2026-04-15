<?php
/**
 * Migration script to create event_sponsorships and event_sponsorship_packages tables
 * Run this file directly to create the tables
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $conn = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Creating event sponsorship tables...\n\n";
    
    // Create event_sponsorship_packages table
    $sql1 = "CREATE TABLE IF NOT EXISTS event_sponsorship_packages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        package_name VARCHAR(100) NOT NULL,
        package_type ENUM('bronze', 'silver', 'gold', 'platinum', 'custom') NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        description TEXT,
        benefits TEXT,
        terms_conditions TEXT,
        available_slots INT DEFAULT 1,
        filled_slots INT DEFAULT 0,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        INDEX idx_event_id (event_id),
        INDEX idx_package_type (package_type),
        INDEX idx_is_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql1);
    echo "✓ Created event_sponsorship_packages table\n";
    
    // Create event_sponsorships table (tracking actual sponsorships)
    $sql2 = "CREATE TABLE IF NOT EXISTS event_sponsorships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        package_id INT NOT NULL,
        sponsor_id INT NOT NULL,
        sponsor_type ENUM('sponsor', 'publisher') DEFAULT 'sponsor',
        amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
        payment_proof VARCHAR(255),
        payment_reference VARCHAR(100),
        payment_date DATETIME,
        transaction_id VARCHAR(100),
        notes TEXT,
        approved_by INT,
        approved_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (package_id) REFERENCES event_sponsorship_packages(id) ON DELETE CASCADE,
        INDEX idx_event_id (event_id),
        INDEX idx_sponsor_id (sponsor_id),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql2);
    echo "✓ Created event_sponsorships table\n";
    
    // Add sponsorship fields to events table
    $alterEvents = [
        "ALTER TABLE events ADD COLUMN accepts_sponsorships TINYINT(1) DEFAULT 0",
        "ALTER TABLE events ADD COLUMN sponsorship_bank_name VARCHAR(100)",
        "ALTER TABLE events ADD COLUMN sponsorship_account_name VARCHAR(200)",
        "ALTER TABLE events ADD COLUMN sponsorship_account_number VARCHAR(50)",
        "ALTER TABLE events ADD COLUMN sponsorship_branch VARCHAR(100)",
        "ALTER TABLE events ADD COLUMN sponsorship_swift_code VARCHAR(20)",
        "ALTER TABLE events ADD COLUMN sponsorship_instructions TEXT"
    ];
    
    foreach ($alterEvents as $sql) {
        try {
            $conn->exec($sql);
            echo "✓ Added column to events table\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                throw $e;
            }
            echo "- Column already exists in events table\n";
        }
    }
    
    echo "\n✅ Event sponsorship tables created successfully!\n\n";
    
    echo "Tables created:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "1. event_sponsorship_packages\n";
    echo "   - Stores sponsorship packages for events\n";
    echo "   - Fields: package_name, package_type, amount, description, benefits\n";
    echo "   - Supports: bronze, silver, gold, platinum, custom packages\n\n";
    
    echo "2. event_sponsorships\n";
    echo "   - Tracks actual sponsorship commitments\n";
    echo "   - Fields: sponsor_id, package_id, amount, status, payment details\n";
    echo "   - Statuses: pending, approved, rejected, completed\n\n";
    
    echo "3. events table (new columns)\n";
    echo "   - accepts_sponsorships: Enable/disable sponsorships\n";
    echo "   - sponsorship_bank_name: Bank name for payments\n";
    echo "   - sponsorship_account_name: Account holder name\n";
    echo "   - sponsorship_account_number: Bank account number\n";
    echo "   - sponsorship_branch: Bank branch\n";
    echo "   - sponsorship_swift_code: International transfers\n";
    echo "   - sponsorship_instructions: Additional payment instructions\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "Usage:\n";
    echo "- Publishers can enable sponsorships when creating events\n";
    echo "- Add multiple packages (bronze/silver/gold/platinum/custom)\n";
    echo "- Set amount, benefits, and terms for each package\n";
    echo "- Provide bank details for sponsor payments\n";
    echo "- Sponsors can view and select packages\n";
    echo "- Track sponsorship status and payments\n\n";
    
} catch (PDOException $e) {
    echo "✗ Error creating event sponsorship tables: " . $e->getMessage() . "\n";
    exit(1);
}
