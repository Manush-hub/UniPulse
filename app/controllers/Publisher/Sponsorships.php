<?php

class Sponsorships extends Controller
{
    use Database;

    /**
     * Show publisher's sponsorship management page
     */
    public function index()
    {
        SessionMiddleware::requireAuth('publisher');
        $currentUser = AuthService::getCurrentUser();

        try {
            error_log("Sponsorships::index - Publisher ID: " . $currentUser['id']);
            
            // Get all sponsorship requests for this publisher's events
            $sql = "SELECT 
                        es.*,
                        e.title as event_title,
                        e.event_date,
                        e.venue_name,
                        e.city,
                        e.university_name,
                        esp.package_name,
                        esp.package_type,
                        s.company_name as sponsor_name,
                        s.email as sponsor_email,
                        s.phone as sponsor_phone
                    FROM event_sponsorships es
                    INNER JOIN events e ON es.event_id = e.id
                    INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                    LEFT JOIN sponsors s ON es.sponsor_id = s.id AND es.sponsor_type = 'sponsor'
                    WHERE e.created_by = ?
                    ORDER BY 
                        CASE es.status
                            WHEN 'pending' THEN 1
                            WHEN 'approved' THEN 2
                            WHEN 'completed' THEN 3
                            WHEN 'rejected' THEN 4
                        END,
                        es.created_at DESC";
            
            $sponsorships = $this->query($sql, [$currentUser['id']]);
            
            error_log("Sponsorships::index - Query result: " . ($sponsorships ? count($sponsorships) . " records" : "false/empty"));
            
            // Convert to array if results exist
            if (!$sponsorships) {
                $sponsorships = [];
            } else {
                // Convert objects to arrays
                $sponsorships = array_map(function($item) {
                    return (array) $item;
                }, $sponsorships);
            }
            
            error_log("Sponsorships::index - After conversion: " . count($sponsorships) . " records");

            // Group by status for easier display
            $grouped = [
                'pending' => [],
                'approved' => [],
                'completed' => [],
                'rejected' => []
            ];

            foreach ($sponsorships as $sponsorship) {
                $grouped[$sponsorship['status']][] = $sponsorship;
            }

            $this->view('Publisher/sponsorships', [
                'sponsorships' => $sponsorships,
                'grouped' => $grouped,
                'stats' => [
                    'pending' => count($grouped['pending']),
                    'approved' => count($grouped['approved']),
                    'completed' => count($grouped['completed']),
                    'rejected' => count($grouped['rejected']),
                    'total' => count($sponsorships)
                ]
            ]);
        } catch (Exception $e) {
            error_log("Sponsorships::index error: " . $e->getMessage());
            $this->view('Publisher/sponsorships', [
                'sponsorships' => [],
                'grouped' => ['pending' => [], 'approved' => [], 'completed' => [], 'rejected' => []],
                'stats' => ['pending' => 0, 'approved' => 0, 'completed' => 0, 'rejected' => 0, 'total' => 0],
                'error' => 'Failed to load sponsorship requests'
            ]);
        }
    }

    /**
     * View details of a specific sponsorship request
     */
    public function detail($id = null)
    {
        SessionMiddleware::requireAuth('publisher');
        $currentUser = AuthService::getCurrentUser();

        if (!$id) {
            header('Location: /unipulse/public/publisher/sponsorships');
            exit;
        }

        try {
            $sql = "SELECT 
                        es.*,
                        e.title as event_title,
                        e.description as event_description,
                        e.event_date,
                        e.event_time,
                        e.venue_name,
                        e.city,
                        e.university_name,
                        e.image_url as event_image,
                        e.created_by as event_publisher_id,
                        esp.package_name,
                        esp.package_type,
                        esp.description as package_description,
                        esp.benefits as package_benefits,
                        s.company_name as sponsor_name,
                        s.email as sponsor_email,
                        s.contact_number as sponsor_phone,
                        s.website as sponsor_website,
                        s.business_address as sponsor_address
                    FROM event_sponsorships es
                    INNER JOIN events e ON es.event_id = e.id
                    INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                    LEFT JOIN sponsors s ON es.sponsor_id = s.id AND es.sponsor_type = 'sponsor'
                    WHERE es.id = ? AND e.created_by = ?";
            
            $sponsorship = $this->query($sql, [$id, $currentUser['id']]);

            if (empty($sponsorship)) {
                header('Location: /unipulse/public/publisher/sponsorships');
                exit;
            }

            $this->view('Publisher/sponsorship-view', [
                'sponsorship' => $sponsorship[0]
            ]);
        } catch (Exception $e) {
            error_log("Sponsorships::detail error: " . $e->getMessage());
            header('Location: /unipulse/public/publisher/sponsorships');
            exit;
        }
    }

    /**
     * Approve a sponsorship request
     */
    public function approve()
    {
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            return;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $sponsorshipId = $_POST['sponsorship_id'] ?? null;

            if (!$sponsorshipId) {
                throw new Exception('Sponsorship ID is required');
            }

            // Verify this sponsorship belongs to publisher's event
            $checkSql = "SELECT es.*, e.created_by 
                        FROM event_sponsorships es
                        INNER JOIN events e ON es.event_id = e.id
                        WHERE es.id = ? AND e.created_by = ?";
            
            $sponsorship = $this->query($checkSql, [$sponsorshipId, $currentUser['id']]);

            if (empty($sponsorship)) {
                throw new Exception('Sponsorship not found or unauthorized');
            }

            // Update sponsorship status
            $sql = "UPDATE event_sponsorships 
                    SET status = 'approved', 
                        approved_by = ?, 
                        approved_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?";
            
            $conn = $this->connect();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$currentUser['id'], $sponsorshipId]);

            if ($result) {
                // TODO: Send notification email to sponsor
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship approved successfully'
                ]);
                exit;
            } else {
                throw new Exception('Failed to approve sponsorship');
            }

        } catch (Exception $e) {
            error_log("Sponsorships::approve error: " . $e->getMessage());
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Reject a sponsorship request
     */
    public function reject()
    {
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            return;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $sponsorshipId = $_POST['sponsorship_id'] ?? null;
            $reason = $_POST['reason'] ?? '';

            if (!$sponsorshipId) {
                throw new Exception('Sponsorship ID is required');
            }

            // Verify this sponsorship belongs to publisher's event
            $checkSql = "SELECT es.*, e.created_by 
                        FROM event_sponsorships es
                        INNER JOIN events e ON es.event_id = e.id
                        WHERE es.id = ? AND e.created_by = ?";
            
            $sponsorship = $this->query($checkSql, [$sponsorshipId, $currentUser['id']]);

            if (empty($sponsorship)) {
                throw new Exception('Sponsorship not found or unauthorized');
            }

            // Update sponsorship status with rejection reason
            $sql = "UPDATE event_sponsorships 
                    SET status = 'rejected',
                        notes = CONCAT(IFNULL(notes, ''), '\n\nRejection Reason: ', ?),
                        updated_at = NOW()
                    WHERE id = ?";
            
            $conn = $this->connect();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$reason, $sponsorshipId]);

            if ($result) {
                // TODO: Send notification email to sponsor
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship rejected'
                ]);
                exit;
            } else {
                throw new Exception('Failed to reject sponsorship');
            }

        } catch (Exception $e) {
            error_log("Sponsorships::reject error: " . $e->getMessage());
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mark a sponsorship as completed
     */
    public function complete()
    {
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            return;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $sponsorshipId = $_POST['sponsorship_id'] ?? null;

            if (!$sponsorshipId) {
                throw new Exception('Sponsorship ID is required');
            }

            // Verify this sponsorship belongs to publisher's event and is approved
            $checkSql = "SELECT es.*, e.created_by 
                        FROM event_sponsorships es
                        INNER JOIN events e ON es.event_id = e.id
                        WHERE es.id = ? AND e.created_by = ? AND es.status = 'approved'";
            
            $sponsorship = $this->query($checkSql, [$sponsorshipId, $currentUser['id']]);

            if (empty($sponsorship)) {
                throw new Exception('Sponsorship not found, unauthorized, or not approved');
            }

            // Update sponsorship status to completed
            $sql = "UPDATE event_sponsorships 
                    SET status = 'completed',
                        updated_at = NOW()
                    WHERE id = ?";
            
            $conn = $this->connect();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$sponsorshipId]);

            if ($result) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship marked as completed'
                ]);
                exit;
            } else {
                throw new Exception('Failed to complete sponsorship');
            }

        } catch (Exception $e) {
            error_log("Sponsorships::complete error: " . $e->getMessage());
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
