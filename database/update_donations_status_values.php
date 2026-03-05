<?php

require_once __DIR__ . '/../app/Core/Database.php';

/**
 * Migration: normalize donations.status values
 * From: pending/completed/failed/refunded
 * To:   pending/accepted/rejected
 */

try {
    $db = new Database();
    $conn = $db->getConnection();

    $conn->beginTransaction();

    // 1) Extend enum temporarily so old and new values are allowed during conversion
    $conn->exec("ALTER TABLE donations
        MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'completed', 'failed', 'refunded')
        NOT NULL DEFAULT 'pending'");

    // 2) Convert old values
    $conn->exec("UPDATE donations SET status = 'accepted' WHERE status = 'completed'");
    $conn->exec("UPDATE donations SET status = 'rejected' WHERE status IN ('failed', 'refunded')");

    // 3) Lock enum to final allowed values
    $conn->exec("ALTER TABLE donations
        MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected')
        NOT NULL DEFAULT 'pending'");

    $conn->commit();

    echo "✓ donations.status migrated to pending/accepted/rejected\n";
} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }

    echo "Error updating donations.status: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDonations status migration complete!\n";
