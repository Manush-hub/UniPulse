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

        // Count public and university users
        $publicCountResult = $this->query("SELECT COUNT(*) as cnt FROM public_users");
        $universityCountResult = $this->query("SELECT COUNT(*) as cnt FROM university_users");
        $data['stats']['total_public_users'] = $publicCountResult ? (int)$publicCountResult[0]->cnt : 0;
        $data['stats']['total_university_users'] = $universityCountResult ? (int)$universityCountResult[0]->cnt : 0;
        $data['stats']['total_users'] =
            $data['stats']['total_public_users'] +
            $data['stats']['total_university_users'] +
            $data['stats']['total_publishers'] +
            $data['stats']['total_sponsors'];

        // Event statistics for overview card
        $activeEventsResult = $this->query(
            "SELECT COUNT(*) as cnt
             FROM events
             WHERE is_deleted = 0
               AND event_date >= CURDATE()"
        );
        $totalEventsResult = $this->query(
            "SELECT COUNT(*) as cnt
             FROM events
             WHERE is_deleted = 0"
        );

        $data['stats']['active_events'] = $activeEventsResult ? (int)$activeEventsResult[0]->cnt : 0;
        $data['stats']['total_events'] = $totalEventsResult ? (int)$totalEventsResult[0]->cnt : 0;
        
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

        $pendingAppealsMap = $this->getLatestPendingAppealsByUser();
        foreach ($recentRegistrations as &$registration) {
            if (is_object($registration)) {
                $rowUserType = strtolower((string)($registration->user_type ?? ''));
                $rowUserId = (int)($registration->id ?? 0);
                $appealKey = $rowUserType . ':' . $rowUserId;

                if (isset($pendingAppealsMap[$appealKey])) {
                    $appeal = $pendingAppealsMap[$appealKey];
                    $registration->has_pending_appeal = true;
                    $registration->pending_appeal_id = (int)$appeal['id'];
                    $registration->pending_appeal_message = $appeal['appeal_message'];
                    $registration->pending_appeal_submitted_at = $appeal['created_at'];
                } else {
                    $registration->has_pending_appeal = false;
                }
            } else {
                $rowUserType = strtolower((string)($registration['user_type'] ?? ''));
                $rowUserId = (int)($registration['id'] ?? 0);
                $appealKey = $rowUserType . ':' . $rowUserId;

                if (isset($pendingAppealsMap[$appealKey])) {
                    $appeal = $pendingAppealsMap[$appealKey];
                    $registration['has_pending_appeal'] = true;
                    $registration['pending_appeal_id'] = (int)$appeal['id'];
                    $registration['pending_appeal_message'] = $appeal['appeal_message'];
                    $registration['pending_appeal_submitted_at'] = $appeal['created_at'];
                } else {
                    $registration['has_pending_appeal'] = false;
                }
            }
        }
        unset($registration);
        
        $data['recent_registrations'] = $recentRegistrations;
        $data['pending_appeals_count'] = count($pendingAppealsMap);
        
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
        
        // Get per-user-type totals
        $publicUsersResult = $this->query("SELECT COUNT(*) as count FROM public_users");
        $universityUsersResult = $this->query("SELECT COUNT(*) as count FROM university_users");
        $publishersResult = $this->query("SELECT COUNT(*) as count FROM publishers");
        $sponsorsResult = $this->query("SELECT COUNT(*) as count FROM sponsors");

        $publicUsers = $publicUsersResult ? (int)$publicUsersResult[0]->count : 0;
        $universityUsers = $universityUsersResult ? (int)$universityUsersResult[0]->count : 0;
        $publishers = $publishersResult ? (int)$publishersResult[0]->count : 0;
        $sponsors = $sponsorsResult ? (int)$sponsorsResult[0]->count : 0;
        $totalUsers = $publicUsers + $universityUsers + $publishers + $sponsors;

        // Get users registered this week (across all user types)
        $weekAgo = date('Y-m-d H:i:s', strtotime('-1 week'));
        $newPublicThisWeekResult = $this->query("SELECT COUNT(*) as count FROM public_users WHERE created_at >= ?", [$weekAgo]);
        $newUniversityThisWeekResult = $this->query("SELECT COUNT(*) as count FROM university_users WHERE created_at >= ?", [$weekAgo]);
        $newPublishersThisWeekResult = $this->query("SELECT COUNT(*) as count FROM publishers WHERE created_at >= ?", [$weekAgo]);
        $newSponsorsThisWeekResult = $this->query("SELECT COUNT(*) as count FROM sponsors WHERE created_at >= ?", [$weekAgo]);

        $newUsersThisWeek =
            ($newPublicThisWeekResult ? (int)$newPublicThisWeekResult[0]->count : 0) +
            ($newUniversityThisWeekResult ? (int)$newUniversityThisWeekResult[0]->count : 0) +
            ($newPublishersThisWeekResult ? (int)$newPublishersThisWeekResult[0]->count : 0) +
            ($newSponsorsThisWeekResult ? (int)$newSponsorsThisWeekResult[0]->count : 0);

        // Event totals
        $activeEventsResult = $this->query(
            "SELECT COUNT(*) as count
             FROM events
             WHERE is_deleted = 0
               AND event_date >= CURDATE()"
        );
        $activeEvents = $activeEventsResult ? (int)$activeEventsResult[0]->count : 0;

        $totalEventsResult = $this->query(
            "SELECT COUNT(*) as count
             FROM events
             WHERE is_deleted = 0"
        );
        $totalEvents = $totalEventsResult ? (int)$totalEventsResult[0]->count : 0;
        
        $stats = [
            'totalUsers' => intval($totalUsers),
            'publicUsers' => intval($publicUsers),
            'universityUsers' => intval($universityUsers),
            'publisherUsers' => intval($publishers),
            'sponsorUsers' => intval($sponsors),
            'activeEvents' => intval($activeEvents),
            'totalEvents' => intval($totalEvents),
            'newUsersThisWeek' => intval($newUsersThisWeek),
            'eventsThisWeek' => 0
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

            $pendingAppealsMap = $this->getLatestPendingAppealsByUser();
            
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
                    $suspensionReason = $user->suspension_reason ?? null;
                    $rawUserType = strtolower((string)($user->user_type ?? ''));
                    $appealKey = $rawUserType . ':' . (int)$userId;
                    $pendingAppeal = $pendingAppealsMap[$appealKey] ?? null;
                    
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
                    $suspensionReason = $user['suspension_reason'] ?? null;
                    $rawUserType = strtolower((string)($user['user_type'] ?? ''));
                    $appealKey = $rawUserType . ':' . (int)$userId;
                    $pendingAppeal = $pendingAppealsMap[$appealKey] ?? null;
                    
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
                    'isSuspended' => $isSuspended,
                    'suspensionReason' => $suspensionReason,
                    'hasPendingAppeal' => !empty($pendingAppeal),
                    'pendingAppealId' => !empty($pendingAppeal) ? (int)$pendingAppeal['id'] : null,
                    'pendingAppealMessage' => !empty($pendingAppeal) ? $pendingAppeal['appeal_message'] : null,
                    'pendingAppealSubmittedAt' => !empty($pendingAppeal) ? date('M j, Y g:i A', strtotime($pendingAppeal['created_at'])) : null
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
     * Approve/reject a pending suspension appeal.
     */
    public function reviewAppeal() {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $appealId = isset($payload['appeal_id']) ? (int)$payload['appeal_id'] : 0;
        $decision = strtolower(trim((string)($payload['decision'] ?? '')));
        $adminResponse = trim((string)($payload['admin_response'] ?? ''));

        if ($appealId <= 0 || !in_array($decision, ['approved', 'rejected'], true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        if ($decision === 'rejected' && $adminResponse === '') {
            echo json_encode(['success' => false, 'message' => 'Please provide a response when rejecting an appeal']);
            return;
        }

        try {
            $appealRows = $this->query(
                "SELECT id, user_id, user_type, appeal_message, status
                 FROM suspension_appeals
                 WHERE id = ? LIMIT 1",
                [$appealId]
            );

            if (!$appealRows) {
                echo json_encode(['success' => false, 'message' => 'Appeal not found']);
                return;
            }

            $appeal = $appealRows[0];
            if ($appeal->status !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'This appeal has already been reviewed']);
                return;
            }

            $targetTable = $this->getUserTable($appeal->user_type);
            if (!$targetTable) {
                echo json_encode(['success' => false, 'message' => 'Invalid appeal user type']);
                return;
            }

            $admin = AuthService::getCurrentUser();
            $nameField = in_array($appeal->user_type, ['publisher']) ? 'society_name' : (in_array($appeal->user_type, ['sponsor']) ? 'company_name' : 'full_name');
            $nameRow = $this->query("SELECT {$nameField} as display_name FROM {$targetTable} WHERE id = ? LIMIT 1", [$appeal->user_id]);
            $displayName = $nameRow ? $nameRow[0]->display_name : 'Unknown';

            $conn = $this->connect();
            $conn->beginTransaction();

            if ($decision === 'approved') {
                $unsuspendStmt = $conn->prepare(
                    "UPDATE {$targetTable}
                     SET is_suspended = 0,
                         suspension_reason = NULL,
                         suspended_at = NULL,
                         suspended_by = NULL
                     WHERE id = :user_id"
                );
                $unsuspendStmt->execute(['user_id' => (int)$appeal->user_id]);
            }

            $reviewStmt = $conn->prepare(
                "UPDATE suspension_appeals
                 SET status = :status,
                     admin_response = :admin_response,
                     reviewed_by = :reviewed_by,
                     reviewed_at = NOW()
                 WHERE id = :appeal_id"
            );
            $reviewStmt->execute([
                'status' => $decision,
                'admin_response' => $adminResponse !== '' ? $adminResponse : null,
                'reviewed_by' => (int)$admin['id'],
                'appeal_id' => $appealId,
            ]);

            $conn->commit();

            if ($decision === 'approved') {
                AdminActivity::log(
                    $admin['id'],
                    $admin['name'],
                    'appeal_approved',
                    $appeal->user_type,
                    (int)$appeal->user_id,
                    $displayName,
                    'Approved suspension appeal for ' . ucfirst($appeal->user_type) . ': ' . $displayName,
                    'check-circle'
                );
            } else {
                AdminActivity::log(
                    $admin['id'],
                    $admin['name'],
                    'appeal_rejected',
                    $appeal->user_type,
                    (int)$appeal->user_id,
                    $displayName,
                    'Rejected suspension appeal for ' . ucfirst($appeal->user_type) . ': ' . $displayName,
                    'times-circle'
                );
            }

            echo json_encode([
                'success' => true,
                'message' => $decision === 'approved' ? 'Appeal approved and account reactivated' : 'Appeal rejected',
                'user_id' => (int)$appeal->user_id,
                'user_type' => strtolower((string)$appeal->user_type),
            ]);
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Review appeal error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Returns map like "user_type:user_id" => latest pending appeal row.
     */
    private function getLatestPendingAppealsByUser() {
        $rows = $this->query(
            "SELECT sa.id, sa.user_id, sa.user_type, sa.appeal_message, sa.created_at
             FROM suspension_appeals sa
             INNER JOIN (
                 SELECT user_id, user_type, MAX(created_at) AS latest_created_at
                 FROM suspension_appeals
                 WHERE status = 'pending'
                 GROUP BY user_id, user_type
             ) latest
             ON sa.user_id = latest.user_id
             AND sa.user_type = latest.user_type
             AND sa.created_at = latest.latest_created_at
             WHERE sa.status = 'pending'"
        );

        $map = [];
        if ($rows) {
            foreach ($rows as $row) {
                $key = strtolower((string)$row->user_type) . ':' . (int)$row->user_id;
                $map[$key] = [
                    'id' => (int)$row->id,
                    'user_id' => (int)$row->user_id,
                    'user_type' => strtolower((string)$row->user_type),
                    'appeal_message' => (string)$row->appeal_message,
                    'created_at' => (string)$row->created_at,
                ];
            }
        }

        return $map;
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
