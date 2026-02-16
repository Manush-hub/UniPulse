<?php
$conn = new PDO('mysql:host=localhost;port=8889;dbname=unipulse_db', 'root', 'root');

$stmt = $conn->query("SELECT id, title, image_url FROM events WHERE id = 118");
$event = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Event 118 details:\n";
print_r($event);
echo "\n\nimage_url value: ";
var_dump($event['image_url']);
echo "\n\nis empty: " . (empty($event['image_url']) ? 'YES' : 'NO');
echo "</pre>";
