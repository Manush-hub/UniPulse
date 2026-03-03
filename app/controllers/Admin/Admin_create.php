<?php

class Admin_create extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Handle POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createAdmin();
            return;
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        $this->view('Admin/admin_create', $data);
    }
    
    private function createAdmin() {
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
        
        if (!empty($_POST['confirm_password']) && $_POST['password'] !== $_POST['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // Check if email already exists in admin table
        if (empty($errors['email'])) {
            $adminModel = new Admin();
            $existingAdmin = $adminModel->where(['email' => $_POST['email']]);
            if ($existingAdmin) {
                $errors['email'] = 'This email is already registered as an admin';
            }
        }
        
        // If there are validation errors, show the form again with errors
        if (!empty($errors)) {
            $data['errors'] = $errors;
            $data['old_data'] = $old_data;
            $this->view('Admin/admin_create', $data);
            return;
        }
        
        // Prepare data for insertion
        $adminData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
            'is_active' => 1
        ];
        
        // Create the admin
        $adminModel = new Admin();
        $result = $adminModel->create($adminData);
        
        if ($result['success']) {
            // Log admin activity
            AdminActivity::log(
                $data['user']['id'],
                $data['user']['name'],
                'admin_created',
                'admin',
                null,
                $adminData['full_name'],
                'Added new admin ' . $adminData['full_name'],
                'user-tie'
            );
            // Redirect to admins list with success message
            $_SESSION['success_message'] = 'Admin created successfully';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        } else {
            // Show form with errors
            $data['errors'] = $result['errors'] ?? ['general' => 'Failed to create admin'];
            $data['old_data'] = $old_data;
            $this->view('Admin/admin_create', $data);
        }
    }
}
