<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayHere | UniPulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f3f4f6;
               display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .redirect-card { background: white; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.1);
                         padding: 48px 40px; text-align: center; max-width: 440px; width: 100%; }
        .spinner { font-size: 52px; color: #00457C; margin-bottom: 20px; animation: spin 1.2s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { color: #1f2937; margin-bottom: 12px; }
        p  { color: #6b7280; font-size: 15px; }
    </style>
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

    <script>
        // Auto-submit after a brief moment so the user sees the loading screen
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('payhere-checkout').submit();
            }, 600);
        });
    </script>
</body>
</html>
