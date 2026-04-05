<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/payhere_cancel.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="cancel-container">
        <div class="cancel-card">
            <div class="cancel-icon">
                <i class="fas fa-times-circle"></i>
            </div>

            <h1>Payment Cancelled</h1>

            <p class="cancel-message">
                You cancelled the PayHere payment. Your order has <strong>not</strong> been processed
                and <strong>no money has been charged</strong>. You can try again whenever you're ready.
            </p>

            <div class="action-buttons">
                <a href="<?= ROOT ?>/payment" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Try Again
                </a>
                <a href="<?= ROOT ?>/home" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Go Home
                </a>
            </div>

            <hr class="divider">
            <p class="note">
                <i class="fas fa-info-circle"></i>
                If you believe you were charged by mistake, contact us at
                <strong>support@unipulse.lk</strong>
            </p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
