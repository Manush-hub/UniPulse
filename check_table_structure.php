<?php
/**
 * Check actual table structures
 */

require_once 'app/Core/init.php';

echo "<h1>Database Table Structures</h1>";

try {
    $db = new Database();
    $conn = $db->connect();
    
    $tables = ['university_users', 'public_users', 'publishers', 'sponsors'];
    
    foreach ($tables as $table) {
        echo "<h2>{$table}</h2>";
        $stmt = $conn->query("DESCRIBE {$table}");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
