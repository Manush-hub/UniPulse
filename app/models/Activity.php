<?php

class Activity
{

    use Model;

    protected $table = 'user_activities';
    protected $allowedColumns = [
        'user_id',
        'user_type',
        'activity_type',
        'event_id',
        'event_title',
        'title',
        'description',
        'icon',
        'activity_data',
        'created_at',
        'expires_at'
    ];

    /**
     * Get recent activities for a user (activities from the last 7 days)
     */
    public function getRecentActivities($userId, $userType, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                AND user_type = :user_type 
                AND (expires_at IS NULL OR expires_at > NOW())
                ORDER BY created_at DESC
                LIMIT :limit";

        $result = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'limit' => $limit
        ]);

        return $result ?: [];
    }

    /**
     * Log an activity for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type (university, public, publisher, sponsor)
     * @param string $activityType Type of activity (event_registration, volunteer_registration, etc.)
     * @param string $title Display title for the activity
     * @param string $description Activity description
     * @param string $icon Icon type (calendar, plus, bell, award)
     * @param int $eventId Event ID (optional)
     * @param string $eventTitle Event title (optional)
     * @param array $additionalData Additional data to store as JSON (optional)
     */
    public function logActivity($userId, $userType, $activityType, $title, $description, $icon = 'calendar', $eventId = null, $eventTitle = null, $additionalData = null)
    {

        // Determine expires_at based on activity type
        // Most activities expire after 7 days (1 week)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        // Some activity types might have different retention periods
        switch ($activityType) {
            case 'event_registration':
            case 'volunteer_registration':
                // Keep these for 1 week (7 days)
                $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
                break;
            case 'badge_earned':
                // Keep badges longer (30 days)
                $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
                break;
            default:
                $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
        }

        $data = [
            'user_id' => $userId,
            'user_type' => $userType,
            'activity_type' => $activityType,
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'event_id' => $eventId,
            'event_title' => $eventTitle,
            'activity_data' => $additionalData ? json_encode($additionalData) : null,
            'expires_at' => $expiresAt
        ];

        return $this->insert($data);
    }

    /**
     * Get activity by ID
     */
    public function getActivity($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        $result = $this->query($sql, ['id' => $id]);

        return $result ? $result[0] : null;
    }

    /**
     * Delete expired activities (cleanup)
     */
    public function deleteExpiredActivities()
    {
        $sql = "DELETE FROM {$this->table} WHERE expires_at IS NOT NULL AND expires_at < NOW()";

        return $this->query($sql, []);
    }

    /**
     * Get activities by type for a user
     */
    public function getActivitiesByType($userId, $userType, $activityType, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                AND user_type = :user_type
                AND activity_type = :activity_type
                AND (expires_at IS NULL OR expires_at > NOW())
                ORDER BY created_at DESC
                LIMIT :limit";

        $result = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'activity_type' => $activityType,
            'limit' => $limit
        ]);

        return $result ?: [];
    }

    /**
     * Format activity for frontend display
     */
    public function formatActivityForDisplay($activity)
    {
        $timeAgo = $this->getTimeAgo($activity->created_at);

        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'icon' => $activity->icon,
            'time' => $timeAgo,
            'timestamp' => $activity->created_at,
            'activityType' => $activity->activity_type,
            'eventId' => $activity->event_id,
            'eventTitle' => $activity->event_title
        ];
    }

    /**
     * Get human-readable time ago
     */
    private function getTimeAgo($timestamp)
    {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return "Just now";
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins == 1 ? "1 minute ago" : "$mins minutes ago";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours == 1 ? "1 hour ago" : "$hours hours ago";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days == 1 ? "1 day ago" : "$days days ago";
        } else {
            return date('M d, Y', $time);
        }
    }
}
