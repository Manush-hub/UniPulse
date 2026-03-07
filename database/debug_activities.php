<?php
require_once __DIR__ . '/../app/Core/config.php';
$pdo = new PDO('mysql:host='.DBHOST.';port='.DBPORT.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ]);

echo "=== MODERATORS ===\n";
foreach ($pdo->query('SELECT id, full_name FROM moderators LIMIT 10')->fetchAll() as $r)
    echo "  id={$r->id}  {$r->full_name}\n";

echo "\n=== HIDDEN EVENTS (is_deleted=1) ===\n";
foreach ($pdo->query('SELECT id, title, is_deleted, deleted_at, deleted_by, moderated_by FROM events WHERE is_deleted=1 LIMIT 10')->fetchAll() as $r)
    echo "  id={$r->id}  deleted_by={$r->deleted_by}  moderated_by={$r->moderated_by}  deleted_at={$r->deleted_at}  title={$r->title}\n";

echo "\n=== PUBLISHERS (approved/rejected) ===\n";
foreach ($pdo->query("SELECT id, society_name, approval_status, approved_by, approved_at FROM publishers WHERE approval_status IN ('approved','rejected') LIMIT 10")->fetchAll() as $r)
    echo "  id={$r->id}  status={$r->approval_status}  approved_by={$r->approved_by}  approved_at={$r->approved_at}  name={$r->society_name}\n";

echo "\n=== HIDDEN COMMENTS ===\n";
foreach ($pdo->query('SELECT id, is_hidden, hidden_by, hidden_at FROM event_comments WHERE is_hidden=1 LIMIT 10')->fetchAll() as $r)
    echo "  id={$r->id}  hidden_by={$r->hidden_by}  hidden_at={$r->hidden_at}\n";

echo "\n=== FULL QUERY TEST with CONVERT fix ===\n";
$sql = "
(SELECT e.id, CONVERT(e.title USING utf8mb4) AS item_title, e.deleted_at AS activity_time,
        CONVERT(COALESCE(m.full_name,'') USING utf8mb4) AS moderator_name,
        CONVERT('hidden_event' USING utf8mb4) AS activity_type
 FROM events e LEFT JOIN moderators m ON e.deleted_by = m.id
 WHERE e.is_deleted = 1 AND e.deleted_at IS NOT NULL)
UNION ALL
(SELECT pub.id, CONVERT(pub.society_name USING utf8mb4), pub.approved_at,
        CONVERT(COALESCE(m.full_name,'') USING utf8mb4), CONVERT('publisher_approved' USING utf8mb4)
 FROM publishers pub LEFT JOIN moderators m ON pub.approved_by = m.id
 WHERE pub.approval_status = 'approved' AND pub.approved_at IS NOT NULL)
UNION ALL
(SELECT pub.id, CONVERT(pub.society_name USING utf8mb4), pub.approved_at,
        CONVERT(COALESCE(m.full_name,'') USING utf8mb4), CONVERT('publisher_rejected' USING utf8mb4)
 FROM publishers pub LEFT JOIN moderators m ON pub.approved_by = m.id
 WHERE pub.approval_status = 'rejected' AND pub.approved_at IS NOT NULL)
ORDER BY activity_time DESC LIMIT 20";
$rows = $pdo->query($sql)->fetchAll();
echo "Total rows: " . count($rows) . "\n";
foreach ($rows as $r)
    echo "  [{$r->activity_type}]  mod={$r->moderator_name}  time={$r->activity_time}  title={$r->item_title}\n";
