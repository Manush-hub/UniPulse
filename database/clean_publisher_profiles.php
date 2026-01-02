<?php
/**
 * Script to clean publisher_profiles table - set contact and social media fields to NULL
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Cleaning publisher_profiles table...\n\n";
    
    // Set all contact and social media fields to NULL
    $query = "UPDATE publisher_profiles SET 
        address = NULL,
        website = NULL,
        facebook = NULL,
        instagram = NULL,
        linkedin = NULL,
        twitter = NULL,
        discord = NULL,
        youtube = NULL
        WHERE 1=1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $rowsAffected = $stmt->rowCount();
    
    echo "✓ Cleaned $rowsAffected publisher profile(s)\n";
    echo "✓ Set contact and social media fields to NULL\n\n";
    
    // Show current state
    echo "Current state of publisher_profiles:\n";
    echo str_repeat("-", 80) . "\n";
    
    $result = $pdo->query("SELECT publisher_id, address, website, facebook, instagram, linkedin, twitter, discord, youtube FROM publisher_profiles");
    $profiles = $result->fetchAll();
    
    if (empty($profiles)) {
        echo "No publisher profiles found.\n";
    } else {
        foreach ($profiles as $profile) {
            echo "Publisher ID: " . $profile['publisher_id'] . "\n";
            echo "  Address: " . ($profile['address'] ?? 'NULL') . "\n";
            echo "  Website: " . ($profile['website'] ?? 'NULL') . "\n";
            echo "  Facebook: " . ($profile['facebook'] ?? 'NULL') . "\n";
            echo "  Instagram: " . ($profile['instagram'] ?? 'NULL') . "\n";
            echo "  LinkedIn: " . ($profile['linkedin'] ?? 'NULL') . "\n";
            echo "  Twitter: " . ($profile['twitter'] ?? 'NULL') . "\n";
            echo "  Discord: " . ($profile['discord'] ?? 'NULL') . "\n";
            echo "  YouTube: " . ($profile['youtube'] ?? 'NULL') . "\n";
            echo str_repeat("-", 80) . "\n";
        }
    }
    
    echo "\n✅ Database cleaned successfully!\n";
    echo "All contact and social media fields are now NULL.\n";
    echo "Publishers can fill them through the profile settings page.\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
