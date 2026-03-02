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
        
        // Get payment details from GET params (initial visit) or session (returning after error)
        $data['amount']       = $_GET['amount']       ?? $_SESSION['payment_amount']       ?? '';
        $data['payment_type'] = $_GET['type']         ?? $_SESSION['payment_type']         ?? 'ticket';
        $data['event_id']     = $_GET['event_id']     ?? $_SESSION['payment_event_id']     ?? '';
        $data['publisher_id'] = $_GET['publisher_id'] ?? $_SESSION['payment_publisher_id'] ?? '';

        // Set item description based on payment type
        if ($data['payment_type'] === 'boost') {
            $data['item_description'] = 'Event Boost';
        } else {
            $data['item_description'] = $_GET['description'] ?? $_SESSION['payment_description'] ?? 'Event Ticket';
        }

        // ── Persist payment details to session so payhere() can access them ──
        // This is needed because clicking Pay Now is a new POST request with no GET params.
        if ($data['amount'] !== '')       $_SESSION['payment_amount']      = $data['amount'];
        if ($data['payment_type'])        $_SESSION['payment_type']        = $data['payment_type'];
        if ($data['event_id'] !== '')     $_SESSION['payment_event_id']    = $data['event_id'];
        if ($data['publisher_id'] !== '') $_SESSION['payment_publisher_id'] = $data['publisher_id'];
        $_SESSION['payment_description'] = $data['item_description'];
        
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
    
    // ─────────────────────────────────────────────────────────────────────────
    // PayHere Sandbox Integration
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Step 1 – Build the signed PayHere checkout form and auto-submit it.
     * Triggered by the "Pay with PayHere" button (POST /payment/payhere).
     */
    public function payhere($a = '', $b = '', $c = '') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/signin');
            exit();
        }

        // Read amount from session (set by index()) or POST fallback
        $lkrAmount = (float)($_SESSION['payment_amount'] ?? $_POST['amount'] ?? 0);

        if ($lkrAmount <= 0) {
            $_SESSION['payment_errors'] = ['payment' => 'Invalid payment amount.'];
            header('Location: ' . ROOT . '/payment');
            exit();
        }

        $paymentType = $_SESSION['payment_type']   ?? 'ticket';
        $items       = $paymentType === 'boost'
            ? 'UniPulse Event Boost'
            : ($_SESSION['payment_description'] ?? 'UniPulse Event Ticket');

        // Generate a unique order ID and store it so we can reference it on return
        $orderId = 'UNI-' . $_SESSION['user_id'] . '-' . time();
        $_SESSION['payhere_order_id'] = $orderId;
        $_SESSION['payhere_amount']   = $lkrAmount;

        // Build customer info from session (pre-fill PayHere form)
        $customer = [
            'first_name' => $_SESSION['user_first_name'] ?? $_SESSION['user_name'] ?? 'Customer',
            'last_name'  => $_SESSION['user_last_name']  ?? '',
            'email'      => $_SESSION['user_email']      ?? '',
            'phone'      => $_SESSION['user_phone']      ?? '0000000000',
            'address'    => 'N/A',
            'city'       => 'Colombo',
        ];

        $payhere = new PayHereService();
        $fields  = $payhere->buildCheckoutFields(
            $orderId,
            $lkrAmount,
            ROOT . '/payment/payherereturn',
            ROOT . '/payment/payherecancel',
            ROOT . '/payment/payherenotify',
            $customer,
            $items
        );

        // Pass event_id and publisher_id as custom fields so notify callback can store them
        $fields['custom_1'] = $_SESSION['payment_event_id']     ?? '';
        $fields['custom_2'] = $_SESSION['payment_publisher_id'] ?? '';

        // Render an auto-submitting form (user never sees it flash)
        $this->view('payhere_redirect', [
            'checkout_url' => $payhere->getCheckoutUrl(),
            'fields'       => $fields,
        ]);
        exit();
    }

    /**
     * Step 2a – PayHere server-to-server notification (notify_url).
     * Called by PayHere's servers — NOT by the browser.
     * We verify the signature and save the payment to the database.
     * URL: /payment/payherenotify  (must be publicly accessible in live mode)
     */
    public function payherenotify($a = '', $b = '', $c = '') {
        // PayHere POSTs to this URL server-to-server (no session)
        $payhere = new PayHereService();

        if (!$payhere->verifyNotification($_POST)) {
            error_log('[PayHere] Notify signature mismatch. Possible tampering.');
            http_response_code(400);
            echo 'Invalid signature';
            exit();
        }

        $statusCode  = (int)($_POST['status_code']      ?? 0);
        $orderId     = $_POST['order_id']               ?? '';
        $paymentId   = $_POST['payment_id']             ?? '';
        $amount      = (float)($_POST['payhere_amount'] ?? 0);

        // status_code: 2=success, 0=pending, -1=cancelled, -2=failed, -3=chargedback
        if ($statusCode === 2 && $orderId && $paymentId) {
            // Parse user_id from order ID: UNI-{user_id}-{timestamp}
            $parts  = explode('-', $orderId);
            $userId = $parts[1] ?? null;

            // Avoid duplicate entries
            $existing = $this->query(
                "SELECT id FROM payments WHERE transaction_id = :tid OR payhere_payment_id = :pid LIMIT 1",
                ['tid' => $paymentId, 'pid' => $paymentId]
            );
            if ($existing) {
                echo 'OK';
                exit();
            }

            // Determine payment type from order items or default to ticket
            $paymentType = 'ticket';
            $description = 'Event Ticket';
            $itemsDesc = $_POST['items'] ?? '';
            if (stripos($itemsDesc, 'boost') !== false) {
                $paymentType = 'boost';
                $description = 'Event Boost';
            }

            // Calculate commission
            $commissionAmount = 0;
            $organizerAmount  = 0;
            if ($paymentType === 'ticket') {
                $commissionAmount = round($amount * 0.05, 2);
                $organizerAmount  = round($amount - $commissionAmount, 2);
            } else {
                $commissionAmount = $amount;
                $organizerAmount  = 0;
            }

            $this->query(
                "INSERT INTO payments (
                    user_id, user_type, amount, payment_method, payment_type,
                    transaction_id, payhere_order_id, payhere_payment_id,
                    status, event_id, publisher_id,
                    commission_amount, organizer_amount, description, created_at
                ) VALUES (
                    :uid, :utype, :amt, 'payhere', :ptype,
                    :tid, :order_id, :payment_id,
                    'completed', :event_id, :publisher_id,
                    :commission, :organizer, :desc, :now
                )",
                [
                    'uid'          => $userId,
                    'utype'        => 'user',
                    'amt'          => $amount,
                    'ptype'        => $paymentType,
                    'tid'          => $paymentId,
                    'order_id'     => $orderId,
                    'payment_id'   => $paymentId,
                    'event_id'     => $_POST['custom_1'] ?? null,
                    'publisher_id' => $_POST['custom_2'] ?? null,
                    'commission'   => $commissionAmount,
                    'organizer'    => $organizerAmount,
                    'desc'         => $description,
                    'now'          => date('Y-m-d H:i:s'),
                ]
            );
        }

        echo 'OK';
        exit();
    }

    /**
     * Step 2b – PayHere redirects the user here after a successful payment.
     * URL: /payment/payherereturn
     * Note: For sandbox on localhost, payherenotify may not reach your server,
     * so we also save the record here as a fallback.
     */
    public function payherereturn($a = '', $b = '', $c = '') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/signin');
            exit();
        }

        $orderId     = $_SESSION['payhere_order_id'] ?? ('UNI-' . $_SESSION['user_id'] . '-unknown');
        $lkrAmount   = (float)($_SESSION['payhere_amount']   ?? $_SESSION['payment_amount'] ?? 0);
        $paymentType = $_SESSION['payment_type'] ?? 'ticket';
        $transId     = 'PH-' . strtoupper(substr(md5($orderId . time()), 0, 12));
        $completedEventId = $_SESSION['payment_event_id'] ?? null; // save before clearPaymentSession

        $commissionAmount = 0;
        $organizerAmount  = 0;
        if ($paymentType === 'ticket') {
            $commissionAmount = round($lkrAmount * 0.05, 2);
            $organizerAmount  = round($lkrAmount - $commissionAmount, 2);
        } else {
            $commissionAmount = $lkrAmount;
            $organizerAmount  = 0;
        }

        // Save payment (fallback — notify may have already saved it on live)
        $payhereOrderId = $_SESSION['payhere_order_id'] ?? $orderId;
        try {
            // Check if already saved by notify callback
            $existing = $this->query(
                "SELECT id FROM payments WHERE transaction_id = :tid OR payhere_order_id = :oid LIMIT 1",
                ['tid' => $transId, 'oid' => $payhereOrderId]
            );
            if (!$existing) {
                $quantity = $_SESSION['payment_quantity'] ?? 1;
                $this->query(
                    "INSERT INTO payments (
                        user_id, user_type, amount, quantity, payment_method, payment_type,
                        transaction_id, payhere_order_id, payhere_payment_id,
                        status, event_id, publisher_id,
                        commission_amount, organizer_amount, description, created_at
                    ) VALUES (
                        :user_id, :user_type, :amount, :quantity, 'payhere', :payment_type,
                        :transaction_id, :payhere_order_id, :payhere_payment_id,
                        'completed', :event_id, :publisher_id,
                        :commission_amount, :organizer_amount, :description, :created_at
                    )",
                    [
                        'user_id'            => $_SESSION['user_id'],
                        'user_type'          => $_SESSION['user_type'] ?? 'user',
                        'amount'             => $lkrAmount,
                        'quantity'           => $quantity,
                        'payment_type'       => $paymentType,
                        'transaction_id'     => $transId,
                        'payhere_order_id'   => $payhereOrderId,
                        'payhere_payment_id' => null, // Only available via notify callback
                        'event_id'           => $_SESSION['payment_event_id']     ?? null,
                        'publisher_id'       => $_SESSION['payment_publisher_id'] ?? null,
                        'commission_amount'  => $commissionAmount,
                        'organizer_amount'   => $organizerAmount,
                        'description'        => $paymentType === 'boost' ? 'Event Boost' : 'Event Ticket',
                        'created_at'         => date('Y-m-d H:i:s'),
                    ]
                );
            } else {
                // Update existing record with any missing info from session
                $existingId = is_object($existing[0]) ? $existing[0]->id : $existing[0]['id'];
                $this->query(
                    "UPDATE payments SET 
                        event_id = COALESCE(event_id, :event_id),
                        publisher_id = COALESCE(publisher_id, :publisher_id),
                        commission_amount = :commission_amount,
                        organizer_amount = :organizer_amount,
                        payhere_order_id = COALESCE(payhere_order_id, :payhere_order_id)
                    WHERE id = :id",
                    [
                        'event_id'          => $_SESSION['payment_event_id']     ?? null,
                        'publisher_id'      => $_SESSION['payment_publisher_id'] ?? null,
                        'commission_amount' => $commissionAmount,
                        'organizer_amount'  => $organizerAmount,
                        'payhere_order_id'  => $payhereOrderId,
                        'id'                => $existingId,
                    ]
                );
            }
        } catch (Exception $e) {
            error_log('[PayHere] DB insert failed: ' . $e->getMessage());
        }

        // If this is a boost payment, create the boost record
        if ($paymentType === 'boost') {
            error_log("PayHere Return - Detected boost payment, calling createBoostAfterPayment");
            error_log("PayHere Return - Session data: boost_event_id=" . ($_SESSION['boost_event_id'] ?? 'NULL') . ", boost_duration=" . ($_SESSION['boost_duration'] ?? 'NULL'));
            
            $boostCreated = $this->createBoostAfterPayment(
                $_SESSION['boost_event_id'] ?? $_SESSION['payment_event_id'],
                $_SESSION['boost_duration'],
                $lkrAmount,
                $_SESSION['boost_publisher_id'] ?? $_SESSION['payment_publisher_id'],
                $transId
            );
            
            if ($boostCreated) {
                error_log("PayHere Return - Boost created successfully!");
            } else {
                error_log("PayHere Return - Boost creation FAILED!");
            }
        }

        $this->clearPaymentSession();

        $_SESSION['payment_success']            = 'Payment completed via PayHere! Order: ' . $orderId;
        $_SESSION['payment_date']               = date('F j, Y');
        $_SESSION['payment_time']               = date('g:i A');
        $_SESSION['payment_completed_event_id'] = $completedEventId;

        unset($_SESSION['payhere_order_id'], $_SESSION['payhere_amount'], $_SESSION['payhere_event_id']);
        // Clear boost session variables
        unset($_SESSION['boost_event_id'], $_SESSION['boost_duration'], $_SESSION['boost_amount'], $_SESSION['boost_publisher_id']);

        header('Location: ' . ROOT . '/payment/success');
        exit();
    }

    /**
     * Step 3 (cancel path) – User clicked "Cancel" on PayHere's page.
     * URL: /payment/payherecancel
     */
    public function payherecancel($a = '', $b = '', $c = '') {
        unset($_SESSION['payhere_order_id'], $_SESSION['payhere_amount'], $_SESSION['payhere_event_id']);
        $this->view('payhere_cancel', []);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function clearPaymentSession() {
        unset(
            $_SESSION['payment_amount'],
            $_SESSION['payment_type'],
            $_SESSION['payment_event_id'],
            $_SESSION['payment_publisher_id'],
            $_SESSION['payment_description'],
            $_SESSION['payment_quantity']
        );
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
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : ($_SESSION['payment_quantity'] ?? 1);

        try {
            $query = "INSERT INTO payments (
                user_id, user_type, amount, quantity, payment_method, payment_type, 
                transaction_id, status, event_id, publisher_id,
                commission_amount, organizer_amount, description, created_at
            ) VALUES (
                :user_id, :user_type, :amount, :quantity, :payment_method, :payment_type,
                :transaction_id, :status, :event_id, :publisher_id,
                :commission_amount, :organizer_amount, :description, :created_at
            )";
            
            $params = [
                'user_id' => $_SESSION['user_id'],
                'user_type' => $_SESSION['user_type'] ?? 'user',
                'amount' => $amount,
                'quantity' => $quantity,
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
            unset($_SESSION['payment_quantity']);
            
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
    
    /**
     * Create boost record after successful PayHere payment
     */
    private function createBoostAfterPayment($eventId, $durationDays, $amount, $publisherId, $transactionId) {
        try {
            error_log("createBoostAfterPayment called - eventId: $eventId, duration: $durationDays, amount: $amount, publisherId: $publisherId, transactionId: $transactionId");
            
            if (!$eventId || !$durationDays || !$amount || !$publisherId) {
                error_log("Boost creation failed: Missing parameters - eventId=$eventId, duration=$durationDays, amount=$amount, publisherId=$publisherId");
                return false;
            }
            
            // Verify event exists
            $eventQuery = "SELECT * FROM events WHERE id = :event_id AND is_deleted = 0";
            $events = $this->query($eventQuery, ['event_id' => $eventId]);
            
            error_log("createBoostAfterPayment - Event verification returned " . count($events) . " events");
            
            if (empty($events)) {
                error_log("Boost creation failed: Event not found - eventId=$eventId");
                return false;
            }

            // Calculate boost dates
            $startDate = new DateTime();
            $endDate = clone $startDate;
            $endDate->modify("+{$durationDays} days");
            
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
                'payment_method' => 'payhere',
                'transaction_id' => $transactionId
            ]);
            
            error_log("createBoostAfterPayment - Boost record inserted successfully with transaction ID: $transactionId");
            
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
            
            error_log("createBoostAfterPayment - Event {$eventId} is now boosted! Expires at: " . $endDate->format('Y-m-d H:i:s'));
            return true;
            
        } catch (Exception $e) {
            error_log("Error creating boost: " . $e->getMessage());
            return false;
        }
    }
}
