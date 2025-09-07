<?php

// Set default SERVER_NAME for CLI execution
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

if($_SERVER['SERVER_NAME'] == 'localhost'){
    define('DBNAME','unipulse_db');
    define('DBHOST','localhost');
    define('DBUSER','root');
    define('DBPASS','root');
    define('DBPORT','8889');
    
    define('ROOT','http://localhost:8080');
}
else{
    define('ROOT','https://www.unipulse.lk');
}

define ('APP_NAME',"My website");
define('APP_DESC',"MY WEBISTESDS");

define('DEBUG', true);