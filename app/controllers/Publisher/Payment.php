<?php

class PublisherPayment extends Controller {
    
    use Database;
    
    public function index() {
        // Check if publisher is logged in
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: ' . ROOT . '/signin');
            exit();
        }

        $data = [];
        
        // Handle POST request for payment processing
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validatePaymentData($_POST);
            
            if (empty($errors)) {
                // Store boost details in session for processing after payment
                $_SESSION['boost_event_id'] = $_GET['event_id'] ?? $_POST['event_id'] ?? null;
                $_SESSION['boost_duration'] = $_GET['duration'] ?? $_POST['duration'] ?? null;
                $_SESSION['boost_amount'] = $_POST['amount'];
                $_SESSION['boost_publisher_id'] = $currentUser['id'];
                
                // Process payment
                $paymentResult = $this->processPayment($_POST);
                
                if ($paymentResult['success']) {
                    // Create the boost after successful payment
                    $boostResult = $this->createBoost();
                    
                    if ($boostResult['success']) {
                        $_SESSION['payment_success'] = "Payment successful! Your event has been boosted.";
                        $_SESSION['payment_date'] = date('F j, Y');
                        $_SESSION['payment_time'] = date('g:i A');
                        $_SESSION['payment_completed_event_id'] = $_SESSION['boost_event_id'] ?? null;
                        
                        // Clear boost session data
                        unset($_SESSION['boost_event_id']);
                        unset($_SESSION['boost_duration']);
                        unset($_SESSION['boost_amount']);
                        unset($_SESSION['boost_publisher_id']);
                        
                        header('Location: ' . ROOT . '/publisher/payment/success');
                        exit();
                    } else {
                        $_SESSION['payment_errors'] = ['payment' => 'Payment successful but boost activation failed. Please contact support.'];
                        $_SESSION['form_data'] = $_POST;
                        header('Location: ' . $_SERVER['REQUEST_URI']);
                        exit();
                    }
                } else {
                    $_SESSION['payment_errors'] = ['payment' => $paymentResult['message']];
                    $_SESSION['form_data'] = $_POST;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            } else {
                $_SESSION['payment_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        
        // Retrieve errors and form data from session
        $data['errors'] = $_SESSION['payment_errors'] ?? [];
        $data['form_data'] = $_SESSION['form_data'] ?? [];
        
        // Clear session data after retrieving
        unset($_SESSION['payment_errors']);
        unset($_SESSION['form_data']);
        
        // Get payment details from query parameters
        $data['amount'] = $_GET['amount'] ?? '';
        $data['payment_type'] = 'boost';
        $data['event_id'] = $_GET['event_id'] ?? '';
        $data['duration'] = $_GET['duration'] ?? '';
        $data['item_description'] = $_GET['description'] ?? 'Event Boost';
        
        $this->view('payment', $data);
    }
    
    public function success() {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
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
        
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = 'Please select a payment method';
        }
        
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
        // Simulate payment processing
        $transactionId = 'BOOST-' . time() . rand(1000, 9999);
        
        $amount = $data['amount'];
        
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
                'user_type' => $_SESSION['user_type'] ?? 'publisher',
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
                'payment_type' => 'boost',
                'transaction_id' => $transactionId,
                'status' => 'completed',
                'event_id' => $_SESSION['boost_event_id'] ?? null,
                'publisher_id' => $_SESSION['boost_publisher_id'] ?? null,
                'commission_amount' => $amount, // 100% to UniPulse for boosts
                'organizer_amount' => 0,
                'description' => 'Event Boost',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->query($query, $params);
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment processed successfully'
            ];
        } catch (Exception $e) {
            error_log("Payment processing error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ];
        }
    }
    
    private function createBoost() {
        try {
            $eventId = $_SESSION['boost_event_id'];
            $durationDays = $_SESSION['boost_duration'];
            $amount = $_SESSION['boost_amount'];
            $publisherId = $_SESSION['boost_publisher_id'];
            
            if (!$eventId || !$durationDays || !$amount || !$publisherId) {
                return ['success' => false, 'error' => 'Missing boost parameters'];
            }
            
            // Verify event belongs to publisher
            $eventQuery = "SELECT * FROM events WHERE id = :event_id AND created_by = :publisher_id AND created_by_type = 'publisher' AND is_deleted = 0";
            $events = $this->query($eventQuery, [
                'event_id' => $eventId,
                'publisher_id' => $publisherId
            ]);
            
            if (empty($events)) {
                return ['success' => false, 'error' => 'Event not found or unauthorized'];
            }

            // Calculate boost dates
            $startDate = new DateTime();
            $endDate = clone $startDate;
            $endDate->modify("+{$durationDays} days");
            
            // Generate transaction ID
            $transactionId = 'BOOST-' . time() . '-' . rand(1000, 9999);
            
            // Insert boost record
            $insertQuery = "
                INSERT INTO event_boosts 
                (event_id, publisher_id, boost_start_date, boost_end_date, duration_days, 
                 amount_paid, payment_method, transaction_id, payment_status, boost_status, priority_level)
                VALUES 
                (:event_id, :publisher_id, :start_date, :end_date, :duration_days,
                 :amount, :payment_method, :transaction_id, 'completed', 'active', 1)
            ";
            
            $this->query($insertQuery, [
                'event_id' => $eventId,
                'publisher_id' => $publisherId,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s'),
                'duration_days' => $durationDays,
                'amount' => $amount,
                'payment_method' => 'card',
                'transaction_id' => $transactionId
            ]);
            
            // Update event boost status
            $updateEventQuery = "
                UPDATE events 
                SET is_boosted = 1, 
                    boost_expires_at = :expires_at,
                    boost_priority = 1
                WHERE id = :event_id
            ";
            
            $this->query($updateEventQuery, [
                'event_id' => $eventId,
                'expires_at' => $endDate->format('Y-m-d H:i:s')
            ]);
            
            return [
                'success' => true,
                'message' => 'Event boosted successfully!',
                'transaction_id' => $transactionId
            ];
            
        } catch (Exception $e) {
            error_log("Error creating boost: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create boost: ' . $e->getMessage()];
        }
    }
}
