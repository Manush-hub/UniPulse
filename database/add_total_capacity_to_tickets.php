<?php
/**
 * Migration: Add total_capacity tracking to ticket_types
 * This updates existing events to store the original ticket quantity as total_capacity
 * so we can track how many have been sold
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
    $pdo = new PDO($dsn, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Updating ticket_types to include total_capacity...\n";
    
    // Get all events with ticket_types
    $stmt = $pdo->query("SELECT id, ticket_types FROM events WHERE ticket_types IS NOT NULL");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updated = 0;
    foreach ($events as $event) {
        $ticketTypes = json_decode($event['ticket_types'], true);
        
        if (is_array($ticketTypes) && count($ticketTypes) > 0) {
            $modified = false;
            
            foreach ($ticketTypes as &$ticket) {
                // If total_capacity doesn't exist, set it to current quantity
                if (!isset($ticket['total_capacity'])) {
                    $ticket['total_capacity'] = $ticket['quantity'];
                    $modified = true;
                }
            }
            
            if ($modified) {
                $updatedJson = json_encode($ticketTypes);
                $updateStmt = $pdo->prepare("UPDATE events SET ticket_types = ? WHERE id = ?");
                $updateStmt->execute([$updatedJson, $event['id']]);
                $updated++;
            }
        }
    }
    
    echo "✓ Successfully updated $updated events with total_capacity tracking\n";
    echo "Migration complete!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
