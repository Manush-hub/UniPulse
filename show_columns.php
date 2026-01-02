<?php
/**
 * Quick check of actual columns in each table
 */

require_once 'app/Core/init.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    echo "<h1>Actual Table Columns</h1>";
    
    // University Users
    echo "<h2>university_users</h2>";
    $result = $conn->query("SHOW COLUMNS FROM university_users");
    $columns = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    // Public Users
    echo "<h2>public_users</h2>";
    $result = $conn->query("SHOW COLUMNS FROM public_users");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    // Publishers
    echo "<h2>publishers</h2>";
    $result = $conn->query("SHOW COLUMNS FROM publishers");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
    // Sponsors
    echo "<h2>sponsors</h2>";
    $result = $conn->query("SHOW COLUMNS FROM sponsors");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
