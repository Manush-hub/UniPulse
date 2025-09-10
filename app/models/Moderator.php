<?php

class Moderator {
    
    use Model;
    
    protected $table = 'moderators';
    protected $allowedColumns = [
        'full_name',
        'email', 
        'password_hash',
        'phone',
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
                if ($existing && $existing[0]->id != $id) {
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
        return [];
    }
}
