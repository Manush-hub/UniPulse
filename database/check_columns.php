<?php
/**
 * Check what columns exist in publisher_profiles table
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $pdo = new PDO('mysql:host='.DBHOST.';port='.DBPORT.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking publisher_profiles table structure...\n\n";
    
    $stmt = $pdo->query("DESCRIBE publisher_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Column Name          | Type                 | Null | Key | Default\n";
    echo "-------------------  | -------------------- | ---- | --- | -------\n";
    
    foreach($columns as $col) {
        printf("%-20s | %-20s | %-4s | %-3s | %s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'], 
            $col['Key'], 
            $col['Default'] ?? 'NULL'
        );
    }
    
    echo "\n\nChecking for specific social media columns:\n";
    $socialColumns = ['website', 'facebook', 'instagram', 'linkedin', 'twitter', 'discord', 'youtube', 'telegram', 'github'];
    foreach($socialColumns as $social) {
        $exists = false;
        foreach($columns as $col) {
            if($col['Field'] === $social) {
                $exists = true;
                break;
            }
        }
        echo ($exists ? "✓" : "✗") . " $social\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
