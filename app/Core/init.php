<?php

spl_autoload_register(function($classname){
    // Only autoload model classes, not controllers
    $modelFile = "../app/models/".ucfirst($classname). ".php";
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
});

require_once 'config.php';
require_once 'functions.php';
require_once 'Controller.php';
require_once 'Database.php';
require_once 'Model.php';
require_once 'App.php';

