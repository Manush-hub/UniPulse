<?php
require_once __DIR__ . '/app/Core/init.php';

class TestConnection {
    use Database;
    
    public function getConnection() {
        return $this->connect();
    }
}

$test = new TestConnection();
$conn = $test->getConnection();

$tables = ['public_users', 'university_users', 'publishers', 'sponsors', 'admins', 'moderators'];
foreach($tables as $table) {
    try {
        // First, let's see what columns exist
        $stmt = $conn->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n=== $table COLUMNS ===\n";
        foreach($columns as $col) {
            echo "Column: {$col['Field']} (Type: {$col['Type']})\n";
        }
        
        // Then get some sample data
        $stmt = $conn->query("SELECT * FROM $table LIMIT 3");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n=== $table DATA ===\n";
        foreach($users as $user) {
            echo print_r($user, true) . "\n";
        }
    } catch(Exception $e) {
        echo "Error with $table: " . $e->getMessage() . "\n";
    }
}
?>