<?php
/**
 * Session Management for UniPulse
 * Handles user sessions and authentication checks
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

class SessionManager {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = DatabaseConfig::getInstance();
        $this->initializeSession();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize session
     */
    private function initializeSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session settings for security
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
        }
    }
    
    /**
     * Login user and create session
     */
    public function login(User $user) {
        try {
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Store user data in session
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['user_type'] = $user->getUserType();
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Store session in database
            $this->storeSessionInDatabase($user->getId());
            
            return true;
            
        } catch (Exception $e) {
            error_log("Session login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logout user and destroy session
     */
    public function logout() {
        try {
            // Remove session from database
            if (isset($_SESSION['user_id'])) {
                $this->removeSessionFromDatabase();
            }
            
            // Clear session data
            $_SESSION = array();
            
            // Delete session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            // Destroy session
            session_destroy();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Session logout error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return false;
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout (24 hours)
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 86400)) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return User::getUserById($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get current user type
     */
    public function getCurrentUserType() {
        return $this->isLoggedIn() ? $_SESSION['user_type'] : null;
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userType = $this->getCurrentUserType();
        
        // Admin has all permissions
        if ($userType === 'admin') {
            return true;
        }
        
        return $userType === $role;
    }
    
    /**
     * Require authentication (redirect if not logged in)
     */
    public function requireAuth($redirectUrl = '/public/signin.php') {
        if (!$this->isLoggedIn()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Authentication required',
                'redirect' => $redirectUrl
            ]);
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
            exit;
        }
    }
    
    /**
     * Store session in database
     */
    private function storeSessionInDatabase($userId) {
        try {
            // Clean up old sessions for this user (keep only last 5)
            $this->cleanupUserSessions($userId);
            
            $sql = "INSERT INTO user_sessions (id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)";
            $params = [
                session_id(),
                $userId,
                $this->getClientIP(),
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
            ];
            
            $this->db->query($sql, $params);
            
        } catch (Exception $e) {
            error_log("Failed to store session in database: " . $e->getMessage());
        }
    }
    
    /**
     * Remove session from database
     */
    private function removeSessionFromDatabase() {
        try {
            $sql = "DELETE FROM user_sessions WHERE id = ?";
            $this->db->query($sql, [session_id()]);
        } catch (Exception $e) {
            error_log("Failed to remove session from database: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up old sessions for user
     */
    private function cleanupUserSessions($userId, $keepCount = 5) {
        try {
            // Keep only the most recent sessions
            $sql = "DELETE FROM user_sessions 
                    WHERE user_id = ? 
                    AND id NOT IN (
                        SELECT id FROM (
                            SELECT id FROM user_sessions 
                            WHERE user_id = ? 
                            ORDER BY created_at DESC 
                            LIMIT ?
                        ) tmp
                    )";
            
            $this->db->query($sql, [$userId, $userId, $keepCount]);
            
        } catch (Exception $e) {
            error_log("Failed to cleanup user sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up expired sessions (run periodically)
     */
    public function cleanupExpiredSessions() {
        try {
            // Remove sessions older than 24 hours
            $sql = "DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Failed to cleanup expired sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get session data for API responses
     */
    public function getSessionData() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'user_type' => $_SESSION['user_type'],
            'login_time' => $_SESSION['login_time']
        ];
    }
    
    /**
     * Update session activity
     */
    public function updateActivity() {
        if ($this->isLoggedIn()) {
            try {
                $sql = "UPDATE user_sessions SET last_activity = CURRENT_TIMESTAMP WHERE id = ?";
                $this->db->query($sql, [session_id()]);
            } catch (Exception $e) {
                error_log("Failed to update session activity: " . $e->getMessage());
            }
        }
    }
}