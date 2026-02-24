<?php
// Check publishers in University of Moratuwa
require_once 'app/Core/config.php';

try {
    // Create PDO connection directly
    $conn = new PDO(
        "mysql:host=" . DBHOST . ";dbname=" . DBNAME,
        DBUSER,
        DBPASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>All Publishers in University of Moratuwa:</h2>\n";
    
    $stmt = $conn->prepare("SELECT id, society_name, university, approval_status FROM publishers WHERE university = ? ORDER BY society_name");
    $stmt->execute(['University of Moratuwa']);
    $publishers = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<pre>";
    echo "Total publishers found: " . count($publishers) . "\n\n";
    
    foreach ($publishers as $publisher) {
        echo "ID: {$publisher->id}\n";
        echo "Name: {$publisher->society_name}\n";
        echo "University: {$publisher->university}\n";
        echo "Approval Status: {$publisher->approval_status}\n";
        echo "---\n";
    }
    echo "</pre>";
    
    echo "<h2>Approved Publishers Only:</h2>\n";
    $stmt = $conn->prepare("SELECT id, society_name, university, approval_status FROM publishers WHERE university = ? AND approval_status = 'approved' ORDER BY society_name");
    $stmt->execute(['University of Moratuwa']);
    $approved = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<pre>";
    echo "Total approved publishers: " . count($approved) . "\n\n";
    
    foreach ($approved as $publisher) {
        echo "ID: {$publisher->id}\n";
        echo "Name: {$publisher->society_name}\n";
        echo "University: {$publisher->university}\n";
        echo "Approval Status: {$publisher->approval_status}\n";
        echo "---\n";
    }
    echo "</pre>";
    
    echo "<h2>Moderators from University of Moratuwa:</h2>\n";
    $stmt = $conn->prepare("SELECT id, full_name, university FROM moderators WHERE university = ? ORDER BY full_name");
    $stmt->execute(['University of Moratuwa']);
    $moderators = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<pre>";
    echo "Total moderators: " . count($moderators) . "\n\n";
    
    foreach ($moderators as $mod) {
        echo "ID: {$mod->id}\n";
        echo "Name: {$mod->full_name}\n";
        echo "University: {$mod->university}\n";
        echo "---\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
