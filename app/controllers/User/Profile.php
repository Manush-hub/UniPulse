<?php

class Userprofile extends BaseUserController {
    
    public function index($a = '', $b = '', $c = '') {
        // Get user's events (if they have created any)
        $event = new Event();
        $userEvents = $event->getEventsForUser($this->currentUser['type'], $this->userUniversity, ['limit' => 10]);
        
        $this->userView('User/profile', [
            'userEvents' => $userEvents
        ]);
    }
    
    public function edit($a = '', $b = '', $c = '') {
        // Require authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->updateUserProfile($currentUser, $_POST);
            
            if ($result['success']) {
                $_SESSION['success_message'] = 'Profile updated successfully!';
                header('Location: /unipulse/public/user/profile');
                exit();
            } else {
                $data['errors'] = $result['errors'];
            }
        }
        
        // Get current user details
        $userData = $this->getUserDetails($currentUser);
        
        $data = [
            'user' => $currentUser,
            'userDetails' => $userData
        ];
        
        $this->view('User/profile_edit', $data);
    }
    
    private function getUserDetails($currentUser) {
        $query = "SELECT * FROM {$currentUser['table']} WHERE id = :id LIMIT 1";
        $model = new Model();
        $result = $model->query($query, ['id' => $currentUser['id']]);
        
        return $result ? $result[0] : null;
    }
    
    private function updateUserProfile($currentUser, $data) {
        $errors = [];
        $updateFields = [];
        $params = ['id' => $currentUser['id']];
        
        // Validate and prepare common fields
        if (!empty($data['full_name'])) {
            $updateFields[] = 'full_name = :full_name';
            $params['full_name'] = trim($data['full_name']);
        }
        
        if (!empty($data['phone'])) {
            if (preg_match('/^[0-9]{9,10}$/', $data['phone'])) {
                $updateFields[] = 'phone = :phone';
                $params['phone'] = $data['phone'];
            } else {
                $errors[] = 'Please enter a valid phone number';
            }
        }
        
        // University-specific fields
        if ($currentUser['type'] === 'university') {
            if (!empty($data['faculty'])) {
                $updateFields[] = 'faculty = :faculty';
                $params['faculty'] = trim($data['faculty']);
            }
            
            if (!empty($data['academic_year'])) {
                $updateFields[] = 'academic_year = :academic_year';
                $params['academic_year'] = trim($data['academic_year']);
            }
        }
        
        // Handle interests (JSON field)
        if (isset($data['interests'])) {
            $interests = is_array($data['interests']) ? $data['interests'] : explode(',', $data['interests']);
            $updateFields[] = 'interests = :interests';
            $params['interests'] = json_encode(array_map('trim', $interests));
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if (empty($updateFields)) {
            return ['success' => false, 'errors' => ['No fields to update']];
        }
        
        // Update user record
        $sql = "UPDATE {$currentUser['table']} SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $model = new Model();
        $result = $model->query($sql, $params);
        
        if ($result !== false) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['Failed to update profile']];
        }
    }
}
