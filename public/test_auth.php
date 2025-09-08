<?php
require_once '../app/Core/init.php';

// Test database connection and user tables
try {
    $authService = new AuthService();
    
    echo "<h2>Testing Authentication System</h2>";
    
    // Test database connection
    echo "<h3>1. Database Connection Test</h3>";
    
    $tables = ['public_users', 'university_users', 'sponsors', 'publishers'];
    
    foreach ($tables as $table) {
        try {
            $query = "SELECT COUNT(*) as count FROM $table";
            $result = $authService->query($query);
            
            if ($result) {
                echo "✅ Table '$table' exists and is accessible<br>";
            } else {
                echo "❌ Table '$table' query failed<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error accessing table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<br><h3>2. Sample Authentication Test</h3>";
    echo "You can test authentication with any existing user credentials from your database.<br>";
    echo "The system will check all user tables (public_users, university_users, sponsors, publishers) automatically.<br>";
    
    echo "<br><h3>3. Authentication Flow</h3>";
    echo "1. User enters email and password<br>";
    echo "2. System checks all user tables for matching email<br>";
    echo "3. If found, verifies password hash<br>";
    echo "4. If valid, starts session and redirects to appropriate dashboard:<br>";
    echo "&nbsp;&nbsp;- Public/University users → /unipulse/public/user/dashboard<br>";
    echo "&nbsp;&nbsp;- Sponsors → /unipulse/public/sponsor/dashboard<br>";
    echo "&nbsp;&nbsp;- Publishers → /unipulse/public/publisher/dashboard<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
