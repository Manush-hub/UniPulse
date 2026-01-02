<?php
/**
 * Debug script to check admin registration data
 */

require_once 'app/Core/init.php';

echo "<h1>Debug: Admin Recent Registrations</h1>";

try {
    // Test each model
    echo "<h2>University Users</h2>";
    $universityUser = new UniversityUser();
    $universityRegistrations = $universityUser->getRecentRegistrations(5);
    echo "<pre>";
    var_dump($universityRegistrations);
    echo "</pre>";
    
    echo "<h2>Public Users</h2>";
    $publicUser = new PublicUser();
    $publicRegistrations = $publicUser->getRecentRegistrations(5);
    echo "<pre>";
    var_dump($publicRegistrations);
    echo "</pre>";
    
    echo "<h2>Publishers</h2>";
    $publisher = new Publisher();
    $publisherRegistrations = $publisher->getRecentRegistrations(5);
    echo "<pre>";
    var_dump($publisherRegistrations);
    echo "</pre>";
    
    echo "<h2>Sponsors</h2>";
    $sponsor = new Sponsor();
    $sponsorRegistrations = $sponsor->getRecentRegistrations(5);
    echo "<pre>";
    var_dump($sponsorRegistrations);
    echo "</pre>";
    
    // Test merged data
    echo "<h2>Merged Registrations</h2>";
    $recentRegistrations = array_merge(
        is_array($universityRegistrations) ? $universityRegistrations : [],
        is_array($publicRegistrations) ? $publicRegistrations : [],
        is_array($publisherRegistrations) ? $publisherRegistrations : [],
        is_array($sponsorRegistrations) ? $sponsorRegistrations : []
    );
    
    echo "Total count: " . count($recentRegistrations) . "<br>";
    echo "<pre>";
    var_dump($recentRegistrations);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
