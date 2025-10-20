<?php

class Sponsor {
    
    use Model;
    protected $table = 'sponsors';
    
    public function create($data) {
        $query = "INSERT INTO sponsors (
            company_name, email, phone, country_code, password_hash
        ) VALUES (
            :company_name, :email, :phone, :country_code, :password_hash
        )";
        
        $result = $this->query($query, $data);
        if ($result !== false) {
            // Get the connection to retrieve last insert ID
            $conn = $this->connect();
            return $conn->lastInsertId();
        }
        return false;
    }
    
    public function findByEmail($email) {
        $query = "SELECT * FROM sponsors WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }
    
    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    public function validateData($data) {
        $errors = [];
        
        // Required fields validation
        $requiredFields = [
            'name' => 'Company/Individual Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password'
        ];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "$label is required";
            }
        }
        
        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }
        
        // Password validation
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }
            if ($data['password'] !== $data['confirm-password']) {
                $errors[] = "Passwords do not match";
            }
        }
        
        // Phone validation
        if (!empty($data['phone']) && !preg_match('/^[0-9]{9,10}$/', $data['phone'])) {
            $errors[] = "Please enter a valid phone number";
        }
        
        // Check if email already exists
        if (!empty($data['email']) && $this->emailExists($data['email'])) {
            $errors[] = "An account with this email already exists";
        }
        
        return $errors;
    }
    
    public function prepareDataForInsert($data) {
        return [
            'company_name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'country_code' => $data['country-code'] ?? '+94',
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];
    }
    
    public function getAllSponsors() {
        $query = "SELECT 
            s.id,
            s.company_name,
            s.email,
            s.phone,
            s.country_code,
            s.created_at,
            u.last_login,
            CASE 
                WHEN u.last_login IS NULL THEN 'Never'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'Active'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Recently Active'
                ELSE 'Inactive'
            END as activity_status
        FROM sponsors s
        LEFT JOIN users u ON s.id = u.user_id AND u.user_type = 'sponsor'
        ORDER BY s.created_at DESC";
        
        return $this->query($query);
    }
    
    public function getSponsorById($id) {
        $query = "SELECT 
            s.*,
            u.last_login,
            CASE 
                WHEN u.last_login IS NULL THEN 'Never'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'Active'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Recently Active'
                ELSE 'Inactive'
            END as activity_status
        FROM sponsors s
        LEFT JOIN users u ON s.id = u.user_id AND u.user_type = 'sponsor'
        WHERE s.id = :id";
        
        return $this->getRow($query, ['id' => $id]);
    }
    
    public function getSponsorStats() {
        $query = "SELECT 
            COUNT(*) as total_sponsors,
            COUNT(CASE WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_sponsors,
            COUNT(CASE WHEN s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_sponsors
        FROM sponsors s
        LEFT JOIN users u ON s.id = u.user_id AND u.user_type = 'sponsor'";
        
        return $this->getRow($query);
    }
}
