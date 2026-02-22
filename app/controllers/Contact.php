<?php

class Contact extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        $data = [
            'errors' => [],
            'success_message' => null,
            'form_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supportMessage = new SupportMessage();
            $errors = $supportMessage->validate($_POST);

            if (empty($errors)) {
                $savedId = $supportMessage->createFromContactForm($_POST);

                if ($savedId) {
                    $data['success_message'] = 'Thank you! Your support message has been submitted successfully.';
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
