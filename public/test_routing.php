<?php
// Test the messages routing

echo "<h1>Messages Routing Test</h1>";

// Simulate the routing for /publisher/messages/details/123
$_GET['url'] = 'publisher/messages/details/123';

// Load the core files
require_once '../app/Core/init.php';

echo "<h2>1. URL Parsing Test</h2>";
echo "URL: " . $_GET['url'] . "<br>";

// Parse URL manually to see what's happening
$url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
echo "URL parts: " . print_r($url, true) . "<br>";

if (isset($url[0]) && $url[0] === 'publisher') {
    echo "✓ Role detected: publisher<br>";
    $controller = isset($url[1]) ? ucfirst($url[1]) : 'Dashboard';
    echo "Controller: " . $controller . "<br>";
    
    if (isset($url[2])) {
        $method = $url[2];
        echo "Method: " . $method . "<br>";
        unset($url[2]);
    }
    
    unset($url[0], $url[1]);
    $params = $url ? array_values($url) : [];
    echo "Params: " . print_r($params, true) . "<br>";
}

echo "<h2>2. Check Controller File</h2>";
$controllerFile = "../app/controllers/Publisher/Messages.php";
if (file_exists($controllerFile)) {
    echo "✓ Controller file exists: " . $controllerFile . "<br>";
    
    require_once $controllerFile;
    if (class_exists('PublisherMessages')) {
        echo "✓ PublisherMessages class exists<br>";
        
        $controller = new PublisherMessages();
        if (method_exists($controller, 'details')) {
            echo "✓ details method exists<br>";
        } else {
            echo "❌ details method does not exist<br>";
        }
    } else {
        echo "❌ PublisherMessages class does not exist<br>";
    }
} else {
    echo "❌ Controller file does not exist: " . $controllerFile . "<br>";
}

echo "<h2>3. Simulate AJAX Request</h2>";
echo "Setting AJAX headers...<br>";
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

// Try to load the controller and call the method manually
try {
    if (class_exists('PublisherMessages')) {
        echo "Calling PublisherMessages->details('123')...<br>";
        
        // Capture output
        ob_start();
        $controller = new PublisherMessages();
        $controller->details('123');
        $output = ob_get_clean();
        
        echo "<h3>Controller Output:</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error calling controller: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?>