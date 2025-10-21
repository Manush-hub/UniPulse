<?php
session_start();

echo "Session Debug\n";
echo "=============\n\n";

if (isset($_SESSION['user'])) {
    echo "User found in session:\n";
    print_r($_SESSION['user']);
    
    if (isset($_SESSION['user']['university'])) {
        echo "\nUniversity: " . $_SESSION['user']['university'] . "\n";
    } else {
        echo "\nNo 'university' key in user session\n";
    }
} else {
    echo "No user in session\n";
}
