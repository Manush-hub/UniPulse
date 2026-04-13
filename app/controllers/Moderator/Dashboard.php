<?php

class ModeratorDashboard extends Controller {

    private $notificationReadScope = 'moderator_header_notifications';

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get moderator permissions and dashboard data
        $moderatorModel = new Moderator();
        $dashboardStats = $moderatorModel->getDashboardStats($data['user']['id']);
        
        if (!$dashboardStats) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data['permissions'] = $dashboardStats['permissions'];
        $data['moderator'] = $dashboardStats['moderator'];
        $data['publisher_stats'] = $dashboardStats['publisher_stats'];
        
        // Get recent pending publishers if moderator has permission
        if (isset($data['permissions']['approve_publishers']) && $data['permissions']['approve_publishers']) {
            $publisherModel = new Publisher();
            $pendingPublishers = $publisherModel->getPendingByUniversity($data['moderator']->university);
            $data['recent_pending_publishers'] = $pendingPublishers ?: [];
            // Limit to 5 most recent
            if (is_array($data['recent_pending_publishers']) && count($data['recent_pending_publishers']) > 5) {
                $data['recent_pending_publishers'] = array_slice($data['recent_pending_publishers'], 0, 5);
            }
        }
        
        // Get all recent moderation activities (all moderators, so the log is never empty)
        $eventModel = new Event();
        $data['recent_activities'] = $eventModel->getRecentModerationActivities(null, 50);

        // Get recent user reports for this moderator's university
        try {
            $reportModel = new Report();
            $data['user_reports'] = $reportModel->getReportsForUniversity($data['moderator']->university, 20) ?: [];
        } catch (Exception $e) {
            error_log("Dashboard reports error: " . $e->getMessage());
            $data['user_reports'] = [];
        }
        
        // Calculate moderation stats using direct PDO connection
        try {
            $string = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $conn = new PDO($string, DBUSER, DBPASS, $options);
            
            $moderatorId = $data['user']['id'];

            // Count hidden events by this moderator
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM events WHERE is_deleted = 1 AND moderated_by = :mid");
            $stmt->execute([':mid' => $moderatorId]);
            $data['moderation_stats']['hidden_events'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Count approved publishers handled by this moderator
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'approved' AND approved_by = :mid");
            $stmt->execute([':mid' => $moderatorId]);
            $data['moderation_stats']['approved_publishers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Count rejected publishers handled by this moderator
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM publishers WHERE approval_status = 'rejected' AND approved_by = :mid");
            $stmt->execute([':mid' => $moderatorId]);
            $data['moderation_stats']['rejected_publishers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            // Total moderation actions
            $data['moderation_stats']['total_actions'] = $data['moderation_stats']['hidden_events'] + 
                                                          $data['moderation_stats']['approved_publishers'] + 
                                                          $data['moderation_stats']['rejected_publishers'];
        } catch (PDOException $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
            // Set default values if query fails
            $data['moderation_stats']['hidden_events'] = 0;
            $data['moderation_stats']['approved_publishers'] = 0;
            $data['moderation_stats']['rejected_publishers'] = 0;
            $data['moderation_stats']['total_actions'] = 0;
        }
        
        $this->view('Moderator/dashboard', $data);
    }
    
    /**
     * API endpoint to get recent moderation activities
     */
    public function getActivities() {
        header('Content-Type: application/json');
        
        // Check authentication
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            $eventModel = new Event();
            $activities = $eventModel->getRecentModerationActivities($currentUser['id'], 20);
            
            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            error_log("getActivities error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load activities'
            ]);
        }
        
        exit;
    }

    /**
     * Header notifications for moderators.
     * Includes: new events by publishers in moderator's university,
     * and new comments on those publisher events.
     */
    public function getNotifications()
    {
        header('Content-Type: application/json');
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'moderator') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized', 'notifications' => []]);
            return;
        }

        try {
            $moderatorId = (int)($currentUser['id'] ?? 0);
            $moderatorUniversity = trim((string)($currentUser['university'] ?? ''));

            if ($moderatorUniversity === '') {
                $moderatorModel = new Moderator();
                $moderatorRow = $moderatorModel->findById($moderatorId);
                $moderatorUniversity = trim((string)($moderatorRow->university ?? ''));
            }

            if ($moderatorUniversity === '') {
                echo json_encode(['success' => true, 'notifications' => [], 'unread_count' => 0]);
                return;
            }

            $eventModel = new Event();
            $notificationModel = new Notification();
            $notificationReadStateModel = new NotificationReadState();

            $notifications = [];
            $unreadCount = 0;

            $lastReadAt = $notificationReadStateModel->getLastReadAt(
                $moderatorId,
                'moderator',
                $this->notificationReadScope
            );
            $readItemsMap = $notificationReadStateModel->getReadItemsMap($moderatorId, 'moderator');

            // New events by publishers in moderator's university.
            $eventRows = $eventModel->query(
                "SELECT
                    e.id,
                    e.title,
                    COALESCE(e.updated_at, e.created_at) AS notification_time,
                    p.society_name AS publisher_name
                 FROM events e
                 INNER JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                 WHERE e.is_deleted = 0
                   AND p.university = :moderator_university
                 ORDER BY COALESCE(e.updated_at, e.created_at) DESC
                 LIMIT 60",
                ['moderator_university' => $moderatorUniversity]
            ) ?: [];

            foreach ($eventRows as $row) {
                $createdAt = (string)($row->notification_time ?? date('Y-m-d H:i:s'));
                $notificationKey = 'mod_event|' . (int)($row->id ?? 0) . '|' . $createdAt;
                $isMarkedByTime = strtotime($createdAt) <= strtotime($lastReadAt);
                $isMarkedIndividually = isset($readItemsMap[$notificationKey]);
                $isUnread = !($isMarkedByTime || $isMarkedIndividually);

                if ($isUnread) {
                    $unreadCount++;
                }

                $notifications[] = [
                    'id' => (int)($row->id ?? 0),
                    'title' => 'New Event Published',
                    'message' => (string)($row->publisher_name ?? 'A publisher') . " published \"" . (string)($row->title ?? 'an event') . "\".",
                    'time' => $this->formatRelativeTime($createdAt),
                    'read' => !$isUnread,
                    'created_at' => $createdAt,
                    'notification_key' => $notificationKey,
                    'notification_category' => 'moderator_new_event',
                    'redirect_url' => '/unipulse/public/moderator/eventview?id=' . (int)($row->id ?? 0)
                ];
            }

            // New comments on events from publishers in moderator's university.
            $commentRows = $eventModel->query(
                "SELECT
                    c.id AS comment_id,
                    c.event_id,
                    c.created_at,
                    e.title AS event_title,
                    CASE
                        WHEN c.user_type = 'university' THEN uu.full_name
                        WHEN c.user_type = 'public' THEN pu.full_name
                        WHEN c.user_type = 'publisher' THEN pub.society_name
                        WHEN c.user_type = 'sponsor' THEN s.company_name
                        ELSE 'A user'
                    END AS commenter_name
                 FROM event_comments c
                 INNER JOIN events e ON e.id = c.event_id
                 INNER JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                 LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                 LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                 LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                 LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
                 WHERE c.is_deleted = 0
                   AND c.is_hidden = 0
                   AND p.university = :moderator_university
                 ORDER BY c.created_at DESC
                 LIMIT 60",
                ['moderator_university' => $moderatorUniversity]
            ) ?: [];

            foreach ($commentRows as $row) {
                $createdAt = (string)($row->created_at ?? date('Y-m-d H:i:s'));
                $notificationKey = 'mod_comment|' . (int)($row->comment_id ?? 0) . '|' . $createdAt;
                $isMarkedByTime = strtotime($createdAt) <= strtotime($lastReadAt);
                $isMarkedIndividually = isset($readItemsMap[$notificationKey]);
                $isUnread = !($isMarkedByTime || $isMarkedIndividually);

                if ($isUnread) {
                    $unreadCount++;
                }

                $notifications[] = [
                    'id' => (int)($row->comment_id ?? 0),
                    'title' => 'New Comment Added',
                    'message' => (string)($row->commenter_name ?? 'A user') . " commented on \"" . (string)($row->event_title ?? 'an event') . "\".",
                    'time' => $this->formatRelativeTime($createdAt),
                    'read' => !$isUnread,
                    'created_at' => $createdAt,
                    'notification_key' => $notificationKey,
                    'notification_category' => 'moderator_new_comment',
                    'redirect_url' => '/unipulse/public/moderator/comments'
                ];
            }

            // Include stored moderator notifications if available.
            $storedNotifications = $notificationModel->getUserNotifications($moderatorId, 'moderator', 50);
            foreach ($storedNotifications ?: [] as $notification) {
                $createdAt = (string)($notification->created_at ?? date('Y-m-d H:i:s'));
                $relatedId = (int)($notification->related_id ?? 0);
                $isRead = (bool)($notification->is_read ?? 0);

                $notifications[] = [
                    'id' => $relatedId,
                    'notification_id' => (int)($notification->id ?? 0),
                    'title' => (string)($notification->title ?? 'Notification'),
                    'message' => (string)($notification->message ?? ''),
                    'time' => $this->formatRelativeTime($createdAt),
                    'read' => $isRead,
                    'created_at' => $createdAt,
                    'notification_key' => 'db|' . (int)($notification->id ?? 0),
                    'notification_category' => (string)($notification->type ?? 'notification'),
                    'redirect_url' => '/unipulse/public/moderator/dashboard'
                ];

                if (!$isRead) {
                    $unreadCount++;
                }
            }

            usort($notifications, function ($a, $b) {
                return strtotime($b['created_at'] ?? '1970-01-01 00:00:00') <=> strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
            });

            echo json_encode([
                'success' => true,
                'notifications' => array_slice($notifications, 0, 20),
                'unread_count' => $unreadCount
            ]);
        } catch (Exception $e) {
            error_log('Error in ModeratorDashboard::getNotifications: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load notifications',
                'notifications' => []
            ]);
        }
    }

    /**
     * Mark all moderator header notifications as read.
     */
    public function markAllNotificationsRead()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'moderator') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $moderatorId = (int)($currentUser['id'] ?? 0);

        $notificationReadStateModel = new NotificationReadState();
        $notificationReadStateModel->markAllRead($moderatorId, 'moderator', $this->notificationReadScope);

        $notificationModel = new Notification();
        $notificationModel->markAllAsRead($moderatorId, 'moderator');

        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    private function formatRelativeTime($dateTime)
    {
        $timestamp = strtotime((string)$dateTime);
        if ($timestamp === false) {
            return '';
        }

        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . 'm ago';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . 'h ago';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . 'd ago';
        }

        return date('M j, Y', $timestamp);
    }
}
