<?php

class AdminDashboard extends Controller {

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get dashboard statistics
        $moderatorModel = new Moderator();
        $adminModel = new Admin();
        
        $activeModerators = $moderatorModel->getActiveModerators();
        $activeAdmins = $adminModel->getActiveAdmins();
        
        $data['stats'] = [
            'total_moderators' => count($activeModerators),
            'total_admins' => count($activeAdmins)
        ];
        
        $this->view('Admin/dashboard', $data);
    }
    
    /**
     * Manage moderators
     */
    public function moderators($action = '', $id = '') {
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $moderatorModel = new Moderator();
        
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $postData = $_POST;
                    $postData['assigned_by'] = $data['user']['id'];
                    
                    $result = $moderatorModel->create($postData);
                    
                    if ($result['success']) {
                        header('Location: /unipulse/public/admin/moderators?success=' . urlencode($result['message']));
                        exit();
                    } else {
                        $data['errors'] = $result['errors'];
                        $data['old_data'] = $postData;
                    }
                }
                $this->view('Admin/moderator_create', $data);
                break;
                
            case 'edit':
                if (!$id) {
                    header('Location: /unipulse/public/admin/moderators');
                    exit();
                }
                
                $moderator = $moderatorModel->find($id);
                if (!$moderator) {
                    header('Location: /unipulse/public/admin/moderators?error=Moderator not found');
                    exit();
                }
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $moderatorModel->updateModerator($id, $_POST);
                    
                    if ($result['success']) {
                        header('Location: /unipulse/public/admin/moderators?success=' . urlencode($result['message']));
                        exit();
                    } else {
                        $data['errors'] = $result['errors'];
                    }
                }
                
                $data['moderator'] = $moderator;
                $data['permissions'] = $moderatorModel->getPermissions($id);
                $this->view('Admin/moderator_edit', $data);
                break;
                
            case 'deactivate':
                if ($id && $moderatorModel->deactivate($id)) {
                    header('Location: /unipulse/public/admin/moderators?success=Moderator deactivated');
                } else {
                    header('Location: /unipulse/public/admin/moderators?error=Failed to deactivate moderator');
                }
                exit();
                
            case 'activate':
                if ($id && $moderatorModel->activate($id)) {
                    header('Location: /unipulse/public/admin/moderators?success=Moderator activated');
                } else {
                    header('Location: /unipulse/public/admin/moderators?error=Failed to activate moderator');
                }
                exit();
                
            default:
                $data['moderators'] = $moderatorModel->all();
                $data['message'] = isset($_GET['success']) ? $_GET['success'] : (isset($_GET['error']) ? $_GET['error'] : '');
                $data['message_type'] = isset($_GET['success']) ? 'success' : 'error';
                $this->view('Admin/moderators_list', $data);
                break;
        }
    }
    
    /**
     * Manage admins
     */
    public function admins($action = '', $id = '') {
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $adminModel = new Admin();
        
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $adminModel->create($_POST);
                    
                    if ($result['success']) {
                        header('Location: /unipulse/public/admin/admins?success=' . urlencode($result['message']));
                        exit();
                    } else {
                        $data['errors'] = $result['errors'];
                        $data['old_data'] = $_POST;
                    }
                }
                $this->view('Admin/admin_create', $data);
                break;
                
            default:
                $data['admins'] = $adminModel->all();
                $data['message'] = isset($_GET['success']) ? $_GET['success'] : (isset($_GET['error']) ? $_GET['error'] : '');
                $data['message_type'] = isset($_GET['success']) ? 'success' : 'error';
                $this->view('Admin/admins_list', $data);
                break;
        }
    }
}
