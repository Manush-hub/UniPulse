<?php

class Sponsorships extends Controller
{
    use Database;


    /**
     * Approve a sponsorship request
     */
    public function approve()
    {
        // Disable error display for AJAX endpoint (errors will still be logged)
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        
        // Clear any previous output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            ob_end_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            ob_end_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
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

            $conn = $this->connect();
            $conn->beginTransaction();

            try {
                // Update sponsorship status to completed directly
                $sql = "UPDATE event_sponsorships 
                        SET status = 'completed', 
                            approved_by = ?, 
                            approved_at = NOW(),
                            updated_at = NOW()
                        WHERE id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$currentUser['id'], $sponsorshipId]);

                // Increment filled_slots in the package
                $updatePackageSql = "UPDATE event_sponsorship_packages 
                                     SET filled_slots = filled_slots + 1
                                     WHERE id = ?";
                $stmtPackage = $conn->prepare($updatePackageSql);
                $stmtPackage->execute([$sponsorship[0]->package_id]);

                $conn->commit();

                // TODO: Send notification email to sponsor
                
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship marked as received and completed successfully'
                ]);
                exit;

            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Sponsorships::approve error: " . $e->getMessage());
            ob_end_clean();
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Reject a sponsorship request
     */
    public function reject()
    {
        // Disable error display for AJAX endpoint
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        
        // Clear any previous output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            ob_end_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            ob_end_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
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

            $conn = $this->connect();
            $conn->beginTransaction();

            try {
                $previousStatus = $sponsorship[0]->status;

                // Update sponsorship status with rejection reason
                $sql = "UPDATE event_sponsorships 
                        SET status = 'rejected',
                            notes = CONCAT(IFNULL(notes, ''), '\n\nRejection Reason: ', ?),
                            updated_at = NOW()
                        WHERE id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$reason, $sponsorshipId]);

                // If previously completed, decrement filled_slots
                if ($previousStatus === 'completed') {
                    $updatePackageSql = "UPDATE event_sponsorship_packages 
                                         SET filled_slots = GREATEST(0, filled_slots - 1)
                                         WHERE id = ?";
                    $stmtPackage = $conn->prepare($updatePackageSql);
                    $stmtPackage->execute([$sponsorship[0]->package_id]);
                }

                $conn->commit();

                // TODO: Send notification email to sponsor
                
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship rejected'
                ]);
                exit;

            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Sponsorships::reject error: " . $e->getMessage());
            ob_end_clean();
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Mark a sponsorship as completed
     */
    public function complete()
    {
        // Disable error display for AJAX endpoint
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        
        // Clear any previous output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        // Check authentication for AJAX request
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!AuthService::isLoggedIn()) {
            ob_end_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $currentUser = AuthService::getCurrentUser();
        
        if ($currentUser['type'] !== 'publisher') {
            ob_end_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
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
                ob_end_clean();
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
            ob_end_clean();
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}
