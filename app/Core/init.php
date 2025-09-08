<?php

spl_autoload_register(function($classname){
    // Check for model files first
    $modelFile = "../app/models/".ucfirst($classname). ".php";
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    
    // Check for core files
    $coreFile = "../app/Core/".ucfirst($classname). ".php";
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
});

require_once 'config.php';
require_once 'functions.php';
require_once 'Controller.php';
require_once 'Database.php';
require_once 'Model.php';
require_once 'DatabaseInitializer.php';
require_once 'App.php';

// Auto-initialize database on first run
try {
    $dbInitializer = new DatabaseInitializer();
    if (!$dbInitializer->isDatabaseInitialized()) {
        $dbInitializer->initializeDatabase();
    }
} catch (Exception $e) {
    // Log error but don't break the application
    error_log("Database auto-initialization failed: " . $e->getMessage());
}

