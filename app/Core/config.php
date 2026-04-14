<?php

// Set timezone to match database/application needs
date_default_timezone_set('Asia/Colombo'); // Set to your timezone

// Set default SERVER_NAME for CLI execution
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // MAMP Configuration (macOS/Windows)
    define('DBNAME', 'unipulse_db2-3 (2)');
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');

    // Check if running on MAMP (macOS) or WAMP (Windows)
    if (PHP_OS_FAMILY === 'Windows' || strpos(__DIR__, 'wamp') !== false) {
        // WAMP Configuration (Windows)
        define('DBPASS', '');  // WAMP default is empty password
        define('DBPORT', '3306'); // WAMP default port
    } else {
        // MAMP Configuration (macOS)
        define('DBPASS', 'root'); // MAMP default password
        define('DBPORT', '8889'); // MAMP default port
    }

    define('ROOT', 'http://localhost/UniPulse/public');
} else {
    define('ROOT', 'https://www.unipulse.lk');
}

define('APP_NAME', "My website");
define('APP_DESC', "MY WEBSITES");

define('DEBUG', true);

// SMTP configuration (for password reset emails)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_EMAIL', '');
define('SMTP_FROM_NAME', 'UniPulse');

// ─── PayHere Sandbox Configuration ───────────────────────────────────────────
// 1. Register at https://www.payhere.lk → Login → Settings → Domains & Credentials
// 2. Find your Merchant ID and Merchant Secret, paste them below
// 3. Add your site URL (http://localhost/UniPulse/public) to allowed domains
// 4. Change PAYHERE_MODE to 'live' and swap credentials when going to production
define('PAYHERE_MODE',        'sandbox');  // 'sandbox' | 'live'
define('PAYHERE_MERCHANT_ID', '1234247');    // ← your sandbox merchant ID
define('PAYHERE_SECRET',      'MjEzNjk0MDU1OTEwMzg5NTk3NjAzODQwMjk5OTM5MTQxMTg3MDgwMw=='); // ← your sandbox secret

// Sandbox checkout endpoint (accepts LKR natively — no conversion needed)
define('PAYHERE_CHECKOUT_URL', 'https://sandbox.payhere.lk/pay/checkout');
// Live endpoint (swap when going live):
// define('PAYHERE_CHECKOUT_URL', 'https://www.payhere.lk/pay/checkout');
