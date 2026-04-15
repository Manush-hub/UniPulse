<?php
require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("ALTER TABLE paid_event_registrations DROP COLUMN unit_price;");
    echo "Successfully removed unit_price column from paid_event_registrations.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "check that column/key exists") !== false) {
        echo "Column unit_price already removed.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
