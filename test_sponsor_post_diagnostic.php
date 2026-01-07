<?php
/**
 * Direct test of the sponsor post endpoint
 * Visit this page and it will show you exactly what error is happening
 */

// Include the necessary files to set up the environment
require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';
require_once 'app/Core/Model.php';
require_once 'app/Core/AuthService.php';
require_once 'app/models/User.php';
require_once 'app/models/Sponsor.php';
require_once 'app/models/Event.php';
require_once 'app/models/SponsorPost.php';

echo "<h1>Sponsor Post Submission Diagnostic</h1>";

// Check if user is logged in as sponsor
$user = AuthService::getCurrentUser();

if (!$user) {
    echo "<p style='color:red'><strong>❌ NOT LOGGED IN</strong></p>";
    echo "<p><a href='/unipulse/public/signin'>Login as Sponsor</a></p>";
} else if ($user['type'] !== 'sponsor') {
    echo "<p style='color:red'><strong>❌ NOT A SPONSOR</strong> (You are: " . $user['type'] . ")</p>";
} else {
    echo "<p style='color:green'><strong>✓ Logged in as Sponsor</strong></p>";
    echo "<p>Sponsor ID: " . $user['id'] . "</p>";
    
    // Check if sponsor profile is complete
    $validation = SponsorPost::validateSponsorProfile($user['id']);
    if ($validation['valid']) {
        echo "<p style='color:green'><strong>✓ Sponsor profile is complete</strong></p>";
    } else {
        echo "<p style='color:orange'><strong>⚠ Sponsor profile incomplete:</strong> " . $validation['message'] . "</p>";
    }
    
    // Check if sponsor_posts table exists
    try {
        $string = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];
        $conn = new PDO($string, DBUSER, DBPASS, $options);
        
        $tableCheck = $conn->query("SHOW TABLES LIKE 'sponsor_posts'")->fetchAll();
        if (!empty($tableCheck)) {
            echo "<p style='color:green'><strong>✓ sponsor_posts table exists</strong></p>";
        } else {
            echo "<p style='color:red'><strong>❌ sponsor_posts table does NOT exist</strong></p>";
            echo "<p><a href='/unipulse/test_sponsor_posts_table.php'>Run Migration</a></p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'><strong>❌ Database error:</strong> " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h2>Try Submitting a Test Post</h2>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='text' name='title' placeholder='Title' value='Test Sponsor Post' required>";
echo "<textarea name='content' placeholder='Content' required style='width:100%;height:100px'>This is a test sponsor post for debugging purposes</textarea>";
echo "<input type='text' name='website_url' placeholder='Website URL (optional)' value='https://example.com'>";
echo "<button type='submit'>Test Submit</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Data Received</h2>";
    echo "<pre>";
    echo "Title: " . ($_POST['title'] ?? 'N/A') . "\n";
    echo "Content: " . (isset($_POST['content']) ? substr($_POST['content'], 0, 50) . "..." : 'N/A') . "\n";
    echo "</pre>";
}
?>
