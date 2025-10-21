<?php

class Comment {
    
    use Model;
    
    protected $table = 'event_comments';
    
    /**
     * Get comment by ID with user information
     */
    public function getCommentById($commentId) {
        $query = "
            SELECT c.*, 
                CASE 
                    WHEN c.user_type = 'university' THEN uu.full_name
                    WHEN c.user_type = 'public' THEN pu.full_name
                    WHEN c.user_type = 'publisher' THEN p.society_name
                    WHEN c.user_type = 'sponsor' THEN s.company_name
                END as user_name
            FROM event_comments c
            LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
            LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
            LEFT JOIN publishers p ON c.user_type = 'publisher' AND c.user_id = p.id
            LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
            WHERE c.id = :comment_id 
            AND c.is_deleted = 0
        ";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['comment_id' => $commentId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    protected $allowedColumns = [
        'event_id',
        'user_id',
        'user_type',
        'user_table',
        'comment_text',
        'rating',
        'is_edited',
        'is_deleted'
    ];
    
    /**
     * Get all active comments for an event
     */
    public function getEventComments($eventId) {
        $query = "
            SELECT 
                c.*,
                CASE 
                    WHEN c.user_type = 'university' THEN uu.full_name
                    WHEN c.user_type = 'public' THEN pu.full_name
                    WHEN c.user_type = 'publisher' THEN p.society_name
                    WHEN c.user_type = 'sponsor' THEN s.company_name
                END as user_name,
                CASE 
                    WHEN c.user_type = 'university' THEN uu.email
                    WHEN c.user_type = 'public' THEN pu.email
                    WHEN c.user_type = 'publisher' THEN p.email
                    WHEN c.user_type = 'sponsor' THEN s.email
                END as user_email
            FROM event_comments c
            LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
            LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
            LEFT JOIN publishers p ON c.user_type = 'publisher' AND c.user_id = p.id
            LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
            WHERE c.event_id = :event_id 
            AND c.is_deleted = 0
            ORDER BY c.created_at DESC
        ";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Add a new comment
     */
    public function addComment($data) {
        // Validate required fields
        $errors = [];
        
        if (empty($data['event_id'])) {
            $errors[] = 'Event ID is required';
        }
        
        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        }
        
        if (empty($data['user_type'])) {
            $errors[] = 'User type is required';
        }
        
        if (empty($data['comment_text'])) {
            $errors[] = 'Comment text is required';
        } elseif (strlen(trim($data['comment_text'])) < 5) {
            $errors[] = 'Comment must be at least 5 characters long';
        }
        
        if (!empty($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            $errors[] = 'Rating must be between 1 and 5';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if event exists and is completed
        $event = $this->getEventForComment($data['event_id']);
        if (!$event) {
            return ['success' => false, 'errors' => ['Event not found']];
        }
        
        if ($event->status !== 'completed') {
            return ['success' => false, 'errors' => ['Comments can only be added to completed events']];
        }
        
        // Set user table based on user type
        $userTableMap = [
            'university' => 'university_users',
            'public' => 'public_users',
            'publisher' => 'publishers',
            'sponsor' => 'sponsors'
        ];
        $data['user_table'] = $userTableMap[$data['user_type']];
        
        // Clean comment text
        $data['comment_text'] = trim(strip_tags($data['comment_text']));
        
        // Insert comment
        $result = $this->insert($data);
        
        if ($result) {
            // Send notification to event publisher
            $this->sendCommentNotification($event, $data);
            
            return ['success' => true, 'comment_id' => $result];
        }
        
        return ['success' => false, 'errors' => ['Failed to add comment']];
    }
    
    /**
     * Update a comment
     */
    public function updateComment($commentId, $data, $userId, $userType) {
        // Get existing comment
        $comment = $this->getCommentById($commentId);
        
        if (!$comment) {
            return ['success' => false, 'errors' => ['Comment not found']];
        }
        
        // Check if user owns this comment
        if ($comment->user_id != $userId || $comment->user_type != $userType) {
            return ['success' => false, 'errors' => ['You can only edit your own comments']];
        }
        
        // Validate comment text
        if (empty($data['comment_text'])) {
            return ['success' => false, 'errors' => ['Comment text is required']];
        } elseif (strlen(trim($data['comment_text'])) < 5) {
            return ['success' => false, 'errors' => ['Comment must be at least 5 characters long']];
        }
        
        // Validate rating if provided
        if (isset($data['rating']) && !empty($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            return ['success' => false, 'errors' => ['Rating must be between 1 and 5']];
        }
        
        // Update data
        $updateData = [
            'comment_text' => trim(strip_tags($data['comment_text'])),
            'is_edited' => 1
        ];
        
        if (isset($data['rating'])) {
            $updateData['rating'] = $data['rating'];
        }
        
        $result = $this->update($commentId, $updateData);
        
        if ($result) {
            return ['success' => true];
        }
        
        return ['success' => false, 'errors' => ['Failed to update comment']];
    }
    
    /**
     * Delete a comment (hard delete for completed events, soft delete for others)
     */
    public function deleteComment($commentId, $userId, $userType) {
        // Get existing comment
        $comment = $this->getCommentById($commentId);
        
        if (!$comment) {
            return ['success' => false, 'errors' => ['Comment not found']];
        }
        
        // Check if user owns this comment
        if ($comment->user_id != $userId || $comment->user_type != $userType) {
            return ['success' => false, 'errors' => ['You can only delete your own comments']];
        }
        
        // Get event details to check status
        $event = $this->getEventForComment($comment->event_id);
        
        if (!$event) {
            return ['success' => false, 'errors' => ['Event not found']];
        }
        
        // For completed events, perform hard delete (actually remove from database)
        if ($event->status === 'completed') {
            $query = "DELETE FROM event_comments WHERE id = :comment_id";
            $stmt = $this->connect()->prepare($query);
            $result = $stmt->execute(['comment_id' => $commentId]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Comment permanently deleted'];
            }
            
            return ['success' => false, 'errors' => ['Failed to delete comment']];
        } else {
            // For non-completed events, use soft delete
            $result = $this->update($commentId, [
                'is_deleted' => 1,
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Comment deleted'];
            }
            
            return ['success' => false, 'errors' => ['Failed to delete comment']];
        }
    }
    
    /**
     * Get event details for comment validation
     */
    private function getEventForComment($eventId) {
        $query = "
            SELECT id, title, status, created_by, created_by_type 
            FROM events 
            WHERE id = :event_id
        ";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Send notification to event publisher about new comment
     */
    private function sendCommentNotification($event, $commentData) {
        if ($event->created_by_type !== 'publisher') {
            return; // Only send notifications for publisher events
        }
        
        $notification = new Notification();
        $notification->sendNotification([
            'recipient_id' => $event->created_by,
            'recipient_type' => 'publisher',
            'type' => 'new_comment',
            'title' => 'New Comment on Your Event',
            'message' => "Someone commented on your event '{$event->title}'",
            'related_id' => $event->id,
            'related_type' => 'event'
        ]);
    }
    
    /**
     * Get comment statistics for an event
     */
    public function getEventCommentStats($eventId) {
        $query = "
            SELECT 
                COUNT(*) as total_comments,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating IS NOT NULL THEN 1 END) as rated_comments
            FROM event_comments 
            WHERE event_id = :event_id 
            AND is_deleted = 0
        ";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Check if user has already commented on an event
     */
    public function hasUserCommented($eventId, $userId, $userType) {
        $query = "
            SELECT id 
            FROM event_comments 
            WHERE event_id = :event_id 
            AND user_id = :user_id 
            AND user_type = :user_type 
            AND is_deleted = 0
        ";
        
        $result = $this->first($query, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        return $result !== false;
    }
    
    /**
     * Get comments by publisher (for publisher dashboard)
     */
    public function getCommentsForPublisher($publisherId) {
        $query = "
            SELECT 
                c.*,
                e.title as event_title,
                e.event_date,
                CASE 
                    WHEN c.user_type = 'university' THEN uu.full_name
                    WHEN c.user_type = 'public' THEN pu.full_name
                    WHEN c.user_type = 'publisher' THEN p.society_name
                    WHEN c.user_type = 'sponsor' THEN s.company_name
                END as user_name
            FROM event_comments c
            JOIN events e ON c.event_id = e.id
            LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
            LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
            LEFT JOIN publishers p ON c.user_type = 'publisher' AND c.user_id = p.id
            LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
            WHERE e.created_by = :publisher_id 
            AND e.created_by_type = 'publisher'
            AND c.is_deleted = 0
            ORDER BY c.created_at DESC
        ";
        
        return $this->query($query, ['publisher_id' => $publisherId]);
    }
}