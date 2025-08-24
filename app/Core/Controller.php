<?php

class Controller {
    
    protected $userRole;
    
    public function __construct() {
        $this->userRole = $this->getUserRole();
    }
    
    public function view($name, $data = [], $role = null) {
        
        if (!empty($data))
            extract($data);
        
        // Use provided role or default to current user role
        $viewRole = $role ?? $this->userRole ?? 'User';
        
        // Make the controller instance available in views for asset loading
        $controller = $this;
        
        // Try role-specific view first
        $filename = "../app/views/{$viewRole}/" . $name . ".view.php";
        
        // Fallback to general views if role-specific doesn't exist
        if (!file_exists($filename)) {
            $filename = "../app/views/" . $name . ".view.php";
        }
        
        if (file_exists($filename)) {
            require $filename;
        } else {
            $filename = "../app/views/404.view.php";
            require $filename;
        }
    }
    
    // Method to get current user role
    protected function getUserRole() {
        // Get role from session or other authentication method
        if (isset($_SESSION['user_role'])) {
            return $_SESSION['user_role'];
        } elseif (isset($_SESSION['user_type'])) {
            return $_SESSION['user_type'];
        }
        
        return 'User'; // Default role
    }
    
    // Method to load role-specific CSS assets
    public function loadCSS($filename, $role = null) {
        $assetRole = $role ?? $this->userRole ?? 'User';
        
        // Try role-specific CSS first
        $rolePath = "/unipulse/public/assets/css/{$assetRole}/{$filename}";
        if (file_exists("../public/assets/css/{$assetRole}/{$filename}")) {
            return $rolePath;
        }
        
        // Fallback to general CSS
        $generalPath = "/unipulse/public/assets/css/{$filename}";
        if (file_exists("../public/assets/css/{$filename}")) {
            return $generalPath;
        }
        
        return null; // File not found
    }
    
    // Method to load role-specific JS assets
    public function loadJS($filename, $role = null) {
        $assetRole = $role ?? $this->userRole ?? 'User';
        
        // Try role-specific JS first
        $rolePath = "/unipulse/public/assets/js/{$assetRole}/{$filename}";
        if (file_exists("../public/assets/js/{$assetRole}/{$filename}")) {
            return $rolePath;
        }
        
        // Fallback to general JS
        $generalPath = "/unipulse/public/assets/js/{$filename}";
        if (file_exists("../public/assets/js/{$filename}")) {
            return $generalPath;
        }
        
        return null; // File not found
    }
    
    // Method to check if user has required role
    protected function requireRole($requiredRole) {
        if ($this->userRole !== $requiredRole) {
            // Redirect to unauthorized page or throw error
            header("Location: /unipulse/unauthorized");
            exit();
        }
    }
    
    // Method to check if user has any of the required roles
    protected function requireAnyRole($requiredRoles) {
        if (!in_array($this->userRole, $requiredRoles)) {
            // Redirect to unauthorized page or throw error
            header("Location: /unipulse/unauthorized");
            exit();
        }
    }
    
    // Method to get role hierarchy for permissions
    protected function getRoleHierarchy() {
        return [
            'Admin' => 5,
            'Moderator' => 4,
            'Publisher' => 3,
            'Sponsor' => 2,
            'User' => 1
        ];
    }
    
    // Method to check if current user has higher or equal role level
    protected function hasRoleLevel($requiredRole) {
        $hierarchy = $this->getRoleHierarchy();
        $userLevel = $hierarchy[$this->userRole] ?? 0;
        $requiredLevel = $hierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    // Method to load models with role support
    public function model($model, $role = null) {
        $modelRole = $role ?? $this->userRole ?? 'User';
        
        // Try role-specific model first
        $roleModelFile = "../app/models/{$modelRole}/" . $model . ".php";
        if (file_exists($roleModelFile)) {
            require_once $roleModelFile;
            return new $model();
        }
        
        // Fallback to general models
        $generalModelFile = "../app/models/" . $model . ".php";
        if (file_exists($generalModelFile)) {
            require_once $generalModelFile;
            return new $model();
        }
        
        return false; // Model not found
    }
}
