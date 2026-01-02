<?php
/**
 * Check database tables for user data
 */

require_once 'app/Core/init.php';

echo "<h1>Database Table Check</h1>";

try {
    $db = new Database();
    
    echo "<h2>University Users Table</h2>";
    $result = $db->query("SELECT COUNT(*) as count FROM university_users");
    echo "Total records: " . ($result ? $result[0]->count : '0') . "<br>";
    
    echo "<h2>Public Users Table</h2>";
    $result = $db->query("SELECT COUNT(*) as count FROM public_users");
    echo "Total records: " . ($result ? $result[0]->count : '0') . "<br>";
    
    echo "<h2>Publishers Table</h2>";
    $result = $db->query("SELECT COUNT(*) as count FROM publishers");
    echo "Total records: " . ($result ? $result[0]->count : '0') . "<br>";
    
    echo "<h2>Sponsors Table</h2>";
    $result = $db->query("SELECT COUNT(*) as count FROM sponsors");
    echo "Total records: " . ($result ? $result[0]->count : '0') . "<br>";
    
    echo "<hr>";
    echo "<h2>Sample Data Check</h2>";
    
    echo "<h3>Recent Publishers:</h3>";
    $result = $db->query("SELECT id, society_name, email, created_at FROM publishers ORDER BY created_at DESC LIMIT 3");
    if ($result) {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "No publishers found<br>";
    }
    
    echo "<h3>Recent Sponsors:</h3>";
    $result = $db->query("SELECT id, company_name, email, created_at FROM sponsors ORDER BY created_at DESC LIMIT 3");
    if ($result) {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "No sponsors found<br>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
