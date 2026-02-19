<?php
/**
 * Test script to verify amanda can see faculty-only events
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
    
    echo "<h2>Faculty Visibility Test for User 'amanda'</h2>\n";
    echo "<style>table { border-collapse: collapse; margin: 20px 0; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #4CAF50; color: white; } tr:nth-child(even) { background-color: #f2f2f2; } .success { background-color: #d4edda !important; } .fail { background-color: #f8d7da !important; }</style>\n\n";
    
    // Get amanda's data
    $stmt = $pdo->prepare("SELECT id, full_name, email, university, faculty FROM university_users WHERE email = :email");
    $stmt->execute(['email' => 'amanda@gmail.com']);
    $amanda = $stmt->fetch();
    
    if (!$amanda) {
        echo "<p style='color: red;'>User 'amanda' not found!</p>\n";
        exit;
    }
    
    echo "<h3>User Information</h3>\n";
    echo "<table>\n";
    echo "<tr><th>Field</th><th>Value</th></tr>\n";
    echo "<tr><td>ID</td><td>" . $amanda['id'] . "</td></tr>\n";
    echo "<tr><td>Name</td><td>" . $amanda['full_name'] . "</td></tr>\n";
    echo "<tr><td>Email</td><td>" . $amanda['email'] . "</td></tr>\n";
    echo "<tr><td>University</td><td><strong>" . $amanda['university'] . "</strong></td></tr>\n";
    echo "<tr><td>Faculty</td><td><strong>" . $amanda['faculty'] . "</strong></td></tr>\n";
    echo "</table>\n\n";
    
    // Build the same visibility filter that would be used in the code
    $userType = 'university';
    $userUniversity = $amanda['university'];
    $userFaculty = $amanda['faculty'];
    
    $visibilityConditions = ["e.visibility = 'public'"];
    $params = [];
    
    // All universities events
    $visibilityConditions[] = "e.visibility = 'all-universities'";
    
    // University-only events
    $visibilityConditions[] = "(e.visibility = 'university-only' AND e.university = :user_university)";
    $params['user_university'] = $userUniversity;
    
    // Faculty-only events
    $visibilityConditions[] = "(e.visibility = 'faculty-only' AND e.university = :user_university2 AND e.faculty_department = :user_faculty)";
    $params['user_university2'] = $userUniversity;
    $params['user_faculty'] = $userFaculty;
    
    $visibilityClause = '(' . implode(' OR ', $visibilityConditions) . ')';
    
    echo "<h3>Visibility Filter SQL</h3>\n";
    echo "<pre>" . htmlspecialchars($visibilityClause) . "</pre>\n";
    echo "<p><strong>Parameters:</strong></p>\n";
    echo "<ul>\n";
    foreach ($params as $key => $value) {
        echo "<li>$key = <strong>" . htmlspecialchars($value) . "</strong></li>\n";
    }
    echo "</ul>\n\n";
    
    // Get all events amanda should be able to see
    $query = "SELECT e.id, e.title, e.visibility, e.university, e.faculty_department, e.created_by, e.created_by_type,
                     p.society_name as publisher_name, p.faculty as publisher_faculty
              FROM events e
              LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
              WHERE e.is_deleted = 0 AND $visibilityClause
              ORDER BY e.id DESC
              LIMIT 20";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
    
    echo "<h3>Events Amanda Can See (" . count($events) . " total)</h3>\n";
    
    if ($events) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Title</th><th>Visibility</th><th>University</th><th>Faculty</th><th>Publisher</th><th>Match Reason</th></tr>\n";
        
        foreach ($events as $event) {
            $matchReason = '';
            $rowClass = '';
            
            if ($event['visibility'] === 'public') {
                $matchReason = '✅ Public event';
                $rowClass = 'success';
            } elseif ($event['visibility'] === 'all-universities') {
                $matchReason = '✅ All universities';
                $rowClass = 'success';
            } elseif ($event['visibility'] === 'university-only') {
                if ($event['university'] === $userUniversity) {
                    $matchReason = '✅ Same university';
                    $rowClass = 'success';
                } else {
                    $matchReason = '❌ Different university';
                    $rowClass = 'fail';
                }
            } elseif ($event['visibility'] === 'faculty-only') {
                if ($event['university'] === $userUniversity && $event['faculty_department'] === $userFaculty) {
                    $matchReason = '✅ Same university & faculty';
                    $rowClass = 'success';
                } else {
                    $reasons = [];
                    if ($event['university'] !== $userUniversity) {
                        $reasons[] = 'Different university';
                    }
                    if ($event['faculty_department'] !== $userFaculty) {
                        $reasons[] = 'Different faculty';
                    }
                    $matchReason = '❌ ' . implode(', ', $reasons);
                    $rowClass = 'fail';
                }
            }
            
            echo "<tr class='$rowClass'>\n";
            echo "<td>" . $event['id'] . "</td>\n";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['visibility']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['university']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['faculty_department'] ?: 'N/A') . "</td>\n";
            echo "<td>" . htmlspecialchars($event['publisher_name'] ?: 'N/A') . "</td>\n";
            echo "<td><strong>" . $matchReason . "</strong></td>\n";
            echo "</tr>\n";
        }
        
        echo "</table>\n";
    } else {
        echo "<p>No events found that amanda can see.</p>\n";
    }
    
    // Check specifically for faculty-only events from Rekha Music Circle
    echo "<h3>Faculty-Only Events from Rekha Music Circle</h3>\n";
    
    $stmt = $pdo->query("
        SELECT e.id, e.title, e.visibility, e.university, e.faculty_department,
               p.society_name, p.university as publisher_univ, p.faculty as publisher_faculty
        FROM events e
        LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
        WHERE p.society_name LIKE '%Rekha%'
        AND e.visibility = 'faculty-only'
        AND e.is_deleted = 0
        ORDER BY e.id DESC
    ");
    $rekhaEvents = $stmt->fetchAll();
    
    if ($rekhaEvents) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Title</th><th>Event University</th><th>Event Faculty</th><th>Publisher Univ</th><th>Publisher Faculty</th><th>Amanda Can See?</th></tr>\n";
        
        foreach ($rekhaEvents as $event) {
            $canSee = ($event['university'] === $amanda['university'] && $event['faculty_department'] === $amanda['faculty']);
            $rowClass = $canSee ? 'success' : 'fail';
            $canSeeText = $canSee ? '✅ YES' : '❌ NO';
            
            echo "<tr class='$rowClass'>\n";
            echo "<td>" . $event['id'] . "</td>\n";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['university']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['faculty_department']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['publisher_univ']) . "</td>\n";
            echo "<td>" . htmlspecialchars($event['publisher_faculty']) . "</td>\n";
            echo "<td><strong>" . $canSeeText . "</strong></td>\n";
            echo "</tr>\n";
        }
        
        echo "</table>\n";
    } else {
        echo "<p>No faculty-only events found from Rekha Music Circle.</p>\n";
    }
    
    echo "\n<h3>Summary</h3>\n";
    $facultyOnlyCount = count(array_filter($events, function($e) { return $e['visibility'] === 'faculty-only'; }));
    echo "<p>✅ Amanda can see <strong>" . count($events) . "</strong> total events</p>\n";
    echo "<p>✅ Including <strong>$facultyOnlyCount</strong> faculty-only events</p>\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
