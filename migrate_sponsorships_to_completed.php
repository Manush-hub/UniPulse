<?php
/**
 * Migration Script: Convert Approved Sponsorships to Completed
 * 
 * This script:
 * 1. Updates all 'approved' sponsorships to 'completed' status
 * 2. Recalculates and fixes package filled_slots based on completed sponsorships
 * 3. Ensures data integrity across event_sponsorships and event_sponsorship_packages
 */

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/config.php';

class SponsorshipMigration
{
    use Database;

    public function migrate()
    {
        echo "Starting Sponsorship Migration...\n";
        echo str_repeat("=", 50) . "\n";

        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            // Step 1: Update all approved sponsorships to completed
            echo "\n[Step 1] Converting approved sponsorships to completed...\n";
            $updateSql = "UPDATE event_sponsorships 
                         SET status = 'completed', 
                             updated_at = NOW() 
                         WHERE status = 'approved'";
            
            $stmt = $conn->prepare($updateSql);
            $stmt->execute();
            $updatedCount = $stmt->rowCount();
            echo "✓ Updated $updatedCount sponsorships from 'approved' to 'completed'\n";

            // Step 2: Reset all package filled_slots to 0
            echo "\n[Step 2] Resetting package slots...\n";
            $resetSql = "UPDATE event_sponsorship_packages SET filled_slots = 0";
            $stmt = $conn->prepare($resetSql);
            $stmt->execute();
            echo "✓ Reset all package filled_slots to 0\n";

            // Step 3: Recalculate filled_slots based on completed sponsorships
            echo "\n[Step 3] Recalculating filled_slots from completed sponsorships...\n";
            $recalculateSql = "UPDATE event_sponsorship_packages esp
                              SET filled_slots = (
                                  SELECT COUNT(*) 
                                  FROM event_sponsorships es 
                                  WHERE es.package_id = esp.id 
                                  AND es.status = 'completed'
                              )";
            
            $stmt = $conn->prepare($recalculateSql);
            $stmt->execute();
            echo "✓ Recalculated filled_slots for all packages\n";

            // Step 4: Verify the migration
            echo "\n[Step 4] Verifying migration...\n";
            
            // Check for any remaining approved sponsorships
            $checkSql = "SELECT COUNT(*) as count FROM event_sponsorships WHERE status = 'approved'";
            $result = $conn->query($checkSql)->fetch(PDO::FETCH_ASSOC);
            $remainingApproved = $result['count'];
            
            if ($remainingApproved > 0) {
                throw new Exception("Migration failed: $remainingApproved approved sponsorships still exist");
            }
            echo "✓ No approved sponsorships remaining\n";

            // Get statistics
            $statsSql = "SELECT 
                            (SELECT COUNT(*) FROM event_sponsorships WHERE status = 'completed') as completed_count,
                            (SELECT COUNT(*) FROM event_sponsorships WHERE status = 'pending') as pending_count,
                            (SELECT COUNT(*) FROM event_sponsorships WHERE status = 'rejected') as rejected_count,
                            (SELECT COUNT(*) FROM event_sponsorship_packages) as total_packages,
                            (SELECT SUM(filled_slots) FROM event_sponsorship_packages) as total_filled_slots,
                            (SELECT SUM(available_slots) FROM event_sponsorship_packages) as total_available_slots";
            
            $stats = $conn->query($statsSql)->fetch(PDO::FETCH_ASSOC);

            $conn->commit();

            // Display final statistics
            echo "\n" . str_repeat("=", 50) . "\n";
            echo "Migration Completed Successfully!\n";
            echo str_repeat("=", 50) . "\n";
            echo "\nSponsorship Status Summary:\n";
            echo "  - Completed: {$stats['completed_count']}\n";
            echo "  - Pending: {$stats['pending_count']}\n";
            echo "  - Rejected: {$stats['rejected_count']}\n";
            echo "\nPackage Slots Summary:\n";
            echo "  - Total Packages: {$stats['total_packages']}\n";
            echo "  - Total Filled Slots: {$stats['total_filled_slots']}\n";
            echo "  - Total Available Slots: {$stats['total_available_slots']}\n";
            echo "  - Remaining Slots: " . ($stats['total_available_slots'] - $stats['total_filled_slots']) . "\n";
            
            return true;

        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Run the migration
$migration = new SponsorshipMigration();
$success = $migration->migrate();

exit($success ? 0 : 1);
