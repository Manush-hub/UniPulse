<?php

require_once __DIR__ . '/../app/Core/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME,
        DBUSER,
        DBPASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]
    );
    
    echo "Adding soft delete and moderation columns to events table...\n\n";
    
    // Add soft delete columns
    $columns = [
        "ALTER TABLE events ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE",
        "ALTER TABLE events ADD COLUMN deleted_at TIMESTAMP NULL",
        "ALTER TABLE events ADD COLUMN deleted_by INT NULL",
        "ALTER TABLE events ADD COLUMN deletion_reason TEXT NULL",
        "ALTER TABLE events ADD COLUMN moderated_by INT NULL",
        "ALTER TABLE events ADD COLUMN moderated_at TIMESTAMP NULL",
        "ALTER TABLE events ADD COLUMN moderation_reason TEXT NULL"
    ];
    
    foreach ($columns as $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Column added: " . substr($sql, strpos($sql, 'ADD COLUMN') + 11) . "\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "↳ Column already exists: " . substr($sql, strpos($sql, 'ADD COLUMN') + 11) . "\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Add indexes
    echo "\nAdding indexes...\n";
    $indexes = [
        "ALTER TABLE events ADD INDEX idx_is_deleted (is_deleted)",
        "ALTER TABLE events ADD INDEX idx_deleted_by (deleted_by)",
        "ALTER TABLE events ADD INDEX idx_moderated_by (moderated_by)"
    ];
    
    foreach ($indexes as $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Index added\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "↳ Index already exists\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Database migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
