<?php

class PublisherDashboard extends Controller
{

    use Database;

    private $notificationReadScope = 'publisher_dashboard';

    public function index($a = '', $b = '', $c = '')
    {
        // Allow publishers, admins, and moderators
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('Publisher/dashboard', $data);
    }

    /**
     * Display publisher monthly evolution report page.
     */
    public function monthlyEvolution($a = '', $b = '', $c = '')
    {
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $data = [
            'user' => $currentUser,
            'page_title' => 'Monthly Evolution'
        ];

        $this->view('Publisher/monthly-evolution', $data);
    }

    /**
     * Display completed-event profit report page.
     */
    public function eventProfitReport($a = '', $b = '', $c = '')
    {
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $data = [
            'user' => $currentUser,
            'page_title' => 'Completed Event Profit Report'
        ];

        $this->view('Publisher/event-profit-report', $data);
    }

    /**
     * Get header notifications for publisher users.
     * Source: newly visible events published by other publishers.
     */
    public function getNotifications()
    {
        header('Content-Type: application/json');
        if (ob_get_level() > 0) ob_clean();

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized', 'notifications' => []]);
            return;
        }

        try {
            if (!class_exists('Activity')) {
                require_once __DIR__ . '/../../models/Activity.php';
            }

            $eventModel = new Event();
            $activityModel = new Activity();
            $notificationModel = new Notification();
            $notificationReadStateModel = new NotificationReadState();

            $notifications = [];
            $unreadCount = 0;

            $publisherId = (int)($currentUser['id'] ?? 0);
            $lastReadAt = $notificationReadStateModel->getLastReadAt(
                $publisherId,
                'publisher',
                $this->notificationReadScope
            );
            $readItemsMap = $notificationReadStateModel->getReadItemsMap($publisherId, 'publisher');

            // Match visibility used by Publisher All Events page.
            $events = $eventModel->getAllEvents([
                'status' => 'upcoming',
                'limit' => 100,
                'offset' => 0
            ], $currentUser);

            if (!$events) {
                $events = [];
            }

            $otherPublisherEvents = [];

            foreach ($events as $event) {
                $eventOwnerId = isset($event->created_by) ? (int)$event->created_by : 0;
                $eventOwnerType = $event->created_by_type ?? '';

                if ($eventOwnerType === 'publisher' && $eventOwnerId > 0 && $eventOwnerId !== $publisherId) {
                    $otherPublisherEvents[] = $event;
                }
            }

            if (!empty($otherPublisherEvents)) {
                usort($otherPublisherEvents, function ($a, $b) {
                    $aTime = strtotime($a->updated_at ?? $a->created_at ?? '1970-01-01 00:00:00');
                    $bTime = strtotime($b->updated_at ?? $b->created_at ?? '1970-01-01 00:00:00');
                    return $bTime <=> $aTime;
                });

                foreach ($otherPublisherEvents as $event) {
                    $notificationTime = $event->updated_at ?? $event->created_at ?? date('Y-m-d H:i:s');
                    $eventId = (int)($event->id ?? 0);
                    $notificationKey = 'event|' . $eventId . '|' . $notificationTime;

                    $isMarkedByTime = strtotime($notificationTime) <= strtotime($lastReadAt);
                    $isMarkedIndividually = isset($readItemsMap[$notificationKey]);
                    $isUnread = !($isMarkedByTime || $isMarkedIndividually);

                    if ($isUnread) {
                        $unreadCount++;
                    }

                    $notifications[] = [
                        'id' => $eventId,
                        'title' => 'New Event Published',
                        'message' => ($event->title ?? 'A new event') . ' is now available in All Events.',
                        'time' => $this->formatRelativeTime($notificationTime),
                        'read' => !$isUnread,
                        'created_at' => $notificationTime,
                        'notification_key' => $notificationKey,
                        'notification_category' => 'new_event_published',
                        'redirect_url' => '/unipulse/public/publisher/eventview?id=' . $eventId
                    ];
                }
            }

            // Include stored notifications (comments, volunteer applications, etc.).
            $storedNotifications = $notificationModel->getUserNotifications($publisherId, 'publisher', 50);
            foreach ($storedNotifications ?: [] as $notification) {
                $relatedId = (int)($notification->related_id ?? 0);
                $createdAt = $notification->created_at ?? date('Y-m-d H:i:s');
                $notificationKey = 'db|' . (int)$notification->id;
                $title = (string)($notification->title ?? 'Notification');
                $notificationType = strtolower((string)($notification->type ?? 'notification'));
                $isVolunteerNotification =
                    stripos($title, 'volunteer application') !== false ||
                    $notificationType === 'volunteer_registration';
                $notificationMessage = (string)($notification->message ?? '');
                $isHiddenEventNotification =
                    $notificationType === 'event_hidden' ||
                    stripos($title, 'event hidden') !== false ||
                    stripos($notificationMessage, 'was hidden by') !== false;

                $redirectUrl = '/unipulse/public/publisher/dashboard';
                if ($isVolunteerNotification) {
                    $redirectUrl = '/unipulse/public/publisher/dashboard#volunteer-management';
                } elseif ($isHiddenEventNotification) {
                    $redirectUrl = '/unipulse/public/publisher/dashboard#events-management';
                } elseif ($relatedId > 0) {
                    $redirectUrl = '/unipulse/public/publisher/eventview?id=' . $relatedId;
                }

                $notifications[] = [
                    'id' => $relatedId,
                    'notification_id' => (int)$notification->id,
                    'title' => $title,
                    'message' => (string)($notification->message ?? ''),
                    'time' => $this->formatRelativeTime($createdAt),
                    'read' => (bool)($notification->is_read ?? 0),
                    'created_at' => $createdAt,
                    'notification_key' => $notificationKey,
                    'notification_category' => (string)($notification->type ?? 'notification'),
                    'redirect_url' => $redirectUrl
                ];

                if (!(bool)($notification->is_read ?? 0)) {
                    $unreadCount++;
                }
            }

            usort($notifications, function ($a, $b) {
                return strtotime($b['created_at'] ?? '1970-01-01 00:00:00') <=> strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
            });

            echo json_encode([
                'success' => true,
                'notifications' => array_slice($notifications, 0, 10),
                'unread_count' => $unreadCount
            ]);
        } catch (Exception $e) {
            error_log('Error in PublisherDashboard::getNotifications: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load notifications',
                'notifications' => []
            ]);
        }
    }

    /**
     * Mark a single publisher header notification as read
     */
    public function markNotificationRead()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $eventId = (int)($payload['event_id'] ?? 0);
        $notificationId = (int)($payload['notification_id'] ?? 0);
        $createdAt = trim((string)($payload['created_at'] ?? ''));
        $notificationReadStateModel = new NotificationReadState();

        if ($notificationId > 0) {
            $notificationModel = new Notification();
            $result = $notificationModel->markAsRead($notificationId, (int)$currentUser['id'], 'publisher');

            echo json_encode([
                'success' => (bool)$result,
                'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
            ]);
            return;
        }

        if ($eventId <= 0 || $createdAt === '') {
            echo json_encode(['success' => false, 'error' => 'Invalid notification payload']);
            return;
        }

        $notificationKey = 'event|' . $eventId . '|' . $createdAt;
        if (!empty($payload['notification_key'])) {
            $notificationKey = trim((string)$payload['notification_key']);
        }

        $result = $notificationReadStateModel->markRead((int)$currentUser['id'], 'publisher', $notificationKey);

        echo json_encode([
            'success' => (bool)$result,
            'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
        ]);
    }

    /**
     * Mark all publisher header notifications as read
     */
    public function markAllNotificationsRead()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $notificationReadStateModel = new NotificationReadState();
        $notificationReadStateModel->markAllRead((int)$currentUser['id'], 'publisher', $this->notificationReadScope);

        $notificationModel = new Notification();
        $notificationModel->markAllAsRead((int)$currentUser['id'], 'publisher');

        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * API endpoint to get all upcoming events visible to publisher users.
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $eventModel = new Event();
            $upcoming = $eventModel->getAllEvents([
                'status' => 'upcoming',
                'limit' => 300,
                'offset' => 0
            ], $currentUser);

            $upcomingEvents = [];
            foreach ($upcoming ?: [] as $event) {
                $eventDateTime = trim((string)$event->event_date . ' ' . ((string)$event->event_time !== '' ? (string)$event->event_time : '23:59:59'));
                $eventTimestamp = strtotime($eventDateTime);
                if ($eventTimestamp === false || $eventTimestamp < time()) {
                    continue;
                }

                $upcomingEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => isset($event->description) ? substr((string)$event->description, 0, 100) . '...' : '',
                    'date' => $event->event_date,
                    'time' => $event->event_time,
                    'location' => $event->location ?: ($event->venue_name ?: ($event->city ?? '')),
                    'category' => $event->category,
                    'university' => $event->university_name,
                    'image_url' => $event->image_url,
                    'organizer' => $event->organizer_name ?? ($event->organizer ?? ''),
                    'max_participants' => $event->max_participants,
                    'current_participants' => $event->current_participants ?? 0
                ];
            }

            usort($upcomingEvents, function ($a, $b) {
                $aDateTime = trim((string)$a['date'] . ' ' . ((string)$a['time'] !== '' ? (string)$a['time'] : '23:59:59'));
                $bDateTime = trim((string)$b['date'] . ' ' . ((string)$b['time'] !== '' ? (string)$b['time'] : '23:59:59'));

                $aTimestamp = strtotime($aDateTime) ?: 0;
                $bTimestamp = strtotime($bDateTime) ?: 0;

                return $aTimestamp <=> $bTimestamp;
            });

            echo json_encode([
                'success' => true,
                'events' => $upcomingEvents,
                'count' => count($upcomingEvents)
            ]);
        } catch (Exception $e) {
            error_log('Error in PublisherDashboard::getUpcomingEvents: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load upcoming events'
            ]);
        }
    }

    /**
     * Get recent comments for dashboard
     */
    public function getRecentComments()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get recent comments on publisher's events
            $query = "
                SELECT 
                    c.*,
                    e.title as event_title,
                    e.status as event_status,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.full_name
                        WHEN c.user_type = 'public' THEN pu.full_name
                        WHEN c.user_type = 'publisher' THEN pub.society_name
                        WHEN c.user_type = 'sponsor' THEN s.company_name
                        WHEN c.user_type = 'admin' THEN 'Admin'
                        WHEN c.user_type = 'moderator' THEN m.full_name
                    END as user_name,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.email
                        WHEN c.user_type = 'public' THEN pu.email
                        WHEN c.user_type = 'publisher' THEN pub.email
                        WHEN c.user_type = 'sponsor' THEN s.email
                        WHEN c.user_type = 'admin' THEN 'system@unipulse.com'
                        WHEN c.user_type = 'moderator' THEN m.email
                    END as user_email,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.profile_photo
                        WHEN c.user_type = 'public' THEN pu.profile_photo
                        WHEN c.user_type = 'publisher' THEN pp.logo_url
                        WHEN c.user_type = 'sponsor' THEN sp.logo_url
                        ELSE NULL
                    END as profile_photo
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                LEFT JOIN publisher_profiles pp ON c.user_type = 'publisher' AND pub.id = pp.publisher_id
                LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
                LEFT JOIN sponsor_profiles sp ON c.user_type = 'sponsor' AND s.id = sp.sponsor_id
                LEFT JOIN moderators m ON c.user_type = 'moderator' AND c.user_id = m.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
                ORDER BY c.created_at DESC
                LIMIT 10
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute(['publisher_id' => $currentUser['id']]);
            $comments = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Get comment statistics
            $statsQuery = "
                SELECT 
                    COUNT(c.id) as total_comments,
                    ROUND(AVG(CASE WHEN c.rating > 0 THEN c.rating END), 1) as average_rating
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
            ";

            $statsStmt = $this->connect()->prepare($statsQuery);
            $statsStmt->execute(['publisher_id' => $currentUser['id']]);
            $stats = $statsStmt->fetch(PDO::FETCH_OBJ);

            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'event_id' => $comment->event_id,
                    'event_title' => $comment->event_title,
                    'event_status' => $comment->event_status,
                    'user_name' => $comment->user_name,
                    'user_type' => $comment->user_type,
                    'profile_photo' => $comment->profile_photo ?? null,
                    'comment_text' => $comment->comment_text,
                    'rating' => $comment->rating,
                    'is_hidden' => (bool)$comment->is_hidden,
                    'hidden_reason' => $comment->hidden_reason,
                    'created_at' => $comment->created_at,
                    'formatted_date' => $this->formatDate($comment->created_at)
                ];
            }

            echo json_encode([
                'success' => true,
                'comments' => $formattedComments,
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'average_rating' => $stats->average_rating ? (float)$stats->average_rating : 0.0
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error getting recent comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }

    /**
     * Get publisher's events for dashboard
     */
    public function getMyEvents()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get filter from request (upcoming or past)
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

            // Build query based on user role
            $whereClause = "";
            $params = [];

            if ($currentUser['type'] === 'publisher') {
                // Publishers see only their own events
                $whereClause = "WHERE e.created_by_type = 'publisher' AND e.created_by = :publisher_id";
                $params['publisher_id'] = $currentUser['id'];
            } else {
                // Admins and moderators see all events (including completed ones)
                $whereClause = "WHERE 1=1"; // No filtering
            }

            // Add date filter
            if ($filter === 'upcoming') {
                $whereClause .= " AND e.event_date >= CURDATE()";
            } elseif ($filter === 'past') {
                $whereClause .= " AND e.event_date < CURDATE()";
            }

            // Upcoming should show nearest event first.
            // Past/completed should keep latest finished first.
            $orderByClause = "ORDER BY 
                CASE WHEN e.event_date >= CURDATE() THEN 0 ELSE 1 END ASC,
                CASE WHEN e.event_date >= CURDATE() THEN e.event_date END ASC,
                CASE WHEN e.event_date >= CURDATE() THEN COALESCE(e.event_time, '23:59:59') END ASC,
                CASE WHEN e.event_date < CURDATE() THEN e.event_date END DESC,
                CASE WHEN e.event_date < CURDATE() THEN COALESCE(e.event_time, '23:59:59') END DESC";
            if ($filter === 'upcoming') {
                $orderByClause = "ORDER BY e.event_date ASC, e.event_time ASC";
            } elseif ($filter === 'past') {
                $orderByClause = "ORDER BY e.event_date DESC, e.event_time DESC";
            }

            $query = "
                SELECT 
                    e.*,
                    p.society_name as organizer_name,
                    COUNT(DISTINCT ec.id) as comment_count,
                    COUNT(DISTINCT CASE WHEN ec.rating > 0 THEN ec.id END) as rating_count,
                    AVG(CASE WHEN ec.rating > 0 THEN ec.rating END) as avg_rating,
                    COUNT(DISTINCT CASE WHEN es.status = 'pending' THEN es.id END) as pending_sponsorships,
                    COUNT(DISTINCT es.id) as total_sponsorships,
                    COUNT(DISTINCT esp.id) as total_packages,
                    SUM(DISTINCT CASE WHEN esp.is_active = 1 THEN esp.available_slots ELSE 0 END) as total_slots,
                    SUM(DISTINCT CASE WHEN esp.is_active = 1 THEN esp.filled_slots ELSE 0 END) as filled_slots,
                    (SELECT COALESCE(SUM(amount), 0) FROM event_sponsorships WHERE event_id = e.id AND status = 'completed') as approved_budget,
                    (SELECT COALESCE(SUM(amount), 0) FROM event_sponsorships WHERE event_id = e.id AND status = 'pending') as pending_budget
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                LEFT JOIN event_comments ec ON e.id = ec.event_id AND ec.is_deleted = 0
                LEFT JOIN event_sponsorships es ON e.id = es.event_id
                LEFT JOIN event_sponsorship_packages esp ON e.id = esp.event_id
                $whereClause
                GROUP BY e.id
                $orderByClause
                LIMIT 50
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);

            $formattedEvents = [];
            $currentDate = date('Y-m-d');

            foreach ($events as $event) {
                // Preserve hidden status for soft-deleted events.
                $eventStatus = strtolower((string)($event->status ?? ''));
                $isDeleted = (int)($event->is_deleted ?? 0) === 1;

                if ($isDeleted || $eventStatus === 'hidden') {
                    $eventStatus = 'hidden';
                } else {
                    // Calculate active/completed status based on event date.
                    if ($event->event_date < $currentDate) {
                        $eventStatus = 'completed';
                    } elseif ($event->event_date == $currentDate) {
                        $eventStatus = 'ongoing';
                    } else {
                        $eventStatus = 'upcoming';
                    }
                }

                $formattedEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'event_date' => $event->event_date,
                    'event_time' => $event->event_time,
                    'location_type' => $event->location_type,
                    'exact_location' => $event->location ?? '', // Use 'location' field
                    'venue_name' => $event->venue_name ?? '',
                    'city' => $event->city ?? '',
                    'university_name' => $event->university_name ?? '',
                    'faculty_department' => $event->faculty_department ?? '',
                    'status' => $eventStatus,
                    'hidden_reason' => ($eventStatus === 'hidden')
                        ? (string)($event->deletion_reason ?? 'Hidden by moderator')
                        : null,
                    'category' => $event->category,
                    'cover_image' => $event->cover_image ?? $event->image_url ?? '',
                    'organizer_name' => $event->organizer_name ?? $event->organizer ?? '',
                    'postponed_count' => (int)($event->postponed_count ?? 0),
                    'ticket_type' => $event->ticket_type ?? 'free-all',
                    'ticket_types' => $event->ticket_types ?? '[]',
                    'ticket_price_free' => 0, // Not stored separately in database
                    'ticket_price_paid' => 0, // Not stored separately in database
                    'current_participants' => $event->current_participants ?? 0,
                    'max_participants' => $event->max_participants ?? null,
                    'comment_count' => (int)($event->comment_count ?? 0),
                    'rating_count' => (int)($event->rating_count ?? 0),
                    'avg_rating' => $event->avg_rating ? round((float)$event->avg_rating, 1) : null,
                    'pending_sponsorships' => (int)($event->pending_sponsorships ?? 0),
                    'total_sponsorships' => (int)($event->total_sponsorships ?? 0),
                    'accepts_sponsorships' => (int)($event->accepts_sponsorships ?? 0),
                    'sponsorship_stats' => [
                        'total_packages' => (int)($event->total_packages ?? 0),
                        'total_slots' => (int)($event->total_slots ?? 0),
                        'filled_slots' => (int)($event->filled_slots ?? 0),
                        'available_slots' => (int)(($event->total_slots ?? 0) - ($event->filled_slots ?? 0)),
                        'approved_budget' => (float)($event->approved_budget ?? 0),
                        'pending_budget' => (float)($event->pending_budget ?? 0),
                        'total_budget' => (float)(($event->approved_budget ?? 0) + ($event->pending_budget ?? 0))
                    ]
                ];
            }

            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'total' => count($formattedEvents)
            ]);
        } catch (Exception $e) {
            error_log("Error getting publisher events: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load events']);
        }
    }

    /**
     * Get registration & ticketing data grouped by publisher events.
     */
    public function getRegistrationTicketing()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $params = [];
            $whereClause = '';

            if (($currentUser['type'] ?? '') === 'publisher') {
                $whereClause = "WHERE e.created_by_type = 'publisher' AND e.created_by = :publisher_id";
                $params['publisher_id'] = (int)$currentUser['id'];
            }

            $upcomingCondition = "e.event_date >= CURDATE() AND e.is_deleted = 0 AND (e.status IS NULL OR e.status NOT IN ('completed', 'cancelled'))";

            if (trim($whereClause) === '') {
                $eventsWhere = "WHERE {$upcomingCondition}";
            } else {
                $eventsWhere = $whereClause . " AND {$upcomingCondition}";
            }

            $eventsQuery = "
                SELECT e.id, e.title, e.ticket_type, e.event_date
                FROM events e
                {$eventsWhere}
                ORDER BY e.event_date ASC, e.id DESC
                LIMIT 100
            ";

            $eventStmt = $this->connect()->prepare($eventsQuery);
            $eventStmt->execute($params);
            $events = $eventStmt->fetchAll(PDO::FETCH_OBJ);

            $result = [];

            foreach ($events as $event) {
                $eventRows = [];
                $eventId = (int)$event->id;

                try {
                    $freeQuery = "
                        SELECT
                            COALESCE(fr.registered_user_name_snapshot, CONCAT('User #', fr.registered_user_id)) AS user_name,
                            'free' AS ticket_type,
                            1 AS ticket_quantity,
                            0.00 AS amount,
                            fr.registered_at AS registered_at
                        FROM free_event_registrations fr
                        WHERE fr.event_id = :event_id
                          AND fr.status IN ('registered', 'checked_in')
                    ";

                    $freeStmt = $this->connect()->prepare($freeQuery);
                    $freeStmt->execute(['event_id' => $eventId]);
                    $freeRows = $freeStmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($freeRows)) {
                        $eventRows = array_merge($eventRows, $freeRows);
                    }
                } catch (Throwable $freeTableError) {
                    error_log('Registration ticketing free table query warning: ' . $freeTableError->getMessage());
                }

                try {
                    $paidQuery = "
                        SELECT
                            COALESCE(pr.registered_user_name_snapshot, CONCAT('User #', pr.registered_user_id)) AS user_name,
                            'paid' AS ticket_type,
                            COALESCE(pr.ticket_quantity, 1) AS ticket_quantity,
                            COALESCE(pr.total_amount, 0.00) AS amount,
                            pr.paid_at AS registered_at
                        FROM paid_event_registrations pr
                        WHERE pr.event_id = :event_id
                          AND pr.registration_status IN ('confirmed', 'checked_in')
                          AND pr.payment_status IN ('paid', 'partially_refunded', 'refunded', 'completed')
                    ";

                    $paidStmt = $this->connect()->prepare($paidQuery);
                    $paidStmt->execute(['event_id' => $eventId]);
                    $paidRows = $paidStmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($paidRows)) {
                        $eventRows = array_merge($eventRows, $paidRows);
                    }
                } catch (Throwable $paidTableError) {
                    error_log('Registration ticketing paid table query warning: ' . $paidTableError->getMessage());
                }

                usort($eventRows, function ($a, $b) {
                    $aTime = strtotime($a['registered_at'] ?? '1970-01-01 00:00:00');
                    $bTime = strtotime($b['registered_at'] ?? '1970-01-01 00:00:00');
                    return $bTime <=> $aTime;
                });

                $result[] = [
                    'event_id' => $eventId,
                    'event_title' => $event->title,
                    'ticket_type' => $event->ticket_type,
                    'event_date' => $event->event_date,
                    'registrations' => array_map(function ($row) {
                        return [
                            'user_name' => (string)($row['user_name'] ?? 'Unknown User'),
                            'ticket_type' => (string)($row['ticket_type'] ?? 'free'),
                            'ticket_quantity' => (int)($row['ticket_quantity'] ?? 1),
                            'amount' => (float)($row['amount'] ?? 0),
                            'registered_at' => $row['registered_at'] ?? null
                        ];
                    }, $eventRows)
                ];
            }

            echo json_encode([
                'success' => true,
                'events' => $result
            ]);
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::getRegistrationTicketing: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load registration and ticketing data'
            ]);
        }
    }

    /**
     * Get event-wise ticket totals and net revenue for publisher dashboard.
     */
    public function getEventRevenue()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $params = [];
            $whereClause = '';

            if (($currentUser['type'] ?? '') === 'publisher') {
                $whereClause = "WHERE e.created_by_type = 'publisher' AND e.created_by = :publisher_id";
                $params['publisher_id'] = (int)$currentUser['id'];
            }

            $upcomingCondition = "e.event_date >= CURDATE() AND e.is_deleted = 0 AND (e.status IS NULL OR e.status NOT IN ('completed', 'cancelled'))";
            $eventsWhere = trim($whereClause) === ''
                ? "WHERE {$upcomingCondition}"
                : $whereClause . " AND {$upcomingCondition}";

            $eventsQuery = "
                SELECT e.id, e.title, e.event_date
                FROM events e
                {$eventsWhere}
                ORDER BY e.event_date ASC, e.id DESC
                LIMIT 100
            ";

            $eventStmt = $this->connect()->prepare($eventsQuery);
            $eventStmt->execute($params);
            $events = $eventStmt->fetchAll(PDO::FETCH_OBJ);

            $result = [];

            foreach ($events as $event) {
                $eventId = (int)$event->id;
                $freeTickets = 0;
                $paidTicketCount = 0;
                $netRevenue = 0.0;

                try {
                    $freeCountQuery = "
                        SELECT COUNT(*) AS free_ticket_count
                        FROM free_event_registrations fr
                        WHERE fr.event_id = :event_id
                          AND fr.status IN ('registered', 'checked_in')
                    ";

                    $freeCountStmt = $this->connect()->prepare($freeCountQuery);
                    $freeCountStmt->execute(['event_id' => $eventId]);
                    $freeData = $freeCountStmt->fetch(PDO::FETCH_ASSOC);
                    $freeTickets = (int)($freeData['free_ticket_count'] ?? 0);
                } catch (Throwable $freeTableError) {
                    error_log('Event revenue free table query warning: ' . $freeTableError->getMessage());
                }

                try {
                    $paidSummaryQuery = "
                        SELECT
                            COALESCE(SUM(pr.ticket_quantity), 0) AS paid_ticket_count,
                            COALESCE(SUM(
                                CASE
                                    WHEN pr.payment_status IN ('paid', 'partially_refunded', 'refunded')
                                        THEN GREATEST(COALESCE(pr.total_amount, 0) - COALESCE(pr.refund_amount, 0), 0)
                                    ELSE 0
                                END
                            ), 0) AS net_revenue
                        FROM paid_event_registrations pr
                        WHERE pr.event_id = :event_id
                          AND pr.registration_status IN ('confirmed', 'checked_in')
                    ";

                    $paidSummaryStmt = $this->connect()->prepare($paidSummaryQuery);
                    $paidSummaryStmt->execute(['event_id' => $eventId]);
                    $paidData = $paidSummaryStmt->fetch(PDO::FETCH_ASSOC);

                    $paidTicketCount = (int)($paidData['paid_ticket_count'] ?? 0);
                    $netRevenue = (float)($paidData['net_revenue'] ?? 0);
                } catch (Throwable $paidTableError) {
                    error_log('Event revenue paid table query warning: ' . $paidTableError->getMessage());
                }

                $result[] = [
                    'event_id' => $eventId,
                    'event_title' => (string)$event->title,
                    'event_date' => $event->event_date,
                    'ticket_amount' => $freeTickets + $paidTicketCount,
                    'total_revenue' => round($netRevenue, 2)
                ];
            }

            echo json_encode([
                'success' => true,
                'events' => $result
            ]);
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::getEventRevenue: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load event revenue data'
            ]);
        }
    }

    private function tableExists($tableName)
    {
        try {
            $result = $this->query(
                "SELECT COUNT(*) AS table_count
                 FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = :schema_name
                   AND TABLE_NAME = :table_name",
                [
                    'schema_name' => DBNAME,
                    'table_name' => $tableName
                ]
            );

            return !empty($result) && (int)($result[0]->table_count ?? 0) > 0;
        } catch (Throwable $e) {
            error_log('Table existence check failed for ' . $tableName . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get volunteer applications and shifts for publisher dashboard
     */
    public function getVolunteerData()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $query = "
                SELECT
                    vr.id,
                    vr.user_id,
                    vr.user_type,
                    vr.event_id,
                    vr.volunteer_position,
                    vr.availability,
                    vr.experience,
                    vr.skills,
                    vr.motivation,
                    vr.status,
                    vr.created_at,
                    e.title as event_title,
                    e.event_date,
                    CASE
                        WHEN vr.user_type = 'university' THEN uu.full_name
                        WHEN vr.user_type = 'public' THEN pu.full_name
                        WHEN vr.user_type = 'publisher' THEN p.society_name
                        WHEN vr.user_type = 'sponsor' THEN s.company_name
                        ELSE CONCAT('User #', vr.user_id)
                    END as volunteer_name,
                    CASE
                        WHEN vr.user_type = 'university' THEN uu.email
                        WHEN vr.user_type = 'public' THEN pu.email
                        WHEN vr.user_type = 'publisher' THEN p.email
                        WHEN vr.user_type = 'sponsor' THEN s.email
                        ELSE NULL
                    END as volunteer_email,
                    CASE
                        WHEN vr.user_type = 'university' THEN uu.phone
                        WHEN vr.user_type = 'public' THEN pu.phone
                        WHEN vr.user_type = 'publisher' THEN p.phone
                        WHEN vr.user_type = 'sponsor' THEN s.phone
                        ELSE NULL
                    END as volunteer_phone
                FROM volunteer_registrations vr
                INNER JOIN events e ON e.id = vr.event_id
                LEFT JOIN university_users uu ON vr.user_type = 'university' AND vr.user_id = uu.id
                LEFT JOIN public_users pu ON vr.user_type = 'public' AND vr.user_id = pu.id
                LEFT JOIN publishers p ON vr.user_type = 'publisher' AND vr.user_id = p.id
                LEFT JOIN sponsors s ON vr.user_type = 'sponsor' AND vr.user_id = s.id
                WHERE e.created_by_type = 'publisher'
                  AND e.created_by = :publisher_id
                  AND vr.status != 'withdrawn'
                ORDER BY vr.created_at DESC
                LIMIT 50
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute(['publisher_id' => $currentUser['id']]);
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

            $applications = [];
            $shifts = [];

            foreach ($rows as $row) {
                $name = $row->volunteer_name ?: 'Volunteer';
                $applications[] = [
                    'id' => $row->id,
                    'name' => $name,
                    'email' => $row->volunteer_email ?: null,
                    'phone' => $row->volunteer_phone ?: null,
                    'user_type' => $row->user_type,
                    'event_title' => $row->event_title,
                    'event_date' => $row->event_date,
                    'role' => $row->volunteer_position ?: 'General Volunteer',
                    'availability' => $row->availability ?: null,
                    'experience' => $row->experience ?: null,
                    'skills' => $row->skills ?: null,
                    'motivation' => $row->motivation ?: null,
                    'status' => $row->status,
                    'applied_at' => $row->created_at
                ];

                if ($row->status === 'accepted') {
                    $shifts[] = [
                        'id' => $row->id,
                        'name' => $name,
                        'email' => $row->volunteer_email ?: null,
                        'phone' => $row->volunteer_phone ?: null,
                        'user_type' => $row->user_type,
                        'event_title' => $row->event_title,
                        'event_date' => $row->event_date,
                        'role' => $row->volunteer_position ?: 'General Volunteer',
                        'shift' => $row->availability ?: 'Schedule pending',
                        'status' => $row->status
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'applications' => array_slice($applications, 0, 8),
                'shifts' => array_slice($shifts, 0, 8)
            ]);
        } catch (Exception $e) {
            error_log('Error getting volunteer data: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load volunteer data']);
        }
    }

    /**
     * Update volunteer application status (publisher management)
     */
    public function updateVolunteerStatus()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $registrationId = $_POST['registration_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        $allowedStatuses = ['accepted', 'rejected', 'pending'];

        if (!$registrationId || !is_numeric($registrationId) || !in_array($newStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'error' => 'Invalid request data']);
            return;
        }

        try {
            $ownershipQuery = "
                SELECT vr.id, vr.event_id, vr.status AS current_status, e.volunteers_needed
                FROM volunteer_registrations vr
                INNER JOIN events e ON e.id = vr.event_id
                WHERE vr.id = :registration_id
                  AND e.created_by_type = 'publisher'
                  AND e.created_by = :publisher_id
                LIMIT 1
            ";

            $stmt = $this->connect()->prepare($ownershipQuery);
            $stmt->execute([
                'registration_id' => $registrationId,
                'publisher_id' => $currentUser['id']
            ]);

            $owned = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$owned) {
                echo json_encode(['success' => false, 'error' => 'Application not found for your events']);
                return;
            }

            $currentStatus = strtolower((string)($owned->current_status ?? 'pending'));
            $isChangingToAccepted = ($newStatus === 'accepted' && $currentStatus !== 'accepted');
            $isChangingFromAccepted = ($newStatus !== 'accepted' && $currentStatus === 'accepted');

            if ($isChangingToAccepted && !is_null($owned->volunteers_needed) && (int)$owned->volunteers_needed <= 0) {
                echo json_encode(['success' => false, 'error' => 'No volunteer slots remaining for this event']);
                return;
            }

            $volunteerReg = new VolunteerRegistration();
            $eventModel = new Event();
            $db = $this->connect();
            $db->beginTransaction();

            $updated = $volunteerReg->updateStatus((int)$registrationId, $newStatus);

            if (!$updated) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                echo json_encode(['success' => false, 'error' => 'Failed to update status']);
                return;
            }

            if (!is_null($owned->volunteers_needed)) {
                $currentNeeded = (int)$owned->volunteers_needed;

                if ($isChangingToAccepted) {
                    $currentNeeded = max(0, $currentNeeded - 1);
                    $eventModel->update((int)$owned->event_id, ['volunteers_needed' => $currentNeeded]);
                } elseif ($isChangingFromAccepted) {
                    $currentNeeded = $currentNeeded + 1;
                    $eventModel->update((int)$owned->event_id, ['volunteers_needed' => $currentNeeded]);
                }
            }

            if ($db->inTransaction()) {
                $db->commit();
            }

            echo json_encode([
                'success' => true,
                'message' => 'Volunteer status updated successfully',
                'status' => $newStatus
            ]);
        } catch (Exception $e) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log('Error updating volunteer status: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to update volunteer status']);
        }
    }

    /**
     * Format date for display
     */
    private function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        $now = new DateTime();
        $diff = $now->diff($date);

        if ($diff->days == 0) {
            if ($diff->h == 0) {
                return $diff->i . ' minutes ago';
            }
            return $diff->h . ' hours ago';
        } elseif ($diff->days == 1) {
            return 'Yesterday';
        } elseif ($diff->days < 7) {
            return $diff->days . ' days ago';
        } else {
            return $date->format('M j, Y');
        }
    }

    /**
     * Format timestamp as relative time
     */
    private function formatRelativeTime($dateTime)
    {
        $timestamp = strtotime($dateTime ?: 'now');
        $seconds = time() - $timestamp;

        if ($seconds < 60) {
            return 'Just now';
        }

        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        }

        $days = floor($hours / 24);
        if ($days < 7) {
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }

        return date('M d, Y', $timestamp);
    }

    /**
     * Get boost pricing tiers
     */
    public function getBoostPricing()
    {
        header('Content-Type: application/json');

        try {
            $query = "SELECT * FROM boost_pricing WHERE is_active = 1 ORDER BY duration_days ASC";
            $result = $this->query($query);

            echo json_encode([
                'success' => true,
                'pricing' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error getting boost pricing: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load pricing']);
        }
    }

    /**
     * Get publisher events for boosting
     */
    public function getEventsForBoosting()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get publisher's upcoming events only (not ongoing, completed, or cancelled)
            // Exclude events that currently have active boosts
            $query = "
                SELECT 
                    e.id,
                    e.title,
                    e.event_date,
                    e.event_time,
                    e.status,
                    e.is_boosted,
                    e.boost_expires_at,
                    eb.id as active_boost_id,
                    eb.boost_end_date as active_boost_end_date
                FROM events e
                LEFT JOIN event_boosts eb ON e.id = eb.event_id 
                    AND eb.boost_status = 'active' 
                    AND eb.boost_end_date > NOW()
                    AND eb.payment_status = 'completed'
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                AND e.status = 'upcoming'
                AND e.is_deleted = 0
                AND e.event_date >= CURDATE()
                AND eb.id IS NULL
                ORDER BY e.event_date ASC
            ";

            $events = $this->query($query, ['publisher_id' => $currentUser['id']]);
            if (!is_array($events)) {
                $events = [];
            }

            // Debug logging
            error_log("Publisher ID: " . $currentUser['id']);
            error_log("Events available for boosting: " . count($events));

            echo json_encode([
                'success' => true,
                'events' => $events,
                'publisher_id' => $currentUser['id'],
                'count' => count($events)
            ]);
        } catch (Throwable $e) {
            error_log("Error getting events for boosting: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load events', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get active boosts
     */
    public function getActiveBoosts()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $query = "
                SELECT 
                    eb.*,
                    e.title as event_title,
                    e.event_date,
                    e.cover_image
                FROM event_boosts eb
                JOIN events e ON eb.event_id = e.id
                WHERE eb.publisher_id = :publisher_id
                AND eb.boost_status = 'active'
                AND eb.boost_end_date > NOW()
                AND eb.payment_status = 'completed'
                ORDER BY eb.boost_end_date ASC
            ";

            $boosts = $this->query($query, ['publisher_id' => $currentUser['id']]);

            // Calculate remaining time for each boost
            foreach ($boosts as &$boost) {
                $endDate = new DateTime($boost->boost_end_date);
                $now = new DateTime();
                $diff = $now->diff($endDate);

                if ($diff->days > 0) {
                    $boost->time_remaining = $diff->days . ' days';
                } elseif ($diff->h > 0) {
                    $boost->time_remaining = $diff->h . ' hours';
                } else {
                    $boost->time_remaining = $diff->i . ' minutes';
                }
            }

            echo json_encode([
                'success' => true,
                'boosts' => $boosts
            ]);
        } catch (Exception $e) {
            error_log("Error getting active boosts: " . $e->getMessage());
            echo json_encode([
                'success' => true,
                'boosts' => []
            ]);
        }
    }

    /**
     * Create a new boost request
     */
    public function createBoost()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $eventId = $data['event_id'] ?? null;
            $durationDays = $data['duration_days'] ?? null;
            $amount = $data['amount'] ?? null;
            $paymentMethod = $data['payment_method'] ?? 'card';

            if (!$eventId || !$durationDays || !$amount) {
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                return;
            }

            // Verify event belongs to publisher
            $eventQuery = "SELECT * FROM events WHERE id = :event_id AND created_by = :publisher_id AND created_by_type = 'publisher' AND is_deleted = 0";
            $event = $this->query($eventQuery, [
                'event_id' => $eventId,
                'publisher_id' => $currentUser['id']
            ]);

            if (empty($event)) {
                echo json_encode(['success' => false, 'error' => 'Event not found or unauthorized']);
                return;
            }

            // Check if event already has an active boost
            $activeBoostQuery = "
                SELECT id, boost_end_date 
                FROM event_boosts 
                WHERE event_id = :event_id 
                AND boost_status = 'active' 
                AND boost_end_date > NOW()
                AND payment_status = 'completed'
            ";

            $activeBoost = $this->query($activeBoostQuery, ['event_id' => $eventId]);

            if (!empty($activeBoost)) {
                $boostEndDate = new DateTime($activeBoost[0]->boost_end_date);
                $formattedDate = $boostEndDate->format('F j, Y g:i A');
                echo json_encode([
                    'success' => false,
                    'error' => 'This event is already boosted',
                    'message' => "This event already has an active boost until {$formattedDate}. You can boost it again after the current boost expires."
                ]);
                return;
            }

            // Check if event has already passed
            $eventDate = new DateTime($event[0]->event_date);
            $now = new DateTime();
            if ($eventDate < $now) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Cannot boost past events',
                    'message' => 'This event has already passed and cannot be boosted.'
                ]);
                return;
            }

            // Calculate boost dates
            $startDate = new DateTime();
            $endDate = clone $startDate;
            $endDate->modify("+{$durationDays} days");

            // Generate transaction ID
            $transactionId = 'BOOST-' . time() . '-' . rand(1000, 9999);

            // Insert boost record
            $insertQuery = "
                INSERT INTO event_boosts 
                (event_id, publisher_id, boost_start_date, boost_end_date, duration_days, 
                 amount_paid, payment_method, transaction_id, payment_status, boost_status, priority_level)
                VALUES 
                (:event_id, :publisher_id, :start_date, :end_date, :duration_days,
                 :amount, :payment_method, :transaction_id, 'completed', 'active', 1)
            ";

            $this->query($insertQuery, [
                'event_id' => $eventId,
                'publisher_id' => $currentUser['id'],
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s'),
                'duration_days' => $durationDays,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId
            ]);

            // Update event boost status
            $updateEventQuery = "
                UPDATE events 
                SET is_boosted = 1, 
                    boost_expires_at = :expires_at,
                    boost_priority = 1
                WHERE id = :event_id
            ";

            $this->query($updateEventQuery, [
                'event_id' => $eventId,
                'expires_at' => $endDate->format('Y-m-d H:i:s')
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Event boosted successfully!',
                'transaction_id' => $transactionId,
                'boost_end_date' => $endDate->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("Error creating boost: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to create boost']);
        }
    }

    /**
     * Cancel an active boost
     */
    public function cancelBoost()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $boostId = $data['boost_id'] ?? null;

            if (!$boostId) {
                echo json_encode(['success' => false, 'error' => 'Boost ID required']);
                return;
            }

            // Update boost status
            $query = "
                UPDATE event_boosts 
                SET boost_status = 'cancelled'
                WHERE id = :boost_id 
                AND publisher_id = :publisher_id
            ";

            $this->query($query, [
                'boost_id' => $boostId,
                'publisher_id' => $currentUser['id']
            ]);

            // Update event boost status if no other active boosts
            $checkQuery = "
                SELECT COUNT(*) as count 
                FROM event_boosts 
                WHERE event_id = (SELECT event_id FROM event_boosts WHERE id = :boost_id)
                AND boost_status = 'active'
                AND id != :boost_id
            ";

            $result = $this->query($checkQuery, ['boost_id' => $boostId]);

            if ($result[0]['count'] == 0) {
                $updateEventQuery = "
                    UPDATE events 
                    SET is_boosted = 0, 
                        boost_expires_at = NULL,
                        boost_priority = 0
                    WHERE id = (SELECT event_id FROM event_boosts WHERE id = :boost_id)
                ";

                $this->query($updateEventQuery, ['boost_id' => $boostId]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Boost cancelled successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error cancelling boost: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to cancel boost']);
        }
    }

    /**
     * API endpoint to get publisher monthly ticket-sales evolution.
     */
    public function getMonthlyEvolution()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            echo json_encode(['success' => false, 'error' => 'Invalid month format']);
            return;
        }

        try {
            $publisherId = (int)($currentUser['id'] ?? 0);
            $reportData = $this->getPublisherMonthlyReportData($publisherId, $month);

            echo json_encode([
                'success' => true,
                'month' => $month,
                'data' => $reportData
            ]);
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::getMonthlyEvolution: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load monthly evolution data']);
        }
    }

    /**
     * Download publisher monthly evolution report as PDF.
     */
    public function downloadMonthlyReport()
    {
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid month format']);
            exit;
        }

        try {
            $publisherId = (int)($currentUser['id'] ?? 0);
            $publisherName = (string)($currentUser['name'] ?? 'Publisher');

            $reportData = $this->getPublisherMonthlyReportData($publisherId, $month);
            $pdf = $this->generatePublisherMonthlyReportPDF($publisherName, $month, $reportData);

            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="publisher-monthly-report-' . $month . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::downloadMonthlyReport: ' . $e->getMessage());
            header('Content-Type: application/json');
            $response = ['success' => false, 'error' => 'Failed to generate monthly report'];
            if (defined('DEBUG') && DEBUG) {
                $response['details'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
        }
    }

    /**
     * API endpoint to get completed-event profitability report for publisher.
     */
    public function getEventProfitReport()
    {
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $defaultFrom = date('Y-m-d', strtotime('-12 months'));
        $defaultTo = date('Y-m-d');

        $fromDate = $_GET['from_date'] ?? $defaultFrom;
        $toDate = $_GET['to_date'] ?? $defaultTo;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$toDate)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            return;
        }

        if (strtotime($fromDate) === false || strtotime($toDate) === false || strtotime($fromDate) > strtotime($toDate)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date range']);
            return;
        }

        try {
            $publisherId = (int)($currentUser['id'] ?? 0);
            $reportData = $this->getCompletedEventProfitReportData($publisherId, $fromDate, $toDate);

            echo json_encode([
                'success' => true,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'data' => $reportData
            ]);
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::getEventProfitReport: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load completed event profit report']);
        }
    }

    /**
     * Download completed-event profitability report as PDF.
     */
    public function downloadEventProfitReport()
    {
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $defaultFrom = date('Y-m-d', strtotime('-12 months'));
        $defaultTo = date('Y-m-d');

        $fromDate = $_GET['from_date'] ?? $defaultFrom;
        $toDate = $_GET['to_date'] ?? $defaultTo;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            exit;
        }

        if (strtotime($fromDate) === false || strtotime($toDate) === false || strtotime($fromDate) > strtotime($toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date range']);
            exit;
        }

        try {
            $publisherId = (int)($currentUser['id'] ?? 0);
            $publisherName = (string)($currentUser['name'] ?? 'Publisher');

            $reportData = $this->getCompletedEventProfitReportData($publisherId, $fromDate, $toDate);
            $pdf = $this->generateCompletedEventProfitReportPDF($publisherName, $fromDate, $toDate, $reportData);

            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="publisher-event-profit-report-' . $fromDate . '-to-' . $toDate . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::downloadEventProfitReport: ' . $e->getMessage());
            header('Content-Type: application/json');
            $response = ['success' => false, 'error' => 'Failed to generate completed event profit report'];
            if (defined('DEBUG') && DEBUG) {
                $response['details'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Download single completed-event profit report as PDF.
     */
    public function downloadEventProfitByEvent()
    {
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];

        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles, true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
        if ($eventId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid event id']);
            exit;
        }

        $defaultFrom = date('Y-m-d', strtotime('-12 months'));
        $defaultTo = date('Y-m-d');

        $fromDate = $_GET['from_date'] ?? $defaultFrom;
        $toDate = $_GET['to_date'] ?? $defaultTo;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            exit;
        }

        if (strtotime($fromDate) === false || strtotime($toDate) === false || strtotime($fromDate) > strtotime($toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date range']);
            exit;
        }

        try {
            $publisherId = (int)($currentUser['id'] ?? 0);
            $publisherName = (string)($currentUser['name'] ?? 'Publisher');
            $reportData = $this->getCompletedEventProfitReportData($publisherId, $fromDate, $toDate);
            $events = is_array($reportData['events'] ?? null) ? $reportData['events'] : [];

            $selectedEvent = null;
            foreach ($events as $event) {
                if ((int)($event['event_id'] ?? 0) === $eventId) {
                    $selectedEvent = $event;
                    break;
                }
            }

            if (!$selectedEvent) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Selected event was not found in this report range']);
                exit;
            }

            $pdf = $this->generateSingleCompletedEventProfitPDF($publisherName, $fromDate, $toDate, $selectedEvent);

            if (ob_get_length()) {
                ob_clean();
            }

            $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)($selectedEvent['event_name'] ?? 'event'));
            $safeName = trim((string)$safeName, '-');
            if ($safeName === '') {
                $safeName = 'event';
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $safeName . '-profit-report.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Throwable $e) {
            error_log('Error in PublisherDashboard::downloadEventProfitByEvent: ' . $e->getMessage());
            header('Content-Type: application/json');
            $response = ['success' => false, 'error' => 'Failed to generate event profit report'];
            if (defined('DEBUG') && DEBUG) {
                $response['details'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Build monthly ticket-sales report data for publisher events.
     */
    private function getPublisherMonthlyReportData($publisherId, $month)
    {
        $paidRows = [];
        $freeRows = [];

        try {
            $paidRows = $this->query(
                "SELECT
                    e.id AS event_id,
                    e.title AS event_name,
                    COALESCE(NULLIF(TRIM(pr.ticket_tier_name), ''), 'General Admission') AS ticket_type,
                    SUM(COALESCE(pr.ticket_quantity, 1)) AS ticket_amount,
                    SUM(
                        CASE
                            WHEN pr.payment_status IN ('paid', 'partially_refunded', 'refunded', 'completed')
                                THEN GREATEST(COALESCE(pr.total_amount, 0) - COALESCE(pr.refund_amount, 0), 0)
                            ELSE 0
                        END
                    ) AS sales
                FROM paid_event_registrations pr
                INNER JOIN events e ON e.id = pr.event_id
                WHERE e.created_by_type = 'publisher'
                  AND e.created_by = :publisher_id
                  AND e.is_deleted = 0
                  AND pr.registration_status IN ('confirmed', 'checked_in')
                  AND DATE_FORMAT(COALESCE(pr.paid_at, pr.created_at), '%Y-%m') = :month
                                GROUP BY
                                        e.id,
                                        e.title,
                                        COALESCE(NULLIF(TRIM(pr.ticket_tier_name), ''), 'General Admission')
                ORDER BY e.title ASC, ticket_type ASC",
                [
                    'publisher_id' => $publisherId,
                    'month' => $month,
                ]
            ) ?: [];
        } catch (Throwable $e) {
            error_log('Publisher monthly report paid-query warning: ' . $e->getMessage());
            $paidRows = [];
        }

        try {
            $freeRows = $this->query(
                "SELECT
                    e.id AS event_id,
                    e.title AS event_name,
                    'Free Registration' AS ticket_type,
                    COUNT(*) AS ticket_amount,
                    0.00 AS sales
                FROM free_event_registrations fr
                INNER JOIN events e ON e.id = fr.event_id
                WHERE e.created_by_type = 'publisher'
                  AND e.created_by = :publisher_id
                  AND e.is_deleted = 0
                  AND fr.status IN ('registered', 'checked_in')
                  AND DATE_FORMAT(COALESCE(fr.registered_at, fr.created_at), '%Y-%m') = :month
                GROUP BY e.id, e.title
                ORDER BY e.title ASC",
                [
                    'publisher_id' => $publisherId,
                    'month' => $month,
                ]
            ) ?: [];
        } catch (Throwable $e) {
            error_log('Publisher monthly report free-query warning: ' . $e->getMessage());
            $freeRows = [];
        }

        $detailRows = [];
        foreach ($paidRows as $row) {
            $detailRows[] = [
                'event_id' => (int)($row->event_id ?? 0),
                'event_name' => (string)($row->event_name ?? 'Untitled Event'),
                'ticket_type' => (string)($row->ticket_type ?? 'General Admission'),
                'ticket_amount' => (int)($row->ticket_amount ?? 0),
                'sales' => round((float)($row->sales ?? 0), 2)
            ];
        }

        foreach ($freeRows as $row) {
            $detailRows[] = [
                'event_id' => (int)($row->event_id ?? 0),
                'event_name' => (string)($row->event_name ?? 'Untitled Event'),
                'ticket_type' => (string)($row->ticket_type ?? 'Free Registration'),
                'ticket_amount' => (int)($row->ticket_amount ?? 0),
                'sales' => 0.00
            ];
        }

        usort($detailRows, function ($a, $b) {
            $eventSort = strcmp((string)($a['event_name'] ?? ''), (string)($b['event_name'] ?? ''));
            if ($eventSort !== 0) {
                return $eventSort;
            }
            return strcmp((string)($a['ticket_type'] ?? ''), (string)($b['ticket_type'] ?? ''));
        });

        $eventTotalsMap = [];
        $grandTotalSales = 0.0;
        $totalTicketsSold = 0;

        foreach ($detailRows as $row) {
            $eventName = (string)$row['event_name'];
            $sales = (float)$row['sales'];
            $tickets = (int)$row['ticket_amount'];

            if (!isset($eventTotalsMap[$eventName])) {
                $eventTotalsMap[$eventName] = 0.0;
            }

            $eventTotalsMap[$eventName] += $sales;
            $grandTotalSales += $sales;
            $totalTicketsSold += $tickets;
        }

        $eventTotals = [];
        foreach ($eventTotalsMap as $eventName => $totalSales) {
            $eventTotals[] = [
                'event_name' => $eventName,
                'total_sales' => round((float)$totalSales, 2)
            ];
        }

        usort($eventTotals, function ($a, $b) {
            return strcmp((string)($a['event_name'] ?? ''), (string)($b['event_name'] ?? ''));
        });

        return [
            'details' => $detailRows,
            'event_totals' => $eventTotals,
            'summary' => [
                'events_count' => count($eventTotals),
                'tickets_count' => $totalTicketsSold,
                'grand_total_sales' => round($grandTotalSales, 2)
            ]
        ];
    }

    //Build completed-event profitability data for publisher events.
    private function getCompletedEventProfitReportData($publisherId, $fromDate, $toDate)
    {
        $rows = $this->query(
            "SELECT
                e.id AS event_id,
                e.title AS event_name,
                e.event_date,
                COALESCE(paid_summary.paid_tickets, 0) AS paid_tickets,
                COALESCE(free_summary.free_tickets, 0) AS free_tickets,
                COALESCE(payment_summary.gross_sales, 0) AS payment_gross_sales,
                COALESCE(payment_summary.total_commission, 0) AS total_commission,
                COALESCE(payment_summary.total_profit, 0) AS total_profit,
                COALESCE(paid_summary.registration_net_sales, 0) AS registration_net_sales
            FROM events e
            LEFT JOIN (
                SELECT
                    pr.event_id,
                    SUM(COALESCE(pr.ticket_quantity, 1)) AS paid_tickets,
                    SUM(
                        CASE
                            WHEN pr.payment_status IN ('paid', 'partially_refunded', 'refunded', 'completed')
                                THEN GREATEST(COALESCE(pr.total_amount, 0) - COALESCE(pr.refund_amount, 0), 0)
                            ELSE 0
                        END
                    ) AS registration_net_sales
                FROM paid_event_registrations pr
                WHERE pr.registration_status IN ('confirmed', 'checked_in')
                GROUP BY pr.event_id
            ) AS paid_summary ON paid_summary.event_id = e.id
            LEFT JOIN (
                SELECT
                    fr.event_id,
                    COUNT(*) AS free_tickets
                FROM free_event_registrations fr
                WHERE fr.status IN ('registered', 'checked_in')
                GROUP BY fr.event_id
            ) AS free_summary ON free_summary.event_id = e.id
            LEFT JOIN (
                SELECT
                    p.event_id,
                    SUM(COALESCE(p.amount, 0)) AS gross_sales,
                    SUM(COALESCE(p.commission_amount, 0)) AS total_commission,
                    SUM(COALESCE(p.organizer_amount, 0)) AS total_profit
                FROM payments p
                WHERE p.payment_type = 'ticket'
                                    AND p.publisher_id = :payment_publisher_id
                  AND LOWER(COALESCE(p.status, '')) IN ('completed', 'paid', 'success')
                GROUP BY p.event_id
            ) AS payment_summary ON payment_summary.event_id = e.id
            WHERE e.created_by_type = 'publisher'
                            AND e.created_by = :event_publisher_id
              AND e.is_deleted = 0
              AND (e.status = 'completed' OR e.event_date < CURDATE())
              AND e.event_date BETWEEN :from_date AND :to_date
            ORDER BY e.event_date DESC, e.id DESC",
            [
                'payment_publisher_id' => $publisherId,
                'event_publisher_id' => $publisherId,
                'from_date' => $fromDate,
                'to_date' => $toDate
            ]
        ) ?: [];

        $events = [];
        $summary = [
            'events_count' => 0,
            'tickets_count' => 0,
            'gross_sales' => 0.0,
            'commission_total' => 0.0,
            'profit_total' => 0.0,
            'avg_profit_per_event' => 0.0
        ];

        foreach ($rows as $row) {
            $paidTickets = (int)($row->paid_tickets ?? 0);
            $freeTickets = (int)($row->free_tickets ?? 0);
            $totalTickets = $paidTickets + $freeTickets;

            $grossSales = (float)($row->payment_gross_sales ?? 0);
            $registrationNetSales = (float)($row->registration_net_sales ?? 0);
            $commission = (float)($row->total_commission ?? 0);
            $profit = (float)($row->total_profit ?? 0);

            if ($grossSales <= 0 && $registrationNetSales > 0) {
                $grossSales = $registrationNetSales;
                $commission = round($grossSales * 0.05, 2);
            }

            if ($profit <= 0 && $grossSales > 0 && $commission >= 0) {
                $profit = max($grossSales - $commission, 0);
            }

            $profitMargin = $grossSales > 0 ? ($profit / $grossSales) * 100 : 0;

            $events[] = [
                'event_id' => (int)($row->event_id ?? 0),
                'event_name' => (string)($row->event_name ?? 'Untitled Event'),
                'event_date' => (string)($row->event_date ?? ''),
                'paid_tickets' => $paidTickets,
                'free_tickets' => $freeTickets,
                'total_tickets_sold' => $totalTickets,
                'gross_sales' => round($grossSales, 2),
                'commission_total' => round($commission, 2),
                'profit_total' => round($profit, 2),
                'profit_margin' => round($profitMargin, 2)
            ];

            $summary['events_count']++;
            $summary['tickets_count'] += $totalTickets;
            $summary['gross_sales'] += $grossSales;
            $summary['commission_total'] += $commission;
            $summary['profit_total'] += $profit;
        }

        if ($summary['events_count'] > 0) {
            $summary['avg_profit_per_event'] = $summary['profit_total'] / $summary['events_count'];
        }

        $topProfitEvent = null;
        $topTicketEvent = null;

        if (!empty($events)) {
            $profitSorted = $events;
            usort($profitSorted, function ($a, $b) {
                return ((float)$b['profit_total']) <=> ((float)$a['profit_total']);
            });
            $topProfitEvent = $profitSorted[0];

            $ticketSorted = $events;
            usort($ticketSorted, function ($a, $b) {
                return ((int)$b['total_tickets_sold']) <=> ((int)$a['total_tickets_sold']);
            });
            $topTicketEvent = $ticketSorted[0];
        }

        return [
            'events' => $events,
            'summary' => [
                'events_count' => (int)$summary['events_count'],
                'tickets_count' => (int)$summary['tickets_count'],
                'gross_sales' => round((float)$summary['gross_sales'], 2),
                'commission_total' => round((float)$summary['commission_total'], 2),
                'profit_total' => round((float)$summary['profit_total'], 2),
                'avg_profit_per_event' => round((float)$summary['avg_profit_per_event'], 2)
            ],
            'insights' => [
                'top_profit_event' => $topProfitEvent,
                'top_ticket_event' => $topTicketEvent
            ]
        ];
    }

    /**
     * Generate a compact PDF report for publisher monthly ticket sales.
     */
    private function generatePublisherMonthlyReportPDF($publisherName, $month, $reportData)
    {
        $monthName = date('F Y', strtotime($month . '-01'));
        $details = is_array($reportData['details'] ?? null) ? $reportData['details'] : [];
        $eventTotals = is_array($reportData['event_totals'] ?? null) ? $reportData['event_totals'] : [];
        $summary = is_array($reportData['summary'] ?? null) ? $reportData['summary'] : [];

        $groupedDetails = [];
        foreach ($details as $row) {
            $eventName = (string)($row['event_name'] ?? 'Untitled Event');
            if (!isset($groupedDetails[$eventName])) {
                $groupedDetails[$eventName] = [];
            }
            $groupedDetails[$eventName][] = $row;
        }

        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 42;
        $contentRight = $pageWidth - $marginX;

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, [248, 250, 252]);
        $content .= $this->pdfLinearGradientRect(0, 694, $pageWidth, 98, [30, 58, 138], [249, 115, 22], 50);
        $content .= $this->pdfText($marginX, 754, 'UniPulse Publisher Report', 'F2', 20, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 734, 'Monthly Ticket Sales Evolution', 'F2', 12, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 718, $monthName . '  |  Generated ' . date('M d, Y'), 'F1', 9, [219, 234, 254]);

        $content .= $this->pdfRect($contentRight - 184, 718, 172, 44, null, [255, 237, 213], 0.8);
        $content .= $this->pdfText($contentRight - 174, 744, 'Publisher', 'F1', 8.5, [255, 237, 213]);
        $content .= $this->pdfText($contentRight - 174, 727, $this->truncatePDFText($publisherName, 24), 'F2', 11.5, [255, 255, 255]);

        $eventsCount = (int)($summary['events_count'] ?? 0);
        $ticketsCount = (int)($summary['tickets_count'] ?? 0);
        $grandSales = (float)($summary['grand_total_sales'] ?? 0);

        $content .= $this->pdfRect($marginX, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 10, 673, 'Events', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 10, 659, (string)$eventsCount, 'F2', 13, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 178, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 188, 673, 'Tickets', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 188, 659, number_format($ticketsCount), 'F2', 13, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 356, 652, 214, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 366, 673, 'Total Sales', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 366, 659, 'LKR ' . number_format($grandSales, 2), 'F2', 13, [6, 95, 70]);

        $tableX = $marginX;
        $tableW = $contentRight - $tableX;
        $rowH = 18;
        $rowY = 620;

        $content .= $this->pdfText($tableX, $rowY + 16, 'Event-wise Ticket Type Sales', 'F2', 12, [30, 58, 138]);
        $rowY -= 10;

        if (empty($groupedDetails)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No ticket sales activity recorded for this month.', 'F1', 8.4, [100, 116, 139]);
            $rowY -= $rowH;
        } else {
            foreach ($groupedDetails as $eventName => $eventRows) {
                $eventSubtotal = 0.0;

                $content .= $this->pdfRect($tableX, $rowY, $tableW, 22, [239, 246, 255], [191, 219, 254], 0.8);
                $content .= $this->pdfText($tableX + 10, $rowY + 7, $this->truncatePDFText($eventName, 48), 'F2', 10.2, [30, 58, 138]);
                $rowY -= 22;

                $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [30, 58, 138], [249, 115, 22], 24);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, 'TICKET TYPE', 'F2', 8.2, [255, 255, 255]);
                $content .= $this->pdfText($tableX + 250, $rowY + 6, 'TICKET AMOUNT', 'F2', 8.2, [255, 255, 255]);
                $content .= $this->pdfText($tableX + 474, $rowY + 6, 'SALES (LKR)', 'F2', 8.2, [255, 255, 255]);
                $rowY -= $rowH;

                foreach ($eventRows as $index => $row) {
                    $ticketType = (string)($row['ticket_type'] ?? 'General Admission');
                    $ticketAmount = (int)($row['ticket_amount'] ?? 0);
                    $sales = (float)($row['sales'] ?? 0);
                    $eventSubtotal += $sales;

                    $bg = ($index % 2 === 0) ? [255, 255, 255] : [248, 250, 252];
                    $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
                    $content .= $this->pdfText($tableX + 10, $rowY + 6, $this->truncatePDFText($ticketType, 28), 'F1', 8.0, [71, 85, 105]);
                    $content .= $this->pdfText($tableX + 250, $rowY + 6, number_format($ticketAmount), 'F2', 8.0, [30, 41, 59]);
                    $content .= $this->pdfText($tableX + 474, $rowY + 6, number_format($sales, 2), 'F2', 8.0, [6, 95, 70]);
                    $rowY -= $rowH;
                }

                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 237, 213], [253, 186, 116], 0.8);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, 'Total Sales', 'F2', 8.8, [154, 52, 18]);
                $content .= $this->pdfText($tableX + 474, $rowY + 6, number_format($eventSubtotal, 2), 'F2', 8.8, [154, 52, 18]);
                $rowY -= ($rowH + 14);
            }
        }

        $rowY -= 16;
        $content .= $this->pdfText($tableX, $rowY + 16, 'Total Sales by Event', 'F2', 12, [30, 58, 138]);
        $rowY -= 8;

        $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [30, 58, 138], [249, 115, 22], 24);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'EVENT NAME', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 474, $rowY + 6, 'TOTAL SALES', 'F2', 8.2, [255, 255, 255]);
        $rowY -= $rowH;

        if (empty($eventTotals)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No event totals available.', 'F1', 8.4, [100, 116, 139]);
            $rowY -= $rowH;
        } else {
            $maxSummaryRows = min(count($eventTotals), 10);
            for ($i = 0; $i < $maxSummaryRows; $i++) {
                $row = $eventTotals[$i];
                $bg = ($i % 2 === 0) ? [255, 255, 255] : [248, 250, 252];
                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, $this->truncatePDFText((string)($row['event_name'] ?? '-'), 36), 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 474, $rowY + 6, number_format((float)($row['total_sales'] ?? 0), 2), 'F2', 8.0, [6, 95, 70]);
                $rowY -= $rowH;
            }
        }

        $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 237, 213], [253, 186, 116], 0.8);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'Grand Total', 'F2', 8.8, [154, 52, 18]);
        $content .= $this->pdfText($tableX + 474, $rowY + 6, number_format($grandSales, 2), 'F2', 8.8, [154, 52, 18]);

        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 26, [30, 58, 138], [249, 115, 22], 30);
        $content .= $this->pdfText($marginX, 8, 'UniPulse  |  Publisher Monthly Evolution  |  ' . $monthName, 'F1', 8.2, [219, 234, 254]);

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

    /**
     * Generate PDF report for completed-event profit data.
     */
    private function generateCompletedEventProfitReportPDF($publisherName, $fromDate, $toDate, $reportData)
    {
        $events = is_array($reportData['events'] ?? null) ? $reportData['events'] : [];
        $summary = is_array($reportData['summary'] ?? null) ? $reportData['summary'] : [];

        $periodLabel = date('M d, Y', strtotime($fromDate)) . ' to ' . date('M d, Y', strtotime($toDate));

        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 42;
        $contentRight = $pageWidth - $marginX;

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, [248, 250, 252]);
        $content .= $this->pdfLinearGradientRect(0, 694, $pageWidth, 98, [17, 94, 89], [249, 115, 22], 50);
        $content .= $this->pdfText($marginX, 754, 'UniPulse Publisher Report', 'F2', 20, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 734, 'Completed Event Profit Report', 'F2', 12, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 718, $periodLabel . '  |  Generated ' . date('M d, Y'), 'F1', 9, [219, 234, 254]);

        $content .= $this->pdfRect($contentRight - 184, 718, 172, 44, null, [254, 215, 170], 0.8);
        $content .= $this->pdfText($contentRight - 174, 744, 'Publisher', 'F1', 8.5, [254, 215, 170]);
        $content .= $this->pdfText($contentRight - 174, 727, $this->truncatePDFText($publisherName, 24), 'F2', 11.5, [255, 255, 255]);

        $eventsCount = (int)($summary['events_count'] ?? 0);
        $ticketsCount = (int)($summary['tickets_count'] ?? 0);
        $profitTotal = (float)($summary['profit_total'] ?? 0);

        $content .= $this->pdfRect($marginX, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 10, 673, 'Completed Events', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 10, 659, (string)$eventsCount, 'F2', 13, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 178, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 188, 673, 'Tickets Sold', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 188, 659, number_format($ticketsCount), 'F2', 13, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 356, 652, 214, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 366, 673, 'Total Profit', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 366, 659, 'LKR ' . number_format($profitTotal, 2), 'F2', 13, [6, 95, 70]);

        $tableX = $marginX;
        $tableW = $contentRight - $tableX;
        $rowH = 18;
        $rowY = 620;

        $content .= $this->pdfText($tableX, $rowY + 16, 'Completed Event Profitability', 'F2', 12, [17, 94, 89]);
        $rowY -= 8;

        $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [17, 94, 89], [249, 115, 22], 30);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'EVENT', 'F2', 7.8, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 252, $rowY + 6, 'TICKETS', 'F2', 7.8, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 316, $rowY + 6, 'GROSS', 'F2', 7.8, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 392, $rowY + 6, 'COMMISSION', 'F2', 7.8, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 468, $rowY + 6, 'PROFIT', 'F2', 7.8, [255, 255, 255]);
        $rowY -= $rowH;

        if (empty($events)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No completed events found for the selected period.', 'F1', 8.4, [100, 116, 139]);
            $rowY -= $rowH;
        } else {
            $maxRows = min(count($events), 16);
            for ($i = 0; $i < $maxRows; $i++) {
                $event = $events[$i];
                $bg = ($i % 2 === 0) ? [255, 255, 255] : [248, 250, 252];

                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
                $content .= $this->pdfText(
                    $tableX + 10,
                    $rowY + 6,
                    $this->truncatePDFText((string)($event['event_name'] ?? '-'), 32),
                    'F2',
                    7.8,
                    [30, 41, 59]
                );
                $content .= $this->pdfText($tableX + 252, $rowY + 6, number_format((int)($event['total_tickets_sold'] ?? 0)), 'F2', 7.8, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 316, $rowY + 6, number_format((float)($event['gross_sales'] ?? 0), 2), 'F2', 7.8, [51, 65, 85]);
                $content .= $this->pdfText($tableX + 392, $rowY + 6, number_format((float)($event['commission_total'] ?? 0), 2), 'F2', 7.8, [217, 119, 6]);
                $content .= $this->pdfText($tableX + 468, $rowY + 6, number_format((float)($event['profit_total'] ?? 0), 2), 'F2', 7.8, [6, 95, 70]);
                $rowY -= $rowH;
            }
        }

        $rowY -= 12;
        $content .= $this->pdfText($tableX, $rowY + 16, 'Summary', 'F2', 11, [17, 94, 89]);
        $rowY -= 8;

        $summaryRows = [
            ['label' => 'Gross Sales', 'value' => number_format((float)($summary['gross_sales'] ?? 0), 2)],
            ['label' => 'Platform Commission', 'value' => number_format((float)($summary['commission_total'] ?? 0), 2)],
            ['label' => 'Total Profit', 'value' => number_format((float)($summary['profit_total'] ?? 0), 2)],
            ['label' => 'Average Profit / Event', 'value' => number_format((float)($summary['avg_profit_per_event'] ?? 0), 2)]
        ];

        foreach ($summaryRows as $index => $row) {
            $bg = ($index % 2 === 0) ? [255, 255, 255] : [248, 250, 252];
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, $row['label'], 'F2', 8.4, [30, 41, 59]);
            $content .= $this->pdfText($tableX + 446, $rowY + 6, $row['value'], 'F2', 8.4, [30, 41, 59]);
            $rowY -= $rowH;
        }

        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 26, [17, 94, 89], [249, 115, 22], 30);
        $content .= $this->pdfText($marginX, 8, 'UniPulse  |  Completed Event Profit Report  |  ' . $periodLabel, 'F1', 8.2, [219, 234, 254]);

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

    /**
     * Generate PDF report for a single completed event.
     */
    private function generateSingleCompletedEventProfitPDF($publisherName, $fromDate, $toDate, $event)
    {
        $periodLabel = date('M d, Y', strtotime($fromDate)) . ' to ' . date('M d, Y', strtotime($toDate));
        $eventName = (string)($event['event_name'] ?? 'Untitled Event');
        $eventDate = (string)($event['event_date'] ?? '');
        $eventDateLabel = $eventDate !== '' ? date('M d, Y', strtotime($eventDate)) : '-';

        $totalTickets = (int)($event['total_tickets_sold'] ?? 0);
        $grossSales = (float)($event['gross_sales'] ?? 0);
        $commission = (float)($event['commission_total'] ?? 0);
        $profit = (float)($event['profit_total'] ?? 0);
        $margin = (float)($event['profit_margin'] ?? 0);
        $paidTickets = (int)($event['paid_tickets'] ?? 0);
        $freeTickets = (int)($event['free_tickets'] ?? 0);

        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 42;
        $contentRight = $pageWidth - $marginX;

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, [248, 250, 252]);
        $content .= $this->pdfLinearGradientRect(0, 694, $pageWidth, 98, [17, 94, 89], [249, 115, 22], 50);
        $content .= $this->pdfText($marginX, 754, 'UniPulse Event Profit Report', 'F2', 20, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 734, 'Completed Event Financial Summary', 'F2', 12, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 718, $periodLabel . '  |  Generated ' . date('M d, Y'), 'F1', 9, [219, 234, 254]);

        $content .= $this->pdfRect($contentRight - 184, 718, 172, 44, null, [254, 215, 170], 0.8);
        $content .= $this->pdfText($contentRight - 174, 744, 'Publisher', 'F1', 8.5, [254, 215, 170]);
        $content .= $this->pdfText($contentRight - 174, 727, $this->truncatePDFText($publisherName, 24), 'F2', 11.5, [255, 255, 255]);

        $content .= $this->pdfRect($marginX, 622, $contentRight - $marginX, 58, [239, 246, 255], [191, 219, 254], 0.8);
        $content .= $this->pdfText($marginX + 12, 662, 'Event Name', 'F1', 8.4, [71, 85, 105]);
        $content .= $this->pdfText($marginX + 12, 645, $this->truncatePDFText($eventName, 58), 'F2', 13.2, [30, 58, 138]);
        $content .= $this->pdfText($marginX + 410, 662, 'Completed On', 'F1', 8.4, [71, 85, 105]);
        $content .= $this->pdfText($marginX + 410, 645, $eventDateLabel, 'F2', 11.2, [30, 41, 59]);

        $metricY = 572;
        $metricGap = 8;
        $metricWidth = 124;

        $metric1X = $marginX;
        $metric2X = $metric1X + $metricWidth + $metricGap;
        $metric3X = $metric2X + $metricWidth + $metricGap;
        $metric4X = $metric3X + $metricWidth + $metricGap;

        $content .= $this->pdfRect($metric1X, $metricY, $metricWidth, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($metric1X + 10, $metricY + 21, 'Tickets Sold', 'F1', 8.2, [100, 116, 139]);
        $content .= $this->pdfText($metric1X + 10, $metricY + 8, number_format($totalTickets), 'F2', 12.5, [30, 41, 59]);

        $content .= $this->pdfRect($metric2X, $metricY, $metricWidth, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($metric2X + 10, $metricY + 21, 'Gross Sales', 'F1', 8.2, [100, 116, 139]);
        $content .= $this->pdfText($metric2X + 10, $metricY + 8, number_format($grossSales, 2), 'F2', 12.5, [30, 41, 59]);

        $content .= $this->pdfRect($metric3X, $metricY, $metricWidth, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($metric3X + 10, $metricY + 21, 'Commission', 'F1', 8.2, [100, 116, 139]);
        $content .= $this->pdfText($metric3X + 10, $metricY + 8, number_format($commission, 2), 'F2', 12.5, [217, 119, 6]);

        $content .= $this->pdfRect($metric4X, $metricY, $metricWidth, 34, [236, 253, 245], [167, 243, 208], 0.8);
        $content .= $this->pdfText($metric4X + 10, $metricY + 21, 'Total Profit', 'F1', 8.2, [6, 95, 70]);
        $content .= $this->pdfText($metric4X + 10, $metricY + 8, number_format($profit, 2), 'F2', 12.5, [6, 95, 70]);

        $tableX = $marginX;
        $tableW = $contentRight - $tableX;
        $rowH = 24;
        $rowY = 512;

        $content .= $this->pdfText($tableX, $rowY + 18, 'Event Breakdown', 'F2', 12, [17, 94, 89]);
        $rowY -= 8;

        $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [17, 94, 89], [249, 115, 22], 30);
        $content .= $this->pdfText($tableX + 10, $rowY + 8, 'METRIC', 'F2', 8.4, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 446, $rowY + 8, 'VALUE', 'F2', 8.4, [255, 255, 255]);
        $rowY -= $rowH;

        $rows = [
            ['label' => 'Paid Tickets', 'value' => number_format($paidTickets)],
            ['label' => 'Free Tickets', 'value' => number_format($freeTickets)],
            ['label' => 'Total Tickets Sold', 'value' => number_format($totalTickets)],
            ['label' => 'Gross Sales', 'value' => number_format($grossSales, 2)],
            ['label' => 'Platform Commission', 'value' => number_format($commission, 2)],
            ['label' => 'Total Profit', 'value' => number_format($profit, 2)],
            ['label' => 'Profit Margin', 'value' => number_format($margin, 2) . '%']
        ];

        foreach ($rows as $index => $row) {
            $bg = ($index % 2 === 0) ? [255, 255, 255] : [248, 250, 252];
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 8, $row['label'], 'F2', 8.8, [30, 41, 59]);
            $content .= $this->pdfText($tableX + 446, $rowY + 8, $row['value'], 'F2', 8.8, [30, 41, 59]);
            $rowY -= $rowH;
        }

        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 26, [17, 94, 89], [249, 115, 22], 30);
        $content .= $this->pdfText($marginX, 8, 'UniPulse  |  Event Profit Report  |  ' . $this->truncatePDFText($eventName, 52), 'F1', 8.2, [219, 234, 254]);

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
            . '(' . $this->escapePDF($text) . ") Tj\n"
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

    private function escapePDF($text)
    {
        $text = str_replace('\\', '\\\\', (string)$text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);
        return $text;
    }
}
