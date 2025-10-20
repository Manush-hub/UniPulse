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
            
            $result = $publisherModel->approve($publisherId, $moderatorData['id']);
            
            if ($result) {
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
            $result = $publisherModel->reject($publisherId, $moderatorData['id'], $reason);
            
            if ($result) {
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