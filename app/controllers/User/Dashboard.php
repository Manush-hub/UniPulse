<?php

class UserDashboard extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        // Require authentication - allow both public and university users
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/dashboard', $data);
    }

    /**
     * Display monthly evolution page
     */
    public function monthlyEvolution($a = '', $b = '', $c = '')
    {
        // Require authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/monthly-evolution', $data);
    }

    /**
     * API endpoint to get current user data
     */
    public function getUserData()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $currentUser['name'] ?? 'User',
                'email' => $currentUser['email'] ?? '',
                'type' => $currentUser['type'] ?? 'user',
                'university' => $currentUser['university'] ?? ''
            ]
        ]);
    }

    /**
     * API endpoint to get header notifications for users
     * Notification source: personal user activities (registrations, etc.)
     */
    public function getNotifications()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated', 'notifications' => []]);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized', 'notifications' => []]);
            return;
        }

        try {
            $activityModel = new Activity();
            $eventModel = new Event();

            $userId = (int)($currentUser['id'] ?? 0);
            $sessionKey = 'user_event_notifications_last_read_at_' . $userId;
            $readItemsKey = 'user_event_notifications_read_items_' . $userId;

            if (empty($_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey] = '1970-01-01 00:00:00';
            }
            if (empty($_SESSION[$readItemsKey]) || !is_array($_SESSION[$readItemsKey])) {
                $_SESSION[$readItemsKey] = [];
            }

            $lastReadAt = $_SESSION[$sessionKey];
            $readItems = $_SESSION[$readItemsKey];

            $activities = $activityModel->getRecentActivities($userId, $currentUser['type'], 50);

            // Keep only activity types relevant to personal notifications
            $allowedTypes = ['event_registration', 'event_cancellation', 'volunteer_registration'];
            $activities = array_values(array_filter($activities ?: [], function ($activity) use ($allowedTypes) {
                return in_array($activity->activity_type ?? '', $allowedTypes, true);
            }));

            $notifications = [];

            // 1) Personal activity notifications
            foreach ($activities as $activity) {
                $notificationTime = $activity->created_at ?? date('Y-m-d H:i:s');
                $eventId = (int)($activity->event_id ?? 0);
                if ($eventId <= 0) {
                    continue;
                }

                $notificationKey = $eventId . '|' . $notificationTime;

                $activityType = $activity->activity_type ?? 'event_registration';
                $eventTitle = $activity->event_title ?? 'this event';

                $title = 'Event Update';
                $message = 'There is an update related to your events.';

                if ($activityType === 'event_registration') {
                    $activityData = [];
                    if (!empty($activity->activity_data)) {
                        if (is_string($activity->activity_data)) {
                            $decoded = json_decode($activity->activity_data, true);
                            if (is_array($decoded)) {
                                $activityData = $decoded;
                            }
                        } elseif (is_array($activity->activity_data)) {
                            $activityData = $activity->activity_data;
                        }
                    }

                    $notificationCategory = strtolower((string)($activityData['notification_category'] ?? ''));
                    if ($notificationCategory === 'donation_status') {
                        $donationStatus = strtolower((string)($activityData['donation_status'] ?? 'pending'));
                        $amountPaid = (float)($activityData['amount'] ?? 0);
                        $currency = (string)($activityData['currency'] ?? 'LKR');

                        if ($donationStatus === 'accepted' || $donationStatus === 'completed') {
                            $title = 'Donation Approved';
                            $message = 'Your donation of ' . $currency . ' ' . number_format($amountPaid, 2) . ' for "' . $eventTitle . '" was approved.';
                        } else {
                            $title = 'Donation Rejected';
                            $message = 'Your donation for "' . $eventTitle . '" was rejected by the publisher.';
                        }
                    } else {
                        $registrationType = strtolower((string)($activityData['registration_type'] ?? 'free'));
                        $amountPaid = (float)($activityData['amount_paid'] ?? 0);
                        $isPaidRegistration = ($registrationType === 'paid' || $amountPaid > 0);

                        if ($isPaidRegistration) {
                            $title = 'Payment & Registration Confirmed';
                            $message = 'Your payment was successful and you are registered for "' . $eventTitle . '".';
                        } else {
                            $title = 'Registration Confirmed';
                            $message = 'You registered for "' . $eventTitle . '".';
                        }
                    }
                } elseif ($activityType === 'event_cancellation') {
                    $title = 'Registration Cancelled';
                    $message = 'You cancelled registration for "' . $eventTitle . '".';
                } elseif ($activityType === 'volunteer_registration') {
                    $title = 'Volunteer Application Sent';
                    $message = 'You applied as a volunteer for "' . $eventTitle . '".';
                }

                $notifications[] = [
                    'id' => $eventId,
                    'title' => $title,
                    'message' => $message,
                    'time' => $this->formatRelativeTime($notificationTime),
                    'read' => false,
                    'created_at' => $notificationTime,
                    'notification_key' => 'activity|' . $notificationKey,
                    'source' => 'activity'
                ];
            }

            // 2) New publisher event notifications (true publish events only)
            // IMPORTANT: use created_at, not updated_at, so registrations do not trigger this.
            $events = $eventModel->getAllEvents([
                'status' => 'upcoming',
                'limit' => 100,
                'offset' => 0
            ], $currentUser);

            foreach ($events ?: [] as $event) {
                $eventId = (int)($event->id ?? 0);
                if ($eventId <= 0) {
                    continue;
                }

                // Only publisher-created events should be treated as publish notifications
                if (($event->created_by_type ?? '') !== 'publisher') {
                    continue;
                }

                $notificationTime = $event->created_at ?? date('Y-m-d H:i:s');
                $notificationKey = 'publish|' . $eventId . '|' . $notificationTime;

                $notifications[] = [
                    'id' => $eventId,
                    'title' => 'New Event Published',
                    'message' => ($event->title ?? 'A new event') . ' is now available in All Events.',
                    'time' => $this->formatRelativeTime($notificationTime),
                    'read' => false,
                    'created_at' => $notificationTime,
                    'notification_key' => $notificationKey,
                    'source' => 'publish'
                ];
            }

            if (empty($notifications)) {
                echo json_encode([
                    'success' => true,
                    'notifications' => [],
                    'unread_count' => 0
                ]);
                return;
            }

            // Sort newest first, then evaluate read state using time and per-item key
            usort($notifications, function ($a, $b) {
                $aTime = strtotime($a['created_at'] ?? '1970-01-01 00:00:00');
                $bTime = strtotime($b['created_at'] ?? '1970-01-01 00:00:00');
                return $bTime <=> $aTime;
            });

            $unreadCount = 0;
            foreach ($notifications as &$notification) {
                $notificationTime = $notification['created_at'] ?? '1970-01-01 00:00:00';
                $notificationKey = $notification['notification_key'] ?? '';

                $isMarkedByTime = strtotime($notificationTime) <= strtotime($lastReadAt);
                $isMarkedIndividually = in_array($notificationKey, $readItems, true);
                $isUnread = !($isMarkedByTime || $isMarkedIndividually);

                $notification['read'] = !$isUnread;
                if ($isUnread) {
                    $unreadCount++;
                }
            }
            unset($notification);

            echo json_encode([
                'success' => true,
                'notifications' => array_slice($notifications, 0, 10),
                'unread_count' => $unreadCount
            ]);
        } catch (Exception $e) {
            error_log('Error in UserDashboard::getNotifications: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load notifications',
                'notifications' => []
            ]);
        }
    }

    /**
     * API endpoint to mark a single header notification as read
     */
    public function markNotificationRead()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'])) {
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

        $userId = (int)($currentUser['id'] ?? 0);
        $readItemsKey = 'user_event_notifications_read_items_' . $userId;
        if (empty($_SESSION[$readItemsKey]) || !is_array($_SESSION[$readItemsKey])) {
            $_SESSION[$readItemsKey] = [];
        }

        $source = trim((string)($payload['source'] ?? 'activity'));
        if (!in_array($source, ['activity', 'publish'], true)) {
            $source = 'activity';
        }

        $notificationKey = $source . '|' . $eventId . '|' . $createdAt;
        if (!in_array($notificationKey, $_SESSION[$readItemsKey], true)) {
            $_SESSION[$readItemsKey][] = $notificationKey;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * API endpoint to mark all header notifications as read
     */
    public function markAllNotificationsRead()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $userId = (int)($currentUser['id'] ?? 0);
        $_SESSION['user_event_notifications_last_read_at_' . $userId] = date('Y-m-d H:i:s');
        $_SESSION['user_event_notifications_read_items_' . $userId] = [];

        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * API endpoint to get user's registered upcoming events
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Load EventRegistration model
            $eventRegistration = new EventRegistration();

            if (!$eventRegistration) {
                echo json_encode(['success' => false, 'error' => 'Failed to load EventRegistration model']);
                return;
            }

            // Get user's registered events (upcoming only)
            $registeredEvents = $eventRegistration->getUserRegisteredEvents($userId, $userType, 'registered');

            // Filter for upcoming events only (event_date >= today)
            $upcomingEvents = [];
            $allRegisteredEvents = [];
            if ($registeredEvents) {
                foreach ($registeredEvents as $event) {
                    $mappedEvent = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => isset($event->description) ? substr($event->description, 0, 100) . '...' : '',
                        'date' => $event->event_date,
                        'time' => $event->event_time,
                        'location' => $event->location,
                        'category' => $event->category,
                        'university' => $event->university_name,
                        'image_url' => $event->image_url,
                        'organizer' => $event->organizer,
                        'max_participants' => $event->max_participants,
                        'current_participants' => $event->current_participants ?? 0
                    ];

                    $allRegisteredEvents[] = $mappedEvent;

                    $eventDate = strtotime($event->event_date);
                    if ($eventDate >= strtotime('today')) {
                        $upcomingEvents[] = $mappedEvent;
                    }
                }
            }

            // Fallback: if strict upcoming filter has no results, show registered events
            if (empty($upcomingEvents) && !empty($allRegisteredEvents)) {
                $upcomingEvents = $allRegisteredEvents;
            }

            // Sort by event date (earliest first)
            usort($upcomingEvents, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            echo json_encode([
                'success' => true,
                'events' => $upcomingEvents,
                'count' => count($upcomingEvents)
            ]);
        } catch (Exception $e) {
            error_log("Error in getUpcomingEvents: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG ? $e->getTraceAsString() : ''
            ]);
        }
    }

    /**
     * API endpoint to get featured events
     */
    public function getFeaturedEvents()
    {
        header('Content-Type: application/json');

        try {
            $event = new Event();

            // Get featured events (you can define your own criteria)
            $filters = [
                'status' => 'upcoming'
            ];

            $featuredEvents = $event->getAllEvents($filters);

            // Format events
            $formatted = [];
            if ($featuredEvents) {
                foreach (array_slice($featuredEvents, 0, 6) as $event) {
                    $formatted[] = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => substr($event->description, 0, 100) . '...',
                        'date' => $event->event_date,
                        'category' => $event->category,
                        'university' => $event->university_name,
                        'image_url' => $event->image_url
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'events' => $formatted
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API endpoint to get recent activity
     */
    public function getRecentActivity()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Load Activity model
            $activity = new Activity();

            // Get recent activities (from last 7 days)
            $recentActivities = $activity->getRecentActivities($userId, $userType, 20);

            // Format activities for frontend
            $formatted = [];
            if ($recentActivities) {
                foreach ($recentActivities as $act) {
                    $formatted[] = $activity->formatActivityForDisplay($act);
                }
            }

            echo json_encode([
                'success' => true,
                'activities' => $formatted,
                'count' => count($formatted)
            ]);
        } catch (Exception $e) {
            error_log("Error in getRecentActivity: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG ? $e->getTraceAsString() : ''
            ]);
        }
    }

    /**
     * API endpoint to get user's donations for dashboard table
     */
    public function getUserDonations()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || !in_array($currentUser['type'] ?? '', ['public', 'university'])) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                return;
            }

            $donationModel = new Donation();
            $donations = $donationModel->getUserDonations((int)$currentUser['id'], (string)$currentUser['type']) ?: [];

            $formatted = array_map(function ($donation) {
                $status = strtolower((string)($donation->status ?? 'pending'));

                $statusLabel = 'Pending';
                if ($status === 'accepted' || $status === 'completed') {
                    $statusLabel = 'Approved';
                } elseif ($status === 'rejected' || $status === 'failed' || $status === 'refunded') {
                    $statusLabel = 'Rejected';
                }

                return [
                    'event_name' => $donation->event_title ?? 'Event',
                    'donated_date' => $donation->created_at ?? null,
                    'amount' => (float)($donation->amount ?? 0),
                    'currency' => $donation->currency ?? 'LKR',
                    'status' => $status,
                    'status_label' => $statusLabel
                ];
            }, $donations);

            echo json_encode([
                'success' => true,
                'donations' => $formatted,
                'count' => count($formatted)
            ]);
        } catch (Exception $e) {
            error_log('Error in getUserDonations: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load donations'
            ]);
        }
    }

    /**
     * API endpoint to get current user's comment history
     */
    public function getMyComments()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                return;
            }

            $commentModel = new Comment();
            $comments = $commentModel->getCommentsByUser($currentUser['id'], $currentUser['type']);

            $formatted = [];
            foreach ($comments ?: [] as $c) {
                $date = new DateTime($c->created_at);
                $now  = new DateTime();
                $diff = $now->diff($date);
                if ($diff->days == 0) {
                    $fmtDate = $diff->h == 0 ? ($diff->i . ' min ago') : ($diff->h . 'h ago');
                } elseif ($diff->days == 1) {
                    $fmtDate = 'Yesterday';
                } elseif ($diff->days < 7) {
                    $fmtDate = $diff->days . ' days ago';
                } else {
                    $fmtDate = $date->format('M j, Y');
                }

                $formatted[] = [
                    'id'             => $c->id,
                    'event_id'       => $c->event_id,
                    'event_title'    => $c->event_title,
                    'event_date'     => $c->event_date,
                    'comment_text'   => $c->comment_text,
                    'rating'         => $c->rating,
                    'is_edited'      => (bool)$c->is_edited,
                    'is_hidden'      => (bool)$c->is_hidden,
                    'hidden_reason'  => $c->hidden_reason,
                    'hidden_by_name' => $c->hidden_by_name,
                    'hidden_at'      => $c->hidden_at,
                    'formatted_date' => $fmtDate,
                ];
            }

            echo json_encode(['success' => true, 'comments' => $formatted]);
        } catch (Exception $e) {
            error_log('getMyComments error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }

    /**
     * API endpoint to get user's volunteering applications and latest status
     */
    public function getVolunteeringStatus()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            $volunteerReg = new VolunteerRegistration();

            $sql = "SELECT vr.id, vr.status, vr.created_at, vr.event_id,
                                                     vr.volunteer_position, vr.availability,
                                                     e.title, e.event_date, e.event_time
                    FROM volunteer_registrations vr
                    LEFT JOIN events e ON e.id = vr.event_id
                    WHERE vr.user_id = :user_id
                      AND vr.user_type = :user_type
                      AND vr.status != 'withdrawn'
                    ORDER BY vr.created_at DESC
                                        LIMIT 20";

            $result = $volunteerReg->query($sql, [
                'user_id' => $userId,
                'user_type' => $userType
            ]);

            if (!$result || count($result) === 0) {
                echo json_encode([
                    'success' => true,
                    'hasApplication' => false,
                    'applications' => [],
                    'application' => null
                ]);
                return;
            }

            $applications = [];
            foreach ($result as $row) {
                $applications[] = [
                    'id' => (int)($row->id ?? 0),
                    'event_id' => (int)($row->event_id ?? 0),
                    'event_title' => $row->title ?? 'Volunteer Application',
                    'event_date' => $row->event_date ?? null,
                    'event_time' => $row->event_time ?? null,
                    'status' => $row->status ?? 'pending',
                    'volunteer_position' => $row->volunteer_position ?? 'General Volunteer',
                    'availability' => $row->availability ?? 'Flexible',
                    'applied_at' => $row->created_at ?? null
                ];
            }

            $latest = $applications[0];

            echo json_encode([
                'success' => true,
                'hasApplication' => true,
                'applications' => $applications,
                // Keep latest application field for backward compatibility.
                'application' => $latest
            ]);
        } catch (Exception $e) {
            error_log("Error in getVolunteeringStatus: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load volunteering status'
            ]);
        }
    }

    /**
     * API endpoint to get monthly evolution data
     */
    public function getMonthlyEvolution()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Get month parameter (default to current month)
            $month = $_GET['month'] ?? date('Y-m');

            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                echo json_encode(['success' => false, 'error' => 'Invalid month format']);
                return;
            }

            // Load models
            $volunteerReg = new VolunteerRegistration();
            $donation = new Donation();
            $eventReg = new EventRegistration();

            // Get data for the month
            $volunteering = $volunteerReg->getUserMonthlyVolunteering($userId, $userType, $month);
            $donations = $donation->getUserMonthlyDonations($userId, $userType, $month);
            $participation = $eventReg->getUserMonthlyParticipation($userId, $userType, $month);

            // Calculate totals
            $donationTotal = $donation->getUserMonthlyDonationTotal($userId, $userType, $month);
            $eventSpending = $eventReg->getUserMonthlyEventSpending($userId, $userType, $month);

            echo json_encode([
                'success' => true,
                'month' => $month,
                'data' => [
                    'volunteering' => $volunteering ?: [],
                    'donations' => $donations ?: [],
                    'participation' => $participation ?: [],
                    'totals' => [
                        'donations' => floatval($donationTotal),
                        'eventSpending' => floatval($eventSpending),
                        'volunteerCount' => count($volunteering ?: []),
                        'participationCount' => count($participation ?: [])
                    ]
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in getMonthlyEvolution: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate and download monthly evolution CSV report
     */
    public function downloadMonthlyReport()
    {
        if (!AuthService::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit();
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            $userId = $currentUser['id'];
            $userType = $currentUser['type'];
            $userName = $currentUser['name'];

            // Get month parameter
            $month = $_GET['month'] ?? date('Y-m');

            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Invalid month format']);
                exit();
            }

            // Load models
            $volunteerReg = new VolunteerRegistration();
            $donation = new Donation();
            $eventReg = new EventRegistration();

            // Get data
            $volunteering = $volunteerReg->getUserMonthlyVolunteering($userId, $userType, $month) ?? [];
            $donations = $donation->getUserMonthlyDonations($userId, $userType, $month) ?? [];
            $participation = $eventReg->getUserMonthlyParticipation($userId, $userType, $month) ?? [];
            $donationTotal = floatval($donation->getUserMonthlyDonationTotal($userId, $userType, $month) ?? 0);
            $eventSpending = floatval($eventReg->getUserMonthlyEventSpending($userId, $userType, $month) ?? 0);

            // Generate PDF content
            $pdf = $this->generateReportPDF($userName, $month, [
                'volunteering' => $volunteering,
                'donations' => $donations,
                'participation' => $participation,
                'donationTotal' => $donationTotal,
                'eventSpending' => $eventSpending
            ]);

            // Return PDF as base64 JSON for client-side download
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'pdf' => base64_encode($pdf),
                'filename' => 'monthly-report-' . $month . '.pdf'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            error_log("Error in downloadMonthlyReport: " . $e->getMessage() . " Stack: " . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Generate PDF for the monthly report using native PHP
     */
    private function generateReportPDF($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));

        // Ensure all data arrays are actual arrays, not false
        $volunteering = is_array($data['volunteering']) ? $data['volunteering'] : [];
        $donations = is_array($data['donations']) ? $data['donations'] : [];
        $participation = is_array($data['participation']) ? $data['participation'] : [];
        $donationTotal = floatval($data['donationTotal'] ?? 0);
        $eventSpending = floatval($data['eventSpending'] ?? 0);
        $headerLogo = $this->loadPDFHeaderLogoImage();

        // Build colorful, statement-style page content.
        $content = $this->buildPDFContent(
            $monthName,
            $userName,
            $volunteering,
            $donations,
            $participation,
            $donationTotal,
            $eventSpending,
            !empty($headerLogo)
        );

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $resourceParts = [
            '/Font << /F1 5 0 R /F2 6 0 R >>'
        ];

        if (!empty($headerLogo)) {
            $resourceParts[] = '/XObject << /Im1 7 0 R >>';
        }

        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << " . implode(' ', $resourceParts) . " >> >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        if (!empty($headerLogo)) {
            $objects[7] = "<< /Type /XObject /Subtype /Image"
                . " /Width " . (int)$headerLogo['width']
                . " /Height " . (int)$headerLogo['height']
                . " /ColorSpace /DeviceRGB /BitsPerComponent 8"
                . " /Filter /DCTDecode /Length " . strlen($headerLogo['data']) . " >>\nstream\n"
                . $headerLogo['data']
                . "\nendstream";
        }

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
     * Build PDF content stream
     */
    private function buildPDFContent($monthName, $userName, $volunteering, $donations, $participation, $donationTotal, $eventSpending, $hasHeaderLogo = false)
    {
        $content = '';

        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 48;
        $contentRight = $pageWidth - $marginX;
        $contentWidth = $contentRight - $marginX;

        $brandPrimary = [30, 58, 138];
        $brandSecondary = [249, 115, 22];
        $pageBackground = [248, 250, 252];
        $textDark = [30, 41, 59];
        $textMuted = [100, 116, 139];
        $lineColor = [226, 232, 240];
        $spendingsTotal = $donationTotal + $eventSpending;
        $headerBottom = 688;
        $headerHeight = 104;

        // Page background + branded top banner.
        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, $pageBackground);
        $content .= $this->pdfLinearGradientRect(0, $headerBottom, $pageWidth, $headerHeight, $brandPrimary, $brandSecondary, 56);
        $content .= $this->pdfLine(0, $headerBottom, $pageWidth, $headerBottom, [254, 215, 170], 1.2);

        if ($hasHeaderLogo) {
            $logoBoxX = $marginX;
            $logoBoxY = 724;
            $logoBoxW = 100;
            $logoBoxH = 52;
            $content .= $this->pdfRect($logoBoxX, $logoBoxY, $logoBoxW, $logoBoxH, [255, 255, 255], [253, 186, 116], 0.8);
            $content .= $this->pdfImage('Im1', $logoBoxX + 6, $logoBoxY + 5, $logoBoxW - 12, $logoBoxH - 10);
        } else {
            $content .= $this->pdfText($marginX, 748, 'UniPulse', 'F2', 23, [255, 255, 255]);
        }

        $content .= $this->pdfText($marginX, 710, 'Monthly Activity Statement', 'F2', 16, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 692, $monthName . '  |  Generated ' . date('M d, Y'), 'F1', 10, [219, 234, 254]);

        $accountPanelX = $contentRight - 180;
        $content .= $this->pdfRect($accountPanelX, 712, 170, 54, null, [255, 237, 213], 0.9);
        $content .= $this->pdfText($accountPanelX + 12, 746, 'Account Holder', 'F1', 9, [255, 237, 213]);
        $content .= $this->pdfText($accountPanelX + 12, 724, $this->truncatePDFText($userName, 24), 'F2', 14, [255, 255, 255]);

        // KPI cards row with consistent side margins.
        $cardY = 602;
        $cardHeight = 70;
        $cardGap = 12;
        $cardWidth = ($contentWidth - ($cardGap * 3)) / 4;
        $cardX = $marginX;

        $cards = [
            ['label' => 'Volunteer Sessions', 'value' => (string)count($volunteering), 'accent' => [30, 58, 138]],
            ['label' => 'Donations', 'value' => 'LKR ' . number_format($donationTotal, 2), 'accent' => [249, 115, 22]],
            ['label' => 'Events Participated', 'value' => (string)count($participation), 'accent' => [37, 99, 235]],
            ['label' => 'Event Spending', 'value' => 'LKR ' . number_format($eventSpending, 2), 'accent' => [251, 146, 60]],
        ];

        foreach ($cards as $card) {
            $content .= $this->pdfRect($cardX, $cardY, $cardWidth, $cardHeight, [255, 255, 255], $lineColor, 0.9);
            $content .= $this->pdfRect($cardX, $cardY + $cardHeight - 5, $cardWidth, 5, $card['accent']);
            $content .= $this->pdfText($cardX + 10, $cardY + 47, $card['label'], 'F1', 8.5, $textMuted);
            $content .= $this->pdfText($cardX + 10, $cardY + 24, $this->truncatePDFText($card['value'], 20), 'F2', 13, $textDark);
            $cardX += ($cardWidth + $cardGap);
        }

        // Donations section.
        $content .= $this->pdfText($marginX, 564, 'Donations', 'F2', 13, $brandPrimary);
        $content .= $this->pdfLine($marginX, 559, $contentRight, 559, $lineColor, 1);

        $tableX = $marginX;
        $tableW = $contentWidth;
        $headerY = 532;
        $rowH = 20;
        $amountX = $tableX + $tableW - 205;
        $statusX = $tableX + $tableW - 70;

        $content .= $this->pdfLinearGradientRect($tableX, $headerY, $tableW, $rowH, $brandPrimary, $brandSecondary, 22);
        $content .= $this->pdfText($tableX + 10, $headerY + 6, '#', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 34, $headerY + 6, 'TYPE', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 210, $headerY + 6, 'DATE', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($amountX, $headerY + 6, 'AMOUNT', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($statusX, $headerY + 6, 'STATUS', 'F2', 8.5, [255, 255, 255]);

        $maxDonationRows = 6;
        $donationRows = array_slice($donations, 0, $maxDonationRows);
        $rowY = $headerY - $rowH;

        if (empty($donationRows)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], $lineColor, 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No donations recorded for this month.', 'F1', 9, $textMuted);
            $rowY -= $rowH;
        } else {
            foreach ($donationRows as $index => $donation) {
                $bg = (($index % 2) === 0) ? [255, 255, 255] : [248, 250, 252];
                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, $lineColor, 0.6);

                $name = $donation->event_title ?? $donation->title ?? 'Event Donation';
                $date = $this->formatPDFDate($donation->created_at ?? null);
                $amount = 'LKR ' . number_format((float)($donation->amount ?? 0), 2);
                $statusRaw = strtolower((string)($donation->status ?? 'pending'));
                $status = ($statusRaw === 'accepted' || $statusRaw === 'completed') ? 'Completed' : (($statusRaw === 'rejected' || $statusRaw === 'failed' || $statusRaw === 'refunded') ? 'Rejected' : 'Pending');
                $statusColor = ($status === 'Completed') ? [22, 163, 74] : (($status === 'Rejected') ? [220, 38, 38] : [245, 158, 11]);

                $content .= $this->pdfText($tableX + 10, $rowY + 6, (string)($index + 1), 'F1', 8.5, $textDark);
                $content .= $this->pdfText($tableX + 34, $rowY + 6, $this->truncatePDFText($name, 32), 'F2', 8.5, $textDark);
                $content .= $this->pdfText($tableX + 210, $rowY + 6, $date, 'F1', 8.5, $textMuted);
                $content .= $this->pdfText($amountX, $rowY + 6, $amount, 'F2', 8.5, $textDark);
                $content .= $this->pdfText($statusX, $rowY + 6, $status, 'F2', 8.5, $statusColor);

                $rowY -= $rowH;
            }
        }

        $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 247, 237]);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'Total Donations', 'F2', 9.5, $brandPrimary);
        $content .= $this->pdfText($tableX + $tableW - 130, $rowY + 6, 'LKR ' . number_format($donationTotal, 2), 'F2', 9.5, $brandPrimary);

        // Participation section.
        $sectionTitleY = $rowY - 40;
        $content .= $this->pdfText($marginX, $sectionTitleY, 'Event Participation', 'F2', 13, $brandPrimary);
        $content .= $this->pdfLine($marginX, $sectionTitleY - 5, $contentRight, $sectionTitleY - 5, $lineColor, 1);

        $pHeaderY = $sectionTitleY - 33;
        $content .= $this->pdfLinearGradientRect($tableX, $pHeaderY, $tableW, $rowH, $brandPrimary, $brandSecondary, 22);
        $content .= $this->pdfText($tableX + 10, $pHeaderY + 6, '#', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 34, $pHeaderY + 6, 'EVENT NAME', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 290, $pHeaderY + 6, 'TICKET TYPE', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 410, $pHeaderY + 6, 'AMOUNT PAID', 'F2', 8.5, [255, 255, 255]);

        $pRowY = $pHeaderY - $rowH;
        $maxParticipationRows = 7;
        $participationRows = array_slice($participation, 0, $maxParticipationRows);

        if (empty($participationRows)) {
            $content .= $this->pdfRect($tableX, $pRowY, $tableW, $rowH, [255, 255, 255], $lineColor, 0.6);
            $content .= $this->pdfText($tableX + 10, $pRowY + 6, 'No event participation for this month.', 'F1', 9, $textMuted);
        } else {
            foreach ($participationRows as $index => $event) {
                $bg = (($index % 2) === 0) ? [255, 255, 255] : [248, 250, 252];
                $content .= $this->pdfRect($tableX, $pRowY, $tableW, $rowH, $bg, $lineColor, 0.6);

                $eventName = $event->title ?? 'Event';
                $ticketType = strtoupper((string)($event->ticket_type ?? 'free'));
                $amountPaidValue = (float)($event->amount_paid ?? 0);
                $amountPaid = $amountPaidValue > 0 ? ('LKR ' . number_format($amountPaidValue, 2)) : 'FREE';
                $amountColor = $amountPaidValue > 0 ? $textDark : [31, 151, 84];

                $content .= $this->pdfText($tableX + 10, $pRowY + 6, (string)($index + 1), 'F1', 8.5, $textDark);
                $content .= $this->pdfText($tableX + 34, $pRowY + 6, $this->truncatePDFText($eventName, 40), 'F2', 8.5, $textDark);
                $content .= $this->pdfText($tableX + 290, $pRowY + 6, $this->truncatePDFText($ticketType, 14), 'F1', 8.5, $textMuted);
                $content .= $this->pdfText($tableX + 410, $pRowY + 6, $amountPaid, 'F2', 8.5, $amountColor);

                $pRowY -= $rowH;
            }
        }

        // Total event spending row (matches donations summary row pattern).
        $content .= $this->pdfRect($tableX, $pRowY, $tableW, $rowH, [255, 247, 237]);
        $content .= $this->pdfText($tableX + 10, $pRowY + 6, 'Total Event Spending', 'F2', 9.5, $brandPrimary);
        $content .= $this->pdfText($tableX + $tableW - 130, $pRowY + 6, 'LKR ' . number_format($eventSpending, 2), 'F2', 9.5, $brandPrimary);
        $pRowY -= 32;

        // Total spendings section.
        $spendingTitleY = $pRowY;
        $content .= $this->pdfText($marginX, $spendingTitleY, 'Total Spendings', 'F2', 13, $brandPrimary);
        $content .= $this->pdfLine($marginX, $spendingTitleY - 5, $contentRight, $spendingTitleY - 5, $lineColor, 1);

        $spendingRowY = $spendingTitleY - 28;
        $content .= $this->pdfRect($tableX, $spendingRowY, $tableW, $rowH, [255, 255, 255], $lineColor, 0.6);
        $content .= $this->pdfText($tableX + 10, $spendingRowY + 6, 'Donations', 'F1', 9, $textDark);
        $content .= $this->pdfText($tableX + $tableW - 130, $spendingRowY + 6, 'LKR ' . number_format($donationTotal, 2), 'F2', 9, $textDark);

        $spendingRowY -= $rowH;
        $content .= $this->pdfRect($tableX, $spendingRowY, $tableW, $rowH, [255, 255, 255], $lineColor, 0.6);
        $content .= $this->pdfText($tableX + 10, $spendingRowY + 6, 'Event Spending', 'F1', 9, $textDark);
        $content .= $this->pdfText($tableX + $tableW - 130, $spendingRowY + 6, 'LKR ' . number_format($eventSpending, 2), 'F2', 9, $textDark);

        $spendingRowY -= $rowH;
        $content .= $this->pdfRect($tableX, $spendingRowY, $tableW, $rowH, [255, 237, 213], $lineColor, 0.8);
        $content .= $this->pdfText($tableX + 10, $spendingRowY + 6, 'Overall Spending', 'F2', 9.5, [194, 65, 12]);
        $content .= $this->pdfText($tableX + $tableW - 130, $spendingRowY + 6, 'LKR ' . number_format($spendingsTotal, 2), 'F2', 9.5, [194, 65, 12]);

        // Footer band.
        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 28, $brandPrimary, $brandSecondary, 36);
        $content .= $this->pdfText($marginX, 9, 'UniPulse  |  Confidential Statement  |  ' . $monthName, 'F1', 8.5, [219, 234, 254]);

        return $content;
    }

    private function pdfImage($imageRefName, $x, $y, $width, $height)
    {
        return "q\n"
            . number_format($width, 2, '.', '') . " 0 0 " . number_format($height, 2, '.', '') . " "
            . number_format($x, 2, '.', '') . " " . number_format($y, 2, '.', '') . " cm\n"
            . "/" . $imageRefName . " Do\nQ\n";
    }

    private function loadPDFHeaderLogoImage()
    {
        $logoPath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';
        if (!is_file($logoPath)) {
            return null;
        }

        if (!function_exists('imagecreatefrompng') || !function_exists('imagejpeg')) {
            return null;
        }

        $sourceImage = @imagecreatefrompng($logoPath);
        if (!$sourceImage) {
            return null;
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($sourceImage);
            return null;
        }

        // Flatten transparency onto white for consistent PDF output.
        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $sourceImage, 0, 0, 0, 0, $width, $height);

        ob_start();
        imagejpeg($canvas, null, 92);
        $jpegBinary = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($sourceImage);

        if ($jpegBinary === false || $jpegBinary === '') {
            return null;
        }

        return [
            'width' => $width,
            'height' => $height,
            'data' => $jpegBinary,
        ];
    }

    private function pdfRect($x, $y, $width, $height, $fillColor = null, $strokeColor = null, $lineWidth = 1)
    {
        $cmd = '';

        if (is_array($fillColor)) {
            $cmd .= $this->pdfColor($fillColor, false);
        }

        if (is_array($strokeColor)) {
            $cmd .= $this->pdfColor($strokeColor, true);
            $cmd .= number_format($lineWidth, 2, '.', '') . " w\n";
        }

        $x = number_format($x, 2, '.', '');
        $y = number_format($y, 2, '.', '');
        $width = number_format($width, 2, '.', '');
        $height = number_format($height, 2, '.', '');

        if (is_array($fillColor) && is_array($strokeColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re B\n";
        } elseif (is_array($fillColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re f\n";
        } elseif (is_array($strokeColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re S\n";
        }

        return $cmd;
    }

    private function pdfLinearGradientRect($x, $y, $width, $height, $startColor, $endColor, $steps = 32)
    {
        $steps = max(2, (int)$steps);
        $cmd = '';

        for ($i = 0; $i < $steps; $i++) {
            $t0 = $i / $steps;
            $t1 = ($i + 1) / $steps;

            $segmentX = $x + ($width * $t0);
            $segmentW = $width * ($t1 - $t0);

            $color = [
                (int)round(($startColor[0] ?? 0) + (($endColor[0] ?? 0) - ($startColor[0] ?? 0)) * $t0),
                (int)round(($startColor[1] ?? 0) + (($endColor[1] ?? 0) - ($startColor[1] ?? 0)) * $t0),
                (int)round(($startColor[2] ?? 0) + (($endColor[2] ?? 0) - ($startColor[2] ?? 0)) * $t0),
            ];

            $cmd .= $this->pdfRect($segmentX, $y, $segmentW + 0.2, $height, $color);
        }

        return $cmd;
    }

    private function pdfLine($x1, $y1, $x2, $y2, $strokeColor = [0, 0, 0], $lineWidth = 1)
    {
        return $this->pdfColor($strokeColor, true)
            . number_format($lineWidth, 2, '.', '') . " w\n"
            . number_format($x1, 2, '.', '') . ' ' . number_format($y1, 2, '.', '') . " m\n"
            . number_format($x2, 2, '.', '') . ' ' . number_format($y2, 2, '.', '') . " l\nS\n";
    }

    private function pdfText($x, $y, $text, $font = 'F1', $fontSize = 10, $color = [0, 0, 0])
    {
        return "BT\n"
            . $this->pdfColor($color, false)
            . '/' . $font . ' ' . number_format($fontSize, 2, '.', '') . " Tf\n"
            . "1 0 0 1 " . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . " Tm\n"
            . '(' . $this->escapePDF($text) . ") Tj\n"
            . "ET\n";
    }

    private function pdfColor($rgb, $isStroke)
    {
        $r = number_format(max(0, min(255, (float)($rgb[0] ?? 0))) / 255, 3, '.', '');
        $g = number_format(max(0, min(255, (float)($rgb[1] ?? 0))) / 255, 3, '.', '');
        $b = number_format(max(0, min(255, (float)($rgb[2] ?? 0))) / 255, 3, '.', '');
        return $r . ' ' . $g . ' ' . $b . ($isStroke ? " RG\n" : " rg\n");
    }

    private function truncatePDFText($text, $maxChars)
    {
        $text = trim((string)$text);
        if (strlen($text) <= $maxChars) {
            return $text;
        }

        return substr($text, 0, max(0, $maxChars - 3)) . '...';
    }

    private function formatPDFDate($value)
    {
        if (!$value) {
            return '-';
        }

        $timestamp = strtotime((string)$value);
        if ($timestamp === false) {
            return '-';
        }

        return date('M d, Y', $timestamp);
    }

    /**
     * Escape special characters for PDF content
     */
    private function escapePDF($text)
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        return $text;
    }

    /**
     * Generate CSV for the monthly report
     */
    private function generateReportCSV($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));
        $csv = "";

        // Ensure all data arrays are actual arrays, not false
        $volunteering = is_array($data['volunteering']) ? $data['volunteering'] : [];
        $donations = is_array($data['donations']) ? $data['donations'] : [];
        $participation = is_array($data['participation']) ? $data['participation'] : [];
        $donationTotal = floatval($data['donationTotal'] ?? 0);
        $eventSpending = floatval($data['eventSpending'] ?? 0);

        // Header
        $csv .= "Monthly Activity Report - " . $monthName . "\n";
        $csv .= "User: " . $this->escapeCSV($userName) . "\n";
        $csv .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // Summary
        $csv .= "SUMMARY\n";
        $csv .= "Volunteer Sessions," . count($volunteering) . "\n";
        $csv .= "Donations," . count($donations) . "\n";
        $csv .= "Events Participated," . count($participation) . "\n";
        $csv .= "Total Donations (LKR)," . number_format($donationTotal, 2) . "\n";
        $csv .= "Event Spending (LKR)," . number_format($eventSpending, 2) . "\n\n";

        // Volunteering Section
        $csv .= "VOLUNTEERING ACTIVITIES\n";
        $csv .= "Event,Title,Position,Status,Date\n";
        if (!empty($volunteering)) {
            foreach ($volunteering as $vol) {
                $csv .= $this->escapeCSV($vol->title ?? '') . ",";
                $csv .= $this->escapeCSV($vol->volunteer_position ?? '') . ",";
                $csv .= $this->escapeCSV($vol->volunteer_status ?? '') . ",";
                $csv .= date('Y-m-d', strtotime($vol->event_date ?? '')) . "\n";
            }
        } else {
            $csv .= "No volunteering activities this month\n";
        }
        $csv .= "\n";

        // Donations Section
        $csv .= "DONATIONS\n";
        $csv .= "Event,Amount (LKR),Donor Name,Status,Date\n";
        if (!empty($donations)) {
            foreach ($donations as $donation) {
                $csv .= $this->escapeCSV($donation->title ?? '') . ",";
                $csv .= number_format($donation->amount ?? 0, 2) . ",";
                $csv .= $this->escapeCSV($donation->donor_name ?? '') . ",";
                $csv .= $this->escapeCSV($donation->status ?? '') . ",";
                $csv .= date('Y-m-d', strtotime($donation->created_at ?? '')) . "\n";
            }
        } else {
            $csv .= "No donations this month\n";
        }
        $csv .= "\n";

        // Participation Section
        $csv .= "EVENT PARTICIPATION\n";
        $csv .= "Event,Ticket Type,Amount Paid (LKR),Registration Date,Event Date\n";
        if (!empty($participation)) {
            foreach ($participation as $event) {
                $csv .= $this->escapeCSV($event->title ?? '') . ",";
                $csv .= $this->escapeCSV($event->ticket_type ?? '') . ",";
                $csv .= number_format($event->amount_paid ?? 0, 2) . ",";
                $csv .= date('Y-m-d', strtotime($event->registration_date ?? '')) . ",";
                $csv .= date('Y-m-d', strtotime($event->event_date ?? '')) . "\n";
            }
        } else {
            $csv .= "No event participation this month\n";
        }

        return $csv;
    }

    /**
     * Escape CSV values
     */
    private function escapeCSV($value)
    {
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Format timestamp as relative time text
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
     * Generate HTML for the monthly report
     */
    private function generateReportHTML($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));

        ob_start();
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <title>Monthly Activity Report - <?php echo $monthName; ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                }

                h1 {
                    color: #4F46E5;
                    border-bottom: 3px solid #4F46E5;
                    padding-bottom: 10px;
                }

                h2 {
                    color: #6366F1;
                    margin-top: 30px;
                    border-left: 4px solid #6366F1;
                    padding-left: 10px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }

                th,
                td {
                    border: 1px solid #ddd;
                    padding: 12px;
                    text-align: left;
                }

                th {
                    background-color: #4F46E5;
                    color: white;
                }

                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .summary {
                    background-color: #EEF2FF;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }

                .total {
                    font-weight: bold;
                    font-size: 18px;
                    color: #4F46E5;
                }

                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>

        <body>
            <h1>UniPulse - Monthly Activity Report</h1>
            <p><strong>User:</strong> <?php echo htmlspecialchars($userName); ?></p>
            <p><strong>Period:</strong> <?php echo $monthName; ?></p>
            <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>

            <div class="summary">
                <h3>Summary</h3>
                <p>Volunteer Activities: <?php echo count($data['volunteering'] ?: []); ?></p>
                <p>Events Participated: <?php echo count($data['participation'] ?: []); ?></p>
                <p>Donations Made: <?php echo count($data['donations'] ?: []); ?></p>
                <p class="total">Total Donations: LKR <?php echo number_format($data['donationTotal'], 2); ?></p>
                <p class="total">Total Event Spending: LKR <?php echo number_format($data['eventSpending'], 2); ?></p>
            </div>

            <h2>1. Volunteering Activities</h2>
            <?php if (!empty($data['volunteering'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['volunteering'] as $vol): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vol->title); ?></td>
                                <td><?php echo htmlspecialchars($vol->volunteer_position ?? 'General'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($vol->event_date)); ?></td>
                                <td><?php echo htmlspecialchars($vol->location); ?></td>
                                <td><?php echo ucfirst($vol->volunteer_status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No volunteering activities this month.</p>
            <?php endif; ?>

            <h2>2. Donations</h2>
            <?php if (!empty($data['donations'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['donations'] as $don): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($don->event_title); ?></td>
                                <td>LKR <?php echo number_format($don->amount, 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($don->created_at)); ?></td>
                                <td><?php echo ucfirst($don->status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="total">Total: LKR <?php echo number_format($data['donationTotal'], 2); ?></p>
            <?php else: ?>
                <p>No donations made this month.</p>
            <?php endif; ?>

            <h2>3. Event Participation</h2>
            <?php if (!empty($data['participation'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Ticket Type</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['participation'] as $part): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($part->title); ?></td>
                                <td><?php echo date('M d, Y', strtotime($part->event_date)); ?></td>
                                <td><?php echo htmlspecialchars($part->location); ?></td>
                                <td><?php echo ucfirst($part->ticket_type ?? 'Free'); ?></td>
                                <td>LKR <?php echo number_format($part->amount_paid, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="total">Total: LKR <?php echo number_format($data['eventSpending'], 2); ?></p>
            <?php else: ?>
                <p>No event participation this month.</p>
            <?php endif; ?>

            <h2>4. Other Information</h2>
            <p>Total Activities: <?php echo count($data['volunteering'] ?: []) + count($data['participation'] ?: []); ?></p>
            <p>Total Financial Contribution: LKR <?php echo number_format($data['donationTotal'] + $data['eventSpending'], 2); ?></p>

            <div class="footer">
                <p>This report was generated by UniPulse - University Event Management System</p>
                <p><?php echo date('Y'); ?> UniPulse. All rights reserved.</p>
            </div>
        </body>

        </html>
<?php
        return ob_get_clean();
    }
}
