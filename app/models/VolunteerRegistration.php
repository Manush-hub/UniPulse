<?php

class VolunteerRegistration
{

    use Model;

    protected $table = 'volunteer_registrations';
    protected $allowedColumns = [
        'user_id',
        'user_type',
        'event_id',
        'volunteer_position',
        'availability',
        'experience',
        'motivation',
        'skills',
        'have_transportation',
        'commitment_understanding',
        'receive_updates',
        'terms_accepted',
        'status'
    ];

    /**
     * Check if user has already registered as volunteer for an event
     */
    public function isUserRegistered($eventId, $userId, $userType)
    {
        $sql = "SELECT id FROM {$this->table} 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                AND status != 'withdrawn'
                LIMIT 1";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        return !empty($result);
    }

    /**
     * Get volunteer registration
     */
    public function getRegistration($eventId, $userId, $userType)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                LIMIT 1";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        return $result ? $result[0] : null;
    }

    /**
     * Get all volunteers for an event
     */
    public function getEventVolunteers($eventId, $status = 'pending')
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_id = :event_id";

        $params = ['event_id' => $eventId];

        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->query($sql, $params) ?: [];
    }

    /**
     * Get volunteer count for an event
     */
    public function getVolunteerCount($eventId, $status = 'pending')
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE event_id = :event_id 
                AND status = :status";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'status' => $status
        ]);

        return $result ? $result[0]->count : 0;
    }

    /**
     * Update volunteer status
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} 
                SET status = :status 
                WHERE id = :id";

        return $this->query($sql, [
            'id' => $id,
            'status' => $status
        ]);
    }
}
