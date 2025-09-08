<?php

class User extends BaseModel {
    
    protected $table = 'users';

    public function create($data) {
        // Validate required fields
        $errors = [];
        
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->emailExists($data['email'])) {
            $errors['email'] = 'Email already exists';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Generate verification token
        $data['verification_token'] = bin2hex(random_bytes(32));
        
        // Set default values
        $data['user_type'] = $data['user_type'] ?? 'public';
        $data['is_active'] = 1; // Use integer for boolean
        $data['is_verified'] = 0; // Use integer for boolean
        
        // Set NULL for optional university fields if not provided
        $data['university_id'] = $data['university_id'] ?? null;
        $data['student_id'] = $data['student_id'] ?? null;
        $data['faculty'] = $data['faculty'] ?? null;
        $data['year_of_study'] = $data['year_of_study'] ?? null;
        $data['degree_program'] = $data['degree_program'] ?? null;
        $data['address_line2'] = $data['address_line2'] ?? null;
        
        // Insert into database
        $query = "INSERT INTO users (user_type, first_name, last_name, email, password, phone, date_of_birth, 
                  gender, university_id, student_id, faculty, year_of_study, degree_program, 
                  address_line1, address_line2, city, district, postal_code, verification_token, is_active, is_verified) 
                  VALUES (:user_type, :first_name, :last_name, :email, :password, :phone, :date_of_birth, 
                  :gender, :university_id, :student_id, :faculty, :year_of_study, :degree_program, 
                  :address_line1, :address_line2, :city, :district, :postal_code, :verification_token, :is_active, :is_verified)";
        
        $result = $this->query($query, $data);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'User registered successfully', 'verification_token' => $data['verification_token']];
        } else {
            return ['success' => false, 'errors' => ['general' => 'Registration failed. Please try again.']];
        }
    }
    
    public function authenticate($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1";
        $user = $this->getRow($query, ['email' => $email]);
        
        if ($user && password_verify($password, $user->password)) {
            // Update last login
            $this->query("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id", ['id' => $user->id]);
            
            // Remove sensitive data
            unset($user->password);
            unset($user->verification_token);
            
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    public function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $result = $this->getRow($query, ['email' => $email]);
        return $result !== false;
    }
    
    public function getUserById($id) {
        $query = "SELECT u.*, un.name as university_name FROM users u 
                  LEFT JOIN universities un ON u.university_id = un.id 
                  WHERE u.id = :id AND u.is_active = 1 LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
    
    public function getUserByEmail($email) {
        $query = "SELECT u.*, un.name as university_name FROM users u 
                  LEFT JOIN universities un ON u.university_id = un.id 
                  WHERE u.email = :email AND u.is_active = 1 LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }
    
    public function updateProfile($id, $data) {
        $allowedFields = ['first_name', 'last_name', 'phone', 'date_of_birth', 'gender', 
                         'university_id', 'student_id', 'faculty', 'year_of_study', 'degree_program',
                         'address_line1', 'address_line2', 'city', 'district', 'postal_code'];
        
        $updateFields = [];
        $updateData = ['id' => $id];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = :$field";
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateFields)) {
            return ['success' => false, 'message' => 'No fields to update'];
        }
        
        $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $result = $this->query($query, $updateData);
        
        return ['success' => $result !== false, 'message' => $result !== false ? 'Profile updated successfully' : 'Update failed'];
    }
    
    public function verifyEmail($token) {
        $query = "UPDATE users SET is_verified = 1, email_verified_at = CURRENT_TIMESTAMP, verification_token = NULL 
                  WHERE verification_token = :token AND is_active = 1";
        $result = $this->query($query, ['token' => $token]);
        
        return ['success' => $result !== false, 'message' => $result !== false ? 'Email verified successfully' : 'Invalid verification token'];
    }
    
    public function changePassword($id, $currentPassword, $newPassword) {
        // Get current password
        $user = $this->getRow("SELECT password FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user || !password_verify($currentPassword, $user->password)) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->query("UPDATE users SET password = :password WHERE id = :id", 
                              ['password' => $hashedPassword, 'id' => $id]);
        
        return ['success' => $result !== false, 'message' => $result !== false ? 'Password changed successfully' : 'Password change failed'];
    }
    
    public function getUniversities() {
        $query = "SELECT id, name, short_name, code FROM universities WHERE is_active = 1 ORDER BY name";
        return $this->query($query);
    }
    
    public function createFromRegistration($email, $password, $user_type, $user_id) {
        try {
            $data = [
                'email' => strtolower(trim($email)),
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'user_type' => $user_type,
                'user_id' => $user_id,
                'is_active' => 1,
                'is_verified' => 0,
                'verification_token' => bin2hex(random_bytes(32)),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            return parent::create($data);
        } catch (Exception $e) {
            error_log("Error creating user from registration: " . $e->getMessage());
            return false;
        }
    }
}
