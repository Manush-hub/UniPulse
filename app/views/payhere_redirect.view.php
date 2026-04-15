<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayHere | UniPulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/payhere_redirect.css">
</head>

<body>
    <div class="redirect-card">
        <div class="spinner"><i class="fas fa-circle-notch"></i></div>
        <h2>Redirecting to PayHere…</h2>
        <p>Please wait while we securely redirect you to PayHere to complete your payment.</p>
        <p style="font-size:12px; color:#9ca3af; margin-top:20px;">
            Do not close this tab.
        </p>
    </div>

    <!-- Hidden auto-submit form posted to PayHere checkout -->
    <form id="payhere-checkout" method="POST" action="<?= htmlspecialchars($checkout_url) ?>">
        <?php foreach ($fields as $key => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endforeach; ?>
    </form>

    <script src="<?php echo ROOT ?>/assets/js/extracted/payhere_redirect.js"></script>
</body>

</html>