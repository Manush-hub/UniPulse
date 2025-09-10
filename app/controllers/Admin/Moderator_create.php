<?php

class Moderator_create extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Handle POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createModerator();
            return;
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        $this->view('Admin/moderator_create', $data);
    }
    
    private function createModerator() {
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $errors = [];
        $old_data = $_POST;
        
        // Validate required fields
        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }
        
        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address';
        }
        
        if (empty($_POST['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($_POST['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        // Check if email already exists in any user table
        if (empty($errors['email'])) {
            $moderatorModel = new Moderator();
            $existingModerator = $moderatorModel->where(['email' => $_POST['email']]);
            if ($existingModerator) {
                $errors['email'] = 'This email is already registered as a moderator';
            }
        }
        
        // If there are validation errors, show the form again with errors
        if (!empty($errors)) {
            $data['errors'] = $errors;
            $data['old_data'] = $old_data;
            $this->view('Admin/moderator_create', $data);
            return;
        }
        
        // Prepare data for insertion
        $moderatorData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
            'assigned_by' => $data['user']['id'], // Current admin's ID
            'is_active' => 1
        ];
        
        // Handle permissions
        $permissions = [];
        if (isset($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $permission => $value) {
                $permissions[$permission] = (bool)$value;
            }
        }
        $moderatorData['permissions'] = json_encode($permissions);
        
        // Create the moderator
        $moderatorModel = new Moderator();
        $result = $moderatorModel->create($moderatorData);
        
        if ($result['success']) {
            // Redirect to moderators list with success message
            $_SESSION['success_message'] = 'Moderator created successfully';
            header('Location: /unipulse/public/admin/moderators_list');
            exit();
        } else {
            // Show form with errors
            $data['errors'] = $result['errors'] ?? ['general' => 'Failed to create moderator'];
            $data['old_data'] = $old_data;
            $this->view('Admin/moderator_create', $data);
        }
    }
}