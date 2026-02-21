<?php
require_once 'app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Testing Sponsorship Database</h2>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'event_sponsorships'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'event_sponsorships' exists<br><br>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        $stmt = $pdo->query("DESCRIBE event_sponsorships");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Count records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM event_sponsorships");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Total sponsorships: $count</p>";
        
        // Show recent records
        if ($count > 0) {
            echo "<h3>Recent Sponsorships:</h3>";
            $stmt = $pdo->query("SELECT * FROM event_sponsorships ORDER BY created_at DESC LIMIT 5");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Event ID</th><th>Package ID</th><th>Sponsor ID</th><th>Amount</th><th>Status</th><th>Created</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['event_id']}</td>";
                echo "<td>{$row['package_id']}</td>";
                echo "<td>{$row['sponsor_id']}</td>";
                echo "<td>{$row['amount']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "❌ Table 'event_sponsorships' does NOT exist<br>";
        echo "<p>You need to run the migration: database/create_event_sponsorships.php</p>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
