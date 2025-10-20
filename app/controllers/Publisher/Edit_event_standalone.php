<?php

class Edit_event_standalone extends Controller
{
    use Database;

    public function index($id = null)
    {
        // Check if user is authenticated
        $auth = new AuthService();
        if (!$auth->isLoggedIn()) {
            header('Location: /unipulse/public/signin');
            exit;
        }

        $currentUser = $auth->getCurrentUser();
        if (!$currentUser || $currentUser['role'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit;
        }

        // If ID is provided in URL, redirect to the standalone page with query parameter
        if ($id) {
            header("Location: /unipulse/public/publisher/edit-event-standalone?id=$id");
            exit;
        }

        // Load the standalone edit view
        $this->view('Publisher/edit-event-standalone');
    }

    // Handle AJAX requests for event data
    public function getEvent()
    {
        header('Content-Type: application/json');
        
        $auth = new AuthService();
        if (!$auth->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'Event ID required']);
            return;
        }

        try {
            $db = $this->connect();
            $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Event not found']);
                return;
            }

            // Check ownership
            $currentUser = $auth->getCurrentUser();
            if ($event['created_by'] != $currentUser['id'] && $event['created_by_type'] !== 'publisher') {
                echo json_encode(['success' => false, 'error' => 'Access denied']);
                return;
            }

            // Process requirements if they exist
            if ($event['requirements']) {
                $event['requirements'] = explode("\n", $event['requirements']);
            }

            // Convert boolean fields
            $event['needs_volunteers'] = (int)$event['needs_volunteers'];
            $event['accepts_donations'] = (int)$event['accepts_donations'];

            echo json_encode(['success' => true, 'event' => $event]);

        } catch (Exception $e) {
            error_log("Error fetching event: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error occurred']);
        }
    }

    // Handle event updates
    public function updateEvent()
    {
        header('Content-Type: application/json');
        
        $auth = new AuthService();
        if (!$auth->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $eventId = $_POST['event_id'] ?? null;
        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'Event ID required']);
            return;
        }

        try {
            $db = $this->connect();
            
            // Check if event exists and user owns it
            $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Event not found']);
                return;
            }

            $currentUser = $auth->getCurrentUser();
            if ($event['created_by'] != $currentUser['id'] && $event['created_by_type'] !== 'publisher') {
                echo json_encode(['success' => false, 'error' => 'Access denied']);
                return;
            }

            // Prepare update data
            $updateData = [
                'title' => $_POST['event_name'] ?? '',
                'description' => $_POST['event_description'] ?? '',
                'category' => $_POST['event_category'] ?? '',
                'target_audience' => $_POST['audience'] ?? '',
                'event_date' => $_POST['event_date'] ?? '',
                'event_time' => $_POST['event_time'] ?? '',
                'location' => $_POST['event_location'] ?? '',
                'location_type' => $_POST['location-type'] ?? 'inside-university',
                'max_participants' => (int)($_POST['max_participants'] ?? 100),
                'visibility' => $_POST['visibility'] ?? 'public',
                'ticket_type' => $_POST['ticketType'] ?? 'free-all',
                'needs_volunteers' => isset($_POST['volunteerToggle']) ? 1 : 0,
                'volunteers_needed' => (int)($_POST['volunteers_needed'] ?? 0),
                'accepts_donations' => isset($_POST['donationToggle']) ? 1 : 0,
                'requirements' => $_POST['requirements'] ?? ''
            ];

            // Handle file upload if provided
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '/Applications/MAMP/htdocs/unipulse/public/uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileInfo = pathinfo($_FILES['cover_image']['name']);
                $fileName = 'event_' . $eventId . '_' . time() . '.' . $fileInfo['extension'];
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
                    $updateData['cover_image'] = '/unipulse/public/uploads/events/' . $fileName;
                }
            }

            // Build and execute update query
            $setParts = [];
            $values = [];
            foreach ($updateData as $key => $value) {
                $setParts[] = "$key = ?";
                $values[] = $value;
            }
            $values[] = $eventId; // Add event ID for WHERE clause

            $sql = "UPDATE events SET " . implode(', ', $setParts) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($values);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update event']);
            }

        } catch (Exception $e) {
            error_log("Error updating event: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error occurred']);
        }
    }

    // Handle event deletion
    public function deleteEvent()
    {
        header('Content-Type: application/json');
        
        $auth = new AuthService();
        if (!$auth->isLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = $input['id'] ?? null;

        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'Event ID required']);
            return;
        }

        try {
            $db = $this->connect();
            
            // Check if event exists and user owns it
            $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Event not found']);
                return;
            }

            $currentUser = $auth->getCurrentUser();
            if ($event['created_by'] != $currentUser['id'] && $event['created_by_type'] !== 'publisher') {
                echo json_encode(['success' => false, 'error' => 'Access denied']);
                return;
            }

            // Delete the event
            $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
            $result = $stmt->execute([$eventId]);

            if ($result) {
                // Delete associated image file if exists
                if ($event['cover_image']) {
                    $imagePath = '/Applications/MAMP/htdocs' . $event['cover_image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete event']);
            }

        } catch (Exception $e) {
            error_log("Error deleting event: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error occurred']);
        }
    }
}