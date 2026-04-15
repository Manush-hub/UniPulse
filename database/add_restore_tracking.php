<?php
/**
 * Migration: Add restored_by and restored_at columns to track who unhid events
 */
require_once __DIR__ . '/../app/Core/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4",
        DBUSER, DBPASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $columns = $pdo->query("SHOW COLUMNS FROM events")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('restored_by', $columns)) {
        $pdo->exec("ALTER TABLE events ADD COLUMN restored_by INT NULL AFTER deletion_reason");
        echo "✓ Added restored_by column\n";
    } else {
        echo "↳ restored_by already exists\n";
    }

    if (!in_array('restored_at', $columns)) {
        $pdo->exec("ALTER TABLE events ADD COLUMN restored_at TIMESTAMP NULL AFTER restored_by");
        echo "✓ Added restored_at column\n";
    } else {
        echo "↳ restored_at already exists\n";
    }

    echo "\n✅ Migration completed!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
