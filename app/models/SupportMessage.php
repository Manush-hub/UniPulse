<?php

class SupportMessage {
    use Model;

    protected $table = 'support_messages';
    protected $allowedColumns = [
        'full_name',
        'email',
        'phone',
        'category',
        'subject',
        'message',
        'source_page',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    public function validate($input) {
        $errors = [];

        $fullName = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $category = trim($input['category'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');

        if ($fullName === '') {
            $errors[] = 'Full name is required.';
        } elseif (mb_strlen($fullName) > 150) {
            $errors[] = 'Full name must be 150 characters or fewer.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($category === '') {
            $errors[] = 'Issue category is required.';
        }

        if ($subject === '') {
            $errors[] = 'Subject is required.';
        } elseif (mb_strlen($subject) > 255) {
            $errors[] = 'Subject must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        } elseif (mb_strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters.';
        }

        return $errors;
    }

    public function createFromContactForm($input) {
        $data = [
            'full_name' => trim($input['name'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'category' => trim($input['category'] ?? ''),
            'subject' => trim($input['subject'] ?? ''),
            'message' => trim($input['message'] ?? ''),
            'source_page' => '/unipulse/public/contact',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }
}
