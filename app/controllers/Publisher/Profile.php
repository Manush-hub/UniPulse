<?php

class PublisherProfile extends Controller{

    private $publisherModel;

    public function __construct() {
        $this->publisherModel = new Publisher();
    }

    public function index($a = '', $b = '' , $c = ''){
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Get publisher data
        $publisherId = $currentUser['id'];
        $publisherData = $this->publisherModel->findById($publisherId);
        
        if (!$publisherData) {
            $this->view('profile', ['error' => 'Publisher profile not found']);
            return;
        }

        // Get additional profile data
        $profileData = $this->publisherModel->getProfileData($publisherId);
        
        // Merge publisher and profile data
        $data = [
            'publisher' => $publisherData,
            'profile' => $profileData,
            'publisherJson' => json_encode([
                'id' => $publisherData->id,
                'society_name' => $publisherData->society_name,
                'email' => $publisherData->email,
                'phone' => $publisherData->phone,
                'country_code' => $publisherData->country_code,
                'university' => $publisherData->university,
                'faculty' => $publisherData->faculty,
                'approval_status' => $publisherData->approval_status,
                'is_active' => $publisherData->is_active,
                'created_at' => $publisherData->created_at,
                // Profile data
                'org_type' => $profileData->org_type ?? null,
                'address' => $profileData->address ?? null,
                'established_year' => $profileData->established_year ?? null,
                'member_count' => $profileData->member_count ?? null,
                'headline' => $profileData->headline ?? null,
                'bio' => $profileData->bio ?? null,
                'mission' => $profileData->mission ?? null,
                'website' => $profileData->website ?? null,
                'facebook' => $profileData->facebook ?? null,
                'instagram' => $profileData->instagram ?? null,
                'linkedin' => $profileData->linkedin ?? null,
                'twitter' => $profileData->twitter ?? null,
                'discord' => $profileData->discord ?? null,
                'youtube' => $profileData->youtube ?? null,
                'logo_url' => $profileData->logo_url ?? null,
                'cover_photo_url' => $profileData->cover_photo_url ?? null,
                'preferences' => $profileData->preferences ?? null
            ])
        ];

        $this->view('profile', $data);
    }

    /**
     * API endpoint to get profile data
     */
    public function getProfileData($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $publisherId = $currentUser['id'];
        $publisherData = $this->publisherModel->findById($publisherId);
        $profileData = $this->publisherModel->getProfileData($publisherId);
        
        if ($publisherData) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'publisher' => $publisherData,
                    'profile' => $profileData
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Publisher not found']);
        }
    }

    /**
     * Update organization information
     */
    public function updateOrganizationInfo($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $publisherId = $currentUser['id'];
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            error_log("updateOrganizationInfo: Invalid input data");
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        error_log("updateOrganizationInfo: Input received: " . print_r($input, true));

        // Update publisher basic info
        $basicData = [];
        if (isset($input['orgName'])) $basicData['society_name'] = $input['orgName'];
        if (isset($input['university'])) $basicData['university'] = $input['university'];
        if (isset($input['faculty'])) $basicData['faculty'] = $input['faculty'];
        if (isset($input['contactNumber'])) $basicData['phone'] = $input['contactNumber'];
        
        $basicResult = true;
        if (!empty($basicData)) {
            error_log("updateOrganizationInfo: Updating basic info: " . print_r($basicData, true));
            $basicResult = $this->publisherModel->updateBasicInfo($publisherId, $basicData);
            error_log("updateOrganizationInfo: Basic info result: " . ($basicResult ? 'SUCCESS' : 'FAILED'));
        }

        // Update profile data
        $profileData = [];
        if (isset($input['orgType']) && $input['orgType'] !== '') $profileData['org_type'] = $input['orgType'];
        if (isset($input['address']) && $input['address'] !== '') $profileData['address'] = $input['address'];
        if (isset($input['establishedYear']) && $input['establishedYear'] !== '') $profileData['established_year'] = (int)$input['establishedYear'];
        if (isset($input['memberCount']) && $input['memberCount'] !== '') $profileData['member_count'] = (int)$input['memberCount'];
        if (isset($input['headline']) && $input['headline'] !== '') $profileData['headline'] = $input['headline'];
        if (isset($input['bio']) && $input['bio'] !== '') $profileData['bio'] = $input['bio'];
        if (isset($input['mission']) && $input['mission'] !== '') $profileData['mission'] = $input['mission'];
        
        $profileResult = true;
        if (!empty($profileData)) {
            error_log("updateOrganizationInfo: Updating profile data: " . print_r($profileData, true));
            $profileResult = $this->publisherModel->updateProfileData($publisherId, $profileData);
            error_log("updateOrganizationInfo: Profile data result: " . ($profileResult ? 'SUCCESS' : 'FAILED'));
        }

        if ($basicResult && $profileResult) {
            echo json_encode(['success' => true, 'message' => 'Organization information updated successfully']);
        } else {
            $errorMsg = [];
            if (!$basicResult) $errorMsg[] = 'basic info failed';
            if (!$profileResult) $errorMsg[] = 'profile data failed';
            error_log("updateOrganizationInfo: Update FAILED - " . implode(', ', $errorMsg));
            echo json_encode(['success' => false, 'message' => 'Failed to update organization information']);
        }
    }

    /**
     * Update social links
     */
    public function updateSocialLinks($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $publisherId = $currentUser['id'];
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        $socialData = [];
        // Allow empty strings to clear fields, but only include if key is present
        if (array_key_exists('website', $input)) $socialData['website'] = $input['website'];
        if (array_key_exists('facebook', $input)) $socialData['facebook'] = $input['facebook'];
        if (array_key_exists('instagram', $input)) $socialData['instagram'] = $input['instagram'];
        if (array_key_exists('linkedin', $input)) $socialData['linkedin'] = $input['linkedin'];
        if (array_key_exists('twitter', $input)) $socialData['twitter'] = $input['twitter'];
        if (array_key_exists('discord', $input)) $socialData['discord'] = $input['discord'];
        if (array_key_exists('youtube', $input)) $socialData['youtube'] = $input['youtube'];

        $result = $this->publisherModel->updateProfileData($publisherId, $socialData);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Social links updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update social links']);
        }
    }

    /**
     * Upload profile image (logo)
     */
    public function uploadProfileImage($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No image uploaded']);
            return;
        }

        $publisherId = $currentUser['id'];
        $imageUrl = $this->publisherModel->uploadImage($_FILES['image'], 'logo', $publisherId);

        if ($imageUrl) {
            $result = $this->publisherModel->updateProfileData($publisherId, ['logo_url' => $imageUrl]);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Logo uploaded successfully', 'imageUrl' => $imageUrl]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save logo URL']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload logo']);
        }
    }

    /**
     * Upload cover photo
     */
    public function uploadCoverPhoto($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        // error_log("uploadCoverPhoto called");
        // error_log("FILES: " . print_r($_FILES, true));
        
        $currentUser = AuthService::getCurrentUser();
        // error_log("Current user: " . print_r($currentUser, true));
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES['image']) ? $_FILES['image']['error'] : 'No file';
            error_log("File upload error: " . $error);
            echo json_encode(['success' => false, 'message' => 'No image uploaded', 'error' => $error]);
            return;
        }

        $publisherId = $currentUser['id'];
        $imageUrl = $this->publisherModel->uploadImage($_FILES['image'], 'cover', $publisherId);
        error_log("Publisher ID: " . $publisherId);
        error_log("Image URL returned: " . ($imageUrl ? $imageUrl : 'FALSE'));

        if ($imageUrl) {
            $result = $this->publisherModel->updateProfileData($publisherId, ['cover_photo_url' => $imageUrl]);
            error_log("Update result: " . ($result ? 'TRUE' : 'FALSE'));
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cover photo uploaded successfully', 'imageUrl' => $imageUrl]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save cover photo URL']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload cover photo']);
        }
    }

    /**
     * Update organization preferences (Auto-save via AJAX)
     */
    public function updatePreferences($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit();
        }

        $publisherId = $currentUser['id'];
        
        // Handle AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get preferences from POST data (JSON string from JavaScript)
            $preferences = [];
            
            if (isset($_POST['selected_preferences'])) {
                $preferencesData = json_decode($_POST['selected_preferences'], true);
                if (is_array($preferencesData)) {
                    $preferences = $preferencesData;
                }
            }
            
            // Store preferences as JSON string (empty array if nothing selected)
            $preferencesJson = json_encode($preferences);
            
            // Log what we're saving
            error_log("Publisher $publisherId saving preferences: " . $preferencesJson);
            
            $result = $this->publisherModel->updateProfileData($publisherId, ['preferences' => $preferencesJson]);

            if ($result) {
                error_log("Publisher $publisherId preferences saved successfully");
                echo json_encode([
                    'success' => true, 
                    'message' => 'Preferences updated successfully',
                    'saved_preferences' => $preferences
                ]);
            } else {
                error_log("Publisher $publisherId preferences save failed");
                echo json_encode(['success' => false, 'message' => 'Failed to update preferences']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        exit();
    }

    // ==================== PHOTO GALLERY METHODS ====================
    
    /**
     * Get all galleries for the current publisher
     */
    public function getGalleries($a = '', $b = '', $c = '') {
        $this->jsonResponse(function() {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            $publisherId = $currentUser['id'];
            $galleries = $this->publisherModel->getPublisherGalleries($publisherId);
            
            return ['success' => true, 'data' => $galleries];
        });
    }

    /**
     * Get a specific gallery by ID
     */
    public function getGallery($galleryId = null, $b = '', $c = '') {
        $this->jsonResponse(function() use ($galleryId) {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            if (!$galleryId) {
                return ['success' => false, 'message' => 'Gallery ID is required'];
            }

            $publisherId = $currentUser['id'];
            $gallery = $this->publisherModel->getGalleryById($galleryId);
            
            if (!$gallery) {
                return ['success' => false, 'message' => 'Gallery not found'];
            }

            // Verify ownership
            if ($gallery['publisher_id'] != $publisherId) {
                return ['success' => false, 'message' => 'Access denied'];
            }

            return ['success' => true, 'data' => $gallery];
        });
    }

    /**
     * Create a new gallery
     */
    public function createGallery($a = '', $b = '', $c = '') {
        $this->jsonResponse(function() {
            error_log("=== CREATE GALLERY START ===");
            
            $currentUser = AuthService::getCurrentUser();
            error_log("Current user: " . json_encode($currentUser));
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                error_log("Authorization failed - User type: " . ($currentUser['type'] ?? 'none'));
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("Wrong method: " . $_SERVER['REQUEST_METHOD']);
                return ['success' => false, 'message' => 'Method not allowed'];
            }

            $publisherId = $currentUser['id'];
            error_log("Publisher ID: " . $publisherId);

            // Validate input
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            error_log("Title: " . $title);
            error_log("Description: " . $description);
            error_log("FILES data: " . json_encode($_FILES));

            $errors = [];

            if (empty($title)) {
                $errors[] = 'Title is required';
            } elseif (strlen($title) > 50) {
                $errors[] = 'Title must not exceed 50 characters';
            }

            if (strlen($description) > 150) {
                $errors[] = 'Description must not exceed 150 characters';
            }

            // Validate photos
            if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
                $errors[] = 'At least one photo is required';
                error_log("No photos uploaded");
            }

            if (!empty($errors)) {
                error_log("Validation errors: " . implode(', ', $errors));
                return ['success' => false, 'message' => implode(', ', $errors)];
            }

            // Process photo uploads
            error_log("Starting photo upload...");
            $uploadedPhotos = $this->uploadPhotos($_FILES['photos']);
            error_log("Uploaded photos: " . json_encode($uploadedPhotos));

            if (empty($uploadedPhotos)) {
                error_log("Photo upload returned empty array");
                return ['success' => false, 'message' => 'Photo upload failed'];
            }

            // Create gallery
            error_log("Creating gallery in database...");
            $galleryId = $this->publisherModel->createGallery($publisherId, $title, $description, $uploadedPhotos);
            error_log("Gallery ID created: " . $galleryId);

            if ($galleryId) {
                $gallery = $this->publisherModel->getGalleryById($galleryId);
                error_log("Gallery retrieved: " . json_encode($gallery));
                error_log("=== CREATE GALLERY SUCCESS ===");
                return ['success' => true, 'message' => 'Gallery created successfully', 'data' => $gallery];
            } else {
                error_log("Gallery ID is false/null");
                error_log("=== CREATE GALLERY FAILED ===");
                return ['success' => false, 'message' => 'Failed to create gallery'];
            }
        });
    }

    /**
     * Update an existing gallery
     */
    public function updateGallery($galleryId = null, $b = '', $c = '') {
        $this->jsonResponse(function() use ($galleryId) {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return ['success' => false, 'message' => 'Method not allowed'];
            }

            if (!$galleryId) {
                return ['success' => false, 'message' => 'Gallery ID is required'];
            }

            $publisherId = $currentUser['id'];

            // Verify ownership
            $gallery = $this->publisherModel->getGalleryById($galleryId);
            
            if (!$gallery) {
                return ['success' => false, 'message' => 'Gallery not found'];
            }

            if ($gallery['publisher_id'] != $publisherId) {
                return ['success' => false, 'message' => 'Access denied'];
            }

            // Validate input
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            $errors = [];

            if (empty($title)) {
                $errors[] = 'Title is required';
            } elseif (strlen($title) > 50) {
                $errors[] = 'Title must not exceed 50 characters';
            }

            if (strlen($description) > 150) {
                $errors[] = 'Description must not exceed 150 characters';
            }

            if (!empty($errors)) {
                return ['success' => false, 'message' => implode(', ', $errors)];
            }

            // Process new photo uploads if any
            $newPhotos = [];
            if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                $newPhotos = $this->uploadPhotos($_FILES['photos']);
            }

            // Get existing photos to keep (from POST data as JSON array of URLs)
            $keepPhotos = isset($_POST['keep_photos']) ? json_decode($_POST['keep_photos'], true) : [];
            if (!is_array($keepPhotos)) {
                $keepPhotos = [];
            }

            // Update gallery
            $success = $this->publisherModel->updateGallery($galleryId, $title, $description, $keepPhotos, $newPhotos);

            if ($success) {
                $gallery = $this->publisherModel->getGalleryById($galleryId);
                return ['success' => true, 'message' => 'Gallery updated successfully', 'data' => $gallery];
            } else {
                return ['success' => false, 'message' => 'Failed to update gallery'];
            }
        });
    }

    /**
     * Delete a gallery
     */
    public function deleteGallery($galleryId = null, $b = '', $c = '') {
        $this->jsonResponse(function() use ($galleryId) {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return ['success' => false, 'message' => 'Method not allowed'];
            }

            if (!$galleryId) {
                return ['success' => false, 'message' => 'Gallery ID is required'];
            }

            $publisherId = $currentUser['id'];

            // Verify ownership
            $gallery = $this->publisherModel->getGalleryById($galleryId);
            
            if (!$gallery) {
                return ['success' => false, 'message' => 'Gallery not found'];
            }

            if ($gallery['publisher_id'] != $publisherId) {
                return ['success' => false, 'message' => 'Access denied'];
            }

            // Delete gallery
            $success = $this->publisherModel->deleteGallery($galleryId);

            if ($success) {
                return ['success' => true, 'message' => 'Gallery deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete gallery'];
            }
        });
    }

    // ==================== EVENT METHODS ====================
    
    /**
     * Get upcoming events for the current publisher
     */
    public function getUpcomingEvents($a = '', $b = '', $c = '') {
        $this->jsonResponse(function() {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            $publisherId = $currentUser['id'];
            $events = $this->publisherModel->getUpcomingEvents($publisherId, $currentUser);
            
            // Format events with calculated status
            $formattedEvents = [];
            $currentDate = date('Y-m-d');
            
            foreach ($events as $event) {
                $eventArray = (array) $event;
                
                // Calculate actual status based on event date
                if ($eventArray['event_date'] < $currentDate) {
                    $eventArray['status'] = 'past';
                } elseif ($eventArray['event_date'] == $currentDate) {
                    $eventArray['status'] = 'ongoing';
                } elseif ($eventArray['event_date'] > $currentDate) {
                    $eventArray['status'] = 'upcoming';
                }
                
                $formattedEvents[] = $eventArray;
            }
            
            return ['success' => true, 'data' => $formattedEvents];
        });
    }

    /**
     * Get past events for the current publisher
     */
    public function getPastEvents($a = '', $b = '', $c = '') {
        $this->jsonResponse(function() {
            $currentUser = AuthService::getCurrentUser();
            
            if (!$currentUser || $currentUser['type'] !== 'publisher') {
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            $publisherId = $currentUser['id'];
            $events = $this->publisherModel->getPastEvents($publisherId, $currentUser);
            
            // Format events with calculated status
            $formattedEvents = [];
            $currentDate = date('Y-m-d');
            
            foreach ($events as $event) {
                $eventArray = (array) $event;
                
                // Calculate actual status based on event date
                if ($eventArray['event_date'] < $currentDate) {
                    $eventArray['status'] = 'past';
                } elseif ($eventArray['event_date'] == $currentDate) {
                    $eventArray['status'] = 'ongoing';
                } elseif ($eventArray['event_date'] > $currentDate) {
                    $eventArray['status'] = 'upcoming';
                }
                
                $formattedEvents[] = $eventArray;
            }
            
            return ['success' => true, 'data' => $formattedEvents];
        });
    }

    /**
     * Upload photos and return array of uploaded file paths
     */
    private function uploadPhotos($files) {
        error_log("uploadPhotos called with: " . json_encode($files));
        
        $uploadedPhotos = [];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $maxPhotos = 10;

        // Create upload directory if it doesn't exist
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/UniPulse/public/uploads/gallery/';
        error_log("Upload directory: " . $uploadDir);
        
        if (!file_exists($uploadDir)) {
            error_log("Creating upload directory...");
            mkdir($uploadDir, 0755, true);
        }
        
        if (!is_writable($uploadDir)) {
            error_log("Upload directory is NOT writable!");
            throw new Exception("Upload directory is not writable");
        } else {
            error_log("Upload directory is writable");
        }

        $fileCount = count($files['name']);
        error_log("File count: " . $fileCount);
        
        if ($fileCount > $maxPhotos) {
            throw new Exception("Maximum {$maxPhotos} photos allowed");
        }

        for ($i = 0; $i < $fileCount; $i++) {
            error_log("Processing file {$i}: " . $files['name'][$i]);
            error_log("File error code: " . $files['error'][$i]);
            
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("Skipping file {$i} due to error: " . $files['error'][$i]);
                continue;
            }

            // Validate file type
            $fileType = $files['type'][$i];
            error_log("File type: " . $fileType);
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG and PNG allowed.");
            }

            // Validate file size
            if ($files['size'][$i] > $maxSize) {
                throw new Exception("File size must not exceed 5MB");
            }

            // Generate unique filename
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('gallery_' . time() . '_') . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            
            error_log("Target path: " . $targetPath);
            error_log("Temp file: " . $files['tmp_name'][$i]);

            // Move uploaded file
            if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                $webPath = '/UniPulse/public/uploads/gallery/' . $filename;
                $uploadedPhotos[] = $webPath;
                error_log("Successfully uploaded file to: " . $webPath);
            } else {
                error_log("Failed to move uploaded file!");
                throw new Exception("Failed to upload file");
            }
        }

        error_log("Total uploaded photos: " . count($uploadedPhotos));
        return $uploadedPhotos;
    }

    /**
     * Get current publisher data for header
     */
    public function getCurrentPublisher() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $publisherId = $currentUser['id'];
        $publisherData = $this->publisherModel->findById($publisherId);
        
        if (!$publisherData) {
            echo json_encode(['success' => false, 'message' => 'Publisher not found']);
            exit();
        }
        
        // Get profile data for logo
        $profileData = $this->publisherModel->getProfileData($publisherId);
        
        echo json_encode([
            'success' => true,
            'publisher' => [
                'id' => $publisherData->id,
                'society_name' => $publisherData->society_name,
                'email' => $publisherData->email,
                'university' => $publisherData->university,
                'faculty' => $publisherData->faculty,
                'logo_url' => $profileData->logo_url ?? null
            ]
        ]);
        exit();
    }

    /**
     * Update primary admin email
     */
    public function updateAdminEmail($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $publisherId = $currentUser['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $newEmail = $data['email'] ?? '';
        $currentPassword = $data['current_password'] ?? '';

        if (empty($newEmail) || empty($currentPassword)) {
            echo json_encode(['success' => false, 'message' => 'Email and current password are required']);
            exit();
        }

        // Validate email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit();
        }

        // Verify current password
        $publisher = $this->publisherModel->findById($publisherId);
        if (!password_verify($currentPassword, $publisher->password_hash)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit();
        }

        // Check if email already exists
        $existingPublisher = $this->publisherModel->findByEmail($newEmail);
        if ($existingPublisher && $existingPublisher->id != $publisherId) {
            echo json_encode(['success' => false, 'message' => 'Email already in use by another organization']);
            exit();
        }

        // Update email
        $result = $this->publisherModel->updateEmail($publisherId, $newEmail);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Admin email updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update email']);
        }
        exit();
    }

    /**
     * Change password
     */
    public function changePassword($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $publisherId = $currentUser['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }

        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
            exit();
        }

        // Verify current password
        $publisher = $this->publisherModel->findById($publisherId);
        if (!password_verify($currentPassword, $publisher->password_hash)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit();
        }

        // Update password
        $result = $this->publisherModel->updatePassword($publisherId, $newPassword);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to change password']);
        }
        exit();
    }

    /**
     * Helper method to send JSON response
     */
    private function jsonResponse($callback) {
        header('Content-Type: application/json');
        try {
            $result = $callback();
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

}
