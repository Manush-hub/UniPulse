<?php

class FreeEventRegistration
{
    use Model;

    protected $table = 'free_event_registrations';
    protected $allowedColumns = [
        'event_id',
        'publisher_id',
        'event_title_snapshot',
        'publisher_name_snapshot',
        'registered_user_id',
        'registered_user_type',
        'registered_user_name_snapshot',
        'registered_user_email_snapshot',
        'registered_user_phone_snapshot',
        'registration_source',
        'status',
        'registration_notes',
        'cancellation_reason',
        'registered_at',
        'checked_in_at',
        'cancelled_at'
    ];

    private $tableColumnsCache = null;

    public function isUserRegistered($eventId, $userId, $userType)
    {
        $userType = $this->normalizeUserType($userType);
        $columns = $this->getExistingColumns();
        $hasStatus = in_array('status', $columns, true);

        $sql = "SELECT id FROM {$this->table}
                WHERE event_id = :event_id
                AND registered_user_id = :registered_user_id
                AND registered_user_type = :registered_user_type";

        if ($hasStatus) {
            $sql .= " AND status != 'cancelled'";
        }

        $sql .= " LIMIT 1";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'registered_user_id' => $userId,
            'registered_user_type' => $userType
        ]);

        return !empty($result);
    }

    public function registerUser($data)
    {
        $data['registered_user_type'] = $this->normalizeUserType($data['registered_user_type'] ?? null);

        $columns = $this->getExistingColumns();
        if (empty($columns)) {
            return false;
        }

        $existing = $this->getAnyRegistration(
            $data['event_id'] ?? null,
            $data['registered_user_id'] ?? null,
            $data['registered_user_type'] ?? null
        );

        if ($existing) {
            $existingStatus = isset($existing->status) ? strtolower((string)$existing->status) : 'registered';
            if ($existingStatus !== 'cancelled') {
                return $existing->id;
            }

            $reactivateData = [];
            if (in_array('status', $columns, true)) {
                $reactivateData['status'] = 'registered';
            }
            if (in_array('cancelled_at', $columns, true)) {
                $reactivateData['cancelled_at'] = null;
            }
            if (in_array('registered_at', $columns, true)) {
                $reactivateData['registered_at'] = date('Y-m-d H:i:s');
            }
            if (in_array('registration_notes', $columns, true) && array_key_exists('registration_notes', $data)) {
                $reactivateData['registration_notes'] = $data['registration_notes'];
            }

            if (!empty($reactivateData) && $this->update($existing->id, $reactivateData)) {
                return $existing->id;
            }

            return false;
        }

        if (in_array('status', $columns, true) && !isset($data['status'])) {
            $data['status'] = 'registered';
        }
        if (in_array('registration_source', $columns, true) && !isset($data['registration_source'])) {
            $data['registration_source'] = 'web';
        }

        $insertData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $columns, true)) {
                $insertData[$key] = $value;
            }
        }

        foreach (['event_id', 'registered_user_id', 'registered_user_type'] as $requiredKey) {
            if (!array_key_exists($requiredKey, $insertData)) {
                return false;
            }
        }

        $keys = array_keys($insertData);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $keys) . ") VALUES (:" . implode(',:', $keys) . ")";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $executeResult = $stm->execute($insertData);

        return $executeResult ? $conn->lastInsertId() : false;
    }

    private function getAnyRegistration($eventId, $userId, $userType)
    {
        if (empty($eventId) || empty($userId) || empty($userType)) {
            return null;
        }

        $sql = "SELECT * FROM {$this->table}
                WHERE event_id = :event_id
                AND registered_user_id = :registered_user_id
                AND registered_user_type = :registered_user_type
                LIMIT 1";

        $result = $this->query($sql, [
            'event_id' => $eventId,
            'registered_user_id' => $userId,
            'registered_user_type' => $userType
        ]);

        return $result ? $result[0] : null;
    }

    private function getExistingColumns()
    {
        if (is_array($this->tableColumnsCache)) {
            return $this->tableColumnsCache;
        }

        try {
            $result = $this->query("SHOW COLUMNS FROM {$this->table}");
            if (!$result) {
                $this->createTableIfMissing();
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
                error_log('FreeEventRegistration table recovery failed: ' . $innerError->getMessage());
            }

            error_log('FreeEventRegistration schema introspection failed: ' . $e->getMessage());
            $this->tableColumnsCache = [];
            return $this->tableColumnsCache;
        }
    }

    private function createTableIfMissing()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            publisher_id INT NULL,
            event_title_snapshot VARCHAR(255) NOT NULL,
            publisher_name_snapshot VARCHAR(255) NULL,
            registered_user_id INT NOT NULL,
            registered_user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
            registered_user_name_snapshot VARCHAR(255) NOT NULL,
            registered_user_email_snapshot VARCHAR(255) NULL,
            registered_user_phone_snapshot VARCHAR(25) NULL,
            registration_source ENUM('web', 'mobile', 'admin', 'import') NOT NULL DEFAULT 'web',
            status ENUM('registered', 'waitlisted', 'cancelled', 'checked_in', 'no_show') NOT NULL DEFAULT 'registered',
            registration_notes TEXT NULL,
            cancellation_reason VARCHAR(255) NULL,
            registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            checked_in_at DATETIME NULL,
            cancelled_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_free_event_user (event_id, registered_user_id, registered_user_type),
            KEY idx_free_event_id (event_id),
            KEY idx_free_publisher_id (publisher_id),
            KEY idx_free_user (registered_user_id, registered_user_type),
            KEY idx_free_status (status),
            KEY idx_free_registered_at (registered_at),
            CONSTRAINT fk_free_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            CONSTRAINT fk_free_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $stm->execute();
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
}
