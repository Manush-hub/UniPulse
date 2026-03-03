<?php

class EventRegistration
{

    use Model;

    private $tableColumnsCache = null;
    private $tableIntegrityChecked = false;

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
        $userType = $this->normalizeUserType($userType);

        $columns = $this->getExistingColumns();
        $hasStatusColumn = in_array('status', $columns, true);

        $sql = "SELECT id FROM {$this->table} 
                WHERE event_id = :event_id 
                AND user_id = :user_id 
                AND user_type = :user_type";

        if ($hasStatusColumn) {
            $sql .= " AND status != 'cancelled'";
        }

        $sql .= " LIMIT 1";

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
        $data['user_type'] = $this->normalizeUserType($data['user_type'] ?? null);

        $columns = $this->getExistingColumns();

        // Handle existing registration row first (important because of unique key)
        $existingRegistration = $this->getAnyRegistration($data['event_id'], $data['user_id'], $data['user_type']);
        if ($existingRegistration) {
            $existingStatus = isset($existingRegistration->status) ? strtolower((string)$existingRegistration->status) : 'registered';

            // Already active registration
            if ($existingStatus !== 'cancelled') {
                return false;
            }

            // Reactivate cancelled registration instead of inserting a new row
            $reactivateData = [];
            if (in_array('status', $columns, true)) {
                $reactivateData['status'] = 'registered';
            }
            if (in_array('cancelled_at', $columns, true)) {
                $reactivateData['cancelled_at'] = null;
            }
            if (in_array('registration_type', $columns, true)) {
                $reactivateData['registration_type'] = $data['registration_type'] ?? 'free';
            }
            if (in_array('amount_paid', $columns, true)) {
                $reactivateData['amount_paid'] = $data['amount_paid'] ?? 0.00;
            }
            if (in_array('notes', $columns, true) && array_key_exists('notes', $data)) {
                $reactivateData['notes'] = $data['notes'];
            }

            if (!empty($reactivateData)) {
                $updated = $this->update($existingRegistration->id, $reactivateData);
                if ($updated) {
                    $this->logRegistrationActivity($data);
                    return $existingRegistration->id;
                }
            }

            return false;
        }

        // Set defaults
        if (in_array('registration_type', $columns, true)) {
            $data['registration_type'] = $data['registration_type'] ?? 'free';
        }
        if (in_array('status', $columns, true)) {
            $data['status'] = 'registered';
        }
        if (in_array('amount_paid', $columns, true)) {
            $data['amount_paid'] = $data['amount_paid'] ?? 0.00;
        }

        // Keep only columns that actually exist in database
        $insertData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $columns, true)) {
                $insertData[$key] = $value;
            }
        }

        // Required minimal payload
        foreach (['event_id', 'user_id', 'user_type'] as $requiredKey) {
            if (!array_key_exists($requiredKey, $insertData)) {
                return false;
            }
        }

        $keys = array_keys($insertData);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $keys) . ") VALUES (:" . implode(',:', $keys) . ")";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $executeResult = $stm->execute($insertData);
        $result = $executeResult ? $conn->lastInsertId() : false;

        // Log activity if registration was successful
        if ($result) {
            $this->logRegistrationActivity($data);
        }

        return $result;
    }

    /**
     * Ensure user has an active paid registration for an event.
     * If registration exists, it updates payment-related fields.
     */
    public function ensurePaidRegistration($data)
    {
        $data['user_type'] = $this->normalizeUserType($data['user_type'] ?? null);
        $data['registration_type'] = 'paid';
        $data['status'] = 'registered';
        $data['amount_paid'] = isset($data['amount_paid']) ? (float)$data['amount_paid'] : 0.00;

        $columns = $this->getExistingColumns();
        $existingRegistration = $this->getAnyRegistration($data['event_id'], $data['user_id'], $data['user_type']);

        if (!$existingRegistration) {
            return $this->registerUser($data);
        }

        $updateData = [];

        if (in_array('registration_type', $columns, true)) {
            $updateData['registration_type'] = 'paid';
        }

        if (in_array('status', $columns, true)) {
            $updateData['status'] = 'registered';
        }

        if (in_array('cancelled_at', $columns, true)) {
            $updateData['cancelled_at'] = null;
        }

        if (in_array('amount_paid', $columns, true)) {
            $updateData['amount_paid'] = $data['amount_paid'];
        }

        if (in_array('payment_id', $columns, true) && !empty($data['payment_id'])) {
            $updateData['payment_id'] = $data['payment_id'];
        }

        if (in_array('notes', $columns, true) && array_key_exists('notes', $data)) {
            $updateData['notes'] = $data['notes'];
        }

        if (empty($updateData)) {
            return $existingRegistration->id;
        }

        $updated = $this->update($existingRegistration->id, $updateData);
        return $updated ? $existingRegistration->id : false;
    }

    /**
     * Find registration regardless of status
     */
    private function getAnyRegistration($eventId, $userId, $userType)
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
        $userType = $this->normalizeUserType($userType);

        $columns = $this->getExistingColumns();

        // If status column doesn't exist, remove the registration record directly
        if (!in_array('status', $columns, true)) {
            $sql = "DELETE FROM {$this->table}
                    WHERE event_id = :event_id
                    AND user_id = :user_id
                    AND user_type = :user_type";

            return $this->query($sql, [
                'event_id' => $eventId,
                'user_id' => $userId,
                'user_type' => $userType
            ]);
        }

        $hasCancelledAt = in_array('cancelled_at', $columns, true);
        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled'";

        if ($hasCancelledAt) {
            $sql .= ", cancelled_at = NOW()";
        }

        $sql .= "
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
        $userType = $this->normalizeUserType($userType);

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
        $userType = $this->normalizeUserType($userType);

        $columns = $this->getExistingColumns();
        $hasRegisteredAt = in_array('registered_at', $columns, true);
        $hasStatus = in_array('status', $columns, true);

        $sql = "SELECT e.*";
        if ($hasRegisteredAt) {
            $sql .= ", er.registered_at";
        } else {
            $sql .= ", NULL as registered_at";
        }

        if ($hasStatus) {
            $sql .= ", er.status as registration_status";
        } else {
            $sql .= ", 'registered' as registration_status";
        }

        $sql .= "
                FROM {$this->table} er
                INNER JOIN events e ON er.event_id = e.id
                WHERE er.user_id = :user_id 
                AND er.user_type = :user_type";

        $params = [
            'user_id' => $userId,
            'user_type' => $userType
        ];

        if ($status && $hasStatus) {
            $sql .= " AND er.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY e.event_date ASC, e.event_time ASC";

        return $this->query($sql, $params);
    }

    /**
     * Get existing columns for event_registrations table
     */
    private function getExistingColumns()
    {
        if (is_array($this->tableColumnsCache)) {
            return $this->tableColumnsCache;
        }

        try {
            $this->ensureTableIntegrity();

            $result = $this->query("SHOW COLUMNS FROM {$this->table}");
            if (!$result) {
                $this->createTableIfMissing();
                $this->ensureTableIntegrity();
                $result = $this->query("SHOW COLUMNS FROM {$this->table}");
            }

            if (!$result) {
                $this->tableColumnsCache = [];
                return $this->tableColumnsCache;
            }

            $columns = [];
            foreach ($result as $column) {
                if (isset($column->Field)) {
                    $columns[] = $column->Field;
                }
            }

            $this->tableColumnsCache = $columns;
            return $this->tableColumnsCache;
        } catch (Throwable $e) {
            try {
                $this->createTableIfMissing();
                $this->ensureTableIntegrity();
                $result = $this->query("SHOW COLUMNS FROM {$this->table}");

                if ($result) {
                    $columns = [];
                    foreach ($result as $column) {
                        if (isset($column->Field)) {
                            $columns[] = $column->Field;
                        }
                    }

                    $this->tableColumnsCache = $columns;
                    return $this->tableColumnsCache;
                }
            } catch (Throwable $innerError) {
                error_log("EventRegistration table recovery failed: " . $innerError->getMessage());
            }

            error_log("EventRegistration column introspection failed: " . $e->getMessage());
            $this->tableColumnsCache = [];
            return $this->tableColumnsCache;
        }
    }

    /**
     * Create registrations table if it does not exist
     */
    private function createTableIfMissing()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
            registration_type ENUM('free', 'paid') DEFAULT 'free',
            status ENUM('registered', 'cancelled', 'attended') DEFAULT 'registered',
            notes TEXT NULL,
            payment_id VARCHAR(255) NULL,
            amount_paid DECIMAL(10, 2) NULL DEFAULT 0.00,
            registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            cancelled_at TIMESTAMP NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_event_id (event_id),
            INDEX idx_user_id (user_id),
            INDEX idx_user_type (user_type),
            INDEX idx_status (status),
            UNIQUE KEY unique_registration (event_id, user_id, user_type),
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $stm->execute();
    }

    /**
     * Ensure critical schema constraints exist for reliable inserts
     */
    private function ensureTableIntegrity()
    {
        if ($this->tableIntegrityChecked) {
            return;
        }

        $this->tableIntegrityChecked = true;

        try {
            $dbName = DBNAME;

            $hasPrimaryKeyQuery = "SELECT COUNT(*) as count
                                   FROM information_schema.TABLE_CONSTRAINTS
                                   WHERE TABLE_SCHEMA = :schema
                                   AND TABLE_NAME = :table
                                   AND CONSTRAINT_TYPE = 'PRIMARY KEY'";
            $hasPrimaryKeyResult = $this->query($hasPrimaryKeyQuery, [
                'schema' => $dbName,
                'table' => $this->table
            ]);
            $hasPrimaryKey = ($hasPrimaryKeyResult && (int)$hasPrimaryKeyResult[0]->count > 0);

            $idMetaQuery = "SELECT EXTRA
                            FROM information_schema.COLUMNS
                            WHERE TABLE_SCHEMA = :schema
                            AND TABLE_NAME = :table
                            AND COLUMN_NAME = 'id'
                            LIMIT 1";
            $idMetaResult = $this->query($idMetaQuery, [
                'schema' => $dbName,
                'table' => $this->table
            ]);
            $idExtra = $idMetaResult[0]->EXTRA ?? '';
            $isAutoIncrement = stripos((string)$idExtra, 'auto_increment') !== false;

            $hasUniqueQuery = "SELECT COUNT(*) as count
                               FROM information_schema.STATISTICS
                               WHERE TABLE_SCHEMA = :schema
                               AND TABLE_NAME = :table
                               AND INDEX_NAME = 'unique_registration'";
            $hasUniqueResult = $this->query($hasUniqueQuery, [
                'schema' => $dbName,
                'table' => $this->table
            ]);
            $hasUniqueRegistration = ($hasUniqueResult && (int)$hasUniqueResult[0]->count > 0);

            $conn = $this->connect();

            if (!$hasPrimaryKey) {
                $conn->exec("ALTER TABLE {$this->table} ADD PRIMARY KEY (id)");
            }

            if (!$isAutoIncrement) {
                $conn->exec("ALTER TABLE {$this->table} MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT");
            }

            if (!$hasUniqueRegistration) {
                $conn->exec("ALTER TABLE {$this->table} ADD UNIQUE KEY unique_registration (event_id, user_id, user_type)");
            }
        } catch (Throwable $e) {
            error_log("EventRegistration integrity check warning: " . $e->getMessage());
        }
    }

    /**
     * Mark registration as attended
     */
    public function markAsAttended($eventId, $userId, $userType)
    {
        $userType = $this->normalizeUserType($userType);

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
        $userType = $this->normalizeUserType($userType);

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
        $userType = $this->normalizeUserType($userType);

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

    /**
     * Normalize user type values used across the app to DB enum values
     */
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
}
