<?php
require_once '../app/Core/config.php';

try {
    $conn = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Adding Approval Columns to Publishers Table</h2>";
    
    // Check if columns exist first
    $stmt = $conn->query("DESCRIBE publishers");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $existingColumns = array_column($columns, 'Field');
    
    // Add approved_by column if it doesn't exist
    if (!in_array('approved_by', $existingColumns)) {
        $conn->exec("ALTER TABLE publishers ADD COLUMN approved_by INT NULL AFTER approval_status");
        echo "<p style='color: green;'>✓ Added approved_by column</p>";
    } else {
        echo "<p style='color: orange;'>⚠ approved_by column already exists</p>";
    }
    
    // Add approved_at column if it doesn't exist
    if (!in_array('approved_at', $existingColumns)) {
        $conn->exec("ALTER TABLE publishers ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by");
        echo "<p style='color: green;'>✓ Added approved_at column</p>";
    } else {
        echo "<p style='color: orange;'>⚠ approved_at column already exists</p>";
    }
    
    // Add rejection_reason column if it doesn't exist
    if (!in_array('rejection_reason', $existingColumns)) {
        $conn->exec("ALTER TABLE publishers ADD COLUMN rejection_reason TEXT NULL AFTER approved_at");
        echo "<p style='color: green;'>✓ Added rejection_reason column</p>";
    } else {
        echo "<p style='color: orange;'>⚠ rejection_reason column already exists</p>";
    }
    
    echo "<h3>Migration Complete!</h3>";
    echo "<p><a href='/unipulse/public/admin/dashboard'>Go to Admin Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
