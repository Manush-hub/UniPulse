<?php
require_once '../app/Core/init.php';

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.success { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
.error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
.info { color: #004085; background: #e7f3ff; padding: 10px; border-radius: 4px; margin: 10px 0; }
</style>";

echo "<div class='container'>";
echo "<h1>🔐 Testing Specific User: mansss@gmail.com</h1>";

try {
    $authService = new AuthService();
    
    // Test the specific user
    $email = 'mansss@gmail.com';
    $password = '1142119112';
    
    echo "<h2>Testing Authentication</h2>";
    echo "<div class='info'>Testing: $email with password: $password</div>";
    
    // First, let's check if the user exists in the database
    echo "<h3>1. Database Check</h3>";
    $query = "SELECT id, company_name, email, password_hash FROM sponsors WHERE email = :email";
    $user = $authService->getRow($query, ['email' => $email]);
    
    if ($user) {
        echo "<div class='success'>✅ User found in sponsors table</div>";
        echo "<div class='info'>
            ID: {$user->id}<br>
            Company: {$user->company_name}<br>
            Email: {$user->email}<br>
            Password Hash: " . substr($user->password_hash, 0, 20) . "...
        </div>";
        
        // Test password verification
        echo "<h3>2. Password Verification Test</h3>";
        $passwordMatch = password_verify($password, $user->password_hash);
        
        if ($passwordMatch) {
            echo "<div class='success'>✅ Password verification successful</div>";
        } else {
            echo "<div class='error'>❌ Password verification failed</div>";
            echo "<div class='info'>This means the password '$password' doesn't match the stored hash</div>";
        }
        
        // Test full authentication
        echo "<h3>3. Full Authentication Test</h3>";
        $result = $authService->authenticate($email, $password);
        
        if ($result) {
            echo "<div class='success'>✅ Full authentication successful!</div>";
            echo "<div class='info'>
                User Type: {$result['type']}<br>
                Dashboard: {$result['dashboard']}<br>
                Table: {$result['table']}
            </div>";
        } else {
            echo "<div class='error'>❌ Full authentication failed</div>";
        }
        
    } else {
        echo "<div class='error'>❌ User not found in sponsors table</div>";
    }
    
    // Let's also check what password hash should be generated for this password
    echo "<h3>4. Password Hash Generation Test</h3>";
    $generatedHash = password_hash($password, PASSWORD_DEFAULT);
    echo "<div class='info'>Generated hash for '$password': " . substr($generatedHash, 0, 30) . "...</div>";
    
    $testVerify = password_verify($password, $generatedHash);
    echo "<div class='info'>Verification of generated hash: " . ($testVerify ? "✅ Success" : "❌ Failed") . "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>Stack trace: " . $e->getTraceAsString() . "</div>";
}

echo "</div>";
?>
