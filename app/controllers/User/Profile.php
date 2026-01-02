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
        $nameParts = $this->splitName($profile['full_name'] ?? ($u['name'] ?? ''));
        $data = [
            'firstname' => $nameParts['first'],
            'lastname' => $nameParts['last'],
            'email' => $profile['email'] ?? ($u['email'] ?? ''),
            'phone' => $profile['phone'] ?? '',
            'university' => $profile['university'] ?? ($u['university'] ?? ''),
            'faculty' => $profile['faculty'] ?? '',
            'student_staff_id' => $profile['student_staff_id'] ?? '',
            'academic_year' => $profile['academic_year'] ?? '',
            'dob' => $profile['date_of_birth'] ?? '',
            'gender' => $profile['gender'] ?? '',
            'currentCity' => $profile['current_city'] ?? '',
            'homeTown' => $profile['home_town'] ?? '',
            'role' => $u['type'] ?? 'public',
            'nic' => $profile['nic'] ?? '',
            'headline' => $profile['headline'] ?? '',
            'bio' => $profile['bio'] ?? '',
            'profile_photo' => $profile['profile_photo'] ?? '',
            'cover_photo' => $profile['cover_photo'] ?? ''
        ];

        error_log('Response Data: ' . json_encode($data));
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Update current user's profile from POSTed JSON
     * Only allows editing of: First Name, Last Name, Gender, Phone, Bio
     * Read-only fields: University, Faculty, Student/Staff ID, Email, NIC
     */
    public function updateProfile()
    {
        header('Content-Type: application/json');
        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $u = AuthService::getCurrentUser();

        // Validate input
        $errors = $this->validateEditableFields($payload);
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $updated = false;
        try {
            error_log('[Profile::updateProfile] User: ' . json_encode($u));
            // Only editable fields for basic info
            $fields = [
                'full_name' => $this->joinName($payload['firstname'] ?? '', $payload['lastname'] ?? ''),
                'phone' => $payload['phone'] ?? null,
                'gender' => $payload['gender'] ?? null,
                'bio' => $payload['bio'] ?? null,
            ];

            // Allow image updates if explicitly provided (do not expose in UI as editable fields)
            if (isset($payload['profile_photo'])) {
                $fields['profile_photo'] = $payload['profile_photo'];
            }
            if (isset($payload['cover_photo'])) {
                $fields['cover_photo'] = $payload['cover_photo'];
            }

            // Remove null values
            $fields = array_filter($fields, fn($v) => $v !== null);

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
            error_log('[Profile::updateProfile] Update result: ' . json_encode($updated));

            // Refresh session name if full_name changed
            if (!empty($fields['full_name'])) {
                $_SESSION['user_name'] = $fields['full_name'];
            }

            echo json_encode(['success' => (bool)$updated]);
        } catch (Exception $e) {
            error_log('Profile update error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()]);
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

        // Validate first name
        $firstname = trim($payload['firstname'] ?? '');
        if (empty($firstname)) {
            $errors[] = 'First Name is required';
        } elseif (strlen($firstname) < 2) {
            $errors[] = 'First Name must be at least 2 characters';
        }

        // Validate last name
        $lastname = trim($payload['lastname'] ?? '');
        if (empty($lastname)) {
            $errors[] = 'Last Name is required';
        } elseif (strlen($lastname) < 2) {
            $errors[] = 'Last Name must be at least 2 characters';
        }

        // Validate phone if provided
        if (!empty($payload['phone'])) {
            $phone = trim($payload['phone']);
            if (!preg_match('/^[0-9\+\-\s\(\)]{10,20}$/', $phone)) {
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

        return $errors;
    }
}
