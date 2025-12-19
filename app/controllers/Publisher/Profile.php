<?php

class Publisherprofile extends Controller{

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
                'cover_photo_url' => $profileData->cover_photo_url ?? null
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
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            return;
        }

        // Update publisher basic info
        $basicData = [];
        if (isset($input['orgName'])) $basicData['society_name'] = $input['orgName'];
        if (isset($input['university'])) $basicData['university'] = $input['university'];
        if (isset($input['faculty'])) $basicData['faculty'] = $input['faculty'];
        if (isset($input['contactNumber'])) $basicData['phone'] = $input['contactNumber'];
        
        $basicResult = true;
        if (!empty($basicData)) {
            $basicResult = $this->publisherModel->updateBasicInfo($publisherId, $basicData);
        }

        // Update profile data
        $profileData = [];
        if (isset($input['orgType'])) $profileData['org_type'] = $input['orgType'];
        if (isset($input['address'])) $profileData['address'] = $input['address'];
        if (isset($input['establishedYear'])) $profileData['established_year'] = $input['establishedYear'];
        if (isset($input['memberCount'])) $profileData['member_count'] = $input['memberCount'];
        if (isset($input['headline'])) $profileData['headline'] = $input['headline'];
        if (isset($input['bio'])) $profileData['bio'] = $input['bio'];
        if (isset($input['mission'])) $profileData['mission'] = $input['mission'];
        
        $profileResult = true;
        if (!empty($profileData)) {
            $profileResult = $this->publisherModel->updateProfileData($publisherId, $profileData);
        }

        if ($basicResult && $profileResult) {
            echo json_encode(['success' => true, 'message' => 'Organization information updated successfully']);
        } else {
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
        if (isset($input['website'])) $socialData['website'] = $input['website'];
        if (isset($input['facebook'])) $socialData['facebook'] = $input['facebook'];
        if (isset($input['instagram'])) $socialData['instagram'] = $input['instagram'];
        if (isset($input['linkedin'])) $socialData['linkedin'] = $input['linkedin'];
        if (isset($input['twitter'])) $socialData['twitter'] = $input['twitter'];
        if (isset($input['discord'])) $socialData['discord'] = $input['discord'];
        if (isset($input['youtube'])) $socialData['youtube'] = $input['youtube'];

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

}

