<?php

class UserDashboard extends Controller
{

    public function index($a = '', $b = '', $c = '')
    {
        // Require authentication - allow both public and university users
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/dashboard', $data);
    }

    /**
     * Display monthly evolution page
     */
    public function monthlyEvolution($a = '', $b = '', $c = '')
    {
        // Require authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || !in_array($currentUser['type'], ['public', 'university'])) {
            header('Location: /unipulse/public/signin');
            exit();
        }

        // Load profile photo into session for header display
        $this->loadUserProfilePhotoToSession();

        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];

        $this->view('User/monthly-evolution', $data);
    }

    /**
     * API endpoint to get current user data
     */
    public function getUserData()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $currentUser = AuthService::getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $currentUser['name'] ?? 'User',
                'email' => $currentUser['email'] ?? '',
                'type' => $currentUser['type'] ?? 'user',
                'university' => $currentUser['university'] ?? ''
            ]
        ]);
    }

    /**
     * API endpoint to get user's registered upcoming events
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Load EventRegistration model
            $eventRegistration = new EventRegistration();

            if (!$eventRegistration) {
                echo json_encode(['success' => false, 'error' => 'Failed to load EventRegistration model']);
                return;
            }

            // Get user's registered events (upcoming only)
            $registeredEvents = $eventRegistration->getUserRegisteredEvents($userId, $userType, 'registered');

            // Filter for upcoming events only (event_date >= today)
            $upcomingEvents = [];
            if ($registeredEvents) {
                foreach ($registeredEvents as $event) {
                    $eventDate = strtotime($event->event_date);
                    if ($eventDate >= strtotime('today')) {
                        $upcomingEvents[] = [
                            'id' => $event->id,
                            'title' => $event->title,
                            'description' => isset($event->description) ? substr($event->description, 0, 100) . '...' : '',
                            'date' => $event->event_date,
                            'time' => $event->event_time,
                            'location' => $event->location,
                            'category' => $event->category,
                            'university' => $event->university_name,
                            'image_url' => $event->image_url,
                            'organizer' => $event->organizer,
                            'max_participants' => $event->max_participants,
                            'current_participants' => $event->current_participants ?? 0
                        ];
                    }
                }
            }

            // Sort by event date (earliest first)
            usort($upcomingEvents, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            echo json_encode([
                'success' => true,
                'events' => $upcomingEvents,
                'count' => count($upcomingEvents)
            ]);
        } catch (Exception $e) {
            error_log("Error in getUpcomingEvents: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG ? $e->getTraceAsString() : ''
            ]);
        }
    }

    /**
     * API endpoint to get featured events
     */
    public function getFeaturedEvents()
    {
        header('Content-Type: application/json');

        try {
            $event = new Event();

            // Get featured events (you can define your own criteria)
            $filters = [
                'status' => 'upcoming'
            ];

            $featuredEvents = $event->getAllEvents($filters);

            // Format events
            $formatted = [];
            if ($featuredEvents) {
                foreach (array_slice($featuredEvents, 0, 6) as $event) {
                    $formatted[] = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => substr($event->description, 0, 100) . '...',
                        'date' => $event->event_date,
                        'category' => $event->category,
                        'university' => $event->university_name,
                        'image_url' => $event->image_url
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'events' => $formatted
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API endpoint to get recent activity
     */
    public function getRecentActivity()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            if (!$currentUser) {
                echo json_encode(['success' => false, 'error' => 'User data not found']);
                return;
            }

            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Load Activity model
            $activity = new Activity();

            // Get recent activities (from last 7 days)
            $recentActivities = $activity->getRecentActivities($userId, $userType, 20);

            // Format activities for frontend
            $formatted = [];
            if ($recentActivities) {
                foreach ($recentActivities as $act) {
                    $formatted[] = $activity->formatActivityForDisplay($act);
                }
            }

            echo json_encode([
                'success' => true,
                'activities' => $formatted,
                'count' => count($formatted)
            ]);
        } catch (Exception $e) {
            error_log("Error in getRecentActivity: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG ? $e->getTraceAsString() : ''
            ]);
        }
    }

    /**
     * API endpoint to get monthly evolution data
     */
    public function getMonthlyEvolution()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            $userId = $currentUser['id'];
            $userType = $currentUser['type'];

            // Get month parameter (default to current month)
            $month = $_GET['month'] ?? date('Y-m');

            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                echo json_encode(['success' => false, 'error' => 'Invalid month format']);
                return;
            }

            // Load models
            $volunteerReg = new VolunteerRegistration();
            $donation = new Donation();
            $eventReg = new EventRegistration();

            // Get data for the month
            $volunteering = $volunteerReg->getUserMonthlyVolunteering($userId, $userType, $month);
            $donations = $donation->getUserMonthlyDonations($userId, $userType, $month);
            $participation = $eventReg->getUserMonthlyParticipation($userId, $userType, $month);

            // Calculate totals
            $donationTotal = $donation->getUserMonthlyDonationTotal($userId, $userType, $month);
            $eventSpending = $eventReg->getUserMonthlyEventSpending($userId, $userType, $month);

            echo json_encode([
                'success' => true,
                'month' => $month,
                'data' => [
                    'volunteering' => $volunteering ?: [],
                    'donations' => $donations ?: [],
                    'participation' => $participation ?: [],
                    'totals' => [
                        'donations' => floatval($donationTotal),
                        'eventSpending' => floatval($eventSpending),
                        'volunteerCount' => count($volunteering ?: []),
                        'participationCount' => count($participation ?: [])
                    ]
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in getMonthlyEvolution: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate and download monthly evolution CSV report
     */
    public function downloadMonthlyReport()
    {
        if (!AuthService::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit();
        }

        try {
            $currentUser = AuthService::getCurrentUser();
            $userId = $currentUser['id'];
            $userType = $currentUser['type'];
            $userName = $currentUser['name'];

            // Get month parameter
            $month = $_GET['month'] ?? date('Y-m');

            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Invalid month format']);
                exit();
            }

            // Load models
            $volunteerReg = new VolunteerRegistration();
            $donation = new Donation();
            $eventReg = new EventRegistration();

            // Get data
            $volunteering = $volunteerReg->getUserMonthlyVolunteering($userId, $userType, $month) ?? [];
            $donations = $donation->getUserMonthlyDonations($userId, $userType, $month) ?? [];
            $participation = $eventReg->getUserMonthlyParticipation($userId, $userType, $month) ?? [];
            $donationTotal = floatval($donation->getUserMonthlyDonationTotal($userId, $userType, $month) ?? 0);
            $eventSpending = floatval($eventReg->getUserMonthlyEventSpending($userId, $userType, $month) ?? 0);

            // Generate PDF content
            $pdf = $this->generateReportPDF($userName, $month, [
                'volunteering' => $volunteering,
                'donations' => $donations,
                'participation' => $participation,
                'donationTotal' => $donationTotal,
                'eventSpending' => $eventSpending
            ]);

            // Return PDF as base64 JSON for client-side download
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'pdf' => base64_encode($pdf),
                'filename' => 'monthly-report-' . $month . '.pdf'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            error_log("Error in downloadMonthlyReport: " . $e->getMessage() . " Stack: " . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Generate PDF for the monthly report using native PHP
     */
    private function generateReportPDF($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));

        // Ensure all data arrays are actual arrays, not false
        $volunteering = is_array($data['volunteering']) ? $data['volunteering'] : [];
        $donations = is_array($data['donations']) ? $data['donations'] : [];
        $participation = is_array($data['participation']) ? $data['participation'] : [];
        $donationTotal = floatval($data['donationTotal'] ?? 0);
        $eventSpending = floatval($data['eventSpending'] ?? 0);

        // Start PDF content
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

        // Build content
        $content = $this->buildPDFContent($monthName, $userName, $volunteering, $donations, $participation, $donationTotal, $eventSpending);

        // Content object
        $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>\nendobj\n";
        $pdf .= "4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

        // Font objects
        $pdf .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $pdf .= "6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

        // Xref and trailer
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 7\n";
        $pdf .= "0000000000 65535 f \n";

        // Calculate offsets (simplified - just use placeholders)\n        $pdf .= "0000000009 00000 n \n";
        $pdf .= "0000000058 00000 n \n";
        $pdf .= "0000000115 00000 n \n";
        $pdf .= "0000000214 00000 n \n";
        $pdf .= str_pad($xrefOffset - 100, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        $pdf .= str_pad($xrefOffset - 50, 10, '0', STR_PAD_LEFT) . " 00000 n \n";

        $pdf .= "trailer\n<< /Size 7 /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    /**
     * Build PDF content stream
     */
    private function buildPDFContent($monthName, $userName, $volunteering, $donations, $participation, $donationTotal, $eventSpending)
    {
        $content = "BT\n/F2 16 Tf\n50 750 Td\n(Monthly Activity Report - " . $this->escapePDF($monthName) . ") Tj\n";
        $content .= "ET\n";
        $content .= "BT\n/F1 12 Tf\n50 720 Td\n(User: " . $this->escapePDF($userName) . ") Tj\n";
        $content .= "ET\n";
        $content .= "BT\n/F1 10 Tf\n50 700 Td\n(Generated: " . date('Y-m-d H:i:s') . ") Tj\n";
        $content .= "ET\n";

        $yPos = 670;

        // Summary Section
        $content .= "BT\n/F2 14 Tf\n50 " . $yPos . " Td\n(SUMMARY) Tj\nET\n";
        $yPos -= 25;

        $summaryData = [
            "Volunteer Sessions: " . count($volunteering),
            "Total Donations: LKR " . number_format($donationTotal, 2) . " (" . count($donations) . " donations)",
            "Events Participated: " . count($participation),
            "Total Event Spending: LKR " . number_format($eventSpending, 2)
        ];

        foreach ($summaryData as $item) {
            $content .= "BT\n/F1 10 Tf\n50 " . $yPos . " Td\n(" . $this->escapePDF($item) . ") Tj\nET\n";
            $yPos -= 15;
        }

        $yPos -= 20;

        // Volunteering Section
        if (!empty($volunteering)) {
            $content .= "BT\n/F2 12 Tf\n50 " . $yPos . " Td\n(VOLUNTEERING ACTIVITIES) Tj\nET\n";
            $yPos -= 18;

            $count = 1;
            foreach ($volunteering as $vol) {
                if ($yPos < 80) break;

                $title = $this->escapePDF($vol->title ?? 'N/A');
                $date = date('M d, Y', strtotime($vol->event_date ?? ''));
                $position = $this->escapePDF($vol->volunteer_position ?? 'General');
                $status = ucfirst($vol->volunteer_status ?? 'pending');

                $content .= "BT\n/F2 9 Tf\n50 " . $yPos . " Td\n(" . $count . ". " . $title . ") Tj\nET\n";
                $yPos -= 12;
                $content .= "BT\n/F1 8 Tf\n60 " . $yPos . " Td\n(Date: " . $date . " | Position: " . $position . " | Status: " . $status . ") Tj\nET\n";
                $yPos -= 15;
                $count++;
            }
        } else {
            $content .= "BT\n/F1 9 Tf\n60 " . $yPos . " Td\n(No volunteering activities this month) Tj\nET\n";
            $yPos -= 15;
        }

        $yPos -= 15;

        // Donations Section
        if (!empty($donations)) {
            if ($yPos < 100) {
                $content .= "BT\n/F1 9 Tf\n50 " . $yPos . " Td\n(Continued on next page...) Tj\nET\n";
            } else {
                $content .= "BT\n/F2 12 Tf\n50 " . $yPos . " Td\n(DONATIONS) Tj\nET\n";
                $yPos -= 18;

                $count = 1;
                foreach ($donations as $donation) {
                    if ($yPos < 80) break;

                    $title = $this->escapePDF($donation->title ?? 'Event Donation');
                    $amount = number_format($donation->amount ?? 0, 2);
                    $date = date('M d, Y', strtotime($donation->created_at ?? ''));
                    $status = ucfirst($donation->status ?? 'pending');

                    $content .= "BT\n/F2 9 Tf\n50 " . $yPos . " Td\n(" . $count . ". " . $title . ") Tj\nET\n";
                    $yPos -= 12;
                    $content .= "BT\n/F1 8 Tf\n60 " . $yPos . " Td\n(Amount: LKR " . $amount . " | Date: " . $date . " | Status: " . $status . ") Tj\nET\n";
                    $yPos -= 15;
                    $count++;
                }

                // Donations Total
                $yPos -= 5;
                $content .= "BT\n/F2 10 Tf\n50 " . $yPos . " Td\n(Total Donations: LKR " . number_format($donationTotal, 2) . ") Tj\nET\n";
                $yPos -= 20;
            }
        } else {
            $content .= "BT\n/F1 9 Tf\n60 " . $yPos . " Td\n(No donations this month) Tj\nET\n";
            $yPos -= 20;
        }

        // Participation Section
        if (!empty($participation)) {
            if ($yPos < 100) {
                $content .= "BT\n/F1 9 Tf\n50 " . $yPos . " Td\n(Continued on next page...) Tj\nET\n";
            } else {
                $content .= "BT\n/F2 12 Tf\n50 " . $yPos . " Td\n(EVENT PARTICIPATION) Tj\nET\n";
                $yPos -= 18;

                $count = 1;
                foreach ($participation as $event) {
                    if ($yPos < 80) break;

                    $title = $this->escapePDF($event->title ?? 'Event');
                    $date = date('M d, Y', strtotime($event->event_date ?? ''));
                    $ticketType = $this->escapePDF($event->ticket_type ?? 'Standard');
                    $amountPaid = number_format($event->amount_paid ?? 0, 2);

                    $content .= "BT\n/F2 9 Tf\n50 " . $yPos . " Td\n(" . $count . ". " . $title . ") Tj\nET\n";
                    $yPos -= 12;
                    $content .= "BT\n/F1 8 Tf\n60 " . $yPos . " Td\n(Date: " . $date . " | Ticket: " . $ticketType . " | Paid: LKR " . $amountPaid . ") Tj\nET\n";
                    $yPos -= 15;
                    $count++;
                }

                // Event Spending Total
                $yPos -= 5;
                $content .= "BT\n/F2 10 Tf\n50 " . $yPos . " Td\n(Total Event Spending: LKR " . number_format($eventSpending, 2) . ") Tj\nET\n";
            }
        } else {
            $content .= "BT\n/F1 9 Tf\n60 " . $yPos . " Td\n(No event participation this month) Tj\nET\n";
        }

        return $content;
    }

    /**
     * Escape special characters for PDF content
     */
    private function escapePDF($text)
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        return $text;
    }

    /**
     * Generate CSV for the monthly report
     */
    private function generateReportCSV($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));
        $csv = "";

        // Ensure all data arrays are actual arrays, not false
        $volunteering = is_array($data['volunteering']) ? $data['volunteering'] : [];
        $donations = is_array($data['donations']) ? $data['donations'] : [];
        $participation = is_array($data['participation']) ? $data['participation'] : [];
        $donationTotal = floatval($data['donationTotal'] ?? 0);
        $eventSpending = floatval($data['eventSpending'] ?? 0);

        // Header
        $csv .= "Monthly Activity Report - " . $monthName . "\n";
        $csv .= "User: " . $this->escapeCSV($userName) . "\n";
        $csv .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // Summary
        $csv .= "SUMMARY\n";
        $csv .= "Volunteer Sessions," . count($volunteering) . "\n";
        $csv .= "Donations," . count($donations) . "\n";
        $csv .= "Events Participated," . count($participation) . "\n";
        $csv .= "Total Donations (LKR)," . number_format($donationTotal, 2) . "\n";
        $csv .= "Event Spending (LKR)," . number_format($eventSpending, 2) . "\n\n";

        // Volunteering Section
        $csv .= "VOLUNTEERING ACTIVITIES\n";
        $csv .= "Event,Title,Position,Status,Date\n";
        if (!empty($volunteering)) {
            foreach ($volunteering as $vol) {
                $csv .= $this->escapeCSV($vol->title ?? '') . ",";
                $csv .= $this->escapeCSV($vol->volunteer_position ?? '') . ",";
                $csv .= $this->escapeCSV($vol->volunteer_status ?? '') . ",";
                $csv .= date('Y-m-d', strtotime($vol->event_date ?? '')) . "\n";
            }
        } else {
            $csv .= "No volunteering activities this month\n";
        }
        $csv .= "\n";

        // Donations Section
        $csv .= "DONATIONS\n";
        $csv .= "Event,Amount (LKR),Donor Name,Status,Date\n";
        if (!empty($donations)) {
            foreach ($donations as $donation) {
                $csv .= $this->escapeCSV($donation->title ?? '') . ",";
                $csv .= number_format($donation->amount ?? 0, 2) . ",";
                $csv .= $this->escapeCSV($donation->donor_name ?? '') . ",";
                $csv .= $this->escapeCSV($donation->status ?? '') . ",";
                $csv .= date('Y-m-d', strtotime($donation->created_at ?? '')) . "\n";
            }
        } else {
            $csv .= "No donations this month\n";
        }
        $csv .= "\n";

        // Participation Section
        $csv .= "EVENT PARTICIPATION\n";
        $csv .= "Event,Ticket Type,Amount Paid (LKR),Registration Date,Event Date\n";
        if (!empty($participation)) {
            foreach ($participation as $event) {
                $csv .= $this->escapeCSV($event->title ?? '') . ",";
                $csv .= $this->escapeCSV($event->ticket_type ?? '') . ",";
                $csv .= number_format($event->amount_paid ?? 0, 2) . ",";
                $csv .= date('Y-m-d', strtotime($event->registration_date ?? '')) . ",";
                $csv .= date('Y-m-d', strtotime($event->event_date ?? '')) . "\n";
            }
        } else {
            $csv .= "No event participation this month\n";
        }

        return $csv;
    }

    /**
     * Escape CSV values
     */
    private function escapeCSV($value)
    {
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Generate HTML for the monthly report
     */
    private function generateReportHTML($userName, $month, $data)
    {
        $monthName = date('F Y', strtotime($month . '-01'));

        ob_start();
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <title>Monthly Activity Report - <?php echo $monthName; ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                }

                h1 {
                    color: #4F46E5;
                    border-bottom: 3px solid #4F46E5;
                    padding-bottom: 10px;
                }

                h2 {
                    color: #6366F1;
                    margin-top: 30px;
                    border-left: 4px solid #6366F1;
                    padding-left: 10px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }

                th,
                td {
                    border: 1px solid #ddd;
                    padding: 12px;
                    text-align: left;
                }

                th {
                    background-color: #4F46E5;
                    color: white;
                }

                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .summary {
                    background-color: #EEF2FF;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }

                .total {
                    font-weight: bold;
                    font-size: 18px;
                    color: #4F46E5;
                }

                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>

        <body>
            <h1>UniPulse - Monthly Activity Report</h1>
            <p><strong>User:</strong> <?php echo htmlspecialchars($userName); ?></p>
            <p><strong>Period:</strong> <?php echo $monthName; ?></p>
            <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>

            <div class="summary">
                <h3>Summary</h3>
                <p>Volunteer Activities: <?php echo count($data['volunteering'] ?: []); ?></p>
                <p>Events Participated: <?php echo count($data['participation'] ?: []); ?></p>
                <p>Donations Made: <?php echo count($data['donations'] ?: []); ?></p>
                <p class="total">Total Donations: LKR <?php echo number_format($data['donationTotal'], 2); ?></p>
                <p class="total">Total Event Spending: LKR <?php echo number_format($data['eventSpending'], 2); ?></p>
            </div>

            <h2>1. Volunteering Activities</h2>
            <?php if (!empty($data['volunteering'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['volunteering'] as $vol): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vol->title); ?></td>
                                <td><?php echo htmlspecialchars($vol->volunteer_position ?? 'General'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($vol->event_date)); ?></td>
                                <td><?php echo htmlspecialchars($vol->location); ?></td>
                                <td><?php echo ucfirst($vol->volunteer_status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No volunteering activities this month.</p>
            <?php endif; ?>

            <h2>2. Donations</h2>
            <?php if (!empty($data['donations'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['donations'] as $don): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($don->event_title); ?></td>
                                <td>LKR <?php echo number_format($don->amount, 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($don->created_at)); ?></td>
                                <td><?php echo ucfirst($don->status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="total">Total: LKR <?php echo number_format($data['donationTotal'], 2); ?></p>
            <?php else: ?>
                <p>No donations made this month.</p>
            <?php endif; ?>

            <h2>3. Event Participation</h2>
            <?php if (!empty($data['participation'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Ticket Type</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['participation'] as $part): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($part->title); ?></td>
                                <td><?php echo date('M d, Y', strtotime($part->event_date)); ?></td>
                                <td><?php echo htmlspecialchars($part->location); ?></td>
                                <td><?php echo ucfirst($part->ticket_type ?? 'Free'); ?></td>
                                <td>LKR <?php echo number_format($part->amount_paid, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="total">Total: LKR <?php echo number_format($data['eventSpending'], 2); ?></p>
            <?php else: ?>
                <p>No event participation this month.</p>
            <?php endif; ?>

            <h2>4. Other Information</h2>
            <p>Total Activities: <?php echo count($data['volunteering'] ?: []) + count($data['participation'] ?: []); ?></p>
            <p>Total Financial Contribution: LKR <?php echo number_format($data['donationTotal'] + $data['eventSpending'], 2); ?></p>

            <div class="footer">
                <p>This report was generated by UniPulse - University Event Management System</p>
                <p><?php echo date('Y'); ?> UniPulse. All rights reserved.</p>
            </div>
        </body>

        </html>
<?php
        return ob_get_clean();
    }
}
