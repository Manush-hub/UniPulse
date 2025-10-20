<?php
require_once __DIR__ . '/app/Core/config.php';

try {
    $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing Event Creation via Database\n";
    echo "===================================\n\n";
    
    // Create a test event
    $sql = "INSERT INTO events (
        title, description, category, university, university_name, 
        visibility, status, event_date, event_time, location, 
        organizer, organizer_email, created_by, created_by_type, 
        participants, max_participants, requirements, schedule
    ) VALUES (
        :title, :description, :category, :university, :university_name,
        :visibility, :status, :event_date, :event_time, :location,
        :organizer, :organizer_email, :created_by, :created_by_type,
        :participants, :max_participants, :requirements, :schedule
    )";
    
    $data = [
        'title' => 'Test Event from Form',
        'description' => 'This is a test event created through form submission to verify the complete workflow.',
        'category' => 'technology',
        'university' => 'university-of-colombo',
        'university_name' => 'University of Colombo',
        'visibility' => 'public',
        'status' => 'upcoming',
        'event_date' => '2025-03-15',
        'event_time' => '14:30:00',
        'location' => 'Computer Science Lab',
        'organizer' => 'Software Engineering Society',
        'organizer_email' => 'ses@uoc.lk',
        'created_by' => 1,
        'created_by_type' => 'publisher',
        'participants' => 0,
        'max_participants' => 50,
        'requirements' => json_encode(['Laptop', 'Basic programming knowledge']),
        'schedule' => json_encode([
            '14:30' => 'Registration',
            '15:00' => 'Opening Session',
            '16:00' => 'Break',
            '16:30' => 'Workshop Session',
            '17:30' => 'Closing'
        ])
    ];
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);
    
    if ($result) {
        $eventId = $pdo->lastInsertId();
        echo "✅ Event created successfully!\n";
        echo "Event ID: {$eventId}\n\n";
        
        // Retrieve the created event to verify
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute(['id' => $eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            echo "Created event details:\n";
            echo "  - Title: {$event['title']}\n";
            echo "  - Category: {$event['category']}\n";
            echo "  - Date: {$event['event_date']} {$event['event_time']}\n";
            echo "  - Location: {$event['location']}\n";
            echo "  - University: {$event['university_name']}\n";
            echo "  - Organizer: {$event['organizer']}\n";
            echo "  - Max Participants: {$event['max_participants']}\n";
            echo "  - Status: {$event['status']}\n";
            echo "  - Visibility: {$event['visibility']}\n";
            echo "  - Created by: {$event['created_by']} ({$event['created_by_type']})\n";
            
            // Check requirements and schedule
            if ($event['requirements']) {
                $requirements = json_decode($event['requirements'], true);
                echo "  - Requirements: " . implode(', ', $requirements) . "\n";
            }
            
            if ($event['schedule']) {
                $schedule = json_decode($event['schedule'], true);
                echo "  - Schedule:\n";
                foreach ($schedule as $time => $activity) {
                    echo "    {$time}: {$activity}\n";
                }
            }
        }
        
        // Clean up
        echo "\nCleaning up test event...\n";
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute(['id' => $eventId]);
        echo "✅ Test event removed\n";
        
    } else {
        echo "❌ Failed to create event\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>