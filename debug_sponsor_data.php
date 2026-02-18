<!DOCTYPE html>
<html>
<head>
    <title>Debug Sponsor Events Data</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        pre { background: #f0f0f0; padding: 15px; border-radius: 4px; overflow-x: auto; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        h3 { color: #667eea; margin-top: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <h1>🔍 Sponsor Events Debug</h1>
    
    <?php
    session_start();
    require_once 'app/Core/init.php';
    
    // Set sponsor session
    if (!isset($_SESSION['USER']) || $_SESSION['USER']->type !== 'sponsor') {
        echo "<div class='debug-box error'>";
        echo "<h3>⚠️ No Sponsor Session Found</h3>";
        echo "<p>Creating temporary sponsor session...</p>";
        $_SESSION['USER'] = (object)[
            'id' => 999,
            'type' => 'sponsor',
            'company_name' => 'Debug Sponsor',
            'email' => 'debug@sponsor.com'
        ];
        echo "<p class='success'>✓ Temporary session created</p>";
        echo "</div>";
    }
    
    $eventModel = new Event();
    $currentUser = AuthService::getCurrentUser();
    
    echo "<div class='debug-box'>";
    echo "<h2>1. Current User</h2>";
    echo "<pre>";
    print_r($currentUser);
    echo "</pre>";
    echo "</div>";
    
    // Test getAllEvents
    echo "<div class='debug-box'>";
    echo "<h2>2. Testing getAllEvents()</h2>";
    try {
        $filters = ['limit' => 6, 'offset' => 0];
        $eventsObj = $eventModel->getAllEvents($filters, $currentUser);
        
        echo "<p><strong>Return Type:</strong> " . gettype($eventsObj) . "</p>";
        echo "<p><strong>Count:</strong> " . (is_array($eventsObj) ? count($eventsObj) : 0) . "</p>";
        
        if (is_array($eventsObj) && count($eventsObj) > 0) {
            echo "<p class='success'>✓ Events found!</p>";
            echo "<p><strong>First Event Type:</strong> " . gettype($eventsObj[0]) . "</p>";
            echo "<h3>First Event (Raw):</h3><pre>";
            print_r($eventsObj[0]);
            echo "</pre>";
            
            // Test conversion
            echo "<h3>First Event (Converted to Array):</h3><pre>";
            $converted = is_object($eventsObj[0]) ? (array) $eventsObj[0] : $eventsObj[0];
            print_r($converted);
            echo "</pre>";
        } else {
            echo "<p class='warning'>⚠️ No events returned</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Test sponsorship events
    echo "<div class='debug-box'>";
    echo "<h2>3. Testing Sponsorship Events Query</h2>";
    try {
        $sql = "SELECT DISTINCT e.*, 
                COUNT(DISTINCT esp.id) as package_count,
                SUM(esp.available_slots - esp.filled_slots) as total_slots_available
                FROM events e
                INNER JOIN event_sponsorship_packages esp ON e.id = esp.event_id
                WHERE e.accepts_sponsorships = 1 
                AND e.is_deleted = 0
                AND esp.is_active = 1
                AND (esp.available_slots - esp.filled_slots) > 0
                AND (e.visibility = 'public' OR e.visibility = 'university-only')
                AND e.status IN ('upcoming', 'ongoing')
                AND e.event_date >= CURDATE()
                GROUP BY e.id
                ORDER BY e.event_date ASC
                LIMIT 12";
        
        $sponsorshipEventsObj = $eventModel->query($sql);
        
        echo "<p><strong>Return Type:</strong> " . gettype($sponsorshipEventsObj) . "</p>";
        echo "<p><strong>Count:</strong> " . (is_array($sponsorshipEventsObj) ? count($sponsorshipEventsObj) : 0) . "</p>";
        
        if ($sponsorshipEventsObj && is_array($sponsorshipEventsObj) && count($sponsorshipEventsObj) > 0) {
            echo "<p class='success'>✓ Sponsorship events found!</p>";
            echo "<h3>First Sponsorship Event:</h3><pre>";
            print_r($sponsorshipEventsObj[0]);
            echo "</pre>";
            
            // Test conversion
            $converted = (array) $sponsorshipEventsObj[0];
            echo "<h3>Converted to Array:</h3><pre>";
            print_r($converted);
            echo "</pre>";
        } else {
            echo "<p class='warning'>⚠️ No sponsorship events found</p>";
            echo "<p><strong>Tip:</strong> Create test data using the SQL in SPONSORSHIP_TEST_GUIDE.md</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    // Test what the controller would send
    echo "<div class='debug-box'>";
    echo "<h2>4. Simulated Controller Data</h2>";
    try {
        $filters = ['limit' => 6, 'offset' => 0];
        $eventsObj = $eventModel->getAllEvents($filters, $currentUser);
        
        // Convert to arrays like the controller does
        $events = [];
        if ($eventsObj && is_array($eventsObj)) {
            foreach ($eventsObj as $event) {
                $events[] = is_object($event) ? (array) $event : $event;
            }
        }
        
        echo "<h3>Converted Events Array:</h3>";
        echo "<p><strong>Count:</strong> " . count($events) . "</p>";
        if (count($events) > 0) {
            echo "<p class='success'>✓ Array conversion successful!</p>";
            echo "<h4>First Event (Array):</h4><pre>";
            print_r($events[0]);
            echo "</pre>";
        }
        
        // Simulate serverData
        $serverData = [
            'events' => $events,
            'currentPage' => 1,
            'totalPages' => ceil(count($events) / 6),
            'filters' => [],
            'apiEndpoint' => '/unipulse/public/sponsor/events/getEvents'
        ];
        
        echo "<h3>Simulated window.serverData:</h3>";
        echo "<pre>" . json_encode($serverData, JSON_PRETTY_PRINT) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    ?>
    
    <div class="debug-box">
        <h2>5. Next Steps</h2>
        <ul>
            <li>If events are shown above, the controller is working correctly</li>
            <li>Check browser console for JavaScript errors on <a href="/unipulse/public/Sponsor/events" target="_blank">Sponsor Events Page</a></li>
            <li>Verify that window.serverData is populated in browser console</li>
            <li>Check if events-app.js is loading correctly</li>
        </ul>
    </div>
</body>
</html>
