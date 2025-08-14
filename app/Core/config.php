<?php

if($_SERVER['SERVER_NAME'] == 'localhost'){
    define('DBNAME','my_db');
    define('DBHOST','localhost');
    define('DBUSER','root');
    define('DBPASS','');
    
    define('ROOT','http://localhost/BookMart/public');
}
else{
    define('ROOT','https://www.BookMart.lk');
}

define ('APP_NAME',"My website");
define('APP_DESC',"MY WEBISTESDS");

define('DEBUG', true);