<?php
require_once 'app/Core/Database.php';

$db = Database::getInstance()->getConnection();

$result = $db->query('DESCRIBE events');

echo "Image/Banner columns in events table:\n";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    if (strpos($row['Field'], 'image') !== false || strpos($row['Field'], 'banner') !== false) {
        echo $row['Field'] . "\n";
    }
}
