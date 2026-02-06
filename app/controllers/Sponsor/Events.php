<?php

class SponsorEvents extends Controller{

    private $eventModel;
    
    public function __construct() {
        parent::__construct();
        // Initialize Event model
        $this->eventModel = new Event();
    }

    public function index($a = '', $b = '' , $c = ''){
        
        // Check if this is a sponsor view request
        if (isset($_GET['view']) && $_GET['view'] === 'sponsor') {
            return $this->browseForSponsors();
        }
        
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
            
            // Get current user
            $currentUser = AuthService::getCurrentUser();
            
            // Get events from database
            $events = $this->eventModel->getAllEvents($filters, $currentUser);
            
            // Get total count for pagination (without limit)
            $totalEvents = $this->eventModel->getAllEvents([], $currentUser);
            $totalPages = ceil(count($totalEvents) / $limit);
            
            // Prepare data for view with server data for JavaScript
            $data = [
                'events' => $events,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'serverData' => [
                    'events' => $events,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'filters' => $filters,
                    'apiEndpoint' => '/unipulse/public/sponsor/events/getEvents'
                ]
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in SponsorEvents::index: " . $e->getMessage());
            $data = [
                'error' => 'Unable to load events. Please try again later.',
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => []
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
            
            // Get current user for visibility filtering
            $currentUser = AuthService::getCurrentUser();
            
            // Get events from database
            $events = $this->eventModel->getAllEvents($filters, $currentUser);
            
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
            error_log("Database error in SponsorEvents::getEvents: " . $e->getMessage());
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
     * Browse events that are seeking sponsors
     */
    private function browseForSponsors() {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        // Temporarily disable auth for testing
        /*if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }*/
        
        // Set a default user for testing
        if (!$currentUser) {
            $currentUser = ['id' => 1, 'type' => 'sponsor', 'company_name' => 'Test Sponsor'];
        }

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
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Only show upcoming events that might need sponsors
            $filters['status'] = 'upcoming';
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 9; // Events per page
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Get current user for visibility filtering
            $currentUser = AuthService::getCurrentUser();
            
            // Get events seeking sponsors from database
            $events = $this->eventModel->getEventsSeekingSponsors($filters, $currentUser);
            
            // Get total count for pagination
            $totalFilters = $filters;
            unset($totalFilters['limit'], $totalFilters['offset']);
            $totalEvents = $this->eventModel->getEventsSeekingSponsors($totalFilters, $currentUser);
            $totalPages = ceil(count($totalEvents) / $limit);
            
            // Prepare data for view
            $data = [
                'events' => $events,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'user' => $currentUser,
                'page_title' => 'Find Events to Sponsor'
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in SponsorEvents::browse: " . $e->getMessage());
            $data = [
                'error' => 'Unable to load events. Please try again later.',
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => [],
                'user' => $currentUser,
                'page_title' => 'Find Events to Sponsor'
            ];
        }
        
        $this->view('Sponsor/browse-events', $data);
    }
}
