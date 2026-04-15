<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=8889;dbname=unipulse_db", 'root', 'root');
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($columns as $col) {
        if(strpos($col['Field'], 'reg') !== false || strpos($col['Field'], 'ticket') !== false) {
            echo $col['Field']." - ".$col['Type']."\n";
        }
    }
} catch(PDOException $e) {}
