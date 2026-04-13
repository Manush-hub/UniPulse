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
            $sponsorId = $conn->lastInsertId();
            
            // Automatically create sponsor_profiles entry
            if ($sponsorId) {
                $this->createEmptyProfile($sponsorId);
            }
            
            return $sponsorId;
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
    
    public function getRecentRegistrations($limit = 10) {
        $limit = (int)$limit; // Ensure it's an integer
        $query = "SELECT 
            id,
            company_name as name,
            email,
            created_at,
            verification_status,
            is_suspended,
            suspension_reason,
            'sponsor' as user_type
        FROM sponsors 
        ORDER BY created_at DESC 
        LIMIT {$limit}";
        
        return $this->query($query, []);
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
            // 'verification_status' => 'Verified' // Default to verified for simplicity, can be changed based on actual verification process
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
            sp.logo_url,
            sp.cover_photo_url,
            CASE 
                WHEN u.last_login IS NULL THEN 'Never'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'Active'
                WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Recently Active'
                ELSE 'Inactive'
            END as activity_status
        FROM sponsors s
        LEFT JOIN users u ON s.id = u.user_id AND u.user_type = 'sponsor'
        LEFT JOIN sponsor_profiles sp ON s.id = sp.sponsor_id
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

    /**
     * Find sponsor by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM sponsors WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }

    /**
     * Get profile data for sponsor
     */
    public function getProfileData($sponsorId) {
        // First check if profile exists
        $query = "SELECT * FROM sponsor_profiles WHERE sponsor_id = :sponsor_id LIMIT 1";
        $profile = $this->getRow($query, ['sponsor_id' => $sponsorId]);

        if (!$profile) {
            // Create empty profile if doesn't exist
            $this->createEmptyProfile($sponsorId);
            $profile = $this->getRow($query, ['sponsor_id' => $sponsorId]);
        }

        return $profile;
    }

    /**
     * Create empty profile for sponsor
     * Made public so it can be called during registration
     */
    public function createEmptyProfile($sponsorId) {
        try {
            $query = "INSERT INTO sponsor_profiles (sponsor_id) VALUES (:sponsor_id)";
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute(['sponsor_id' => $sponsorId]);
        } catch (Exception $e) {
            error_log("Error creating empty sponsor profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update basic sponsor info (in sponsors table)
     */
    public function updateBasicInfo($sponsorId, $data) {
        $updates = [];
        $params = ['sponsor_id' => $sponsorId];
        
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        if (empty($updates)) {
            return true;
        }
        
        $query = "UPDATE sponsors SET " . implode(', ', $updates) . " WHERE id = :sponsor_id";
        
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Error updating sponsor basic info: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update profile data (in sponsor_profiles table)
     */
    public function updateProfileData($sponsorId, $data) {
        error_log("Sponsor::updateProfileData called - Sponsor ID: $sponsorId, Data: " . print_r($data, true));
        
        // Check if profile exists
        $existingProfile = $this->getRow(
            "SELECT id FROM sponsor_profiles WHERE sponsor_id = :sponsor_id",
            ['sponsor_id' => $sponsorId]
        );

        error_log("Sponsor::updateProfileData - Existing profile: " . print_r($existingProfile, true));

        if (!$existingProfile) {
            // Create profile first
            error_log("Sponsor::updateProfileData - Creating empty profile first");
            $this->createEmptyProfile($sponsorId);
        }

        $updates = [];
        $params = ['sponsor_id' => $sponsorId];
        
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        if (empty($updates)) {
            error_log("Sponsor::updateProfileData - No updates to perform");
            return true;
        }
        
        $query = "UPDATE sponsor_profiles SET " . implode(', ', $updates) . " WHERE sponsor_id = :sponsor_id";
        
        error_log("Sponsor::updateProfileData - Query: $query");
        error_log("Sponsor::updateProfileData - Params: " . print_r($params, true));
        
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            $result = $stmt->execute($params);
            error_log("Sponsor::updateProfileData - Result: " . ($result ? 'success' : 'failed'));
            error_log("Sponsor::updateProfileData - Affected rows: " . $stmt->rowCount());
            return $result;
        } catch (Exception $e) {
            error_log("Error updating sponsor profile data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sponsored events for a sponsor
     */
    public function getSponsoredEvents($sponsorId) {
        $query = "SELECT 
            e.*,
            es.id as sponsorship_id,
            es.amount as sponsored_amount,
            es.status as sponsorship_status,
            esp.package_name,
            esp.package_type,
            p.society_name as publisher_name,
            pp.logo_url as publisher_logo
        FROM event_sponsorships es
        INNER JOIN events e ON es.event_id = e.id
        INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
        LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
        LEFT JOIN publisher_profiles pp ON p.id = pp.publisher_id
        WHERE es.sponsor_id = :sponsor_id 
        AND es.status IN ('approved', 'completed')
        AND e.deleted_at IS NULL
        ORDER BY e.event_date DESC";
        
        try {
            $rows = $this->query($query, ['sponsor_id' => $sponsorId]);
            return is_array($rows) ? $rows : [];
        } catch (Exception $e) {
            error_log("Error fetching sponsored events: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Permanently delete sponsor account and related data.
     */
    public function deleteAccount($sponsorId) {
        $sponsorId = (int) $sponsorId;

        if ($sponsorId <= 0) {
            return false;
        }

        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            // Remove related rows that are known to reference sponsor ids without FK constraints.
            $stmt = $conn->prepare("DELETE FROM event_sponsorships WHERE sponsor_id = :sponsor_id AND sponsor_type = 'sponsor'");
            $stmt->execute(['sponsor_id' => $sponsorId]);

            $stmt = $conn->prepare("DELETE FROM users WHERE user_type = 'sponsor' AND user_id = :sponsor_id");
            $stmt->execute(['sponsor_id' => $sponsorId]);

            // sponsor_profiles has FK ON DELETE CASCADE in migration, but this keeps compatibility if FK is missing.
            $stmt = $conn->prepare("DELETE FROM sponsor_profiles WHERE sponsor_id = :sponsor_id");
            $stmt->execute(['sponsor_id' => $sponsorId]);

            $stmt = $conn->prepare("DELETE FROM sponsors WHERE id = :sponsor_id");
            $stmt->execute(['sponsor_id' => $sponsorId]);

            if ($stmt->rowCount() < 1) {
                $conn->rollBack();
                return false;
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Error deleting sponsor account: " . $e->getMessage());
            return false;
        }
    }
}
