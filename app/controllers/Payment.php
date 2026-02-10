<?php

class Payment extends Controller {
    
    use Database;

    public function index($a = '', $b = '', $c = '') {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/signin');
            exit();
        }

        $data = [];
        
        // Handle POST request for payment processing
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validatePaymentData($_POST);
            
            if (empty($errors)) {
                // Process payment
                $paymentResult = $this->processPayment($_POST);
                
                if ($paymentResult['success']) {
                    $_SESSION['payment_success'] = "Payment processed successfully! Transaction ID: " . $paymentResult['transaction_id'];
                    $_SESSION['payment_date'] = $paymentResult['payment_date'];
                    $_SESSION['payment_time'] = $paymentResult['payment_time'];
                    $_SESSION['payment_completed_event_id'] = $_SESSION['payment_event_id'] ?? null;
                    header('Location: ' . ROOT . '/payment/success');
                    exit();
                } else {
                    // Store error and redirect to prevent form resubmission
                    $_SESSION['payment_errors'] = ['payment' => $paymentResult['message']];
                    $_SESSION['form_data'] = $_POST;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            } else {
                // Store errors and form data in session, then redirect
                $_SESSION['payment_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        
        // Retrieve errors and form data from session (for POST/Redirect/GET pattern)
        $data['errors'] = $_SESSION['payment_errors'] ?? [];
        $data['form_data'] = $_SESSION['form_data'] ?? [];
        $data['success'] = '';
        
        // Clear session data after retrieving
        unset($_SESSION['payment_errors']);
        unset($_SESSION['form_data']);
        
        // Get payment details from session or query parameters
        $data['amount'] = $_GET['amount'] ?? $_SESSION['payment_amount'] ?? '';
        $data['payment_type'] = $_GET['type'] ?? $_SESSION['payment_type'] ?? 'ticket'; // 'ticket' or 'boost'
        $data['event_id'] = $_GET['event_id'] ?? $_SESSION['payment_event_id'] ?? '';
        $data['publisher_id'] = $_GET['publisher_id'] ?? $_SESSION['payment_publisher_id'] ?? '';
        
        // Set item description based on payment type
        if ($data['payment_type'] === 'boost') {
            $data['item_description'] = 'Event Boost';
        } else {
            $data['item_description'] = $_GET['description'] ?? $_SESSION['payment_description'] ?? 'Event Ticket';
        }
        
        // Calculate commission for ticket sales only (5%)
        $data['commission_amount'] = 0;
        $data['organizer_amount'] = $data['amount'];
        
        if ($data['payment_type'] === 'ticket' && $data['amount'] > 0) {
            $data['commission_amount'] = round($data['amount'] * 0.05, 2); // 5% commission
            $data['organizer_amount'] = round($data['amount'] - $data['commission_amount'], 2);
        }
        
        $this->view('payment', $data);
    }
    
    public function success($a = '', $b = '', $c = '') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/signin');
            exit();
        }
        
        $data = [];
        $data['success_message'] = $_SESSION['payment_success'] ?? 'Payment completed successfully!';
        $data['payment_date'] = $_SESSION['payment_date'] ?? date('F j, Y');
        $data['payment_time'] = $_SESSION['payment_time'] ?? date('g:i A');
        $data['event_id'] = $_SESSION['payment_completed_event_id'] ?? null;
        
        unset($_SESSION['payment_success']);
        unset($_SESSION['payment_date']);
        unset($_SESSION['payment_time']);
        unset($_SESSION['payment_completed_event_id']);
        
        $this->view('payment_success', $data);
    }
    
    private function validatePaymentData($data) {
        $errors = [];
        
        // Validate payment method
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = 'Please select a payment method';
        }
        
        // Validate amount
        if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'Please enter a valid amount';
        }
        
        // If card payment, validate card details
        if (isset($data['payment_method']) && $data['payment_method'] === 'card') {
            if (empty($data['card_number']) || !preg_match('/^\d{16}$/', str_replace(' ', '', $data['card_number']))) {
                $errors['card_number'] = 'Please enter a valid 16-digit card number';
            }
            
            if (empty($data['card_name'])) {
                $errors['card_name'] = 'Please enter the cardholder name';
            }
            
            if (empty($data['expiry_date']) || !preg_match('/^\d{2}\/\d{2}$/', $data['expiry_date'])) {
                $errors['expiry_date'] = 'Please enter expiry date in MM/YY format';
            }
            
            if (empty($data['cvv']) || !preg_match('/^\d{3,4}$/', $data['cvv'])) {
                $errors['cvv'] = 'Please enter a valid CVV (3-4 digits)';
            }
        }
        
        return $errors;
    }
    
    private function processPayment($data) {
        // In a real application, this would integrate with a payment gateway API
        // like Stripe, PayPal, PayHere, or a local payment processor
        
        // Simulate payment processing
        $transactionId = 'TXN' . time() . rand(1000, 9999);
        
        // Calculate commission for tickets (5%), boost is 100% to UniPulse
        $paymentType = $_SESSION['payment_type'] ?? 'ticket';
        $amount = $data['amount'];
        $commissionAmount = 0;
        $organizerAmount = 0;
        
        if ($paymentType === 'ticket') {
            $commissionAmount = round($amount * 0.05, 2); // 5% commission
            $organizerAmount = round($amount - $commissionAmount, 2);
        } else {
            // Boost payment - 100% to UniPulse
            $commissionAmount = $amount;
            $organizerAmount = 0;
        }
        
        // Log payment to database
        
        try {
            $query = "INSERT INTO payments (
                user_id, user_type, amount, payment_method, payment_type, 
                transaction_id, status, event_id, publisher_id,
                commission_amount, organizer_amount, description, created_at
            ) VALUES (
                :user_id, :user_type, :amount, :payment_method, :payment_type,
                :transaction_id, :status, :event_id, :publisher_id,
                :commission_amount, :organizer_amount, :description, :created_at
            )";
            
            $params = [
                'user_id' => $_SESSION['user_id'],
                'user_type' => $_SESSION['user_type'] ?? 'user',
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'status' => 'completed',
                'event_id' => $_SESSION['payment_event_id'] ?? null,
                'publisher_id' => $_SESSION['payment_publisher_id'] ?? null,
                'commission_amount' => $commissionAmount,
                'organizer_amount' => $organizerAmount,
                'description' => $paymentType === 'boost' ? 'Event Boost' : 'Event Ticket',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->query($query, $params);
            
            // Clear payment session data
            unset($_SESSION['payment_amount']);
            unset($_SESSION['payment_type']);
            unset($_SESSION['payment_event_id']);
            unset($_SESSION['payment_publisher_id']);
            unset($_SESSION['payment_description']);
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'payment_date' => date('F j, Y'),
                'payment_time' => date('g:i A'),
                'message' => 'Payment processed successfully'
            ];
        } catch (Exception $e) {
            // Log error
            error_log("Payment processing error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ];
        }
    }
}
