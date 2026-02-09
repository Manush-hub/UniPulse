<?php

class PublisherEventview extends Controller {
    
    private $eventModel;
    private $registrationModel;
    
    public function __construct() {
        // Initialize Event model
        $this->eventModel = new Event();
        $this->registrationModel = new EventRegistration();
    }
    
    public function index($id = null) {
        
        // Get event ID from URL parameter or GET request
        $eventId = $id;
        if (!$eventId && isset($_GET['id'])) {
            $eventId = $_GET['id'];
        }
        
        $data = [];
        
        if ($eventId) {
            try {
                // Validate event ID is numeric
                if (!is_numeric($eventId)) {
                    $data['error'] = 'Invalid event ID';
                } else {
                    // Get specific event from database
                    $event = $this->eventModel->getEventById($eventId);
                    
                    if ($event) {
                        // Get similar events from database
                        $similarEvents = $this->eventModel->getSimilarEvents(
                            $event->id, 
                            $event->category, 
                            $event->university, 
                            3,
                            $currentUser
                        );
                        
                        // Check if current publisher owns this event
                        $currentUser = AuthService::getCurrentUser();
                        $currentPublisherId = ($currentUser && $currentUser['type'] === 'publisher') ? $currentUser['id'] : null;
                        $isOwner = ($currentPublisherId && $event->created_by == $currentPublisherId && $event->created_by_type == 'publisher');
                        
                        // Check if publisher is already registered
                        $isRegistered = false;
                        if ($currentPublisherId) {
                            $isRegistered = $this->registrationModel->isUserRegistered($eventId, $currentPublisherId, 'publisher');
                        }
                        
                        // Pass server data to view for JavaScript (use raw event object, not formatted)
                        $data = [
                            'event' => $event,
                            'similarEvents' => $similarEvents,
                            'isOwner' => $isOwner,
                            'serverData' => [
                                'event' => $event,
                                'similarEvents' => $similarEvents,
                                'isOwner' => $isOwner,
                                'isRegistered' => $isRegistered,
                                'apiEndpoint' => '/unipulse/public/publisher/eventview/getEvent',
                                'joinEndpoint' => '/unipulse/public/publisher/eventview/joinEvent'
                            ]
                        ];
                    } else {
                        $data['error'] = 'Event not found in database';
                    }
                }
            } catch (Exception $e) {
                // Log error and show user-friendly message
                error_log("Database error in PublisherEventview::index: " . $e->getMessage());
                $data['error'] = 'Unable to load event details. Please try again later.';
            }
        } else {
            $data['error'] = 'No event ID provided';
        }
        
        $this->view('eventview', $data);
    }
    
    /**
     * API endpoint to get event details as JSON
     */
    public function getEvent($id = null) {
        header('Content-Type: application/json');
        
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
            // Get event from database
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event not found in database'
                ]);
                exit;
            }
            
            // Get similar events from database
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
            error_log("Database error in PublisherEventview::getEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to retrieve event data. Please try again later.'
            ]);
        }
        
        exit;
    }
    
    /**
     * Join event endpoint
     */
    public function joinEvent($id = null) {
        header('Content-Type: application/json');
        
        // Get event ID from parameter or POST request
        $eventId = $id;
        if (!$eventId && isset($_POST['id'])) {
            $eventId = $_POST['id'];
        }
        
        if (!$eventId) {
            echo json_encode([
                'success' => false,
                'error' => 'No event ID provided'
            ]);
            exit;
        }
        
        // Check if publisher is logged in
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'publisher') {
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in as a publisher to join events'
            ]);
            exit;
        }
        
        $publisherId = $currentUser['id'];
        
        // Validate event ID is numeric
        if (!is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID format'
            ]);
            exit;
        }
        
        try {
            // Check if publisher is already registered
            if ($this->registrationModel->isUserRegistered($eventId, $publisherId, 'publisher')) {
                echo json_encode([
                    'success' => false,
                    'alreadyRegistered' => true,
                    'error' => 'You have already registered for this event'
                ]);
                exit;
            }
            
            // Check if event exists
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event not found in database'
                ]);
                exit;
            }
            
            // Check if event has available spots (only if max_participants is set)
            if ($event->max_participants !== null) {
                if ($event->current_participants >= $event->max_participants) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Event is full'
                    ]);
                    exit;
                }
            }
            
            if ($event->status === 'completed' || $event->status === 'cancelled') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Cannot join this event'
                ]);
                exit;
            }
            
            // Create registration record
            $registrationData = [
                'event_id' => $eventId,
                'user_id' => $publisherId,
                'user_type' => 'publisher',
                'notes' => $_POST['notes'] ?? '',
                'status' => 'registered'
            ];
            
            if (!$this->registrationModel->registerUser($registrationData)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to create registration record'
                ]);
                exit;
            }
            
            // Join the event by incrementing current participants
            if ($this->eventModel->incrementParticipants($eventId)) {
                // Get updated event data from database
                $updatedEvent = $this->eventModel->getEventById($eventId);
                
                // Calculate available spots (null if unlimited)
                $availableSpots = null;
                if ($updatedEvent->max_participants !== null) {
                    $availableSpots = $updatedEvent->max_participants - $updatedEvent->current_participants;
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully joined the event',
                    'participants' => $updatedEvent->participants, // Legacy
                    'current_participants' => $updatedEvent->current_participants,
                    'max_participants' => $updatedEvent->max_participants,
                    'availableSpots' => $availableSpots
                ]);
            } else {
                // Rollback registration if increment fails
                $this->registrationModel->cancelRegistration($eventId, $publisherId, 'publisher');
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to join event. Event may be full or an error occurred.'
                ]);
            }
            
        } catch (Exception $e) {
            // Log error and return generic error message
            error_log("Database error in PublisherEventview::joinEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to join event. Please try again later.'
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
