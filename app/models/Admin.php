<?php

class Admin {
    
    use Model;
    
    protected $table = 'admins';
    protected $allowedColumns = [
        'full_name',
        'email', 
        'password_hash',
        'phone',
        'is_active'
    ];
    
    /**
     * Validate admin data
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
        
        return $errors;
    }
    
    /**
     * Create new admin
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
        
        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));
        
        if ($this->insert($filteredData)) {
            return ['success' => true, 'message' => 'Admin created successfully'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to create admin']];
    }
    
    /**
     * Update admin
     */
    public function updateAdmin($id, $data) {
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
                // Check if email already exists (excluding current admin)
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
            return ['success' => true, 'message' => 'Admin updated successfully'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to update admin']];
    }
    
    /**
     * Get all active admins
     */
    public function getActiveAdmins() {
        $result = $this->where(['is_active' => 1]);
        return $result ?: [];
    }
    
    /**
     * Deactivate admin
     */
    public function deactivate($id) {
        return $this->update($id, ['is_active' => 0]);
    }
    
    /**
     * Activate admin
     */
    public function activate($id) {
        return $this->update($id, ['is_active' => 1]);
    }
}
