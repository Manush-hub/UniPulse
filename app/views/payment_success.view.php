<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f3f4f6; }
        .success-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .success-card { background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 40px; text-align: center; max-width: 600px; }
        .success-icon { font-size: 80px; color: #10b981; margin-bottom: 20px; }
        h1 { color: #1f2937; margin-bottom: 15px; }
        .success-message { color: #6b7280; font-size: 16px; margin-bottom: 30px; }
        .success-details { display: flex; justify-content: center; gap: 30px; margin: 30px 0; }
        .detail-item { display: flex; align-items: center; gap: 10px; color: #6b7280; }
        .detail-item i { color: #3b82f6; }
        .info-message { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 8px; margin: 30px 0; }
        .action-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 30px; }
        .btn { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .back-home { display: inline-block; margin-top: 20px; color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Success Message Container -->
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Payment Successful!</h1>
            
            <p class="success-message">
                <?= htmlspecialchars($success_message ?? 'Your payment has been processed successfully.') ?>
            </p>
            
            <div class="success-details">
                <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <span>Date: <?= htmlspecialchars($payment_date ?? date('F j, Y')) ?></span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
                    <span>Time: <?= htmlspecialchars($payment_time ?? date('g:i A')) ?></span>
                </div>
            </div>

            <p class="info-message">
                <i class="fas fa-envelope"></i>
                A confirmation email has been sent to your registered email address.
            </p>

            <div class="action-buttons">
                <?php if (isset($order_number) && !empty($order_number)): ?>
                    <a href="<?= ROOT ?>/ticket/download?order=<?= htmlspecialchars($order_number) ?>" target="_blank" class="btn btn-secondary" style="border: 2px solid #3b82f6; color: #3b82f6; background: white;">
                        <i class="fas fa-ticket-alt"></i> Download Ticket
                    </a>
                <?php endif; ?>

                <?php 
                $user_type = $_SESSION['user_type'] ?? 'user';
                
                if (isset($event_id) && !empty($event_id)): 
                    // Route to the appropriate eventview based on user type
                    switch($user_type) {
                        case 'publisher':
                            $eventview_url = ROOT . '/publisher/eventview?id=' . htmlspecialchars($event_id);
                            break;
                        case 'sponsor':
                            $eventview_url = ROOT . '/sponsor/eventview?id=' . htmlspecialchars($event_id);
                            break;
                        default:
                            $eventview_url = ROOT . '/user/eventview?id=' . htmlspecialchars($event_id);
                            break;
                    }
                ?>
                    <a href="<?= $eventview_url ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Return to Event
                    </a>
                <?php else: ?>
                    <?php
                    // Route to the appropriate dashboard based on user type
                    switch($user_type) {
                        case 'publisher':
                            $landing_url = ROOT . '/publisher/dashboard';
                            break;
                        case 'sponsor':
                            $landing_url = ROOT . '/sponsor/dashboard';
                            break;
                        default:
                            $landing_url = ROOT . '/user/dashboard';
                            break;
                    }
                    ?>
                    <a href="<?= $landing_url ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Browse Events
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
