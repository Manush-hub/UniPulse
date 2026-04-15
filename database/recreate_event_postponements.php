<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=8889;dbname=unipulse_db", 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop table if exists
    $pdo->exec("DROP TABLE IF EXISTS `event_postponements`");
    
    // Create new table
    $createTableQuery = "
    CREATE TABLE `event_postponements` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `event_id` int(11) NOT NULL,
        `reason` text DEFAULT NULL,
        
        `previous_event_date` date NOT NULL,
        `previous_event_time` time NOT NULL,
        `previous_event_end_time` time DEFAULT NULL,
        `previous_registration_end_date` date DEFAULT NULL,
        `previous_registration_end_time` time DEFAULT NULL,
        
        `new_event_date` date NOT NULL,
        `new_event_time` time NOT NULL,
        `new_event_end_time` time DEFAULT NULL,
        `new_registration_end_date` date DEFAULT NULL,
        `new_registration_end_time` time DEFAULT NULL,
        
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`id`),
        KEY `event_id` (`event_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createTableQuery);
    
    echo "Successfully recreated event_postponements table with correct columns!\n";

} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
