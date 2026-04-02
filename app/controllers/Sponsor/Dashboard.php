<?php
     
class SponsorDashboard extends Controller{
    use Database;

    public function index($a = '', $b = '' , $c = ''){
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
     * API endpoint to get user profile data
     */
    public function getUserProfile() {
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
    public function getNotifications() {
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
            
            // For now, return empty notifications
            // TODO: Implement notification system for sponsors
            echo json_encode([
                'success' => true,
                'notifications' => []
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
     * API endpoint to get sponsor statistics
     */
    public function getStats() {
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
    public function getActiveSponsorships() {
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
                $sponsorships = array_map(function($item) {
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
} 