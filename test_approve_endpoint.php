<?php
// Test the approve endpoint to see what's being returned

// Start session and set up test user
session_start();
$_SESSION['user_id'] = 5; // Publisher ID
$_SESSION['user_type'] = 'publisher';

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['sponsorship_id'] = 2; // Use an existing sponsorship ID

// Include necessary files
require_once __DIR__ . '/app/Core/config.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/SessionMiddleware.php';
require_once __DIR__ . '/app/Core/AuthService.php';

// Load the Sponsorships controller
require_once __DIR__ . '/app/controllers/Publisher/Sponsorships.php';

// Create instance and call approve
$controller = new Sponsorships();

echo "\n=== Starting approve() call ===\n";
echo "Before approve() output:\n\n";

// Capture all output
ob_start();
$controller->approve();
$output = ob_get_clean();

echo "\n=== Output captured ===\n";
echo "Length: " . strlen($output) . " bytes\n";
echo "Content:\n";
echo $output;
echo "\n=== End output ===\n";

// Try to decode as JSON
echo "\n=== JSON decode test ===\n";
$decoded = json_decode($output);
if ($decoded) {
    echo "Success! Decoded: " . print_r($decoded, true) . "\n";
} else {
    echo "Failed to decode. JSON error: " . json_last_error_msg() . "\n";
    echo "First 500 chars: " . substr($output, 0, 500) . "\n";
    echo "Last 500 chars: " . substr($output, -500) . "\n";
    
    // Show hex dump of first 100 bytes
    echo "\nHex dump of first 100 bytes:\n";
    echo bin2hex(substr($output, 0, 100)) . "\n";
}
