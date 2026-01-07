<?php
require_once 'app/Core/init.php';

echo "<h2>Event Setup for Sponsor Testing</h2>";
// Use a small helper class to access Database trait methods
class DbProbe { use Database; }
$db = new DbProbe();

// Check existing events
$row = $db->getRow("SELECT COUNT(*) as total FROM events WHERE status = 'upcoming'");
$currentTotal = $row ? (int)$row->total : 0;
echo "<p>Current upcoming events: " . $currentTotal . "</p>";

if ($currentTotal < 3) {
    echo "<p style='color: orange;'><strong>Adding test events...</strong></p>";
    
    // Add test events
    $testEvents = [
        [
            'title' => 'Annual Science Fair 2025',
            'description' => 'A grand science fair showcasing innovative student projects and research.',
            'event_date' => date('Y-m-d', strtotime('+15 days')),
            'event_time' => '10:00:00',
            'location' => 'Main Campus Auditorium',
            'category' => 'academic',
            'university' => 'State University',
            'university_name' => 'State University',
            'organizer' => 'Science Department',
            'status' => 'upcoming',
            'participants' => 150,
            'max_participants' => 500,
            'accepts_donations' => 1
        ],
        [
            'title' => 'Spring Music Festival',
            'description' => 'Celebrate spring with live music performances from student bands and orchestras.',
            'event_date' => date('Y-m-d', strtotime('+22 days')),
            'event_time' => '18:00:00',
            'location' => 'Campus Open Field',
            'category' => 'cultural',
            'university' => 'Central University',
            'university_name' => 'Central University',
            'organizer' => 'Music Club',
            'status' => 'upcoming',
            'participants' => 200,
            'max_participants' => 1000,
            'accepts_donations' => 1
        ],
        [
            'title' => 'Basketball Championship 2025',
            'description' => 'Exciting inter-university basketball championship tournament.',
            'event_date' => date('Y-m-d', strtotime('+30 days')),
            'event_time' => '14:00:00',
            'location' => 'University Sports Complex',
            'category' => 'sports',
            'university' => 'Tech Institute',
            'university_name' => 'Tech Institute',
            'organizer' => 'Sports Department',
            'status' => 'upcoming',
            'participants' => 80,
            'max_participants' => 200,
            'accepts_donations' => 1
        ]
    ];
    
    foreach ($testEvents as $event) {
        $sql = "INSERT INTO events (title, description, event_date, event_time, location, category, university, university_name, organizer, status, participants, max_participants, accepts_donations, created_at, updated_at) 
                VALUES (:title, :description, :event_date, :event_time, :location, :category, :university, :university_name, :organizer, :status, :participants, :max_participants, :accepts_donations, NOW(), NOW())";
        
        $db->query($sql, $event);
        echo "<p style='color: green;'>✓ Added: " . $event['title'] . "</p>";
    }
    
    echo "<p style='color: green;'><strong>Test events added successfully!</strong></p>";
} else {
    echo "<p style='color: green;'><strong>Plenty of events already exist.</strong></p>";
}

echo "<hr>";
echo "<p><a href='/unipulse/public/sponsor/events' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Browse Events →</a></p>";
?>
