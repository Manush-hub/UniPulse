<?php
require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

$db = new Database();
$columns = $db->query("DESCRIBE events");

echo "<h2>Events Table Structure</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td><strong>{$col->Field}</strong></td>";
    echo "<td>{$col->Type}</td>";
    echo "<td>{$col->Null}</td>";
    echo "<td>{$col->Key}</td>";
    echo "<td>{$col->Default}</td>";
    echo "</tr>";
}
echo "</table>";
?>
