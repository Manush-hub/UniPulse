<?php

// Set timezone to match database/application needs
date_default_timezone_set('Asia/Colombo'); // Set to your timezone

// Set default SERVER_NAME for CLI execution
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local development defaults (override with env vars when needed)
    $isWindows = (PHP_OS_FAMILY === 'Windows');
    $pathHints = implode(' ', [
        __DIR__,
        $_SERVER['DOCUMENT_ROOT'] ?? '',
        $_SERVER['SCRIPT_FILENAME'] ?? ''
    ]);

    $isMamp = (stripos($pathHints, 'mamp') !== false);
    $isWamp = (stripos($pathHints, 'wamp') !== false);
    $isXampp = (stripos($pathHints, 'xampp') !== false);

    define('DBNAME', getenv('UNIPULSE_DB_NAME') ?: 'unipulse_final');
    define('DBHOST', getenv('UNIPULSE_DB_HOST') ?: 'localhost');
    define('DBUSER', getenv('UNIPULSE_DB_USER') ?: 'root');

    // Default DB credentials by local stack
    $defaultDbPass = 'root';
    $defaultDbPort = '8889';

    if ($isWamp || $isXampp || $isWindows) {
        // WAMP/XAMPP defaults (and generic Windows fallback)
        $defaultDbPass = '';
        $defaultDbPort = '3306';
    }

    if ($isMamp) {
        // MAMP defaults
        $defaultDbPass = 'root';
        $defaultDbPort = '8889';
    }

    define('DBPASS', getenv('UNIPULSE_DB_PASS') !== false ? getenv('UNIPULSE_DB_PASS') : $defaultDbPass);
    define('DBPORT', getenv('UNIPULSE_DB_PORT') ?: $defaultDbPort);

    define('DB_CHARSET', getenv('UNIPULSE_DB_CHARSET') ?: 'utf8mb4');
    define('DB_COLLATION', getenv('UNIPULSE_DB_COLLATION') ?: 'utf8mb4_general_ci');

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (!isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SERVER_PORT']) && !in_array((string)$_SERVER['SERVER_PORT'], ['80', '443'], true)) {
        $host .= ':' . $_SERVER['SERVER_PORT'];
    }

    $projectFolder = basename(dirname(__DIR__, 2));
    $defaultRoot = $scheme . '://' . $host . '/' . $projectFolder . '/public';
    define('ROOT', getenv('UNIPULSE_ROOT_URL') ?: $defaultRoot);
} else {
    define('ROOT', 'https://www.unipulse.lk');

    define('DB_CHARSET', getenv('UNIPULSE_DB_CHARSET') ?: 'utf8mb4');
    define('DB_COLLATION', getenv('UNIPULSE_DB_COLLATION') ?: 'utf8mb4_general_ci');
}

define('APP_NAME', "My website");
define('APP_DESC', "MY WEBSITES");

define('DEBUG', false); // Set to true for development, false for production

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
