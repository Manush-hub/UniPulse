<?php

class Sponsorship extends Controller
{
    use Database;

    /**
     * Show sponsor's sponsorship requests page
     */
    public function index()
    {
        SessionMiddleware::requireAuth('sponsor');
        $currentUser = AuthService::getCurrentUser();

        try {
            // Get all sponsorship requests for this sponsor
            $sql = "SELECT 
                        es.*,
                        e.title as event_title,
                        e.event_date,
                        e.venue_name,
                        e.city,
                        e.university_name,
                        esp.package_name,
                        esp.package_type,
                        p.society_name as organizer_name,
                        p.email as organizer_email
                    FROM event_sponsorships es
                    INNER JOIN events e ON es.event_id = e.id
                    INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                    LEFT JOIN publishers p ON e.created_by = p.id
                    WHERE es.sponsor_id = ? AND es.sponsor_type = 'sponsor'
                    ORDER BY es.created_at DESC";
            
            $sponsorships = $this->query($sql, [$currentUser['id']]);

            // Convert to array if results exist
            if (!$sponsorships) {
                $sponsorships = [];
            } else {
                // Convert objects to arrays
                $sponsorships = array_map(function($item) {
                    return (array) $item;
                }, $sponsorships);
            }

            $this->view('Sponsor/sponsorships', [
                'sponsorships' => $sponsorships
            ]);
        } catch (Exception $e) {
            error_log("Sponsorship::index error: " . $e->getMessage());
            $this->view('Sponsor/sponsorships', [
                'sponsorships' => [],
                'error' => 'Failed to load sponsorship requests'
            ]);
        }
    }

    /**
     * Submit a new sponsorship request with payment proof
     */
    public function submit()
    {
        // Prevent any output before JSON
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
        
        if ($currentUser['type'] !== 'sponsor') {
            ob_end_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        // Handle POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        try {
            // Validate required fields
            $eventId = $_POST['event_id'] ?? null;
            $packageId = $_POST['package_id'] ?? null;
            $amount = $_POST['amount'] ?? null;

            if (!$eventId || !$packageId || !$amount) {
                throw new Exception('Missing required fields');
            }

            // Validate file upload
            if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Payment proof file is required');
            }

            $file = $_FILES['payment_proof'];
            
            // Validate file size (5MB max)
            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                throw new Exception('File size must be less than 5MB');
            }

            // Validate file type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimes)) {
                throw new Exception('Invalid file type. Only JPG, PNG, and PDF are allowed');
            }

            // Create upload directory if it doesn't exist
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/unipulse/public/uploads/sponsorship_receipts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'receipt_' . $currentUser['id'] . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            $dbPath = '/unipulse/public/uploads/sponsorship_receipts/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload file');
            }

            // Insert sponsorship request into database
            $sql = "INSERT INTO event_sponsorships 
                    (event_id, package_id, sponsor_id, sponsor_type, amount, status, 
                     payment_proof, payment_reference, payment_date, notes, created_at)
                    VALUES (?, ?, ?, 'sponsor', ?, 'pending', ?, ?, ?, ?, NOW())";

            $conn = $this->connect();
            $stmt = $conn->prepare($sql);
            
            $result = $stmt->execute([
                $eventId,
                $packageId,
                $currentUser['id'],
                $amount,
                $dbPath,
                null, // payment_reference
                null, // payment_date
                !empty($_POST['notes']) ? $_POST['notes'] : null
            ]);

            if ($result) {
                // Send notification email to publisher (optional - implement later)
                // $this->sendSponsorshipNotification($eventId);

                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sponsorship request submitted successfully'
                ]);
                exit;
            } else {
                throw new Exception('Failed to save sponsorship request');
            }

        } catch (Exception $e) {
            error_log("Sponsorship::submit error: " . $e->getMessage());
            
            // Clean up uploaded file if database insert failed
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }

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
     * View details of a specific sponsorship request
     */
    public function detail($id = null)
    {
        SessionMiddleware::requireAuth('sponsor');
        $currentUser = AuthService::getCurrentUser();

        if (!$id) {
            header('Location: /unipulse/public/sponsor/sponsorships');
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
                        esp.package_name,
                        esp.package_type,
                        esp.description as package_description,
                        esp.benefits as package_benefits,
                        p.society_name as organizer_name,
                        p.email as organizer_email,
                        p.contact_number as organizer_phone
                    FROM event_sponsorships es
                    INNER JOIN events e ON es.event_id = e.id
                    INNER JOIN event_sponsorship_packages esp ON es.package_id = esp.id
                    LEFT JOIN publishers p ON e.created_by = p.id
                    WHERE es.id = ? AND es.sponsor_id = ? AND es.sponsor_type = 'sponsor'";
            
            $sponsorship = $this->query($sql, [$id, $currentUser['id']]);

            if (empty($sponsorship)) {
                header('Location: /unipulse/public/sponsor/sponsorships');
                exit;
            }

            $this->view('Sponsor/sponsorship-view', [
                'sponsorship' => $sponsorship[0]
            ]);
        } catch (Exception $e) {
            error_log("Sponsorship::detail error: " . $e->getMessage());
            header('Location: /unipulse/public/sponsor/sponsorships');
            exit;
        }
    }
}
