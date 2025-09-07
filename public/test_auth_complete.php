<?php
require_once '../app/Core/init.php';

// Test authentication with existing users
$authService = new AuthService();

echo "<h2>Testing Authentication with Existing Users</h2>";
echo "<p>Testing with password: 'password'</p>";

$testUsers = [
    ['email' => 'public.test.1757174163@gmail.com', 'expected_type' => 'public'],
    ['email' => 'university.test.1757174162@student.uoc.lk', 'expected_type' => 'university'],
    ['email' => 'sponsor.test.1757174163@company.com', 'expected_type' => 'sponsor'],
    ['email' => 'publisher.test.1757174163@society.uoc.lk', 'expected_type' => 'publisher']
];

foreach ($testUsers as $testUser) {
    echo "<h3>Testing: {$testUser['email']}</h3>";
    
    $result = $authService->authenticate($testUser['email'], 'password');
    
    if ($result) {
        echo "✅ Authentication successful!<br>";
        echo "&nbsp;&nbsp;- User Type: {$result['type']}<br>";
        echo "&nbsp;&nbsp;- Dashboard: {$result['dashboard']}<br>";
        echo "&nbsp;&nbsp;- Table: {$result['table']}<br>";
        
        if ($result['type'] === $testUser['expected_type']) {
            echo "&nbsp;&nbsp;- ✅ User type matches expected: {$testUser['expected_type']}<br>";
        } else {
            echo "&nbsp;&nbsp;- ❌ User type mismatch. Expected: {$testUser['expected_type']}, Got: {$result['type']}<br>";
        }
    } else {
        echo "❌ Authentication failed<br>";
    }
    
    echo "<br>";
}

echo "<h3>Testing with Invalid Credentials</h3>";
$invalidResult = $authService->authenticate('invalid@email.com', 'wrongpassword');
if ($invalidResult) {
    echo "❌ Authentication should have failed but didn't<br>";
} else {
    echo "✅ Authentication correctly failed for invalid credentials<br>";
}
?>
