<?php
/**
 * Handle suspension appeal submissions
 */

require_once '../app/Core/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['user_id'] ?? null;
$userType = $data['user_type'] ?? null;
$appealMessage = $data['appeal_message'] ?? '';

if (!$userId || !$userType || !$appealMessage) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->connect();
    
    // Insert appeal
    $query = "INSERT INTO suspension_appeals (user_id, user_type, appeal_message, status, created_at) 
              VALUES (:user_id, :user_type, :appeal_message, 'pending', NOW())";
    
    $stmt = $conn->prepare($query);
    $result = $stmt->execute([
        'user_id' => $userId,
        'user_type' => $userType,
        'appeal_message' => $appealMessage
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your appeal has been submitted successfully. An admin will review it shortly.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit appeal']);
    }
} catch (Exception $e) {
    error_log("Appeal submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
