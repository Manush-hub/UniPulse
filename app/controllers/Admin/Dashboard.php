<?php

class AdminDashboard extends Controller {
    use Database;

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
        
        // Get recent registrations from all user types
        $universityUser = new UniversityUser();
        $publicUser = new PublicUser();
        $publisher = new Publisher();
        $sponsor = new Sponsor();
        
        // Get recent registrations (limit 5 from each)
        $universityRegistrations = $universityUser->getRecentRegistrations(5);
        $publicRegistrations = $publicUser->getRecentRegistrations(5);
        $publisherRegistrations = $publisher->getRecentRegistrations(5);
        $sponsorRegistrations = $sponsor->getRecentRegistrations(5);
        
        // Merge all registrations
        $recentRegistrations = array_merge(
            is_array($universityRegistrations) ? $universityRegistrations : [],
            is_array($publicRegistrations) ? $publicRegistrations : [],
            is_array($publisherRegistrations) ? $publisherRegistrations : [],
            is_array($sponsorRegistrations) ? $sponsorRegistrations : []
        );
        
        // Sort by created_at descending
        if (count($recentRegistrations) > 0) {
            usort($recentRegistrations, function($a, $b) {
                $timeA = is_object($a) ? strtotime($a->created_at) : strtotime($a['created_at']);
                $timeB = is_object($b) ? strtotime($b->created_at) : strtotime($b['created_at']);
                return $timeB - $timeA;
            });
        }
        
        // Get only top 10
        $recentRegistrations = array_slice($recentRegistrations, 0, 10);
        
        $data['recent_registrations'] = $recentRegistrations;
        
        // Get pending publisher approvals from all universities
        $pendingPublishers = $publisher->getAllPending();
        $data['pending_approvals'] = is_array($pendingPublishers) ? $pendingPublishers : [];
        
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
    
    /**
     * API endpoint to get recent activity
     */
    public function getRecentActivity() {
        header('Content-Type: application/json');
        
        // Temporarily disable authentication for testing
        // if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Access denied']);
        //     return;
        // }
        
        // Get recent registrations, events, etc.
        $activities = [];
        
        // Get recent user registrations
        $recentUsers = $this->query("SELECT first_name, last_name, type, created_at FROM users ORDER BY created_at DESC LIMIT 3");
        if ($recentUsers) {
            foreach ($recentUsers as $user) {
                $timeAgo = $this->timeAgo($user->created_at);
                $activities[] = [
                    'id' => count($activities) + 1,
                    'type' => 'user',
                    'title' => 'New user registration',
                    'description' => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) . ' registered as ' . ucfirst($user->type),
                    'time' => $timeAgo,
                    'icon' => 'user-plus'
                ];
            }
        }
        
        // Get recent events
        $recentEvents = $this->query("SELECT title, created_at FROM events ORDER BY created_at DESC LIMIT 2");
        if ($recentEvents) {
            foreach ($recentEvents as $event) {
                $timeAgo = $this->timeAgo($event->created_at);
                $activities[] = [
                    'id' => count($activities) + 1,
                    'type' => 'event',
                    'title' => 'Event published',
                    'description' => $event->title . ' was published',
                    'time' => $timeAgo,
                    'icon' => 'calendar'
                ];
            }
        }
        
        // Sort by most recent
        usort($activities, function($a, $b) {
            return strcmp($b['time'], $a['time']);
        });
        
        echo json_encode(array_slice($activities, 0, 5));
    }
    
    /**
     * API endpoint to get pending approvals
     */
    public function getPendingApprovals() {
        header('Content-Type: application/json');
        
        // Temporarily disable authentication for testing
        // if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Access denied']);
        //     return;
        // }
        
        // Create some sample pending approvals for now
        $approvals = [
            [
                'id' => 1,
                'name' => 'Sample Organization',
                'type' => 'Organization Verification',
                'submitted' => '2 hours ago'
            ],
            [
                'id' => 2,
                'name' => 'Tech Workshop 2025',
                'type' => 'Event Approval',
                'submitted' => '1 day ago'
            ]
        ];
        
        echo json_encode($approvals);
    }
    
    /**
     * API endpoint to get recent users
     */
    public function getRecentUsers() {
        header('Content-Type: application/json');
        
        // Temporarily disable authentication for testing
        // if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Access denied']);
        //     return;
        // }
        
        $users = $this->query("SELECT id, first_name, last_name, email, type, created_at FROM users ORDER BY created_at DESC LIMIT 10");
        
        $userData = [];
        if ($users) {
            foreach ($users as $user) {
                $userData[] = [
                    'id' => $user->id,
                    'name' => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                    'email' => $user->email,
                    'role' => ucfirst($user->type),
                    'registrationDate' => $user->created_at,
                    'status' => 'active', // Default to active since status column might not exist
                    'avatar' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1))
                ];
            }
        }
        
        echo json_encode($userData);
    }
    
    /**
     * API endpoint to get dashboard stats
     */
    public function getStats() {
        header('Content-Type: application/json');
        
        // Temporarily disable authentication for testing
        // if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Access denied']);
        //     return;
        // }
        
        // Get total users
        $totalUsersResult = $this->query("SELECT COUNT(*) as count FROM users");
        $totalUsers = $totalUsersResult ? $totalUsersResult[0]->count : 0;
        
        // Get active events
        $activeEventsResult = $this->query("SELECT COUNT(*) as count FROM events");
        $activeEvents = $activeEventsResult ? $activeEventsResult[0]->count : 0;
        
        // Get pending approvals count (simplified since status column might not exist)
        $totalPending = 3; // Set a default value for now
        
        // Get users registered this week
        $weekAgo = date('Y-m-d H:i:s', strtotime('-1 week'));
        $newUsersThisWeekResult = $this->query("SELECT COUNT(*) as count FROM users WHERE created_at >= ?", [$weekAgo]);
        $newUsersThisWeek = $newUsersThisWeekResult ? $newUsersThisWeekResult[0]->count : 0;
        
        // Get events this week
        $eventsThisWeekResult = $this->query("SELECT COUNT(*) as count FROM events WHERE created_at >= ?", [$weekAgo]);
        $eventsThisWeek = $eventsThisWeekResult ? $eventsThisWeekResult[0]->count : 0;
        
        $stats = [
            'totalUsers' => intval($totalUsers),
            'activeEvents' => intval($activeEvents),
            'pendingApprovals' => intval($totalPending),
            'systemHealth' => 98, // This could be calculated based on various factors
            'newUsersThisWeek' => intval($newUsersThisWeek),
            'userActiveRate' => 94, // This would need more complex calculation
            'eventsThisWeek' => intval($eventsThisWeek),
            'attendanceRate' => 78, // This would need attendance tracking
            'systemUptime' => 98,
            'avgResponseTime' => '1.2s',
            'errorRate' => '0.2%'
        ];
        
        echo json_encode($stats);
    }
    
    /**
     * Simple test endpoint
     */
    public function test() {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'API is working']);
    }
    
    /**
     * Helper function to calculate time ago
     */
    private function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2628000) return floor($time/86400) . ' days ago';
        
        return date('M j, Y', strtotime($datetime));
    }
    
    /**
     * Suspend a user account
     */
    public function suspendUser() {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['user_id'] ?? null;
        $userType = $data['user_type'] ?? null;
        $reason = $data['reason'] ?? '';
        
        if (!$userId || !$userType || !$reason) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }
        
        // Get admin ID
        $adminUser = AuthService::getCurrentUser();
        $adminId = $adminUser['id'];
        
        // Determine table name
        $tableName = $this->getUserTable($userType);
        if (!$tableName) {
            echo json_encode(['success' => false, 'message' => 'Invalid user type']);
            exit();
        }
        
        try {
            $conn = $this->connect();
            $query = "UPDATE {$tableName} SET 
                      is_suspended = 1, 
                      suspension_reason = :reason,
                      suspended_at = NOW(),
                      suspended_by = :admin_id
                      WHERE id = :user_id";
            
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
                'reason' => $reason,
                'admin_id' => $adminId,
                'user_id' => $userId
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Account suspended successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to suspend account']);
            }
        } catch (Exception $e) {
            error_log("Suspension error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
    
    /**
     * Reactivate a suspended user account
     */
    public function reactivateUser() {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['user_id'] ?? null;
        $userType = $data['user_type'] ?? null;
        
        if (!$userId || !$userType) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }
        
        // Determine table name
        $tableName = $this->getUserTable($userType);
        if (!$tableName) {
            echo json_encode(['success' => false, 'message' => 'Invalid user type']);
            exit();
        }
        
        try {
            $conn = $this->connect();
            $query = "UPDATE {$tableName} SET 
                      is_suspended = 0, 
                      suspension_reason = NULL,
                      suspended_at = NULL,
                      suspended_by = NULL
                      WHERE id = :user_id";
            
            $stmt = $conn->prepare($query);
            $result = $stmt->execute(['user_id' => $userId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Account reactivated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reactivate account']);
            }
        } catch (Exception $e) {
            error_log("Reactivation error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
    
    /**
     * Helper to get table name from user type
     */
    private function getUserTable($userType) {
        $tables = [
            'university' => 'university_users',
            'public' => 'public_users',
            'publisher' => 'publishers',
            'sponsor' => 'sponsors'
        ];
        
        return $tables[$userType] ?? null;
    }
    
    /**
     * Approve a publisher registration
     */
    public function approvePublisher() {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $publisherId = $input['publisher_id'] ?? null;
            
            if (!$publisherId) {
                echo json_encode(['success' => false, 'message' => 'Publisher ID is required']);
                return;
            }
            
            $publisher = new Publisher();
            $admin = AuthService::getCurrentUser();
            
            $result = $publisher->approve($publisherId, $admin['id']);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Publisher approved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to approve publisher']);
            }
        } catch (Exception $e) {
            error_log("Approval error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
    
    /**
     * Reject a publisher registration
     */
    public function rejectPublisher() {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $publisherId = $input['publisher_id'] ?? null;
            $rejectionReason = $input['rejection_reason'] ?? '';
            
            if (!$publisherId) {
                echo json_encode(['success' => false, 'message' => 'Publisher ID is required']);
                return;
            }
            
            if (empty($rejectionReason)) {
                echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
                return;
            }
            
            $publisher = new Publisher();
            $admin = AuthService::getCurrentUser();
            
            $result = $publisher->reject($publisherId, $admin['id'], $rejectionReason);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Publisher rejected successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reject publisher']);
            }
        } catch (Exception $e) {
            error_log("Rejection error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
