<?php
/**
 * User Class for UniPulse
 * Handles user registration, authentication, and management
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $first_name;
    private $last_name;
    private $user_type;
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance();
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Validate required fields
            if (!$this->validateRegistrationData($data)) {
                return ['success' => false, 'message' => 'Invalid registration data'];
            }
            
            // Check if email already exists
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email address already registered'];
            }
            
            // Check if username already exists
            if ($this->usernameExists($data['username'])) {
                return ['success' => false, 'message' => 'Username already taken'];
            }
            
            // Validate password strength
            if (!$this->validatePasswordStrength($data['password'])) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, lowercase, number and special character'];
            }
            
            // Hash password
            $password_hash = $this->hashPassword($data['password']);
            
            // Generate verification token
            $verification_token = $this->generateToken();
            
            // Insert user into database
            $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, verification_token) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['username'],
                $data['email'],
                $password_hash,
                $data['first_name'] ?? '',
                $data['last_name'] ?? '',
                $data['user_type'] ?? 'student',
                $verification_token
            ];
            
            $this->db->query($sql, $params);
            $userId = $this->db->lastInsertId();
            
            // Load user data
            $this->loadUserById($userId);
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user' => $this->getUserData(),
                'verification_token' => $verification_token
            ];
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password) {
        try {
            // Find user by email
            $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
            $user = $this->db->fetchRow($sql, [$email]);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Verify password
            if (!$this->verifyPassword($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Load user data
            $this->loadUserData($user);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $this->getUserData()
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Hash password using PHP password_hash
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3          // 3 threads
        ]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Validate registration data
     */
    private function validateRegistrationData($data) {
        $required = ['username', 'email', 'password'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Validate username (alphanumeric and underscore only)
        if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $data['username'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password) {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return false;
        }
        
        // Must contain uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Must contain lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Must contain number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // Must contain special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $result = $this->db->fetchRow($sql, [$email]);
        return $result['count'] > 0;
    }
    
    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $result = $this->db->fetchRow($sql, [$username]);
        return $result['count'] > 0;
    }
    
    /**
     * Generate random token
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Load user by ID
     */
    private function loadUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $user = $this->db->fetchRow($sql, [$id]);
        
        if ($user) {
            $this->loadUserData($user);
        }
    }
    
    /**
     * Load user data into object properties
     */
    private function loadUserData($user) {
        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->email = $user['email'];
        $this->first_name = $user['first_name'];
        $this->last_name = $user['last_name'];
        $this->user_type = $user['user_type'];
    }
    
    /**
     * Get user data array
     */
    public function getUserData() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_type' => $this->user_type
        ];
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$userId]);
    }
    
    /**
     * Get user by ID (static method)
     */
    public static function getUserById($id) {
        $user = new self();
        $user->loadUserById($id);
        return $user->id ? $user : null;
    }
    
    /**
     * Get user by email (static method)
     */
    public static function getUserByEmail($email) {
        $db = DatabaseConfig::getInstance();
        $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
        $userData = $db->fetchRow($sql, [$email]);
        
        if ($userData) {
            $user = new self();
            $user->loadUserData($userData);
            return $user;
        }
        
        return null;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getUserType() { return $this->user_type; }
    public function getFullName() { return trim($this->first_name . ' ' . $this->last_name); }
}