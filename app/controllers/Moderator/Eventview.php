<?php

class ModeratorEventview extends Controller {
    
    private $eventModel;
    private $moderatorModel;
    
    public function __construct() {
        // Initialize models
        $this->eventModel = new Event();
        $this->moderatorModel = new Moderator();
    }
    
    public function index($id = null) {
        
        // Check if user is logged in and is a moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Get event ID from URL parameter or GET request
        $eventId = $id;
        if (!$eventId && isset($_GET['id'])) {
            $eventId = $_GET['id'];
        }
        
        $data = [];
        
        // Get moderator data for header
        try {
            $currentUser = AuthService::getCurrentUser();
            $moderator = $this->moderatorModel->findById($currentUser['id']);
            $data['moderator'] = $moderator;
            $data['user'] = $currentUser;
            
            // Add moderator's university to currentUser for filtering
            if ($moderator) {
                $currentUser['university'] = $moderator->university;
            }
        } catch (Exception $e) {
            error_log("Error loading moderator data: " . $e->getMessage());
            $data['moderator'] = (object) ['full_name' => 'Moderator'];
        }
        
        if ($eventId) {
            try {
                // Validate event ID is numeric
                if (!is_numeric($eventId)) {
                    error_log("ModeratorEventview::index - Invalid event ID (not numeric): " . $eventId);
                    $data['error'] = 'Invalid event ID';
                    $data['userRole'] = 'Moderator';
                    $data['serverData'] = [
                        'error' => 'Invalid event ID'
                    ];
                } else {
                    // Get specific event from database
                    $event = $this->eventModel->getEventById($eventId);
                    
                    if ($event) {
                        // Check if event was created by a publisher from moderator's university
                        $publisherModel = new Publisher();
                        $publisher = null;
                        
                        if ($event->created_by_type === 'publisher' && $event->created_by) {
                            $publisherData = $publisherModel->where(['id' => $event->created_by]);
                            if ($publisherData && count($publisherData) > 0) {
                                $publisher = $publisherData[0];
                            }
                        }
                        
                        // Check if publisher belongs to moderator's university
                        if ($moderator && $publisher && $publisher->university !== $moderator->university) {
                            error_log("ModeratorEventview::index - Access denied: Publisher from different university");
                            $data['error'] = 'You can only view events from publishers in your university';
                            $data['userRole'] = 'Moderator';
                            $data['serverData'] = [
                                'error' => 'Access denied'
                            ];
                        } elseif (!$publisher && $event->created_by_type === 'publisher') {
                            error_log("ModeratorEventview::index - Publisher not found");
                            $data['error'] = 'Event organizer not found';
                            $data['userRole'] = 'Moderator';
                            $data['serverData'] = [
                                'error' => 'Event organizer not found'
                            ];
                        } else {
                            // Get similar events from database (from same university)
                            $similarEvents = $this->eventModel->getSimilarEvents(
                                $event->id, 
                                $event->category, 
                                $event->university, 
                                3,
                                $currentUser
                            );
                            
                            // Pass server data to view for JavaScript
                            $data = [
                                'event' => $event,
                                'similarEvents' => $similarEvents,
                                'moderator' => $moderator,
                                'user' => $currentUser,
                                'userRole' => 'Moderator',
                                'error' => null,
                                'serverData' => [
                                    'event' => $event,
                                    'similarEvents' => $similarEvents,
                                    'apiEndpoint' => '/unipulse/public/moderator/eventview/getEvent'
                                ]
                            ];
                        }
                    } else {
                        error_log("ModeratorEventview::index - Event NOT found with ID: " . $eventId);
                        $data['error'] = 'Event not found in database';
                        $data['userRole'] = 'Moderator';
                        $data['serverData'] = [
                            'error' => 'Event not found in database'
                        ];
                    }
                }
            } catch (Exception $e) {
                // Log error and show user-friendly message
                error_log("Database error in ModeratorEventview::index: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $data['error'] = 'Unable to load event details. Please try again later.';
                $data['userRole'] = 'Moderator';
                $data['serverData'] = [
                    'error' => 'Unable to load event details. Please try again later.'
                ];
            }
        } else {
            error_log("ModeratorEventview::index - No event ID provided");
            $data['error'] = 'No event ID provided';
            $data['userRole'] = 'Moderator';
            $data['serverData'] = [
                'error' => 'No event ID provided'
            ];
        }
        
        $this->view('eventview', $data);
    }
    
    /**
     * API endpoint to get event details as JSON
     */
    public function getEvent($id = null) {
        header('Content-Type: application/json');
        
        // Check if user is logged in and is a moderator
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'moderator') {
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
            exit;
        }
        
        // Get event ID from parameter or GET request
        $eventId = $id;
        if (!$eventId && isset($_GET['id'])) {
            $eventId = $_GET['id'];
        }
        
        if (!$eventId) {
            echo json_encode([
                'success' => false,
                'error' => 'No event ID provided'
            ]);
            exit;
        }
        
        // Validate event ID is numeric
        if (!is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID format'
            ]);
            exit;
        }
        
        try {
            // Get moderator's university
            $currentUser = AuthService::getCurrentUser();
            $moderator = $this->moderatorModel->findById($currentUser['id']);
            
            if (!$moderator) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Moderator data not found'
                ]);
                exit;
            }
            
            // Get event from database
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event not found in database'
                ]);
                exit;
            }
            
            // Check if event was created by a publisher from moderator's university
            $publisherModel = new Publisher();
            $publisher = null;
            
            if ($event->created_by_type === 'publisher' && $event->created_by) {
                $publisherData = $publisherModel->where(['id' => $event->created_by]);
                if ($publisherData && count($publisherData) > 0) {
                    $publisher = $publisherData[0];
                }
            }
            
            // Check if publisher belongs to moderator's university
            if (!$publisher && $event->created_by_type === 'publisher') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event organizer not found'
                ]);
                exit;
            }
            
            if ($publisher && $publisher->university !== $moderator->university) {
                echo json_encode([
                    'success' => false,
                    'error' => 'You can only view events from publishers in your university'
                ]);
                exit;
            }
            
            // Add moderator's university to currentUser for filtering
            $currentUser['university'] = $moderator->university;
            
            // Get similar events from database (from same university)
            $similarEvents = $this->eventModel->getSimilarEvents(
                $event->id, 
                $event->category, 
                $event->university, 
                3,
                $currentUser
            );
            
            // Format event data for JSON response
            $eventData = $this->formatEventForResponse($event);
            
            // Format similar events
            $formattedSimilarEvents = [];
            foreach ($similarEvents as $similarEvent) {
                $formattedSimilarEvents[] = $this->formatEventForResponse($similarEvent);
            }
            
            echo json_encode([
                'success' => true,
                'event' => $eventData,
                'similarEvents' => $formattedSimilarEvents
            ]);
            
        } catch (Exception $e) {
            // Log error and return generic error message
            error_log("Database error in ModeratorEventview::getEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to retrieve event data. Please try again later.'
            ]);
        }
        
        exit;
    }
    
    /**
     * Helper method to format event data for API responses
     */
    private function formatEventForResponse($event) {
        $eventData = (array) $event;
        
        // Format date and time for frontend display
        if (isset($eventData['event_date'])) {
            $eventData['date'] = $eventData['event_date'];
        }
        
        if (isset($eventData['event_time'])) {
            $eventData['time'] = date('h:i A', strtotime($eventData['event_time']));
        }
        
        // Ensure JSON fields are properly decoded
        if (isset($eventData['requirements']) && is_string($eventData['requirements'])) {
            $eventData['requirements'] = json_decode($eventData['requirements'], true) ?: [];
        }
        
        if (isset($eventData['schedule']) && is_string($eventData['schedule'])) {
            $eventData['schedule'] = json_decode($eventData['schedule'], true) ?: [];
        }
        
        // Fetch organizer profile photo and phone if event is created by publisher
        if (isset($eventData['created_by_type']) && $eventData['created_by_type'] === 'publisher' && isset($eventData['created_by'])) {
            $publisherModel = new Publisher();
            
            // Get publisher basic info (includes phone and current organization name)
            $publisherInfo = $publisherModel->where(['id' => $eventData['created_by']]);
            if ($publisherInfo && count($publisherInfo) > 0) {
                $publisher = $publisherInfo[0];
                if (!empty($publisher->phone)) {
                    $eventData['organizer_phone'] = $publisher->phone;
                }
                // Update organizer name to current organization name from database
                if (!empty($publisher->society_name)) {
                    $eventData['organizer'] = $publisher->society_name;
                }
            }
            
            // Get publisher profile (includes logo)
            $publisherProfile = $publisherModel->getProfileData($eventData['created_by']);
            if ($publisherProfile) {
                if (!empty($publisherProfile->logo_url)) {
                    $eventData['organizer_photo'] = $publisherProfile->logo_url;
                }
                if (!empty($publisherProfile->headline)) {
                    $eventData['organizer_role'] = $publisherProfile->headline;
                } else {
                    $eventData['organizer_role'] = 'Event Organizer';
                }
            }
        }
        
        return $eventData;
    }
}
