<?php

class SponsorProfile extends Controller {

    private $sponsorModel;

    public function __construct() {
        $this->sponsorModel = new Sponsor();
    }

    public function index($a = '', $b = '', $c = '') {
        // Check authentication
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Get sponsor data
        $sponsorId = $currentUser['id'];
        $sponsorData = $this->sponsorModel->findById($sponsorId);
        
        if (!$sponsorData) {
            $this->view('profile', ['error' => 'Sponsor profile not found']);
            return;
        }

        // Get additional profile data
        $profileData = $this->sponsorModel->getProfileData($sponsorId);
        
        // Update session with profile data for header
        if ($profileData && isset($profileData->logo_url)) {
            $_SESSION['user_logo'] = $profileData->logo_url;
        }
        
        // Merge sponsor and profile data
        $data = [
            'sponsor' => $sponsorData,
            'profile' => $profileData,
            'sponsorJson' => json_encode([
                'id' => $sponsorData->id,
                'company_name' => $sponsorData->company_name,
                'email' => $sponsorData->email,
                'phone' => $sponsorData->phone,
                'country_code' => $sponsorData->country_code,
                'verification_status' => $sponsorData->verification_status ?? 'active',
                'is_active' => $sponsorData->is_active ?? true,
                'created_at' => $sponsorData->created_at,
                // Profile data
                'sponsor_type' => $profileData->sponsor_type ?? null,
                'industry' => $profileData->industry ?? null,
                'company_size' => $profileData->company_size ?? null,
                'address' => $profileData->address ?? null,
                'headline' => $profileData->headline ?? null,
                'about' => $profileData->about ?? null,
                'mission' => $profileData->mission ?? null,
                'interests' => $profileData->interests ?? null,
                'website' => $profileData->website ?? null,
                'facebook' => $profileData->facebook ?? null,
                'instagram' => $profileData->instagram ?? null,
                'linkedin' => $profileData->linkedin ?? null,
                'twitter' => $profileData->twitter ?? null,
                'youtube' => $profileData->youtube ?? null,
                'logo_url' => $profileData->logo_url ?? null,
                'cover_photo_url' => $profileData->cover_photo_url ?? null,
                'is_verified' => $profileData->is_verified ?? 0
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
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $sponsorId = $currentUser['id'];
        $sponsorData = $this->sponsorModel->findById($sponsorId);
        $profileData = $this->sponsorModel->getProfileData($sponsorId);
        
        if ($sponsorData) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'sponsor' => $sponsorData,
                    'profile' => $profileData
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sponsor not found']);
        }
    }

    /**
     * Update sponsor information
     */
    public function updateSponsorInfo($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $sponsorId = $currentUser['id'];
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            error_log("updateSponsorInfo: Invalid input data");
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        // Validate required fields
        $requiredFields = [
            'company_name' => 'Company Name',
            'sponsor_type' => 'Company Type',
            'industry' => 'Industry / Sector',
            'phone' => 'Phone Number',
            'about' => 'About'
        ];

        foreach ($requiredFields as $field => $label) {
            $inputField = $field === 'company_name' ? 'sponsorName' : 
                         ($field === 'sponsor_type' ? 'sponsorType' : 
                         ($field === 'phone' ? 'sponsorPhone' : $field));
            
            if (empty($input[$inputField]) || trim($input[$inputField]) === '') {
                echo json_encode(['success' => false, 'message' => "$label is required"]);
                return;
            }
        }

        // Update sponsor basic info (email is readonly and cannot be changed)
        $basicData = [];
        if (isset($input['sponsorName']) && trim($input['sponsorName']) !== '') {
            $basicData['company_name'] = trim($input['sponsorName']);
        }
        if (isset($input['sponsorPhone']) && trim($input['sponsorPhone']) !== '') {
            $basicData['phone'] = trim($input['sponsorPhone']);
        }
        
        $basicResult = true;
        if (!empty($basicData)) {
            $basicResult = $this->sponsorModel->updateBasicInfo($sponsorId, $basicData);
        }

        // Update profile data (required fields)
        $profileData = [];
        if (isset($input['sponsorType']) && $input['sponsorType'] !== '') {
            $profileData['sponsor_type'] = trim($input['sponsorType']);
        }
        if (isset($input['industry']) && $input['industry'] !== '') {
            $profileData['industry'] = trim($input['industry']);
        }
        if (isset($input['about']) && $input['about'] !== '') {
            $profileData['about'] = trim($input['about']);
        }
        
        // Optional fields
        if (isset($input['companySize']) && $input['companySize'] !== '') {
            $profileData['company_size'] = trim($input['companySize']);
        }
        if (isset($input['sponsorAddress']) && $input['sponsorAddress'] !== '') {
            $profileData['address'] = trim($input['sponsorAddress']);
        }
        if (isset($input['headline']) && $input['headline'] !== '') {
            $profileData['headline'] = trim($input['headline']);
        }
        if (isset($input['mission']) && $input['mission'] !== '') {
            $profileData['mission'] = trim($input['mission']);
        }
        
        // Handle interests (JSON array)
        if (isset($input['interests'])) {
            if (is_array($input['interests'])) {
                $profileData['interests'] = json_encode($input['interests']);
            } else if (is_string($input['interests'])) {
                $profileData['interests'] = $input['interests'];
            }
        }
        
        $profileResult = true;
        if (!empty($profileData)) {
            $profileResult = $this->sponsorModel->updateProfileData($sponsorId, $profileData);
        }

        if ($basicResult && $profileResult) {
            // Update session data
            if (isset($input['sponsorName'])) {
                $_SESSION['user_name'] = $input['sponsorName'];
            }
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    }

    /**
     * Update interests/sponsorship focus areas immediately
     */
    public function updateInterests($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $sponsorId = $currentUser['id'];
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['interests'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        // Convert interests array to JSON string
        $interestsJson = is_array($input['interests']) 
            ? json_encode($input['interests']) 
            : $input['interests'];

        // Update interests in profile
        $profileData = ['interests' => $interestsJson];
        $result = $this->sponsorModel->updateProfileData($sponsorId, $profileData);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Interests updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update interests']);
        }
    }

    /**
     * Update contact/social links
     */
    public function updateContactInfo($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $sponsorId = $currentUser['id'];
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        // Update social links in profile
        $profileData = [];
        if (isset($input['website'])) $profileData['website'] = $input['website'];
        if (isset($input['facebook'])) $profileData['facebook'] = $input['facebook'];
        if (isset($input['instagram'])) $profileData['instagram'] = $input['instagram'];
        if (isset($input['linkedin'])) $profileData['linkedin'] = $input['linkedin'];
        if (isset($input['twitter'])) $profileData['twitter'] = $input['twitter'];
        if (isset($input['youtube'])) $profileData['youtube'] = $input['youtube'];
        
        $result = $this->sponsorModel->updateProfileData($sponsorId, $profileData);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Contact information updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update contact information']);
        }
    }

    /**
     * Change password
     */
    public function changePassword($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['currentPassword']) || !isset($input['newPassword']) || !isset($input['confirmPassword'])) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        if ($input['newPassword'] !== $input['confirmPassword']) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
            return;
        }

        $sponsorId = $currentUser['id'];
        $sponsor = $this->sponsorModel->findById($sponsorId);

        if (!password_verify($input['currentPassword'], $sponsor->password_hash)) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            return;
        }

        $newPasswordHash = password_hash($input['newPassword'], PASSWORD_DEFAULT);
        $result = $this->sponsorModel->updateBasicInfo($sponsorId, ['password_hash' => $newPasswordHash]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        }
    }

    /**
     * Upload image (logo or cover photo)
     */
    public function uploadImage($a = '', $b = '', $c = '') {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (!isset($_FILES['image']) || !isset($_POST['type'])) {
            echo json_encode(['success' => false, 'message' => 'Missing image or type']);
            return;
        }

        $file = $_FILES['image'];
        $type = $_POST['type']; // 'logo' or 'cover'
        
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG and PNG are allowed']);
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
            return;
        }

        // Create upload directory if it doesn't exist
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/UniPulse/public/uploads/sponsors/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'sponsor_' . $currentUser['id'] . '_' . $type . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Generate URL for the image
            $imageUrl = '/UniPulse/public/uploads/sponsors/' . $filename;
            
            // Update database
            $sponsorId = $currentUser['id'];
            $updateData = $type === 'logo' ? ['logo_url' => $imageUrl] : ['cover_photo_url' => $imageUrl];
            
            $result = $this->sponsorModel->updateProfileData($sponsorId, $updateData);
            
            if ($result) {
                // Update session with logo URL for header
                if ($type === 'logo') {
                    $_SESSION['user_logo'] = $imageUrl;
                }
                echo json_encode(['success' => true, 'url' => $imageUrl, 'message' => 'Image uploaded successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
    }
}

