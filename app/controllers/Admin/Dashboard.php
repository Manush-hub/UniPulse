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
     * API endpoint to get admin revenue report data.
     * Includes platform commission income, event boost income, and publisher-wise income totals.
     */
    public function getRevenueReport() {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $defaultFrom = date('Y-m-d', strtotime('-12 months'));
        $defaultTo = date('Y-m-d');

        $fromDate = $_GET['from_date'] ?? $defaultFrom;
        $toDate = $_GET['to_date'] ?? $defaultTo;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$toDate)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            return;
        }

        if (strtotime($fromDate) === false || strtotime($toDate) === false || strtotime($fromDate) > strtotime($toDate)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date range']);
            return;
        }

        try {
            $report = $this->getAdminRevenueReportData($fromDate, $toDate);
            echo json_encode([
                'success' => true,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'data' => $report
            ]);
        } catch (Throwable $e) {
            error_log('Error in AdminDashboard::getRevenueReport: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load revenue report']);
        }
    }

    /**
     * Download admin revenue report as PDF.
     */
    public function downloadRevenueReport() {
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            exit;
        }

        $defaultFrom = date('Y-m-d', strtotime('-12 months'));
        $defaultTo = date('Y-m-d');

        $fromDate = $_GET['from_date'] ?? $defaultFrom;
        $toDate = $_GET['to_date'] ?? $defaultTo;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            exit;
        }

        if (strtotime($fromDate) === false || strtotime($toDate) === false || strtotime($fromDate) > strtotime($toDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid date range']);
            exit;
        }

        try {
            $report = $this->getAdminRevenueReportData($fromDate, $toDate);
            $pdf = $this->generateAdminRevenueReportPDF($fromDate, $toDate, $report);

            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="admin-revenue-report-' . $fromDate . '-to-' . $toDate . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Throwable $e) {
            error_log('Error in AdminDashboard::downloadRevenueReport: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to generate revenue report']);
            exit;
        }
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
     * Build revenue report data for admin dashboard.
     */
    private function getAdminRevenueReportData($fromDate, $toDate) {
        $params = [
            'from_date' => $fromDate,
            'to_date' => $toDate
        ];

        $paymentsTableExists = $this->tableExists('payments');
        $publishersTableExists = $this->tableExists('publishers');
        $eventBoostsTableExists = $this->tableExists('event_boosts');

        $hasPaymentType = $paymentsTableExists && $this->columnExists('payments', 'payment_type');
        $hasCommissionAmount = $paymentsTableExists && $this->columnExists('payments', 'commission_amount');
        $hasAmount = $paymentsTableExists && $this->columnExists('payments', 'amount');
        $hasPublisherId = $paymentsTableExists && $this->columnExists('payments', 'publisher_id');

        $ticketTypeFilter = $hasPaymentType ? "AND p.payment_type = 'ticket'" : '';
        $commissionExpr = '0';
        if ($hasCommissionAmount) {
            $commissionExpr = 'SUM(COALESCE(p.commission_amount, 0))';
        } elseif ($hasAmount) {
            // Fallback for older schema: estimate platform commission as 5% of ticket payment amount.
            $commissionExpr = 'SUM(COALESCE(p.amount, 0) * 0.05)';
        }

        $ticketPaymentCount = 0;
        $commissionTotal = 0.0;

        if ($paymentsTableExists) {
            try {
                $countRows = $this->query(
                    "SELECT COUNT(*) AS cnt
                     FROM payments p
                     WHERE LOWER(COALESCE(p.status, '')) IN ('completed', 'paid', 'success')
                       $ticketTypeFilter
                       AND DATE(p.created_at) BETWEEN :from_date AND :to_date",
                    $params
                );
                $countSource = $countRows[0] ?? null;
                if (is_object($countSource)) {
                    $ticketPaymentCount = (int)($countSource->cnt ?? 0);
                } elseif (is_array($countSource)) {
                    $ticketPaymentCount = (int)($countSource['cnt'] ?? 0);
                }
            } catch (Throwable $e) {
                $ticketPaymentCount = 0;
            }

            try {
                $commissionRows = $this->query(
                    "SELECT COALESCE($commissionExpr, 0) AS total_commission
                     FROM payments p
                     WHERE LOWER(COALESCE(p.status, '')) IN ('completed', 'paid', 'success')
                       $ticketTypeFilter
                       AND DATE(p.created_at) BETWEEN :from_date AND :to_date",
                    $params
                );
                $commissionSource = $commissionRows[0] ?? null;
                if (is_object($commissionSource)) {
                    $commissionTotal = (float)($commissionSource->total_commission ?? 0);
                } elseif (is_array($commissionSource)) {
                    $commissionTotal = (float)($commissionSource['total_commission'] ?? 0);
                }
            } catch (Throwable $e) {
                $commissionTotal = 0.0;
            }
        }

        $boostRow = [['total_boosting' => 0]];
        if ($eventBoostsTableExists && $this->columnExists('event_boosts', 'amount_paid')) {
            try {
                $boostRow = $this->query(
                    "SELECT COALESCE(SUM(COALESCE(eb.amount_paid, 0)), 0) AS total_boosting
                     FROM event_boosts eb
                     WHERE LOWER(COALESCE(eb.payment_status, '')) = 'completed'
                       AND DATE(eb.created_at) BETWEEN :from_date AND :to_date",
                    $params
                );
            } catch (Throwable $e) {
                $boostRow = [['total_boosting' => 0]];
            }
        } elseif ($paymentsTableExists && $hasAmount && $hasPaymentType) {
            try {
                $boostRow = $this->query(
                    "SELECT COALESCE(SUM(COALESCE(p.amount, 0)), 0) AS total_boosting
                     FROM payments p
                     WHERE p.payment_type = 'boost'
                       AND LOWER(COALESCE(p.status, '')) IN ('completed', 'paid', 'success')
                       AND DATE(p.created_at) BETWEEN :from_date AND :to_date",
                    $params
                );
            } catch (Throwable $e) {
                $boostRow = [['total_boosting' => 0]];
            }
        }

        $publisherRows = [];
        if ($publishersTableExists && $hasPublisherId && $paymentsTableExists) {
            $commissionTypeFilter = $hasPaymentType ? "AND pay.payment_type = 'ticket'" : '';
            $commissionIncomeExpr = '0';
            if ($hasCommissionAmount) {
                $commissionIncomeExpr = 'SUM(COALESCE(pay.commission_amount, 0))';
            } elseif ($hasAmount) {
                $commissionIncomeExpr = 'SUM(COALESCE(pay.amount, 0) * 0.05)';
            }

            $boostSubquery = "SELECT pay.publisher_id, 0 AS boost_income FROM payments pay WHERE 1 = 0";
            if ($eventBoostsTableExists && $this->columnExists('event_boosts', 'publisher_id') && $this->columnExists('event_boosts', 'amount_paid')) {
                $boostSubquery =
                    "SELECT
                        eb.publisher_id,
                        SUM(COALESCE(eb.amount_paid, 0)) AS boost_income
                     FROM event_boosts eb
                     WHERE LOWER(COALESCE(eb.payment_status, '')) = 'completed'
                       AND DATE(eb.created_at) BETWEEN :from_date AND :to_date
                     GROUP BY eb.publisher_id";
            } elseif ($hasAmount && $hasPaymentType) {
                $boostSubquery =
                    "SELECT
                        pay.publisher_id,
                        SUM(COALESCE(pay.amount, 0)) AS boost_income
                     FROM payments pay
                     WHERE pay.payment_type = 'boost'
                       AND LOWER(COALESCE(pay.status, '')) IN ('completed', 'paid', 'success')
                       AND DATE(pay.created_at) BETWEEN :from_date AND :to_date
                     GROUP BY pay.publisher_id";
            }

            try {
                $publisherRows = $this->query(
                    "SELECT
                        p.id AS publisher_id,
                        p.society_name AS publisher_name,
                        COALESCE(comm.commission_income, 0) AS commission_income,
                        COALESCE(boost.boost_income, 0) AS boost_income,
                        COALESCE(comm.commission_income, 0) + COALESCE(boost.boost_income, 0) AS total_income
                    FROM publishers p
                    LEFT JOIN (
                        SELECT
                            pay.publisher_id,
                            COALESCE($commissionIncomeExpr, 0) AS commission_income
                        FROM payments pay
                        WHERE LOWER(COALESCE(pay.status, '')) IN ('completed', 'paid', 'success')
                          $commissionTypeFilter
                          AND DATE(pay.created_at) BETWEEN :from_date AND :to_date
                        GROUP BY pay.publisher_id
                    ) AS comm ON comm.publisher_id = p.id
                    LEFT JOIN (
                        $boostSubquery
                    ) AS boost ON boost.publisher_id = p.id
                    WHERE (COALESCE(comm.commission_income, 0) + COALESCE(boost.boost_income, 0)) > 0
                    ORDER BY total_income DESC, p.society_name ASC",
                    $params
                ) ?: [];
            } catch (Throwable $e) {
                $publisherRows = [];
            }
        }

        $boostSource = $boostRow[0] ?? null;

        if (is_object($boostSource)) {
            $boostingTotal = (float)($boostSource->total_boosting ?? 0);
        } elseif (is_array($boostSource)) {
            $boostingTotal = (float)($boostSource['total_boosting'] ?? 0);
        } else {
            $boostingTotal = 0.0;
        }

        // If there are no ticket payment rows, fallback to paid-event registrations.
        // This supports environments where revenue was tracked before payments table integration.
        $paidRegsTableExists = $this->tableExists('paid_event_registrations');
        $paidRegsHasPublisherId = $paidRegsTableExists && $this->columnExists('paid_event_registrations', 'publisher_id');

        if (($ticketPaymentCount === 0 || $commissionTotal <= 0 || empty($publisherRows)) && $paidRegsTableExists && $paidRegsHasPublisherId) {
            $paidDateExpr = $this->columnExists('paid_event_registrations', 'paid_at')
                ? 'COALESCE(pr.paid_at, pr.created_at)'
                : 'pr.created_at';
            $refundExpr = $this->columnExists('paid_event_registrations', 'refund_amount')
                ? 'COALESCE(pr.refund_amount, 0)'
                : '0';

            try {
                $fallbackCommissionRows = $this->query(
                    "SELECT
                        COALESCE(SUM(
                            CASE
                                WHEN LOWER(COALESCE(pr.payment_status, '')) IN ('paid', 'partially_refunded', 'refunded', 'completed')
                                    THEN GREATEST(COALESCE(pr.total_amount, 0) - $refundExpr, 0)
                                ELSE 0
                            END
                        ) * 0.05, 0) AS total_commission
                     FROM paid_event_registrations pr
                     WHERE pr.publisher_id IS NOT NULL
                       AND LOWER(COALESCE(pr.registration_status, '')) IN ('reserved', 'confirmed', 'checked_in')
                       AND DATE($paidDateExpr) BETWEEN :from_date AND :to_date",
                    $params
                );

                $source = $fallbackCommissionRows[0] ?? null;
                if (is_object($source)) {
                    $commissionTotal = (float)($source->total_commission ?? 0);
                } elseif (is_array($source)) {
                    $commissionTotal = (float)($source['total_commission'] ?? 0);
                } else {
                    $commissionTotal = 0.0;
                }
            } catch (Throwable $e) {
                $commissionTotal = 0.0;
            }

            try {
                $publisherNameExpr = $publishersTableExists
                    ? "COALESCE(p.society_name, CONCAT('Publisher #', comm.publisher_id))"
                    : "CONCAT('Publisher #', comm.publisher_id)";
                $publisherJoin = $publishersTableExists
                    ? 'LEFT JOIN publishers p ON p.id = comm.publisher_id'
                    : '';

                $publisherRows = $this->query(
                    "SELECT
                        comm.publisher_id AS publisher_id,
                        $publisherNameExpr AS publisher_name,
                        COALESCE(comm.commission_income, 0) AS commission_income,
                        0 AS boost_income,
                        COALESCE(comm.commission_income, 0) AS total_income
                    FROM (
                        SELECT
                            pr.publisher_id AS publisher_id,
                            COALESCE(SUM(
                                CASE
                                    WHEN LOWER(COALESCE(pr.payment_status, '')) IN ('paid', 'partially_refunded', 'refunded', 'completed')
                                        THEN GREATEST(COALESCE(pr.total_amount, 0) - $refundExpr, 0)
                                    ELSE 0
                                END
                            ) * 0.05, 0) AS commission_income
                        FROM paid_event_registrations pr
                        WHERE pr.publisher_id IS NOT NULL
                          AND LOWER(COALESCE(pr.registration_status, '')) IN ('reserved', 'confirmed', 'checked_in')
                          AND DATE($paidDateExpr) BETWEEN :from_date AND :to_date
                        GROUP BY pr.publisher_id
                    ) AS comm
                    $publisherJoin
                    WHERE COALESCE(comm.commission_income, 0) > 0
                    ORDER BY total_income DESC, publisher_name ASC",
                    $params
                ) ?: [];

                // Merge in real boost income if available.
                if (!empty($publisherRows)) {
                    $boostMap = [];
                    if ($eventBoostsTableExists && $this->columnExists('event_boosts', 'publisher_id') && $this->columnExists('event_boosts', 'amount_paid')) {
                        $boostRows = $this->query(
                            "SELECT
                                eb.publisher_id,
                                SUM(COALESCE(eb.amount_paid, 0)) AS boost_income
                             FROM event_boosts eb
                             WHERE LOWER(COALESCE(eb.payment_status, '')) = 'completed'
                               AND DATE(eb.created_at) BETWEEN :from_date AND :to_date
                             GROUP BY eb.publisher_id",
                            $params
                        ) ?: [];
                        foreach ($boostRows as $bRow) {
                            $pid = (int)($bRow->publisher_id ?? 0);
                            $boostMap[$pid] = (float)($bRow->boost_income ?? 0);
                        }
                    } elseif ($paymentsTableExists && $hasAmount && $hasPaymentType && $hasPublisherId) {
                        $boostRows = $this->query(
                            "SELECT
                                pay.publisher_id,
                                SUM(COALESCE(pay.amount, 0)) AS boost_income
                             FROM payments pay
                             WHERE pay.payment_type = 'boost'
                               AND LOWER(COALESCE(pay.status, '')) IN ('completed', 'paid', 'success')
                               AND DATE(pay.created_at) BETWEEN :from_date AND :to_date
                             GROUP BY pay.publisher_id",
                            $params
                        ) ?: [];
                        foreach ($boostRows as $bRow) {
                            $pid = (int)($bRow->publisher_id ?? 0);
                            $boostMap[$pid] = (float)($bRow->boost_income ?? 0);
                        }
                    }

                    foreach ($publisherRows as $idx => $row) {
                        $pid = (int)($row->publisher_id ?? 0);
                        $currentCommission = (float)($row->commission_income ?? 0);
                        $boostIncome = $boostMap[$pid] ?? 0.0;
                        $row->boost_income = $boostIncome;
                        $row->total_income = $currentCommission + $boostIncome;
                        $publisherRows[$idx] = $row;
                    }

                    usort($publisherRows, function($a, $b) {
                        $aTotal = (float)($a->total_income ?? 0);
                        $bTotal = (float)($b->total_income ?? 0);
                        return $bTotal <=> $aTotal;
                    });
                }
            } catch (Throwable $e) {
                // Keep previous result if fallback merge fails.
            }
        }

        $totalRevenue = $commissionTotal + $boostingTotal;

        $publisherIncome = [];
        foreach ($publisherRows as $row) {
            $publisherIncome[] = [
                'publisher_id' => (int)($row->publisher_id ?? 0),
                'publisher_name' => (string)($row->publisher_name ?? 'Unknown Publisher'),
                'commission_income' => round((float)($row->commission_income ?? 0), 2),
                'boost_income' => round((float)($row->boost_income ?? 0), 2),
                'total_income' => round((float)($row->total_income ?? 0), 2),
            ];
        }

        return [
            'summary' => [
                'commission_total' => round($commissionTotal, 2),
                'boosting_total' => round($boostingTotal, 2),
                'total_revenue' => round($totalRevenue, 2),
                'publishers_with_income' => count($publisherIncome),
            ],
            'publisher_income' => $publisherIncome,
        ];
    }

    private function tableExists($tableName) {
        try {
            $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
            if ($safeTableName === '') {
                return false;
            }

            $rows = $this->query("SHOW TABLES LIKE '$safeTableName'", []);
            return !empty($rows);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function columnExists($tableName, $columnName) {
        try {
            $safeTableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
            $safeColumnName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$columnName);
            if ($safeTableName === '' || $safeColumnName === '') {
                return false;
            }

            $rows = $this->query("SHOW COLUMNS FROM `$safeTableName` LIKE '$safeColumnName'", []);
            return !empty($rows);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Generate a decorated PDF for admin revenue data.
     */
    private function generateAdminRevenueReportPDF($fromDate, $toDate, $reportData) {
        $summary = is_array($reportData['summary'] ?? null) ? $reportData['summary'] : [];
        $publishers = is_array($reportData['publisher_income'] ?? null) ? $reportData['publisher_income'] : [];

        $periodLabel = date('M d, Y', strtotime($fromDate)) . ' to ' . date('M d, Y', strtotime($toDate));

        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 42;
        $contentRight = $pageWidth - $marginX;

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, [248, 250, 252]);
        $content .= $this->pdfLinearGradientRect(0, 694, $pageWidth, 98, [30, 58, 138], [249, 115, 22], 50);
        $content .= $this->pdfText($marginX, 754, 'UniPulse Admin Revenue Report', 'F2', 20, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 734, 'Platform Revenue Overview', 'F2', 12, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 718, $periodLabel . '  |  Generated ' . date('M d, Y'), 'F1', 9, [219, 234, 254]);

        $content .= $this->pdfRect($contentRight - 184, 718, 172, 44, null, [255, 237, 213], 0.8);
        $content .= $this->pdfText($contentRight - 174, 744, 'Report Type', 'F1', 8.5, [255, 237, 213]);
        $content .= $this->pdfText($contentRight - 174, 727, 'Admin Revenue', 'F2', 11.5, [255, 255, 255]);

        $commissionTotal = (float)($summary['commission_total'] ?? 0);
        $boostingTotal = (float)($summary['boosting_total'] ?? 0);
        $totalRevenue = (float)($summary['total_revenue'] ?? 0);
        $publisherCount = (int)($summary['publishers_with_income'] ?? count($publishers));

        $content .= $this->pdfRect($marginX, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 10, 673, 'Total Commission', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 10, 659, 'LKR ' . number_format($commissionTotal, 2), 'F2', 11.5, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 178, 652, 168, 34, [255, 255, 255], [226, 232, 240], 0.8);
        $content .= $this->pdfText($marginX + 188, 673, 'Boosting Income', 'F1', 8.5, [100, 116, 139]);
        $content .= $this->pdfText($marginX + 188, 659, 'LKR ' . number_format($boostingTotal, 2), 'F2', 11.5, [30, 41, 59]);

        $content .= $this->pdfRect($marginX + 356, 652, 214, 34, [236, 253, 245], [167, 243, 208], 0.8);
        $content .= $this->pdfText($marginX + 366, 673, 'Platform Revenue', 'F1', 8.5, [6, 95, 70]);
        $content .= $this->pdfText($marginX + 366, 659, 'LKR ' . number_format($totalRevenue, 2), 'F2', 12.8, [6, 95, 70]);

        $tableX = $marginX;
        $tableW = $contentRight - $tableX;
        $rowH = 18;
        $rowY = 620;

        $content .= $this->pdfText($tableX, $rowY + 16, 'Income by Publisher (' . $publisherCount . ')', 'F2', 12, [30, 58, 138]);
        $rowY -= 8;

        $content .= $this->pdfLinearGradientRect($tableX, $rowY, $tableW, $rowH, [30, 58, 138], [249, 115, 22], 30);
        $content .= $this->pdfText($tableX + 10, $rowY + 6, 'PUBLISHER', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 318, $rowY + 6, 'COMMISSION', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 418, $rowY + 6, 'BOOSTING', 'F2', 8.2, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 500, $rowY + 6, 'TOTAL', 'F2', 8.2, [255, 255, 255]);
        $rowY -= $rowH;

        if (empty($publishers)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, 'No publisher income found for this period.', 'F1', 8.4, [100, 116, 139]);
            $rowY -= $rowH;
        } else {
            $maxRows = min(count($publishers), 18);
            for ($i = 0; $i < $maxRows; $i++) {
                $row = $publishers[$i];
                $bg = ($i % 2 === 0) ? [255, 255, 255] : [248, 250, 252];

                $publisherName = $this->truncatePDFText((string)($row['publisher_name'] ?? 'Unknown Publisher'), 36);
                $commissionIncome = (float)($row['commission_income'] ?? 0);
                $boostIncome = (float)($row['boost_income'] ?? 0);
                $rowTotal = (float)($row['total_income'] ?? 0);

                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, $publisherName, 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 318, $rowY + 6, number_format($commissionIncome, 2), 'F2', 8.0, [217, 119, 6]);
                $content .= $this->pdfText($tableX + 418, $rowY + 6, number_format($boostIncome, 2), 'F2', 8.0, [30, 41, 59]);
                $content .= $this->pdfText($tableX + 500, $rowY + 6, number_format($rowTotal, 2), 'F2', 8.0, [6, 95, 70]);
                $rowY -= $rowH;
            }

            if (count($publishers) > $maxRows) {
                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 251, 235], [253, 230, 138], 0.6);
                $content .= $this->pdfText($tableX + 10, $rowY + 6, 'Showing first ' . $maxRows . ' publishers in this PDF export.', 'F1', 7.8, [120, 53, 15]);
                $rowY -= $rowH;
            }
        }

        $rowY -= 12;
        $content .= $this->pdfText($tableX, $rowY + 16, 'Summary', 'F2', 11, [30, 58, 138]);
        $rowY -= 8;

        $summaryRows = [
            ['label' => 'Total Commission Revenue', 'value' => 'LKR ' . number_format($commissionTotal, 2)],
            ['label' => 'Total Boosting Revenue', 'value' => 'LKR ' . number_format($boostingTotal, 2)],
            ['label' => 'Total Platform Revenue', 'value' => 'LKR ' . number_format($totalRevenue, 2)],
            ['label' => 'Publishers With Revenue', 'value' => number_format($publisherCount)],
        ];

        foreach ($summaryRows as $index => $row) {
            $bg = ($index % 2 === 0) ? [255, 255, 255] : [248, 250, 252];
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, [226, 232, 240], 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 6, $row['label'], 'F2', 8.4, [30, 41, 59]);
            $content .= $this->pdfText($tableX + 430, $rowY + 6, $row['value'], 'F2', 8.4, [30, 41, 59]);
            $rowY -= $rowH;
        }

        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 26, [30, 58, 138], [249, 115, 22], 30);
        $content .= $this->pdfText($marginX, 8, 'UniPulse  |  Admin Revenue Report  |  ' . $periodLabel, 'F1', 8.2, [219, 234, 254]);

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $index => $objectBody) {
            $offsets[$index] = strlen($pdf);
            $pdf .= $index . " 0 obj\n" . $objectBody . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($objects as $index => $_) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$index]) . "\n";
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText($text) {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\\(', '\\)'],
            $text
        );
    }

    private function pdfRect($x, $y, $width, $height, $fillColor = null, $strokeColor = null, $lineWidth = 1)
    {
        $cmd = '';
        if ($fillColor !== null) {
            $cmd .= $this->pdfColor($fillColor, false);
        }
        if ($strokeColor !== null) {
            $cmd .= $this->pdfColor($strokeColor, true);
            $cmd .= number_format($lineWidth, 2, '.', '') . " w\n";
        }

        $op = 'n';
        if ($fillColor !== null && $strokeColor !== null) {
            $op = 'B';
        } elseif ($fillColor !== null) {
            $op = 'f';
        } elseif ($strokeColor !== null) {
            $op = 'S';
        }

        return $cmd
            . number_format($x, 2, '.', '') . ' '
            . number_format($y, 2, '.', '') . ' '
            . number_format($width, 2, '.', '') . ' '
            . number_format($height, 2, '.', '') . " re $op\n";
    }

    private function pdfLinearGradientRect($x, $y, $width, $height, $startColor, $endColor, $steps = 32)
    {
        $steps = max(1, (int)$steps);
        $segmentW = $width / $steps;
        $cmd = '';

        for ($i = 0; $i < $steps; $i++) {
            $ratio = $steps === 1 ? 0 : ($i / ($steps - 1));
            $color = [
                (int)round($startColor[0] + (($endColor[0] - $startColor[0]) * $ratio)),
                (int)round($startColor[1] + (($endColor[1] - $startColor[1]) * $ratio)),
                (int)round($startColor[2] + (($endColor[2] - $startColor[2]) * $ratio)),
            ];

            $cmd .= $this->pdfRect($x + ($segmentW * $i), $y, $segmentW + 0.2, $height, $color);
        }

        return $cmd;
    }

    private function pdfText($x, $y, $text, $font = 'F1', $fontSize = 10, $color = [0, 0, 0])
    {
        return "BT\n"
            . $this->pdfColor($color, false)
            . "/{$font} " . number_format($fontSize, 2, '.', '') . " Tf\n"
            . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . " Td\n"
            . '(' . $this->escapePdfText($text) . ") Tj\n"
            . "ET\n";
    }

    private function pdfColor($rgb, $isStroke)
    {
        $r = number_format(((int)($rgb[0] ?? 0)) / 255, 3, '.', '');
        $g = number_format(((int)($rgb[1] ?? 0)) / 255, 3, '.', '');
        $b = number_format(((int)($rgb[2] ?? 0)) / 255, 3, '.', '');

        return $r . ' ' . $g . ' ' . $b . ($isStroke ? " RG\n" : " rg\n");
    }

    private function truncatePDFText($text, $maxChars)
    {
        $safeText = (string)$text;
        if (strlen($safeText) <= $maxChars) {
            return $safeText;
        }

        return rtrim(substr($safeText, 0, max(1, $maxChars - 1))) . '...';
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
            $supportMessageModel = new SupportMessage();
            $notifications = [];

            if (is_array($pending)) {
                foreach (array_slice($pending, 0, 10) as $p) {
                    $notifications[] = [
                        'id'      => 'publisher:' . (int)$p->id,
                        'type'    => 'publisher_pending',
                        'title'   => 'Publisher Approval Pending',
                        'message' => ($p->society_name ?? 'A publisher') . ' is awaiting approval',
                        'time'    => isset($p->created_at) ? $this->timeAgo($p->created_at) : '',
                        'unread'  => true,
                        'link'    => '/unipulse/public/admin/dashboard',
                        'raw_time' => isset($p->created_at) ? strtotime($p->created_at) : time(),
                    ];
                }
            }

            $supportNotifications = $supportMessageModel->getUnreadNotificationsForAdmin(10);
            if (is_array($supportNotifications)) {
                foreach ($supportNotifications as $s) {
                    $notifications[] = [
                        'id'       => 'support:' . (int)$s->id,
                        'type'     => 'support_message',
                        'title'    => 'New Contact Us Reach',
                        'message'  => (string)($s->subject ?? 'New support message') . ' - from ' . (string)($s->full_name ?? 'Unknown user'),
                        'time'     => isset($s->created_at) ? $this->timeAgo($s->created_at) : '',
                        'unread'   => true,
                        'link'     => '/unipulse/public/admin/messages',
                        'raw_time' => isset($s->created_at) ? strtotime($s->created_at) : time(),
                    ];
                }
            }

            usort($notifications, function ($a, $b) {
                return ((int)($b['raw_time'] ?? 0)) - ((int)($a['raw_time'] ?? 0));
            });

            $notifications = array_map(function ($item) {
                unset($item['raw_time']);
                return $item;
            }, array_slice($notifications, 0, 20));

            echo json_encode(['success' => true, 'notifications' => $notifications]);
        } catch (Exception $e) {
            error_log('Admin getNotifications error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
        exit;
    }

    /**
     * Mark one notification as read (supports contact-us notifications).
     */
    public function markNotificationRead() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'Invalid request method']);
                exit;
            }

            $payload = json_decode(file_get_contents('php://input'), true);
            $notificationId = trim((string)($payload['notificationId'] ?? ''));

            if ($notificationId === '') {
                echo json_encode(['success' => false, 'error' => 'Notification ID is required']);
                exit;
            }

            if (strpos($notificationId, 'support:') === 0) {
                $supportId = (int)substr($notificationId, strlen('support:'));
                $supportMessageModel = new SupportMessage();
                $ok = $supportMessageModel->markNotificationAsRead($supportId);
                echo json_encode(['success' => (bool)$ok]);
                exit;
            }

            // Non-support notifications are not persisted as read yet.
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log('Admin markNotificationRead error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
        exit;
    }

    /**
     * Mark all contact-us notifications as read.
     */
    public function markAllNotificationsRead() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || $currentUser['type'] !== 'admin') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'Invalid request method']);
                exit;
            }

            $supportMessageModel = new SupportMessage();
            $ok = $supportMessageModel->markAllNotificationsAsRead();
            echo json_encode(['success' => (bool)$ok]);
        } catch (Exception $e) {
            error_log('Admin markAllNotificationsRead error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
        exit;
    }
}
