<?php

class PublisherDashboard extends Controller{

    use Database;

    public function index($a = '', $b = '' , $c = ''){
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
     * Get recent comments for dashboard
     */
    public function getRecentComments() {
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
    public function getMyEvents() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
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
            
            $query = "
                SELECT 
                    e.*,
                    COUNT(ec.id) as comment_count,
                    COUNT(CASE WHEN ec.rating > 0 THEN 1 END) as rating_count,
                    AVG(CASE WHEN ec.rating > 0 THEN ec.rating END) as avg_rating
                FROM events e
                LEFT JOIN event_comments ec ON e.id = ec.event_id AND ec.is_deleted = 0
                $whereClause
                GROUP BY e.id
                ORDER BY e.created_at DESC
                LIMIT 10
            ";
            
            $stmt = $this->connect()->prepare($query);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $formattedEvents = [];
            foreach ($events as $event) {
                $formattedEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'status' => $event->status,
                    'category' => $event->category,
                    'image_url' => $event->image_url,
                    'comment_count' => (int)$event->comment_count,
                    'rating_count' => (int)$event->rating_count,
                    'avg_rating' => $event->avg_rating ? round((float)$event->avg_rating, 1) : null,
                    'formatted_date' => $this->formatDate($event->start_date)
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
     * Format date for display
     */
    private function formatDate($dateString) {
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
}
