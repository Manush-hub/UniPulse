<?php

class UserEventview extends Controller
{

    private $eventModel;
    private $registrationModel;

    public function __construct()
    {
        // Initialize models
        $this->eventModel = new Event();
        $this->registrationModel = new EventRegistration();
    }

    public function index($id = null)
    {

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
                            3
                        );

                        // Check if user is already registered (if user is logged in)
                        $isRegistered = false;
                        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
                            $isRegistered = $this->registrationModel->isUserRegistered(
                                $eventId,
                                $_SESSION['user_id'],
                                $_SESSION['user_type']
                            );
                        }

                        // Pass server data to view for JavaScript
                        $data = [
                            'event' => $event,
                            'similarEvents' => $similarEvents,
                            'serverData' => [
                                'event' => $event,
                                'similarEvents' => $similarEvents,
                                'isRegistered' => $isRegistered,
                                'apiEndpoint' => '/unipulse/public/user/eventview/getEvent',
                                'joinEndpoint' => '/unipulse/public/user/eventview/joinEvent'
                            ]
                        ];
                    } else {
                        $data['error'] = 'Event not found in database';
                    }
                }
            } catch (Exception $e) {
                // Log error and show user-friendly message
                error_log("Database error in UserEventview::index: " . $e->getMessage());
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
    public function getEvent($id = null)
    {
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
                3
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
            error_log("Database error in UserEventview::getEvent: " . $e->getMessage());
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
    public function joinEvent($id = null)
    {
        header('Content-Type: application/json');

        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in to join an event'
            ]);
            exit;
        }

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

        // Validate event ID is numeric
        if (!is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID format'
            ]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            // Check if user is already registered
            if ($this->registrationModel->isUserRegistered($eventId, $userId, $userType)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'You have already registered for this event',
                    'alreadyRegistered' => true
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
            $notes = $_POST['notes'] ?? '';
            $registrationData = [
                'event_id' => $eventId,
                'user_id' => $userId,
                'user_type' => $userType,
                'registration_type' => 'free',
                'notes' => $notes
            ];

            if ($this->registrationModel->registerUser($registrationData)) {
                // Increment current participants count
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
                        'availableSpots' => $availableSpots,
                        'isRegistered' => true
                    ]);
                } else {
                    // If incrementing fails, rollback the registration
                    $this->registrationModel->cancelRegistration($eventId, $userId, $userType);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to join event. Event may be full or an error occurred.'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to create registration. You may have already registered.'
                ]);
            }
        } catch (Exception $e) {
            // Log error with full details
            error_log("Database error in UserEventview::joinEvent: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            // Return error message for debugging (remove in production)
            echo json_encode([
                'success' => false,
                'error' => 'Unable to join event. Please try again later.',
                'debug' => $e->getMessage() // Remove this in production
            ]);
        }

        exit;
    }

    /**
     * Helper method to format event data for API responses
     */
    private function formatEventForResponse($event)
    {
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

        return $eventData;
    }
}
