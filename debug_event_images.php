<!DOCTYPE html>
<html>
<head>
    <title>Event Image Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 30px 0; padding: 20px; border: 2px solid #ddd; border-radius: 8px; }
        .test-section h2 { margin-top: 0; color: #333; }
        .image-test { margin: 15px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .image-test img { max-width: 300px; height: auto; border: 1px solid #ccc; }
        .success { color: green; }
        .error { color: red; }
        .path { font-family: monospace; background: #fff; padding: 5px; border: 1px solid #ddd; display: inline-block; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Event Image Upload & Display Debug</h1>
    
    <?php
    // Test 1: Check PHP working directory
    echo '<div class="test-section">';
    echo '<h2>Test 1: PHP Environment</h2>';
    echo '<p><strong>Current Working Directory:</strong> <span class="path">' . getcwd() . '</span></p>';
    echo '<p><strong>Script Location:</strong> <span class="path">' . __FILE__ . '</span></p>';
    echo '<p><strong>Document Root:</strong> <span class="path">' . $_SERVER['DOCUMENT_ROOT'] . '</span></p>';
    echo '</div>';
    
    // Test 2: Check if upload directory exists
    echo '<div class="test-section">';
    echo '<h2>Test 2: Upload Directory</h2>';
    $uploadDir = __DIR__ . '/public/uploads/event_covers/';
    echo '<p><strong>Upload Directory Path:</strong> <span class="path">' . $uploadDir . '</span></p>';
    echo '<p><strong>Directory Exists:</strong> <span class="' . (is_dir($uploadDir) ? 'success' : 'error') . '">' . (is_dir($uploadDir) ? '✓ YES' : '✗ NO') . '</span></p>';
    
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . 'event_cover_*.*');
        echo '<p><strong>Number of Images:</strong> ' . count($files) . '</p>';
        echo '<p><strong>Images Found:</strong></p><ul>';
        foreach (array_slice($files, 0, 5) as $file) {
            $filename = basename($file);
            echo '<li>' . $filename . ' <span class="success">(' . number_format(filesize($file) / 1024, 2) . ' KB)</span></li>';
        }
        if (count($files) > 5) {
            echo '<li><em>... and ' . (count($files) - 5) . ' more</em></li>';
        }
        echo '</ul>';
    }
    echo '</div>';
    
    // Test 3: Check database paths
    echo '<div class="test-section">';
    echo '<h2>Test 3: Database Event Records</h2>';
    
    require_once 'app/Core/init.php';
    
    try {
        $eventModel = new Event();
        $events = $eventModel->getAllEvents(['limit' => 5]);
        
        if ($events) {
            echo '<p><strong>Found ' . count($events) . ' events in database</strong></p>';
            
            foreach ($events as $event) {
                echo '<div class="image-test">';
                echo '<h3>' . htmlspecialchars($event->title) . ' (ID: ' . $event->id . ')</h3>';
                echo '<p><strong>Database cover_image:</strong> <span class="path">' . ($event->cover_image ?? 'NULL') . '</span></p>';
                echo '<p><strong>Database image_url:</strong> <span class="path">' . ($event->image_url ?? 'NULL') . '</span></p>';
                
                $imagePath = $event->cover_image ?? $event->image_url;
                
                if ($imagePath) {
                    $fullFilePath = __DIR__ . '/public/' . $imagePath;
                    $webPath = '/unipulse/public/' . $imagePath;
                    
                    echo '<p><strong>Full File Path:</strong> <span class="path">' . $fullFilePath . '</span></p>';
                    echo '<p><strong>File Exists:</strong> <span class="' . (file_exists($fullFilePath) ? 'success' : 'error') . '">' . (file_exists($fullFilePath) ? '✓ YES' : '✗ NO') . '</span></p>';
                    echo '<p><strong>Web Path:</strong> <span class="path">' . $webPath . '</span></p>';
                    
                    if (file_exists($fullFilePath)) {
                        echo '<p><strong>Display Test:</strong></p>';
                        echo '<img src="' . $webPath . '" alt="' . htmlspecialchars($event->title) . '">';
                    } else {
                        echo '<p class="error">✗ Image file not found at expected location!</p>';
                    }
                } else {
                    echo '<p class="error">✗ No image path stored in database</p>';
                }
                
                echo '</div>';
            }
        } else {
            echo '<p class="error">No events found in database</p>';
        }
    } catch (Exception $e) {
        echo '<p class="error">Database Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
    
    echo '</div>';
    
    // Test 4: Test JavaScript path construction
    echo '<div class="test-section">';
    echo '<h2>Test 4: JavaScript Path Construction</h2>';
    echo '<p>Testing the same path logic that JavaScript uses:</p>';
    
    $testCoverImage = 'uploads/event_covers/event_cover_68f79c85797e0.jpg';
    echo '<p><strong>Sample DB Path:</strong> <span class="path">' . $testCoverImage . '</span></p>';
    
    if (strpos($testCoverImage, 'http') === 0) {
        $jsPath = $testCoverImage;
    } else {
        $jsPath = '/unipulse/public/' . $testCoverImage;
    }
    
    echo '<p><strong>Constructed JS Path:</strong> <span class="path">' . $jsPath . '</span></p>';
    echo '<p><strong>Display Test:</strong></p>';
    echo '<img src="' . $jsPath . '" alt="Test" style="max-width: 300px;">';
    echo '</div>';
    ?>
    
</body>
</html>
