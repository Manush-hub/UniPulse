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

        // Calculate moderation stats from model (single source of truth)
        $data['moderation_stats'] = $moderatorModel->getModerationStats((int)$data['user']['id']);
        
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

    /**
     * API endpoint to get upcoming publisher events relevant to the moderator's university.
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'moderator') {
            echo json_encode([
                'success' => false,
                'events' => [],
                'message' => 'Unauthorized'
            ]);
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
                echo json_encode([
                    'success' => true,
                    'events' => []
                ]);
                return;
            }

            $eventModel = new Event();
            $rows = $eventModel->query(
                "SELECT
                    e.id,
                    e.title,
                    e.event_date,
                    e.event_time,
                    COALESCE(NULLIF(TRIM(e.venue_name), ''), NULLIF(TRIM(e.location), ''), 'Location TBA') AS location
                 FROM events e
                 INNER JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                 WHERE e.is_deleted = 0
                   AND p.university = :moderator_university
                   AND TIMESTAMP(e.event_date, COALESCE(e.event_time, '00:00:00')) >= NOW()
                 ORDER BY e.event_date ASC, e.event_time ASC
                 LIMIT 500",
                ['moderator_university' => $moderatorUniversity]
            ) ?: [];

            $events = [];
            foreach ($rows as $row) {
                $eventId = (int)($row->id ?? 0);
                if ($eventId <= 0) {
                    continue;
                }

                $events[] = [
                    'id' => $eventId,
                    'title' => (string)($row->title ?? 'Untitled Event'),
                    'date' => (string)($row->event_date ?? ''),
                    'time' => (string)($row->event_time ?? ''),
                    'location' => (string)($row->location ?? 'Location TBA')
                ];
            }

            echo json_encode([
                'success' => true,
                'events' => $events
            ]);
        } catch (Exception $e) {
            error_log('Error in ModeratorDashboard::getUpcomingEvents: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'events' => [],
                'message' => 'Failed to load events'
            ]);
        }
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
