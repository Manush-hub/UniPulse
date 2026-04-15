<?php

class UserProfile extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        if (!AuthService::isLoggedIn()) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $currentUser = AuthService::getCurrentUser();

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        $this->view('User/profile', ['currentUser' => $currentUser]);
    }

    /**
     * Return current user's profile data as JSON
     */
    public function getProfile()
    {
        header('Content-Type: application/json');
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $u = AuthService::getCurrentUser();
        error_log('Current User: ' . json_encode($u));

        $profile = $this->loadProfileRecord($u);
        error_log('Loaded Profile: ' . json_encode($profile));

        // Map to view field IDs for easy binding
        $data = [
            'full_name' => $profile['full_name'] ?? ($u['name'] ?? ''),
            'email' => $profile['email'] ?? ($u['email'] ?? ''),
            'phone' => $profile['phone'] ?? '',
            'country_code' => $profile['country_code'] ?? '+94',
            'university' => $profile['university'] ?? ($u['university'] ?? ''),
            'faculty' => $profile['faculty'] ?? '',
            'student_staff_id' => $profile['student_staff_id'] ?? '',
            'academic_year' => $profile['academic_year'] ?? '',
            'date_of_birth' => $profile['date_of_birth'] ?? '',
            'gender' => $profile['gender'] ?? '',
            'current_city' => $profile['current_city'] ?? '',
            'home_town' => $profile['home_town'] ?? '',
            'role' => $u['type'] ?? 'public',
            'nic' => $profile['nic'] ?? '',
            'headline' => $profile['headline'] ?? '',
            'bio' => $profile['bio'] ?? '',
            'interests' => $profile['interests'] ?? '[]',
            'profile_photo' => $profile['profile_photo'] ?? '',
            'cover_photo' => $profile['cover_photo'] ?? ''
        ];

        error_log('Response Data: ' . json_encode($data));
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Update current user's profile from POSTed JSON or FormData
     * Only allows editing of: First Name, Last Name, Gender, Phone, Bio
     * Read-only fields: University, Faculty, Student/Staff ID, Email, NIC
     */
    public function updateProfile()
    {
        error_log('=== [Profile::updateProfile] CALLED ===');
        error_log('[Profile::updateProfile] Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET'));
        error_log('[Profile::updateProfile] Request Method: ' . $_SERVER['REQUEST_METHOD']);

        header('Content-Type: application/json');
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $u = AuthService::getCurrentUser();
        $payload = [];
        $fields = [];

        // Check if it's FormData (multipart/form-data) or JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Log what files are being uploaded
            error_log('[Profile::updateProfile] $_FILES: ' . json_encode(array_keys($_FILES)));

            // Handle file uploads
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                error_log('[Profile::updateProfile] Processing profile_photo');
                $file = $_FILES['profile_photo'];
                if ($this->isValidImageFile($file)) {
                    $imageData = $this->processImageUpload($file);
                    if ($imageData) {
                        $fields['profile_photo'] = $imageData;
                        error_log('[Profile::updateProfile] profile_photo processed successfully');
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to process profile photo']);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid profile photo file type']);
                    return;
                }
            }

            if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                error_log('[Profile::updateProfile] Processing cover_photo');
                $file = $_FILES['cover_photo'];
                if ($this->isValidImageFile($file)) {
                    $imageData = $this->processImageUpload($file);
                    if ($imageData) {
                        $fields['cover_photo'] = $imageData;
                        error_log('[Profile::updateProfile] cover_photo processed successfully: ' . strlen($imageData) . ' bytes');
                    } else {
                        error_log('[Profile::updateProfile] Failed to process cover_photo');
                        echo json_encode(['success' => false, 'error' => 'Failed to process cover photo']);
                        return;
                    }
                } else {
                    error_log('[Profile::updateProfile] Invalid cover_photo file type');
                    echo json_encode(['success' => false, 'error' => 'Invalid cover photo file type']);
                    return;
                }
            } else if (isset($_FILES['cover_photo'])) {
                error_log('[Profile::updateProfile] cover_photo error: ' . $_FILES['cover_photo']['error']);
            }

            // Handle other fields from POST
            if (!empty($_POST)) {
                $payload = $_POST;
            }
        } else {
            // Handle JSON
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Only validate if editing profile fields (not just uploading images)
        $isImageOnlyUpdate = empty($payload) && !empty($fields);
        if (!$isImageOnlyUpdate) {
            $errors = $this->validateEditableFields($payload);
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
        }

        $updated = false;
        try {
            error_log('[Profile::updateProfile] User: ' . json_encode($u));

            // Only editable fields for basic info
            $basicFields = [];

            // Accept full_name directly from form
            if (!empty($payload['full_name'])) {
                $basicFields['full_name'] = trim($payload['full_name']);
            }
            // Backward compatibility: still accept firstname/lastname and join them
            else if (!empty($payload['firstname']) || !empty($payload['lastname'])) {
                $fullName = $this->joinName($payload['firstname'] ?? '', $payload['lastname'] ?? '');
                if (!empty(trim($fullName))) {
                    $basicFields['full_name'] = $fullName;
                }
            }

            // Add other editable fields if provided
            if (isset($payload['phone']) && $payload['phone'] !== '') {
                $basicFields['phone'] = $payload['phone'];
            }
            if (isset($payload['country_code']) && $payload['country_code'] !== '') {
                $basicFields['country_code'] = $payload['country_code'];
            }
            if (isset($payload['gender']) && $payload['gender'] !== '') {
                $basicFields['gender'] = $payload['gender'];
            }
            if (isset($payload['date_of_birth']) && $payload['date_of_birth'] !== '') {
                $basicFields['date_of_birth'] = $payload['date_of_birth'];
            }
            if (isset($payload['headline']) && $payload['headline'] !== '') {
                $basicFields['headline'] = $payload['headline'];
            }
            if (isset($payload['academic_year']) && $payload['academic_year'] !== '') {
                $basicFields['academic_year'] = $payload['academic_year'];
            }
            if (isset($payload['current_city']) && $payload['current_city'] !== '') {
                $basicFields['current_city'] = $payload['current_city'];
            }
            if (isset($payload['home_town']) && $payload['home_town'] !== '') {
                $basicFields['home_town'] = $payload['home_town'];
            }
            if (isset($payload['bio']) && $payload['bio'] !== '') {
                $basicFields['bio'] = $payload['bio'];
            }

            // Handle interests (event preferences)
            if (isset($payload['interests'])) {
                // If it's already a JSON string, use it directly
                if (is_string($payload['interests'])) {
                    // Validate it's valid JSON
                    $decoded = json_decode($payload['interests']);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $basicFields['interests'] = $payload['interests'];
                    }
                }
                // If it's an array, encode it
                else if (is_array($payload['interests'])) {
                    $basicFields['interests'] = json_encode($payload['interests']);
                }
            }

            // Allow image updates if explicitly provided (do not expose in UI as editable fields)
            if (isset($payload['profile_photo'])) {
                $basicFields['profile_photo'] = $payload['profile_photo'];
            }
            if (isset($payload['cover_photo'])) {
                $basicFields['cover_photo'] = $payload['cover_photo'];
            }

            // Merge with file uploads
            $fields = array_merge($basicFields, $fields);

            // Remove null values
            $fields = array_filter($fields, fn($v) => $v !== null);

            error_log('[Profile::updateProfile] Fields to update: ' . json_encode(array_keys($fields)));
            error_log('[Profile::updateProfile] cover_photo in fields: ' . (isset($fields['cover_photo']) ? 'YES (' . strlen($fields['cover_photo']) . ' bytes)' : 'NO'));

            // Prevent empty update which would break SQL
            if (empty($fields)) {
                echo json_encode(['success' => false, 'error' => 'No fields to update']);
                return;
            }

            // Load appropriate model based on user type
            if ($u['type'] === 'university') {
                require_once __DIR__ . '/../../models/UniversityUser.php';
                $model = new UniversityUser();
            } else {
                require_once __DIR__ . '/../../models/PublicUser.php';
                $model = new PublicUser();
            }

            error_log('[Profile::updateProfile] Fields: ' . json_encode($fields));
            $updated = $model->update($u['id'], $fields);

            // If update didn't return an error but returned false, still consider it success
            // since we passed validation and reached the database
            $success = true;

            // Refresh session name if full_name changed
            if (!empty($fields['full_name'])) {
                $_SESSION['user_name'] = $fields['full_name'];
            }

            // Update session with profile photo if changed
            if (!empty($fields['profile_photo'])) {
                $_SESSION['user_profile_photo'] = $fields['profile_photo'];
            }

            // Update session with cover photo if changed
            if (!empty($fields['cover_photo'])) {
                $_SESSION['user_cover_photo'] = $fields['cover_photo'];
            }

            echo json_encode(['success' => $success, 'message' => 'Profile updated successfully']);
        } catch (Exception $e) {
            error_log('Profile update error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Change the current user's password.
     */
    public function changePassword()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        if (!in_array($currentUser['type'] ?? '', ['public', 'university'], true)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?? [];

        $currentPassword = (string)($payload['current_password'] ?? '');
        $newPassword = (string)($payload['new_password'] ?? '');
        $confirmPassword = (string)($payload['confirm_password'] ?? '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
            return;
        }

        $profile = $this->loadProfileRecord($currentUser);
        if (!$profile || empty($profile['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'User record not found']);
            return;
        }

        if (!password_verify($currentPassword, $profile['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            return;
        }

        if ($currentUser['type'] === 'university') {
            require_once __DIR__ . '/../../models/UniversityUser.php';
            $model = new UniversityUser();
            $table = 'university_users';
        } else {
            require_once __DIR__ . '/../../models/PublicUser.php';
            $model = new PublicUser();
            $table = 'public_users';
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $conn = $model->connect();
            $stmt = $conn->prepare("UPDATE {$table} SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id");
            $result = $stmt->execute([
                'password_hash' => $newPasswordHash,
                'id' => $currentUser['id']
            ]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to change password']);
            }
        } catch (Exception $e) {
            error_log('Change password error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to change password']);
        }
    }

    /**
     * Soft-delete current user account.
     */
    public function deleteAccount()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        $userId = (int)($currentUser['id'] ?? 0);
        $userType = $currentUser['type'] ?? '';

        if ($userId <= 0 || !in_array($userType, ['public', 'university'], true)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            if ($userType === 'university') {
                require_once __DIR__ . '/../../models/UniversityUser.php';
                $model = new UniversityUser();
            } else {
                require_once __DIR__ . '/../../models/PublicUser.php';
                $model = new PublicUser();
            }

            $deleted = $model->softDeleteAccount($userId);

            if ($deleted) {
                AuthService::logout();
                echo json_encode(['success' => true, 'message' => 'Account deactivated successfully']);
                return;
            }

            echo json_encode(['success' => false, 'message' => 'Failed to deactivate account']);
        } catch (Exception $e) {
            error_log('User soft delete error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to deactivate account']);
        }
    }

    /**
     * Validate if uploaded file is a valid image
     */
    private function isValidImageFile($file)
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return false;
        }

        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            return false;
        }

        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Process uploaded image and convert to base64
     */
    private function processImageUpload($file)
    {
        try {
            $imageData = file_get_contents($file['tmp_name']);
            if ($imageData === false) {
                return null;
            }

            // Convert to base64 for storage
            $base64Image = 'data:' . mime_content_type($file['tmp_name']) . ';base64,' . base64_encode($imageData);
            return $base64Image;
        } catch (Exception $e) {
            error_log('Error processing image upload: ' . $e->getMessage());
            return null;
        }
    }

    private function loadProfileRecord($u)
    {
        try {
            if ($u['type'] === 'university') {
                require_once __DIR__ . '/../../models/UniversityUser.php';
                $model = new UniversityUser();
                $row = $model->find($u['id']);
                return (array)$row;
            }
            // default public
            require_once __DIR__ . '/../../models/PublicUser.php';
            $model = new PublicUser();
            $row = $model->find($u['id']);
            return (array)$row;
        } catch (Exception $e) {
            error_log('Load profile error: ' . $e->getMessage());
            return [];
        }
    }

    private function splitName($fullName)
    {
        $fullName = trim((string)$fullName);
        if ($fullName === '') return ['first' => '', 'last' => ''];
        $parts = preg_split('/\s+/', $fullName, 2);
        return ['first' => $parts[0], 'last' => $parts[1] ?? ''];
    }

    private function joinName($first, $last)
    {
        $first = trim((string)$first);
        $last = trim((string)$last);
        return trim($first . ' ' . $last);
    }

    /**
     * Validate editable fields
     */
    private function validateEditableFields($payload)
    {
        $errors = [];

        // Validate full name (optional - only if provided)
        $fullName = trim($payload['full_name'] ?? '');
        if (!empty($fullName)) {
            if (strlen($fullName) < 2) {
                $errors[] = 'Full Name must be at least 2 characters';
            }
        }

        // Backward compatibility: validate first name (optional - only if provided)
        $firstname = trim($payload['firstname'] ?? '');
        if (!empty($firstname)) {
            if (strlen($firstname) < 2) {
                $errors[] = 'First Name must be at least 2 characters';
            }
        }

        // Validate last name (optional - only if provided)
        $lastname = trim($payload['lastname'] ?? '');
        if (!empty($lastname)) {
            if (strlen($lastname) < 2) {
                $errors[] = 'Last Name must be at least 2 characters';
            }
        }

        // Validate phone if provided
        if (!empty($payload['phone'] ?? null)) {
            $phone = trim($payload['phone']);
            // Allow phone numbers with digits, plus sign, hyphens, spaces, parentheses (10-20 chars)
            // Also allow phone numbers that are just digits (7-15 digits)
            if (!preg_match('/^[\d+\-\s\(\)]{10,20}$/', $phone) && !preg_match('/^\d{7,15}$/', $phone)) {
                $errors[] = 'Phone number format is invalid';
            }
        }

        // Validate gender if provided
        if (!empty($payload['gender'])) {
            $validGenders = ['male', 'female', 'other', 'prefer-not-to-say'];
            if (!in_array($payload['gender'], $validGenders)) {
                $errors[] = 'Invalid gender selection';
            }
        }

        // Validate date of birth if provided
        if (!empty($payload['date_of_birth'])) {
            $date = $payload['date_of_birth'];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = 'Invalid date of birth format';
            }
        }

        // Validate headline if provided
        if (!empty($payload['headline'])) {
            if (strlen($payload['headline']) > 255) {
                $errors[] = 'Headline must be 255 characters or less';
            }
        }

        return $errors;
    }

    /**
     * Get user's gallery data
     */
    public function getGallery()
    {
        header('Content-Type: application/json');
        error_log('=== getGallery() CALLED ===');

        if (!AuthService::isLoggedIn()) {
            error_log('getGallery: User not authenticated');
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $u = AuthService::getCurrentUser();
        $userId = $u['id'];
        $userType = $u['type'] ?? 'university';

        error_log("getGallery: User ID=$userId, Type=$userType");

        // Determine which table to query based on user type
        $tableName = ($userType === 'public') ? 'public_users' : 'university_users';
        error_log("getGallery: Querying table: $tableName");

        try {
            // Create PDO connection directly
            $conn = new PDO(
                "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4",
                DBUSER,
                DBPASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Ensure gallery column exists to avoid SQL errors
            $this->ensureGalleryColumn($conn, $tableName);

            $query = "SELECT id, gallery FROM {$tableName} WHERE id = :user_id";
            error_log("getGallery: Executing query: $query");

            $stmt = $conn->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            error_log("getGallery: Query result: " . json_encode($result));

            $gallery = [];
            if ($result && isset($result[0])) {
                $galleryData = $result[0]->gallery ?? null;
                error_log("getGallery: Raw gallery data from DB (first 100 chars): " . substr((string)$galleryData, 0, 100));

                if (!empty($galleryData)) {
                    $gallery = json_decode($galleryData, true) ?? [];
                    error_log("getGallery: Decoded gallery - " . count($gallery) . " albums");
                } else {
                    error_log("getGallery: Gallery column is empty/null");
                }
            } else {
                error_log("getGallery: No result found or empty result");
            }

            echo json_encode(['success' => true, 'gallery' => $gallery]);
            error_log('=== getGallery() COMPLETE ===');
        } catch (Exception $e) {
            error_log('getGallery: Exception - ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode(['success' => true, 'gallery' => []]);
        }
    }

    /**
     * Update user's gallery data
     */
    public function updateGallery()
    {
        // Start output buffering to catch any stray output
        ob_start();

        header('Content-Type: application/json');

        // Debug: Log the request received
        error_log('=== updateGallery() CALLED ===');
        error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        error_log('CONTENT_TYPE: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));

        if (!AuthService::isLoggedIn()) {
            error_log('updateGallery: User not authenticated');
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        error_log('updateGallery: User authenticated, reading input');
        $rawInput = file_get_contents('php://input');
        error_log('Raw input length: ' . strlen($rawInput));
        error_log('Raw input preview: ' . substr($rawInput, 0, 100));

        $input = json_decode($rawInput, true);
        error_log('updateGallery: Input decoded, keys: ' . json_encode(array_keys($input ?? [])));

        if (!isset($input['gallery'])) {
            error_log('updateGallery: No gallery data provided');
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'No gallery data provided']);
            exit;
        }

        $u = AuthService::getCurrentUser();
        $userId = $u['id'];
        $userType = $u['type'] ?? 'university';
        $gallery = $input['gallery'];

        error_log("updateGallery: User ID=$userId, Type=$userType, Gallery items=" . count($gallery));
        error_log("updateGallery: Full user data: " . json_encode($u));

        // Validate gallery data
        if (!is_array($gallery)) {
            error_log('updateGallery: Invalid gallery data - not an array');
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Invalid gallery data']);
            exit;
        }

        // Determine which table to update based on user type
        $tableName = ($userType === 'public') ? 'public_users' : 'university_users';
        error_log("updateGallery: Using table: $tableName");

        // Store gallery as JSON
        $galleryJson = json_encode($gallery);
        error_log("updateGallery: Gallery JSON length: " . strlen($galleryJson) . " bytes");
        error_log("updateGallery: First 200 chars of JSON: " . substr($galleryJson, 0, 200));

        try {
            // Create PDO connection directly
            $conn = new PDO(
                "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4",
                DBUSER,
                DBPASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            error_log("updateGallery: PDO connection established");

            // FIRST: Check if user exists in the table
            $checkUserQuery = "SELECT id, email FROM {$tableName} WHERE id = :id";
            $checkUserStmt = $conn->prepare($checkUserQuery);
            $checkUserStmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $checkUserStmt->execute();
            $existingUser = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                error_log("updateGallery: CRITICAL - User ID $userId does NOT exist in $tableName!");
                ob_clean();
                echo json_encode(['success' => false, 'error' => "User not found in $tableName. Check your user type."]);
                exit;
            }

            error_log("updateGallery: User exists - ID: {$existingUser['id']}, Email: {$existingUser['email']}");

            // Prepare the UPDATE statement
            $query = "UPDATE {$tableName} SET gallery = :gallery WHERE id = :id";
            error_log("updateGallery: Query: $query");

            $stmt = $conn->prepare($query);
            error_log("updateGallery: Statement prepared");

            // Bind parameters
            $stmt->bindParam(':gallery', $galleryJson, PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            error_log("updateGallery: Parameters bound - gallery=" . strlen($galleryJson) . " bytes, id=$userId");

            // Execute
            $result = $stmt->execute();
            error_log("updateGallery: Execute returned: " . ($result ? 'true' : 'false'));

            // Get row count
            $rowCount = $stmt->rowCount();
            error_log("updateGallery: Rows affected: $rowCount");

            // VERIFICATION: Immediately read back what was saved
            $verifyQuery = "SELECT gallery FROM {$tableName} WHERE id = :id";
            $verifyStmt = $conn->prepare($verifyQuery);
            $verifyStmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $verifyStmt->execute();
            $verifyRow = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            $savedGallery = $verifyRow['gallery'] ?? null;

            if ($savedGallery) {
                error_log("updateGallery: VERIFICATION - Data IS in database, length: " . strlen($savedGallery) . " bytes");
                error_log("updateGallery: VERIFICATION - Preview: " . substr($savedGallery, 0, 100));
            } else {
                error_log("updateGallery: VERIFICATION - Data is NULL/empty in database!");
            }

            if ($rowCount > 0) {
                error_log("updateGallery: SUCCESS - Data updated in database");
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Gallery updated successfully', 'rows' => $rowCount]);
            } elseif ($result === true) {
                // Execute succeeded but no rows affected - check if user exists
                error_log("updateGallery: Execute succeeded but 0 rows affected - checking if user exists");
                $checkQuery = "SELECT id FROM {$tableName} WHERE id = :id";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bindParam(':id', $userId, PDO::PARAM_INT);
                $checkStmt->execute();
                $userExists = $checkStmt->fetch();

                if ($userExists) {
                    error_log("updateGallery: User exists, update succeeded (0 rows affected might indicate no change)");
                    ob_clean();
                    echo json_encode(['success' => true, 'message' => 'Gallery updated successfully', 'rows' => 0]);
                } else {
                    error_log("updateGallery: ERROR - User $userId not found in $tableName");
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => "User not found in $tableName"]);
                }
            } else {
                error_log('updateGallery: Execute returned false');
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
        } catch (Exception $e) {
            error_log('updateGallery: Exception caught: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Failed to update gallery: ' . $e->getMessage()]);
        }

        error_log('=== updateGallery() COMPLETE ===');
        exit;
    }

    /**
     * Ensure gallery column exists in the given table (adds it if missing)
     */
    private function ensureGalleryColumn($conn, $tableName)
    {
        try {
            $checkQuery = "SHOW COLUMNS FROM {$tableName} LIKE 'gallery'";
            $stmt = $conn->prepare($checkQuery);
            $stmt->execute();
            $exists = $stmt->fetchAll();

            if (empty($exists)) {
                // Use LONGTEXT to safely store multiple base64 images
                $alterQuery = "ALTER TABLE {$tableName} ADD COLUMN gallery LONGTEXT NULL COMMENT 'User photo gallery albums stored as JSON'";
                $conn->exec($alterQuery);
                error_log("ensureGalleryColumn: Added gallery column to {$tableName}");
            }
        } catch (Exception $e) {
            error_log('ensureGalleryColumn error on ' . $tableName . ': ' . $e->getMessage());
        }
    }
}
