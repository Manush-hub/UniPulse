<?php
// Test if sponsor_posts table exists and create it if not

require_once 'app/Core/config.php';
require_once 'app/Core/Database.php';

$db = new Database();

echo "<h1>Sponsor Posts Table Check</h1>";

try {
    // Check if table exists
    $result = $db->query("SHOW TABLES LIKE 'sponsor_posts'");
    
    if (empty($result)) {
        echo "<p style='color: orange;'><strong>Table 'sponsor_posts' does NOT exist!</strong></p>";
        echo "<p>Running migration...</p>";
        
        // Run the migration
        $migrationFile = __DIR__ . '/database/create_sponsor_posts_table.php';
        if (file_exists($migrationFile)) {
            include $migrationFile;
            echo "<p style='color: green;'><strong>Migration executed! Checking again...</strong></p>";
            
            // Check again
            $result = $db->query("SHOW TABLES LIKE 'sponsor_posts'");
            if (!empty($result)) {
                echo "<p style='color: green;'><strong>✓ Table created successfully!</strong></p>";
            } else {
                echo "<p style='color: red;'><strong>✗ Table creation failed. Check migration file.</strong></p>";
            }
        } else {
            echo "<p style='color: red;'><strong>Migration file not found at: $migrationFile</strong></p>";
        }
    } else {
        echo "<p style='color: green;'><strong>✓ Table 'sponsor_posts' EXISTS!</strong></p>";
        
        // Show table structure
        $structure = $db->query("DESCRIBE sponsor_posts");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/unipulse/public/sponsor/events'>← Back to Events</a></p>";
?>
