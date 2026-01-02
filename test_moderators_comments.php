<?php
require "app/Core/init.php";

// Use the Database trait
class TestDB {
    use Database;
    
    public function getConnection() {
        return $this->connect();
    }
}

$db = new TestDB();
$pdo = $db->getConnection();

echo "<h2>Moderators:</h2>";
$stmt = $pdo->query('SELECT id, full_name, email, university FROM moderators');
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>University</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>{$row['id']}</td><td>{$row['full_name']}</td><td>{$row['email']}</td><td>{$row['university']}</td></tr>";
}
echo "</table>";

echo "<h2>Comments with Event Universities:</h2>";
$stmt2 = $pdo->query('SELECT c.id, c.comment_text, e.title as event_title, e.university FROM event_comments c INNER JOIN events e ON c.event_id = e.id WHERE c.is_deleted = 0');
echo "<table border='1'><tr><th>Comment ID</th><th>Comment</th><th>Event</th><th>University</th></tr>";
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars(substr($row['comment_text'], 0, 50)) . "...</td><td>{$row['event_title']}</td><td>{$row['university']}</td></tr>";
}
echo "</table>";
