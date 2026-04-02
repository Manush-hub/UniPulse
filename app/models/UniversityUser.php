<?php

class UniversityUser
{

    use Model;
    protected $table = 'university_users';

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
        'user_role',
        'is_verified',
        'email_verified_at',
        'profile_photo',
        'cover_photo',
        'university',
        'faculty',
        'student_staff_id',
        'academic_year',
        'nic'
    ];

    private const UNIVERSITY_EMAIL_DOMAINS = [
        'university-of-colombo' => 'cmb.ac.lk',
        'university-of-peradeniya' => 'pdn.ac.lk',
        'university-of-sri-jayewardenepura' => 'sjp.ac.lk',
        'university-of-kelaniya' => 'kln.ac.lk',
        'university-of-moratuwa' => 'uom.lk',
        'university-of-jaffna' => 'jfn.ac.lk',
        'university-of-ruhuna' => 'ruh.ac.lk',
        'eastern-university' => 'esn.ac.lk',
        'south-eastern-university' => 'seu.ac.lk',
        'rajarata-university' => 'rjt.ac.lk',
        'sabaragamuwa-university' => 'sab.ac.lk',
        'wayamba-university' => 'wyb.ac.lk',
        'uva-wellassa-university' => 'uwu.ac.lk',
        'open-university' => 'ou.ac.lk',
        'buddhist-and-pali-university' => 'bpuls.ac.lk',
        'sliit' => 'sliit.lk',
        'nsbm' => 'nsbm.ac.lk',
        'cinec' => 'cinec.edu',
        'apiit' => 'apiit.lk',
        'metropolitan-campus' => 'kiu.ac.lk'
    ];

    public function create($data)
    {
        $query = "INSERT INTO university_users (
            full_name, email, phone, country_code, password_hash, 
            university, faculty, student_staff_id, academic_year, 
            nic, gender, interests
        ) VALUES (
            :full_name, :email, :phone, :country_code, :password_hash,
            :university, :faculty, :student_staff_id, :academic_year,
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
        $query = "SELECT * FROM university_users WHERE email = :email LIMIT 1";
        return $this->getRow($query, ['email' => $email]);
    }

    public function findByNIC($nic)
    {
        $query = "SELECT * FROM university_users WHERE nic = :nic LIMIT 1";
        return $this->getRow($query, ['nic' => $nic]);
    }

    public function findByStudentStaffId($student_staff_id)
    {
        $query = "SELECT * FROM university_users WHERE student_staff_id = :student_staff_id LIMIT 1";
        return $this->getRow($query, ['student_staff_id' => $student_staff_id]);
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
            'university' as user_type
        FROM university_users 
        ORDER BY created_at DESC 
        LIMIT {$limit}";

        return $this->query($query, []);
    }

    public function studentStaffIdExists($student_staff_id)
    {
        $user = $this->findByStudentStaffId($student_staff_id);
        return $user !== false;
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
            'university' => 'University',
            'faculty' => 'Faculty',
            'student-staff-id' => 'Student/Staff ID',
            'academic-year' => 'Academic Year',
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

        // University and email domain matching validation
        if (!empty($data['university']) && !empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            if (!$this->isUniversityEmailMatch($data['university'], $data['email'])) {
                $expectedDomain = self::UNIVERSITY_EMAIL_DOMAINS[$data['university']] ?? null;

                if ($expectedDomain) {
                    $errors[] = "University and Email Address mismatch. Please use your university email domain (@{$expectedDomain}) for the selected university.";
                } else {
                    $errors[] = "University and Email Address mismatch. Please use your official university email address.";
                }
            }
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

        // Check if Student/Staff ID already exists
        if (!empty($data['student-staff-id']) && $this->studentStaffIdExists($data['student-staff-id'])) {
            $errors[] = "An account with this Student/Staff ID already exists";
        }

        return $errors;
    }

    private function isUniversityEmailMatch($university, $email)
    {
        if (empty(self::UNIVERSITY_EMAIL_DOMAINS[$university])) {
            return true;
        }

        $emailDomain = strtolower(substr(strrchr(trim($email), '@'), 1));
        $expectedDomain = strtolower(self::UNIVERSITY_EMAIL_DOMAINS[$university]);

        if ($emailDomain === $expectedDomain) {
            return true;
        }

        // Allow valid subdomains such as student.cmb.ac.lk
        return str_ends_with($emailDomain, '.' . $expectedDomain);
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
            'university' => $data['university'],
            'faculty' => $data['faculty'],
            'student_staff_id' => trim($data['student-staff-id']),
            'academic_year' => $data['academic-year'],
            'nic' => strtoupper(trim($data['nic'])),
            'gender' => !empty($data['gender']) ? $data['gender'] : null,
            'interests' => !empty($interests) ? json_encode($interests) : null
        ];
    }
}
