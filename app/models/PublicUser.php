<?php

class PublicUser
{

    use Model;
    protected $table = 'public_users';

    // Allowed columns that can be updated via CRUD operations
    protected $allowedColumns = [
        'full_name',
        'phone',
        'country_code',
        'gender',
        'date_of_birth',
        'headline',
        'current_city',
        'home_town',
        'bio',
        'interests',
        'is_verified',
        'email_verified_at',
        'profile_photo',
        'cover_photo',
        'nic'
    ];

    private const UNIVERSITY_EMAIL_DOMAINS = [
        'cmb.ac.lk',
        'pdn.ac.lk',
        'sjp.ac.lk',
        'kln.ac.lk',
        'uom.lk',
        'jfn.ac.lk',
        'ruh.ac.lk',
        'esn.ac.lk',
        'seu.ac.lk',
        'rjt.ac.lk',
        'sab.ac.lk',
        'wyb.ac.lk',
        'uwu.ac.lk',
        'ou.ac.lk',
        'bpuls.ac.lk',
        'sliit.lk',
        'nsbm.ac.lk',
        'cinec.edu',
        'apiit.lk',
        'kiu.ac.lk'
    ];

    public function create($data)
    {
        $query = "INSERT INTO public_users (
            full_name, email, phone, country_code, password_hash, 
            nic, gender, interests
        ) VALUES (
            :full_name, :email, :phone, :country_code, :password_hash,
            :nic, :gender, :interests
        )";

        $result = $this->query($query, $data);
        if ($result !== false) {
            // Get the connection to retrieve last insert ID
            $conn = $this->connect();
            return $conn->lastInsertId();
        }
        return false;
    }

    public function findByEmail($email)
    {
        $query = "SELECT * FROM public_users WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }

    public function findByNIC($nic)
    {
        $query = "SELECT * FROM public_users WHERE nic = :nic LIMIT 1";
        return $this->getRow($query, ['nic' => $nic]);
    }

    public function emailExists($email)
    {
        $user = $this->findByEmail($email);
        return $user !== false;
    }

    public function nicExists($nic)
    {
        $user = $this->findByNIC($nic);
        return $user !== false;
    }

    public function getRecentRegistrations($limit = 10)
    {
        $limit = (int)$limit; // Ensure it's an integer
        $query = "SELECT 
            id,
            full_name as name,
            email,
            created_at,
            is_suspended,
            suspension_reason,
            'public' as user_type
        FROM public_users 
        ORDER BY created_at DESC 
        LIMIT {$limit}";

        return $this->query($query, []);
    }

    public function validateData($data)
    {
        $errors = [];

        // Required fields validation
        $requiredFields = [
            'full-name' => 'Full Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'confirm-password' => 'Confirm Password',
            'nic' => 'NIC'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "$label is required";
            }
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }

        if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL) && $this->isUniversityEmail($data['email'])) {
            $errors[] = "You cannot register as a Public User using a university email address. Please sign up as a University User.";
        }

        // Password validation
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }
            if ($data['password'] !== $data['confirm-password']) {
                $errors[] = "Passwords do not match";
            }
        }

        // Phone validation
        if (!empty($data['phone']) && !preg_match('/^[0-9]{9,10}$/', $data['phone'])) {
            $errors[] = "Please enter a valid phone number";
        }

        // NIC validation (Sri Lankan format)
        if (!empty($data['nic'])) {
            if (!preg_match('/^([0-9]{9}[xXvV]|[0-9]{12})$/', $data['nic'])) {
                $errors[] = "Please enter a valid NIC number";
            }
        }

        // Check if email already exists
        if (!empty($data['email']) && $this->emailExists($data['email'])) {
            $errors[] = "An account with this email already exists";
        }

        // Check if NIC already exists
        if (!empty($data['nic']) && $this->nicExists($data['nic'])) {
            $errors[] = "An account with this NIC already exists";
        }

        return $errors;
    }

    public function prepareDataForInsert($data)
    {
        $interests = [];
        if (isset($data['interests']) && is_array($data['interests'])) {
            $interests = $data['interests'];
        }

        return [
            'full_name' => trim($data['full-name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']),
            'country_code' => $data['country-code'] ?? '+94',
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'nic' => strtoupper(trim($data['nic'])),
            'gender' => !empty($data['gender']) ? $data['gender'] : null,
            'interests' => !empty($interests) ? json_encode($interests) : null
        ];
    }

    private function isUniversityEmail($email)
    {
        $emailDomain = strtolower(substr(strrchr(trim($email), '@'), 1));

        foreach (self::UNIVERSITY_EMAIL_DOMAINS as $universityDomain) {
            $expectedDomain = strtolower($universityDomain);

            if ($emailDomain === $expectedDomain) {
                return true;
            }

            // Allow matching known subdomains like student.cmb.ac.lk
            if (str_ends_with($emailDomain, '.' . $expectedDomain)) {
                return true;
            }
        }

        return false;
    }

    public function softDeleteAccount($userId)
    {
        $query = "UPDATE public_users SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($query);
        return $stmt->execute(['id' => (int)$userId]);
    }

    public function reactivateAccount($userId)
    {
        $query = "UPDATE public_users SET is_deleted = 0, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($query);
        return $stmt->execute(['id' => (int)$userId]);
    }
}
