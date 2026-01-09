<?php

class Publisher
{

    use Model;
    protected $table = 'publishers';

    public function create($data)
    {
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

    public function findByEmail($email)
    {
        $query = "SELECT * FROM publishers WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }

    public function emailExists($email)
    {
        $user = $this->findByEmail($email);
        return $user !== false;
    }

    public function getRecentRegistrations($limit = 10)
    {
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

    public function validateData($data)
    {
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

    public function prepareDataForInsert($data)
    {
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

    private function handleFileUpload($file)
    {
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
    public function getPendingByUniversity($university)
    {
        $query = "SELECT * FROM publishers WHERE university = :university AND approval_status = 'pending' ORDER BY created_at ASC";
        return $this->query($query, ['university' => $university]);
    }

    /**
     * Get all pending publisher registrations
     */
    public function getAllPending()
    {
        $query = "SELECT * FROM publishers 
                  WHERE approval_status = 'pending' 
                  ORDER BY created_at ASC";
        return $this->query($query);
    }

    /**
     * Approve a publisher registration
     */
    public function approve($publisherId, $moderatorId)
    {
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
    public function reject($publisherId, $moderatorId, $reason = '')
    {
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
    public function findById($id)
    {
        $query = "SELECT * FROM publishers WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }

    /**
     * Check if publisher is approved and active
     */
    public function isApprovedAndActive($publisherId)
    {
        $query = "SELECT approval_status, is_active FROM publishers WHERE id = :id LIMIT 1";
        $publisher = $this->getRow($query, ['id' => $publisherId]);

        return $publisher && $publisher['approval_status'] === 'approved' && $publisher['is_active'] == 1;
    }

    /**
     * Create approval notification
     */
    public function createApprovalNotification($publisherId, $moderatorId, $type, $message = '')
    {
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
    public function getStatsByUniversity($university)
    {
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
    public function getRecentPendingForUniversity($university, $limit = 5)
    {
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
    public function getPendingCountByUniversity($university)
    {
        $query = "SELECT COUNT(*) as count FROM publishers 
                  WHERE university = :university AND approval_status = 'pending'";

        $result = $this->getRow($query, ['university' => $university]);
        return $result ? (int)$result->count : 0;
    }

    /**
     * Get profile data for a publisher
     */
    public function getProfileData($publisherId)
    {
        // First check if profile exists
        $query = "SELECT * FROM publisher_profiles WHERE publisher_id = :publisher_id LIMIT 1";
        $profile = $this->getRow($query, ['publisher_id' => $publisherId]);

        if (!$profile) {
            // Create empty profile if doesn't exist
            $this->createEmptyProfile($publisherId);
            $profile = $this->getRow($query, ['publisher_id' => $publisherId]);
        }

        return $profile;
    }

    /**
     * Create empty profile for publisher
     */
    private function createEmptyProfile($publisherId)
    {
        try {
            $query = "INSERT INTO publisher_profiles (publisher_id) VALUES (:publisher_id)";
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute(['publisher_id' => $publisherId]);
        } catch (Exception $e) {
            error_log("Error creating empty profile: " . $e->getMessage());
            // Return a stdClass object with default values if insert fails
            $defaultProfile = new stdClass();
            $defaultProfile->publisher_id = $publisherId;
            $defaultProfile->org_type = null;
            $defaultProfile->address = null;
            $defaultProfile->established_year = null;
            $defaultProfile->member_count = null;
            $defaultProfile->headline = null;
            $defaultProfile->bio = null;
            $defaultProfile->mission = null;
            $defaultProfile->website = null;
            $defaultProfile->facebook = null;
            $defaultProfile->instagram = null;
            $defaultProfile->linkedin = null;
            $defaultProfile->twitter = null;
            $defaultProfile->discord = null;
            $defaultProfile->youtube = null;
            $defaultProfile->logo_url = null;
            $defaultProfile->cover_photo_url = null;
            return $defaultProfile;
        }
    }

    /**
     * Update basic publisher information
     */
    public function updateBasicInfo($publisherId, $data)
    {
        $allowedFields = ['society_name', 'phone', 'university', 'faculty'];

        $updateFields = [];
        $updateData = ['id' => $publisherId];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = :$field";
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            return true; // No fields to update
        }

        $query = "UPDATE publishers SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";

        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute($updateData);
        } catch (Exception $e) {
            error_log("Error updating basic info: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update publisher profile data
     */
    public function updateProfileData($publisherId, $data)
    {
        $allowedFields = [
            'org_type',
            'address',
            'established_year',
            'member_count',
            'headline',
            'bio',
            'mission',
            'website',
            'facebook',
            'instagram',
            'linkedin',
            'twitter',
            'discord',
            'youtube',
            'logo_url',
            'cover_photo_url',
            'preferences'
        ];

        $updateFields = [];
        $updateData = ['publisher_id' => $publisherId];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = :$field";
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            return true; // No fields to update
        }

        // Check if profile exists
        $existsQuery = "SELECT id FROM publisher_profiles WHERE publisher_id = :publisher_id";
        $exists = $this->getRow($existsQuery, ['publisher_id' => $publisherId]);

        if (!$exists) {
            $this->createEmptyProfile($publisherId);
        }

        $query = "UPDATE publisher_profiles SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP WHERE publisher_id = :publisher_id";

        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute($updateData);
        } catch (Exception $e) {
            error_log("Error updating profile data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload and save image
     */
    public function uploadImage($file, $type, $publisherId)
    {
        error_log("uploadImage called with type: $type, publisherId: $publisherId");
        error_log("File info: " . print_r($file, true));

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            error_log("Invalid file type: " . $file['type']);
            return false;
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            error_log("File too large: " . $file['size']);
            return false;
        }

        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../public/uploads/publisher_images/' . $publisherId . '/';
        error_log("Upload directory: " . $uploadDir);

        if (!file_exists($uploadDir)) {
            $result = mkdir($uploadDir, 0755, true);
            error_log("Directory created: " . ($result ? 'YES' : 'NO'));
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;
        error_log("Target file path: " . $filePath);

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Return relative URL
            $url = '/UniPulse/public/uploads/publisher_images/' . $publisherId . '/' . $filename;
            error_log("Upload successful, URL: " . $url);
            return $url;
        }

        error_log("move_uploaded_file failed");
        return false;
    }

    // ==================== GALLERY METHODS ====================

    /**
     * Get all galleries for a specific publisher
     */
    public function getPublisherGalleries($publisherId)
    {
        $query = "SELECT 
                    id,
                    publisher_id,
                    title,
                    description,
                    images,
                    created_at,
                    updated_at
                FROM publisher_profiles_gallery
                WHERE publisher_id = :publisher_id
                ORDER BY created_at DESC";

        $galleries = $this->query($query, ['publisher_id' => $publisherId]);

        // Decode JSON images for each gallery
        if ($galleries) {
            $result = [];
            foreach ($galleries as $gallery) {
                // Convert object to array
                $galleryArray = (array) $gallery;
                $galleryArray['images'] = json_decode($galleryArray['images'], true) ?? [];
                $galleryArray['photo_count'] = count($galleryArray['images']);
                $result[] = $galleryArray;
            }
            return $result;
        }

        return [];
    }

    /**
     * Get a specific gallery by ID with its images
     */
    public function getGalleryById($galleryId)
    {
        $query = "SELECT * FROM publisher_profiles_gallery WHERE id = :id LIMIT 1";
        $result = $this->query($query, ['id' => $galleryId]);
        $gallery = $result ? $result[0] : null;

        if ($gallery) {
            // Convert object to array
            $gallery = (array) $gallery;
            $gallery['images'] = json_decode($gallery['images'], true) ?? [];
        }

        return $gallery;
    }

    /**
     * Create a new gallery with images
     */
    public function createGallery($publisherId, $title, $description, $imageUrls)
    {
        try {
            error_log("Publisher::createGallery called");
            error_log("Publisher ID: " . $publisherId);
            error_log("Title: " . $title);
            error_log("Description: " . $description);
            error_log("Image URLs: " . json_encode($imageUrls));

            $conn = $this->connect();
            error_log("Database connected");

            // Encode images as JSON
            $imagesJson = json_encode($imageUrls);
            error_log("Images JSON: " . $imagesJson);

            // Insert gallery
            $query = "INSERT INTO publisher_profiles_gallery (publisher_id, title, description, images) 
                    VALUES (:publisher_id, :title, :description, :images)";

            error_log("SQL Query: " . $query);

            $stmt = $conn->prepare($query);
            error_log("Statement prepared");

            $params = [
                'publisher_id' => $publisherId,
                'title' => $title,
                'description' => $description,
                'images' => $imagesJson
            ];
            error_log("Params: " . json_encode($params));

            $result = $stmt->execute($params);
            error_log("Execute result: " . ($result ? 'true' : 'false'));

            $lastId = $conn->lastInsertId();
            error_log("Last insert ID: " . $lastId);

            return $lastId;
        } catch (Exception $e) {
            error_log("ERROR in createGallery: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Update an existing gallery
     */
    public function updateGallery($galleryId, $title, $description, $keepImageUrls = [], $newImageUrls = [])
    {
        try {
            $conn = $this->connect();

            // Get current gallery
            $gallery = $this->getGalleryById($galleryId);
            if (!$gallery) {
                throw new Exception("Gallery not found");
            }

            // Ensure gallery is array
            if (is_object($gallery)) {
                $gallery = (array) $gallery;
            }

            $currentImages = $gallery['images'] ?? [];

            // Delete physical files that are not being kept
            foreach ($currentImages as $imageUrl) {
                if (!in_array($imageUrl, $keepImageUrls)) {
                    $this->deleteGalleryFile($imageUrl);
                }
            }

            // Merge kept images with new images
            $updatedImages = array_merge($keepImageUrls, $newImageUrls);

            // Validate total images count (max 10)
            if (count($updatedImages) > 10) {
                throw new Exception("Maximum 10 photos allowed per gallery");
            }

            // Update gallery
            $imagesJson = json_encode($updatedImages);

            $query = "UPDATE publisher_profiles_gallery 
                    SET title = :title, description = :description, images = :images, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                'id' => $galleryId,
                'title' => $title,
                'description' => $description,
                'images' => $imagesJson
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error updating gallery: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a gallery and all its images
     */
    public function deleteGallery($galleryId)
    {
        try {
            $conn = $this->connect();

            // Get gallery with images before deletion
            $gallery = $this->getGalleryById($galleryId);

            if (!$gallery) {
                throw new Exception("Gallery not found");
            }

            // Ensure gallery is array
            if (is_object($gallery)) {
                $gallery = (array) $gallery;
            }

            // Delete gallery from database
            $deleteGalleryQuery = "DELETE FROM publisher_profiles_gallery WHERE id = :id";
            $stmt = $conn->prepare($deleteGalleryQuery);
            $stmt->execute(['id' => $galleryId]);

            // Delete physical files
            if (!empty($gallery['images'])) {
                foreach ($gallery['images'] as $imageUrl) {
                    $this->deleteGalleryFile($imageUrl);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error deleting gallery: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete physical gallery file from server
     */
    private function deleteGalleryFile($imageUrl)
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $imageUrl;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // ==================== EVENT METHODS ====================

    /**
     * Get upcoming events for a specific publisher
     */
    public function getUpcomingEvents($publisherId)
    {
        $query = "SELECT e.*, p.society_name as organizer_name 
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                AND e.event_date >= CURDATE()
                ORDER BY e.event_date ASC, e.event_time ASC";

        $events = $this->query($query, ['publisher_id' => $publisherId]);
        return $events ?: [];
    }

    /**
     * Get past events for a specific publisher
     */
    public function getPastEvents($publisherId)
    {
        $query = "SELECT e.*, p.society_name as organizer_name 
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                AND e.event_date < CURDATE()
                ORDER BY e.event_date DESC, e.event_time DESC";

        $events = $this->query($query, ['publisher_id' => $publisherId]);
        return $events ?: [];
    }

    /**
     * Get all events for a specific publisher (for profile display)
     */
    public function getAllPublisherEvents($publisherId)
    {
        $query = "SELECT e.*, p.society_name as organizer_name 
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                ORDER BY e.event_date DESC";

        $events = $this->query($query, ['publisher_id' => $publisherId]);
        return $events ?: [];
    }

    /**
     * Update publisher email
     */
    public function updateEmail($publisherId, $newEmail)
    {
        $query = "UPDATE publishers SET email = :email, updated_at = NOW() WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($query);
        return $stmt->execute(['email' => $newEmail, 'id' => $publisherId]);
    }

    /**
     * Update publisher password
     */
    public function updatePassword($publisherId, $newPassword)
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE publishers SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($query);
        return $stmt->execute(['password_hash' => $passwordHash, 'id' => $publisherId]);
    }

    /**
     * Get all approved publishers by university
     */
    public function getApprovedByUniversity($university)
    {
        $query = "SELECT * FROM publishers 
                  WHERE university = :university 
                  AND approval_status = 'approved' 
                  ORDER BY society_name ASC";

        return $this->query($query, ['university' => $university]);
    }


    /**
     * Get a publisher by ID
     */
    public function getPublisherById($id)
    {
        $query = "SELECT * FROM publishers WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
}
