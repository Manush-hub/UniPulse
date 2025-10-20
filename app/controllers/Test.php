<?php

// Simple test controller without authentication
class TestController extends Controller {
    
    public function index() {
        echo "<h1>Testing Moderator Controllers</h1>";
        
        echo "<h2>Testing Moderator Model</h2>";
        try {
            $moderatorModel = new Moderator();
            echo "<p>✅ Moderator model created successfully</p>";
            
            // Test findById method
            $result = $moderatorModel->findById(1);
            if ($result) {
                echo "<p>✅ findById(1) returned: " . print_r($result, true) . "</p>";
            } else {
                echo "<p>⚠️ findById(1) returned null (no moderator with ID 1)</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
        
        echo "<h2>Testing Event Model</h2>";
        try {
            $eventModel = new Event();
            echo "<p>✅ Event model created successfully</p>";
            
            // Test if moderation methods exist
            if (method_exists($eventModel, 'getPendingEventsForUniversity')) {
                echo "<p>✅ getPendingEventsForUniversity method exists</p>";
            } else {
                echo "<p>❌ getPendingEventsForUniversity method missing</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
        
        echo "<h2>Testing Report Model</h2>";
        try {
            $reportModel = new Report();
            echo "<p>✅ Report model created successfully</p>";
            
            // Test if methods exist
            if (method_exists($reportModel, 'getReportsForUniversity')) {
                echo "<p>✅ getReportsForUniversity method exists</p>";
            } else {
                echo "<p>❌ getReportsForUniversity method missing</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
        
        echo "<h2>Controller Classes</h2>";
        
        if (class_exists('ContentModeration')) {
            echo "<p>✅ ContentModeration controller exists</p>";
        } else {
            echo "<p>❌ ContentModeration controller missing</p>";
        }
        
        if (class_exists('UserReports')) {
            echo "<p>✅ UserReports controller exists</p>";
        } else {
            echo "<p>❌ UserReports controller missing</p>";
        }
        
        echo "<h2>Database Tables</h2>";
        try {
            $tempClass = new class {
                use Database;
                public function getConnection() {
                    return $this->connect();
                }
            };
            
            $pdo = $tempClass->getConnection();
            
            // Check tables
            $tables = ['moderators', 'reports', 'event_moderation_notifications'];
            foreach ($tables as $table) {
                $query = "SHOW TABLES LIKE '{$table}'";
                $result = $pdo->query($query);
                
                if ($result->rowCount() > 0) {
                    echo "<p>✅ Table '{$table}' exists</p>";
                    
                    // Count rows
                    $countQuery = "SELECT COUNT(*) as count FROM {$table}";
                    $countResult = $pdo->query($countQuery);
                    $count = $countResult->fetch(PDO::FETCH_OBJ)->count;
                    echo "<p>   📊 {$count} rows in '{$table}'</p>";
                } else {
                    echo "<p>❌ Table '{$table}' missing</p>";
                }
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
        }
    }
}
?>