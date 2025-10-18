<?php

class Unireg extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $data = [];
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $universityUser = new UniversityUser();
            
            // Validate the form data
            $errors = $universityUser->validateData($_POST);
            
            // Check if terms are accepted
            if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
                $errors[] = "You must accept the Terms & Conditions and Privacy Policy";
            }
            
            if (empty($errors)) {
                try {
                    // Prepare data for database insertion
                    $userData = $universityUser->prepareDataForInsert($_POST);
                    
                    // Insert user data
                    $universityUserId = $universityUser->create($userData);
                    
                    if ($universityUserId) {
                        // Also create entry in main users table
                        $user = new User();
                        $user->createFromRegistration(
                            $userData['email'],
                            $_POST['password'],
                            'university',
                            $universityUserId
                        );
                        
                        $success = true;
                        $data['success_message'] = "Registration successful! Your university account has been created.";
                        // Clear form data on success
                        $_POST = [];
                    } else {
                        $errors[] = "Registration failed. Please try again.";
                    }
                } catch (Exception $e) {
                    $errors[] = "An error occurred during registration: " . $e->getMessage();
                }
            }
        }
        
        $data['errors'] = $errors;
        $data['success'] = $success;
        $data['form_data'] = $_POST;
        
        $this->view('unireg', $data);
    }

}