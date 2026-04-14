<?php

class Moderator {
    
    use Model;
    
    protected $table = 'moderators';
    protected $allowedColumns = [
        'full_name',
        'email', 
        'password_hash',
        'phone',
        'university',
        'university_name',
        'assigned_by',
        'permissions',
        'is_active'
    ];
    
    /**
     * Validate moderator data
     */
    public function validate($data) {
        $errors = [];
        
        // Validate full name
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Full name must be at least 2 characters';
        }
        
        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address';
        } else {
            // Check if email already exists
            if ($this->where(['email' => $data['email']])) {
                $errors['email'] = 'This email is already registered';
            }
        }
        
        // Validate password (if provided)
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
        }
        
        // Validate phone (if provided)
        if (!empty($data['phone']) && !preg_match('/^[+]?[0-9\s\-\(\)]+$/', $data['phone'])) {
            $errors['phone'] = 'Please provide a valid phone number';
        }
        
        // Validate university
        if (empty($data['university'])) {
            $errors['university'] = 'University is required';
        }
        
        // Validate university name
        if (empty($data['university_name'])) {
            $errors['university_name'] = 'University name is required';
        }
        
        // Validate assigned_by (must be valid admin ID)
        if (empty($data['assigned_by'])) {
            $errors['assigned_by'] = 'Assigning admin is required';
        } else {
            // Check if admin exists
            $adminModel = new Admin();
            if (!$adminModel->find($data['assigned_by'])) {
                $errors['assigned_by'] = 'Invalid admin ID';
            }
        }
        
        return $errors;
    }
    
    /**
     * Create new moderator (only by admin)
     */
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        // Set default permissions if not provided
        if (!isset($data['permissions'])) {
            $data['permissions'] = json_encode([
                'view_events' => true,
                'edit_events' => true,
                'view_users' => true,
                'moderate_content' => true
            ]);
        }
        
        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));
        
        if ($this->insert($filteredData)) {
            return ['success' => true, 'message' => 'Moderator created successfully'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to create moderator']];
    }
    
    /**
     * Update moderator
     */
    public function updateModerator($id, $data) {
        $errors = [];
        
        // Remove email from update data - email cannot be changed
        if (isset($data['email'])) {
            unset($data['email']);
        }
        
        // Validate full name
        if (isset($data['full_name'])) {
            if (empty($data['full_name'])) {
                $errors['full_name'] = 'Full name is required';
            } elseif (strlen($data['full_name']) < 2) {
                $errors['full_name'] = 'Full name must be at least 2 characters';
            }
        }
        
        // Validate password (if provided)
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            } else {
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data['password']);
            }
        }
        
        // Validate phone (if provided)
        if (isset($data['phone']) && !empty($data['phone']) && !preg_match('/^[+]?[0-9\s\-\(\)]+$/', $data['phone'])) {
            $errors['phone'] = 'Please provide a valid phone number';
        }
        
        // Validate university (if provided)
        if (isset($data['university']) && empty($data['university'])) {
            $errors['university'] = 'University is required';
        }
        
        // Validate university name (if provided)
        if (isset($data['university_name']) && empty($data['university_name'])) {
            $errors['university_name'] = 'University name is required';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));
        
        if ($this->update($id, $filteredData)) {
            return ['success' => true, 'message' => 'Moderator updated successfully'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to update moderator']];
    }
    
    /**
     * Get all active moderators
     */
    public function getActiveModerators() {
        $result = $this->where(['is_active' => 1]);
        return $result ?: [];
    }
    
    /**
     * Get moderators assigned by specific admin
     */
    public function getByAdmin($adminId) {
        return $this->where(['assigned_by' => $adminId, 'is_active' => 1]);
    }
    
    /**
     * Deactivate moderator
     */
    public function deactivate($id) {
        return $this->update($id, ['is_active' => 0]);
    }
    
    /**
     * Activate moderator
     */
    public function activate($id) {
        return $this->update($id, ['is_active' => 1]);
    }
    
    /**
     * Delete moderator permanently
     */
    public function deleteModerator($id) {
        return $this->delete($id);
    }
    
    /**
     * Update permissions
     */
    public function updatePermissions($id, $permissions) {
        return $this->update($id, ['permissions' => json_encode($permissions)]);
    }
    
    /**
     * Get moderator permissions
     */
    public function getPermissions($id) {
        // Default permissions for moderators
        $defaultPermissions = [
            'approve_publishers' => true,
            'moderate_events' => true,
            'handle_reports' => true,
            'view_analytics' => true
        ];
        
        $moderator = $this->find($id);
        if ($moderator && $moderator->permissions) {
            $existingPermissions = json_decode($moderator->permissions, true) ?: [];
            // Merge existing permissions with defaults, giving priority to existing
            return array_merge($defaultPermissions, $existingPermissions);
        }
        
        // Return default permissions if no moderator found or no permissions set
        return $defaultPermissions;
    }
    
    /**
     * Get available universities
     */
    public static function getAvailableUniversities() {
        return [
            // State Universities
            'university-of-colombo'              => 'University of Colombo',
            'university-of-peradeniya'           => 'University of Peradeniya',
            'university-of-sri-jayewardenepura'  => 'University of Sri Jayewardenepura',
            'university-of-kelaniya'             => 'University of Kelaniya',
            'university-of-moratuwa'             => 'University of Moratuwa',
            'university-of-jaffna'               => 'University of Jaffna',
            'university-of-ruhuna'               => 'University of Ruhuna',
            'eastern-university'                 => 'Eastern University, Sri Lanka',
            'south-eastern-university'           => 'South Eastern University of Sri Lanka',
            'rajarata-university'                => 'Rajarata University of Sri Lanka',
            'sabaragamuwa-university'            => 'Sabaragamuwa University of Sri Lanka',
            'wayamba-university'                 => 'Wayamba University of Sri Lanka',
            'uva-wellassa-university'            => 'Uva Wellassa University',
            'open-university'                    => 'Open University of Sri Lanka',
            'buddhist-and-pali-university'       => 'Buddhist and Pali University of Sri Lanka',
            // Private Universities
            'sliit'                              => 'Sri Lanka Institute of Information Technology (SLIIT)',
            'nsbm'                               => 'NSBM Green University',
            'cinec'                              => 'CINEC Campus',
            'apiit'                              => 'Asia Pacific Institute of Information Technology (APIIT)',
            'metropolitan-campus'                => 'KIU (Kaatsu International University)',
        ];
    }
    
    /**
     * Get moderators by university
     */
    public function getByUniversity($university) {
        return $this->where(['university' => $university, 'is_active' => 1]);
    }
    
    /**
     * Find moderator by email for authentication
     */
    public function findByEmail($email) {
        $result = $this->where(['email' => $email, 'is_active' => 1]);
        return $result && count($result) > 0 ? $result[0] : false;
    }
    
    /**
     * Authenticate moderator with email and password
     */
    public function authenticate($email, $password) {
        $moderator = $this->findByEmail($email);
        if ($moderator && password_verify($password, $moderator->password_hash)) {
            return $moderator;
        }
        return false;
    }
    
    /**
     * Check if moderator has specific permission
     */
    public function hasPermission($moderatorId, $permission) {
        $permissions = $this->getPermissions($moderatorId);
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
    
    /**
     * Get moderator dashboard statistics
     */
    public function getDashboardStats($moderatorId) {
        $moderator = $this->where(['id' => $moderatorId])[0] ?? null;
        if (!$moderator) {
            return null;
        }
        
        // Get publisher approval stats for this moderator's university
        $publisherModel = new Publisher();
        $publisherStats = $publisherModel->getStatsByUniversity($moderator->university);
        
        return [
            'moderator' => $moderator,
            'publisher_stats' => $publisherStats,
            'permissions' => $this->getPermissions($moderatorId)
        ];
    }
    
    /**
     * Find moderator by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM moderators WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
    
    /**
     * Check if moderator has pending approvals
     */
    public function hasPendingApprovals($moderatorId) {
        $moderator = $this->find($moderatorId);
        if (!$moderator) {
            return false;
        }
        
        // Check for pending publisher approvals for this moderator's university
        $publisherModel = new Publisher();
        $pendingCount = $publisherModel->getPendingCountByUniversity($moderator->university);
        
        return $pendingCount > 0;
    }
    
    /**
     * Get count of pending approvals for moderator
     */
    public function getPendingApprovalsCount($moderatorId) {
        $moderator = $this->find($moderatorId);
        if (!$moderator) {
            return 0;
        }
        
        // Get pending publisher approvals count for this moderator's university
        $publisherModel = new Publisher();
        return $publisherModel->getPendingCountByUniversity($moderator->university);
    }

    /**
     * Get moderation action stats for a moderator.
     */
    public function getModerationStats($moderatorId) {
        $moderatorId = (int)$moderatorId;
        if ($moderatorId <= 0) {
            return [
                'hidden_events' => 0,
                'approved_publishers' => 0,
                'rejected_publishers' => 0,
                'total_actions' => 0,
            ];
        }

        try {
            $hiddenRows = $this->query(
                "SELECT COUNT(*) AS total FROM events WHERE is_deleted = 1 AND deleted_by = :moderator_id",
                ['moderator_id' => $moderatorId]
            ) ?: [];

            $approvedRows = $this->query(
                "SELECT COUNT(*) AS total FROM publishers WHERE approval_status = 'approved' AND approved_by = :moderator_id",
                ['moderator_id' => $moderatorId]
            ) ?: [];

            $rejectedRows = $this->query(
                "SELECT COUNT(*) AS total FROM publishers WHERE approval_status = 'rejected' AND approved_by = :moderator_id",
                ['moderator_id' => $moderatorId]
            ) ?: [];

            $hiddenEvents = (int)($hiddenRows[0]->total ?? 0);
            $approvedPublishers = (int)($approvedRows[0]->total ?? 0);
            $rejectedPublishers = (int)($rejectedRows[0]->total ?? 0);

            return [
                'hidden_events' => $hiddenEvents,
                'approved_publishers' => $approvedPublishers,
                'rejected_publishers' => $rejectedPublishers,
                'total_actions' => $hiddenEvents + $approvedPublishers + $rejectedPublishers,
            ];
        } catch (Exception $e) {
            error_log('Moderator::getModerationStats error: ' . $e->getMessage());
            return [
                'hidden_events' => 0,
                'approved_publishers' => 0,
                'rejected_publishers' => 0,
                'total_actions' => 0,
            ];
        }
    }

    /**
     * Get publisher performance report rows for a moderator university.
     */
    public function getPublisherPerformanceReportByUniversity($university)
    {
        $university = trim((string)$university);
        if ($university === '') {
            return [];
        }

        try {
            $rows = $this->query(
                "SELECT
                    p.id AS publisher_id,
                    p.society_name,
                    p.email,
                    COUNT(DISTINCT e.id) AS total_events_posted,
                    COUNT(DISTINCT CASE WHEN c.rating > 0 THEN c.id END) AS total_ratings,
                    ROUND(AVG(CASE WHEN c.rating > 0 THEN c.rating END), 2) AS average_rating
                 FROM publishers p
                 LEFT JOIN events e
                    ON e.created_by = p.id
                   AND e.created_by_type = 'publisher'
                   AND e.is_deleted = 0
                 LEFT JOIN event_comments c
                    ON c.event_id = e.id
                   AND c.is_deleted = 0
                   AND c.is_hidden = 0
                 WHERE p.university = :university
                   AND p.approval_status = 'approved'
                   AND COALESCE(p.is_deleted, 0) = 0
                 GROUP BY p.id, p.society_name, p.email
                 ORDER BY total_events_posted DESC, average_rating DESC, p.society_name ASC",
                ['university' => $university]
            ) ?: [];

            $ticketsByPublisher = [];

            if ($this->tableExists('paid_event_registrations')) {
                $ticketRows = $this->query(
                    "SELECT
                        per.publisher_id,
                        COALESCE(SUM(per.ticket_quantity), 0) AS tickets_sold
                     FROM paid_event_registrations per
                     INNER JOIN publishers p ON p.id = per.publisher_id
                     WHERE p.university = :university
                       AND COALESCE(p.is_deleted, 0) = 0
                       AND COALESCE(per.registration_status, 'reserved') != 'cancelled'
                       AND COALESCE(per.payment_status, 'pending') IN ('paid', 'partially_refunded')
                     GROUP BY per.publisher_id",
                    ['university' => $university]
                ) ?: [];

                foreach ($ticketRows as $ticketRow) {
                    $ticketsByPublisher[(int)($ticketRow->publisher_id ?? 0)] = (int)($ticketRow->tickets_sold ?? 0);
                }
            } elseif ($this->tableExists('event_registrations')) {
                $ticketRows = $this->query(
                    "SELECT
                        e.created_by AS publisher_id,
                        COUNT(er.id) AS tickets_sold
                     FROM event_registrations er
                     INNER JOIN events e
                        ON e.id = er.event_id
                       AND e.created_by_type = 'publisher'
                     INNER JOIN publishers p ON p.id = e.created_by
                     WHERE p.university = :university
                       AND COALESCE(p.is_deleted, 0) = 0
                       AND COALESCE(er.registration_type, 'free') = 'paid'
                       AND (er.status IS NULL OR er.status != 'cancelled')
                     GROUP BY e.created_by",
                    ['university' => $university]
                ) ?: [];

                foreach ($ticketRows as $ticketRow) {
                    $ticketsByPublisher[(int)($ticketRow->publisher_id ?? 0)] = (int)($ticketRow->tickets_sold ?? 0);
                }
            }

            $reportRows = [];
            foreach ($rows as $row) {
                $publisherId = (int)($row->publisher_id ?? 0);
                $avgRating = ($row->average_rating !== null) ? round((float)$row->average_rating, 2) : null;

                $reportRows[] = (object)[
                    'publisher_id' => $publisherId,
                    'society_name' => (string)($row->society_name ?? 'Unknown Publisher'),
                    'email' => (string)($row->email ?? ''),
                    'total_events_posted' => (int)($row->total_events_posted ?? 0),
                    'total_ratings' => (int)($row->total_ratings ?? 0),
                    'average_rating' => $avgRating,
                    'tickets_sold' => (int)($ticketsByPublisher[$publisherId] ?? 0),
                ];
            }

            return $reportRows;
        } catch (Exception $e) {
            error_log('Moderator::getPublisherPerformanceReportByUniversity error: ' . $e->getMessage());
            return [];
        }
    }

    private function tableExists($tableName)
    {
        $row = $this->getRow(
            "SELECT COUNT(*) AS total
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name = :table_name",
            ['table_name' => (string)$tableName]
        );

        return (int)($row->total ?? 0) > 0;
    }
}
