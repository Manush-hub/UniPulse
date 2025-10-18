<?php
// Test moderator creation functionality
require_once 'app/Core/init.php';

// Load the Moderator model
spl_autoload_register(function($classname) {
    $classname = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
    require_once "app/models/" . $classname . ".php";
});

echo "Testing Moderator Creation...\n";

// Test data - using admin ID 1 which we know exists
$testData = [
    'full_name' => 'Jane Smith',
    'email' => 'jane.smith' . time() . '@example.com', // Use unique email
    'password' => 'testpass123',
    'phone' => '555-0123',
    'assigned_by' => 1, // Admin ID 1 exists
    'permissions' => json_encode([
        'view_events' => true,
        'edit_events' => true,
        'view_users' => true,
        'moderate_content' => true
    ])
];

// Create moderator instance
$moderatorModel = new Moderator();

// Test creation
echo "Creating test moderator...\n";
$result = $moderatorModel->create($testData);

if ($result['success']) {
    echo "✅ SUCCESS: Moderator created successfully!\n";
    
    // Verify in database
    $created = $moderatorModel->where(['email' => $testData['email']]);
    if ($created) {
        echo "✅ VERIFIED: Moderator found in database\n";
        echo "   ID: " . $created[0]->id . "\n";
        echo "   Name: " . $created[0]->full_name . "\n";
        echo "   Email: " . $created[0]->email . "\n";
        echo "   Permissions: " . $created[0]->permissions . "\n";
    }
} else {
    echo "❌ FAILED: " . json_encode($result['errors']) . "\n";
}

echo "\nTest completed.\n";
?>
