<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f3f4f6; }
        .cancel-container { min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .cancel-card { background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                       padding: 48px 40px; text-align: center; max-width: 520px; width: 100%; }
        .cancel-icon { font-size: 72px; color: #f59e0b; margin-bottom: 20px; }
        h1 { color: #1f2937; margin-bottom: 12px; font-size: 28px; }
        .cancel-message { color: #6b7280; font-size: 16px; line-height: 1.6; margin-bottom: 32px; }
        .action-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
        .btn { padding: 12px 26px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px;
               display: inline-flex; align-items: center; gap: 8px; transition: opacity .2s; border: none; cursor: pointer; }
        .btn:hover { opacity: .85; }
        .btn-primary   { background: #00457C; color: white; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .divider { margin: 28px 0; border: none; border-top: 1px solid #e5e7eb; }
        .note { font-size: 13px; color: #9ca3af; }
    </style>
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
