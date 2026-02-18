<?php

// Set timezone to match database/application needs
date_default_timezone_set('Asia/Colombo'); // Set to your timezone

// Set default SERVER_NAME for CLI execution
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // MAMP Configuration (macOS/Windows)
    define('DBNAME', 'unipulse_db-7');
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');

    // Check if running on MAMP (macOS) or WAMP (Windows)
    if (PHP_OS_FAMILY === 'Windows' || strpos(__DIR__, 'wamp') !== false) {
        // WAMP Configuration (Windows)
        define('DBPASS', 'hash@123');  // WAMP default is empty password
        define('DBPORT', '3306'); // WAMP default port
    } else {
        // MAMP Configuration (macOS)
        define('DBPASS', 'root'); // MAMP default password
        define('DBPORT', '8889'); // MAMP default port
    }

    define('ROOT','http://localhost/UniPulse/public');
} else {
    define('ROOT', 'https://www.unipulse.lk');
}

define('APP_NAME', "My website");
define('APP_DESC', "MY WEBSITES");

define('DEBUG', true);
