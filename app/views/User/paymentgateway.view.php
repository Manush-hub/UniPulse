<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/eventview-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .payment-header p {
            color: #7f8c8d;
        }

        .event-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .event-summary h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .payment-method.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .payment-method i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #667eea;
        }

        .payment-method span {
            display: block;
            color: #2c3e50;
            font-weight: 500;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .secure-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #d4edda;
            border-radius: 6px;
            color: #155724;
            margin-top: 20px;
        }

        .secure-notice i {
            font-size: 20px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
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
                <h1><i class="fas fa-credit-card"></i> Payment Gateway</h1>
                <p>Complete your ticket purchase</p>
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
                    <span>1</span>
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
                    <div class="payment-method active" data-method="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Credit Card</span>
                    </div>
                    <div class="payment-method" data-method="paypal">
                        <i class="fab fa-paypal"></i>
                        <span>PayPal</span>
                    </div>
                    <div class="payment-method" data-method="bank">
                        <i class="fas fa-university"></i>
                        <span>Bank Transfer</span>
                    </div>
                </div>

                <div id="cardPaymentFields">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">
                        <i class="fas fa-user"></i> Billing Information
                    </h3>

                    <div class="form-group">
                        <label for="cardName">
                            <i class="fas fa-user"></i> Cardholder Name
                        </label>
                        <input type="text" id="cardName" name="cardName" placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="cardNumber">
                            <i class="fas fa-credit-card"></i> Card Number
                        </label>
                        <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">
                                <i class="fas fa-calendar"></i> Expiry Date
                            </label>
                            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">
                                <i class="fas fa-lock"></i> CVV
                            </label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" id="email" name="email" placeholder="john.doe@example.com" required>
                    </div>
                </div>

                <div class="secure-notice">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your payment information is encrypted and secure</span>
                </div>

                <div class="btn-container">
                    <a href="#" id="cancelBtn" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get event ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const eventId = urlParams.get('event_id');

        // Load event details
        document.addEventListener('DOMContentLoaded', function() {
            if (!eventId) {
                showError('No event ID provided');
                return;
            }

            loadEventDetails();
            setupPaymentMethodSelection();
            setupFormValidation();
            setupCancelButton();
        });

        function loadEventDetails() {
            // Fetch event details
            fetch(`/unipulse/public/user/eventview/getEvent?id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.event) {
                        displayEventDetails(data.event);
                    } else {
                        showError('Event not found');
                    }
                })
                .catch(error => {
                    console.error('Error loading event:', error);
                    showError('Failed to load event details');
                });
        }

        function displayEventDetails(event) {
            document.getElementById('eventName').textContent = event.title || event.name;
            document.getElementById('eventDate').textContent = formatDate(event.date);
            
            const ticketType = event.ticket_type || 'Standard';
            document.getElementById('ticketType').textContent = formatTicketType(ticketType);
            
            // Calculate amount based on ticket type
            const amount = calculateTicketPrice(ticketType);
            document.getElementById('totalAmount').textContent = `LKR ${amount.toFixed(2)}`;
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        function formatTicketType(type) {
            const typeMap = {
                'free-all': 'Free for All',
                'free-limited': 'Free (Limited)',
                'paid': 'Paid',
                'donation-based': 'Donation Based'
            };
            return typeMap[type] || type;
        }

        function calculateTicketPrice(ticketType) {
            // This is a placeholder - you should get actual prices from the database
            const priceMap = {
                'paid': 1000.00,
                'donation-based': 500.00,
                'free-limited': 0.00,
                'free-all': 0.00
            };
            return priceMap[ticketType] || 1000.00;
        }

        function setupPaymentMethodSelection() {
            const paymentMethods = document.querySelectorAll('.payment-method');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    paymentMethods.forEach(m => m.classList.remove('active'));
                    this.classList.add('active');
                    
                    const selectedMethod = this.dataset.method;
                    // You can add logic to show/hide different payment fields based on method
                });
            });
        }

        function setupFormValidation() {
            const cardNumber = document.getElementById('cardNumber');
            const expiryDate = document.getElementById('expiryDate');
            const cvv = document.getElementById('cvv');

            // Format card number
            cardNumber.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });

            // Format expiry date
            expiryDate.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2, 4);
                }
                e.target.value = value;
            });

            // CVV validation
            cvv.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });

            // Form submission
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                processPayment();
            });
        }

        function setupCancelButton() {
            document.getElementById('cancelBtn').addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to cancel this payment?')) {
                    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
                }
            });
        }

        function processPayment() {
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;

            // Simulate payment processing
            setTimeout(() => {
                // Here you would normally send payment data to your backend
                showSuccess('Payment successful! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
                }, 2000);
            }, 2000);
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
        }
    </script>
</body>

</html>
