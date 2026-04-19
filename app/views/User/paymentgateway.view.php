<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/eventview-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/User/paymentgateway-style.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Payment Container -->
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-header">
                <h1><i class="fas fa-credit-card"></i> Secure Payment</h1>
                <p>Complete your ticket purchase via PayHere</p>
            </div>

            <div id="errorMessage" class="error-message"></div>
            <div id="successMessage" class="success-message"></div>

            <!-- Event Summary -->
            <div class="event-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Event Name:</span>
                    <span id="eventName">Loading...</span>
                </div>
                <div class="summary-row">
                    <span>Event Date:</span>
                    <span id="eventDate">Loading...</span>
                </div>
                <div class="summary-row">
                    <span>Ticket Type:</span>
                    <span id="ticketType">Loading...</span>
                </div>
                <div class="summary-row">
                    <span>Quantity:</span>
                    <span id="ticketQuantity">1</span>
                </div>
                <div class="summary-row" id="savingsRow" style="display: none;">
                    <span style="color: #15803d; font-weight: 600;">You saved:</span>
                    <span id="savingsAmount" style="color: #15803d; font-weight: 700;">LKR 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Total Amount:</span>
                    <span id="totalAmount">LKR 0.00</span>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="paymentForm">
                <h3 style="margin-bottom: 20px; color: #2c3e50;">
                    <i class="fas fa-wallet"></i> Payment Method
                </h3>

                <div class="payment-methods">
                    <div class="payment-method active" data-method="payhere" style="cursor: default; background: #f0f9ff; border: 2px solid #00457C;">
                        <img src="https://www.payhere.lk/downloads/images/payhere_short_logo.png"
                            alt="PayHere"
                            style="height: 24px; vertical-align: middle;"
                            onerror="this.style.display='none'">
                        <span style="margin-left: 10px; color: #00457C; font-weight: 600;">PayHere</span>
                    </div>
                </div>

                <div style="margin-top: 20px; background: #f0f9ff; border: 1px solid #bae6fd;
                            border-radius: 10px; padding: 14px 16px; font-size: 13px;">
                    <p style="margin: 0 0 8px 0; color: #0369a1;">
                        <i class="fas fa-info-circle"></i>
                        <strong>You'll be redirected to PayHere's secure checkout</strong>
                    </p>
                    <p style="margin: 0; color: #6b7280; font-size: 12px;">
                        <i class="fas fa-credit-card"></i> Visa, Mastercard, Amex, Internet Banking &amp; more accepted
                    </p>
                    <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">
                        <i class="fas fa-flask"></i> <strong>Test card:</strong> 4111 1111 1111 1111 | OTP: 123456
                    </p>
                </div>

                <div class="secure-notice">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure payment processed via PayHere - Sri Lanka's trusted payment gateway</span>
                </div>

                <div class="btn-container">
                    <a href="#" id="cancelBtn" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Proceed to PayHere
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/User/paymentgateway-app.js"></script>
</body>

</html>