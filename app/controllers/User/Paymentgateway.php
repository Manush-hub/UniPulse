<?php

class UserPaymentgateway extends Controller
{

    use Database;

    private $eventModel;

    public function __construct()
    {
        // Initialize event model
        $this->eventModel = new Event();
    }

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login if not authenticated
            header('Location: /unipulse/public/signin');
            exit;
        }

        // Only allow regular users (university/public) to purchase tickets
        // Publishers and sponsors cannot buy tickets
        $userType = $_SESSION['user_type'] ?? 'user';
        if ($userType === 'publisher' || $userType === 'sponsor') {
            $_SESSION['error_message'] = 'Only university and public users can purchase tickets. Publishers and sponsors cannot buy tickets.';
            $eventId = $_GET['event_id'] ?? null;
            if ($eventId) {
                header("Location: /unipulse/public/{$userType}/eventview?id={$eventId}");
            } else {
                header("Location: /unipulse/public/{$userType}/dashboard");
            }
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

    public function processPayment()
    {
        // This method will handle the actual payment processing
        // It should be called via AJAX from the payment form

        error_log('ProcessPayment called - Method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Session user_id: ' . ($_SESSION['user_id'] ?? 'NOT SET'));

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
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            // Calculate commission (5% for ticket sales)
            $commissionAmount = round($amount * 0.05, 2);
            $organizerAmount = round($amount - $commissionAmount, 2);

            // Store payment record in the database
            $query = "INSERT INTO payments (
                user_id, user_type, amount, quantity, payment_method, payment_type,
                transaction_id, status, event_id, publisher_id,
                commission_amount, organizer_amount, description, created_at
            ) VALUES (
                :user_id, :user_type, :amount, :quantity, :payment_method, 'ticket',
                :transaction_id, 'completed', :event_id, :publisher_id,
                :commission_amount, :organizer_amount, :description, :created_at
            )";

            $params = [
                'user_id'           => $_SESSION['user_id'],
                'user_type'         => $_SESSION['user_type'] ?? 'user',
                'amount'            => $amount,
                'quantity'          => $quantity,
                'payment_method'    => $paymentMethod,
                'transaction_id'    => $transactionId,
                'event_id'          => $eventId,
                'publisher_id'      => $event->created_by ?? null,
                'commission_amount' => $commissionAmount,
                'organizer_amount'  => $organizerAmount,
                'description'       => 'Event Ticket - ' . ($event->title ?? 'Event'),
                'created_at'        => date('Y-m-d H:i:s'),
            ];

            error_log('About to insert payment: ' . print_r($params, true));

            // Try to insert the payment
            try {
                $result = $this->query($query, $params);
                error_log('Insert result: ' . ($result ? 'SUCCESS' : 'FAILED/EMPTY'));

                // Verify the insert by checking if we can find it
                $checkQuery = "SELECT id FROM payments WHERE transaction_id = :tid LIMIT 1";
                $checkResult = $this->query($checkQuery, ['tid' => $transactionId]);

                if (!$checkResult) {
                    throw new Exception('Payment record was not inserted into database');
                }

                error_log('Payment verified in database! Transaction ID: ' . $transactionId);
            } catch (Exception $e) {
                error_log('Database error during payment insert: ' . $e->getMessage());
                throw $e;
            }

            // Register user for event after successful payment
            try {
                $registrationModel = new EventRegistration();
                $wasRegistered = $registrationModel->isUserRegistered(
                    $eventId,
                    $_SESSION['user_id'],
                    $_SESSION['user_type'] ?? 'public'
                );

                $registrationModel->ensurePaidRegistration([
                    'event_id' => (int)$eventId,
                    'user_id' => (int)$_SESSION['user_id'],
                    'user_type' => $_SESSION['user_type'] ?? 'public',
                    'registration_type' => 'paid',
                    'amount_paid' => (float)$amount,
                    'payment_id' => $transactionId,
                    'notes' => 'Auto-registered after successful user payment gateway payment'
                ]);

                // Increment participants only when this is a new active registration
                if (!$wasRegistered) {
                    $this->eventModel->incrementParticipants($eventId);
                }

                try {
                    $paidRegistrationModel = new PaidEventRegistration();
                    $userType = $_SESSION['user_type'] ?? 'public';
                    $normalizedUserType = strtolower(trim((string)$userType));
                    if ($normalizedUserType === 'user' || $normalizedUserType === 'public_user' || $normalizedUserType === 'publicuser') {
                        $normalizedUserType = 'public';
                    }
                    if ($normalizedUserType === 'student' || $normalizedUserType === 'university_user' || $normalizedUserType === 'universityuser') {
                        $normalizedUserType = 'university';
                    }

                    $ticketQuantity = max(1, (int)$quantity);
                    $totalAmount = (float)$amount;
                    $unitPrice = round($totalAmount / $ticketQuantity, 2);

                    $paidRegistrationModel->upsertByPaymentReference([
                        'event_id' => (int)$eventId,
                        'publisher_id' => (isset($event->created_by_type) && $event->created_by_type === 'publisher' && isset($event->created_by)) ? (int)$event->created_by : null,
                        'event_title_snapshot' => (string)($event->title ?? ''),
                        'publisher_name_snapshot' => (string)($event->organizer_name ?? $event->organizer ?? ''),
                        'registered_user_id' => (int)$_SESSION['user_id'],
                        'registered_user_type' => $normalizedUserType,
                        'registered_user_name_snapshot' => (string)($_SESSION['user_name'] ?? ''),
                        'registered_user_email_snapshot' => (string)($_SESSION['user_email'] ?? ''),
                        'order_number' => substr('ORD-' . $eventId . '-' . $_SESSION['user_id'] . '-' . substr(md5($transactionId), 0, 8), 0, 40),
                        'ticket_tier_name' => 'General',
                        'ticket_quantity' => $ticketQuantity,
                        'unit_price' => $unitPrice,
                        'currency_code' => 'LKR',
                        'subtotal_amount' => $totalAmount,
                        'discount_amount' => 0.00,
                        'service_fee_amount' => 0.00,
                        'tax_amount' => 0.00,
                        'total_amount' => $totalAmount,
                        'payment_status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'payment_transaction_id' => substr((string)$transactionId, 0, 100),
                        'payment_gateway' => $paymentMethod === 'payhere' ? 'payhere' : null,
                        'paid_at' => date('Y-m-d H:i:s'),
                        'registration_status' => 'confirmed',
                        'registration_source' => 'web',
                        'metadata' => [
                            'source' => 'user_paymentgateway_process_payment',
                            'payment_type' => 'ticket'
                        ]
                    ]);
                } catch (Throwable $paidSyncError) {
                    error_log('Paid registration sync failed in UserPaymentgateway::processPayment: ' . $paidSyncError->getMessage());
                }

                error_log('User registered for event successfully');
            } catch (Exception $e) {
                error_log('Event registration failed but payment recorded: ' . $e->getMessage());
                // Don't throw - payment is already recorded
            }

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
