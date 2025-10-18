<?php

class PublisherEditevent extends Controller{

    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index($eventId = '', $b = '' , $c = ''){
        // Check if user is logged in and is a publisher
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            // For AJAX requests, return JSON error instead of redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'You must be logged in as a publisher to edit events. Please login and try again.'],
                    'redirect' => '/unipulse/public/signin'
                ]);
                exit();
            }
            
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get current user
        $currentUser = AuthService::getCurrentUser();
        
        // Check if event ID is provided
        if (empty($eventId) || !is_numeric($eventId)) {
            header('Location: /unipulse/public/publisher/events?error=Invalid event ID');
            exit();
        }
        
        // Get the event
        $event = $this->eventModel->getEventById($eventId);
        
        if (!$event) {
            header('Location: /unipulse/public/publisher/events?error=Event not found');
            exit();
        }
        
        // Check if current user owns this event
        if ($event->created_by != $currentUser['id']) {
            header('Location: /unipulse/public/publisher/events?error=You can only edit your own events');
            exit();
        }
        
        $data = [];
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission($eventId);
            return;
        }
        
        // Pass event data to view
        $data['event'] = $event;
        $data['event_id'] = $eventId;
        
        $this->view('editevent', $data);
    }
    
    private function handleFormSubmission($eventId) {
        // Start output buffering to prevent any accidental output
        ob_start();
        
        // More robust AJAX detection
        $isAjax = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
            (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (isset($_POST['ajax']) && $_POST['ajax'] == '1')
        );
        
        // Set JSON header early for AJAX requests
        if ($isAjax) {
            // Clear any previous output
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
        }
        
        try {
            $user = AuthService::getCurrentUser();
            
            // Verify ownership again
            $event = $this->eventModel->getEventById($eventId);
            if (!$event || $event->created_by != $user['id']) {
                throw new Exception('You can only edit your own events');
            }
            
            // Get form data (same structure as create event)
            $formData = [
                'title' => $_POST['event_name'] ?? '',
                'description' => $_POST['event_description'] ?? '',
                'category' => $_POST['event_category'] ?? '',
                'event_date' => $_POST['event_date'] ?? '',
                'event_time' => $_POST['event_time'] ?? '',
                'location' => $_POST['event_location'] ?? '',
                'location_type' => $_POST['location-type'] ?? 'inside-university',
                'venue_name' => $_POST['venue_name'] ?? '',
                'street_address' => $_POST['street_address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'district_province' => $_POST['district_province'] ?? '',
                'faculty_department' => $_POST['faculty_department'] ?? '',
                'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : 100,
                'target_audience' => $_POST['audience'] ?? 'university-students',
                'ticket_type' => $_POST['ticketType'] ?? 'free-all',
                'registration_limit' => !empty($_POST['registration_limit']) ? (int)$_POST['registration_limit'] : null,
                'registration_start_date' => $_POST['registration_start_date'] ?? null,
                'registration_start_time' => $_POST['registration_start_time'] ?? null,
                'registration_end_date' => $_POST['registration_end_date'] ?? null,
                'registration_end_time' => $_POST['registration_end_time'] ?? null,
                'needs_volunteers' => isset($_POST['volunteerToggle']) && $_POST['volunteerToggle'] == '1' ? 1 : 0,
                'volunteers_needed' => !empty($_POST['volunteers_needed']) ? (int)$_POST['volunteers_needed'] : null,
                'accepts_donations' => isset($_POST['donationToggle']) && $_POST['donationToggle'] == '1' ? 1 : 0,
                'visibility' => $_POST['visibility'] ?? 'public'
            ];
            
            // Handle requirements if provided
            if (!empty($_POST['requirements'])) {
                $formData['requirements'] = explode("\n", $_POST['requirements']);
            }
            
            // Handle schedule if provided
            if (!empty($_POST['schedule'])) {
                $formData['schedule'] = json_decode($_POST['schedule'], true);
            }
            
            // Handle ticket types if provided
            if (!empty($_POST['ticket_types'])) {
                $formData['ticket_types'] = json_decode($_POST['ticket_types'], true);
            }
            
            // Handle custom fields if provided
            if (!empty($_POST['custom_fields'])) {
                $formData['custom_fields'] = json_decode($_POST['custom_fields'], true);
            }
            
            // Handle volunteer sources
            if (!empty($_POST['volunteer_sources'])) {
                if (is_array($_POST['volunteer_sources'])) {
                    $formData['volunteer_sources'] = $_POST['volunteer_sources'];
                } else {
                    $formData['volunteer_sources'] = json_decode($_POST['volunteer_sources'], true);
                }
            }
            
            // Handle volunteer positions
            if (!empty($_POST['volunteer_positions'])) {
                if (is_array($_POST['volunteer_positions'])) {
                    $formData['volunteer_positions'] = $_POST['volunteer_positions'];
                } else {
                    $formData['volunteer_positions'] = json_decode($_POST['volunteer_positions'], true);
                }
            }
            
            // Handle file upload for cover image
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['cover_image']);
                if ($uploadResult['success']) {
                    $formData['cover_image'] = $uploadResult['path'];
                    $formData['image_url'] = $uploadResult['path']; // Keep backward compatibility
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }
            
            // Update the event
            $result = $this->eventModel->updateEvent($eventId, $formData);
            
            if ($result['success']) {
                // Return JSON response for AJAX requests
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Event updated successfully!',
                        'event_id' => $eventId
                    ]);
                    exit();
                } else {
                    // Show success message and stay on the same page
                    $event = $this->eventModel->getEventById($eventId);
                    $data = [
                        'success' => 'Event updated successfully!',
                        'event' => $event,
                        'event_id' => $eventId
                    ];
                    $this->view('editevent', $data);
                    return;
                }
            } else {
                // Return error response
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'errors' => $result['errors'] ?? ['general' => 'Unknown error occurred']
                    ]);
                    exit();
                } else {
                    // Show form with errors
                    $event = $this->eventModel->getEventById($eventId);
                    $data = [
                        'errors' => $result['errors'] ?? ['general' => 'Unknown error occurred'],
                        'event' => $event,
                        'event_id' => $eventId,
                        'old_data' => $formData
                    ];
                    $this->view('editevent', $data);
                }
            }
            
        } catch (Exception $e) {
            error_log("Error updating event: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'Server error: ' . $e->getMessage()],
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
                exit();
            } else {
                $event = $this->eventModel->getEventById($eventId);
                $data = [
                    'errors' => ['general' => 'Server error: ' . $e->getMessage()],
                    'event' => $event,
                    'event_id' => $eventId,
                    'old_data' => $_POST
                ];
                $this->view('editevent', $data);
            }
        }
    }
    
    private function handleImageUpload($file) {
        $uploadDir = 'public/uploads/event_covers/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Please upload a valid image.'];
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('event_cover_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'uploads/event_covers/' . $filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload image.'];
    }
}