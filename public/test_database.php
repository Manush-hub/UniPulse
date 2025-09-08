<?php
require_once '../app/Core/init.php';

echo "<h2>Database Connection Test</h2>\n";

try {
    // Test University User Registration
    echo "<h3>Testing University User Registration</h3>\n";
    
    $universityUser = new UniversityUser();
    
    // Sample data
    $testData = [
        'full_name' => 'John Doe',
        'email' => 'john.doe.test@example.com',
        'phone' => '771234567',
        'country_code' => '+94',
        'password_hash' => password_hash('testpassword123', PASSWORD_DEFAULT),
        'university' => 'university-of-colombo',
        'faculty' => 'faculty-of-engineering',
        'student_staff_id' => 'ENG19123',
        'academic_year' => '3rd-year',
        'nic' => '199512345678',
        'gender' => 'male',
        'interests' => json_encode(['technology', 'academic'])
    ];
    
    // Check if email already exists (to avoid duplicate key error)
    if (!$universityUser->emailExists($testData['email'])) {
        $userId = $universityUser->create($testData);
        if ($userId) {
            echo "✅ University user created successfully with ID: $userId<br>\n";
            
            // Test creating user in main users table
            $user = new User();
            $mainUserResult = $user->createFromRegistration(
                $testData['email'],
                'testpassword123',
                'university',
                $userId
            );
            
            if ($mainUserResult) {
                echo "✅ Main user record created successfully<br>\n";
            } else {
                echo "❌ Failed to create main user record<br>\n";
            }
        } else {
            echo "❌ Failed to create university user<br>\n";
        }
    } else {
        echo "ℹ️ Test university user already exists<br>\n";
    }
    
    // Test Public User Registration
    echo "<h3>Testing Public User Registration</h3>\n";
    
    $publicUser = new PublicUser();
    
    $testPublicData = [
        'full_name' => 'Jane Smith',
        'email' => 'jane.smith.test@example.com',
        'phone' => '771234568',
        'country_code' => '+94',
        'password_hash' => password_hash('testpassword123', PASSWORD_DEFAULT),
        'nic' => '199612345679',
        'gender' => 'female',
        'interests' => json_encode(['cultural', 'social'])
    ];
    
    if (!$publicUser->emailExists($testPublicData['email'])) {
        $publicUserId = $publicUser->create($testPublicData);
        if ($publicUserId) {
            echo "✅ Public user created successfully with ID: $publicUserId<br>\n";
            
            $user = new User();
            $mainUserResult = $user->createFromRegistration(
                $testPublicData['email'],
                'testpassword123',
                'public',
                $publicUserId
            );
            
            if ($mainUserResult) {
                echo "✅ Main user record created successfully<br>\n";
            } else {
                echo "❌ Failed to create main user record<br>\n";
            }
        } else {
            echo "❌ Failed to create public user<br>\n";
        }
    } else {
        echo "ℹ️ Test public user already exists<br>\n";
    }
    
    echo "<h3>Database Tables Status</h3>\n";
    
    // Check table counts
    $conn = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM university_users");
    $uniCount = $stmt->fetchColumn();
    echo "University users: $uniCount<br>\n";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM public_users");
    $pubCount = $stmt->fetchColumn();
    echo "Public users: $pubCount<br>\n";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $mainCount = $stmt->fetchColumn();
    echo "Main users: $mainCount<br>\n";
    
    echo "<br>✅ All tests completed successfully!<br>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
}
?>
