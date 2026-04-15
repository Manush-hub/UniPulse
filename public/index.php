<?php

// Configure session settings for better cookie sharing
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

session_start();

require "../app/Core/init.php";

DEBUG ?  ini_set('display_errors',1) : ini_set('display_errors',0);


$app = new App;
$app->loadController();
