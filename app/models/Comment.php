<?php

class Comment
{

    use Model;

    protected $table = 'event_comments';

    /**
     * Get comment by ID with user information
     */
    public function getCommentById($commentId)
    {
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
        'is_deleted',
        'is_hidden',
        'hidden_by',
        'hidden_at',
        'hidden_reason'
    ];

    /**
     * Get all active comments for an event
     */
    public function getEventComments($eventId)
    {
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
            AND c.is_hidden = 0
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Add a new comment
     */
    public function addComment($data)
    {
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

        // Check if event exists
        $event = $this->getEventForComment($data['event_id']);
        if (!$event) {
            return ['success' => false, 'errors' => ['Event not found']];
        }

        $eventDate = !empty($event->event_date) ? new DateTime($event->event_date) : null;
        $today = new DateTime('today');
        $now   = new DateTime();
        $status = strtolower(trim((string)($event->status ?? '')));

        $isCompleted = ($status === 'completed') || ($eventDate && $eventDate < $today);

        // Also treat as completed if event date is today and end time (or start time) has passed
        if (!$isCompleted && $eventDate && $eventDate == $today) {
            $endTimeStr   = !empty($event->event_end_time) ? $event->event_end_time : null;
            $startTimeStr = !empty($event->event_time)     ? $event->event_time     : null;
            if ($endTimeStr) {
                $endDt = new DateTime($event->event_date . ' ' . $endTimeStr);
                $isCompleted = $now >= $endDt;
            } elseif ($startTimeStr) {
                $startDt = new DateTime($event->event_date . ' ' . $startTimeStr);
                $isCompleted = $now >= $startDt;
            }
        }

        if (!$isCompleted) {
            return ['success' => false, 'errors' => ['Comments are allowed only for completed events']];
        }

        // Set user table based on user type
        $userTableMap = [
            'university' => 'university_users',
            'public' => 'public_users',
            'publisher' => 'publishers',
            'sponsor' => 'sponsors'
        ];

        if (!isset($userTableMap[$data['user_type']])) {
            return ['success' => false, 'errors' => ['Invalid user type for commenting']];
        }

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
    public function updateComment($commentId, $data, $userId, $userType)
    {
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
    public function deleteComment($commentId, $userId, $userType)
    {
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
    private function getEventForComment($eventId)
    {
        $query = "
            SELECT id, title, status, event_date, event_time, event_end_time, created_by, created_by_type 
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
    private function sendCommentNotification($event, $commentData)
    {
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
    public function getEventCommentStats($eventId)
    {
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
    public function hasUserCommented($eventId, $userId, $userType)
    {
        $query = "
            SELECT id 
            FROM event_comments 
            WHERE event_id = :event_id 
            AND user_id = :user_id 
            AND user_type = :user_type 
            AND is_deleted = 0
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result !== false;
    }

    /**
     * Get comments by publisher (for publisher dashboard)
     */
    public function getCommentsForPublisher($publisherId)
    {
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

    /**
     * Hide a comment (Moderator action)
     */
    public function hideComment($commentId, $moderatorId, $reason)
    {
        // Validate inputs
        if (empty($commentId) || empty($moderatorId) || empty($reason)) {
            return ['success' => false, 'errors' => ['Invalid parameters']];
        }

        if (strlen(trim($reason)) < 10) {
            return ['success' => false, 'errors' => ['Reason must be at least 10 characters long']];
        }

        // Get comment details
        $comment = $this->getCommentById($commentId);
        if (!$comment) {
            return ['success' => false, 'errors' => ['Comment not found']];
        }

        // Check if already hidden
        if ($comment->is_hidden) {
            return ['success' => false, 'errors' => ['Comment is already hidden']];
        }

        // Update comment to hidden
        $result = $this->update($commentId, [
            'is_hidden' => 1,
            'hidden_by' => $moderatorId,
            'hidden_at' => date('Y-m-d H:i:s'),
            'hidden_reason' => trim($reason)
        ]);

        if ($result) {
            // Get event details for notification
            $event = $this->getEventForComment($comment->event_id);

            // Send notification to comment author
            $this->sendHiddenNotification($comment, $reason, $event);

            return ['success' => true, 'message' => 'Comment hidden successfully'];
        }

        return ['success' => false, 'errors' => ['Failed to hide comment']];
    }

    /**
     * Unhide a comment (Moderator action)
     */
    public function unhideComment($commentId, $moderatorId)
    {
        // Get comment details
        $comment = $this->getCommentById($commentId);
        if (!$comment) {
            return ['success' => false, 'errors' => ['Comment not found']];
        }

        // Check if actually hidden
        if (!$comment->is_hidden) {
            return ['success' => false, 'errors' => ['Comment is not hidden']];
        }

        // Update comment to unhidden
        $result = $this->update($commentId, [
            'is_hidden' => 0,
            'hidden_by' => null,
            'hidden_at' => null,
            'hidden_reason' => null
        ]);

        if ($result) {
            // Get event details for notification
            $event = $this->getEventForComment($comment->event_id);

            // Send notification to comment author
            $this->sendUnhiddenNotification($comment, $event);

            return ['success' => true, 'message' => 'Comment unhidden successfully'];
        }

        return ['success' => false, 'errors' => ['Failed to unhide comment']];
    }

    /**
     * Send notification when comment is hidden
     */
    private function sendHiddenNotification($comment, $reason, $event)
    {
        // Determine user table name for fetching email
        $userTableMap = [
            'university' => 'university_users',
            'public' => 'public_users',
            'publisher' => 'publishers',
            'sponsor' => 'sponsors'
        ];

        $notification = new Notification();
        $notification->sendNotification([
            'recipient_id' => $comment->user_id,
            'recipient_type' => $comment->user_type,
            'type' => 'comment_hidden',
            'title' => 'Your Comment Has Been Hidden',
            'message' => "Your comment on '{$event->title}' has been hidden by a moderator. Reason: {$reason}",
            'related_id' => $comment->id,
            'related_type' => 'comment'
        ]);
    }

    /**
     * Send notification when comment is unhidden
     */
    private function sendUnhiddenNotification($comment, $event)
    {
        $notification = new Notification();
        $notification->sendNotification([
            'recipient_id' => $comment->user_id,
            'recipient_type' => $comment->user_type,
            'type' => 'comment_unhidden',
            'title' => 'Your Comment Is Now Visible',
            'message' => "Your comment on '{$event->title}' has been restored and is now visible again.",
            'related_id' => $comment->id,
            'related_type' => 'comment'
        ]);
    }

    /**
     * Get all comments posted by a specific user (for their own dashboard)
     */
    public function getCommentsByUser($userId, $userType)
    {
        $query = "
            SELECT
                c.id,
                c.event_id,
                c.comment_text,
                c.rating,
                c.is_edited,
                c.is_hidden,
                c.hidden_reason,
                c.hidden_at,
                c.created_at,
                c.updated_at,
                e.title  AS event_title,
                e.event_date,
                m.full_name AS hidden_by_name
            FROM event_comments c
            JOIN events e ON c.event_id = e.id
            LEFT JOIN moderators m ON c.hidden_by = m.id
            WHERE c.user_id   = :user_id
              AND c.user_type = :user_type
              AND c.is_deleted = 0
            ORDER BY c.created_at DESC
            LIMIT 100
        ";

        return $this->query($query, [
            'user_id'   => $userId,
            'user_type' => $userType,
        ]);
    }

    /**
     * Get all comments for moderation (includes hidden ones)
     */
    /**
     * Get all comments for a specific event (moderator view - includes hidden comments)
     */
    public function getEventCommentsForModerator($eventId)
    {
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
                END as user_email,
                m.full_name as hidden_by_name
            FROM event_comments c
            LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
            LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
            LEFT JOIN publishers p ON c.user_type = 'publisher' AND c.user_id = p.id
            LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
            LEFT JOIN moderators m ON c.hidden_by = m.id
            WHERE c.event_id = :event_id 
            AND c.is_deleted = 0
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllCommentsForModeration($university = null)
    {
        $query = "
            SELECT 
                c.*,
                e.title as event_title,
                e.status as event_status,
                e.university as event_university,
                COALESCE(p.society_name, s.company_name, 'System') as publisher_name,
                CASE 
                    WHEN c.user_type = 'university' THEN uu.full_name
                    WHEN c.user_type = 'public' THEN pu.full_name
                    WHEN c.user_type = 'publisher' THEN pub.society_name
                    WHEN c.user_type = 'sponsor' THEN sp.company_name
                END as user_name,
                CASE 
                    WHEN c.user_type = 'university' THEN uu.email
                    WHEN c.user_type = 'public' THEN pu.email
                    WHEN c.user_type = 'publisher' THEN pub.email
                    WHEN c.user_type = 'sponsor' THEN sp.email
                END as user_email,
                m.full_name as hidden_by_name
            FROM event_comments c
            INNER JOIN events e ON c.event_id = e.id
            LEFT JOIN publishers p ON e.created_by_type = 'publisher' AND e.created_by = p.id
            LEFT JOIN sponsors s ON e.created_by_type = 'sponsor' AND e.created_by = s.id
            LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
            LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
            LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
            LEFT JOIN sponsors sp ON c.user_type = 'sponsor' AND c.user_id = sp.id
            LEFT JOIN moderators m ON c.hidden_by = m.id
            WHERE c.is_deleted = 0
        ";

        $params = [];

        if ($university) {
            // Filter by the publisher's university (events are owned by publishers;
            // e.university may differ from the publisher's own university slug)
            $query .= " AND p.university = :university";
            $params['university'] = $university;
        }

        $query .= " ORDER BY c.created_at DESC";

        if (empty($params)) {
            return $this->query($query);
        }

        return $this->query($query, $params);
    }
}
