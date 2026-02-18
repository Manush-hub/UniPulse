<?php
// Debug file to check what data is being passed to the sponsor events page
session_start();

require_once 'app/Core/init.php';

// Simulate sponsor user
$_SESSION['USER'] = (object)[
    'id' => 1,
    'type' => 'sponsor',
    'company_name' => 'Test Sponsor',
    'email' => 'sponsor@test.com'
];

$controller = new SponsorEvents();

// Capture the output
ob_start();

// Mock the view method to just dump data
class SponsorEventsDebug extends SponsorEvents {
    public function debugIndex() {
        // Call parent index but capture data
        try {
            $filters = [];
            $page = 1;
            $limit = 6;
            $offset = 0;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            $currentUser = AuthService::getCurrentUser();
            
            $eventsObj = $this->eventModel->getAllEvents($filters, $currentUser);
            
            echo "<h2>Debug: Sponsor Events Data</h2>";
            echo "<h3>Current User:</h3><pre>";
            print_r($currentUser);
            echo "</pre>";
            
            echo "<h3>Events Object Type:</h3>";
            echo "<p>" . gettype($eventsObj) . "</p>";
            
            echo "<h3>Events Count:</h3>";
            echo "<p>" . (is_array($eventsObj) ? count($eventsObj) : 0) . "</p>";
            
            if (is_array($eventsObj) && count($eventsObj) > 0) {
                echo "<h3>First Event Type:</h3>";
                echo "<p>" . gettype($eventsObj[0]) . "</p>";
                
                echo "<h3>First Event Data:</h3><pre>";
                print_r($eventsObj[0]);
                echo "</pre>";
            }
            
            // Check sponsorship events
            echo "<h3>Sponsorship Events:</h3>";
            $sponsorshipEvents = $this->getEventsWithSponsorships($currentUser);
            echo "<p>Count: " . count($sponsorshipEvents) . "</p>";
            if (!empty($sponsorshipEvents)) {
                echo "<pre>";
                print_r($sponsorshipEvents[0]);
                echo "</pre>";
            }
            
        } catch (Exception $e) {
            echo "<h3>Error:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }
}

$debug = new SponsorEventsDebug();
$debug->debugIndex();
