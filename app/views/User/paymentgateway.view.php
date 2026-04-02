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

    <script>
        // Get event ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const eventId = urlParams.get('event_id');
        let currentEventDetails = null;
        let currentPaymentData = null;

        // Load event details
        document.addEventListener('DOMContentLoaded', function() {
            if (!eventId) {
                showError('No event ID provided');
                return;
            }

            loadEventDetails();
            setupCancelButton();

            // Setup form submission for PayHere redirect
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                processPayment();
            });
        });

        function loadEventDetails() {
            // Fetch event details
            fetch(`/unipulse/public/user/eventview/getEvent?id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.event) {
                        currentEventDetails = data.event;
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
            const paymentData = getStoredPaymentData();

            document.getElementById('eventName').textContent =
                paymentData?.eventTitle || event.title || event.name || 'Event';
            document.getElementById('eventDate').textContent = event.date ? formatDate(event.date) : 'N/A';

            if (paymentData && Array.isArray(paymentData.tickets) && paymentData.tickets.length > 0) {
                currentPaymentData = paymentData;

                const totalQuantity = paymentData.tickets.reduce((sum, ticket) => {
                    return sum + (parseInt(ticket.quantity, 10) || 0);
                }, 0);

                const ticketTypeLabel = paymentData.tickets.length === 1 ?
                    paymentData.tickets[0].name :
                    `Multiple (${paymentData.tickets.length} types)`;

                document.getElementById('ticketType').textContent = ticketTypeLabel;
                document.getElementById('ticketQuantity').textContent = String(totalQuantity || 1);
                document.getElementById('totalAmount').textContent = `LKR ${Number(paymentData.totalAmount || 0).toFixed(2)}`;
                updateSavingsDisplay(paymentData);
                return;
            }

            // Fallback for direct page access without event selection state.
            const ticketType = event.ticket_type || 'Standard';
            document.getElementById('ticketType').textContent = formatTicketType(ticketType);

            let amount = 100;
            if (Array.isArray(event.ticket_types) && event.ticket_types.length > 0) {
                const firstTicket = event.ticket_types[0];
                amount = Number(firstTicket.discounted_price || firstTicket.price || 100);
            }

            document.getElementById('ticketQuantity').textContent = '1';
            document.getElementById('totalAmount').textContent = `LKR ${amount.toFixed(2)}`;
            updateSavingsDisplay(null);
        }

        function updateSavingsDisplay(paymentData) {
            const savingsRow = document.getElementById('savingsRow');
            const savingsAmount = document.getElementById('savingsAmount');
            if (!savingsRow || !savingsAmount) {
                return;
            }

            let savings = 0;

            if (paymentData && Array.isArray(paymentData.tickets)) {
                savings = paymentData.tickets.reduce((sum, ticket) => {
                    if (ticket && typeof ticket.discountAmount !== 'undefined') {
                        return sum + (Number(ticket.discountAmount) || 0);
                    }

                    const qty = Number(ticket?.quantity) || 0;
                    const originalPrice = Number(ticket?.originalPrice) || 0;
                    const payablePrice = Number(ticket?.price) || 0;
                    const lineSaving = Math.max(0, (originalPrice - payablePrice) * qty);
                    return sum + lineSaving;
                }, 0);
            }

            if (savings > 0) {
                savingsAmount.textContent = `LKR ${savings.toFixed(2)}`;
                savingsRow.style.display = 'flex';
            } else {
                savingsAmount.textContent = 'LKR 0.00';
                savingsRow.style.display = 'none';
            }
        }

        function getStoredPaymentData() {
            try {
                const raw = sessionStorage.getItem('paymentData');
                if (!raw) {
                    return null;
                }

                const parsed = JSON.parse(raw);
                const parsedEventId = parsed?.eventId != null ? String(parsed.eventId) : null;

                if (!parsedEventId || parsedEventId !== String(eventId)) {
                    return null;
                }

                return parsed;
            } catch (error) {
                console.warn('Unable to parse paymentData from sessionStorage', error);
                return null;
            }
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
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
            // Get price from ticket_types if available, or use default
            // This will be properly populated by the event data
            return window.currentEventPrice || 100; // Default fallback
            const priceMap = {
                'paid': 1000.00,
                'donation-based': 500.00,
                'free-limited': 0.00,
                'free-all': 0.00
            };
            return priceMap[ticketType] || 1000.00;
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to PayHere...';
            submitBtn.disabled = true;

            const paymentData = currentPaymentData || getStoredPaymentData();

            let amount = 0;
            let quantity = 1;
            let description = 'Event Ticket';
            let ticketTierName = 'General';

            if (paymentData && Array.isArray(paymentData.tickets) && paymentData.tickets.length > 0) {
                amount = Number(paymentData.totalAmount || 0);
                quantity = paymentData.tickets.reduce((sum, ticket) => {
                    return sum + (parseInt(ticket.quantity, 10) || 0);
                }, 0) || 1;

                if (paymentData.tickets.length === 1) {
                    description = `Event Ticket - ${paymentData.tickets[0].name}`;
                    ticketTierName = paymentData.tickets[0].name;
                } else {
                    description = `Event Tickets - ${paymentData.tickets.length} types`;
                    ticketTierName = paymentData.tickets.map(t => t.name).join(', ');
                }
            } else {
                const totalAmountText = document.getElementById('totalAmount').textContent;
                amount = parseFloat(totalAmountText.replace('LKR', '').trim()) || 0;
            }

            if (amount <= 0) {
                showError('Invalid payment amount. Please go back and select at least one ticket.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Redirect to PayHere payment with session setup
            // The Payment controller expects these params
            const ticketsMetadata = paymentData && paymentData.tickets ? JSON.stringify(paymentData.tickets) : '';
            const paymentUrl = `/unipulse/public/payment?amount=${encodeURIComponent(amount.toFixed(2))}&type=ticket&event_id=${encodeURIComponent(eventId)}&quantity=${encodeURIComponent(quantity)}&description=${encodeURIComponent(description)}&ticket_tier=${encodeURIComponent(ticketTierName)}&tickets_metadata=${encodeURIComponent(ticketsMetadata)}`;

            console.log('Redirecting to PayHere:', {
                event_id: eventId,
                amount: amount,
                quantity: quantity,
                description: description,
                ticket_tier: ticketTierName,
                tickets_metadata: ticketsMetadata,
                url: paymentUrl
            });

            // Redirect to payment page which will show PayHere option
            window.location.href = paymentUrl;
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