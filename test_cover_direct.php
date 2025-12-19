<?php
// Direct test of cover upload
session_start();
require_once 'app/Core/init.php';

echo "<h2>Testing Cover Photo Upload</h2>";

// Simulate logged in publisher
$_SESSION['user_id'] = 4;
$_SESSION['user_type'] = 'publisher';
$_SESSION['username'] = 'test_publisher';

echo "<p>Session set: Publisher ID 4</p>";

// Create a test image file
$testImagePath = __DIR__ . '/test_image.jpg';
if (!file_exists($testImagePath)) {
    // Create a simple red 400x200 image
    $img = imagecreatetruecolor(400, 200);
    $red = imagecolorallocate($img, 255, 0, 0);
    imagefill($img, 0, 0, $red);
    imagejpeg($img, $testImagePath);
    imagedestroy($img);
    echo "<p>Created test image</p>";
}

// Simulate file upload
$_FILES['image'] = [
    'name' => 'test_cover.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $testImagePath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($testImagePath)
];

echo "<p>Simulating file upload...</p>";
echo "<pre>FILES: " . print_r($_FILES, true) . "</pre>";

// Load the controller
require_once 'app/controllers/Publisher/Profile.php';
require_once 'app/models/Publisher.php';

$controller = new Publisherprofile();

echo "<p>Calling uploadCoverPhoto...</p>";
ob_start();
$controller->uploadCoverPhoto();
$response = ob_get_clean();

echo "<h3>Response:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Check database
require_once 'app/Core/config.php';
$pdo = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME, DBUSER, DBPASS);
$stmt = $pdo->query("SELECT logo_url, cover_photo_url FROM publisher_profiles WHERE publisher_id = 4");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>Database Check:</h3>";
echo "<p>Logo URL: " . ($result['logo_url'] ?? 'NULL') . "</p>";
echo "<p>Cover URL: " . ($result['cover_photo_url'] ?? 'NULL') . "</p>";

// Check files
$uploadDir = __DIR__ . '/public/uploads/publisher_images/4/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    echo "<h3>Files in upload directory:</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Upload directory does not exist</p>";
}
