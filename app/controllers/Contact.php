<?php

class Contact extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $currentUser = AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null;
        $flashSuccess = $_SESSION['contact_form_success'] ?? null;
        unset($_SESSION['contact_form_success']);

        $data = [
            'errors' => [],
            'success_message' => null,
            'flash_success' => $flashSuccess,
            'form_data' => [],
            'current_user' => $currentUser
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supportMessage = new SupportMessage();
            $errors = $supportMessage->validate($_POST);

            if (!$currentUser) {
                $errors[] = 'Please sign in to send a contact message.';
            }

            if (empty($errors)) {
                $profileData = [
                    'name' => $currentUser['name'] ?? '',
                    'email' => $currentUser['email'] ?? '',
                    'phone' => $currentUser['phone'] ?? ''
                ];

                $savedId = $supportMessage->createFromContactForm($_POST, $profileData);

                if ($savedId) {
                    $_SESSION['contact_form_success'] = 'Message was sent to admin.';
                    header('Location: /unipulse/public/contact#support-form');
                    exit;
                } else {
                    $errors[] = 'Could not submit your message right now. Please try again.';
                    $data['form_data'] = $_POST;
                }
            } else {
                $data['form_data'] = $_POST;
            }

            $data['errors'] = $errors;
        }

        $this->view('contact', $data);
    }

}
