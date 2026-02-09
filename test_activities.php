<?php

/**
 * Test script to check user activities in database
 */

require_once __DIR__ . '/app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);

    echo "=== Checking User Activities ===\n\n";

    // Check total activities
    $result = $pdo->query('SELECT COUNT(*) as count FROM user_activities');
    $row = $result->fetch();
    echo "Total activities in database: " . $row['count'] . "\n\n";

    // Show recent activities
    echo "Recent activities:\n";
    $result2 = $pdo->query('SELECT * FROM user_activities ORDER BY created_at DESC LIMIT 5');
    while ($row = $result2->fetch()) {
        echo "- ID: " . $row['id'] . "\n";
        echo "  User: " . $row['user_id'] . " (" . $row['user_type'] . ")\n";
        echo "  Title: " . $row['title'] . "\n";
        echo "  Type: " . $row['activity_type'] . "\n";
        echo "  Created: " . $row['created_at'] . "\n";
        echo "  Expires: " . $row['expires_at'] . "\n\n";
    }

    // Check event_registrations table
    echo "\n=== Checking Event Registrations ===\n\n";
    $result3 = $pdo->query('SELECT COUNT(*) as count FROM event_registrations');
    $row = $result3->fetch();
    echo "Total registrations: " . $row['count'] . "\n\n";

    $result4 = $pdo->query('SELECT * FROM event_registrations ORDER BY registered_at DESC LIMIT 3');
    while ($row = $result4->fetch()) {
        echo "- Registration ID: " . $row['id'] . "\n";
        echo "  User: " . $row['user_id'] . " (" . $row['user_type'] . ")\n";
        echo "  Event: " . $row['event_id'] . "\n";
        echo "  Status: " . $row['status'] . "\n";
        echo "  Registered: " . $row['registered_at'] . "\n\n";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
