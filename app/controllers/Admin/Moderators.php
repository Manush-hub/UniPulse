<?php

class Moderators extends Controller{

    public function index($a = '', $b = '', $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        
        // Get all moderators
        $moderatorModel = new Moderator();
        $data['moderators'] = $moderatorModel->findAll();
        
        // Handle success/error messages
        $data['message'] = isset($_GET['success']) ? $_GET['success'] : (isset($_GET['error']) ? $_GET['error'] : '');
        $data['message_type'] = isset($_GET['success']) ? 'success' : 'error';
        
        $this->view('Admin/moderators_list', $data);
    }
    
    public function create($a = '', $b = '', $c = ''){
        // Handle moderator creation
        require_once '../app/controllers/Admin/Moderator_create.php';
        $moderatorCreate = new Moderator_create();
        $moderatorCreate->index($a, $b, $c);
    }
    
    public function edit($id = '', $b = '', $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$id) {
            header('Location: /unipulse/public/admin/moderators?error=No moderator ID provided');
            exit();
        }
        
        $moderatorModel = new Moderator();
        $moderator = $moderatorModel->find($id);
        
        if (!$moderator) {
            header('Location: /unipulse/public/admin/moderators?error=Moderator not found');
            exit();
        }
        
        $data = [];
        $data['user'] = AuthService::getCurrentUser();
        $data['moderator'] = $moderator;
        $data['universities'] = Moderator::getAvailableUniversities();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $moderatorModel->updateModerator($id, $_POST);
            
            if ($result['success']) {
                header('Location: /unipulse/public/admin/moderators?success=' . urlencode($result['message']));
                exit();
            } else {
                $data['errors'] = $result['errors'];
            }
        }
        
        $this->view('Admin/moderator_edit', $data);
    }
    
    public function activate($id = '', $b = '', $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$id) {
            header('Location: /unipulse/public/admin/moderators?error=No moderator ID provided');
            exit();
        }
        
        $moderatorModel = new Moderator();
        if ($moderatorModel->activate($id)) {
            header('Location: /unipulse/public/admin/moderators?success=Moderator activated successfully');
        } else {
            header('Location: /unipulse/public/admin/moderators?error=Failed to activate moderator');
        }
        exit();
    }
    
    public function deactivate($id = '', $b = '', $c = ''){
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$id) {
            header('Location: /unipulse/public/admin/moderators?error=No moderator ID provided');
            exit();
        }
        
        $moderatorModel = new Moderator();
        
        // Check if moderator has pending approvals
        if ($moderatorModel->hasPendingApprovals($id)) {
            $pendingCount = $moderatorModel->getPendingApprovalsCount($id);
            $moderator = $moderatorModel->find($id);
            $message = "Cannot delete moderator {$moderator->full_name}. ";
            $message .= "This moderator has {$pendingCount} pending publisher approval(s) ";
            $message .= "for {$moderator->university_name}. ";
            $message .= "Please reassign or resolve these approvals first.";
            
            header('Location: /unipulse/public/admin/moderators?error=' . urlencode($message));
            exit();
        }
        
        // If no pending approvals, proceed with deletion
        if ($moderatorModel->deleteModerator($id)) {
            header('Location: /unipulse/public/admin/moderators?success=Moderator deleted successfully');
        } else {
            header('Location: /unipulse/public/admin/moderators?error=Failed to delete moderator');
        }
        exit();
    }
    
    public function check_pending($id = '', $b = '', $c = '') {
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No moderator ID provided']);
            exit();
        }
        
        $moderatorModel = new Moderator();
        $hasPending = $moderatorModel->hasPendingApprovals($id);
        $pendingCount = $moderatorModel->getPendingApprovalsCount($id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'hasPending' => $hasPending,
            'pendingCount' => $pendingCount
        ]);
        exit();
    }
}
