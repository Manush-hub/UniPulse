<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="/unipulse/public/assets/css/payment_success-style.css">
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
