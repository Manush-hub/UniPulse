<?php
/**
 * Debug script to check faculty visibility issues
 */
require_once __DIR__ . '/app/Core/config.php';

try {
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "<h2>Faculty Visibility Debug Report</h2>";
    echo "<style>table { border-collapse: collapse; margin: 20px 0; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #4CAF50; color: white; } tr:nth-child(even) { background-color: #f2f2f2; }</style>";
    
    // Check publishers and their faculties
    echo "<h3>1. Publishers Faculty Data</h3>";
    $stmt = $pdo->query("SELECT id, society_name, email, university, faculty FROM publishers LIMIT 10");
    $publishers = $stmt->fetchAll();
    
    if ($publishers) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Society Name</th><th>Email</th><th>University</th><th>Faculty</th></tr>";
        foreach ($publishers as $pub) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($pub['id']) . "</td>";
            echo "<td>" . htmlspecialchars($pub['society_name']) . "</td>";
            echo "<td>" . htmlspecialchars($pub['email']) . "</td>";
            echo "<td>" . htmlspecialchars($pub['university']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($pub['faculty']) . "</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No publishers found</p>";
    }
    
    // Check university users and their faculties
    echo "<h3>2. University Users Faculty Data</h3>";
    $stmt = $pdo->query("SELECT id, full_name, email, university, faculty FROM university_users LIMIT 10");
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>University</th><th>Faculty</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['university']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($user['faculty']) . "</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No university users found</p>";
    }
    
    // Check faculty-only events
    echo "<h3>3. Faculty-Only Events</h3>";
    $stmt = $pdo->query("
        SELECT e.id, e.title, e.visibility, e.university, e.faculty_department, e.created_by, e.created_by_type,
               p.society_name as publisher_name, p.faculty as publisher_faculty
        FROM events e
        LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
        WHERE e.visibility = 'faculty-only' AND e.is_deleted = 0
        LIMIT 20
    ");
    $events = $stmt->fetchAll();
    
    if ($events) {
        echo "<table>";
        echo "<tr><th>Event ID</th><th>Title</th><th>Visibility</th><th>University</th><th>Event Faculty</th><th>Publisher</th><th>Publisher Faculty</th><th>Match?</th></tr>";
        foreach ($events as $event) {
            $match = ($event['faculty_department'] === $event['publisher_faculty']) ? '✅ YES' : '❌ NO';
            $rowStyle = ($match === '❌ NO') ? 'style="background-color: #ffcccc;"' : '';
            echo "<tr $rowStyle>";
            echo "<td>" . htmlspecialchars($event['id']) . "</td>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td>" . htmlspecialchars($event['visibility']) . "</td>";
            echo "<td>" . htmlspecialchars($event['university']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($event['faculty_department'] ?: 'NULL') . "</strong></td>";
            echo "<td>" . htmlspecialchars($event['publisher_name'] ?: 'N/A') . "</td>";
            echo "<td><strong>" . htmlspecialchars($event['publisher_faculty'] ?: 'NULL') . "</strong></td>";
            echo "<td><strong>$match</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No faculty-only events found</p>";
    }
    
    // Check visibility filtering simulation
    echo "<h3>4. Visibility Filter Test</h3>";
    echo "<p>Testing if a user can see faculty-only events based on matching faculty values...</p>";
    
    if ($users && $events) {
        $testUser = $users[0]; // Take first user for testing
        echo "<p><strong>Test User:</strong> " . htmlspecialchars($testUser['full_name']) . " (Faculty: " . htmlspecialchars($testUser['faculty']) . ")</p>";
        
        echo "<table>";
        echo "<tr><th>Event</th><th>Event Faculty</th><th>User Faculty</th><th>Should See?</th></tr>";
        foreach ($events as $event) {
            $sameUniversity = ($event['university'] === $testUser['university']);
            $sameFaculty = ($event['faculty_department'] === $testUser['faculty']);
            $shouldSee = ($sameUniversity && $sameFaculty) ? '✅ YES' : '❌ NO';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($event['faculty_department'] ?: 'NULL') . "</strong></td>";
            echo "<td><strong>" . htmlspecialchars($testUser['faculty']) . "</strong></td>";
            echo "<td><strong>$shouldSee</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check for NULL or empty faculty_department in faculty-only events
    echo "<h3>5. Issues Found</h3>";
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM events 
        WHERE visibility = 'faculty-only' 
        AND (faculty_department IS NULL OR faculty_department = '') 
        AND is_deleted = 0
    ");
    $nullCount = $stmt->fetch()['count'];
    
    if ($nullCount > 0) {
        echo "<p style='color: red; font-weight: bold;'>❌ Found $nullCount faculty-only events with NULL or empty faculty_department field!</p>";
        echo "<p>This is the main issue - these events won't show up for any users.</p>";
    } else {
        echo "<p style='color: green;'>✅ All faculty-only events have faculty_department values.</p>";
    }
    
    // Check for faculty value mismatches
    echo "<h3>6. Unique Faculty Values</h3>";
    
    echo "<h4>In Publishers Table:</h4>";
    $stmt = $pdo->query("SELECT DISTINCT faculty FROM publishers ORDER BY faculty");
    $pubFaculties = $stmt->fetchAll();
    echo "<ul>";
    foreach ($pubFaculties as $f) {
        echo "<li><code>" . htmlspecialchars($f['faculty']) . "</code></li>";
    }
    echo "</ul>";
    
    echo "<h4>In University Users Table:</h4>";
    $stmt = $pdo->query("SELECT DISTINCT faculty FROM university_users ORDER BY faculty");
    $userFaculties = $stmt->fetchAll();
    echo "<ul>";
    foreach ($userFaculties as $f) {
        echo "<li><code>" . htmlspecialchars($f['faculty']) . "</code></li>";
    }
    echo "</ul>";
    
    echo "<h4>In Events Table (faculty_department):</h4>";
    $stmt = $pdo->query("SELECT DISTINCT faculty_department FROM events WHERE faculty_department IS NOT NULL AND faculty_department != '' ORDER BY faculty_department");
    $eventFaculties = $stmt->fetchAll();
    echo "<ul>";
    foreach ($eventFaculties as $f) {
        echo "<li><code>" . htmlspecialchars($f['faculty_department']) . "</code></li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
