<?php
// database/create_event_postponements_table.php

echo "<h2>Creating Event Postponements Table</h2>";

require_once __DIR__ . '/../app/Core/config.php';

$host = DBHOST;
$db = DBNAME;
$user = DBUSER;
$pass = DBPASS;
$port = DBPORT ?? '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $sql = "CREATE TABLE IF NOT EXISTS `event_postponements` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `event_id` INT(11) NOT NULL,
        `reason` TEXT DEFAULT NULL,
        `previous_event_date` DATE DEFAULT NULL,
        `previous_event_time` TIME DEFAULT NULL,
        `previous_event_end_time` TIME DEFAULT NULL,
        `previous_registration_end_date` DATE DEFAULT NULL,
        `previous_registration_end_time` TIME DEFAULT NULL,
        
        `new_event_date` DATE DEFAULT NULL,
        `new_event_time` TIME DEFAULT NULL,
        `new_event_end_time` TIME DEFAULT NULL,
        `new_registration_end_date` DATE DEFAULT NULL,
        `new_registration_end_time` TIME DEFAULT NULL,
        
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `event_id_idx` (`event_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>Successfully created event_postponements table.</p>";
    
    echo "<p><a href='/unipulse/public'>Return to Home</a></p>";
    
} catch (\PDOException $e) {
    echo "<h3 style='color: red;'>Error creating table:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>