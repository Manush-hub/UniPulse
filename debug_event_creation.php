<?php
require_once __DIR__ . '/app/Core/config.php';
require_once __DIR__ . '/app/Core/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/models/Event.php';

try {
    echo "Debugging Event Creation...\n";
    
    $eventModel = new Event();
    
    // Test data with minimal fields first
    $testData = [
        'title' => 'Debug Test Event',
        'description' => 'This is a debug test event',
        'category' => 'technology',
        'event_date' => '2025-01-15',
        'event_time' => '10:00:00',
        'location' => 'Debug Test Location',
        'university' => 'test-university',
        'university_name' => 'Test University',
        'organizer' => 'Debug Organizer',
        'organizer_email' => 'debug@example.com',
        'max_participants' => 50,
        'participants' => 0,
        'status' => 'upcoming',
        'target_audience' => 'university-students',
        'ticket_type' => 'free-all',
        'visibility' => 'public'
    ];
    
    echo "Test data prepared:\n";
    print_r($testData);
    
    // Check database connection first
    echo "\nTesting database connection...\n";
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    echo "✓ Database connection successful!\n";
    
    // Check table structure
    echo "\nChecking events table structure...\n";
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll();
    echo "Available columns:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    // Test validation
    echo "\nTesting validation...\n";
    $errors = $eventModel->validate($testData);
    if (!empty($errors)) {
        echo "Validation errors:\n";
        print_r($errors);
    } else {
        echo "✓ Validation passed!\n";
    }
    
    // Test event creation
    echo "\nTesting event creation...\n";
    $result = $eventModel->createEvent($testData);
    
    echo "Creation result:\n";
    print_r($result);
    
    if ($result['success']) {
        echo "✓ Event created successfully with ID: " . $result['event_id'] . "\n";
        
        // Verify the event was created
        echo "\nVerifying event in database...\n";
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$result['event_id']]);
        $event = $stmt->fetch();
        
        if ($event) {
            echo "✓ Event found in database:\n";
            print_r($event);
        } else {
            echo "✗ Event not found in database!\n";
        }
    } else {
        echo "✗ Failed to create event:\n";
        if (isset($result['error'])) {
            echo "Error: " . $result['error'] . "\n";
        }
        if (isset($result['errors'])) {
            echo "Errors:\n";
            print_r($result['errors']);
        }
    }
    
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>