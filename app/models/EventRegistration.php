<?php

class EventRegistration
{

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
        'ticket_id',
        'amount_paid',
        'cancelled_at'
    ];

    /**
     * Check if user is already registered for an event
     */
    public function isUserRegistered($eventId, $userId, $userType)
    {
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
    public function registerUser($data)
    {
        // Check if already registered
        if ($this->isUserRegistered($data['event_id'], $data['user_id'], $data['user_type'])) {
            return false; // Already registered
        }

        // Set defaults
        $data['registration_type'] = $data['registration_type'] ?? 'free';
        $data['status'] = 'registered';
        $data['amount_paid'] = $data['amount_paid'] ?? 0.00;

        $result = $this->insert($data);

        // Log activity if registration was successful
        if ($result) {
            $this->logRegistrationActivity($data);
        }

        return $result;
    }

    /**
     * Log activity when user registers for an event
     */
    private function logRegistrationActivity($data)
    {
        try {
            // Make sure we have all required data
            if (empty($data['user_id']) || empty($data['user_type']) || empty($data['event_id'])) {
                error_log("Activity logging skipped: Missing required data");
                return;
            }

            // Load models if not already loaded
            if (!class_exists('Activity')) {
                require_once __DIR__ . '/Activity.php';
            }
            if (!class_exists('Event')) {
                require_once __DIR__ . '/Event.php';
            }

            $activity = new Activity();
            $event = new Event();
            $eventDetails = $event->getEventById($data['event_id']);

            // Use event details if found, otherwise use defaults
            $eventTitle = null;
            if ($eventDetails && !empty($eventDetails->title)) {
                $eventTitle = $eventDetails->title;
                $title = "Registered for " . substr($eventTitle, 0, 50);
                $description = "You registered for the event \"" . $eventTitle . "\"";
            } else {
                // Fallback when event not found
                $title = "Registered for Event #" . $data['event_id'];
                $description = "You registered for event #" . $data['event_id'];
            }

            // Log the activity
            $result = $activity->logActivity(
                $data['user_id'],
                $data['user_type'],
                'event_registration',
                $title,
                $description,
                'plus',
                $data['event_id'],
                $eventTitle,
                [
                    'registration_type' => $data['registration_type'] ?? 'free',
                    'status' => $data['status'] ?? 'registered',
                    'amount_paid' => $data['amount_paid'] ?? 0
                ]
            );

            if ($result) {
                error_log("Activity logged successfully for user " . $data['user_id'] . " for event " . $data['event_id']);
            } else {
                error_log("Activity logging returned false");
            }
        } catch (Exception $e) {
            error_log("Activity logging exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Cancel registration
     */
    public function cancelRegistration($eventId, $userId, $userType)
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled', 
                    cancelled_at = NOW() 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type 
                AND status = 'registered'";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        // Log activity if cancellation was successful
        if ($result) {
            $this->logCancellationActivity($eventId, $userId, $userType);
        }

        return $result;
    }

    /**
     * Log activity when user cancels event registration
     */
    private function logCancellationActivity($eventId, $userId, $userType)
    {
        try {
            // Make sure we have all required data
            if (empty($userId) || empty($userType) || empty($eventId)) {
                return; // Skip if required data is missing
            }

            @$activity = new Activity(); // Suppress warnings

            // Get event details for the activity log
            @$event = new Event(); // Suppress warnings
            $eventDetails = $event->getEventById($eventId);

            if (!$eventDetails) {
                return; // Skip if event not found
            }

            // Create activity title and description
            $title = "Cancelled registration for " . (!empty($eventDetails->title) ? substr($eventDetails->title, 0, 50) : "Event");
            $description = "You cancelled your registration for the event \"" . (isset($eventDetails->title) ? $eventDetails->title : "Unknown Event") . "\"";

            // Log the activity
            @$activity->logActivity(
                $userId,
                $userType,
                'event_cancellation',
                $title,
                $description,
                'calendar',  // Icon
                $eventId,
                $eventDetails->title ?? null,
                ['cancelled_by' => 'user']
            );
        } catch (Exception $e) {
            // Don't fail the cancellation if activity logging fails
            error_log("Activity logging failed: " . $e->getMessage());
        }
    }

    /**
     * Get user's registration for an event
     */
    public function getUserRegistration($eventId, $userId, $userType)
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
     * Get all registrations for an event
     */
    public function getEventRegistrations($eventId, $status = 'registered')
    {
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
    public function getRegistrationCount($eventId, $status = 'registered')
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
     * Get user's registered events
     */
    public function getUserRegisteredEvents($userId, $userType, $status = 'registered')
    {
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
    public function markAsAttended($eventId, $userId, $userType)
    {
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

    /**
     * Get user's monthly event participation
     * @param int $userId User ID
     * @param string $userType User type (public/university)
     * @param string $month Month in format 'YYYY-MM'
     * @return array Array of event participation records
     */
    public function getUserMonthlyParticipation($userId, $userType, $month)
    {
        $sql = "SELECT er.*, e.title, e.event_date, e.event_time, e.location,
                       e.ticket_type, e.image_url, e.university_name, e.category,
                       er.amount_paid
                FROM {$this->table} er
                LEFT JOIN events e ON er.event_id = e.id
                WHERE er.user_id = :user_id 
                AND er.user_type = :user_type
                AND er.status IN ('registered', 'attended')
                AND DATE_FORMAT(e.event_date, '%Y-%m') = :month
                ORDER BY e.event_date DESC";

        return $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'month' => $month
        ]);
    }

    /**
     * Get total amount spent on events for a specific month
     */
    public function getUserMonthlyEventSpending($userId, $userType, $month)
    {
        $sql = "SELECT COALESCE(SUM(er.amount_paid), 0) as total
                FROM {$this->table} er
                LEFT JOIN events e ON er.event_id = e.id
                WHERE er.user_id = :user_id 
                AND er.user_type = :user_type
                AND er.status IN ('registered', 'attended')
                AND DATE_FORMAT(e.event_date, '%Y-%m') = :month";

        $result = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'month' => $month
        ]);

        return $result[0]->total ?? 0;
    }
}
