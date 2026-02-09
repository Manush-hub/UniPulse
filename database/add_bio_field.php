<?php

/**
 * Migration: Add bio field to user tables
 * This script adds a bio field for user profiles
 */
require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    $pdo->exec("USE " . DBNAME);

    // Check and add bio to university_users
    $checkColumnsUni = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'university_users' AND COLUMN_NAME = 'bio' AND TABLE_SCHEMA = '" . DBNAME . "'";
    $result = $pdo->query($checkColumnsUni);
    $columnExists = $result->fetch(PDO::FETCH_ASSOC);

    if (!$columnExists) {
        $pdo->exec("ALTER TABLE university_users ADD COLUMN bio LONGTEXT NULL DEFAULT NULL COMMENT 'User bio/about information'");
        echo "✓ Added bio to university_users\n";
    } else {
        echo "⊘ bio already exists in university_users\n";
    }

    // Check and add bio to public_users
    $checkColumnsPub = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'public_users' AND COLUMN_NAME = 'bio' AND TABLE_SCHEMA = '" . DBNAME . "'";
    $result = $pdo->query($checkColumnsPub);
    $columnExists = $result->fetch(PDO::FETCH_ASSOC);

    if (!$columnExists) {
        $pdo->exec("ALTER TABLE public_users ADD COLUMN bio LONGTEXT NULL DEFAULT NULL COMMENT 'User bio/about information'");
        echo "✓ Added bio to public_users\n";
    } else {
        echo "⊘ bio already exists in public_users\n";
    }

    echo "\n✅ Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
