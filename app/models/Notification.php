<?php

class Notification
{

    use Model;

    protected $table = 'notifications';

    protected $allowedColumns = [
        'recipient_id',
        'recipient_type',
        'type',
        'title',
        'message',
        'related_id',
        'related_type',
        'is_read'
    ];

    /**
     * Send a notification
     */
    public function sendNotification($data)
    {
        // Validate required fields
        if (
            empty($data['recipient_id']) || empty($data['recipient_type']) ||
            empty($data['type']) || empty($data['title']) || empty($data['message'])
        ) {
            return false;
        }

        return $this->insert($data);
    }

    /**
     * Get notifications for a user
     */
    public function getUserNotifications($recipientId, $recipientType, $limit = 50)
    {
        $query = "
            SELECT * FROM notifications 
            WHERE recipient_id = :recipient_id 
            AND recipient_type = :recipient_type 
            ORDER BY created_at DESC 
            LIMIT :limit
        ";

        return $this->query($query, [
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType,
            'limit' => $limit
        ]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount($recipientId, $recipientType)
    {
        $query = "
            SELECT COUNT(*) as count 
            FROM notifications 
            WHERE recipient_id = :recipient_id 
            AND recipient_type = :recipient_type 
            AND is_read = 0
        ";

        $result = $this->first($query, [
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType
        ]);

        return $result ? $result->count : 0;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $recipientId, $recipientType)
    {
        $query = "
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = :notification_id 
            AND recipient_id = :recipient_id 
            AND recipient_type = :recipient_type
        ";

        return $this->query($query, [
            'notification_id' => $notificationId,
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($recipientId, $recipientType)
    {
        $query = "
            UPDATE notifications 
            SET is_read = 1 
            WHERE recipient_id = :recipient_id 
            AND recipient_type = :recipient_type
        ";

        return $this->query($query, [
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType
        ]);
    }

    /**
     * Delete old notifications (cleanup)
     */
    public function deleteOldNotifications($days = 30)
    {
        $query = "
            DELETE FROM notifications 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
        ";

        return $this->query($query, ['days' => $days]);
    }
}
