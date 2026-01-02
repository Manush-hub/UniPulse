<?php
/**
 * Simple test to check if models can fetch data
 */

require_once 'app/Core/init.php';

echo "<h1>Testing Model Data Fetch</h1>";

try {
    echo "<h2>Testing UniversityUser Model</h2>";
    $universityUser = new UniversityUser();
    $result = $universityUser->getRecentRegistrations(5);
    echo "Result type: " . gettype($result) . "<br>";
    if (is_array($result)) {
        echo "Count: " . count($result) . "<br>";
        if (count($result) > 0) {
            echo "<pre>";
            print_r($result[0]);
            echo "</pre>";
        }
    } else {
        echo "Result is FALSE<br>";
    }
    
    echo "<h2>Testing PublicUser Model</h2>";
    $publicUser = new PublicUser();
    $result = $publicUser->getRecentRegistrations(5);
    echo "Result type: " . gettype($result) . "<br>";
    if (is_array($result)) {
        echo "Count: " . count($result) . "<br>";
        if (count($result) > 0) {
            echo "<pre>";
            print_r($result[0]);
            echo "</pre>";
        }
    } else {
        echo "Result is FALSE<br>";
    }
    
    echo "<h2>Testing Publisher Model</h2>";
    $publisher = new Publisher();
    $result = $publisher->getRecentRegistrations(5);
    echo "Result type: " . gettype($result) . "<br>";
    if (is_array($result)) {
        echo "Count: " . count($result) . "<br>";
        if (count($result) > 0) {
            echo "<pre>";
            print_r($result[0]);
            echo "</pre>";
        }
    } else {
        echo "Result is FALSE<br>";
    }
    
    echo "<h2>Testing Sponsor Model</h2>";
    $sponsor = new Sponsor();
    $result = $sponsor->getRecentRegistrations(5);
    echo "Result type: " . gettype($result) . "<br>";
    if (is_array($result)) {
        echo "Count: " . count($result) . "<br>";
        if (count($result) > 0) {
            echo "<pre>";
            print_r($result[0]);
            echo "</pre>";
        }
    } else {
        echo "Result is FALSE<br>";
    }
    
    echo "<h2>Success!</h2>";
    echo "<p>If you see data above, the models are working correctly.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
