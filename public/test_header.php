<?php
// Start session to test logged-in user display
session_start();

// Simulate a logged-in university user for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@university.edu';
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_type'] = 'university';
$_SESSION['user_table'] = 'university_users';
$_SESSION['logged_in'] = true;

require_once __DIR__ . '/../app/Core/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Test - UniPulse</title>
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }
        .test-info {
            background: #e3f2fd;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            border-left: 4px solid #1976d2;
        }
        .test-section {
            background: white;
            margin: 20px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="test-info">
        <h2>🧪 Header Display Test Page</h2>
        <p>This page tests the enhanced header functionality with real database user information.</p>
        <p><strong>Simulated User:</strong> University user (ID: 1) from database</p>
    </div>

    <!-- Include the actual header component -->
    <?php include __DIR__ . '/../app/views/User/components/header.php'; ?>

    <div class="test-section">
        <h3>Header Display Test Results</h3>
        <ul>
            <li>✅ Header should display the real user's full name from database</li>
            <li>✅ For university users, university name should appear below username</li>
            <li>✅ For public users, "Public User" should appear below username</li>
            <li>✅ All user information should be properly escaped for security</li>
        </ul>
    </div>

    <div class="test-section">
        <h3>Current User Information (from Session)</h3>
        <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
<?php
$currentUser = AuthService::getCurrentUser();
$userDetails = AuthService::getCurrentUserDetails();
echo "Session User Data:\n";
print_r($currentUser);
echo "\nDatabase User Details:\n";
print_r($userDetails);
?>
        </pre>
    </div>
</body>
</html>