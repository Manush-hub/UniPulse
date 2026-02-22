<?php
// Check database sponsorships
require_once __DIR__ . '/app/Core/config.php';

try {
    $conn = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Database Sponsorships Check ===\n\n";
    echo "Current date: " . date('Y-m-d') . "\n\n";
    
    // Check all sponsorships
    echo "--- All Event Sponsorships ---\n";
    $stmt = $conn->query("SELECT 
                es.id,
                es.sponsor_id,
                es.sponsor_type,
                es.status,
                es.amount,
                e.title as event_title,
                e.event_date,
                e.is_deleted,
                esp.package_name
            FROM event_sponsorships es
            INNER JOIN events e ON es.event_id = e.id
            LEFT JOIN event_sponsorship_packages esp ON es.package_id = esp.id
            ORDER BY es.created_at DESC");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($all && count($all) > 0) {
        echo "Total: " . count($all) . " sponsorships\n\n";
        foreach ($all as $s) {
            echo "  Sponsorship ID: {$s['id']}\n";
            echo "  Sponsor ID: {$s['sponsor_id']} ({$s['sponsor_type']})\n";
            echo "  Status: {$s['status']} | Amount: {$s['amount']}\n";
            echo "  Event: {$s['event_title']}\n";
            echo "  Package: " . ($s['package_name'] ?? 'N/A') . "\n";
            echo "  Event Date: {$s['event_date']}\n";
            echo "  Event Deleted: " . ($s['is_deleted'] ? 'YES' : 'NO') . "\n";
            
            // Check if event is upcoming
            $eventDate = strtotime($s['event_date']);
            $today = strtotime('today');
            
            if ($eventDate >= $today) {
                echo "  ✓ Event is UPCOMING/ONGOING\n";
            } else {
                echo "  ✗ Event is PAST\n";
            }
            
            echo "\n";
        }
    } else {
        echo "  No sponsorships in database\n\n";
    }
    
    // Check for completed sponsorships with upcoming events
    echo "\n--- Completed Sponsorships for Upcoming Events ---\n";
    $stmt = $conn->query("SELECT 
                es.id,
                es.sponsor_id,
                es.amount,
                e.title as event_title,
                e.event_date,
                esp.package_name
            FROM event_sponsorships es
            INNER JOIN events e ON es.event_id = e.id
            LEFT JOIN event_sponsorship_packages esp ON es.package_id = esp.id
            WHERE es.status = 'completed'
                AND e.event_date >= CURDATE()
                AND e.is_deleted = 0");
    $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($upcoming && count($upcoming) > 0) {
        echo "Found " . count($upcoming) . " completed sponsorships for upcoming events:\n\n";
        foreach ($upcoming as $s) {
            echo "  Sponsorship ID: {$s['id']} | Sponsor ID: {$s['sponsor_id']}\n";
            echo "  Event: {$s['event_title']}\n";
            echo "  Package: {$s['package_name']}\n";
            echo "  Amount: {$s['amount']}\n";
            echo "  Date: {$s['event_date']}\n\n";
        }
    } else {
        echo "No completed sponsorships for upcoming events\n\n";
    }
    
    echo "=== End Check ===\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
