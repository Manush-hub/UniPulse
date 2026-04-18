<?php

class Contact extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $currentUser = AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null;

        $data = [
            'errors' => [],
            'success_message' => null,
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
                    $redirectByType = [
                        'admin' => '/unipulse/public/admin/landing',
                        'moderator' => '/unipulse/public/moderator/dashboard',
                        'publisher' => '/unipulse/public/publisher/dashboard',
                        'sponsor' => '/unipulse/public/sponsor/dashboard',
                        'public' => '/unipulse/public/landing',
                        'university' => '/unipulse/public/landing',
                    ];

                    $userType = strtolower((string)($currentUser['type'] ?? ''));
                    $redirectPath = $redirectByType[$userType] ?? '/unipulse/public/landing';

                    if ($userType === 'admin') {
                        $_SESSION['contact_message_sent'] = 'Message sent';
                    }

                    header('Location: ' . $redirectPath);
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
