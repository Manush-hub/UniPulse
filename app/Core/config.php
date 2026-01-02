<?php

// Set default SERVER_NAME for CLI execution
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

if($_SERVER['SERVER_NAME'] == 'localhost'){
    // MAMP Configuration (macOS/Windows)
    define('DBNAME','unipulse_db');
    define('DBHOST','localhost');
    define('DBUSER','root');
    
    // Check if running on MAMP (macOS) or WAMP (Windows)
    if (PHP_OS_FAMILY === 'Windows' || strpos(__DIR__, 'wamp') !== false) {
        // WAMP Configuration (Windows)
        define('DBPASS','hash@123');  // WAMP default is empty password
        define('DBPORT','3306'); // WAMP default port
    } else {
        // MAMP Configuration (macOS)
        define('DBPASS','root'); // MAMP default password
        define('DBPORT','8889'); // MAMP default port
    }
    
    define('ROOT','http://localhost:8080');
}
else{
    define('ROOT','https://www.unipulse.lk');
}

define ('APP_NAME',"My website");
define('APP_DESC',"MY WEBSITES");

define('DEBUG', true);