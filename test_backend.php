<?php
/**
 * Database Setup and Test Script
 * Run this to create the database schema and test connections
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/backend/config/database.php';

try {
    echo "Testing database connection...\n";
    
    // Test database connection
    $db = DatabaseConfig::getInstance();
    $connection = $db->getConnection();
    
    echo "✓ Database connection successful!\n";
    
    // Read and execute SQL schema (using SQLite version for demo)
    $sqlFile = __DIR__ . '/backend/database/setup_sqlite.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL setup file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "\nExecuting database schema...\n";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $connection->exec($statement);
                echo "✓ Executed: " . substr(preg_replace('/\s+/', ' ', $statement), 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "✗ Error executing statement: " . $e->getMessage() . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    // Test User class
    echo "\nTesting User class...\n";
    require_once __DIR__ . '/backend/classes/User.php';
    
    $user = new User();
    echo "✓ User class loaded successfully!\n";
    
    // Test SessionManager
    echo "\nTesting SessionManager class...\n";
    require_once __DIR__ . '/backend/includes/session.php';
    
    $sessionManager = SessionManager::getInstance();
    echo "✓ SessionManager class loaded successfully!\n";
    
    // Test password hashing
    $testPassword = 'TestPass123!';
    $hashedPassword = $user->hashPassword($testPassword);
    $isValid = $user->verifyPassword($testPassword, $hashedPassword);
    
    if ($isValid) {
        echo "✓ Password hashing test successful!\n";
    } else {
        echo "✗ Password hashing test failed!\n";
    }
    
    // Check if users exist
    $result = $db->fetchAll("SELECT username, email, user_type FROM users LIMIT 5");
    
    if ($result) {
        echo "\nExisting users:\n";
        foreach ($result as $user) {
            echo "- {$user['username']} ({$user['email']}) - {$user['user_type']}\n";
        }
    } else {
        echo "\nNo users found in database.\n";
    }
    
    echo "\n✓ Database setup and tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}