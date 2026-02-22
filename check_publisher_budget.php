<?php
require_once __DIR__ . '/app/Core/config.php';

try {
    $conn = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Publisher Dashboard Budget Check ===\n\n";

// Get the event data using the new subquery approach
$stmt = $conn->query("
    SELECT 
        e.id,
        e.title,
        e.event_date,
        (SELECT COALESCE(SUM(amount), 0) 
         FROM event_sponsorships 
         WHERE event_id = e.id AND status = 'completed') as approved_budget,
        (SELECT COALESCE(SUM(amount), 0) 
         FROM event_sponsorships 
         WHERE event_id = e.id AND status = 'pending') as pending_budget
    FROM events e
    WHERE e.is_deleted = 0
    ORDER BY e.event_date DESC
");

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($events) . " events:\n\n";

foreach ($events as $event) {
    echo "Event: {$event['title']} (ID: {$event['id']})\n";
    echo "Date: {$event['event_date']}\n";
    echo "Approved Budget (Completed): Rs. {$event['approved_budget']}\n";
    echo "Pending Budget: Rs. {$event['pending_budget']}\n";
    echo "Total Budget: Rs. " . ($event['approved_budget'] + $event['pending_budget']) . "\n";
    
    // Show individual sponsorships for this event
    $sponsorships = $conn->query("
        SELECT id, sponsor_id, amount, status, package_id
        FROM event_sponsorships
        WHERE event_id = {$event['id']}
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    if ($sponsorships) {
        echo "  Sponsorships:\n";
        foreach ($sponsorships as $s) {
            echo "    - ID {$s['id']}: Rs. {$s['amount']} ({$s['status']})\n";
        }
    }
    echo "\n---\n\n";
}

echo "=== End Check ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
