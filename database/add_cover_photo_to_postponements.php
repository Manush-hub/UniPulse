<?php
require_once __DIR__ . '/../app/Core/config.php';

echo "<h2>Adding Cover Photo Columns to Event Postponements Table</h2>";

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
    
    // Add previous_cover_photo
    try {
        $sql = "ALTER TABLE `event_postponements` ADD COLUMN `previous_cover_photo` VARCHAR(255) DEFAULT NULL AFTER `previous_registration_end_time`";
        $pdo->exec($sql);
        echo "<p style='color: green;'>Successfully added previous_cover_photo column.</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: orange;'>previous_cover_photo column already exists.</p>";
        } else {
            throw $e;
        }
    }
    
    // Add new_cover_photo
    try {
        $sql = "ALTER TABLE `event_postponements` ADD COLUMN `new_cover_photo` VARCHAR(255) DEFAULT NULL AFTER `new_registration_end_time`";
        $pdo->exec($sql);
        echo "<p style='color: green;'>Successfully added new_cover_photo column.</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: orange;'>new_cover_photo column already exists.</p>";
        } else {
            throw $e;
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
