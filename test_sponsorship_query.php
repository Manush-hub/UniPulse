<?php
require_once 'app/Core/config.php';
session_start();

// Simulate logged in publisher
if (!isset($_SESSION['user_id'])) {
    echo "Please log in as a publisher first.<br>";
    echo "<a href='/unipulse/public/signin'>Login</a>";
    exit;
}

$currentUserId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

echo "<h2>Sponsorship Query Test</h2>";
echo "<p>User ID: $currentUserId</p>";
echo "<p>User Type: $userType</p>";

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check events created by this publisher
    $stmt = $pdo->prepare("SELECT id, title, created_by FROM events WHERE created_by = ? LIMIT 5");
    $stmt->execute([$currentUserId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Events Created by This Publisher:</h3>";
    if (empty($events)) {
        echo "<p>No events found for this publisher.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Event ID</th><th>Title</th><th>Created By</th></tr>";
        foreach ($events as $event) {
            echo "<tr>";
            echo "<td>{$event['id']}</td>";
            echo "<td>{$event['title']}</td>";
            echo "<td>{$event['created_by']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check event_sponsorship_packages
    $stmt = $pdo->prepare("
        SELECT esp.*, e.title as event_title 
        FROM event_sponsorship_packages esp
        INNER JOIN events e ON esp.event_id = e.id
        WHERE e.created_by = ?
        LIMIT 5
    ");
    $stmt->execute([$currentUserId]);
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Sponsorship Packages for This Publisher's Events:</h3>";
    if (empty($packages)) {
        echo "<p>No sponsorship packages found.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Package ID</th><th>Event</th><th>Package Name</th><th>Type</th><th>Amount</th></tr>";
        foreach ($packages as $pkg) {
            echo "<tr>";
            echo "<td>{$pkg['id']}</td>";
            echo "<td>{$pkg['event_title']}</td>";
            echo "<td>{$pkg['package_name']}</td>";
            echo "<td>{$pkg['package_type']}</td>";
            echo "<td>{$pkg['amount']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check sponsorships
    $stmt = $pdo->prepare("
        SELECT 
            es.*,
            e.title as event_title,
            e.created_by,
            esp.package_name,
            esp.package_type,
            s.company_name as sponsor_name
        FROM event_sponsorships es
        INNER JOIN events e ON es.event_id = e.id
        INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
        LEFT JOIN sponsors s ON es.sponsor_id = s.id AND es.sponsor_type = 'sponsor'
        WHERE e.created_by = ?
        ORDER BY es.created_at DESC
    ");
    $stmt->execute([$currentUserId]);
    $sponsorships = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Sponsorship Requests for This Publisher:</h3>";
    if (empty($sponsorships)) {
        echo "<p>No sponsorship requests found.</p>";
        echo "<p><strong>This might mean:</strong></p>";
        echo "<ul>";
        echo "<li>No sponsors have submitted sponsorship requests yet</li>";
        echo "<li>The events don't have sponsorship packages enabled</li>";
        echo "<li>The sponsorships table is empty</li>";
        echo "</ul>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Event</th><th>Package</th><th>Sponsor</th><th>Amount</th><th>Status</th><th>Created</th></tr>";
        foreach ($sponsorships as $sp) {
            echo "<tr>";
            echo "<td>{$sp['id']}</td>";
            echo "<td>{$sp['event_title']}</td>";
            echo "<td>{$sp['package_name']} ({$sp['package_type']})</td>";
            echo "<td>{$sp['sponsor_name']}</td>";
            echo "<td>LKR " . number_format($sp['amount'], 2) . "</td>";
            echo "<td>{$sp['status']}</td>";
            echo "<td>{$sp['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check all sponsorships in the database
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM event_sponsorships");
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<h3>Total Sponsorships in Database: $totalCount</h3>";
    
    if ($totalCount > 0) {
        $stmt = $pdo->query("
            SELECT es.*, e.title, e.created_by, s.company_name
            FROM event_sponsorships es
            LEFT JOIN events e ON es.event_id = e.id
            LEFT JOIN sponsors s ON es.sponsor_id = s.id
            ORDER BY es.created_at DESC
            LIMIT 5
        ");
        $allSponsорships = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Event ID</th><th>Event Title</th><th>Event Created By</th><th>Sponsor</th><th>Status</th></tr>";
        foreach ($allSponsорships as $sp) {
            echo "<tr>";
            echo "<td>{$sp['id']}</td>";
            echo "<td>{$sp['event_id']}</td>";
            echo "<td>{$sp['title']}</td>";
            echo "<td>{$sp['created_by']}</td>";
            echo "<td>{$sp['company_name']}</td>";
            echo "<td>{$sp['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
