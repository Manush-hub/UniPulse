<?php
// Simple upload endpoint for sponsor cover/logo images
// Expects multipart POST with 'image' file and 'type' = 'cover'|'logo'
if (session_status() == PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Only sponsors can upload
if ($_SESSION['user_type'] !== 'sponsor') {
    http_response_code(403);
    echo json_encode(['error' => 'Only sponsors can upload images']);
    exit;
}

$userId = $_SESSION['user_id'];
$type = $_POST['type'] ?? $_GET['type'] ?? '';

if (empty($type) || !in_array($type, ['cover','logo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image type']);
    exit;
}

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['image'];
// Basic validations
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (strpos($mime, 'image/') !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

$uploadsDir = __DIR__ . '/../uploads/sponsors/' . intval($userId);
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0755, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeExt = preg_replace('/[^a-z0-9]/i','', $ext) ?: 'jpg';
$filename = ($type === 'cover') ? 'cover.' . $safeExt : 'logo.' . $safeExt;
$dest = $uploadsDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file']);
    exit;
}

$publicUrl = '/UniPulse/public/uploads/sponsors/' . intval($userId) . '/' . $filename;

// Save URL to database
require_once __DIR__ . '/../../app/models/Sponsor.php';
$sponsor = new Sponsor();

if ($type === 'cover') {
    $success = $sponsor->updateProfileImages($userId, $publicUrl, null);
} else {
    $success = $sponsor->updateProfileImages($userId, null, $publicUrl);
}

if ($success) {
    echo json_encode(['url' => $publicUrl, 'message' => 'Image uploaded and saved']);
} else {
    // File was saved but DB update failed - still return URL
    error_log("Failed to save image URL to database for sponsor $userId, type: $type");
    echo json_encode(['url' => $publicUrl, 'warning' => 'Image saved but database update failed']);
}
exit;
