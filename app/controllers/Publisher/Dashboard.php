<?php

class PublisherDashboard extends Controller
{

    use Database;

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
     * Get header notifications for publisher users.
     * Source: newly visible events published by other publishers.
     */
    public function getNotifications()
    {
        header('Content-Type: application/json');

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

            $publisherId = (int)($currentUser['id'] ?? 0);
            $sessionKey = 'publisher_event_notifications_last_read_at_' . $publisherId;
            $readItemsKey = 'publisher_event_notifications_read_items_' . $publisherId;
            if (empty($_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey] = '1970-01-01 00:00:00';
            }
            if (empty($_SESSION[$readItemsKey]) || !is_array($_SESSION[$readItemsKey])) {
                $_SESSION[$readItemsKey] = [];
            }
            $lastReadAt = $_SESSION[$sessionKey];
            $readItems = $_SESSION[$readItemsKey];

            $notifications = [];
            $unreadCount = 0;

            // 1) Personal publisher notifications for new registrations
            $activities = $activityModel->getRecentActivities($publisherId, 'publisher', 30);
            $registrationActivities = array_filter($activities, function ($activity) {
                return ($activity->activity_type ?? '') === 'event_registration';
            });

            foreach ($registrationActivities as $activity) {
                $notificationTime = $activity->created_at ?? date('Y-m-d H:i:s');
                $eventId = (int)($activity->event_id ?? 0);
                $notificationKey = 'activity|' . ($activity->id ?? 0) . '|' . $notificationTime;

                $isMarkedByTime = strtotime($notificationTime) <= strtotime($lastReadAt);
                $isMarkedIndividually = in_array($notificationKey, $readItems, true);
                $isUnread = !($isMarkedByTime || $isMarkedIndividually);

                if ($isUnread) {
                    $unreadCount++;
                }

                $notifications[] = [
                    'id' => $eventId,
                    'title' => $activity->title ?? 'New Event Registration',
                    'message' => $activity->description ?? 'A user registered for your event.',
                    'time' => $this->formatRelativeTime($notificationTime),
                    'read' => !$isUnread,
                    'created_at' => $notificationTime,
                    'notification_key' => $notificationKey
                ];
            }

            // Match visibility used by Publisher All Events page.
            $events = $eventModel->getAllEvents([
                'status' => 'upcoming',
                'limit' => 100,
                'offset' => 0
            ], $currentUser);

            if (!$events) {
                usort($notifications, function ($a, $b) {
                    return strtotime($b['created_at'] ?? '1970-01-01 00:00:00') <=> strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
                });

                echo json_encode([
                    'success' => true,
                    'notifications' => array_slice($notifications, 0, 10),
                    'unread_count' => $unreadCount
                ]);
                return;
            }

            $otherPublisherEvents = [];

            foreach ($events as $event) {
                $eventOwnerId = isset($event->created_by) ? (int)$event->created_by : 0;
                $eventOwnerType = $event->created_by_type ?? '';

                if ($eventOwnerType === 'publisher' && $eventOwnerId > 0 && $eventOwnerId !== $publisherId) {
                    $otherPublisherEvents[] = $event;
                }
            }

            if (empty($otherPublisherEvents)) {
                usort($notifications, function ($a, $b) {
                    return strtotime($b['created_at'] ?? '1970-01-01 00:00:00') <=> strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
                });

                echo json_encode([
                    'success' => true,
                    'notifications' => array_slice($notifications, 0, 10),
                    'unread_count' => $unreadCount
                ]);
                return;
            }

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
                $isMarkedIndividually = in_array($notificationKey, $readItems, true);
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
                    'notification_key' => $notificationKey
                ];
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
        $createdAt = trim((string)($payload['created_at'] ?? ''));

        if ($eventId <= 0 || $createdAt === '') {
            echo json_encode(['success' => false, 'error' => 'Invalid notification payload']);
            return;
        }

        $publisherId = (int)($currentUser['id'] ?? 0);
        $readItemsKey = 'publisher_event_notifications_read_items_' . $publisherId;
        if (empty($_SESSION[$readItemsKey]) || !is_array($_SESSION[$readItemsKey])) {
            $_SESSION[$readItemsKey] = [];
        }

        $notificationKey = $eventId . '|' . $createdAt;
        if (!empty($payload['notification_key'])) {
            $notificationKey = trim((string)$payload['notification_key']);
        }
        if (!in_array($notificationKey, $_SESSION[$readItemsKey], true)) {
            $_SESSION[$readItemsKey][] = $notificationKey;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
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

        $sessionKey = 'publisher_event_notifications_last_read_at_' . (int)$currentUser['id'];
        $_SESSION[$sessionKey] = date('Y-m-d H:i:s');
        $_SESSION['publisher_event_notifications_read_items_' . (int)$currentUser['id']] = [];

        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
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
                    END as user_email
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
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
                ORDER BY e.event_date DESC
                LIMIT 50
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);

            $formattedEvents = [];
            $currentDate = date('Y-m-d');

            foreach ($events as $event) {
                // Calculate actual status based on event date
                $eventStatus = $event->status;
                if ($event->event_date < $currentDate) {
                    $eventStatus = 'completed';
                } elseif ($event->event_date == $currentDate) {
                    $eventStatus = 'ongoing';
                } elseif ($event->event_date > $currentDate) {
                    $eventStatus = 'upcoming';
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
                    'category' => $event->category,
                    'cover_image' => $event->cover_image ?? $event->image_url ?? '',
                    'organizer_name' => $event->organizer_name ?? $event->organizer ?? '',
                    'ticket_type' => $event->ticket_type ?? 'free-all',
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
                    END as volunteer_name
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
                    'event_title' => $row->event_title,
                    'role' => $row->volunteer_position ?: 'General Volunteer',
                    'status' => $row->status,
                    'applied_at' => $row->created_at
                ];

                if ($row->status === 'accepted') {
                    $shifts[] = [
                        'id' => $row->id,
                        'name' => $name,
                        'event_title' => $row->event_title,
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
                SELECT vr.id
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

            $volunteerReg = new VolunteerRegistration();
            $updated = $volunteerReg->updateStatus((int)$registrationId, $newStatus);

            if (!$updated) {
                echo json_encode(['success' => false, 'error' => 'Failed to update status']);
                return;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Volunteer status updated successfully',
                'status' => $newStatus
            ]);
        } catch (Exception $e) {
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

            // Debug logging
            error_log("Publisher ID: " . $currentUser['id']);
            error_log("Events available for boosting: " . count($events));

            echo json_encode([
                'success' => true,
                'events' => $events,
                'publisher_id' => $currentUser['id'],
                'count' => count($events)
            ]);
        } catch (Exception $e) {
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
            echo json_encode(['success' => false, 'error' => 'Failed to load active boosts']);
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
}
