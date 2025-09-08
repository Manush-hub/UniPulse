<?php

class Publisher {
    
    use Model;
    protected $table = 'publishers';
    
    public function create($data) {
        $query = "INSERT INTO publishers (
            society_name, email, phone, country_code, password_hash, 
            university, faculty, confirmation_document
        ) VALUES (
            :society_name, :email, :phone, :country_code, :password_hash,
            :university, :faculty, :confirmation_document
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
        $query = "SELECT * FROM publishers WHERE email = :email LIMIT 1";
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
            'society-name' => 'Society/Club Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'university' => 'University',
            'faculty' => 'Faculty'
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
        
        // File upload validation
        if (isset($_FILES['confirmation-file']) && $_FILES['confirmation-file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['confirmation-file'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            
            if ($file['size'] > $maxSize) {
                $errors[] = "File size must be less than 5MB";
            }
            
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                $errors[] = "File must be PDF, JPG, PNG, DOC, or DOCX format";
            }
        } else {
            $errors[] = "Confirmation document is required";
        }
        
        return $errors;
    }
    
    public function prepareDataForInsert($data) {
        // Handle file upload
        $documentPath = null;
        if (isset($_FILES['confirmation-file']) && $_FILES['confirmation-file']['error'] === UPLOAD_ERR_OK) {
            $documentPath = $this->handleFileUpload($_FILES['confirmation-file']);
        }
        
        return [
            'society_name' => trim($data['society-name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'country_code' => $data['country-code'] ?? '+94',
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'university' => $data['university'],
            'faculty' => $data['faculty'],
            'confirmation_document' => $documentPath
        ];
    }
    
    private function handleFileUpload($file) {
        $uploadDir = '../public/uploads/publisher_documents/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = 'publisher_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return 'uploads/publisher_documents/' . $fileName;
        }
        
        return null;
    }
}
