<?php
// Check comments in database
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;port=8889;dbname=unipulse_db', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Comments Database Debug</h2>";
    
    // Check total comments
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM event_comments");
    $total = $stmt->fetch();
    echo "<p>Total comments in database: " . $total['total'] . "</p>";
    
    // Check comments by event
    $stmt = $pdo->query("SELECT event_id, COUNT(*) as count FROM event_comments GROUP BY event_id");
    $events = $stmt->fetchAll();
    
    echo "<h3>Comments by Event ID:</h3>";
    foreach ($events as $event) {
        echo "<p>Event {$event['event_id']}: {$event['count']} comments</p>";
    }
    
    // Show recent comments
    echo "<h3>Recent Comments:</h3>";
    $stmt = $pdo->query("
        SELECT c.*, e.title as event_title 
        FROM event_comments c 
        LEFT JOIN events e ON c.event_id = e.id 
        ORDER BY c.created_at DESC 
        LIMIT 10
    ");
    $comments = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Event ID</th><th>Event Title</th><th>User Type</th><th>Comment</th><th>Rating</th><th>Created</th></tr>";
    foreach ($comments as $comment) {
        echo "<tr>";
        echo "<td>{$comment['id']}</td>";
        echo "<td>{$comment['event_id']}</td>";
        echo "<td>{$comment['event_title']}</td>";
        echo "<td>{$comment['user_type']}</td>";
        echo "<td>" . htmlspecialchars($comment['comment_text']) . "</td>";
        echo "<td>{$comment['rating']}</td>";
        echo "<td>{$comment['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>