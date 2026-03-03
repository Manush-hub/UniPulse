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
                // Log admin activity
                AdminActivity::log(
                    $data['user']['id'],
                    $data['user']['name'],
                    'moderator_edited',
                    'moderator',
                    (int)$id,
                    $moderator->full_name,
                    'Updated moderator ' . $moderator->full_name,
                    'user-pen'
                );
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
        $modForLog = $moderatorModel->find($id);
        if ($moderatorModel->activate($id)) {
            if ($modForLog) {
                $adminUser = AuthService::getCurrentUser();
                AdminActivity::log(
                    $adminUser['id'],
                    $adminUser['name'],
                    'moderator_activated',
                    'moderator',
                    (int)$id,
                    $modForLog->full_name,
                    'Reactivated moderator ' . $modForLog->full_name,
                    'user-check'
                );
            }
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
        
        // Fetch moderator info before deletion for activity log
        $modToDelete = $moderatorModel->find($id);
        // If no pending approvals, proceed with deletion
        if ($moderatorModel->deleteModerator($id)) {
            if ($modToDelete) {
                $adminUser = AuthService::getCurrentUser();
                AdminActivity::log(
                    $adminUser['id'],
                    $adminUser['name'],
                    'moderator_deleted',
                    'moderator',
                    (int)$id,
                    $modToDelete->full_name,
                    'Deleted moderator ' . $modToDelete->full_name,
                    'user-xmark'
                );
            }
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
