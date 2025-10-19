<?php
// Simple test script to check if routing and models work
header('Content-Type: application/json');

echo json_encode([
    'test' => 'routing works',
    'timestamp' => date('Y-m-d H:i:s'),
    'post_data' => $_POST,
    'get_data' => $_GET
]);
?>