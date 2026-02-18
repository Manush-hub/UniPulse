<?php

class UserPaymentgateway extends Controller {
    
    private $eventModel;
    
    public function __construct() {
        // Initialize event model
        $this->eventModel = new Event();
    }
    
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login if not authenticated
            header('Location: /unipulse/public/signin');
            exit;
        }
        
        // Get event ID from URL parameter
        $eventId = isset($_GET['event_id']) ? $_GET['event_id'] : null;
        
        $data = [];
        
        if (!$eventId) {
            $data['error'] = 'No event specified';
        } else {
            // Validate event exists and has paid tickets
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                $data['error'] = 'Event not found';
            } else if ($event->ticket_type === 'free-all' || $event->ticket_type === 'free-limited') {
                // Redirect back to event view if tickets are free
                header("Location: /unipulse/public/user/eventview?id={$eventId}");
                exit;
            } else {
                // Event is valid and has paid tickets
                $data['event'] = $event;
                $data['event_id'] = $eventId;
            }
        }
        
        // Load payment gateway view
        $this->view('User/paymentgateway', $data);
    }
    
    public function processPayment() {
        // This method will handle the actual payment processing
        // It should be called via AJAX from the payment form
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        // Get POST data
        $eventId = $_POST['event_id'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'card';
        $amount = $_POST['amount'] ?? 0;
        
        if (!$eventId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Event ID is required']);
            return;
        }
        
        // Validate event
        $event = $this->eventModel->getEventById($eventId);
        if (!$event) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Event not found']);
            return;
        }
        
        // TODO: Implement actual payment gateway integration
        // For now, we'll simulate a successful payment
        
        try {
            // Here you would:
            // 1. Validate payment details
            // 2. Process payment through payment gateway (Stripe, PayPal, etc.)
            // 3. Store transaction record in database
            // 4. Register user for the event
            // 5. Send confirmation email
            
            // Simulate successful payment
            $transactionId = 'TXN' . time() . rand(1000, 9999);
            
            // Store payment record (you'll need to create a payments table and model)
            // $paymentModel = new Payment();
            // $paymentModel->createPayment([
            //     'user_id' => $_SESSION['user_id'],
            //     'event_id' => $eventId,
            //     'amount' => $amount,
            //     'payment_method' => $paymentMethod,
            //     'transaction_id' => $transactionId,
            //     'status' => 'completed'
            // ]);
            
            // Register user for event after successful payment
            $registrationModel = new EventRegistration();
            $registrationModel->registerUser(
                $eventId,
                $_SESSION['user_id'],
                $_SESSION['user_type']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Payment successful',
                'transaction_id' => $transactionId
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ]);
        }
    }
}
