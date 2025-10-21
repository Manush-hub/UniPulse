<?php

class EventRegistration {
    
    use Model;
    
    protected $table = 'event_registrations';
    protected $allowedColumns = [
        'event_id',
        'user_id',
        'user_type',
        'registration_type',
        'status',
        'notes',
        'payment_id',
        'amount_paid',
        'cancelled_at'
    ];
    
    /**
     * Check if user is already registered for an event
     */
    public function isUserRegistered($eventId, $userId, $userType) {
        $sql = "SELECT id FROM {$this->table} 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                AND status != 'cancelled'
                LIMIT 1";
        
        $result = $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        return !empty($result);
    }
    
    /**
     * Register user for an event
     */
    public function registerUser($data) {
        // Check if already registered
        if ($this->isUserRegistered($data['event_id'], $data['user_id'], $data['user_type'])) {
            return false; // Already registered
        }
        
        // Set defaults
        $data['registration_type'] = $data['registration_type'] ?? 'free';
        $data['status'] = 'registered';
        $data['amount_paid'] = $data['amount_paid'] ?? 0.00;
        
        return $this->insert($data);
    }
    
    /**
     * Cancel registration
     */
    public function cancelRegistration($eventId, $userId, $userType) {
        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled', 
                    cancelled_at = NOW() 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                AND status = 'registered'";
        
        return $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
    }
    
    /**
     * Get user's registration for an event
     */
    public function getUserRegistration($eventId, $userId, $userType) {
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
     * Get all registrations for an event
     */
    public function getEventRegistrations($eventId, $status = 'registered') {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_id = :event_id";
        
        $params = ['event_id' => $eventId];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY registered_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get registration count for an event
     */
    public function getRegistrationCount($eventId, $status = 'registered') {
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
     * Get user's registered events
     */
    public function getUserRegisteredEvents($userId, $userType, $status = 'registered') {
        $sql = "SELECT e.*, er.registered_at, er.status as registration_status 
                FROM {$this->table} er
                INNER JOIN events e ON er.event_id = e.id
                WHERE er.user_id = :user_id 
                AND er.user_type = :user_type";
        
        $params = [
            'user_id' => $userId,
            'user_type' => $userType
        ];
        
        if ($status) {
            $sql .= " AND er.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY e.event_date ASC, e.event_time ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Mark registration as attended
     */
    public function markAsAttended($eventId, $userId, $userType) {
        $sql = "UPDATE {$this->table} 
                SET status = 'attended' 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                AND status = 'registered'";
        
        return $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
    }
}
?>
