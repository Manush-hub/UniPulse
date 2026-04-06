<?php

class SponsorDashboard extends Controller
{
    use Database;

    public function index($a = '', $b = '', $c = '')
    {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Pass user data to view
        $data = [
            'user' => $currentUser,
            'page_title' => 'Dashboard'
        ];

        $this->view('Sponsor/dashboard', $data);
    }

    /**
     * Display sponsor monthly evaluation page.
     */
    public function monthlyEvaluation($a = '', $b = '', $c = '')
    {
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }

        $data = [
            'user' => $currentUser,
            'page_title' => 'Monthly Evaluation'
        ];

        $this->view('Sponsor/monthly-evaluation', $data);
    }

    /**
     * API endpoint to get user profile data
     */
    public function getUserProfile()
    {
        // Clean output buffer
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]);
                exit;
            }

            $displayName = $_SESSION['user_name'] ?? ($currentUser['name'] ?? ($currentUser['company_name'] ?? 'Sponsor'));
            $displayName = trim((string) $displayName);
            if ($displayName !== '' && $displayName === strtolower($displayName)) {
                $displayName = ucwords($displayName);
            }

            echo json_encode([
                'success' => true,
                'displayName' => $displayName,
                'companyName' => $currentUser['company_name'] ?? 'Sponsor',
                'email' => $currentUser['email'] ?? '',
                'type' => 'sponsor'
            ]);
        } catch (Exception $e) {
            error_log("Error in getUserProfile: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load profile data'
            ]);
        }

        exit;
    }

    /**
     * API endpoint to get notifications
     */
    public function getNotifications()
    {
        // Clean output buffer
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized'
                ]);
                exit;
            }

            $notificationModel = new Notification();
            $rows = $notificationModel->getUserNotifications((int)$currentUser['id'], 'sponsor', 30);

            $notifications = [];
            foreach ($rows ?: [] as $row) {
                $createdAt = $row->created_at ?? date('Y-m-d H:i:s');
                $notifications[] = [
                    'id' => (int)($row->id ?? 0),
                    'title' => (string)($row->title ?? 'Notification'),
                    'message' => (string)($row->message ?? ''),
                    'time' => $this->formatRelativeTime($createdAt),
                    'created_at' => $createdAt,
                    'unread' => !((bool)($row->is_read ?? 0)),
                ];
            }

            echo json_encode([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            error_log("Error in getNotifications: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load notifications',
                'notifications' => []
            ]);
        }

        exit;
    }

    /**
     * API endpoint to mark a single sponsor notification as read
     */
    public function markNotificationRead()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $notificationId = (int)($payload['notificationId'] ?? 0);

        if ($notificationId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid notification id']);
            exit;
        }

        $notificationModel = new Notification();
        $result = $notificationModel->markAsRead($notificationId, (int)$currentUser['id'], 'sponsor');

        echo json_encode([
            'success' => (bool)$result,
            'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
        ]);
        exit;
    }

    /**
     * API endpoint to mark all sponsor notifications as read
     */
    public function markAllNotificationsRead()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $notificationModel = new Notification();
        $result = $notificationModel->markAllAsRead((int)$currentUser['id'], 'sponsor');

        echo json_encode([
            'success' => (bool)$result,
            'message' => $result ? 'All notifications marked as read' : 'Failed to update notifications'
        ]);
        exit;
    }

    /**
     * API endpoint to get all upcoming events visible to sponsor users.
     */
    public function getUpcomingEvents()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'sponsor') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        try {
            $eventModel = new Event();
            $upcoming = $eventModel->getAllEvents([
                'status' => 'upcoming',
                'limit' => 300,
                'offset' => 0
            ], $currentUser);

            $upcomingEvents = [];
            foreach ($upcoming ?: [] as $event) {
                $eventDateTime = trim((string)$event->event_date . ' ' . ((string)$event->event_time !== '' ? (string)$event->event_time : '23:59:59'));
                $eventTimestamp = strtotime($eventDateTime);
                if ($eventTimestamp === false || $eventTimestamp < time()) {
                    continue;
                }

                $upcomingEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => isset($event->description) ? substr((string)$event->description, 0, 100) . '...' : '',
                    'date' => $event->event_date,
                    'time' => $event->event_time,
                    'location' => $event->location ?: ($event->venue_name ?: ($event->city ?? '')),
                    'category' => $event->category,
                    'university' => $event->university_name,
                    'image_url' => $event->image_url,
                    'organizer' => $event->organizer_name ?? ($event->organizer ?? ''),
                    'max_participants' => $event->max_participants,
                    'current_participants' => $event->current_participants ?? 0
                ];
            }

            usort($upcomingEvents, function ($a, $b) {
                $aDateTime = trim((string)$a['date'] . ' ' . ((string)$a['time'] !== '' ? (string)$a['time'] : '23:59:59'));
                $bDateTime = trim((string)$b['date'] . ' ' . ((string)$b['time'] !== '' ? (string)$b['time'] : '23:59:59'));

                $aTimestamp = strtotime($aDateTime) ?: 0;
                $bTimestamp = strtotime($bDateTime) ?: 0;

                return $aTimestamp <=> $bTimestamp;
            });

            echo json_encode([
                'success' => true,
                'events' => $upcomingEvents,
                'count' => count($upcomingEvents)
            ]);
        } catch (Exception $e) {
            error_log('Error in SponsorDashboard::getUpcomingEvents: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load upcoming events'
            ]);
        }

        exit;
    }

    /**
     * API endpoint to get sponsor statistics
     */
    public function getStats()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            // Get sponsorship statistics
            $sql = "SELECT 
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) as active_sponsorships,
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_requests,
                        COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_investment
                    FROM event_sponsorships
                    WHERE sponsor_id = ? AND sponsor_type = 'sponsor'";

            $stats = $this->query($sql, [$currentUser['id']]);

            if (!empty($stats)) {
                $stats = (array) $stats[0];
                echo json_encode([
                    'success' => true,
                    'stats' => [
                        'active_sponsorships' => (int)$stats['active_sponsorships'],
                        'pending_requests' => (int)$stats['pending_requests'],
                        'total_investment' => (float)$stats['total_investment']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'stats' => [
                        'active_sponsorships' => 0,
                        'pending_requests' => 0,
                        'total_investment' => 0
                    ]
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in getStats: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load statistics'
            ]);
        }

        exit;
    }

    /**
     * API endpoint to get active sponsorships (completed sponsorships for upcoming/ongoing events)
     */
    public function getActiveSponsorships()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser || $currentUser['type'] !== 'sponsor') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            // Get completed sponsorships for upcoming or ongoing events
            $sql = "SELECT 
                        es.id,
                        es.amount,
                        es.created_at as sponsored_at,
                        e.id as event_id,
                        e.title as event_title,
                        e.event_date,
                        e.venue_name,
                        e.city,
                        e.university_name,
                        e.created_by as organizer_id,
                        esp.package_name,
                        esp.package_type,
                        p.society_name as organizer_name,
                        p.email as organizer_email
                    FROM event_sponsorships es
                    INNER JOIN events e ON es.event_id = e.id
                    INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                    LEFT JOIN publishers p ON e.created_by = p.id
                    WHERE es.sponsor_id = ? 
                        AND es.sponsor_type = 'sponsor'
                        AND es.status = 'completed'
                        AND e.event_date >= CURDATE()
                        AND e.is_deleted = 0
                    ORDER BY e.event_date ASC";

            error_log("Sponsor Dashboard - Fetching active sponsorships for sponsor ID: " . $currentUser['id']);
            $sponsorships = $this->query($sql, [$currentUser['id']]);
            error_log("Sponsor Dashboard - Found " . (is_array($sponsorships) ? count($sponsorships) : 0) . " sponsorships");

            if (!$sponsorships) {
                $sponsorships = [];
            } else {
                $sponsorships = array_map(function ($item) {
                    $sponsorship = (array) $item;

                    // Determine if event is upcoming or ongoing
                    $eventDate = strtotime($sponsorship['event_date']);
                    $today = strtotime('today');

                    if ($today < $eventDate) {
                        $sponsorship['event_status'] = 'upcoming';
                    } else {
                        // Event is today - mark as ongoing
                        $sponsorship['event_status'] = 'ongoing';
                    }

                    return $sponsorship;
                }, $sponsorships);
            }

            echo json_encode([
                'success' => true,
                'sponsorships' => $sponsorships
            ]);
        } catch (Exception $e) {
            error_log("Error in getActiveSponsorships: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load active sponsorships'
            ]);
        }

        exit;
    }

    /**
     * API endpoint to get sponsor monthly evaluation data.
     */
    public function getMonthlyEvaluationData()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || ($currentUser['type'] ?? '') !== 'sponsor') {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }

            $month = $_GET['month'] ?? date('Y-m');
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                echo json_encode(['success' => false, 'error' => 'Invalid month format']);
                exit;
            }

            $reportData = $this->getSponsorMonthlyReportData((int)$currentUser['id'], $month);
            $summary = $reportData['summary'] ?? [];
            $records = $reportData['records'] ?? [];

            $rows = array_map(function ($record) {
                $statusRaw = strtolower((string)($record->status ?? 'pending'));
                $statusLabel = ucfirst($statusRaw);
                if ($statusRaw === 'approved' || $statusRaw === 'completed') {
                    $statusLabel = ucfirst($statusRaw);
                }

                return [
                    'id' => (int)($record->id ?? 0),
                    'event_title' => $record->event_title ?? 'Event Sponsorship',
                    'package_name' => $record->package_name ?? 'Package',
                    'status' => $statusRaw,
                    'status_label' => $statusLabel,
                    'amount' => (float)($record->amount ?? 0),
                    'created_at' => $record->created_at ?? null,
                    'payment_date' => $record->payment_date ?? null,
                    'event_date' => $record->event_date ?? null,
                    'venue_name' => $record->venue_name ?? '',
                    'city' => $record->city ?? '',
                    'university_name' => $record->university_name ?? '',
                    'organizer_name' => $record->organizer_name ?? ''
                ];
            }, $records);

            echo json_encode([
                'success' => true,
                'month' => $month,
                'summary' => [
                    'total_requests' => (int)($summary['total_requests'] ?? 0),
                    'pending_requests' => (int)($summary['pending_requests'] ?? 0),
                    'approved_requests' => (int)($summary['approved_requests'] ?? 0),
                    'completed_requests' => (int)($summary['completed_requests'] ?? 0),
                    'rejected_requests' => (int)($summary['rejected_requests'] ?? 0),
                    'committed_amount' => (float)($summary['committed_amount'] ?? 0),
                    'pending_amount' => (float)($summary['pending_amount'] ?? 0),
                    'approval_rate' => ((int)($summary['total_requests'] ?? 0) > 0)
                        ? round(((($summary['approved_requests'] ?? 0) + ($summary['completed_requests'] ?? 0)) / (int)$summary['total_requests']) * 100, 1)
                        : 0,
                ],
                'records' => $rows,
            ]);
        } catch (Exception $e) {
            error_log('Error in SponsorDashboard::getMonthlyEvaluationData: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load monthly evaluation data']);
        }

        exit;
    }

    /**
     * Download a monthly sponsor evaluation report as PDF.
     */
    public function downloadMonthlyReport()
    {
        if (!AuthService::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || ($currentUser['type'] ?? '') !== 'sponsor') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid month format']);
            exit;
        }

        try {
            $sponsorId = (int)($currentUser['id'] ?? 0);
            $companyName = (string)($currentUser['company_name'] ?? ($currentUser['name'] ?? 'Sponsor'));
            $reportData = $this->getSponsorMonthlyReportData($sponsorId, $month);
            $pdf = $this->generateSponsorMonthlyReportPDF($companyName, $month, $reportData);

            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="sponsor-monthly-report-' . $month . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            exit;
        } catch (Exception $e) {
            error_log('Error in SponsorDashboard::downloadMonthlyReport: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Failed to generate monthly report']);
            exit;
        }
    }

    /**
     * Fetch sponsor monthly report data from sponsorship records.
     */
    private function getSponsorMonthlyReportData($sponsorId, $month)
    {
        $summarySql = "SELECT
                            COUNT(*) as total_requests,
                            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_requests,
                            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                            COALESCE(SUM(CASE WHEN status IN ('approved', 'completed') THEN amount ELSE 0 END), 0) as committed_amount,
                            COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending_amount
                        FROM event_sponsorships
                        WHERE sponsor_id = :sponsor_id
                          AND sponsor_type = 'sponsor'
                          AND DATE_FORMAT(created_at, '%Y-%m') = :month";

        $summary = $this->query($summarySql, [
            'sponsor_id' => $sponsorId,
            'month' => $month,
        ]);

        $recordsSql = "SELECT
                            es.id,
                            es.amount,
                            es.status,
                            es.created_at,
                            es.payment_date,
                            e.id as event_id,
                            e.title as event_title,
                            e.event_date,
                            e.venue_name,
                            e.city,
                            e.university_name,
                            esp.package_name,
                            esp.package_type,
                            p.society_name as organizer_name
                        FROM event_sponsorships es
                        INNER JOIN events e ON es.event_id = e.id
                        INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                        LEFT JOIN publishers p ON e.created_by = p.id
                        WHERE es.sponsor_id = :sponsor_id
                          AND es.sponsor_type = 'sponsor'
                          AND DATE_FORMAT(es.created_at, '%Y-%m') = :month
                          AND e.deleted_at IS NULL
                        ORDER BY es.created_at DESC";

        $records = $this->query($recordsSql, [
            'sponsor_id' => $sponsorId,
            'month' => $month,
        ]);

        return [
            'summary' => !empty($summary) ? (array)$summary[0] : [
                'total_requests' => 0,
                'pending_requests' => 0,
                'approved_requests' => 0,
                'completed_requests' => 0,
                'rejected_requests' => 0,
                'committed_amount' => 0,
                'pending_amount' => 0,
            ],
            'records' => $records ?: [],
        ];
    }

    /**
     * Build the sponsor monthly report PDF.
     */
    private function generateSponsorMonthlyReportPDF($companyName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));
        $summary = is_array($data['summary'] ?? null) ? $data['summary'] : [];
        $records = is_array($data['records'] ?? null) ? $data['records'] : [];
        $headerLogo = $this->loadPDFHeaderLogoImage();

        $summaryTotals = [
            'total_requests' => (int)($summary['total_requests'] ?? 0),
            'pending_requests' => (int)($summary['pending_requests'] ?? 0),
            'approved_requests' => (int)($summary['approved_requests'] ?? 0),
            'completed_requests' => (int)($summary['completed_requests'] ?? 0),
            'rejected_requests' => (int)($summary['rejected_requests'] ?? 0),
            'committed_amount' => (float)($summary['committed_amount'] ?? 0),
            'pending_amount' => (float)($summary['pending_amount'] ?? 0),
        ];

        $approvalRate = 0;
        if ($summaryTotals['total_requests'] > 0) {
            $approvalRate = round((($summaryTotals['approved_requests'] + $summaryTotals['completed_requests']) / $summaryTotals['total_requests']) * 100, 1);
        }

        $content = $this->buildSponsorPDFContent(
            $monthName,
            $companyName,
            $records,
            $summaryTotals,
            $approvalRate,
            !empty($headerLogo)
        );

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $resourceParts = ['/Font << /F1 5 0 R /F2 6 0 R >>'];

        if (!empty($headerLogo)) {
            $resourceParts[] = '/XObject << /Im1 7 0 R >>';
        }

        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << " . implode(' ', $resourceParts) . " >> >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        if (!empty($headerLogo)) {
            $objects[7] = "<< /Type /XObject /Subtype /Image"
                . " /Width " . (int)$headerLogo['width']
                . " /Height " . (int)$headerLogo['height']
                . " /ColorSpace /DeviceRGB /BitsPerComponent 8"
                . " /Filter /DCTDecode /Length " . strlen($headerLogo['data']) . " >>\nstream\n"
                . $headerLogo['data']
                . "\nendstream";
        }

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

    private function formatRelativeTime($timestamp)
    {
        $time = strtotime((string)$timestamp);
        if (!$time) {
            return 'Just now';
        }

        $diff = time() - $time;
        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            $mins = (int)floor($diff / 60);
            return $mins . ' minute' . ($mins === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 86400) {
            $hours = (int)floor($diff / 3600);
            return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 604800) {
            $days = (int)floor($diff / 86400);
            return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
        }

        return date('M j, Y', $time);
    }

    /**
     * Build sponsor report PDF content stream.
     */
    private function buildSponsorPDFContent($monthName, $companyName, $records, $summary, $approvalRate, $hasHeaderLogo = false)
    {
        $content = '';
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 48;
        $contentRight = $pageWidth - $marginX;
        $contentWidth = $contentRight - $marginX;
        $brandPrimary = [30, 58, 138];
        $brandSecondary = [249, 115, 22];
        $pageBackground = [248, 250, 252];
        $textDark = [30, 41, 59];
        $textMuted = [100, 116, 139];
        $lineColor = [226, 232, 240];
        $headerBottom = 688;
        $headerHeight = 104;
        $rowH = 18;

        $records = is_array($records) ? $records : [];

        $content .= $this->pdfRect(0, 0, $pageWidth, $pageHeight, $pageBackground);
        $content .= $this->pdfLinearGradientRect(0, $headerBottom, $pageWidth, $headerHeight, $brandPrimary, $brandSecondary, 56);
        $content .= $this->pdfLine(0, $headerBottom, $pageWidth, $headerBottom, [254, 215, 170], 1.2);

        if ($hasHeaderLogo) {
            $content .= $this->pdfRect($marginX, 724, 100, 52, [255, 255, 255], [253, 186, 116], 0.8);
            $content .= $this->pdfImage('Im1', $marginX + 6, 729, 88, 42);
        } else {
            $content .= $this->pdfText($marginX, 748, 'UniPulse', 'F2', 23, [255, 255, 255]);
        }

        $content .= $this->pdfText($marginX, 710, 'Monthly Sponsorship Evaluation', 'F2', 16, [255, 255, 255]);
        $content .= $this->pdfText($marginX, 692, $monthName . '  |  Generated ' . date('M d, Y'), 'F1', 10, [219, 234, 254]);

        $accountPanelX = $contentRight - 180;
        $content .= $this->pdfRect($accountPanelX, 712, 170, 54, null, [255, 237, 213], 0.9);
        $content .= $this->pdfText($accountPanelX + 12, 746, 'Sponsor Account', 'F1', 9, [255, 237, 213]);
        $content .= $this->pdfText($accountPanelX + 12, 724, $this->truncatePDFText($companyName, 24), 'F2', 14, [255, 255, 255]);

        $cardY = 602;
        $cardHeight = 70;
        $cardGap = 12;
        $cardWidth = ($contentWidth - ($cardGap * 3)) / 4;
        $cardX = $marginX;

        $cards = [
            ['label' => 'Requests Submitted', 'value' => (string)$summary['total_requests'], 'accent' => [30, 58, 138]],
            ['label' => 'Approved / Completed', 'value' => (string)(($summary['approved_requests'] ?? 0) + ($summary['completed_requests'] ?? 0)), 'accent' => [22, 163, 74]],
            ['label' => 'Pending Review', 'value' => (string)$summary['pending_requests'], 'accent' => [245, 158, 11]],
            ['label' => 'Committed Budget', 'value' => 'LKR ' . number_format((float)$summary['committed_amount'], 2), 'accent' => [249, 115, 22]],
        ];

        foreach ($cards as $card) {
            $content .= $this->pdfRect($cardX, $cardY, $cardWidth, $cardHeight, [255, 255, 255], $lineColor, 0.9);
            $content .= $this->pdfRect($cardX, $cardY + $cardHeight - 5, $cardWidth, 5, $card['accent']);
            $content .= $this->pdfText($cardX + 10, $cardY + 47, $card['label'], 'F1', 8.5, $textMuted);
            $content .= $this->pdfText($cardX + 10, $cardY + 24, $this->truncatePDFText($card['value'], 20), 'F2', 13, $textDark);
            $cardX += ($cardWidth + $cardGap);
        }

        $content .= $this->pdfText($marginX, 569, 'Evaluation Snapshot', 'F2', 13, $brandPrimary);
        $content .= $this->pdfLine($marginX, 564, $contentRight, 564, $lineColor, 1);
        $content .= $this->pdfText($marginX, 548, 'Approval rate: ' . number_format($approvalRate, 1) . '%  |  Records captured this month: ' . count($records), 'F1', 8.8, $textMuted);

        $tableX = $marginX;
        $tableW = $contentWidth;
        $headerY = 522;
        $content .= $this->pdfLinearGradientRect($tableX, $headerY, $tableW, $rowH, $brandPrimary, $brandSecondary, 22);
        $content .= $this->pdfText($tableX + 10, $headerY + 5, '#', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 34, $headerY + 5, 'EVENT', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 184, $headerY + 5, 'PACKAGE', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 294, $headerY + 5, 'DATE', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 388, $headerY + 5, 'AMOUNT', 'F2', 8.5, [255, 255, 255]);
        $content .= $this->pdfText($tableX + 472, $headerY + 5, 'STATUS', 'F2', 8.5, [255, 255, 255]);

        $rowY = $headerY - $rowH;
        $maxRows = 6;
        $displayRows = array_slice($records, 0, $maxRows);

        if (empty($displayRows)) {
            $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 255, 255], $lineColor, 0.6);
            $content .= $this->pdfText($tableX + 10, $rowY + 5, 'No sponsorship activity recorded for this month.', 'F1', 8.5, $textMuted);
            $rowY -= $rowH;
        } else {
            foreach ($displayRows as $index => $record) {
                $bg = (($index % 2) === 0) ? [255, 255, 255] : [248, 250, 252];
                $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, $bg, $lineColor, 0.6);

                $statusRaw = strtolower((string)($record->status ?? 'pending'));
                $statusLabel = ucfirst($statusRaw);
                $statusColor = [245, 158, 11];
                if ($statusRaw === 'approved' || $statusRaw === 'completed') {
                    $statusColor = [22, 163, 74];
                    $statusLabel = ucfirst($statusRaw);
                } elseif ($statusRaw === 'rejected') {
                    $statusColor = [220, 38, 38];
                }

                $eventName = $record->event_title ?? 'Event Sponsorship';
                $packageName = $record->package_name ?? 'Package';
                $date = $this->formatPDFDate($record->created_at ?? null);
                $amount = 'LKR ' . number_format((float)($record->amount ?? 0), 2);

                $content .= $this->pdfText($tableX + 10, $rowY + 5, (string)($index + 1), 'F1', 8.0, $textDark);
                $content .= $this->pdfText($tableX + 34, $rowY + 5, $this->truncatePDFText($eventName, 22), 'F2', 8.0, $textDark);
                $content .= $this->pdfText($tableX + 184, $rowY + 5, $this->truncatePDFText($packageName, 18), 'F1', 8.0, $textMuted);
                $content .= $this->pdfText($tableX + 294, $rowY + 5, $date, 'F1', 8.0, $textMuted);
                $content .= $this->pdfText($tableX + 388, $rowY + 5, $amount, 'F2', 8.0, $textDark);
                $content .= $this->pdfText($tableX + 472, $rowY + 5, $statusLabel, 'F2', 7.8, $statusColor);

                $rowY -= $rowH;
            }
        }

        $content .= $this->pdfRect($tableX, $rowY, $tableW, $rowH, [255, 247, 237]);
        $content .= $this->pdfText($tableX + 10, $rowY + 5, 'Total Committed Budget', 'F2', 9.0, $brandPrimary);
        $content .= $this->pdfText($tableX + $tableW - 130, $rowY + 5, 'LKR ' . number_format((float)$summary['committed_amount'], 2), 'F2', 9.0, $brandPrimary);

        $footerY = 26;
        $content .= $this->pdfLinearGradientRect(0, 0, $pageWidth, 28, $brandPrimary, $brandSecondary, 36);
        $content .= $this->pdfText($marginX, 9, 'UniPulse  |  Sponsor Monthly Evaluation Report  |  ' . $monthName, 'F1', 8.5, [219, 234, 254]);

        return $content;
    }

    private function loadPDFHeaderLogoImage()
    {
        $logoPath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';
        if (!is_file($logoPath)) {
            return null;
        }

        if (!function_exists('imagecreatefrompng') || !function_exists('imagejpeg')) {
            return null;
        }

        $sourceImage = @imagecreatefrompng($logoPath);
        if (!$sourceImage) {
            return null;
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($sourceImage);
            return null;
        }

        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $sourceImage, 0, 0, 0, 0, $width, $height);

        ob_start();
        imagejpeg($canvas, null, 92);
        $jpegBinary = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($sourceImage);

        if ($jpegBinary === false || $jpegBinary === '') {
            return null;
        }

        return [
            'width' => $width,
            'height' => $height,
            'data' => $jpegBinary,
        ];
    }

    private function pdfImage($imageRefName, $x, $y, $width, $height)
    {
        return "q\n"
            . number_format($width, 2, '.', '') . " 0 0 " . number_format($height, 2, '.', '') . " "
            . number_format($x, 2, '.', '') . " " . number_format($y, 2, '.', '') . " cm\n"
            . "/" . $imageRefName . " Do\nQ\n";
    }

    private function pdfRect($x, $y, $width, $height, $fillColor = null, $strokeColor = null, $lineWidth = 1)
    {
        $cmd = '';

        if (is_array($fillColor)) {
            $cmd .= $this->pdfColor($fillColor, false);
        }

        if (is_array($strokeColor)) {
            $cmd .= $this->pdfColor($strokeColor, true);
            $cmd .= number_format($lineWidth, 2, '.', '') . " w\n";
        }

        $x = number_format($x, 2, '.', '');
        $y = number_format($y, 2, '.', '');
        $width = number_format($width, 2, '.', '');
        $height = number_format($height, 2, '.', '');

        if (is_array($fillColor) && is_array($strokeColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re B\n";
        } elseif (is_array($fillColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re f\n";
        } elseif (is_array($strokeColor)) {
            $cmd .= $x . ' ' . $y . ' ' . $width . ' ' . $height . " re S\n";
        }

        return $cmd;
    }

    private function pdfLinearGradientRect($x, $y, $width, $height, $startColor, $endColor, $steps = 32)
    {
        $steps = max(2, (int)$steps);
        $cmd = '';

        for ($i = 0; $i < $steps; $i++) {
            $t0 = $i / $steps;
            $t1 = ($i + 1) / $steps;
            $segmentX = $x + ($width * $t0);
            $segmentW = $width * ($t1 - $t0);

            $color = [
                (int)round(($startColor[0] ?? 0) + (($endColor[0] ?? 0) - ($startColor[0] ?? 0)) * $t0),
                (int)round(($startColor[1] ?? 0) + (($endColor[1] ?? 0) - ($startColor[1] ?? 0)) * $t0),
                (int)round(($startColor[2] ?? 0) + (($endColor[2] ?? 0) - ($startColor[2] ?? 0)) * $t0),
            ];

            $cmd .= $this->pdfRect($segmentX, $y, $segmentW + 0.2, $height, $color);
        }

        return $cmd;
    }

    private function pdfLine($x1, $y1, $x2, $y2, $strokeColor = [0, 0, 0], $lineWidth = 1)
    {
        return $this->pdfColor($strokeColor, true)
            . number_format($lineWidth, 2, '.', '') . " w\n"
            . number_format($x1, 2, '.', '') . ' ' . number_format($y1, 2, '.', '') . " m\n"
            . number_format($x2, 2, '.', '') . ' ' . number_format($y2, 2, '.', '') . " l\nS\n";
    }

    private function pdfText($x, $y, $text, $font = 'F1', $fontSize = 10, $color = [0, 0, 0])
    {
        return "BT\n"
            . $this->pdfColor($color, false)
            . '/' . $font . ' ' . number_format($fontSize, 2, '.', '') . " Tf\n"
            . "1 0 0 1 " . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . " Tm\n"
            . '(' . $this->escapePDF($text) . ") Tj\n"
            . "ET\n";
    }

    private function pdfColor($rgb, $isStroke)
    {
        $r = number_format(max(0, min(255, (float)($rgb[0] ?? 0))) / 255, 3, '.', '');
        $g = number_format(max(0, min(255, (float)($rgb[1] ?? 0))) / 255, 3, '.', '');
        $b = number_format(max(0, min(255, (float)($rgb[2] ?? 0))) / 255, 3, '.', '');
        return $r . ' ' . $g . ' ' . $b . ($isStroke ? " RG\n" : " rg\n");
    }

    private function truncatePDFText($text, $maxChars)
    {
        $text = trim((string)$text);
        if (strlen($text) <= $maxChars) {
            return $text;
        }

        return substr($text, 0, max(0, $maxChars - 3)) . '...';
    }

    private function formatPDFDate($value)
    {
        if (!$value) {
            return '-';
        }

        $timestamp = strtotime((string)$value);
        if ($timestamp === false) {
            return '-';
        }

        return date('M d, Y', $timestamp);
    }

    private function escapePDF($text)
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        return $text;
    }
}
