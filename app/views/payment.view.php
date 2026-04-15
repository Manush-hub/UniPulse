<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Container -->
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="fas fa-lock"></i> Secure Payment</h1>
            <p>Complete your payment securely via PayHere</p>
        </div>

        <div class="payment-content">

            <!-- Left Column: PayHere info -->
            <div class="payment-details">
                <div class="section">
                    <h2>Payment Method</h2>
                    <div class="payment-methods">
                        <div class="method-card active" style="cursor:default;">
                            <img src="https://www.payhere.lk/downloads/images/payhere_short_logo.png"
                                 alt="PayHere"
                                 style="height:24px;vertical-align:middle;margin-right:8px;"
                                 onerror="this.style.display='none'">
                            <span>PayHere</span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>How it works</h2>
                    <div style="color:#6b7280; font-size:14px; line-height:2;">
                        <p style="margin:0;">
                            <i class="fas fa-circle-dot" style="color:#00457C;margin-right:8px;font-size:11px;"></i>
                            Click <strong>Pay Now</strong> to proceed
                        </p>
                        <p style="margin:0;">
                            <i class="fas fa-circle-dot" style="color:#00457C;margin-right:8px;font-size:11px;"></i>
                            You'll be redirected to PayHere's secure checkout
                        </p>
                        <p style="margin:0;">
                            <i class="fas fa-circle-dot" style="color:#00457C;margin-right:8px;font-size:11px;"></i>
                            Complete payment with your card, bank or e-wallet
                        </p>
                        <p style="margin:0;">
                            <i class="fas fa-circle-dot" style="color:#00457C;margin-right:8px;font-size:11px;"></i>
                            You'll be returned here with a confirmation
                        </p>
                    </div>

                    <div style="margin-top:20px; background:#f0f9ff; border:1px solid #bae6fd;
                                border-radius:10px; padding:14px 16px; font-size:13px; color:#0369a1;">
                        <p style="margin:0 0 6px 0;">
                            <i class="fas fa-credit-card"></i>
                            <strong>Accepted:</strong> Visa, Mastercard, Amex, Internet Banking &amp; more
                        </p>
                        <p style="margin:0; color:#6b7280;">
                            <i class="fas fa-flask"></i>
                            <strong>Sandbox test card:</strong> 4111 1111 1111 1111
                            &nbsp;|&nbsp; OTP: 123456
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary + Pay Button -->
            <div class="order-summary">
                <h2>Order Summary</h2>

                <div class="summary-item">
                    <span>Item</span>
                    <span><?= htmlspecialchars($item_description ?? 'Event Ticket') ?></span>
                </div>

                <div class="summary-item">
                    <span><?= ($payment_type ?? 'ticket') === 'ticket' ? 'Ticket Price' : 'Boost Amount' ?></span>
                    <span class="amount">LKR <?= number_format((float)($amount ?? 0), 2) ?></span>
                </div>

                <hr>

                <div class="summary-item total">
                    <span>Total Amount</span>
                    <span class="total-amount">LKR <?= number_format((float)($amount ?? 0), 2) ?></span>
                </div>

                <?php if (!empty($errors['payment'])): ?>
                    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px;
                                padding:12px; margin:14px 0; color:#dc2626; font-size:14px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($errors['payment']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= ROOT ?>/payment/payhere">
                    <input type="hidden" name="amount" value="<?= htmlspecialchars($amount ?? '') ?>">
                    <button type="submit" class="pay-button"
                            style="background:#00457C; border-color:#00457C; width:100%; margin-top:16px;">
                        <i class="fas fa-wallet"></i>
                        Pay Now &nbsp;&mdash;&nbsp; LKR <?= number_format((float)($amount ?? 0), 2) ?>
                    </button>
                </form>

                <div class="security-badges" style="margin-top:16px;">
                    <i class="fas fa-shield-alt" style="color:#00457C;"></i>
                    <span>Secured by PayHere &amp; 256-bit SSL encryption</span>
                </div>

                <div class="accepted-cards" style="margin-top:12px;">
                    <i class="fab fa-cc-visa fa-2x"></i>
                    <i class="fab fa-cc-mastercard fa-2x"></i>
                    <i class="fab fa-cc-amex fa-2x"></i>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
