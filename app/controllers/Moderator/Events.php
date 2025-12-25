<?php

class ModeratorEvents extends Controller{

    private $eventModel;
    private $moderatorModel;
    
    public function __construct() {
        parent::__construct();
        // Initialize Event model
        $this->eventModel = new Event();
        $this->moderatorModel = new Moderator();
    }

    public function index($a = '', $b = '' , $c = ''){
        
        // Check if user is logged in and is a moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        
        // Get moderator data for header
        try {
            $currentUser = AuthService::getCurrentUser();
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            $data['moderator'] = $moderator;
            $data['user'] = $currentUser;
            
            // Debug: Log moderator data
            error_log("ModeratorEvents - Moderator loaded: " . ($moderator ? $moderator->full_name : 'NULL'));
        } catch (Exception $e) {
            error_log("Error loading moderator data: " . $e->getMessage());
            $data['moderator'] = (object) ['full_name' => 'Moderator'];
        }
        
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
            
            // Get total count for pagination (without limit)
            $totalEvents = $this->eventModel->getEventsByRole($userRole);
            $totalPages = ceil(count($totalEvents) / $limit);
            
            // Prepare data for view with server data for JavaScript
            $data['events'] = $events;
            $data['currentPage'] = $page;
            $data['totalPages'] = $totalPages;
            $data['filters'] = $filters;
            $data['serverData'] = [
                'events' => $events,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'apiEndpoint' => '/unipulse/public/Moderator/events/getEvents'
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in ModeratorEvents::index: " . $e->getMessage());
            $data['error'] = 'Unable to load events. Please try again later.';
            $data['events'] = [];
            $data['currentPage'] = 1;
            $data['totalPages'] = 1;
            $data['filters'] = [];
            $data['serverData'] = [
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => [],
                'apiEndpoint' => '/unipulse/public/moderator/events/getEvents'
            ];
        }
        
        $this->view('Moderator/events', $data);
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
            
            // Format events for JSON response
            $formattedEvents = [];
            foreach ($events as $event) {
                $formattedEvent = $this->formatEventForResponse($event);
                $formattedEvents[] = $formattedEvent;
            }
            
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
            error_log("Database error in ModeratorEvents::getEvents: " . $e->getMessage());
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
    
    /**
     * Hide/Delete an event (soft delete)
     */
    public function hideEvent() {
        header('Content-Type: application/json');
        
        // Check authentication
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            // Get POST data
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['event_id']) || !isset($input['reason'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event ID and reason are required'
                ]);
                exit;
            }
            
            $eventId = intval($input['event_id']);
            $reason = trim($input['reason']);
            
            if (empty($reason)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Please provide a reason for hiding this event'
                ]);
                exit;
            }
            
            if (strlen($reason) < 10) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Reason must be at least 10 characters long'
                ]);
                exit;
            }
            
            // Get current moderator
            $currentUser = AuthService::getCurrentUser();
            $moderator = $this->moderatorModel->findById($currentUser['id']);
            
            if (!$moderator) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Moderator not found'
                ]);
                exit;
            }
            
            // Moderators can now moderate any event regardless of university
            // University restriction removed as per requirements
            
            // Log the attempt
            error_log("Moderator {$currentUser['id']} attempting to hide event $eventId with reason: $reason");
            
            // Soft delete the event
            $result = $this->eventModel->softDelete($eventId, $currentUser['id'], $reason);
            
            error_log("softDelete result: " . ($result ? 'true' : 'false'));
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event has been hidden and publisher has been notified'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to hide event. Please try again.'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error hiding event: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred while hiding the event'
            ]);
        }
        
        exit;
    }
    
    /**
     * Restore a hidden event (admin/moderator only)
     */
    public function restoreEvent() {
        header('Content-Type: application/json');
        
        // Check authentication
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['event_id'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event ID is required'
                ]);
                exit;
            }
            
            $eventId = intval($input['event_id']);
            
            // Restore the event
            $result = $this->eventModel->restore($eventId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Event has been restored successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to restore event'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error restoring event: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred while restoring the event'
            ]);
        }
        
        exit;
    }
    
    /**
     * View hidden events page
     */
    public function hiddenEvents($a = '', $b = '', $c = '') {
        // Check if user is logged in and is a moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $data = [];
        
        // Get moderator data for header
        try {
            $currentUser = AuthService::getCurrentUser();
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($currentUser['id']);
            $data['moderator'] = $moderator;
            $data['user'] = $currentUser;
        } catch (Exception $e) {
            error_log("Error loading moderator data: " . $e->getMessage());
            $data['moderator'] = (object) ['full_name' => 'Moderator'];
        }
        
        try {
            // Get filters from request
            $filters = [];
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }
            
            if (isset($_GET['university']) && !empty($_GET['university'])) {
                $filters['university'] = $_GET['university'];
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
            
            // Get hidden events from database
            $hiddenEvents = $this->eventModel->getHiddenEvents($filters);
            
            // Get total count for pagination (without limit)
            $totalHiddenEvents = $this->eventModel->getHiddenEvents();
            $totalPages = ceil(count($totalHiddenEvents) / $limit);
            
            // Prepare data for view with server data for JavaScript
            $data['events'] = $hiddenEvents;
            $data['currentPage'] = $page;
            $data['totalPages'] = $totalPages;
            $data['filters'] = $filters;
            $data['serverData'] = [
                'events' => $hiddenEvents,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'apiEndpoint' => '/unipulse/public/moderator/events/getHiddenEvents'
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in ModeratorEvents::hiddenEvents: " . $e->getMessage());
            $data['error'] = 'Unable to load hidden events. Please try again later.';
            $data['events'] = [];
            $data['currentPage'] = 1;
            $data['totalPages'] = 1;
            $data['filters'] = [];
            $data['serverData'] = [
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => [],
                'apiEndpoint' => '/unipulse/public/moderator/events/getHiddenEvents'
            ];
        }
        
        $this->view('Moderator/hidden_events', $data);
    }
    
    /**
     * API endpoint to get hidden events (AJAX)
     */
    public function getHiddenEvents() {
        header('Content-Type: application/json');
        
        // Check authentication
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        try {
            // Get filters from request
            $filters = [];
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }
            
            if (isset($_GET['university']) && !empty($_GET['university'])) {
                $filters['university'] = $_GET['university'];
            }
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 6;
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Get hidden events from database
            $hiddenEvents = $this->eventModel->getHiddenEvents($filters);
            
            // Get total count for pagination
            $totalHiddenEvents = $this->eventModel->getHiddenEvents();
            $totalPages = ceil(count($totalHiddenEvents) / $limit);
            
            // Format events data
            $formattedEvents = array_map(function($event) {
                return $this->formatEventData($event);
            }, $hiddenEvents);
            
            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'hasMore' => $page < $totalPages
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error fetching hidden events: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred while fetching hidden events'
            ]);
        }
        
        exit;
    }
}
