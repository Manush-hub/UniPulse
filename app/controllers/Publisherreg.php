<?php

class Publisherreg extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $data = [];
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $publisher = new Publisher();
            
            // Validate the form data
            $errors = $publisher->validateData($_POST);
            
            // Check if terms are accepted
            if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
                $errors[] = "You must accept the Terms & Conditions and Privacy Policy";
            }
            
            if (empty($errors)) {
                try {
                    // Prepare data for database insertion
                    $userData = $publisher->prepareDataForInsert($_POST);
                    
                    // Insert publisher data
                    $publisherId = $publisher->create($userData);
                    
                    if ($publisherId) {
                        // Also create entry in main users table
                        $user = new User();
                        $user->createFromRegistration(
                            $userData['email'],
                            $_POST['password'],
                            'publisher',
                            $publisherId
                        );
                        
                        // Notify moderators of the same university
                        $this->notifyModerators($userData['university'], $publisherId);
                        
                        $success = true;
                        $data['success_message'] = "Registration successful! Your publisher account has been created and is pending verification by university moderators.";
                        // Clear form data on success
                        $_POST = [];
                        $_FILES = [];
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
        
        $this->view('publisherreg', $data);
    }
    
    /**
     * Notify moderators of new publisher registration
     */
    private function notifyModerators($university, $publisherId) {
        $moderatorModel = new Moderator();
        $moderators = $moderatorModel->getByUniversity($university);
        
        if (!empty($moderators)) {
            $publisherModel = new Publisher();
            foreach ($moderators as $moderator) {
                // Create notification for each moderator
                $publisherModel->createApprovalNotification(
                    $publisherId, 
                    $moderator->id, 
                    'pending_approval',
                    'New publisher registration requires approval'
                );
            }
        }
    }

}