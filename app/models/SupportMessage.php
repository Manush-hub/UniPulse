<?php

class SupportMessage {
    use Model;

    protected $table = 'support_messages';
    protected $allowedColumns = [
        'full_name',
        'email',
        'phone',
        'category',
        'subject',
        'message',
        'status',
        'source_page',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    public function validate($input) {
        $errors = [];
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');

        if ($subject === '') {
            $errors[] = 'Subject is required.';
        } elseif (mb_strlen($subject) > 255) {
            $errors[] = 'Subject must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        } elseif (mb_strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters.';
        }

        return $errors;
    }

    public function createFromContactForm($input, $profileData = []) {
        $this->ensureTableExists();

        $fullName = trim((string)($profileData['name'] ?? ''));
        $email = trim((string)($profileData['email'] ?? ''));
        $phone = trim((string)($profileData['phone'] ?? ''));

        if ($fullName === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $data = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'category' => 'contact',
            'subject' => trim($input['subject'] ?? ''),
            'message' => trim($input['message'] ?? ''),
            'source_page' => '/unipulse/public/contact',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    public function getRecentForAdmin($limit = 20) {
        $this->ensureTableExists();

        $limit = max(1, (int)$limit);

        $query = "SELECT
                    id,
                    full_name,
                    email,
                    phone,
                    subject,
                    message,
                    status,
                    source_page,
                    created_at
                  FROM support_messages
                  ORDER BY created_at DESC
                  LIMIT {$limit}";

        return $this->query($query, []) ?: [];
    }

    public function getUnreadNotificationsForAdmin($limit = 10) {
        $this->ensureTableExists();

        $limit = max(1, (int)$limit);
        $query = "SELECT id, full_name, email, subject, message, created_at
                  FROM support_messages
                  WHERE status = 'new'
                  ORDER BY created_at DESC
                  LIMIT {$limit}";

        return $this->query($query, []) ?: [];
    }

    public function markNotificationAsRead($id) {
        $this->ensureTableExists();

        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $conn = $this->connect();
        $stmt = $conn->prepare(
            "UPDATE support_messages
             SET status = 'in_progress', updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute(['id' => $id]);
    }

    public function markAllNotificationsAsRead() {
        $this->ensureTableExists();

        $conn = $this->connect();
        $stmt = $conn->prepare(
            "UPDATE support_messages
             SET status = 'in_progress', updated_at = NOW()
             WHERE status = 'new'"
        );

        return $stmt->execute();
    }

    private function ensureTableExists() {
        static $checked = false;
        if ($checked) {
            return;
        }

        $conn = $this->connect();
        $conn->exec(
            "CREATE TABLE IF NOT EXISTS support_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(150) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(30) DEFAULT NULL,
                category VARCHAR(100) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                source_page VARCHAR(255) DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                user_agent TEXT DEFAULT NULL,
                status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_category (category),
                INDEX idx_email (email),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $checked = true;
    }
}
