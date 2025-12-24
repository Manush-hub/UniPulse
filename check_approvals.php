<?php
require_once 'app/init.php';

// Get PDO connection
$string = 'mysql:host='.DBHOST.';port='.DBPORT.';dbname='.DBNAME.';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];
$conn = new PDO($string, DBUSER, DBPASS, $options);

// Check all approved publishers
$stmt = $conn->query("SELECT id, society_name, approval_status, approved_by, approved_at FROM publishers WHERE approval_status = 'approved'");
$approved = $stmt->fetchAll(PDO::FETCH_OBJ);

echo '<h2>Approved Publishers Analysis</h2>';
echo '<p>Total approved publishers: ' . count($approved) . '</p>';
echo '<hr>';

echo '<h3>All Approved Publishers:</h3>';
echo '<table border="1" cellpadding="5">';
echo '<tr><th>ID</th><th>Society Name</th><th>Approved By (Moderator ID)</th><th>Approved At</th></tr>';
foreach ($approved as $pub) {
    echo '<tr>';
    echo '<td>' . $pub->id . '</td>';
    echo '<td>' . htmlspecialchars($pub->society_name) . '</td>';
    echo '<td>' . ($pub->approved_by ?: '<b>NULL</b>') . '</td>';
    echo '<td>' . ($pub->approved_at ?: '<b>NULL</b>') . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr>';
echo '<h3>Grouped by Moderator:</h3>';
$stmt = $conn->query("SELECT approved_by, COUNT(*) as count FROM publishers WHERE approval_status = 'approved' GROUP BY approved_by");
$grouped = $stmt->fetchAll(PDO::FETCH_OBJ);
echo '<table border="1" cellpadding="5">';
echo '<tr><th>Moderator ID</th><th>Approval Count</th></tr>';
foreach ($grouped as $g) {
    echo '<tr>';
    echo '<td>' . ($g->approved_by ?: '<b>NULL</b>') . '</td>';
    echo '<td>' . $g->count . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr>';
echo '<h3>Testing Dashboard Query:</h3>';
// Simulate the dashboard query for moderator ID 21
$moderatorId = 21;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'approved' AND approved_by = :moderator_id");
$stmt->execute(['moderator_id' => $moderatorId]);
$result = $stmt->fetch(PDO::FETCH_OBJ);
echo '<p>Query result for moderator ID ' . $moderatorId . ': <b>' . $result->count . '</b></p>';

// Check if there are approved publishers with NULL approved_by
$stmt = $conn->query("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'approved' AND approved_by IS NULL");
$nullCount = $stmt->fetch(PDO::FETCH_OBJ);
echo '<p>Approved publishers with NULL approved_by: <b>' . $nullCount->count . '</b></p>';
