<?php
/**
 * Test sponsor post submission directly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to capture any stray output
ob_start();

// Check what's in the request
echo "<!-- DEBUG: Request Method: " . $_SERVER['REQUEST_METHOD'] . " -->\n";
echo "<!-- DEBUG: Current URL: " . $_SERVER['REQUEST_URI'] . " -->\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate a POST request to the sponsor post endpoint
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Get buffer contents and clear
    $bufferedOutput = ob_get_clean();
    
    // Return a test response
    $response = [
        'success' => false,
        'message' => 'Test mode - This is a test response',
        'buffered_output' => $bufferedOutput,
        'post_data' => $_POST,
        'files' => array_keys($_FILES ?? [])
    ];
    
    echo json_encode($response);
    exit;
} else {
    // Show form for testing
    ob_end_clean();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Sponsor Post Submission</title>
    </head>
    <body>
        <h1>Test Sponsor Post Submission</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" value="Test Title" required>
            <textarea name="content" placeholder="Content" required>Test content here</textarea>
            <button type="submit">Submit Test</button>
        </form>
    </body>
    </html>
    <?php
}
?>
