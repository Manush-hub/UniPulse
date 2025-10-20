<?php

class PublisherComments extends Controller {
    
    use Database;
    
    private $commentModel;
    private $notificationModel;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Display comments dashboard for publisher
     */
    public function index() {
        // Check if publisher is logged in
        SessionMiddleware::requireAuth('publisher');
        
        $data = [
            'title' => 'Comments on Your Events',
            'page' => 'comments'
        ];
        
        $this->view('Publisher/comments', $data);
    }
    
    /**
     * Get comments for publisher's events
     */
    public function getMyEventComments() {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if ($currentUser['type'] !== 'publisher') {
                echo json_encode(['success' => false, 'error' => 'Publisher access required']);
                return;
            }
            
            $query = "
                SELECT 
                    c.*,
                    e.title as event_title,
                    e.status as event_status,
                    e.start_date,
                    e.end_date,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.full_name
                        WHEN c.user_type = 'public' THEN pu.full_name
                        WHEN c.user_type = 'publisher' THEN pub.society_name
                        WHEN c.user_type = 'sponsor' THEN s.company_name
                    END as user_name,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.email
                        WHEN c.user_type = 'public' THEN pu.email
                        WHEN c.user_type = 'publisher' THEN pub.email
                        WHEN c.user_type = 'sponsor' THEN s.email
                    END as user_email
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
                ORDER BY c.created_at DESC
                LIMIT 100
            ";
            
            $stmt = $this->connect()->prepare($query);
            $stmt->execute(['publisher_id' => $currentUser['id']]);
            $comments = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'event_id' => $comment->event_id,
                    'event_title' => $comment->event_title,
                    'event_status' => $comment->event_status,
                    'start_date' => $comment->start_date,
                    'end_date' => $comment->end_date,
                    'user_name' => $comment->user_name,
                    'user_email' => $comment->user_email,
                    'user_type' => $comment->user_type,
                    'comment_text' => $comment->comment_text,
                    'rating' => $comment->rating,
                    'is_edited' => (bool)$comment->is_edited,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'formatted_date' => $this->formatDate($comment->created_at)
                ];
            }
            
            // Get summary stats for publisher
            $statsQuery = "
                SELECT 
                    COUNT(c.id) as total_comments,
                    COUNT(DISTINCT c.event_id) as events_with_comments,
                    ROUND(AVG(CASE WHEN c.rating > 0 THEN c.rating END), 1) as average_rating,
                    COUNT(CASE WHEN c.rating > 0 THEN 1 END) as rated_comments
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
            ";
            
            $statsStmt = $this->connect()->prepare($statsQuery);
            $statsStmt->execute(['publisher_id' => $currentUser['id']]);
            $stats = $statsStmt->fetch(PDO::FETCH_OBJ);
            
            echo json_encode([
                'success' => true,
                'comments' => $formattedComments,
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'events_with_comments' => (int)$stats->events_with_comments,
                    'average_rating' => $stats->average_rating ? (float)$stats->average_rating : null,
                    'rated_comments' => (int)$stats->rated_comments
                ],
                'total' => count($formattedComments)
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting publisher comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }
    
    /**
     * Get comments for a specific event (publisher view)
     */
    public function getEventComments($eventId = null) {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if ($currentUser['type'] !== 'publisher') {
                echo json_encode(['success' => false, 'error' => 'Publisher access required']);
                return;
            }
            
            if (!$eventId && isset($_GET['event_id'])) {
                $eventId = $_GET['event_id'];
            }
            
            if (!$eventId || !is_numeric($eventId)) {
                echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
                return;
            }
            
            // Check if event belongs to this publisher
            $eventQuery = "
                SELECT * FROM events 
                WHERE id = :event_id 
                AND created_by_type = 'publisher' 
                AND created_by = :publisher_id
            ";
            $eventStmt = $this->connect()->prepare($eventQuery);
            $eventStmt->execute([
                'event_id' => $eventId,
                'publisher_id' => $currentUser['id']
            ]);
            $event = $eventStmt->fetch(PDO::FETCH_OBJ);
            
            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Event not found or not yours']);
                return;
            }
            
            // Get comments for the event
            $comments = $this->commentModel->getEventComments($eventId);
            $stats = $this->commentModel->getEventCommentStats($eventId);
            
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'user_name' => $comment->user_name,
                    'user_email' => $comment->user_email,
                    'user_type' => $comment->user_type,
                    'comment_text' => $comment->comment_text,
                    'rating' => $comment->rating,
                    'is_edited' => (bool)$comment->is_edited,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'formatted_date' => $this->formatDate($comment->created_at)
                ];
            }
            
            echo json_encode([
                'success' => true,
                'comments' => $formattedComments,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'status' => $event->status
                ],
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'average_rating' => $stats->average_rating ? round($stats->average_rating, 1) : null,
                    'rated_comments' => (int)$stats->rated_comments
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting publisher event comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }
    
    /**
     * Get notifications for publisher (AJAX endpoint)
     */
    public function getNotifications() {
        header('Content-Type: application/json');
        
        // Check if publisher is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            // Get notifications
            $notifications = $this->notificationModel->getUserNotifications(
                $currentUser['id'], 
                'publisher'
            );
            
            // Get unread count
            $unreadCount = $this->notificationModel->getUnreadCount(
                $currentUser['id'], 
                'publisher'
            );
            
            // Format notifications
            $formattedNotifications = [];
            foreach ($notifications as $notification) {
                $formattedNotifications[] = [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'related_id' => $notification->related_id,
                    'related_type' => $notification->related_type,
                    'is_read' => (bool)$notification->is_read,
                    'created_at' => $notification->created_at,
                    'formatted_date' => $this->formatDate($notification->created_at)
                ];
            }
            
            echo json_encode([
                'success' => true,
                'notifications' => $formattedNotifications,
                'unread_count' => $unreadCount
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load notifications']);
        }
    }
    
    /**
     * Mark notification as read (AJAX endpoint)
     */
    public function markNotificationRead($notificationId = null) {
        header('Content-Type: application/json');
        
        // Check if publisher is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'error' => 'Notification ID is required']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            $result = $this->notificationModel->markAsRead(
                $notificationId,
                $currentUser['id'],
                'publisher'
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to mark notification as read']);
            }
            
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to update notification']);
        }
    }
    
    /**
     * Mark all notifications as read (AJAX endpoint)
     */
    public function markAllNotificationsRead() {
        header('Content-Type: application/json');
        
        // Check if publisher is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            $result = $this->notificationModel->markAllAsRead(
                $currentUser['id'],
                'publisher'
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to mark notifications as read']);
            }
            
        } catch (Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to update notifications']);
        }
    }
    
    /**
     * Get comment statistics for publisher dashboard
     */
    public function getStats() {
        header('Content-Type: application/json');
        
        // Check if publisher is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            // Get overall statistics
            $query = "
                SELECT 
                    COUNT(DISTINCT c.id) as total_comments,
                    COUNT(DISTINCT c.event_id) as events_with_comments,
                    AVG(c.rating) as average_rating,
                    COUNT(CASE WHEN c.rating IS NOT NULL THEN 1 END) as rated_comments,
                    COUNT(CASE WHEN DATE(c.created_at) = CURDATE() THEN 1 END) as comments_today,
                    COUNT(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as comments_this_week
                FROM event_comments c
                JOIN events e ON c.event_id = e.id
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                AND c.is_deleted = 0
            ";
            
            $stats = $this->commentModel->first($query, ['publisher_id' => $currentUser['id']]);
            
            // Get unread notifications count
            $unreadCount = $this->notificationModel->getUnreadCount(
                $currentUser['id'], 
                'publisher'
            );
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'events_with_comments' => (int)$stats->events_with_comments,
                    'average_rating' => $stats->average_rating ? round($stats->average_rating, 1) : 0,
                    'rated_comments' => (int)$stats->rated_comments,
                    'comments_today' => (int)$stats->comments_today,
                    'comments_this_week' => (int)$stats->comments_this_week,
                    'unread_notifications' => $unreadCount
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting comment stats: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load statistics']);
        }
    }
    
    /**
     * Format date for display
     */
    private function formatDate($dateString) {
        $date = new DateTime($dateString);
        $now = new DateTime();
        $interval = $now->diff($date);
        
        if ($interval->d == 0) {
            if ($interval->h == 0) {
                if ($interval->i == 0) {
                    return 'Just now';
                }
                return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
            }
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d < 7) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } else {
            return $date->format('M j, Y');
        }
    }
}