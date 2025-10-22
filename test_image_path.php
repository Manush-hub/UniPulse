<?php
// Test to verify image paths
$testImages = [
    'uploads/event_covers/event_cover_68f79c85797e0.jpg',
    'uploads/event_covers/event_cover_68f76fd2c6457.jpg',
    'uploads/event_covers/event_cover_68f27bc3120ad.jpg',
];

echo "<h2>Image Path Test</h2>";
echo "<p>Testing if images are accessible from the correct paths:</p>";

foreach ($testImages as $imagePath) {
    $fullPath = __DIR__ . '/public/' . $imagePath;
    $webPath = '/unipulse/public/' . $imagePath;
    
    echo "<div style='margin: 20px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<h3>Testing: $imagePath</h3>";
    echo "<p>Full Path: $fullPath</p>";
    echo "<p>File exists: " . (file_exists($fullPath) ? '✓ YES' : '✗ NO') . "</p>";
    echo "<p>Web Path: $webPath</p>";
    
    if (file_exists($fullPath)) {
        echo "<img src='$webPath' alt='Test' style='max-width: 300px; height: auto;'>";
    }
    echo "</div>";
}
?>
