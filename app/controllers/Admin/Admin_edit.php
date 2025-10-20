<?php

class Admin_edit extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get admin ID from URL
        $adminId = $a;
        
        if (empty($adminId)) {
            $_SESSION['error_message'] = 'Invalid admin ID';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        // Handle POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateAdmin($adminId);
            return;
        }
        
        // Get admin details
        $adminModel = new Admin();
        $admin = $adminModel->find($adminId);
        
        if (!$admin) {
            $_SESSION['error_message'] = 'Admin not found';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $data['admin'] = $admin;
        
        $this->view('Admin/admin_edit', $data);
    }
    
    private function updateAdmin($adminId) {
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $errors = [];
        $old_data = $_POST;
        
        // Get admin details
        $adminModel = new Admin();
        $admin = $adminModel->find($adminId);
        
        if (!$admin) {
            $_SESSION['error_message'] = 'Admin not found';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        $data['admin'] = $admin;
        
        // Validate required fields
        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }
        
        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address';
        }
        
        // Check if email already exists (excluding current admin)
        if (empty($errors['email'])) {
            $existingAdmin = $adminModel->where(['email' => $_POST['email']]);
            if ($existingAdmin && $existingAdmin[0]->id != $adminId) {
                $errors['email'] = 'This email is already registered';
            }
        }
        
        // Validate password if provided
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            
            if (!empty($_POST['confirm_password']) && $_POST['password'] !== $_POST['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }
        
        // If there are validation errors, show the form again with errors
        if (!empty($errors)) {
            $data['errors'] = $errors;
            $data['old_data'] = $old_data;
            $this->view('Admin/admin_edit', $data);
            return;
        }
        
        // Prepare data for update
        $updateData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null
        ];
        
        // Add password if provided
        if (!empty($_POST['password'])) {
            $updateData['password'] = $_POST['password'];
        }
        
        // Update the admin
        $result = $adminModel->updateAdmin($adminId, $updateData);
        
        if ($result['success']) {
            // Redirect to admins list with success message
            $_SESSION['success_message'] = 'Admin updated successfully';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        } else {
            // Show form with errors
            $data['errors'] = $result['errors'] ?? ['general' => 'Failed to update admin'];
            $data['old_data'] = $old_data;
            $this->view('Admin/admin_edit', $data);
        }
    }
}
