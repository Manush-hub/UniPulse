<?php

class SponsorDonations extends Controller {
    
    private $donationModel;
    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->donationModel = new Donation();
        $this->eventModel = new Event();
    }
    
    // View donation history
    public function index() {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $donations = $this->donationModel->getBySponsorId($currentUser['id']);
        
        $data = [
            'user' => $currentUser,
            'donations' => $donations,
            'total_donated' => $this->donationModel->getTotalBySponsor($currentUser['id']),
            'page_title' => 'My Donations'
        ];
        
        $this->view('Sponsor/donations', $data);
    }
    
    // Make a donation
    public function create($eventId = null) {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donationData = [
                'event_id' => $_POST['event_id'],
                'sponsor_id' => $currentUser['id'],
                'amount' => $_POST['amount'],
                'donation_type' => $_POST['type'] ?? 'monetary', // monetary or in-kind
                'message' => $_POST['message'] ?? '',
                'anonymous' => isset($_POST['anonymous']) ? 1 : 0
            ];
            
            // For in-kind donations
            if ($donationData['donation_type'] === 'in-kind') {
                $donationData['item_description'] = $_POST['item_description'];
                $donationData['item_value'] = $_POST['item_value'];
                $donationData['status'] = 'pending'; // Needs organizer approval
            } else {
                // Process payment (PayHere integration)
                $paymentResult = $this->processPayment($donationData['amount']);
                
                if (!$paymentResult['success']) {
                    echo json_encode(['success' => false, 'error' => 'Payment failed']);
                    exit();
                }
                
                $donationData['payment_id'] = $paymentResult['payment_id'];
                $donationData['status'] = 'completed';
            }
            
            if ($this->donationModel->create($donationData)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Donation submitted successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to process donation']);
            }
        }
        exit();
    }
    
    private function processPayment($amount) {
        // PayHere integration logic
        // Return ['success' => true/false, 'payment_id' => '...']
    }
}