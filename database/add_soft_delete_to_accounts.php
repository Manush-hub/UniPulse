<?php

/**
 * Migration script to add soft-delete flag columns to account tables.
 * is_deleted: 0 = active, 1 = deleted/deactivated
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);

    $tables = [
        'university_users',
        'public_users',
        'publishers',
        'sponsors'
    ];

    foreach ($tables as $table) {
        $check = $pdo->prepare("SHOW COLUMNS FROM {$table} LIKE 'is_deleted'");
        $check->execute();
        $exists = $check->fetch();

        if ($exists) {
            echo "is_deleted already exists in {$table}\n";
            continue;
        }

        $sql = "ALTER TABLE {$table} ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active";

        // university_users/public_users do not have is_active, append before timestamps.
        if (in_array($table, ['university_users', 'public_users'], true)) {
            $sql = "ALTER TABLE {$table} ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0 AFTER is_verified";
        }

        $pdo->exec($sql);
        echo "Added is_deleted to {$table}\n";
    }

    echo "Soft-delete account migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
