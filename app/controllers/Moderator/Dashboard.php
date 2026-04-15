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

    /**
     * API endpoint for publisher performance report under moderator scope.
     */
    public function getPublisherPerformanceReport()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized',
                'rows' => []
            ]);
            return;
        }

        try {
            $moderatorUniversity = $this->resolveModeratorUniversity((int)($currentUser['id'] ?? 0), (string)($currentUser['university'] ?? ''));
            if ($moderatorUniversity === '') {
                echo json_encode([
                    'success' => true,
                    'rows' => [],
                    'summary' => [
                        'publisher_count' => 0,
                        'total_events' => 0,
                        'total_tickets_sold' => 0,
                        'overall_average_rating' => null,
                    ]
                ]);
                return;
            }

            $moderatorModel = new Moderator();
            $rows = $moderatorModel->getPublisherPerformanceReportByUniversity($moderatorUniversity);

            $summary = $this->calculatePublisherPerformanceSummary($rows);

            echo json_encode([
                'success' => true,
                'rows' => $rows,
                'summary' => $summary
            ]);
        } catch (Exception $e) {
            error_log('Error in ModeratorDashboard::getPublisherPerformanceReport: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load publisher performance report',
                'rows' => []
            ]);
        }
    }

    /**
     * Download publisher performance report as styled PDF.
     */
    public function downloadPublisherPerformanceReport()
    {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'moderator') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $moderatorUniversity = $this->resolveModeratorUniversity((int)($currentUser['id'] ?? 0), (string)($currentUser['university'] ?? ''));
            $moderatorModel = new Moderator();
            $rows = $moderatorUniversity !== ''
                ? $moderatorModel->getPublisherPerformanceReportByUniversity($moderatorUniversity)
                : [];

            $summary = $this->calculatePublisherPerformanceSummary($rows);
            $pdf = $this->generateModeratorPublisherReportPDF($moderatorUniversity, $summary, $rows);

            if (ob_get_length()) {
                ob_clean();
            }

            $filename = 'moderator-publisher-performance-' . date('Y-m-d') . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Exception $e) {
            error_log('Error in ModeratorDashboard::downloadPublisherPerformanceReport: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to generate report']);
        }
    }

    private function resolveModeratorUniversity($moderatorId, $sessionUniversity = '')
    {
        $moderatorUniversity = trim((string)$sessionUniversity);
        if ($moderatorUniversity !== '') {
            return $moderatorUniversity;
        }

        if ($moderatorId <= 0) {
            return '';
        }

        $moderatorModel = new Moderator();
        $moderatorRow = $moderatorModel->findById($moderatorId);
        return trim((string)($moderatorRow->university ?? ''));
    }

    private function calculatePublisherPerformanceSummary($rows)
    {
        $publisherCount = is_array($rows) ? count($rows) : 0;
        $totalEvents = 0;
        $totalTicketsSold = 0;
        $ratingWeightedTotal = 0.0;
        $ratingSampleCount = 0;

        foreach ($rows ?: [] as $row) {
            $totalEvents += (int)($row->total_events_posted ?? 0);
            $totalTicketsSold += (int)($row->tickets_sold ?? 0);

            $ratingCount = (int)($row->total_ratings ?? 0);
            $avgRating = $row->average_rating !== null ? (float)$row->average_rating : null;
            if ($avgRating !== null && $ratingCount > 0) {
                $ratingWeightedTotal += ($avgRating * $ratingCount);
                $ratingSampleCount += $ratingCount;
            }
        }

        return [
            'publisher_count' => $publisherCount,
            'total_events' => $totalEvents,
            'total_tickets_sold' => $totalTicketsSold,
            'overall_average_rating' => $ratingSampleCount > 0
                ? round($ratingWeightedTotal / $ratingSampleCount, 2)
                : null,
        ];
    }

    private function generateModeratorPublisherReportPDF($university, $summary, $rows)
    {
        $periodLabel = 'University Scope: ' . ucwords(str_replace('-', ' ', (string)$university));

        $publisherCount = (int)($summary['publisher_count'] ?? 0);
        $totalEvents = (int)($summary['total_events'] ?? 0);
        $totalTickets = (int)($summary['total_tickets_sold'] ?? 0);
        $overallRating = $summary['overall_average_rating'] !== null
            ? number_format((float)$summary['overall_average_rating'], 2)
            : 'N/A';

        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 42;
        $contentRight = $pageWidth - $marginX;

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, [248, 250, 252]);
        $content .= $this->pdfLinearGradientRect(0, 694, $pageWidth, 98, [30, 58, 138], [249, 115, 22], 50);
        $content .= $this->pdfText($marginX, 754, 'UniPulse Moderator Report', 'F2', 20, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 734, 'Publisher Performance Overview', 'F2', 12, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 718, $periodLabel . '  |  Generated ' . date('M d, Y'), 'F1', 9, [219, 234, 254]);

        $content .= $this->pdfRect($contentRight - 184, 718, 172, 44, null, [255, 237, 213], 0.8);
        $content .= $this->pdfText($contentRight - 174, 744, 'Report Type', 'F1', 8.5, [255, 237, 213]);
        $content .= $this->pdfText($contentRight - 174, 727, 'Publisher Performance', 'F2', 11.0, [255, 255, 255]);

        $content .= $this->pdfRect($marginX, 652, 132, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 10, 673, 'Publishers', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 10, 659, number_format($publisherCount), 'F2', 11.5, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 142, 652, 132, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 152, 673, 'Events Posted', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 152, 659, number_format($totalEvents), 'F2', 11.5, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 284, 652, 132, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 294, 673, 'Tickets Sold', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 294, 659, number_format($totalTickets), 'F2', 11.5, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 426, 652, 144, 34, [236, 253, 245], [167, 243, 208], 0.8);
        $content .= $this->pdfText($marginX + 436, 673, 'Overall Avg Rating', 'F1', 8.5, [6, 95, 70]);
        $content .= $this->pdfText($marginX + 436, 659, $overallRating, 'F2', 12.0, [6, 95, 70]);

        $tableX = $marginX;
        $tableW = $contentRight - $tableX;
        $rowH = 18;
        $rowY = 620;

        $content .= $this->pdfText($tableX, $rowY + 16, 'Publisher Breakdown', 'F2', 12, [30, 58, 138]);
        $rowY -= 8;

        $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [30, 58, 138], [249, 115, 22], 30);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'PUBLISHER', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 278, $rowY + 6, 'EVENTS', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 348, $rowY + 6, 'TICKETS SOLD', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 444, $rowY + 6, 'RATINGS', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 510, $rowY + 6, 'AVG', 'F2', 8.2, [255, 255, 255]);
        $rowY -= $rowH;

        if (empty($rows)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No publisher data found for this moderator scope.', 'F1', 8.4, [100, 116, 139]);
            $rowY -= $rowH;
        } else {
            $maxRows = min(count($rows), 18);
            for ($i = 0; $i < $maxRows; $i++) {
                $row = $rows[$i];
                $bg = ($i % 2 === 0) ? [255, 255, 255] : [248, 250, 252];

                $publisherName = $this->truncatePDFText((string)($row->society_name ?? 'Unknown Publisher'), 34);
                $eventsPosted = (int)($row->total_events_posted ?? 0);
                $ticketsSold = (int)($row->tickets_sold ?? 0);
                $ratings = (int)($row->total_ratings ?? 0);
                $avg = $row->average_rating !== null ? number_format((float)$row->average_rating, 2) : 'N/A';

                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, $publisherName, 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 278, $rowY + 6, number_format($eventsPosted), 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 348, $rowY + 6, number_format($ticketsSold), 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 444, $rowY + 6, number_format($ratings), 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 510, $rowY + 6, $avg, 'F2', 8.0, [6, 95, 70]);
                $rowY -= $rowH;
            }

            if (count($rows) > $maxRows) {
                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 251, 235], [253, 230, 138], 0.6);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, 'Showing first ' . $maxRows . ' publishers in this PDF export.', 'F1', 7.8, [120, 53, 15]);
                $rowY -= $rowH;
            }
        }

        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 26, [30, 58, 138], [249, 115, 22], 30);
        $content .= $this->pdfText($marginX, 8, 'UniPulse  |  Moderator Publisher Performance Report  |  ' . date('M d, Y'), 'F1', 8.2, [219, 234, 254]);

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $index => $objectBody) {
            $offsets[$index] = strlen($pdf);
            $pdf .= $index . " 0 obj\n" . $objectBody . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($objects as $index => $_) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$index]) . "\n";
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText($text)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string)$text);
    }

    private function pdfRect($x, $y, $width, $height, $fillColor = null, $strokeColor = null, $lineWidth = 1)
    {
        $cmd = '';
        if ($fillColor !== null) {
            $cmd .= $this->pdfColor($fillColor, false);
        }
        if ($strokeColor !== null) {
            $cmd .= $this->pdfColor($strokeColor, true);
            $cmd .= number_format($lineWidth, 2, '.', '') . " w\n";
        }

        $op = 'n';
        if ($fillColor !== null && $strokeColor !== null) {
            $op = 'B';
        } elseif ($fillColor !== null) {
            $op = 'f';
        } elseif ($strokeColor !== null) {
            $op = 'S';
        }

        return $cmd
            . number_format($x, 2, '.', '') . ' '
            . number_format($y, 2, '.', '') . ' '
            . number_format($width, 2, '.', '') . ' '
            . number_format($height, 2, '.', '') . " re $op\n";
    }

    private function pdfLinearGradientRect($x, $y, $width, $height, $startColor, $endColor, $steps = 32)
    {
        $steps = max(1, (int)$steps);
        $segmentW = $width / $steps;
        $cmd = '';

        for ($i = 0; $i < $steps; $i++) {
            $ratio = $steps === 1 ? 0 : ($i / ($steps - 1));
            $color = [
                (int)round($startColor[0] + (($endColor[0] - $startColor[0]) * $ratio)),
                (int)round($startColor[1] + (($endColor[1] - $startColor[1]) * $ratio)),
                (int)round($startColor[2] + (($endColor[2] - $startColor[2]) * $ratio)),
            ];

            $cmd .= $this->pdfRect($x + ($segmentW * $i), $y, $segmentW + 0.2, $height, $color);
        }

        return $cmd;
    }

    private function pdfText($x, $y, $text, $font = 'F1', $fontSize = 10, $color = [0, 0, 0])
    {
        return "BT\n"
            . $this->pdfColor($color, false)
            . "/{$font} " . number_format($fontSize, 2, '.', '') . " Tf\n"
            . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . " Td\n"
            . '(' . $this->escapePdfText($text) . ") Tj\n"
            . "ET\n";
    }

    private function pdfColor($rgb, $isStroke)
    {
        $r = number_format(((int)($rgb[0] ?? 0)) / 255, 3, '.', '');
        $g = number_format(((int)($rgb[1] ?? 0)) / 255, 3, '.', '');
        $b = number_format(((int)($rgb[2] ?? 0)) / 255, 3, '.', '');

        return $r . ' ' . $g . ' ' . $b . ($isStroke ? " RG\n" : " rg\n");
    }

    private function truncatePDFText($text, $maxChars)
    {
        $safeText = (string)$text;
        if (strlen($safeText) <= $maxChars) {
            return $safeText;
        }

        return rtrim(substr($safeText, 0, max(1, $maxChars - 1))) . '...';
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
