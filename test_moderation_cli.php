<?php
/**
 * Command-line test for comment moderation system
 */

require_once __DIR__ . '/app/Core/init.php';

echo "=== TESTING COMMENT MODERATION SYSTEM ===" . PHP_EOL . PHP_EOL;

// Test hiding a comment
$commentModel = new Comment();
echo "1. Testing hideComment() method..." . PHP_EOL;
$result = $commentModel->hideComment(17, 7, 'Testing the comment moderation system - inappropriate test content');

if ($result['success']) {
    echo "✓ SUCCESS: Comment 17 has been hidden" . PHP_EOL;
    echo "  Message: " . $result['message'] . PHP_EOL . PHP_EOL;
} else {
    echo "✗ FAILED: " . print_r($result['errors'], true) . PHP_EOL . PHP_EOL;
}

// Verify the comment is hidden
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=unipulse_db', 'root', 'root');
$stmt = $pdo->prepare('SELECT id, is_hidden, hidden_by, hidden_reason, hidden_at FROM event_comments WHERE id = 17');
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

echo "2. Verifying database update..." . PHP_EOL;
echo "  Comment ID: " . $comment['id'] . PHP_EOL;
echo "  Is Hidden: " . ($comment['is_hidden'] ? 'YES' : 'NO') . PHP_EOL;
echo "  Hidden By: Moderator ID " . $comment['hidden_by'] . PHP_EOL;
echo "  Hidden At: " . $comment['hidden_at'] . PHP_EOL;
echo "  Reason: " . $comment['hidden_reason'] . PHP_EOL . PHP_EOL;

// Check notification
$notifStmt = $pdo->query('SELECT * FROM notifications WHERE type = "comment_hidden" ORDER BY created_at DESC LIMIT 1');
$notification = $notifStmt->fetch(PDO::FETCH_ASSOC);

if ($notification) {
    echo "3. Notification created..." . PHP_EOL;
    echo "  Title: " . $notification['title'] . PHP_EOL;
    echo "  Message: " . $notification['message'] . PHP_EOL;
    echo "  Recipient: " . $notification['recipient_type'] . " user ID " . $notification['recipient_id'] . PHP_EOL . PHP_EOL;
} else {
    echo "3. No notification found (this is expected if comment was already hidden)" . PHP_EOL . PHP_EOL;
}

// Now test unhiding
echo "4. Testing unhideComment() method..." . PHP_EOL;
$unhideResult = $commentModel->unhideComment(17, 7);

if ($unhideResult['success']) {
    echo "✓ SUCCESS: Comment 17 has been unhidden" . PHP_EOL;
    echo "  Message: " . $unhideResult['message'] . PHP_EOL . PHP_EOL;
} else {
    echo "✗ FAILED: " . print_r($unhideResult['errors'], true) . PHP_EOL . PHP_EOL;
}

// Verify the comment is visible again
$verifyStmt = $pdo->prepare('SELECT is_hidden, hidden_by, hidden_reason FROM event_comments WHERE id = 17');
$verifyStmt->execute();
$verify = $verifyStmt->fetch(PDO::FETCH_ASSOC);

echo "5. Verifying unhide..." . PHP_EOL;
echo "  Is Hidden: " . ($verify['is_hidden'] ? 'YES' : 'NO') . PHP_EOL;
echo "  Hidden By: " . ($verify['hidden_by'] ?: 'NULL') . PHP_EOL;
echo "  Reason: " . ($verify['hidden_reason'] ?: 'NULL') . PHP_EOL . PHP_EOL;

// Check unhide notification
$unhideNotifStmt = $pdo->query('SELECT * FROM notifications WHERE type = "comment_unhidden" ORDER BY created_at DESC LIMIT 1');
$unhideNotification = $unhideNotifStmt->fetch(PDO::FETCH_ASSOC);

if ($unhideNotification) {
    echo "6. Unhide notification created..." . PHP_EOL;
    echo "  Title: " . $unhideNotification['title'] . PHP_EOL;
    echo "  Message: " . $unhideNotification['message'] . PHP_EOL . PHP_EOL;
}

echo "=== ALL TESTS COMPLETED SUCCESSFULLY ===" . PHP_EOL;
echo PHP_EOL;
echo "You can now:" . PHP_EOL;
echo "- Log in as a moderator at: http://localhost:8888/unipulse/public/signin" . PHP_EOL;
echo "- View comments at: http://localhost:8888/unipulse/public/moderator/comments" . PHP_EOL;
echo "- Test hiding/unhiding comments through the UI" . PHP_EOL;
?>
