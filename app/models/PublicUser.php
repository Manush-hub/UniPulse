<?php

class PublicUser {
    
    use Model;
    protected $table = 'public_users';
    
    public function create($data) {
        $query = "INSERT INTO public_users (
            full_name, email, phone, country_code, password_hash, 
            nic, gender, interests
        ) VALUES (
            :full_name, :email, :phone, :country_code, :password_hash,
            :nic, :gender, :interests
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
        $query = "SELECT * FROM public_users WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }
    
    public function findByNIC($nic) {
        $query = "SELECT * FROM public_users WHERE nic = :nic LIMIT 1";
        return $this->getRow($query, ['nic' => $nic]);
    }
    
    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    public function nicExists($nic) {
        $user = $this->findByNIC($nic);
        return $user !== false;
    }
    
    public function validateData($data) {
        $errors = [];
        
        // Required fields validation
        $requiredFields = [
            'full-name' => 'Full Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'nic' => 'NIC'
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
        
        // NIC validation (Sri Lankan format)
        if (!empty($data['nic'])) {
            if (!preg_match('/^([0-9]{9}[xXvV]|[0-9]{12})$/', $data['nic'])) {
                $errors[] = "Please enter a valid NIC number";
            }
        }
        
        // Check if email already exists
        if (!empty($data['email']) && $this->emailExists($data['email'])) {
            $errors[] = "An account with this email already exists";
        }
        
        // Check if NIC already exists
        if (!empty($data['nic']) && $this->nicExists($data['nic'])) {
            $errors[] = "An account with this NIC already exists";
        }
        
        return $errors;
    }
    
    public function prepareDataForInsert($data) {
        $interests = [];
        if (isset($data['interests']) && is_array($data['interests'])) {
            $interests = $data['interests'];
        }
        
        return [
            'full_name' => trim($data['full-name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'country_code' => $data['country-code'] ?? '+94',
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'nic' => strtoupper(trim($data['nic'])),
            'gender' => !empty($data['gender']) ? $data['gender'] : null,
            'interests' => !empty($interests) ? json_encode($interests) : null
        ];
    }
}
