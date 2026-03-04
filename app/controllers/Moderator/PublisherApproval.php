<?php

class PublisherApproval extends Controller {

    public function approve($publisherId = '') {
        // Set JSON response header
        header('Content-Type: application/json');
        
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($publisherId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Publisher ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $publisherModel = new Publisher();

            // Fetch publisher name for activity log
            $conn = $publisherModel->connect();
            $stmt = $conn->prepare("SELECT society_name FROM publishers WHERE id = ?");
            $stmt->execute([$publisherId]);
            $pubRow = $stmt->fetch(PDO::FETCH_OBJ);
            $pubName = $pubRow ? $pubRow->society_name : 'Unknown';
            
            $result = $publisherModel->approve($publisherId, $moderatorData['id']);
            
            if ($result) {
                AdminActivity::log(
                    $moderatorData['id'],
                    $moderatorData['name'],
                    'mod_publisher_approved',
                    'publisher',
                    (int)$publisherId,
                    $pubName,
                    'Approved publisher account: ' . $pubName,
                    'check-circle'
                );
                echo json_encode([
                    'success' => true, 
                    'message' => 'Publisher approved successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to approve publisher'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function reject($publisherId = '') {
        // Set JSON response header
        header('Content-Type: application/json');
        
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($publisherId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Publisher ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $reason = $_POST['reason'] ?? 'No reason provided';
            
            $publisherModel = new Publisher();

            // Fetch publisher name for activity log
            $conn = $publisherModel->connect();
            $stmt = $conn->prepare("SELECT society_name FROM publishers WHERE id = ?");
            $stmt->execute([$publisherId]);
            $pubRow = $stmt->fetch(PDO::FETCH_OBJ);
            $pubName = $pubRow ? $pubRow->society_name : 'Unknown';

            $result = $publisherModel->reject($publisherId, $moderatorData['id'], $reason);
            
            if ($result) {
                AdminActivity::log(
                    $moderatorData['id'],
                    $moderatorData['name'],
                    'mod_publisher_rejected',
                    'publisher',
                    (int)$publisherId,
                    $pubName,
                    'Rejected publisher account: ' . $pubName . ' (Reason: ' . $reason . ')',
                    'times-circle'
                );
                echo json_encode([
                    'success' => true, 
                    'message' => 'Publisher rejected successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to reject publisher'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}

?>