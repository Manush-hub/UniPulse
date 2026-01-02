<?php
/**
 * Debug: Check what's happening in the admin controller
 */

require_once 'app/Core/init.php';

echo "<h1>Debug: Admin Recent Registrations Flow</h1>";

try {
    echo "<h2>Step 1: Check Database Tables</h2>";
    $db = new Database();
    
    $tables = ['university_users', 'public_users', 'publishers', 'sponsors'];
    foreach ($tables as $table) {
        $result = $db->query("SELECT COUNT(*) as count FROM {$table}");
        echo "{$table}: " . ($result ? $result[0]->count : '0') . " records<br>";
    }
    
    echo "<hr><h2>Step 2: Test Model Methods</h2>";
    
    echo "<h3>UniversityUser->getRecentRegistrations(5)</h3>";
    $universityUser = new UniversityUser();
    $uniResult = $universityUser->getRecentRegistrations(5);
    echo "Type: " . gettype($uniResult) . "<br>";
    echo "Is Array: " . (is_array($uniResult) ? 'YES' : 'NO') . "<br>";
    echo "Count: " . (is_array($uniResult) ? count($uniResult) : '0') . "<br>";
    if (is_array($uniResult) && count($uniResult) > 0) {
        echo "<pre>";
        print_r($uniResult[0]);
        echo "</pre>";
    }
    
    echo "<h3>PublicUser->getRecentRegistrations(5)</h3>";
    $publicUser = new PublicUser();
    $pubResult = $publicUser->getRecentRegistrations(5);
    echo "Type: " . gettype($pubResult) . "<br>";
    echo "Is Array: " . (is_array($pubResult) ? 'YES' : 'NO') . "<br>";
    echo "Count: " . (is_array($pubResult) ? count($pubResult) : '0') . "<br>";
    if (is_array($pubResult) && count($pubResult) > 0) {
        echo "<pre>";
        print_r($pubResult[0]);
        echo "</pre>";
    }
    
    echo "<h3>Publisher->getRecentRegistrations(5)</h3>";
    $publisher = new Publisher();
    $publisherResult = $publisher->getRecentRegistrations(5);
    echo "Type: " . gettype($publisherResult) . "<br>";
    echo "Is Array: " . (is_array($publisherResult) ? 'YES' : 'NO') . "<br>";
    echo "Count: " . (is_array($publisherResult) ? count($publisherResult) : '0') . "<br>";
    if (is_array($publisherResult) && count($publisherResult) > 0) {
        echo "<pre>";
        print_r($publisherResult[0]);
        echo "</pre>";
    }
    
    echo "<h3>Sponsor->getRecentRegistrations(5)</h3>";
    $sponsor = new Sponsor();
    $sponsorResult = $sponsor->getRecentRegistrations(5);
    echo "Type: " . gettype($sponsorResult) . "<br>";
    echo "Is Array: " . (is_array($sponsorResult) ? 'YES' : 'NO') . "<br>";
    echo "Count: " . (is_array($sponsorResult) ? count($sponsorResult) : '0') . "<br>";
    if (is_array($sponsorResult) && count($sponsorResult) > 0) {
        echo "<pre>";
        print_r($sponsorResult[0]);
        echo "</pre>";
    }
    
    echo "<hr><h2>Step 3: Test Merge Logic</h2>";
    $recentRegistrations = array_merge(
        is_array($uniResult) ? $uniResult : [],
        is_array($pubResult) ? $pubResult : [],
        is_array($publisherResult) ? $publisherResult : [],
        is_array($sponsorResult) ? $sponsorResult : []
    );
    
    echo "Merged count: " . count($recentRegistrations) . "<br>";
    
    if (count($recentRegistrations) > 0) {
        echo "<h3>First item in merged array:</h3>";
        echo "<pre>";
        print_r($recentRegistrations[0]);
        echo "</pre>";
    }
    
    echo "<hr><h2>Step 4: Check View Data Variable</h2>";
    echo "If merged count > 0, then data should appear in the view.<br>";
    echo "Variable that will be passed to view: \$recent_registrations<br>";
    echo "Count: " . count($recentRegistrations) . "<br>";
    
    if (count($recentRegistrations) > 0) {
        echo "<h3 style='color: green;'>✓ Data is available and should display!</h3>";
    } else {
        echo "<h3 style='color: red;'>✗ No data found - need to add test data</h3>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
