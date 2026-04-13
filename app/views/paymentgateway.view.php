<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Gateway</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="/unipulse/public/assets/css/paymentgateway-style.css">
</head>

<body class="payment-gateway-body">
    <?php
    // include the user header component (correct relative path from this file)
    include __DIR__ . '/User/components/header.php';
    ?>
    <div class="payment-gateway-wrapper">
        <div class="payment-gateway-container">
            <div class="payment-gateway-header">
                <h1>Secure Checkout</h1>
                <div class="payment-progress-bar" data-step="1">
                    <div class="payment-step active">
                        <div class="payment-step-circle">1</div>
                        <div class="payment-step-label">Tickets</div>
                    </div>
                    <div class="payment-step">
                        <div class="payment-step-circle">2</div>
                        <div class="payment-step-label">Payment</div>
                    </div>
                    <div class="payment-step">
                        <div class="payment-step-circle">3</div>
                        <div class="payment-step-label">Complete</div>
                    </div>
                </div>
            </div>

            <div class="payment-content">
                <!-- Step 1: Ticket Selection -->
                <div class="payment-form-step active" data-step="1">
                    <h2 style="margin-bottom: 20px;">Select Tickets</h2>

                    <?php
                    // Get ticket price from event data
                    $eventTicketPrice = 0;
                    if (isset($data['event']) && isset($data['event']->ticket_types)) {
                        $ticketTypes = $data['event']->ticket_types;
                        if (is_string($ticketTypes)) {
                            $ticketTypes = json_decode($ticketTypes, true);
                        }
                        if (is_array($ticketTypes) && !empty($ticketTypes)) {
                            $firstTicket = $ticketTypes[0];
                            $eventTicketPrice = $firstTicket['discounted_price'] ?? $firstTicket['price'] ?? 0;
                        }
                    }
                    ?>
                    <input type="hidden" id="eventId" value="<?= htmlspecialchars($data['event_id'] ?? '') ?>">
                    <input type="hidden" id="publisherId" value="<?= htmlspecialchars($data['event']->created_by ?? '') ?>">
                    <input type="hidden" id="eventTitle" value="<?= htmlspecialchars($data['event']->title ?? 'Event') ?>">

                    <div class="payment-form-group">
                        <label class="payment-label">Ticket Quantity</label>
                        <div class="payment-ticket-controls">
                            <div class="payment-ticket-quantity">
                                <button type="button" onclick="decreaseQuantity()">-</button>
                                <input type="number" id="quantity" value="1" min="1" max="10" readonly>
                                <button type="button" onclick="increaseQuantity()">+</button>
                            </div>
                            <div class="payment-ticket-price">
                                Rs <span id="ticketPrice"><?= number_format($eventTicketPrice, 2) ?></span> each
                            </div>
                        </div>
                    </div>

                    <div class="payment-order-summary">
                        <h3 style="margin-bottom: 15px;">Order Summary</h3>
                        <div class="payment-summary-row">
                            <span>Ticket Price:</span>
                            <span>Rs <span id="displayPrice"><?= number_format($eventTicketPrice, 2) ?></span></span>
                        </div>
                        <div class="payment-summary-row">
                            <span>Quantity:</span>
                            <span id="displayQuantity">1</span>
                        </div>
                        <div class="payment-summary-row total">
                            <span>Total:</span>
                            <span>Rs <span id="totalPrice"><?= number_format($eventTicketPrice, 2) ?></span></span>
                        </div>
                    </div>

                    <div class="payment-button-group">
                        <button class="payment-button payment-btn-primary" onclick="nextStep(1)">Continue to Payment</button>
                    </div>
                </div>

                <!-- Step 2: Payment Information -->
                <div class="payment-form-step" data-step="2">
                    <h2 style="margin-bottom: 20px;">Payment Details</h2>

                    <div class="payment-order-summary">
                        <h3 style="margin-bottom: 15px;">Order Summary</h3>
                        <div class="payment-summary-row">
                            <span>Quantity:</span>
                            <span id="summaryQuantity">1</span>
                        </div>
                        <div class="payment-summary-row">
                            <span>Ticket Price:</span>
                            <span>Rs <span id="summaryPrice"><?= number_format($eventTicketPrice, 2) ?></span></span>
                        </div>
                        <div class="payment-summary-row total">
                            <span>Total:</span>
                            <span>Rs <span id="summaryTotal"><?= number_format($eventTicketPrice, 2) ?></span></span>
                        </div>
                    </div>

                    <label class="payment-label" style="margin-bottom: 15px;">Select Payment Method</label>
                    <div class="payment-methods-container">
                        <div class="payment-method-option selected" onclick="selectPayment(this, 'card')">
                            <div style="font-size: 30px; margin-bottom: 10px;">💳</div>
                            <div>Credit Card</div>
                        </div>
                        <div class="payment-method-option" onclick="selectPayment(this, 'debit')">
                            <div style="font-size: 30px; margin-bottom: 10px;">🏦</div>
                            <div>Debit Card</div>
                        </div>
                        <div class="payment-method-option" onclick="selectPayment(this, 'paypal')">
                            <div style="font-size: 30px; margin-bottom: 10px;">💰</div>
                            <div>PayPal</div>
                        </div>
                        <div class="payment-method-option" onclick="selectPayment(this, 'slip')">
                            <div style="font-size: 30px; margin-bottom: 10px;">📄</div>
                            <div>Payment Slip</div>
                        </div>
                    </div>

                    <!-- Card Payment Form -->
                    <div id="card-payment-form">
                        <div class="payment-form-group">
                            <label class="payment-label" for="cardnumber">Card Number *</label>
                            <input class="payment-input" type="text" id="cardnumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>
                        <div class="payment-form-group">
                            <label class="payment-label" for="cardname">Cardholder Name *</label>
                            <input class="payment-input" type="text" id="cardname" placeholder="JOHN DOE" required>
                        </div>
                        <div class="payment-input-row">
                            <div class="payment-form-group">
                                <label class="payment-label" for="expiry">Expiry Date *</label>
                                <input class="payment-input" type="text" id="expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="payment-form-group">
                                <label class="payment-label" for="cvv">CVV *</label>
                                <input class="payment-input" type="text" id="cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Slip Upload -->
                    <div id="slip-payment-form" style="display: none;">
                        <div class="payment-file-upload" id="slipUpload" onclick="document.getElementById('slipFile').click()">
                            <div class="payment-file-upload-icon">📄</div>
                            <div>Click to upload payment slip</div>
                            <div style="font-size: 12px; color: #666; margin-top: 5px;">Supported formats: JPG, PNG, PDF
                            </div>
                            <div class="payment-file-name" id="fileName"></div>
                        </div>
                        <input type="file" id="slipFile" accept=".jpg,.jpeg,.png,.pdf" style="display: none;"
                            onchange="handleFileSelect(this)">

                        <div class="payment-pending-status">
                            <p><strong>Pending Confirmation</strong></p>
                            <p>Your tickets will be confirmed after we verify your payment slip.</p>
                            <p>This usually takes 1-2 business days.</p>
                        </div>
                    </div>

                    <div class="payment-button-group">
                        <button class="payment-button payment-btn-secondary" onclick="prevStep(2)">Back</button>
                        <button class="payment-button payment-btn-primary" id="paymentButton" onclick="processPayment()">Complete Payment</button>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div class="payment-form-step" data-step="3">
                    <div class="payment-success-icon">✓</div>
                    <div class="payment-success-message">
                        <h2 id="confirmationTitle">Payment Successful!</h2>
                        <p style="margin: 20px 0;">Thank you for your purchase.</p>
                        <p>Ticket ID: #<span id="orderId"></span></p>
                        <p id="confirmationMessage">A confirmation email has been sent to your email address.</p>

                        <!-- Barcode Section -->
                        <div class="payment-barcode-container">
                            <div class="payment-barcode-title">Your Ticket Barcode</div>
                            <div class="payment-barcode-id">ID: #<span id="barcodeOrderId"></span></div>
                            <svg class="payment-barcode" id="barcode"></svg>
                            <button class="payment-download-btn" onclick="downloadBarcode()">
                                <span>Download Barcode</span>
                            </button>
                        </div>

                        <button class="payment-button payment-btn-primary" onclick="resetForm()" style="width: 100%; margin-top: 20px;">Make
                            Another Purchase</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.paymentGatewayConfig = {
            ticketPrice: <?= (float)$eventTicketPrice ?>,
            processPaymentUrl: '<?= ROOT ?>/user/paymentgateway/processPayment'
        };
    </script>
    <script src="<?php echo ROOT ?>/assets/js/extracted/paymentgateway.js"></script>
    <?php
    // include the global footer (actual file is app/views/footer.php)
    include __DIR__ . '/footer.php';
    ?>
</body>

</html>