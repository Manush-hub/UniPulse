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
        
        // Validate full name
        if (isset($data['full_name'])) {
            if (empty($data['full_name'])) {
                $errors['full_name'] = 'Full name is required';
            } elseif (strlen($data['full_name']) < 2) {
                $errors['full_name'] = 'Full name must be at least 2 characters';
            }
        }
        
        // Validate email
        if (isset($data['email'])) {
            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please provide a valid email address';
            } else {
                // Check if email already exists (excluding current moderator)
                $existing = $this->where(['email' => $data['email']]);
                if ($existing && count($existing) > 0 && $existing[0]->id != $id) {
                    $errors['email'] = 'This email is already registered';
                }
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
        $moderator = $this->find($id);
        if ($moderator && $moderator->permissions) {
            return json_decode($moderator->permissions, true);
        }
        
        // Default permissions for moderators
        return [
            'approve_publishers' => true,
            'moderate_events' => true,
            'handle_reports' => true,
            'view_analytics' => true
        ];
    }
    
    /**
     * Get available universities
     */
    public static function getAvailableUniversities() {
        return [
            'university-of-moratuwa' => 'University of Moratuwa',
            'university-of-peradeniya' => 'University of Peradeniya', 
            'university-of-colombo' => 'University of Colombo',
            'university-of-kelaniya' => 'University of Kelaniya',
            'university-of-sri-jayewardenepura' => 'University of Sri Jayewardenepura'
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
}
