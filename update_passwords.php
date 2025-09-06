<?php
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/classes/User.php';

$db = DatabaseConfig::getInstance();
$user = new User();

// Generate correct hash
$hash = $user->hashPassword('password123');

// Update users
$sql = "UPDATE users SET password_hash = ? WHERE email IN (?, ?)";
$db->query($sql, [$hash, 'demo@unipulse.lk', 'admin@unipulse.lk']);

echo "Password hashes updated successfully!\n";
echo "Hash used: $hash\n";