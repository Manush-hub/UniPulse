<?php

class PublisherSponsors extends Controller {
    
    public function index($a = '', $b = '', $c = '') {
        // Require publisher authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsor = new Sponsor();
        
        // Get all sponsors
        $sponsors = $sponsor->getAllSponsors();
        $stats = $sponsor->getSponsorStats();
        
        // Prepare data for view
        $data = [
            'user' => $currentUser,
            'sponsors' => $sponsors,
            'stats' => $stats,
            'page_title' => 'Current Sponsors'
        ];
        
        parent::view('Publisher/sponsors', $data);
    }
    
    public function details($sponsorId = '') {
        // Require publisher authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($sponsorId)) {
            header('Location: /unipulse/public/publisher/sponsors');
            exit();
        }
        
        $sponsor = new Sponsor();
        $sponsorData = $sponsor->getSponsorById($sponsorId);
        
        if (!$sponsorData) {
            // Sponsor not found, redirect back
            header('Location: /unipulse/public/publisher/sponsors');
            exit();
        }
        
        // Prepare data for view
        $data = [
            'user' => $currentUser,
            'sponsor' => $sponsorData,
            'page_title' => 'Sponsor Details - ' . $sponsorData['company_name']
        ];
        
        parent::view('Publisher/sponsor-details', $data);
    }
    
    public function contact($a = '', $b = '', $c = '') {
        // Clean any output buffer completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Suppress any potential PHP warnings/notices that might interfere with JSON
        error_reporting(E_ERROR | E_PARSE);
        
        // Set headers before any output
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                $this->sendJsonResponse(false, 'Unauthorized');
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendJsonResponse(false, 'Invalid request method');
                return;
            }
            
            // Get form data
            $sponsorId = $_POST['sponsor_id'] ?? '';
            $subject = trim($_POST['subject'] ?? '');
            $messageContent = trim($_POST['message'] ?? '');
            
            // Validation
            if (empty($sponsorId) || empty($subject) || empty($messageContent)) {
                $this->sendJsonResponse(false, 'All fields are required');
                return;
            }
            
            // Verify sponsor exists
            $sponsor = new Sponsor();
            $sponsorData = $sponsor->getSponsorById($sponsorId);
            
            if (!$sponsorData) {
                $this->sendJsonResponse(false, 'Sponsor not found');
                return;
            }
            
            // Send message
            $message = new Message();
            $messageData = [
                'from_user_id' => $currentUser['id'],
                'from_user_type' => 'publisher',
                'to_user_id' => $sponsorId,
                'to_user_type' => 'sponsor',
                'subject' => $subject,
                'message' => $messageContent
            ];
            
            $messageId = $message->sendMessage($messageData);
            
            if ($messageId) {
                $this->sendJsonResponse(true, 'Message sent successfully!');
            } else {
                $this->sendJsonResponse(false, 'Failed to send message. Please try again.');
            }
            
        } catch (Exception $e) {
            error_log('Exception in contact method: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'An error occurred while sending the message.');
        }
    }
    
    private function sendJsonResponse($success, $message) {
        // Clean any remaining output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Ensure clean JSON output
        $response = [
            'success' => (bool)$success, 
            'message' => (string)$message
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }
}