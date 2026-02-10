<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Container -->
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="fas fa-credit-card"></i> Secure Payment</h1>
            <p>Complete your payment securely</p>
        </div>

        <form method="POST" action="" id="payment-form">
            <div class="payment-content">
                <!-- Left Column: Payment Details -->
                <div class="payment-details">
                    <div class="section">
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <div class="method-card active">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit/Debit Card</span>
                            </div>
                            <input type="hidden" name="payment_method" value="card">
                        </div>
                    </div>

                    <!-- Card Payment Form -->
                    <div class="section" id="card-payment-section">
                        <h2>Card Details</h2>
                        
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card"></i>
                                <input type="text" 
                                       id="card_number" 
                                       name="card_number" 
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       value="<?= htmlspecialchars($form_data['card_number'] ?? '') ?>">
                            </div>
                            <?php if (!empty($errors['card_number'])): ?>
                                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">
                                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['card_number']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="card_name">Cardholder Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" 
                                       id="card_name" 
                                       name="card_name" 
                                       placeholder="John Doe"
                                       value="<?= htmlspecialchars($form_data['card_name'] ?? '') ?>">
                            </div>
                            <?php if (!empty($errors['card_name'])): ?>
                                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">
                                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['card_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-calendar"></i>
                                    <input type="text" 
                                           id="expiry_date" 
                                           name="expiry_date" 
                                           placeholder="MM/YY"
                                           maxlength="5"
                                           value="<?= htmlspecialchars($form_data['expiry_date'] ?? '') ?>">
                                </div>
                                <?php if (!empty($errors['expiry_date'])): ?>
                                    <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['expiry_date']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="text" 
                                           id="cvv" 
                                           name="cvv" 
                                           placeholder="123"
                                           maxlength="3"
                                           value="<?= htmlspecialchars($form_data['cvv'] ?? '') ?>">
                                </div>
                                <?php if (!empty($errors['cvv'])): ?>
                                    <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['cvv']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="summary-item">
                        <span>Item</span>
                        <span><?= htmlspecialchars($item_description ?? 'Event Ticket') ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span><?= ($payment_type ?? 'ticket') === 'ticket' ? 'Ticket Price' : 'Boost Amount' ?></span>
                        <span class="amount">LKR <?= htmlspecialchars($amount ?? '0.00') ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="summary-item total">
                        <span>Total Amount</span>
                        <span class="total-amount">LKR <?= htmlspecialchars($amount ?? '0.00') ?></span>
                    </div>

                    <input type="hidden" name="amount" value="<?= htmlspecialchars($amount ?? '') ?>">
                            <button type="submit" class="pay-button">
                        <i class="fas fa-lock"></i> Pay Now
                    </button>

                    <div class="security-badges">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secured by 256-bit SSL encryption</span>
                    </div>

                    <div class="accepted-cards">
                        <img src="/unipulse/public/assets/images/visa.png" alt="Visa" onerror="this.style.display='none'">
                        <img src="/unipulse/public/assets/images/mastercard.png" alt="Mastercard" onerror="this.style.display='none'">
                        <img src="/unipulse/public/assets/images/amex.png" alt="Amex" onerror="this.style.display='none'">
                        <i class="fab fa-cc-visa fa-2x"></i>
                        <i class="fab fa-cc-mastercard fa-2x"></i>
                        <i class="fab fa-cc-amex fa-2x"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>

        // Card number formatting - only numbers allowed
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let value = e.target.value.replace(/\D/g, '');
                // Format in groups of 4
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }

        // Cardholder name - only letters and spaces allowed
        const cardNameInput = document.getElementById('card_name');
        if (cardNameInput) {
            cardNameInput.addEventListener('input', function(e) {
                // Remove all numbers and special characters, keep only letters and spaces
                let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
                e.target.value = value;
            });
        }

        // Expiry date formatting - only numbers allowed
        const expiryInput = document.getElementById('expiry_date');
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let value = e.target.value.replace(/\D/g, '');
                // Format as MM/YY
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }

        // CVV - only numbers allowed
        const cvvInput = document.getElementById('cvv');
        if (cvvInput) {
            cvvInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            });
        }

        // Form validation
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'card') {
                const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
                const cvv = document.getElementById('cvv').value;
                
                if (cardNumber.length !== 16) {
                    e.preventDefault();
                    alert('Please enter a valid 16-digit card number');
                    return false;
                }
                
                if (cvv.length !== 3) {
                    e.preventDefault();
                    alert('Please enter a valid 3-digit CVV');
                    return false;
                }
            }
        });
    </script>
</body>
</html>
