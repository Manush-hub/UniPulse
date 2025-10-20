<?php

class ModeratorComments extends Controller {
    
    use Database;
    
    private $commentModel;
    private $eventModel;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->eventModel = new Event();
    }
    
    /**
     * Get comments for events in moderator's university
     */
    public function getUniversityComments() {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if ($currentUser['type'] !== 'moderator') {
                echo json_encode(['success' => false, 'error' => 'Moderator access required']);
                return;
            }
            
            // Get moderator's university
            $modQuery = "SELECT university FROM moderators WHERE id = :mod_id";
            $modStmt = $this->connect()->prepare($modQuery);
            $modStmt->execute(['mod_id' => $currentUser['id']]);
            $moderator = $modStmt->fetch(PDO::FETCH_OBJ);
            
            if (!$moderator) {
                echo json_encode(['success' => false, 'error' => 'Moderator not found']);
                return;
            }
            
            $query = "
                SELECT 
                    c.*,
                    e.title as event_title,
                    e.status as event_status,
                    p.society_name as publisher_name,
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
                LEFT JOIN publishers p ON e.created_by_type = 'publisher' AND e.created_by = p.id
                LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
                WHERE c.is_deleted = 0
                AND p.university = :university
                ORDER BY c.created_at DESC
                LIMIT 50
            ";
            
            $stmt = $this->connect()->prepare($query);
            $stmt->execute(['university' => $moderator->university]);
            $comments = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'event_id' => $comment->event_id,
                    'event_title' => $comment->event_title,
                    'event_status' => $comment->event_status,
                    'publisher_name' => $comment->publisher_name,
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
                'university' => $moderator->university,
                'total' => count($formattedComments)
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting moderator comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }
    
    /**
     * Get comments for a specific event (moderator view)
     */
    public function getEventComments($eventId = null) {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            if ($currentUser['type'] !== 'moderator') {
                echo json_encode(['success' => false, 'error' => 'Moderator access required']);
                return;
            }
            
            if (!$eventId || !is_numeric($eventId)) {
                echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
                return;
            }
            
            // Check if event is in moderator's university
            $modQuery = "SELECT university FROM moderators WHERE id = :mod_id";
            $modStmt = $this->connect()->prepare($modQuery);
            $modStmt->execute(['mod_id' => $currentUser['id']]);
            $moderator = $modStmt->fetch(PDO::FETCH_OBJ);
            
            $eventQuery = "
                SELECT e.*, p.university as publisher_university 
                FROM events e 
                LEFT JOIN publishers p ON e.created_by_type = 'publisher' AND e.created_by = p.id
                WHERE e.id = :event_id
            ";
            $eventStmt = $this->connect()->prepare($eventQuery);
            $eventStmt->execute(['event_id' => $eventId]);
            $event = $eventStmt->fetch(PDO::FETCH_OBJ);
            
            if (!$event || $event->publisher_university !== $moderator->university) {
                echo json_encode(['success' => false, 'error' => 'Event not in your university']);
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
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'average_rating' => $stats->average_rating ? round($stats->average_rating, 1) : null,
                    'rated_comments' => (int)$stats->rated_comments
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting moderator event comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
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
?>