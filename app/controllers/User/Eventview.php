<?php

class UserEventview extends Controller
{

    private $eventModel;
    private $registrationModel;
    private $freeRegistrationModel;
    private $volunteerRegistrationModel;

    public function __construct()
    {
        // Initialize models
        $this->eventModel = new Event();
        $this->registrationModel = new EventRegistration();
        $this->freeRegistrationModel = new FreeEventRegistration();
        $this->volunteerRegistrationModel = new VolunteerRegistration();
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
                        // Get current user for visibility filtering
                        $currentUser = AuthService::getCurrentUser();

                        // Get similar events from database
                        $similarEvents = $this->eventModel->getSimilarEvents(
                            $event->id,
                            $event->category,
                            $event->university,
                            3,
                            $currentUser
                        );

                        // Check if user is already registered (if user is logged in)
                        $isRegistered = false;
                        $isVolunteerApplied = false;
                        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
                            $isRegistered = $this->registrationModel->isUserRegistered(
                                $eventId,
                                $_SESSION['user_id'],
                                $_SESSION['user_type']
                            );

                            $isVolunteerApplied = $this->volunteerRegistrationModel->isUserRegistered(
                                $eventId,
                                $_SESSION['user_id'],
                                $_SESSION['user_type']
                            );
                        }

                        // Pass server data to view for JavaScript
                        $data = [
                            'event' => $event,
                            'similarEvents' => $similarEvents,
                            'userRole' => 'User',
                            'error' => null,
                            'serverData' => [
                                'event' => $event,
                                'similarEvents' => $similarEvents,
                                'isRegistered' => $isRegistered,
                                'isVolunteerApplied' => $isVolunteerApplied,
                                'apiEndpoint' => '/unipulse/public/user/eventview/getEvent',
                                'joinEndpoint' => '/unipulse/public/user/eventview/joinEvent',
                                'volunteerApplyEndpoint' => '/unipulse/public/user/eventview/applyVolunteer'
                            ]
                        ];
                    } else {
                        $data = [
                            'error' => 'Event not found in database',
                            'userRole' => 'User',
                            'serverData' => [
                                'error' => 'Event not found in database'
                            ]
                        ];
                    }
                }
            } catch (Exception $e) {
                // Log error and show user-friendly message
                error_log("Database error in UserEventview::index: " . $e->getMessage());
                $data = [
                    'error' => 'Unable to load event details. Please try again later.',
                    'userRole' => 'User',
                    'serverData' => [
                        'error' => 'Unable to load event details. Please try again later.'
                    ]
                ];
            }
        } else {
            $data = [
                'error' => 'No event ID provided',
                'userRole' => 'User',
                'serverData' => [
                    'error' => 'No event ID provided'
                ]
            ];
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

            // Get current user for visibility filtering
            $currentUser = AuthService::getCurrentUser();

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

            $isVolunteerApplied = false;
            if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
                $isVolunteerApplied = $this->volunteerRegistrationModel->isUserRegistered(
                    $eventId,
                    $_SESSION['user_id'],
                    $_SESSION['user_type']
                );
            }

            // Format similar events
            $formattedSimilarEvents = [];
            foreach ($similarEvents as $similarEvent) {
                $formattedSimilarEvents[] = $this->formatEventForResponse($similarEvent);
            }

            echo json_encode([
                'success' => true,
                'event' => $eventData,
                'similarEvents' => $formattedSimilarEvents,
                'isVolunteerApplied' => $isVolunteerApplied
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

            // Check if event has available spots (only when both fields exist and max is set)
            $hasMaxParticipants = isset($event->max_participants) && $event->max_participants !== null;
            if ($hasMaxParticipants) {
                $currentParticipants = isset($event->current_participants) ? (int)$event->current_participants : 0;
                if ($currentParticipants >= (int)$event->max_participants) {
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
                $ticketType = strtolower((string)($event->ticket_type ?? 'free-all'));

                if ($ticketType === 'free-all') {
                    try {
                        $currentUser = AuthService::getCurrentUser();
                        $freeRegistrationData = [
                            'event_id' => (int)$eventId,
                            'publisher_id' => (isset($event->created_by_type) && $event->created_by_type === 'publisher' && isset($event->created_by)) ? (int)$event->created_by : null,
                            'event_title_snapshot' => (string)($event->title ?? ''),
                            'publisher_name_snapshot' => (string)($event->organizer_name ?? $event->organizer ?? ''),
                            'registered_user_id' => (int)$userId,
                            'registered_user_type' => (string)$userType,
                            'registered_user_name_snapshot' => (string)($currentUser['name'] ?? ($_SESSION['user_name'] ?? '')),
                            'registered_user_email_snapshot' => (string)($currentUser['email'] ?? ($_SESSION['user_email'] ?? '')),
                            'registration_source' => 'web',
                            'status' => 'registered',
                            'registration_notes' => $notes,
                            'registered_at' => date('Y-m-d H:i:s')
                        ];

                        $this->freeRegistrationModel->registerUser($freeRegistrationData);
                    } catch (Throwable $freeRegError) {
                        error_log("Free registration mirror warning in UserEventview::joinEvent: " . $freeRegError->getMessage());
                    }
                }

                // Registration is the primary action. Participant counter updates are best effort.
                $updatedEvent = $event;

                try {
                    $incrementResult = $this->eventModel->incrementParticipants($eventId);
                    if ($incrementResult) {
                        $latestEvent = $this->eventModel->getEventById($eventId);
                        if ($latestEvent) {
                            $updatedEvent = $latestEvent;
                        }
                    }
                } catch (Throwable $counterException) {
                    error_log("Participant count update warning in UserEventview::joinEvent: " . $counterException->getMessage());
                }

                // Calculate available spots (null if unlimited or unavailable)
                $availableSpots = null;
                if (isset($updatedEvent->max_participants) && $updatedEvent->max_participants !== null) {
                    $updatedCurrentParticipants = isset($updatedEvent->current_participants) ? (int)$updatedEvent->current_participants : 0;
                    $availableSpots = (int)$updatedEvent->max_participants - $updatedCurrentParticipants;
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully joined the event',
                    'participants' => $updatedEvent->participants ?? 0, // Legacy
                    'current_participants' => $updatedEvent->current_participants ?? 0,
                    'max_participants' => $updatedEvent->max_participants ?? null,
                    'availableSpots' => $availableSpots,
                    'isRegistered' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to create registration. You may have already registered.'
                ]);
            }
        } catch (Throwable $e) {
            // Log error and return generic error message
            error_log("Database error in UserEventview::joinEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to join event. Please try again later.'
            ]);
        }

        exit;
    }

    /**
     * Quick volunteer apply endpoint
     */
    public function applyVolunteer($id = null)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in to apply as a volunteer'
            ]);
            exit;
        }

        $eventId = $id;
        if (!$eventId && isset($_POST['id'])) {
            $eventId = $_POST['id'];
        }

        if (!$eventId || !is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID'
            ]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        try {
            $event = $this->eventModel->getEventById($eventId);

            if (!$event) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Event not found'
                ]);
                exit;
            }

            if (!$event->needs_volunteers) {
                echo json_encode([
                    'success' => false,
                    'error' => 'This event is not accepting volunteers'
                ]);
                exit;
            }

            if (!is_null($event->volunteers_needed) && (int)$event->volunteers_needed <= 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'No volunteer positions are currently available'
                ]);
                exit;
            }

            if ($this->volunteerRegistrationModel->isUserRegistered($eventId, $userId, $userType)) {
                echo json_encode([
                    'success' => false,
                    'alreadyRegistered' => true,
                    'error' => 'You have already applied as a volunteer for this event',
                    'volunteers_needed' => $event->volunteers_needed
                ]);
                exit;
            }

            $volunteerData = [
                'user_id' => $userId,
                'user_type' => $userType,
                'event_id' => $eventId,
                'volunteer_position' => 'General Volunteer',
                'availability' => 'Flexible',
                'experience' => 'Submitted via quick apply',
                'motivation' => 'Interested in supporting this event',
                'skills' => 'N/A',
                'have_transportation' => 0,
                'commitment_understanding' => 1,
                'receive_updates' => 1,
                'terms_accepted' => 1,
                'status' => 'pending'
            ];

            if (!$this->volunteerRegistrationModel->insert($volunteerData)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to submit volunteer application'
                ]);
                exit;
            }

            if (!is_null($event->volunteers_needed) && (int)$event->volunteers_needed > 0) {
                $remainingVolunteers = max(0, (int)$event->volunteers_needed - 1);
                $this->eventModel->update($eventId, ['volunteers_needed' => $remainingVolunteers]);
            }

            $updatedEvent = $this->eventModel->getEventById($eventId);

            echo json_encode([
                'success' => true,
                'message' => 'Volunteer application submitted successfully',
                'volunteers_needed' => $updatedEvent ? $updatedEvent->volunteers_needed : $event->volunteers_needed
            ]);
        } catch (Exception $e) {
            error_log("Database error in UserEventview::applyVolunteer: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to apply as volunteer. Please try again later.'
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

        // Add publisher profile headline for organizer role display
        if (isset($eventData['created_by_type']) && $eventData['created_by_type'] === 'publisher' && isset($eventData['created_by'])) {
            $publisherModel = new Publisher();
            $publisherProfile = $publisherModel->getProfileData($eventData['created_by']);
            if ($publisherProfile && !empty($publisherProfile->headline)) {
                $eventData['organizer_role'] = $publisherProfile->headline;
            } else {
                $eventData['organizer_role'] = 'Event Organizer';
            }
        }

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
