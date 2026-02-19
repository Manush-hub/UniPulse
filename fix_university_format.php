<?php
/**
 * Fix script to normalize university field format in events table
 */
require_once __DIR__ . '/app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Starting university field normalization...\n\n";
    
    // Mapping from various formats to the standard slug format
    $universityMap = [
        'University of Colombo' => 'university-of-colombo',
        'University Of Colombo' => 'university-of-colombo',
        'University of Peradeniya' => 'university-of-peradeniya',
        'University Of Peradeniya' => 'university-of-peradeniya',
        'University of Kelaniya' => 'university-of-kelaniya',
        'University Of Kelaniya' => 'university-of-kelaniya',
        'University of Moratuwa' => 'university-of-moratuwa',
        'University Of Moratuwa' => 'university-of-moratuwa',
        'University of Sri Jayewardenepura' => 'university-of-sri-jayewardenepura',
        'University Of Sri Jayewardenepura' => 'university-of-sri-jayewardenepura',
        'University of Jaffna' => 'university-of-jaffna',
        'University Of Jaffna' => 'university-of-jaffna',
        'University of Ruhuna' => 'university-of-ruhuna',
        'University Of Ruhuna' => 'university-of-ruhuna',
        'Eastern University' => 'eastern-university',
        'Sabaragamuwa University' => 'sabaragamuwa-university',
        'Wayamba University' => 'wayamba-university',
        'Rajarata University' => 'rajarata-university',
        'Uva Wellassa University' => 'uva-wellassa-university',
        'Open University of Sri Lanka' => 'open-university',
        'Open University Of Sri Lanka' => 'open-university',
        'Buddhist and Pali University' => 'buddhist-and-pali-university',
        'Buddhist And Pali University' => 'buddhist-and-pali-university',
        'SLIIT' => 'sliit',
        'Sri Lanka Institute of Information Technology' => 'sliit',
        'NSBM Green University' => 'nsbm',
        'NSBM' => 'nsbm',
        'CINEC Campus' => 'cinec',
        'CINEC' => 'cinec',
        'APIIT' => 'apiit',
        'Asia Pacific Institute of Information Technology' => 'apiit',
        'KIU' => 'kiu',
        'Kaatsu International University' => 'kiu',
    ];
    
    // Get all events with university field
    $stmt = $pdo->query("SELECT id, title, university, visibility FROM events WHERE is_deleted = 0");
    $events = $stmt->fetchAll();
    
    $updatedCount = 0;
    $alreadyCorrect = 0;
    $notMapped = [];
    
    foreach ($events as $event) {
        $currentUniversity = $event['university'];
        $eventId = $event['id'];
        $eventTitle = $event['title'];
        
        // Check if it's already in the correct format (lowercase with hyphens)
        if (preg_match('/^[a-z0-9-]+$/', $currentUniversity)) {
            $alreadyCorrect++;
            continue;
        }
        
        // Try to find mapping
        if (isset($universityMap[$currentUniversity])) {
            $newUniversity = $universityMap[$currentUniversity];
            
            // Update the event
            $updateStmt = $pdo->prepare("UPDATE events SET university = :new_university WHERE id = :id");
            $updateStmt->execute([
                'new_university' => $newUniversity,
                'id' => $eventId
            ]);
            
            echo "✅ Updated Event #$eventId \"$eventTitle\"\n";
            echo "   From: \"$currentUniversity\" → To: \"$newUniversity\"\n\n";
            $updatedCount++;
        } else {
            $notMapped[] = [
                'id' => $eventId,
                'title' => $eventTitle,
                'university' => $currentUniversity
            ];
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Events already in correct format: $alreadyCorrect\n";
    echo "✅ Events updated: $updatedCount\n";
    
    if (!empty($notMapped)) {
        echo "⚠️  Events not mapped (manual review needed): " . count($notMapped) . "\n\n";
        foreach ($notMapped as $item) {
            echo "   Event #{$item['id']}: \"{$item['title']}\" - University: \"{$item['university']}\"\n";
        }
    }
    
    echo "\n✓ University field normalization complete!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
