<?php
// Test API endpoint responses
session_start();
require_once 'app/Core/init.php';

// Set sponsor session
$_SESSION['USER'] = (object)[
    'id' => 999,
    'type' => 'sponsor',
    'company_name' => 'Test Sponsor',
    'email' => 'test@sponsor.com'
];

echo "<h1>Testing Sponsor API Endpoints</h1>";

// Test 1: getUserProfile
echo "<h2>1. Testing getUserProfile</h2>";
echo "<a href='/unipulse/public/sponsor/dashboard/getUserProfile' target='_blank'>Open in new tab</a><br>";

// Test 2: getNotifications  
echo "<h2>2. Testing getNotifications</h2>";
echo "<a href='/unipulse/public/sponsor/dashboard/getNotifications' target='_blank'>Open in new tab</a><br>";

// Test 3: getEvents
echo "<h2>3. Testing getEvents</h2>";
echo "<a href='/unipulse/public/sponsor/events/getEvents' target='_blank'>Open in new tab</a><br>";

echo "<hr>";
echo "<h2>Expected Result:</h2>";
echo "<p>All three endpoints should return valid JSON (no HTML, no PHP errors)</p>";
echo "<p>Check browser console on the events page for any remaining errors.</p>";
