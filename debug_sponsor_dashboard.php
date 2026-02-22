<?php
// Simple debug script without complex dependencies
require_once __DIR__ . '/app/Core/config.php';

session_start();

// Direct database connection
try {
    $conn = new PDO("mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Sponsor Dashboard Debug ===\n\n";
    
    // Check if sponsor is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'sponsor') {
        echo "❌ No sponsor logged in\n";
        echo "Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . "\n";
        echo "Session user_type: " . ($_SESSION['user_type'] ?? 'not set') . "\n";
        exit;
    }
    
    $sponsorId = $_SESSION['user_id'];
    echo "✓ Sponsor ID: $sponsorId\n\n";
    
    // Check all sponsorships for this sponsor
    echo "--- All Sponsorships ---\n";
    $stmt = $conn->prepare("SELECT 
                es.id,
                es.status,
                es.amount,
                e.title as event_title,
                e.event_date,
                e.end_date,
                e.is_deleted
            FROM event_sponsorships es
            INNER JOIN events e ON es.event_id = e.id
            WHERE es.sponsor_id = ? AND es.sponsor_type = 'sponsor'
            ORDER BY es.created_at DESC");
    $stmt->execute([$sponsorId]);
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($all && count($all) > 0) {
        foreach ($all as $s) {
            echo "  ID: {$s['id']} | Status: {$s['status']} | Amount: {$s['amount']}\n";
            echo "  Event: {$s['event_title']}\n";
            echo "  Date: {$s['event_date']} - " . ($s['end_date'] ?? 'N/A') . "\n";
            echo "  Deleted: " . ($s['is_deleted'] ? 'YES' : 'NO') . "\n\n";
        }
    } else {
        echo "  No sponsorships found\n\n";
    }
    
    // Check completed sponsorships for upcoming/ongoing events
    echo "--- Active Sponsorships Query ---\n";
    $stmt = $conn->prepare("SELECT 
                es.id,
                es.amount,
                e.id as event_id,
                e.title as event_title,
                e.event_date,
                e.end_date,
                esp.package_name,
                esp.package_type
            FROM event_sponsorships es
            INNER JOIN events e ON es.event_id = e.id
            INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
            WHERE es.sponsor_id = ? 
                AND es.sponsor_type = 'sponsor'
                AND es.status = 'completed'
                AND (e.event_date >= CURDATE() OR (e.end_date IS NOT NULL AND e.end_date >= CURDATE()))
                AND e.is_deleted = 0
            ORDER BY e.event_date ASC");
    $stmt->execute([$sponsorId]);
    $active = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current date: " . date('Y-m-d') . "\n\n";
    
    if ($active && count($active) > 0) {
        echo "Found " . count($active) . " active sponsorships:\n\n";
        foreach ($active as $s) {
            echo "  ID: {$s['id']} | Event ID: {$s['event_id']}\n";
            echo "  Event: {$s['event_title']}\n";
            echo "  Package: {$s['package_name']} ({$s['package_type']})\n";
            echo "  Amount: {$s['amount']}\n";
            echo "  Date: {$s['event_date']} - " . ($s['end_date'] ?? 'N/A') . "\n\n";
        }
    } else {
        echo "No active sponsorships found\n";
        echo "Reasons could be:\n";
        echo "  - No completed sponsorships\n";
        echo "  - All events are in the past\n";
        echo "  - Events are marked as deleted\n\n";
    }
    
    echo "=== End Debug ===\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
