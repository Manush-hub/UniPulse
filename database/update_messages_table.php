<?php
/**
 * Migration script to add updated_at column to messages table
 */

require_once '../app/Core/config.php';

try {
    // Create connection using the same config as the app
    $conn = new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4",
        DBUSER,
        DBPASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "🔄 Adding updated_at column to messages table...\n";
    
    // Check if column already exists
    $checkColumn = $conn->prepare("SHOW COLUMNS FROM messages LIKE 'updated_at'");
    $checkColumn->execute();
    
    if ($checkColumn->rowCount() == 0) {
        // Add updated_at column
        $sql = "ALTER TABLE messages ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL";
        $conn->exec($sql);
        echo "✅ Added updated_at column to messages table\n";
    } else {
        echo "ℹ️ updated_at column already exists in messages table\n";
    }
    
    echo "🎉 Messages table migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error updating messages table: " . $e->getMessage() . "\n";
    exit(1);
}
?>