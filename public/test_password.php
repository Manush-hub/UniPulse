<?php
$password = '1142119112';
$hash = '$2y$10$cxqC0tMkgx3I244jjv8Sk.qwXTU4mncK9tGXABlS.2tYH8.kZ.qgC';

echo "Testing password hash verification:" . PHP_EOL;
echo "Password: " . $password . PHP_EOL;
echo "Hash: " . $hash . PHP_EOL;
echo "Verification: " . (password_verify($password, $hash) ? 'SUCCESS' : 'FAILED') . PHP_EOL;
?>
