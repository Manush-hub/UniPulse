<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Change to the correct working directory
chdir('/Applications/MAMP/htdocs/unipulse/public');

require_once '../app/Core/init.php';

echo "=== Direct Authentication Test ===\n";

try {
    $authService = new AuthService();
    
    $email = 'mansss@gmail.com';
    $password = '1142119112';
    
    echo "Testing with:\n";
    echo "Email: $email\n";
    echo "Password: $password\n\n";
    
    // Test authentication
    $result = $authService->authenticate($email, $password);
    
    if ($result) {
        echo "✅ AUTHENTICATION SUCCESSFUL!\n";
        echo "User Type: " . $result['type'] . "\n";
        echo "Dashboard: " . $result['dashboard'] . "\n";
        echo "Table: " . $result['table'] . "\n";
        echo "User ID: " . $result['user']->id . "\n";
    } else {
        echo "❌ AUTHENTICATION FAILED\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
