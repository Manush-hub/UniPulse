<?php

// Test script to verify the controllers and models are working
require_once __DIR__ . '/app/Core/init.php';

echo "🧪 Testing Moderator Controllers and Models\n\n";

// Test 1: Check if Moderator model has findById method
echo "1️⃣ Testing Moderator::findById() method...\n";
try {
    $moderatorModel = new Moderator();
    if (method_exists($moderatorModel, 'findById')) {
        echo "✅ Moderator::findById() method exists\n";
    } else {
        echo "❌ Moderator::findById() method missing\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing Moderator model: " . $e->getMessage() . "\n";
}

// Test 2: Check if Event model has moderation methods
echo "\n2️⃣ Testing Event moderation methods...\n";
try {
    $eventModel = new Event();
    $methods = ['getPendingEventsForUniversity', 'getModerationStatsForUniversity', 'approve', 'reject'];
    
    foreach ($methods as $method) {
        if (method_exists($eventModel, $method)) {
            echo "✅ Event::{$method}() method exists\n";
        } else {
            echo "❌ Event::{$method}() method missing\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error testing Event model: " . $e->getMessage() . "\n";
}

// Test 3: Check if Report model exists and has required methods
echo "\n3️⃣ Testing Report model methods...\n";
try {
    if (class_exists('Report')) {
        $reportModel = new Report();
        $methods = ['getReportsForUniversity', 'getReportStatsForUniversity', 'assignToModerator', 'resolve'];
        
        foreach ($methods as $method) {
            if (method_exists($reportModel, $method)) {
                echo "✅ Report::{$method}() method exists\n";
            } else {
                echo "❌ Report::{$method}() method missing\n";
            }
        }
    } else {
        echo "❌ Report class does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing Report model: " . $e->getMessage() . "\n";
}

// Test 4: Check if controllers exist
echo "\n4️⃣ Testing Controller classes...\n";
$controllers = ['ContentModeration', 'UserReports'];

foreach ($controllers as $controller) {
    if (class_exists($controller)) {
        echo "✅ {$controller} controller exists\n";
        
        // Test if controller has index method
        if (method_exists($controller, 'index')) {
            echo "✅ {$controller}::index() method exists\n";
        } else {
            echo "❌ {$controller}::index() method missing\n";
        }
    } else {
        echo "❌ {$controller} controller does not exist\n";
    }
}

// Test 5: Check database tables
echo "\n5️⃣ Testing Database tables...\n";
try {
    $tempClass = new class {
        use Database;
        public function getConnection() {
            return $this->connect();
        }
    };
    
    $pdo = $tempClass->getConnection();
    
    $tables = ['reports', 'event_moderation_notifications'];
    
    foreach ($tables as $table) {
        $query = "SHOW TABLES LIKE '{$table}'";
        $result = $pdo->query($query);
        
        if ($result->rowCount() > 0) {
            echo "✅ Table '{$table}' exists\n";
        } else {
            echo "❌ Table '{$table}' missing\n";
        }
    }
    
    // Check if events table has moderation columns
    $query = "SHOW COLUMNS FROM events LIKE 'moderated_by'";
    $result = $pdo->query($query);
    
    if ($result->rowCount() > 0) {
        echo "✅ Events table has moderation columns\n";
    } else {
        echo "❌ Events table missing moderation columns\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing database: " . $e->getMessage() . "\n";
}

echo "\n🎉 Testing completed!\n";
?>