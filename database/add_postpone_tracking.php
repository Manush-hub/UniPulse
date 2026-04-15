<?php
// database/add_postpone_tracking.php

echo "<h2>Adding Postpone Tracking Column to Events</h2>";

require_once __DIR__ . '/../app/Core/config.php';

$host = DBHOST;
$db = DBNAME;
$user = DBUSER;
$pass = DBPASS;
$port = DBPORT;
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM `events` LIKE 'postponed_count'");
    $exists = $stmt->rowCount() > 0;
    
    if (!$exists) {
        // Add postponed_count column
        $pdo->exec("ALTER TABLE `events` ADD COLUMN `postponed_count` INT NOT NULL DEFAULT 0 AFTER `status`");
        echo "<p style='color: green;'>Successfully added postponed_count column to events table.</p>";
    } else {
        echo "<p style='color: blue;'>postponed_count column already exists in events table.</p>";
    }
    
    echo "<p><a href='/unipulse/public'>Return to Home</a></p>";
    
} catch (\PDOException $e) {
    echo "<h3 style='color: red;'>Error updating events table:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>