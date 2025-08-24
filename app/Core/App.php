<?php

class App {
    
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];
    protected $userRole = 'User'; // Default role
    
    public function __construct() {
        $this->parseUrl();
        $this->setUserRole();
        // Remove the automatic loadController call - let index.php control when to load
    }
    
    public function parseUrl() {
        if (isset($_GET['url'])) {
            $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
            
            // Check if the first part is a role (user, admin, publisher, etc.)
            $validRoles = ['user', 'publisher', 'admin', 'moderator', 'sponsor'];
            
            if (isset($url[0]) && in_array(strtolower($url[0]), $validRoles)) {
                // Role-based URL: /user/dashboard or /admin/users
                $this->userRole = ucfirst(strtolower($url[0]));
                $this->controller = isset($url[1]) ? ucfirst($url[1]) : 'Dashboard'; // Default to Dashboard
                
                if (isset($url[2])) {
                    $this->method = $url[2];
                    unset($url[2]);
                }
                
                // Remove role and controller from params
                unset($url[0], $url[1]);
                $this->params = $url ? array_values($url) : [];
            } else {
                // Regular URL: /home or /signin
                $this->controller = isset($url[0]) ? ucfirst($url[0]) : 'Home';
                
                if (isset($url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                }
                
                unset($url[0]);
                $this->params = $url ? array_values($url) : [];
            }
        }
    }
    
    public function setUserRole() {
        // If role was already set from URL parsing, don't override it
        if ($this->userRole !== 'User') {
            return;
        }
        
        // Get user role from session, JWT, or database
        // This is where you implement your authentication logic
        if (isset($_SESSION['user_role'])) {
            $this->userRole = $_SESSION['user_role'];
        } elseif (isset($_SESSION['user_type'])) {
            $this->userRole = $_SESSION['user_type'];
        } else {
            // Default to User for non-authenticated users
            $this->userRole = 'User';
        }
        
        // Validate role exists
        $validRoles = ['User', 'Publisher', 'Admin', 'Moderator', 'Sponsor'];
        if (!in_array($this->userRole, $validRoles)) {
            $this->userRole = 'User';
        }
    }
    
    public function loadController() {
        // Try role-specific controller first
        $roleControllerFile = "../app/controllers/{$this->userRole}/" . $this->controller . ".php";
        
        if (file_exists($roleControllerFile)) {
            require_once $roleControllerFile;
            $controllerInstance = new $this->controller;
            
            // Check if method exists in controller
            if (method_exists($controllerInstance, $this->method)) {
                call_user_func_array([$controllerInstance, $this->method], $this->params);
            } else {
                // Method doesn't exist, call index
                call_user_func_array([$controllerInstance, 'index'], $this->params);
            }
            return;
        }
        
        // Fallback to general controllers
        $generalControllerFile = "../app/controllers/" . $this->controller . ".php";
        
        if (file_exists($generalControllerFile)) {
            require_once $generalControllerFile;
            $controllerInstance = new $this->controller;
            
            // Check if method exists in controller
            if (method_exists($controllerInstance, $this->method)) {
                call_user_func_array([$controllerInstance, $this->method], $this->params);
            } else {
                // Method doesn't exist, call index
                call_user_func_array([$controllerInstance, 'index'], $this->params);
            }
        } else {
            // Load 404 controller
            $this->load404();
        }
    }
    
    public function load404() {
        require_once "../app/controllers/_404.php";
        $controllerInstance = new _404;
        call_user_func_array([$controllerInstance, 'index'], []);
    }
    
    public function getUserRole() {
        return $this->userRole;
    }
}
