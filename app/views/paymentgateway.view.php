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
        let currentStep = 1;
        let ticketPrice = <?= (float)$eventTicketPrice ?>;
        let selectedPaymentMethod = 'card';
        let hasUploadedSlip = false;
        let currentOrderId = '';

        function updateProgress() {
            document.querySelector('.payment-progress-bar').setAttribute('data-step', currentStep);

            const steps = document.querySelectorAll('.payment-step');
            steps.forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 === currentStep) {
                    step.classList.add('active');
                } else if (index + 1 < currentStep) {
                    step.classList.add('completed');
                }
            });
        }

        function showStep(step) {
            document.querySelectorAll('.payment-form-step').forEach(s => s.classList.remove('active'));
            document.querySelector(`.payment-form-step[data-step="${step}"]`).classList.add('active');
        }

        function validateStep(step) {
            const currentStepEl = document.querySelector(`.payment-form-step[data-step="${step}"]`);
            const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
            let valid = true;

            inputs.forEach(input => {
                input.classList.remove('payment-error');
                const errorMsg = input.parentElement.querySelector('.payment-error-message');
                if (errorMsg) errorMsg.remove();

                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('payment-error');
                    const error = document.createElement('div');
                    error.className = 'payment-error-message';
                    error.textContent = 'This field is required';
                    input.parentElement.appendChild(error);
                }

                if (input.type === 'email' && input.value && !input.value.includes('@')) {
                    valid = false;
                    input.classList.add('payment-error');
                    const error = document.createElement('div');
                    error.className = 'payment-error-message';
                    error.textContent = 'Please enter a valid email';
                    input.parentElement.appendChild(error);
                }
            });

            // Additional validation for step 2 based on payment method
            if (step === 2) {
                if (selectedPaymentMethod === 'slip' && !hasUploadedSlip) {
                    valid = false;
                    document.getElementById('slipUpload').classList.add('payment-error');
                    const error = document.createElement('div');
                    error.className = 'payment-error-message';
                    error.textContent = 'Please upload a payment slip';
                    document.getElementById('slipUpload').parentElement.appendChild(error);
                }
            }

            return valid;
        }

        function nextStep(step) {
            if (validateStep(step)) {
                // Update order summary for step 2
                if (step === 1) {
                    updateOrderSummary();
                }

                currentStep++;
                updateProgress();
                showStep(currentStep);
            }
        }

        function prevStep(step) {
            currentStep--;
            updateProgress();
            showStep(currentStep);
        }

        function selectPayment(element, method) {
            document.querySelectorAll('.payment-method-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            selectedPaymentMethod = method;

            // Show/hide appropriate payment forms
            if (method === 'slip') {
                document.getElementById('card-payment-form').style.display = 'none';
                document.getElementById('slip-payment-form').style.display = 'block';
                document.getElementById('paymentButton').textContent = 'Upload Payment Slip';
            } else {
                document.getElementById('card-payment-form').style.display = 'block';
                document.getElementById('slip-payment-form').style.display = 'none';
                document.getElementById('paymentButton').textContent = 'Complete Payment';
            }
        }

        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            if (quantity < 10) {
                quantityInput.value = quantity + 1;
                updateTicketTotal();
            }
        }

        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateTicketTotal();
            }
        }

        function updateTicketTotal() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const displayQuantity = document.getElementById('displayQuantity');
            const totalPrice = document.getElementById('totalPrice');

            displayQuantity.textContent = quantity;
            const total = ticketPrice * quantity;
            totalPrice.textContent = total.toFixed(2);
        }

        function updateOrderSummary() {
            const quantity = document.getElementById('quantity').value;

            document.getElementById('summaryQuantity').textContent = quantity;
            document.getElementById('summaryPrice').textContent = ticketPrice.toFixed(2);
            document.getElementById('summaryTotal').textContent = (ticketPrice * quantity).toFixed(2);
        }

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                document.getElementById('fileName').textContent = fileName;
                document.getElementById('slipUpload').classList.add('has-file');
                hasUploadedSlip = true;

                // Remove any existing error messages
                const errorMsg = document.getElementById('slipUpload').parentElement.querySelector('.payment-error-message');
                if (errorMsg) errorMsg.remove();
                document.getElementById('slipUpload').classList.remove('payment-error');
            }
        }

        function generateBarcode(orderId) {
            // Generate barcode using JsBarcode library
            JsBarcode("#barcode", orderId, {
                format: "CODE128",
                width: 2,
                height: 100,
                displayValue: true,
                fontSize: 16,
                background: "#ffffff",
                lineColor: "#333333"
            });
        }

        function downloadBarcode() {
            // Get the SVG element
            const svg = document.getElementById("barcode");

            // Create a canvas to convert SVG to image
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // Get SVG data
            const svgData = new XMLSerializer().serializeToString(svg);
            const img = new Image();

            // Create a blob from the SVG data
            const svgBlob = new Blob([svgData], {
                type: "image/svg+xml;charset=utf-8"
            });
            const url = URL.createObjectURL(svgBlob);

            img.onload = function() {
                // Set canvas dimensions
                canvas.width = svg.clientWidth;
                canvas.height = svg.clientHeight;

                // Draw the image on canvas
                ctx.drawImage(img, 0, 0);

                // Create download link
                const pngUrl = canvas.toDataURL("image/png");
                const downloadLink = document.createElement("a");
                downloadLink.href = pngUrl;
                downloadLink.download = `ticket-barcode-${currentOrderId}.png`;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

                // Clean up
                URL.revokeObjectURL(url);
            };

            img.src = url;
        }

        function processPayment() {
            if (!validateStep(2)) return;

            const paymentButton = document.getElementById('paymentButton');
            paymentButton.disabled = true;
            paymentButton.textContent = 'Processing...';

            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const totalAmount = (ticketPrice * quantity).toFixed(2);
            const eventId = document.getElementById('eventId').value;
            const publisherId = document.getElementById('publisherId').value;
            const eventTitle = document.getElementById('eventTitle').value;

            // Build form data to send to the server
            const formData = new FormData();
            formData.append('event_id', eventId);
            formData.append('payment_method', selectedPaymentMethod);
            formData.append('amount', totalAmount);
            formData.append('quantity', quantity);

            // If payment slip, attach the file
            if (selectedPaymentMethod === 'slip') {
                const slipFile = document.getElementById('slipFile').files[0];
                if (slipFile) {
                    formData.append('payment_slip', slipFile);
                }
            }

            // Send AJAX request to backend
            console.log('Sending payment request:', {
                event_id: eventId,
                payment_method: selectedPaymentMethod,
                amount: totalAmount,
                quantity: quantity
            });

            fetch('<?= ROOT ?>/user/paymentgateway/processPayment', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Payment response:', data);
                if (data.success) {
                    currentOrderId = data.transaction_id || Math.floor(100000 + Math.random() * 900000).toString();

                    if (selectedPaymentMethod === 'slip') {
                        document.getElementById('confirmationTitle').textContent = 'Payment Slip Uploaded!';
                        document.getElementById('confirmationMessage').innerHTML =
                            'Your payment slip has been uploaded successfully.<br>' +
                            'Your tickets are pending confirmation and will be activated after payment verification.<br>' +
                            'This usually takes 1-2 business days.';
                    }

                    currentStep = 3;
                    updateProgress();
                    showStep(3);

                    // Set order ID and generate barcode
                    document.getElementById('orderId').textContent = currentOrderId;
                    document.getElementById('barcodeOrderId').textContent = currentOrderId;
                    generateBarcode(currentOrderId);
                } else {
                    alert('Payment failed: ' + (data.error || 'Unknown error. Please try again.'));
                    paymentButton.disabled = false;
                    paymentButton.textContent = selectedPaymentMethod === 'slip' ? 'Upload Payment Slip' : 'Complete Payment';
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                alert('Payment processing failed. Please try again.');
                paymentButton.disabled = false;
                paymentButton.textContent = selectedPaymentMethod === 'slip' ? 'Upload Payment Slip' : 'Complete Payment';
            });
        }

        function resetForm() {
            currentStep = 1;
            updateProgress();
            showStep(1);
            document.querySelectorAll('input').forEach(input => input.value = '');
            document.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            document.getElementById('quantity').value = 1;
            updateTicketTotal();
            selectPayment(document.querySelector('.payment-method-option'), 'card');
            hasUploadedSlip = false;
            document.getElementById('fileName').textContent = '';
            document.getElementById('slipUpload').classList.remove('has-file');
            document.getElementById('confirmationTitle').textContent = 'Payment Successful!';
            document.getElementById('confirmationMessage').textContent = 'A confirmation email has been sent to your email address.';
            currentOrderId = '';
        }

        // Format card number input
        document.getElementById('cardnumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date input
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });

        // Only allow numbers in CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Initialize ticket total
        updateTicketTotal();
    </script>
    <?php
    // include the global footer (actual file is app/views/footer.php)
    include __DIR__ . '/footer.php';
    ?>
</body>

</html>