<?php

class NotificationReadState
{
    use Database;

    private $table = 'notification_read_states';

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                user_type VARCHAR(50) NOT NULL,
                notification_key VARCHAR(255) NOT NULL,
                read_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_user_notification (user_id, user_type, notification_key),
                INDEX idx_user_type (user_id, user_type),
                INDEX idx_read_at (read_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->query($sql);
        $initialized = true;
    }

    public function getReadItemsMap(int $userId, string $userType): array
    {
        $sql = "
            SELECT notification_key
            FROM {$this->table}
            WHERE user_id = :user_id
              AND user_type = :user_type
              AND notification_key NOT LIKE '__all__:%'
        ";

        $rows = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        if (!$rows) {
            return [];
        }

        $map = [];
        foreach ($rows as $row) {
            $key = (string)($row->notification_key ?? '');
            if ($key !== '') {
                $map[$key] = true;
            }
        }

        return $map;
    }

    public function getLastReadAt(int $userId, string $userType, string $scope): string
    {
        $scopeKey = '__all__:' . $scope;

        $sql = "
            SELECT read_at
            FROM {$this->table}
            WHERE user_id = :user_id
              AND user_type = :user_type
              AND notification_key = :scope_key
            LIMIT 1
        ";

        $row = $this->getRow($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'scope_key' => $scopeKey
        ]);

        return $row && !empty($row->read_at) ? (string)$row->read_at : '1970-01-01 00:00:00';
    }

    public function markRead(int $userId, string $userType, string $notificationKey): bool
    {
        $notificationKey = trim($notificationKey);
        if ($notificationKey === '') {
            return false;
        }

        $sql = "
            INSERT INTO {$this->table} (user_id, user_type, notification_key, read_at)
            VALUES (:user_id, :user_type, :notification_key, NOW())
            ON DUPLICATE KEY UPDATE read_at = NOW()
        ";

        return (bool)$this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'notification_key' => $notificationKey
        ]);
    }

    public function markAllRead(int $userId, string $userType, string $scope): bool
    {
        $scopeKey = '__all__:' . trim($scope);
        if ($scopeKey === '__all__:') {
            return false;
        }

        $sql = "
            INSERT INTO {$this->table} (user_id, user_type, notification_key, read_at)
            VALUES (:user_id, :user_type, :scope_key, NOW())
            ON DUPLICATE KEY UPDATE read_at = NOW()
        ";

        return (bool)$this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'scope_key' => $scopeKey
        ]);
    }
}
