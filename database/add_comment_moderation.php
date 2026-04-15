<?php
/**
 * Migration script to add comment moderation fields
 * Adds is_hidden, hidden_by, hidden_at, and hidden_reason to event_comments table
 */

require_once dirname(__DIR__) . '/app/Core/config.php';

try {
    $pdo = new PDO("mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 Adding comment moderation fields to event_comments table...\n\n";
    
    // Check if columns already exist
    $stmt = $pdo->query("DESCRIBE event_comments");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $columnsToAdd = [];
    
    if (!in_array('is_hidden', $columns)) {
        $columnsToAdd[] = "ADD COLUMN is_hidden BOOLEAN DEFAULT FALSE AFTER is_deleted";
    }
    
    if (!in_array('hidden_by', $columns)) {
        $columnsToAdd[] = "ADD COLUMN hidden_by INT NULL AFTER is_hidden";
    }
    
    if (!in_array('hidden_at', $columns)) {
        $columnsToAdd[] = "ADD COLUMN hidden_at TIMESTAMP NULL AFTER hidden_by";
    }
    
    if (!in_array('hidden_reason', $columns)) {
        $columnsToAdd[] = "ADD COLUMN hidden_reason TEXT NULL AFTER hidden_at";
    }
    
    if (!empty($columnsToAdd)) {
        $alterQuery = "ALTER TABLE event_comments " . implode(", ", $columnsToAdd);
        $pdo->exec($alterQuery);
        echo "✅ Added columns: " . implode(", ", ['is_hidden', 'hidden_by', 'hidden_at', 'hidden_reason']) . "\n";
    } else {
        echo "ℹ️  All columns already exist\n";
    }
    
    // Add index for hidden comments
    try {
        $pdo->exec("CREATE INDEX idx_hidden_comments ON event_comments(is_hidden, event_id)");
        echo "✅ Created index for hidden comments\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "ℹ️  Index already exists\n";
        } else {
            throw $e;
        }
    }
    
    // Update notifications table to support comment_hidden type
    echo "\n🔧 Updating notifications table...\n";
    
    // Check current ENUM values
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications WHERE Field = 'type'");
    $typeColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (strpos($typeColumn['Type'], 'comment_hidden') === false) {
        $pdo->exec("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'new_comment', 
            'comment_edited', 
            'comment_deleted', 
            'comment_hidden',
            'comment_unhidden',
            'event_comment'
        ) NOT NULL");
        echo "✅ Updated notification types to include comment moderation\n";
    } else {
        echo "ℹ️  Notification types already updated\n";
    }
    
    // Also update recipient_type to include users
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications WHERE Field = 'recipient_type'");
    $recipientColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (strpos($recipientColumn['Type'], 'university') === false) {
        $pdo->exec("ALTER TABLE notifications MODIFY COLUMN recipient_type ENUM(
            'publisher', 
            'admin', 
            'moderator',
            'university',
            'public',
            'sponsor'
        ) NOT NULL");
        echo "✅ Updated recipient types to include all user types\n";
    } else {
        echo "ℹ️  Recipient types already updated\n";
    }
    
    echo "\n📊 Migration Summary:\n";
    echo "- event_comments table: Added moderation fields\n";
    echo "- Moderators can now hide inappropriate comments\n";
    echo "- Users will be notified when their comments are hidden\n";
    echo "- Hidden comments won't appear in public view\n";
    echo "\n🎉 Comment moderation system setup complete!\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
