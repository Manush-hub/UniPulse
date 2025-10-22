<?php

class PublisherEvents extends Controller{

    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        // Initialize Event model
        $this->eventModel = new Event();
    }

    public function index($a = '', $b = '' , $c = ''){
        
        // Check if user is logged in and is a publisher
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get current user data
        $currentUser = AuthService::getCurrentUser();
        
        $data = [];
        
        try {
            // Get filters from request
            $filters = [];
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }
            
            if (isset($_GET['university']) && !empty($_GET['university'])) {
                $filters['university'] = $_GET['university'];
            }
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 6; // Events per page
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Get current user role
            $currentUser = AuthService::getCurrentUser();
            $userRole = $currentUser ? $currentUser['type'] : 'user';
            
            // Get events from database based on user role
            $events = $this->eventModel->getEventsByRole($userRole, $filters);
            
            // Format events for JavaScript
            $formattedEvents = array_map([$this, 'formatEventForResponse'], $events);
            
            // Get total count for pagination (without limit)
            $totalFilters = $filters;
            unset($totalFilters['limit'], $totalFilters['offset']);
            $totalEvents = $this->eventModel->getEventsByRole($userRole, $totalFilters);
            $totalPages = ceil(count($totalEvents) / $limit);
            
            // Prepare data for view with server data for JavaScript
            $data = [
                'events' => $formattedEvents,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'serverData' => [
                    'events' => $formattedEvents,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'filters' => $filters,
                    'apiEndpoint' => '/unipulse/public/publisher/events/getEvents',
                    'currentUser' => $currentUser
                ]
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in PublisherEvents::index: " . $e->getMessage());
            $data = [
                'error' => 'Unable to load events. Please try again later.',
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => [],
                'serverData' => [
                    'events' => [],
                    'currentPage' => 1,
                    'totalPages' => 1,
                    'filters' => [],
                    'apiEndpoint' => '/unipulse/public/publisher/events/getEvents'
                ]
            ];
        }
        
        $this->view('events', $data);
    }
    
    /**
     * API endpoint to get events as JSON
     */
    public function getEvents() {
        header('Content-Type: application/json');
        
        try {
            // Get filters from request
            $filters = [];
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }
            
            if (isset($_GET['university']) && !empty($_GET['university'])) {
                $filters['university'] = $_GET['university'];
            }
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 6;
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Get current user role
            $currentUser = AuthService::getCurrentUser();
            $userRole = $currentUser ? $currentUser['type'] : 'user';
            
            // Get events from database based on user role
            $events = $this->eventModel->getEventsByRole($userRole, $filters);
            
            // Format events for JavaScript
            $formattedEvents = array_map([$this, 'formatEventForResponse'], $events);
            
            // Get total count for pagination (without limit)
            $totalFilters = $filters;
            unset($totalFilters['limit'], $totalFilters['offset']);
            $totalEvents = $this->eventModel->getEventsByRole($userRole, $totalFilters);
            $totalPages = ceil(count($totalEvents) / $limit);
            
            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'pagination' => [
                    'currentPage' => $page,
                    'limit' => $limit,
                    'hasMore' => count($events) == $limit
                ]
            ]);
            
        } catch (Exception $e) {
            // Log error and return generic error message
            error_log("Database error in PublisherEvents::getEvents: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to retrieve events. Please try again later.',
                'events' => [],
                'pagination' => [
                    'currentPage' => 1,
                    'limit' => 6,
                    'hasMore' => false
                ]
            ]);
        }
        
        exit;
    }
    
    /**
     * Helper method to format event data for API responses
     */
    private function formatEventForResponse($event) {
        $formattedEvent = (array) $event;
        
        // Decode JSON fields
        if (isset($formattedEvent['requirements']) && is_string($formattedEvent['requirements'])) {
            $formattedEvent['requirements'] = json_decode($formattedEvent['requirements'], true) ?: [];
        }
        if (isset($formattedEvent['schedule']) && is_string($formattedEvent['schedule'])) {
            $formattedEvent['schedule'] = json_decode($formattedEvent['schedule'], true) ?: [];
        }
        
        // Format date and time for frontend
        if (isset($formattedEvent['event_date'])) {
            $formattedEvent['date'] = $formattedEvent['event_date'];
        }
        
        if (isset($formattedEvent['event_time'])) {
            $formattedEvent['time'] = date('h:i A', strtotime($formattedEvent['event_time']));
        }
        
        return $formattedEvent;
    }
    
    public function delete($eventId = '') {
        // Check if request is AJAX and POST
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' ||
            $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }
        
        // Check if user is logged in and is a publisher
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        if (empty($eventId)) {
            echo json_encode(['success' => false, 'message' => 'Event ID is required']);
            exit();
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            // Get event to verify ownership
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                echo json_encode(['success' => false, 'message' => 'Event not found']);
                exit();
            }
            
            // Check if current user owns this event
            if ($event->created_by_type !== 'publisher' || $event->created_by != $currentUser['id']) {
                echo json_encode(['success' => false, 'message' => 'You can only delete your own events']);
                exit();
            }
            
            // Check if event has any participants/registrations
            // Add this check if you have a registrations table
            // $hasRegistrations = $this->eventModel->hasRegistrations($eventId);
            // if ($hasRegistrations) {
            //     echo json_encode(['success' => false, 'message' => 'Cannot delete event with existing registrations']);
            //     exit();
            // }
            
            // Delete the event using the existing method
            $result = $this->eventModel->deleteEvent($eventId, $currentUser['id']);
            
            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => $result['message']]);
            } else {
                $errorMessage = isset($result['errors']['general']) ? $result['errors']['general'] : 'Failed to delete event';
                echo json_encode(['success' => false, 'message' => $errorMessage]);
            }
            
        } catch (Exception $e) {
            error_log("Error deleting event: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the event']);
        }
        
        exit();
    }
    
    public function edit($eventId = '') {
        // Check if user is logged in and is a publisher
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'publisher') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (empty($eventId)) {
            header('Location: /unipulse/public/publisher/events');
            exit();
        }
        
        try {
            $currentUser = AuthService::getCurrentUser();
            
            // Get event to verify ownership
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                $_SESSION['error'] = 'Event not found';
                header('Location: /unipulse/public/publisher/events');
                exit();
            }
            
            // Check if current user owns this event
            if ($event->created_by_type !== 'publisher' || $event->created_by != $currentUser['id']) {
                $_SESSION['error'] = 'You can only edit your own events';
                header('Location: /unipulse/public/publisher/events');
                exit();
            }
            
            // Redirect to the edit event page
            header("Location: /unipulse/public/publisher/editevent/{$eventId}");
            exit();
            
        } catch (Exception $e) {
            error_log("Error accessing edit event: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while accessing the event';
            header('Location: /unipulse/public/publisher/events');
            exit();
        }
    }
}
