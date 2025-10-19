<?php

class Admins_list extends Controller{

    public function index($a = '', $b = '' , $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Handle status change actions
        if ($a === 'deactivate' && !empty($b)) {
            $this->deactivateAdmin($b);
            return;
        }
        
        if ($a === 'activate' && !empty($b)) {
            $this->activateAdmin($b);
            return;
        }
        
        if ($a === 'delete' && !empty($b)) {
            $this->deleteAdmin($b);
            return;
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get success message if any
        if (isset($_SESSION['success_message'])) {
            $data['message'] = $_SESSION['success_message'];
            $data['message_type'] = 'success';
            unset($_SESSION['success_message']);
        }
        
        // Get error message if any
        if (isset($_SESSION['error_message'])) {
            $data['message'] = $_SESSION['error_message'];
            $data['message_type'] = 'error';
            unset($_SESSION['error_message']);
        }
        
        // Get all admins
        $adminModel = new Admin();
        $data['admins'] = $adminModel->findAll();
        
        $this->view('Admin/admins_list', $data);
    }
    
    private function deactivateAdmin($id) {
        // Check if trying to deactivate self
        $currentUser = AuthService::getCurrentUser();
        if ($currentUser['id'] == $id) {
            $_SESSION['error_message'] = 'You cannot deactivate your own account';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        $adminModel = new Admin();
        if ($adminModel->deactivate($id)) {
            $_SESSION['success_message'] = 'Admin deactivated successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to deactivate admin';
        }
        
        header('Location: /unipulse/public/admin/admins_list');
        exit();
    }
    
    private function activateAdmin($id) {
        $adminModel = new Admin();
        if ($adminModel->activate($id)) {
            $_SESSION['success_message'] = 'Admin activated successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to activate admin';
        }
        
        header('Location: /unipulse/public/admin/admins_list');
        exit();
    }
    
    private function deleteAdmin($id) {
        // Check if trying to delete self
        $currentUser = AuthService::getCurrentUser();
        if ($currentUser['id'] == $id) {
            $_SESSION['error_message'] = 'You cannot delete your own account';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        // Check if this is the last active admin
        $adminModel = new Admin();
        $activeAdmins = $adminModel->getActiveAdmins();
        if (count($activeAdmins) <= 1) {
            $_SESSION['error_message'] = 'Cannot delete the last active admin';
            header('Location: /unipulse/public/admin/admins_list');
            exit();
        }
        
        if ($adminModel->delete($id)) {
            $_SESSION['success_message'] = 'Admin deleted successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to delete admin';
        }
        
        header('Location: /unipulse/public/admin/admins_list');
        exit();
    }
}
