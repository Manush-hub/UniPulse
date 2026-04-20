<?php

class UserComments extends Controller
{

    use Database; // Add database access

    private $commentModel;
    private $eventModel;
    private $registrationModel;
    private $freeRegistrationModel;
    private $paidRegistrationModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
        $this->eventModel = new Event();
        $this->registrationModel = new EventRegistration();
        $this->freeRegistrationModel = new FreeEventRegistration();
        $this->paidRegistrationModel = new PaidEventRegistration();
    }

    /**
     * Get comments for an event (AJAX endpoint)
     */
    public function getComments($eventId = null)
    {
        header('Content-Type: application/json');

        if (!$eventId && isset($_GET['event_id'])) {
            $eventId = $_GET['event_id'];
        }

        if (!$eventId || !is_numeric($eventId)) {
            echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
            return;
        }

        try {
            // Get comments
            $comments = $this->commentModel->getEventComments($eventId);

            // Get comment statistics
            $stats = $this->commentModel->getEventCommentStats($eventId);

            // Format comments for display
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'user_name' => $comment->user_name,
                    'profile_photo' => $comment->profile_photo ?? null,
                    'user_type' => $comment->user_type,
                    'comment_text' => $comment->comment_text,
                    'rating' => $comment->rating,
                    'is_edited' => (bool)$comment->is_edited,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'formatted_date' => $this->formatDate($comment->created_at),
                    'can_edit' => $this->canUserEditComment($comment),
                    'can_delete' => $this->canUserDeleteComment($comment)
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
            error_log("Error getting comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }

    /**
     * Add a new comment (AJAX endpoint)
     */
    public function addComment()
    {
        header('Content-Type: application/json');

        // Start session explicitly
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Debug logging
        error_log("=== addComment Debug ===");
        error_log("Session ID: " . session_id());
        error_log("Session Data: " . print_r($_SESSION, true));
        error_log("Is Logged In: " . (AuthService::isLoggedIn() ? 'YES' : 'NO'));

        // Get POST data first to check for user info
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'No data provided']);
            return;
        }

        // Check if user is logged in
        if (!AuthService::isLoggedIn()) {
            error_log("User not logged in during addComment");

            // Fallback authentication using provided username
            if (isset($input['fallback_user_name']) && !empty($input['fallback_user_name'])) {
                $userName = $input['fallback_user_name'];
                error_log("Attempting fallback authentication for user: " . $userName);

                // Try to find user in different user tables
                $fallbackUser = $this->findUserByName($userName);
                if ($fallbackUser) {
                    error_log("Fallback user found: " . print_r($fallbackUser, true));
                    // Use this user for the comment
                    $currentUser = $fallbackUser;
                } else {
                    error_log("Fallback user not found for name: " . $userName);
                    echo json_encode(['success' => false, 'error' => 'Authentication failed. Please try refreshing the page and logging in again.', 'debug' => 'fallback_auth_failed']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Session authentication failed. Please try refreshing the page and logging in again.', 'debug' => 'auth_failed']);
                return;
            }
        } else {
            // Get current user normally
            $currentUser = AuthService::getCurrentUser();
        }

        try {
            // Prepare comment data
            $eventId = $input['event_id'] ?? null;
            if (!$eventId || !is_numeric($eventId)) {
                echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
                return;
            }

            $event = $this->eventModel->getEventById($eventId);
            if (!$event || !$this->isCompletedEvent($event)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Comments are allowed only for completed events'
                ]);
                return;
            }

            $restrictionMessage = '';
            if (!$this->canUserCommentOnCompletedEvent($eventId, $currentUser['id'], $currentUser['type'], $restrictionMessage)) {
                echo json_encode([
                    'success' => false,
                    'error' => $restrictionMessage ?: 'Comments are open only for users who registered for this event.'
                ]);
                return;
            }

            $commentData = [
                'event_id' => $eventId,
                'user_id' => $currentUser['id'],
                'user_type' => $currentUser['type'],
                'comment_text' => $input['comment_text'] ?? '',
                'rating' => !empty($input['rating']) ? (int)$input['rating'] : null
            ];

            // Add comment
            $result = $this->commentModel->addComment($commentData);

            if ($result['success']) {
                // Get the new comment details
                $newComment = $this->commentModel->getCommentById($result['comment_id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Comment added successfully',
                    'comment' => [
                        'id' => $newComment->id,
                        'user_name' => $newComment->user_name,
                        'profile_photo' => $newComment->profile_photo ?? null,
                        'user_type' => $newComment->user_type,
                        'comment_text' => $newComment->comment_text,
                        'rating' => $newComment->rating,
                        'is_edited' => false,
                        'created_at' => $newComment->created_at,
                        'formatted_date' => $this->formatDate($newComment->created_at),
                        'can_edit' => true,
                        'can_delete' => true
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $result['errors'])
                ]);
            }
        } catch (Exception $e) {
            error_log("Error adding comment: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to add comment']);
        }
    }

    /**
     * Update a comment (AJAX endpoint)
     */
    public function updateComment($commentId = null)
    {
        header('Content-Type: application/json');

        // Check if user is logged in
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'You must be logged in to edit comments']);
            return;
        }

        if (!$commentId) {
            echo json_encode(['success' => false, 'error' => 'Comment ID is required']);
            return;
        }

        // Get PUT/POST data
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        try {
            // Get current user
            $currentUser = AuthService::getCurrentUser();

            // Update comment
            $result = $this->commentModel->updateComment(
                $commentId,
                $input,
                $currentUser['id'],
                $currentUser['type']
            );

            if ($result['success']) {
                // Get updated comment
                $updatedComment = $this->commentModel->getCommentById($commentId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Comment updated successfully',
                    'comment' => [
                        'id' => $updatedComment->id,
                        'user_name' => $updatedComment->user_name,
                        'profile_photo' => $updatedComment->profile_photo ?? null,
                        'user_type' => $updatedComment->user_type,
                        'comment_text' => $updatedComment->comment_text,
                        'rating' => $updatedComment->rating,
                        'is_edited' => true,
                        'updated_at' => $updatedComment->updated_at,
                        'formatted_date' => $this->formatDate($updatedComment->updated_at)
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $result['errors'])
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating comment: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to update comment']);
        }
    }

    /**
     * Delete a comment (AJAX endpoint)
     */
    public function deleteComment($commentId = null)
    {
        header('Content-Type: application/json');

        // Check if user is logged in
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'You must be logged in to delete comments']);
            return;
        }

        if (!$commentId) {
            echo json_encode(['success' => false, 'error' => 'Comment ID is required']);
            return;
        }

        try {
            // Get current user
            $currentUser = AuthService::getCurrentUser();

            // Delete comment
            $result = $this->commentModel->deleteComment(
                $commentId,
                $currentUser['id'],
                $currentUser['type']
            );

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Comment deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $result['errors'])
                ]);
            }
        } catch (Exception $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to delete comment']);
        }
    }

    /**
     * Find user by display name across all user tables (fallback authentication)
     */
    private function findUserByName($userName)
    {
        $userTables = [
            'public_users' => ['type' => 'public', 'name_col' => 'full_name'],
            'university_users' => ['type' => 'university', 'name_col' => 'full_name'],
            'publishers' => ['type' => 'publisher', 'name_col' => 'society_name'],
            'sponsors' => ['type' => 'sponsor', 'name_col' => 'company_name'],
            'admins' => ['type' => 'admin', 'name_col' => 'full_name'],
            'moderators' => ['type' => 'moderator', 'name_col' => 'full_name']
        ];

        foreach ($userTables as $table => $config) {
            try {
                $nameCol = $config['name_col'];
                $query = "SELECT id, {$nameCol} as name, email FROM {$table} WHERE {$nameCol} = :name LIMIT 1";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute(['name' => $userName]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    return [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'type' => $config['type'],
                        'table' => $table
                    ];
                }
            } catch (Exception $e) {
                error_log("Error searching in table {$table}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    /**
     * Check if user has already commented on an event
     */
    public function checkUserComment($eventId = null)
    {
        header('Content-Type: application/json');

        // Start session explicitly
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Debug logging
        error_log("=== checkUserComment Debug ===");
        error_log("Event ID: " . $eventId);
        error_log("Session ID: " . session_id());
        error_log("Session Status: " . session_status());
        error_log("Session Data: " . print_r($_SESSION, true));
        error_log("Is Logged In: " . (AuthService::isLoggedIn() ? 'YES' : 'NO'));

        if (!AuthService::isLoggedIn()) {
            error_log("User not logged in - Session details:");
            error_log("logged_in key exists: " . (isset($_SESSION['logged_in']) ? 'YES' : 'NO'));
            if (isset($_SESSION['logged_in'])) {
                error_log("logged_in value: " . var_export($_SESSION['logged_in'], true));
            }
            echo json_encode(['has_commented' => false, 'can_comment' => false, 'debug' => 'not_logged_in', 'session_id' => session_id()]);
            return;
        }

        if (!$eventId || !is_numeric($eventId)) {
            error_log("Invalid event ID: " . $eventId);
            echo json_encode(['has_commented' => false, 'can_comment' => false, 'debug' => 'invalid_event_id']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            error_log("Current User: " . print_r($currentUser, true));

            // Check if event is completed
            $event = $this->eventModel->getEventById($eventId);
            error_log("Event found: " . ($event ? 'YES' : 'NO'));
            if ($event) {
                error_log("Event status: " . $event->status);
            }

            if (!$event || !$this->isCompletedEvent($event)) {
                error_log("Event not found or not completed");
                echo json_encode([
                    'has_commented' => false,
                    'can_comment' => false,
                    'debug' => 'event_not_completed',
                    'message' => 'Comments are available only after an event is completed.',
                    'event_status' => $event ? $event->status : 'not_found'
                ]);
                return;
            }

            $restrictionMessage = '';
            if (!$this->canUserCommentOnCompletedEvent($eventId, $currentUser['id'], $currentUser['type'], $restrictionMessage)) {
                echo json_encode([
                    'has_commented' => false,
                    'can_comment' => false,
                    'debug' => 'registration_required',
                    'message' => $restrictionMessage ?: 'Comments are open only for users who registered for this event.'
                ]);
                return;
            }

            $hasCommented = $this->commentModel->hasUserCommented(
                $eventId,
                $currentUser['id'],
                $currentUser['type']
            );

            error_log("Has commented: " . ($hasCommented ? 'YES' : 'NO'));

            // Users can comment multiple times — always allow if event is completed
            echo json_encode([
                'has_commented' => $hasCommented,
                'can_comment' => true,
                'debug' => 'success',
                'message' => null,
                'user_id' => $currentUser['id'],
                'user_type' => $currentUser['type']
            ]);
        } catch (Exception $e) {
            error_log("Error checking user comment: " . $e->getMessage());
            echo json_encode(['has_commented' => false, 'can_comment' => false]);
        }
    }

    private function canUserCommentOnCompletedEvent($eventId, $userId, $userType, &$restrictionMessage = null)
    {
        $normalizedUserType = $this->normalizeUserType($userType);

        if ($this->hasActiveLegacyRegistration($eventId, $userId, $normalizedUserType)) {
            return true;
        }

        if ($this->hasActiveFreeRegistration($eventId, $userId, $normalizedUserType)) {
            return true;
        }

        if ($this->hasActivePaidRegistration($eventId, $userId, $normalizedUserType)) {
            return true;
        }

        $restrictionMessage = 'Comments are open only for users who registered and participated in this completed event.';
        return false;
    }

    private function hasActiveLegacyRegistration($eventId, $userId, $userType)
    {
        try {
            $query = "
                SELECT id
                FROM event_registrations
                WHERE event_id = :event_id
                  AND user_id = :user_id
                  AND user_type = :user_type
                  AND (status IS NULL OR status NOT IN ('cancelled', 'waitlisted', 'no_show'))
                LIMIT 1
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute([
                'event_id' => $eventId,
                'user_id' => $userId,
                'user_type' => $userType
            ]);

            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Legacy registration check failed: ' . $e->getMessage());
            return false;
        }
    }

    private function hasActiveFreeRegistration($eventId, $userId, $userType)
    {
        try {
            $query = "
                SELECT id
                FROM free_event_registrations
                WHERE event_id = :event_id
                  AND registered_user_id = :user_id
                  AND registered_user_type = :user_type
                  AND status IN ('registered', 'checked_in')
                LIMIT 1
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute([
                'event_id' => $eventId,
                'user_id' => $userId,
                'user_type' => $userType
            ]);

            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Free registration check failed: ' . $e->getMessage());
            return false;
        }
    }

    private function hasActivePaidRegistration($eventId, $userId, $userType)
    {
        try {
            $query = "
                SELECT id
                FROM paid_event_registrations
                WHERE event_id = :event_id
                  AND registered_user_id = :user_id
                  AND registered_user_type = :user_type
                  AND registration_status IN ('reserved', 'confirmed', 'checked_in')
                  AND payment_status IN ('paid', 'partially_refunded', 'refunded')
                LIMIT 1
            ";

            $stmt = $this->connect()->prepare($query);
            $stmt->execute([
                'event_id' => $eventId,
                'user_id' => $userId,
                'user_type' => $userType
            ]);

            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Paid registration check failed: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizeUserType($userType)
    {
        $normalized = strtolower(trim((string)$userType));

        $map = [
            'user' => 'public',
            'public_user' => 'public',
            'publicuser' => 'public',
            'student' => 'university',
            'university_user' => 'university',
            'universityuser' => 'university'
        ];

        return $map[$normalized] ?? $normalized;
    }

    /**
     * Format date for display
     */
    private function formatDate($dateString)
    {
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

    /**
     * Check if current user can edit a comment
     */
    private function canUserEditComment($comment)
    {
        if (!AuthService::isLoggedIn()) {
            return false;
        }

        $currentUser = AuthService::getCurrentUser();
        return $comment->user_id == $currentUser['id'] && $comment->user_type == $currentUser['type'];
    }

    /**
     * Check if current user can delete a comment
     */
    private function canUserDeleteComment($comment)
    {
        if (!AuthService::isLoggedIn()) {
            return false;
        }

        $currentUser = AuthService::getCurrentUser();
        return $comment->user_id == $currentUser['id'] && $comment->user_type == $currentUser['type'];
    }

    private function isCompletedEvent($event)
    {
        if (!$event) {
            return false;
        }

        $status = strtolower(trim((string)($event->status ?? '')));
        if ($status === 'completed') {
            return true;
        }

        if (empty($event->event_date)) {
            return false;
        }

        try {
            $eventDate = new DateTime($event->event_date);
            $eventDate->setTime(0, 0, 0);
            $today = new DateTime('today');

            // Strictly past date
            if ($eventDate < $today) {
                return true;
            }

            // Same date: check if end time has passed (or start time has passed if no end time)
            if ($eventDate == $today) {
                $now = new DateTime();
                $endTimeStr   = !empty($event->event_end_time) ? $event->event_end_time : null;
                $startTimeStr = !empty($event->event_time)     ? $event->event_time     : null;

                if ($endTimeStr) {
                    $endDt = new DateTime($event->event_date . ' ' . $endTimeStr);
                    return $now >= $endDt;
                } elseif ($startTimeStr) {
                    $startDt = new DateTime($event->event_date . ' ' . $startTimeStr);
                    return $now >= $startDt;
                }
            }

            return false;
        } catch (Exception $e) {
            error_log("Error parsing event date in isCompletedEvent: " . $e->getMessage());
            return false;
        }
    }
}
