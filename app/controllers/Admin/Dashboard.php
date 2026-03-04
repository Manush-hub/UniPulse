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
        
        // Count publishers and sponsors
        $publisherCountResult = $this->query("SELECT COUNT(*) as cnt FROM publishers");
        $sponsorCountResult   = $this->query("SELECT COUNT(*) as cnt FROM sponsors");
        $data['stats']['total_publishers'] = $publisherCountResult ? (int)$publisherCountResult[0]->cnt : 0;
        $data['stats']['total_sponsors']   = $sponsorCountResult   ? (int)$sponsorCountResult[0]->cnt   : 0;
        
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
     * Combines: user/publisher/sponsor registrations + admin management actions
     */
    public function getRecentActivity() {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $activities = [];

        // --- University user registrations ---
        $uniUsers = $this->query(
            "SELECT full_name, created_at FROM university_users ORDER BY created_at DESC LIMIT 10"
        );
        if ($uniUsers) {
            foreach ($uniUsers as $row) {
                $activities[] = [
                    'raw_time' => strtotime($row->created_at),
                    'type'        => 'registration',
                    'title'       => 'New University User Registration',
                    'description' => $row->full_name . ' registered as a University User',
                    'time'        => $this->timeAgo($row->created_at),
                    'icon'        => 'user-graduate',
                ];
            }
        }

        // --- Public user registrations ---
        $pubUsers = $this->query(
            "SELECT full_name, created_at FROM public_users ORDER BY created_at DESC LIMIT 10"
        );
        if ($pubUsers) {
            foreach ($pubUsers as $row) {
                $activities[] = [
                    'raw_time' => strtotime($row->created_at),
                    'type'        => 'registration',
                    'title'       => 'New Public User Registration',
                    'description' => $row->full_name . ' registered as a Public User',
                    'time'        => $this->timeAgo($row->created_at),
                    'icon'        => 'user-plus',
                ];
            }
        }

        // --- Publisher registrations ---
        $publishers = $this->query(
            "SELECT society_name, created_at FROM publishers ORDER BY created_at DESC LIMIT 10"
        );
        if ($publishers) {
            foreach ($publishers as $row) {
                $activities[] = [
                    'raw_time' => strtotime($row->created_at),
                    'type'        => 'registration',
                    'title'       => 'New Publisher Registration',
                    'description' => $row->society_name . ' registered as a Publisher',
                    'time'        => $this->timeAgo($row->created_at),
                    'icon'        => 'building',
                ];
            }
        }

        // --- Sponsor registrations ---
        $sponsors = $this->query(
            "SELECT company_name, created_at FROM sponsors ORDER BY created_at DESC LIMIT 10"
        );
        if ($sponsors) {
            foreach ($sponsors as $row) {
                $activities[] = [
                    'raw_time' => strtotime($row->created_at),
                    'type'        => 'registration',
                    'title'       => 'New Sponsor Registration',
                    'description' => $row->company_name . ' registered as a Sponsor',
                    'time'        => $this->timeAgo($row->created_at),
                    'icon'        => 'handshake',
                ];
            }
        }

        // --- Admin management actions (moderator/admin CRUD) ---
        $adminActions = $this->query(
            "SELECT action_type, admin_name, target_name, description, icon, created_at
             FROM admin_activities ORDER BY created_at DESC LIMIT 20"
        );
        if ($adminActions) {
            foreach ($adminActions as $row) {
                $titleMap = [
                    'moderator_created'    => 'Moderator Added',
                    'moderator_edited'     => 'Moderator Updated',
                    'moderator_deleted'    => 'Moderator Deleted',
                    'moderator_activated'  => 'Moderator Reactivated',
                    'admin_created'        => 'New Admin Added',
                    'user_suspended'       => 'Account Suspended',
                    'user_reactivated'     => 'Account Reactivated',
                    'publisher_approved'   => 'Publisher Approved',
                    'publisher_rejected'   => 'Publisher Rejected',
                    'mod_publisher_approved' => 'Publisher Approved',
                    'mod_publisher_rejected' => 'Publisher Rejected',
                ];
                $typeMap = [
                    'user_suspended'         => 'suspension',
                    'user_reactivated'       => 'reactivation',
                    'publisher_approved'     => 'approval',
                    'mod_publisher_approved' => 'approval',
                    'publisher_rejected'     => 'rejection',
                    'mod_publisher_rejected' => 'rejection',
                ];
                $actType = $typeMap[$row->action_type] ?? 'admin_action';
                $title = $titleMap[$row->action_type] ?? ucwords(str_replace('_', ' ', $row->action_type));
                $activities[] = [
                    'raw_time' => strtotime($row->created_at),
                    'type'        => $actType,
                    'title'       => $title,
                    'description' => $row->description . ' (by ' . $row->admin_name . ')',
                    'time'        => $this->timeAgo($row->created_at),
                    'icon'        => $row->icon,
                ];
            }
        }

        // Sort all by newest first
        usort($activities, function ($a, $b) {
            return $b['raw_time'] - $a['raw_time'];
        });

        // Strip raw_time before sending to client
        $activities = array_map(function ($item) {
            unset($item['raw_time']);
            return $item;
        }, $activities);

        echo json_encode(array_values(array_slice($activities, 0, 20)));
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
        
        // Fetch user name for activity log
        $nameField = in_array($userType, ['publisher']) ? 'society_name' : (in_array($userType, ['sponsor']) ? 'company_name' : 'full_name');
        $userRow = $this->query("SELECT {$nameField} as display_name FROM {$tableName} WHERE id = ?", [$userId]);
        $displayName = $userRow ? $userRow[0]->display_name : 'Unknown';

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
                AdminActivity::log(
                    $adminId,
                    $adminUser['name'],
                    'user_suspended',
                    $userType,
                    (int)$userId,
                    $displayName,
                    'Suspended ' . ucfirst($userType) . ' account: ' . $displayName . ' (Reason: ' . $reason . ')',
                    'ban'
                );
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

        // Fetch user name for activity log
        $nameField = in_array($userType, ['publisher']) ? 'society_name' : (in_array($userType, ['sponsor']) ? 'company_name' : 'full_name');
        $userRow = $this->query("SELECT {$nameField} as display_name FROM {$tableName} WHERE id = ?", [$userId]);
        $displayName = $userRow ? $userRow[0]->display_name : 'Unknown';
        $reactivatingAdmin = AuthService::getCurrentUser();

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
                AdminActivity::log(
                    $reactivatingAdmin['id'],
                    $reactivatingAdmin['name'],
                    'user_reactivated',
                    $userType,
                    (int)$userId,
                    $displayName,
                    'Reactivated ' . ucfirst($userType) . ' account: ' . $displayName,
                    'circle-check'
                );
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

            // Fetch publisher name for activity log
            $pubRow = $this->query("SELECT society_name FROM publishers WHERE id = ?", [$publisherId]);
            $pubName = $pubRow ? $pubRow[0]->society_name : 'Unknown';
            
            $result = $publisher->approve($publisherId, $admin['id']);
            
            if ($result) {
                AdminActivity::log(
                    $admin['id'],
                    $admin['name'],
                    'publisher_approved',
                    'publisher',
                    (int)$publisherId,
                    $pubName,
                    'Approved publisher account: ' . $pubName,
                    'check-circle'
                );
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

            // Fetch publisher name for activity log
            $pubRow = $this->query("SELECT society_name FROM publishers WHERE id = ?", [$publisherId]);
            $pubName = $pubRow ? $pubRow[0]->society_name : 'Unknown';
            
            $result = $publisher->reject($publisherId, $admin['id'], $rejectionReason);
            
            if ($result) {
                AdminActivity::log(
                    $admin['id'],
                    $admin['name'],
                    'publisher_rejected',
                    'publisher',
                    (int)$publisherId,
                    $pubName,
                    'Rejected publisher account: ' . $pubName . ' (Reason: ' . $rejectionReason . ')',
                    'times-circle'
                );
                echo json_encode(['success' => true, 'message' => 'Publisher rejected successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reject publisher']);
            }
        } catch (Exception $e) {
            error_log("Rejection error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * API endpoint to get all user registrations
     */
    public function getAllUsers() {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        try {
            // Get all registrations from all user types
            $universityUser = new UniversityUser();
            $publicUser = new PublicUser();
            $publisher = new Publisher();
            $sponsor = new Sponsor();
            
            // Get all registrations (no limit)
            $universityRegistrations = $universityUser->getRecentRegistrations(1000);
            $publicRegistrations = $publicUser->getRecentRegistrations(1000);
            $publisherRegistrations = $publisher->getRecentRegistrations(1000);
            $sponsorRegistrations = $sponsor->getRecentRegistrations(1000);
            
            // Merge all registrations
            $allUsers = array_merge(
                is_array($universityRegistrations) ? $universityRegistrations : [],
                is_array($publicRegistrations) ? $publicRegistrations : [],
                is_array($publisherRegistrations) ? $publisherRegistrations : [],
                is_array($sponsorRegistrations) ? $sponsorRegistrations : []
            );
            
            // Sort by created_at descending
            if (count($allUsers) > 0) {
                usort($allUsers, function($a, $b) {
                    $timeA = is_object($a) ? strtotime($a->created_at) : strtotime($a['created_at']);
                    $timeB = is_object($b) ? strtotime($b->created_at) : strtotime($b['created_at']);
                    return $timeB - $timeA;
                });
            }
            
            // Format users for response
            $formattedUsers = [];
            foreach ($allUsers as $user) {
                $status = 'Active';
                $statusClass = 'status-active';
                
                if (is_object($user)) {
                    $name = $user->name ?? 'N/A';
                    $email = $user->email ?? 'N/A';
                    $userType = ucfirst($user->user_type ?? 'User');
                    $createdAt = date('M j, Y', strtotime($user->created_at));
                    $userId = $user->id ?? 0;
                    $isSuspended = isset($user->is_suspended) ? $user->is_suspended : false;
                    
                    // Check status based on user type
                    if ($user->user_type === 'publisher' && isset($user->approval_status)) {
                        if ($user->approval_status === 'pending') {
                            $status = 'Pending Approval';
                            $statusClass = 'status-pending';
                        } elseif ($user->approval_status === 'rejected') {
                            $status = 'Rejected';
                            $statusClass = 'status-rejected';
                        } elseif ($user->approval_status === 'approved') {
                            $status = 'Active';
                            $statusClass = 'status-active';
                        }
                    } elseif ($user->user_type === 'sponsor' && isset($user->verification_status)) {
                        if ($user->verification_status === 'suspended') {
                            $status = 'Suspended';
                            $statusClass = 'status-suspended';
                        } elseif ($user->verification_status === 'active') {
                            $status = 'Active';
                            $statusClass = 'status-active';
                        }
                    }
                    
                    if ($isSuspended) {
                        $status = 'Suspended';
                        $statusClass = 'status-inactive';
                    }
                } else {
                    $name = $user['name'] ?? 'N/A';
                    $email = $user['email'] ?? 'N/A';
                    $userType = ucfirst($user['user_type'] ?? 'User');
                    $createdAt = date('M j, Y', strtotime($user['created_at']));
                    $userId = $user['id'] ?? 0;
                    $isSuspended = isset($user['is_suspended']) ? $user['is_suspended'] : false;
                    
                    if ($isSuspended) {
                        $status = 'Suspended';
                        $statusClass = 'status-inactive';
                    }
                }
                
                $formattedUsers[] = [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'userType' => $userType,
                    'createdAt' => $createdAt,
                    'status' => $status,
                    'statusClass' => $statusClass,
                    'isSuspended' => $isSuspended
                ];
            }
            
            echo json_encode([
                'success' => true,
                'users' => $formattedUsers,
                'total' => count($formattedUsers)
            ]);
        } catch (Exception $e) {
            error_log("Error fetching all users: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to fetch users']);
        }
    }

    /**
     * Return the logged-in admin's profile for the header
     */
    public function getUserProfile() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            $displayName = trim((string)($currentUser['name'] ?? 'Admin'));
            if ($displayName !== '' && $displayName === strtolower($displayName)) {
                $displayName = ucwords($displayName);
            }

            echo json_encode([
                'success'     => true,
                'username'    => $displayName,
                'displayName' => $displayName,
                'email'       => $currentUser['email'] ?? '',
                'type'        => 'admin',
            ]);
        } catch (Exception $e) {
            error_log('Admin getUserProfile error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
        exit;
    }

    /**
     * Return notifications for the admin header
     */
    public function getNotifications() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            // Pending publisher approvals become notifications
            $publisher = new Publisher();
            $pending   = $publisher->getAllPending();
            $notifications = [];

            if (is_array($pending)) {
                foreach (array_slice($pending, 0, 10) as $p) {
                    $notifications[] = [
                        'id'      => $p->id,
                        'message' => ($p->society_name ?? 'A publisher') . ' is awaiting approval',
                        'time'    => isset($p->created_at) ? $this->timeAgo($p->created_at) : '',
                        'unread'  => true,
                        'link'    => '/unipulse/public/admin/dashboard',
                    ];
                }
            }

            echo json_encode(['success' => true, 'notifications' => $notifications]);
        } catch (Exception $e) {
            error_log('Admin getNotifications error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
        exit;
    }
}
