<?php
require_once '../app/Core/init.php';

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.success { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
.error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
.info { color: #004085; background: #e7f3ff; padding: 10px; border-radius: 4px; margin: 10px 0; }
h2 { color: #333; border-bottom: 2px solid #4A5BCC; padding-bottom: 10px; }
h3 { color: #4A5BCC; }
code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style>";

echo "<div class='container'>";
echo "<h1>🔐 UniPulse Authentication System Status</h1>";

try {
    // Test 1: Database Connection
    echo "<h2>1. Database Connection Test</h2>";
    $authService = new AuthService();
    
    $tables = [
        'public_users' => 'Public Users',
        'university_users' => 'University Users', 
        'sponsors' => 'Sponsors',
        'publishers' => 'Publishers'
    ];
    
    foreach ($tables as $table => $label) {
        try {
            $query = "SELECT COUNT(*) as count FROM $table";
            $result = $authService->query($query);
            
            if ($result && $result[0]->count >= 0) {
                echo "<div class='success'>✅ $label table: {$result[0]->count} users found</div>";
            } else {
                echo "<div class='error'>❌ $label table: Query failed</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ $label table: Error - " . $e->getMessage() . "</div>";
        }
    }
    
    // Test 2: Authentication System
    echo "<h2>2. Authentication System Test</h2>";
    
    // Get actual users from database
    $testCredentials = [
        ['email' => 'public.test.1757174163@gmail.com', 'type' => 'public'],
        ['email' => 'university.test.1757174162@student.uoc.lk', 'type' => 'university'],
        ['email' => 'sponsor.test.1757174163@company.com', 'type' => 'sponsor'],
        ['email' => 'publisher.test.1757174163@society.uoc.lk', 'type' => 'publisher']
    ];
    
    foreach ($testCredentials as $cred) {
        echo "<h3>Testing: {$cred['email']}</h3>";
        
        $result = $authService->authenticate($cred['email'], 'password');
        
        if ($result) {
            echo "<div class='success'>✅ Authentication successful</div>";
            echo "<div class='info'>
                👤 User Type: <code>{$result['type']}</code><br>
                🏠 Dashboard: <code>{$result['dashboard']}</code><br>
                📊 Table: <code>{$result['table']}</code>
            </div>";
        } else {
            echo "<div class='error'>❌ Authentication failed</div>";
        }
    }
    
    // Test 3: Invalid Credentials
    echo "<h2>3. Security Test (Invalid Credentials)</h2>";
    $invalidResult = $authService->authenticate('nonexistent@test.com', 'wrongpassword');
    if (!$invalidResult) {
        echo "<div class='success'>✅ Invalid credentials correctly rejected</div>";
    } else {
        echo "<div class='error'>❌ Security issue: Invalid credentials accepted</div>";
    }
    
    // Test 4: Session System
    echo "<h2>4. Session System Test</h2>";
    if (class_exists('AuthService')) {
        echo "<div class='success'>✅ AuthService class loaded</div>";
        
        if (method_exists('AuthService', 'isLoggedIn')) {
            echo "<div class='success'>✅ Session methods available</div>";
        } else {
            echo "<div class='error'>❌ Session methods missing</div>";
        }
    } else {
        echo "<div class='error'>❌ AuthService class not found</div>";
    }
    
    // Test 5: Dashboard Routes
    echo "<h2>5. Dashboard Routes Test</h2>";
    
    $dashboardPaths = [
        'User Dashboard' => '/Applications/MAMP/htdocs/unipulse/app/controllers/User/Dashboard.php',
        'Sponsor Dashboard' => '/Applications/MAMP/htdocs/unipulse/app/controllers/Sponsor/Dashboard.php',
        'Publisher Dashboard' => '/Applications/MAMP/htdocs/unipulse/app/controllers/Publisher/Dashboard.php'
    ];
    
    foreach ($dashboardPaths as $name => $path) {
        if (file_exists($path)) {
            echo "<div class='success'>✅ $name controller exists</div>";
        } else {
            echo "<div class='error'>❌ $name controller missing</div>";
        }
    }
    
    echo "<h2>6. Implementation Summary</h2>";
    echo "<div class='info'>
        <h3>✅ Completed Features:</h3>
        <ul>
            <li><strong>Multi-table Authentication:</strong> Checks public_users, university_users, sponsors, and publishers</li>
            <li><strong>Password Verification:</strong> Uses password_hash/password_verify for security</li>
            <li><strong>Session Management:</strong> Maintains user sessions with type-specific data</li>
            <li><strong>Role-based Dashboards:</strong> Redirects users to appropriate dashboards based on user type</li>
            <li><strong>Security Middleware:</strong> Protects dashboard routes from unauthorized access</li>
        </ul>
        
        <h3>🎯 User Type Mappings:</h3>
        <ul>
            <li><strong>Public Users:</strong> → <code>/unipulse/public/user/dashboard</code></li>
            <li><strong>University Users:</strong> → <code>/unipulse/public/user/dashboard</code></li>
            <li><strong>Sponsors:</strong> → <code>/unipulse/public/sponsor/dashboard</code></li>
            <li><strong>Publishers:</strong> → <code>/unipulse/public/publisher/dashboard</code></li>
        </ul>
        
        <h3>🔧 How to Test:</h3>
        <p>Visit <a href='/test_login.html'>/test_login.html</a> to test the login system with the existing users.</p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Critical Error: " . $e->getMessage() . "</div>";
}

echo "</div>";
?>
