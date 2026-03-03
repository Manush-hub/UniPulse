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
     * Comments moderation page
     */
    public function index() {
        // Check authentication
        if (!AuthService::isLoggedIn()) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $currentUser = AuthService::getCurrentUser();
        if ($currentUser['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        try {
            // Get moderator details
            $moderator = new Moderator();
            $moderatorData = $moderator->find($currentUser['id']);

            $data = [
                'title' => 'Comments Moderation',
                'page' => 'comments_moderation',
                'moderator' => $moderatorData,
                'user' => $currentUser,
                'page_title' => 'Comments Moderation'
            ];

            parent::view('Moderator/comments_moderation', $data);

        } catch (Exception $e) {
            error_log("Error loading comments moderation page: " . $e->getMessage());
            
            $data = [
                'title' => 'Comments Moderation',
                'page' => 'comments_moderation',
                'error' => 'Unable to load comments data'
            ];

            parent::view('Moderator/comments_moderation', $data);
        }
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
            
            // Get all comments for moderation (includes hidden ones)
            $comments = $this->commentModel->getAllCommentsForModeration($moderator->university);
            
            // Handle case where query returns false (no results)
            if ($comments === false) {
                $comments = [];
            }
            
            // Calculate statistics
            $totalComments = count($comments);
            $hiddenCount = 0;
            $visibleCount = 0;
            $todayHiddenCount = 0;
            $today = date('Y-m-d');
            
            $formattedComments = [];
            foreach ($comments as $comment) {
                if ($comment->is_hidden) {
                    $hiddenCount++;
                    // Check if hidden today
                    if ($comment->hidden_at && strpos($comment->hidden_at, $today) === 0) {
                        $todayHiddenCount++;
                    }
                } else {
                    $visibleCount++;
                }
                
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
                    'is_hidden' => (bool)$comment->is_hidden,
                    'hidden_by_name' => $comment->hidden_by_name ?? null,
                    'hidden_at' => $comment->hidden_at ?? null,
                    'hidden_reason' => $comment->hidden_reason ?? null,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'formatted_date' => $this->formatDate($comment->created_at)
                ];
            }
            
            echo json_encode([
                'success' => true,
                'comments' => $formattedComments,
                'university' => $moderator->university,
                'total' => $totalComments,
                'stats' => [
                    'total_comments' => $totalComments,
                    'visible_comments' => $visibleCount,
                    'hidden_comments' => $hiddenCount,
                    'moderated_today' => $todayHiddenCount
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting moderator comments: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments', 'debug' => $e->getMessage()]);
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

            // Accept event_id from query string when not passed as URL path segment
            if (!$eventId) {
                $eventId = $_GET['event_id'] ?? $_GET['id'] ?? null;
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
            
            // Get comments for the event (all, including hidden — moderator view)
            $comments = $this->commentModel->getEventCommentsForModerator($eventId);
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
                    'is_hidden' => (bool)$comment->is_hidden,
                    'hidden_by_name' => $comment->hidden_by_name ?? null,
                    'hidden_at' => $comment->hidden_at ?? null,
                    'hidden_reason' => $comment->hidden_reason ?? null,
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
    
    /**
     * Hide a comment (AJAX endpoint)
     */
    public function hideComment() {
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
            
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['comment_id']) || !isset($input['reason'])) {
                echo json_encode(['success' => false, 'error' => 'Comment ID and reason are required']);
                return;
            }
            
            $commentId = (int)$input['comment_id'];
            $reason = trim($input['reason']);
            
            // Validate reason length
            if (strlen($reason) < 10) {
                echo json_encode(['success' => false, 'error' => 'Reason must be at least 10 characters long']);
                return;
            }
            
            if (strlen($reason) > 500) {
                echo json_encode(['success' => false, 'error' => 'Reason must not exceed 500 characters']);
                return;
            }
            
            // Get comment to verify it belongs to moderator's university
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                echo json_encode(['success' => false, 'error' => 'Comment not found']);
                return;
            }
            
            // Get moderator's university
            $modQuery = "SELECT university FROM moderators WHERE id = :mod_id";
            $modStmt = $this->connect()->prepare($modQuery);
            $modStmt->execute(['mod_id' => $currentUser['id']]);
            $moderator = $modStmt->fetch(PDO::FETCH_OBJ);
            
            // Get event details and verify the publisher belongs to moderator's university
            $eventQuery = "
                SELECT e.*, p.university AS publisher_university
                FROM events e
                LEFT JOIN publishers p ON e.created_by_type = 'publisher' AND e.created_by = p.id
                WHERE e.id = :event_id
            ";
            $eventStmt = $this->connect()->prepare($eventQuery);
            $eventStmt->execute(['event_id' => $comment->event_id]);
            $event = $eventStmt->fetch(PDO::FETCH_OBJ);

            if (!$event || $event->publisher_university !== $moderator->university) {
                echo json_encode(['success' => false, 'error' => 'You can only moderate comments from your university']);
                return;
            }
            
            // Hide the comment
            $result = $this->commentModel->hideComment($commentId, $currentUser['id'], $reason);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error hiding comment: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to hide comment']);
        }
    }
    
    /**
     * Unhide a comment (AJAX endpoint)
     */
    public function unhideComment() {
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
            
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['comment_id'])) {
                echo json_encode(['success' => false, 'error' => 'Comment ID is required']);
                return;
            }
            
            $commentId = (int)$input['comment_id'];
            
            // Get comment to verify it belongs to moderator's university
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                echo json_encode(['success' => false, 'error' => 'Comment not found']);
                return;
            }
            
            // Get moderator's university
            $modQuery = "SELECT university FROM moderators WHERE id = :mod_id";
            $modStmt = $this->connect()->prepare($modQuery);
            $modStmt->execute(['mod_id' => $currentUser['id']]);
            $moderator = $modStmt->fetch(PDO::FETCH_OBJ);
            
            // Get event details and verify the publisher belongs to moderator's university
            $eventQuery = "
                SELECT e.*, p.university AS publisher_university
                FROM events e
                LEFT JOIN publishers p ON e.created_by_type = 'publisher' AND e.created_by = p.id
                WHERE e.id = :event_id
            ";
            $eventStmt = $this->connect()->prepare($eventQuery);
            $eventStmt->execute(['event_id' => $comment->event_id]);
            $event = $eventStmt->fetch(PDO::FETCH_OBJ);

            if (!$event || $event->publisher_university !== $moderator->university) {
                echo json_encode(['success' => false, 'error' => 'You can only moderate comments from your university']);
                return;
            }
            
            // Unhide the comment
            $result = $this->commentModel->unhideComment($commentId, $currentUser['id']);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error unhiding comment: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to unhide comment']);
        }
    }
}