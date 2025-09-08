<?php

class Sponsorreg extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $data = [];
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sponsor = new Sponsor();
            
            // Validate the form data
            $errors = $sponsor->validateData($_POST);
            
            // Check if terms are accepted
            if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
                $errors[] = "You must accept the Terms & Conditions and Privacy Policy";
            }
            
            if (empty($errors)) {
                try {
                    // Prepare data for database insertion
                    $userData = $sponsor->prepareDataForInsert($_POST);
                    
                    // Insert sponsor data
                    $sponsorId = $sponsor->create($userData);
                    
                    if ($sponsorId) {
                        // Also create entry in main users table
                        $user = new User();
                        $user->createFromRegistration(
                            $userData['email'],
                            $_POST['password'],
                            'sponsor',
                            $sponsorId
                        );
                        
                        $success = true;
                        $data['success_message'] = "Registration successful! Your sponsor account has been created.";
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
        
        $this->view('sponsorreg', $data);
    } 
}