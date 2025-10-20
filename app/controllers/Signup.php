<?php

class Signup extends Controller
{
    public function index()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();
            
            if ($user->validate($_POST)) {
                $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $_POST['date'] = date("Y-m-d H:i:s");

                // Create the user
                $result = $user->insert($_POST);
                
                if ($result) {
                    // Redirect to login page with success message
                    header("Location: " . ROOT . "/signin?registration=success&message=" . urlencode("Registration successful! Please login with your credentials."));
                    exit;
                } else {
                    $data['errors']['database'] = "Failed to create account. Please try again.";
                }
            } else {
                $data['errors'] = $user->errors;
            }
        }

        $this->view('signup', $data);
    }
}