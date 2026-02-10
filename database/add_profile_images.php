<?php

/**
 * Migration: Add profile_photo and cover_photo fields to user tables
 * This script adds image fields to store profile and cover photos
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

    // Add profile_photo and cover_photo to university_users
    $checkColumnsUni = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'university_users' AND COLUMN_NAME IN ('profile_photo', 'cover_photo') AND TABLE_SCHEMA = '" . DBNAME . "'";
    $result = $pdo->query($checkColumnsUni);
    $existingColumns = $result->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('profile_photo', $existingColumns)) {
        $pdo->exec("ALTER TABLE university_users ADD COLUMN profile_photo LONGTEXT NULL DEFAULT NULL COMMENT 'Base64 or file path for profile photo'");
        echo "✓ Added profile_photo to university_users\n";
    } else {
        echo "⊘ profile_photo already exists in university_users\n";
    }

    if (!in_array('cover_photo', $existingColumns)) {
        $pdo->exec("ALTER TABLE university_users ADD COLUMN cover_photo LONGTEXT NULL DEFAULT NULL COMMENT 'Base64 or file path for cover photo'");
        echo "✓ Added cover_photo to university_users\n";
    } else {
        echo "⊘ cover_photo already exists in university_users\n";
    }

    // Add profile_photo and cover_photo to public_users
    $checkColumnsPub = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'public_users' AND COLUMN_NAME IN ('profile_photo', 'cover_photo') AND TABLE_SCHEMA = '" . DBNAME . "'";
    $result = $pdo->query($checkColumnsPub);
    $existingColumnsPub = $result->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('profile_photo', $existingColumnsPub)) {
        $pdo->exec("ALTER TABLE public_users ADD COLUMN profile_photo LONGTEXT NULL DEFAULT NULL COMMENT 'Base64 or file path for profile photo'");
        echo "✓ Added profile_photo to public_users\n";
    } else {
        echo "⊘ profile_photo already exists in public_users\n";
    }

    if (!in_array('cover_photo', $existingColumnsPub)) {
        $pdo->exec("ALTER TABLE public_users ADD COLUMN cover_photo LONGTEXT NULL DEFAULT NULL COMMENT 'Base64 or file path for cover photo'");
        echo "✓ Added cover_photo to public_users\n";
    } else {
        echo "⊘ cover_photo already exists in public_users\n";
    }

    echo "\n✅ Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
