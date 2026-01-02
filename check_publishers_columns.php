<?php
require_once '../app/Core/config.php';

try {
    $conn = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query("DESCRIBE publishers");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Publishers Table Columns:</h2>";
    echo "<pre>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Null'] . "\n";
    }
    echo "</pre>";
    
    // Check for missing columns
    $requiredColumns = ['approved_by', 'approved_at', 'rejection_reason'];
    echo "<h3>Missing Columns Check:</h3>";
    $existingColumns = array_column($columns, 'Field');
    
    foreach ($requiredColumns as $col) {
        if (!in_array($col, $existingColumns)) {
            echo "<p style='color: red;'>❌ Missing: $col</p>";
        } else {
            echo "<p style='color: green;'>✓ Found: $col</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
