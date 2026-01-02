<?php

class Publisher {
    
    use Model;
    protected $table = 'publishers';
    
    public function create($data) {
        try {
            $query = "INSERT INTO publishers (
                society_name, email, phone, country_code, password_hash, 
                university, faculty, confirmation_document, approval_status
            ) VALUES (
                :society_name, :email, :phone, :country_code, :password_hash,
                :university, :faculty, :confirmation_document, 'pending'
            )";
            
            // Use direct database connection for INSERT operations
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            $result = $stmt->execute($data);
            
            if ($result) {
                $publisherId = $conn->lastInsertId();
                return $publisherId ? (int)$publisherId : false;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error creating publisher: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function findByEmail($email) {
        $query = "SELECT * FROM publishers WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }
    
    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    public function getRecentRegistrations($limit = 10) {
        $limit = (int)$limit; // Ensure it's an integer
        $query = "SELECT 
            id,
            society_name as name,
            email,
            created_at,
            approval_status,
            is_suspended,
            suspension_reason,
            'publisher' as user_type
        FROM publishers 
        ORDER BY created_at DESC 
        LIMIT {$limit}";
        
        return $this->query($query, []);
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
    
    /**
     * Get pending publisher registrations for approval by university
     */
    public function getPendingByUniversity($university) {
        $query = "SELECT * FROM publishers WHERE university = :university AND approval_status = 'pending' ORDER BY created_at ASC";
        return $this->query($query, ['university' => $university]);
    }
    
    /**
     * Get all pending publisher registrations
     */
    public function getAllPending() {
        $query = "SELECT * FROM publishers 
                  WHERE approval_status = 'pending' 
                  ORDER BY created_at ASC";
        return $this->query($query);
    }
    
    /**
     * Approve a publisher registration
     */
    public function approve($publisherId, $moderatorId) {
        $query = "UPDATE publishers SET 
                  approval_status = 'approved', 
                  approved_by = :moderator_id, 
                  approved_at = CURRENT_TIMESTAMP,
                  is_active = TRUE
                  WHERE id = :publisher_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'publisher_id' => $publisherId,
            'moderator_id' => $moderatorId
        ]);
        
        if ($result && $stm->rowCount() > 0) {
            // Create notification
            $this->createApprovalNotification($publisherId, $moderatorId, 'approved');
            return true;
        }
        return false;
    }
    
    /**
     * Reject a publisher registration
     */
    public function reject($publisherId, $moderatorId, $reason = '') {
        $query = "UPDATE publishers SET 
                  approval_status = 'rejected', 
                  approved_by = :moderator_id, 
                  approved_at = CURRENT_TIMESTAMP,
                  rejection_reason = :reason,
                  is_active = FALSE
                  WHERE id = :publisher_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'publisher_id' => $publisherId,
            'moderator_id' => $moderatorId,
            'reason' => $reason
        ]);
        
        if ($result && $stm->rowCount() > 0) {
            // Create notification
            $this->createApprovalNotification($publisherId, $moderatorId, 'rejected', $reason);
            return true;
        }
        return false;
    }
    
    /**
     * Get publisher by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM publishers WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
    
    /**
     * Check if publisher is approved and active
     */
    public function isApprovedAndActive($publisherId) {
        $query = "SELECT approval_status, is_active FROM publishers WHERE id = :id LIMIT 1";
        $publisher = $this->getRow($query, ['id' => $publisherId]);
        
        return $publisher && $publisher['approval_status'] === 'approved' && $publisher['is_active'] == 1;
    }
    
    /**
     * Create approval notification
     */
    public function createApprovalNotification($publisherId, $moderatorId, $type, $message = '') {
        // Validate required parameters
        if (empty($publisherId) || empty($moderatorId) || empty($type)) {
            error_log("Invalid parameters for createApprovalNotification: publisherId=$publisherId, moderatorId=$moderatorId, type=$type");
            return false;
        }
        
        try {
            $query = "INSERT INTO publisher_approval_notifications 
                      (publisher_id, moderator_id, notification_type, message) 
                      VALUES (:publisher_id, :moderator_id, :type, :message)";
            
            // Use direct database connection for INSERT operations
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute([
                'publisher_id' => $publisherId,
                'moderator_id' => $moderatorId,
                'type' => $type,
                'message' => $message
            ]);
        } catch (Exception $e) {
            error_log("Error creating approval notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get publisher statistics for moderator dashboard
     */
    public function getStatsByUniversity($university) {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected
                  FROM publishers 
                  WHERE university = :university";
        
        return $this->getRow($query, ['university' => $university]);
    }
    
    /**
     * Get recent pending publishers for moderator dashboard
     */
    public function getRecentPendingForUniversity($university, $limit = 5) {
        $query = "SELECT * FROM publishers 
                  WHERE university = :university 
                  AND approval_status = 'pending' 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        return $this->query($query, [
            'university' => $university,
            'limit' => $limit
        ]);
    }
    
    /**
     * Get count of pending publisher approvals for a university
     */
    public function getPendingCountByUniversity($university) {
        $query = "SELECT COUNT(*) as count FROM publishers 
                  WHERE university = :university AND approval_status = 'pending'";
        
        $result = $this->getRow($query, ['university' => $university]);
        return $result ? (int)$result->count : 0;
    }
    
    /**
     * Get all approved publishers by university
     */
    public function getApprovedByUniversity($university) {
        $query = "SELECT * FROM publishers 
                  WHERE university = :university 
                  AND approval_status = 'approved' 
                  ORDER BY society_name ASC";
        
        return $this->query($query, ['university' => $university]);
    }
    
    /**
     * Get a publisher by ID
     */
    public function getPublisherById($id) {
        $query = "SELECT * FROM publishers WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
}
