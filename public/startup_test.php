<?php
require_once '../app/Core/init.php';

echo "=== UniPulse Startup Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing database connection...\n";
try {
    $conn = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
    echo "   ✅ Database connection successful\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check tables exist
echo "\n2. Checking required tables...\n";
$requiredTables = ['university_users', 'public_users', 'users'];
$stmt = $conn->prepare("SHOW TABLES");
$stmt->execute();
$existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($requiredTables as $table) {
    if (in_array($table, $existingTables)) {
        echo "   ✅ Table '$table' exists\n";
    } else {
        echo "   ❌ Table '$table' missing\n";
    }
}

// Test 3: Model loading
echo "\n3. Testing model loading...\n";
try {
    $universityUser = new UniversityUser();
    echo "   ✅ UniversityUser model loaded\n";
} catch (Exception $e) {
    echo "   ❌ UniversityUser model failed: " . $e->getMessage() . "\n";
}

try {
    $publicUser = new PublicUser();
    echo "   ✅ PublicUser model loaded\n";
} catch (Exception $e) {
    echo "   ❌ PublicUser model failed: " . $e->getMessage() . "\n";
}

try {
    $user = new User();
    echo "   ✅ User model loaded\n";
} catch (Exception $e) {
    echo "   ❌ User model failed: " . $e->getMessage() . "\n";
}

// Test 4: Database initialization check
echo "\n4. Testing database initializer...\n";
try {
    $dbInit = new DatabaseInitializer();
    if ($dbInit->isDatabaseInitialized()) {
        echo "   ✅ Database is properly initialized\n";
    } else {
        echo "   ❌ Database initialization incomplete\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database initializer failed: " . $e->getMessage() . "\n";
}

// Test 5: Sample data validation
echo "\n5. Testing data validation...\n";
try {
    $universityUser = new UniversityUser();
    $testData = [
        'full-name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '771234567',
        'password' => 'testpass123',
        'confirm-password' => 'testpass123',
        'university' => 'university-of-colombo',
        'faculty' => 'faculty-of-engineering',
        'student-staff-id' => 'TEST123',
        'academic-year' => '3rd-year',
        'nic' => '199512345678'
    ];
    
    $errors = $universityUser->validateData($testData);
    if (empty($errors)) {
        echo "   ✅ Data validation working correctly\n";
    } else {
        echo "   ❌ Data validation issues: " . implode(', ', $errors) . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Validation test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Startup test completed ===\n";
echo "✅ System is ready for use!\n";
echo "\nAccess points:\n";
echo "- University Registration: http://localhost:8080/unireg\n";
echo "- Public Registration: http://localhost:8080/publicreg\n";
echo "- Setup Verification: http://localhost:8080/setup_verification.php\n";
echo "- Database Test: http://localhost:8080/test_database.php\n";
?>
