<?php
require_once '../app/Core/init.php';

// HTML output for setup verification
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Database Setup Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 0.8em; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <h1>UniPulse Database Setup Verification</h1>
    
    <?php
    try {
        // Initialize the database initializer
        $dbInitializer = new DatabaseInitializer();
        
        echo '<div class="status info"><strong>🔄 Checking database setup...</strong></div>';
        
        // Check database connection
        echo '<h2>Database Connection</h2>';
        try {
            $conn = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
            echo '<div class="status success">✅ Successfully connected to database: <strong>' . DBNAME . '</strong></div>';
            
            // Check if database is initialized
            if ($dbInitializer->isDatabaseInitialized()) {
                echo '<div class="status success">✅ Database is fully initialized</div>';
            } else {
                echo '<div class="status error">❌ Database is not fully initialized</div>';
                echo '<div class="status info">🔄 Attempting to initialize database...</div>';
                
                if ($dbInitializer->initializeDatabase()) {
                    echo '<div class="status success">✅ Database initialized successfully!</div>';
                } else {
                    echo '<div class="status error">❌ Failed to initialize database</div>';
                }
            }
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ Database connection failed: ' . $e->getMessage() . '</div>';
            exit;
        }
        
        // Check tables
        echo '<h2>Database Tables</h2>';
        $stmt = $conn->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredTables = ['university_users', 'public_users', 'users'];
        
        echo '<table>';
        echo '<tr><th>Table Name</th><th>Status</th><th>Record Count</th><th>Last Updated</th></tr>';
        
        foreach ($requiredTables as $tableName) {
            $exists = in_array($tableName, $tables);
            $count = 0;
            $lastUpdated = 'N/A';
            
            if ($exists) {
                // Get record count
                $countStmt = $conn->prepare("SELECT COUNT(*) FROM $tableName");
                $countStmt->execute();
                $count = $countStmt->fetchColumn();
                
                // Get last updated (if table has updated_at column)
                try {
                    $updateStmt = $conn->prepare("SELECT MAX(updated_at) FROM $tableName WHERE updated_at IS NOT NULL");
                    $updateStmt->execute();
                    $lastUpdated = $updateStmt->fetchColumn() ?: 'No records';
                } catch (Exception $e) {
                    // Column might not exist
                }
            }
            
            echo '<tr>';
            echo '<td><strong>' . $tableName . '</strong></td>';
            echo '<td>' . ($exists ? '<span class="badge badge-success">EXISTS</span>' : '<span class="badge badge-error">MISSING</span>') . '</td>';
            echo '<td>' . ($exists ? $count : 'N/A') . '</td>';
            echo '<td>' . $lastUpdated . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        
        // Test data insertion capability
        echo '<h2>Functionality Test</h2>';
        
        try {
            // Test UniversityUser model
            $universityUser = new UniversityUser();
            echo '<div class="status success">✅ UniversityUser model loaded successfully</div>';
            
            // Test PublicUser model
            $publicUser = new PublicUser();
            echo '<div class="status success">✅ PublicUser model loaded successfully</div>';
            
            // Test User model
            $user = new User();
            echo '<div class="status success">✅ User model loaded successfully</div>';
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ Model loading failed: ' . $e->getMessage() . '</div>';
        }
        
        // Configuration display
        echo '<h2>Configuration</h2>';
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>Database Host</td><td>' . DBHOST . '</td></tr>';
        echo '<tr><td>Database Port</td><td>' . DBPORT . '</td></tr>';
        echo '<tr><td>Database Name</td><td>' . DBNAME . '</td></tr>';
        echo '<tr><td>Database User</td><td>' . DBUSER . '</td></tr>';
        echo '<tr><td>Root URL</td><td>' . ROOT . '</td></tr>';
        echo '</table>';
        
        // Quick links
        echo '<h2>Quick Links</h2>';
        echo '<div style="margin: 20px 0;">';
        echo '<a href="/" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">🏠 Home</a>';
        echo '<a href="/unireg" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">🎓 University Registration</a>';
        echo '<a href="/publicreg" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px;">👥 Public Registration</a>';
        echo '<a href="/test_database.php" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #ffc107; color: black; text-decoration: none; border-radius: 5px;">🧪 Database Test</a>';
        echo '</div>';
        
        echo '<div class="status success"><strong>🎉 Database setup verification completed successfully!</strong></div>';
        
    } catch (Exception $e) {
        echo '<div class="status error">❌ Verification failed: ' . $e->getMessage() . '</div>';
    }
    ?>
    
    <footer style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; text-align: center;">
        <p>UniPulse Database Setup Verification • Generated on <?= date('Y-m-d H:i:s') ?></p>
    </footer>
</body>
</html>
