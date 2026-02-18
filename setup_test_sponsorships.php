<?php
/**
 * Script to add test sponsorship packages to existing events
 * This will help test the sponsor events page
 */

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

echo "<h1>Add Test Sponsorship Packages</h1>";

try {
    $db = new Database();
    
    // First, check if we have any upcoming events that can accept sponsorships
    echo "<h2>Step 1: Finding upcoming events</h2>";
    $events = $db->query("SELECT id, title, event_date, accepts_sponsorships 
                          FROM events 
                          WHERE is_deleted = 0 
                          AND status IN ('upcoming', 'ongoing') 
                          AND event_date >= CURDATE()
                          ORDER BY event_date ASC
                          LIMIT 5");
    
    if (!$events || count($events) == 0) {
        echo "<p style='color: orange;'>No upcoming events found. You need to create some events first.</p>";
        exit;
    }
    
    echo "<p>Found " . count($events) . " upcoming events:</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Accepts Sponsorships</th></tr>";
    foreach ($events as $event) {
        echo "<tr>";
        echo "<td>" . $event->id . "</td>";
        echo "<td>" . $event->title . "</td>";
        echo "<td>" . $event->event_date . "</td>";
        echo "<td>" . ($event->accepts_sponsorships ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 2: Enable accepts_sponsorships for these events
    echo "<h2>Step 2: Enabling sponsorships for these events</h2>";
    foreach ($events as $event) {
        if ($event->accepts_sponsorships == 0) {
            $updateSql = "UPDATE events SET accepts_sponsorships = 1 WHERE id = ?";
            $db->query($updateSql, [$event->id]);
            echo "✓ Enabled sponsorships for event ID " . $event->id . "<br>";
        } else {
            echo "✓ Event ID " . $event->id . " already accepts sponsorships<br>";
        }
    }
    
    // Step 3: Add sponsorship packages for each event
    echo "<h2>Step 3: Adding sponsorship packages</h2>";
    
    $packageTypes = [
        [
            'type' => 'platinum',
            'title' => 'Platinum Sponsor',
            'price' => 50000.00,
            'description' => 'Premier sponsorship package with maximum visibility and benefits',
            'benefits' => json_encode([
                'Logo placement on all event materials',
                'Dedicated booth space at prime location',
                'Speaking opportunity at event',
                'Social media mentions (5 posts)',
                'VIP passes (10)',
                'Brand integration in event activities'
            ]),
            'slots' => 2
        ],
        [
            'type' => 'gold',
            'title' => 'Gold Sponsor',
            'price' => 30000.00,
            'description' => 'Premium sponsorship package with excellent visibility',
            'benefits' => json_encode([
                'Logo on event materials',
                'Booth space at event',
                'Social media mentions (3 posts)',
                'VIP passes (5)',
                'Brand logo on website'
            ]),
            'slots' => 3
        ],
        [
            'type' => 'silver',
            'title' => 'Silver Sponsor',
            'price' => 15000.00,
            'description' => 'Standard sponsorship package with good exposure',
            'benefits' => json_encode([
                'Logo on select materials',
                'Booth space',
                'Social media mention (1 post)',
                'Regular passes (3)'
            ]),
            'slots' => 5
        ]
    ];
    
    foreach ($events as $event) {
        echo "<br><strong>Adding packages for: " . htmlspecialchars($event->title) . "</strong><br>";
        
        // Check if packages already exist
        $existingPackages = $db->query("SELECT id FROM event_sponsorship_packages WHERE event_id = ?", [$event->id]);
        
        if ($existingPackages && count($existingPackages) > 0) {
            echo "  ⚠ Packages already exist for this event (skipping)<br>";
            continue;
        }
        
        foreach ($packageTypes as $package) {
            $insertSql = "INSERT INTO event_sponsorship_packages 
                         (event_id, package_type, package_title, description, price, benefits, available_slots, filled_slots, is_active, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1, NOW(), NOW())";
            
            $result = $db->query($insertSql, [
                $event->id,
                $package['type'],
                $package['title'],
                $package['description'],
                $package['price'],
                $package['benefits'],
                $package['slots']
            ]);
            
            if ($result) {
                echo "  ✓ Added " . $package['title'] . " (" . $package['slots'] . " slots, Rs. " . number_format($package['price'], 2) . ")<br>";
            } else {
                echo "  ✗ Failed to add " . $package['title'] . "<br>";
            }
        }
    }
    
    // Step 4: Verify the data
    echo "<h2>Step 4: Verification</h2>";
    $verificationSql = "SELECT e.id, e.title, e.event_date,
                        COUNT(DISTINCT esp.id) as package_count,
                        SUM(esp.available_slots - esp.filled_slots) as total_slots_available
                        FROM events e
                        INNER JOIN event_sponsorship_packages esp ON e.id = esp.event_id
                        WHERE e.accepts_sponsorships = 1 
                        AND e.is_deleted = 0
                        AND esp.is_active = 1
                        AND (esp.available_slots - esp.filled_slots) > 0
                        AND e.status IN ('upcoming', 'ongoing')
                        AND e.event_date >= CURDATE()
                        GROUP BY e.id
                        ORDER BY e.event_date ASC";
    
    $verificationResults = $db->query($verificationSql);
    
    if ($verificationResults && count($verificationResults) > 0) {
        echo "<p style='color: green;'><strong>✓ SUCCESS!</strong> Found " . count($verificationResults) . " events with sponsorship packages:</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Event ID</th><th>Title</th><th>Date</th><th>Packages</th><th>Available Slots</th></tr>";
        foreach ($verificationResults as $result) {
            echo "<tr>";
            echo "<td>" . $result->id . "</td>";
            echo "<td>" . htmlspecialchars($result->title) . "</td>";
            echo "<td>" . $result->event_date . "</td>";
            echo "<td>" . $result->package_count . "</td>";
            echo "<td>" . $result->total_slots_available . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<br><p style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 5px;'>";
        echo "<strong>✓ Test data created successfully!</strong><br>";
        echo "You can now visit the Sponsor Events page to see the sponsorship opportunities.<br>";
        echo "<a href='/unipulse/public/sponsor/events' style='color: #155724; font-weight: bold;'>→ Go to Sponsor Events Page</a>";
        echo "</p>";
    } else {
        echo "<p style='color: red;'>✗ No sponsorship events found after setup. Please check the database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
