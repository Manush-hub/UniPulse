<?php

class ModeratorUserreports extends Controller {
    
    public function __construct() {
        // Ensure moderator is logged in
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            // Redirect to login or show unauthorized page
            header('Location: /unipulse/public/signin');
            exit();
        }
    }
    
    /**
     * User reports page
     */
    public function index() {
        try {
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            
            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            
            // Get reports for this university
            $reportModel = new Report();
            $pendingReports = $reportModel->getReportsForUniversity($moderator->university, 50);
            
            // Get report statistics
            $reportStats = $reportModel->getReportStatsForUniversity($moderator->university);
            
            // Get moderator permissions
            $permissions = json_decode($moderator->permissions ?? '{}', true);
            
            $data = [
                'title' => 'User Reports',
                'page' => 'user_reports',
                'moderator' => $moderator,
                'pendingReports' => $pendingReports,
                'reportStats' => $reportStats,
                'permissions' => $permissions
            ];
            
        } catch (Exception $e) {
            error_log("Error loading user reports page: " . $e->getMessage());
            
            // Fallback data
            $data = [
                'title' => 'User Reports',
                'page' => 'user_reports',
                'moderator' => (object) ['full_name' => 'Moderator'],
                'pendingReports' => [],
                'reportStats' => (object) [
                    'pending' => 0,
                    'in_progress' => 0,
                    'resolved' => 0,
                    'resolved_today' => 0
                ],
                'permissions' => []
            ];
        }
        
        $this->view('Moderator/user_reports', $data);
    }
    
    /**
     * Assign a report to the current moderator
     */
    public function assign($reportId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($reportId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Report ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $reportModel = new Report();
            
            $result = $reportModel->assignToModerator($reportId, $moderatorData['id']);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Report assigned successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to assign report'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Resolve a report
     */
    public function resolve($reportId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($reportId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Report ID is required']);
            exit();
        }

        try {
            $moderatorData = AuthService::getCurrentUser();
            $reportModel = new Report();
            
            // Get resolution details from POST data
            $resolution = $_POST['resolution'] ?? '';
            $action_taken = $_POST['action_taken'] ?? '';
            
            $result = $reportModel->resolve($reportId, $moderatorData['id'], $resolution, $action_taken);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Report resolved successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to resolve report'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get report details
     */
    public function details($reportId = '') {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if (empty($reportId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Report ID is required']);
            exit();
        }

        try {
            $reportModel = new Report();
            $report = $reportModel->getReportDetails($reportId);
            
            if ($report) {
                echo json_encode([
                    'success' => true,
                    'report' => $report
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Report not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Filter reports based on criteria
     */
    public function filter() {
        header('Content-Type: application/json');
        
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            
            $reportModel = new Report();
            
            // Get filter parameters
            $status = $_GET['status'] ?? 'all';
            $type = $_GET['type'] ?? 'all';
            $priority = $_GET['priority'] ?? 'all';
            $dateRange = $_GET['date'] ?? 'all';
            
            $reports = $reportModel->getFilteredReports(
                $moderator->university,
                $status,
                $type,
                $priority,
                $dateRange
            );
            
            echo json_encode([
                'success' => true,
                'reports' => $reports
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
?>