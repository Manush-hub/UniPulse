<?php

class AdminEventview extends Controller {

    private $eventModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index($id = null) {
        // Check if user is logged in and is an admin
        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            header('Location: /unipulse/public/signin');
            exit();
        }

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
                    error_log("AdminEventview::index - Invalid event ID: " . $eventId);
                    $data['error'] = 'Invalid event ID';
                    $data['userRole'] = 'Admin';
                    $data['serverData'] = ['error' => 'Invalid event ID'];
                } else {
                    // Get event from database (admin sees all events regardless of visibility)
                    $event = $this->eventModel->getEventById($eventId);

                    if ($event) {
                        // Get similar events
                        $currentUser = AuthService::getCurrentUser();
                        $similarEvents = $this->eventModel->getSimilarEvents(
                            $event->id,
                            $event->category,
                            $event->university,
                            3,
                            $currentUser
                        );

                        $data = [
                            'event'        => $event,
                            'similarEvents' => $similarEvents,
                            'userRole'     => 'Admin',
                            'error'        => null,
                            'serverData'   => [
                                'event'        => $event,
                                'similarEvents' => $similarEvents,
                                'apiEndpoint'  => '/unipulse/public/admin/eventview/getEvent',
                                'hideEndpoint' => '/unipulse/public/admin/eventview/hideEvent',
                                'showEndpoint' => '/unipulse/public/admin/eventview/showEvent',
                            ]
                        ];
                    } else {
                        $data = [
                            'error'    => 'Event not found',
                            'userRole' => 'Admin',
                            'serverData' => ['error' => 'Event not found']
                        ];
                    }
                }
            } catch (Exception $e) {
                error_log("AdminEventview::index error: " . $e->getMessage());
                $data = [
                    'error'    => 'Unable to load event details. Please try again later.',
                    'userRole' => 'Admin',
                    'serverData' => ['error' => 'Unable to load event details.']
                ];
            }
        } else {
            $data = [
                'error'    => 'No event ID provided',
                'userRole' => 'Admin',
                'serverData' => ['error' => 'No event ID provided']
            ];
        }

        $this->view('eventview', $data);
    }

    /**
     * API endpoint – returns event details as JSON
     */
    public function getEvent($id = null) {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $eventId = $id ?: ($_GET['id'] ?? null);

        if (!$eventId) {
            echo json_encode(['success' => false, 'error' => 'No event ID provided']);
            exit;
        }

        if (!is_numeric($eventId)) {
            echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
            exit;
        }

        try {
            $event = $this->eventModel->getEventById($eventId);

            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Event not found']);
                exit;
            }

            $currentUser = AuthService::getCurrentUser();
            $similarEvents = $this->eventModel->getSimilarEvents(
                $event->id,
                $event->category,
                $event->university,
                3,
                $currentUser
            );

            $formattedSimilar = array_map([$this, 'formatEventForResponse'], $similarEvents);

            echo json_encode([
                'success'       => true,
                'event'         => $this->formatEventForResponse($event),
                'similarEvents' => $formattedSimilar
            ]);
        } catch (Exception $e) {
            error_log("AdminEventview::getEvent error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Unable to retrieve event data.']);
        }

        exit;
    }

    /**
     * API endpoint – hide an event (POST)
     */
    public function hideEvent($id = null) {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $input   = json_decode(file_get_contents('php://input'), true) ?? [];
        $eventId = $id ?: ($input['id'] ?? $_POST['id'] ?? null);

        if (!$eventId || !is_numeric($eventId)) {
            echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
            exit;
        }

        $reason = trim($input['reason'] ?? $_POST['reason'] ?? '');
        $currentUser = AuthService::getCurrentUser();

        try {
            $result = $this->eventModel->hideEvent($eventId, $currentUser['id'] ?? null, $reason);

            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'Event hidden successfully.']);
            } else {
                echo json_encode([
                    'success' => false,
                    'error'   => $result['errors']['general'] ?? 'Failed to hide event.'
                ]);
            }
        } catch (Exception $e) {
            error_log("AdminEventview::hideEvent error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'An error occurred. Please try again.']);
        }

        exit;
    }

    /**
     * API endpoint – show (unhide) an event (POST)
     */
    public function showEvent($id = null) {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn() || AuthService::getCurrentUser()['type'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $input   = json_decode(file_get_contents('php://input'), true) ?? [];
        $eventId = $id ?: ($input['id'] ?? $_POST['id'] ?? null);

        if (!$eventId || !is_numeric($eventId)) {
            echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
            exit;
        }

        try {
            $result = $this->eventModel->showEvent($eventId);

            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'Event is now visible.']);
            } else {
                echo json_encode([
                    'success' => false,
                    'error'   => $result['errors']['general'] ?? 'Failed to show event.'
                ]);
            }
        } catch (Exception $e) {
            error_log("AdminEventview::showEvent error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'An error occurred. Please try again.']);
        }

        exit;
    }

    /**
     * Format event data for JSON responses
     */
    private function formatEventForResponse($event) {
        $eventData = (array) $event;

        if (isset($eventData['event_date'])) {
            $eventData['date'] = $eventData['event_date'];
        }

        if (isset($eventData['event_time'])) {
            $eventData['time'] = date('h:i A', strtotime($eventData['event_time']));
        }

        if (isset($eventData['requirements']) && is_string($eventData['requirements'])) {
            $eventData['requirements'] = json_decode($eventData['requirements'], true) ?: [];
        }

        if (isset($eventData['schedule']) && is_string($eventData['schedule'])) {
            $eventData['schedule'] = json_decode($eventData['schedule'], true) ?: [];
        }

        // Fetch organizer profile details when event is created by a publisher
        if (!empty($eventData['created_by_type']) && $eventData['created_by_type'] === 'publisher' && !empty($eventData['created_by'])) {
            $publisherModel = new Publisher();

            $publisherInfo = $publisherModel->where(['id' => $eventData['created_by']]);
            if ($publisherInfo && count($publisherInfo) > 0) {
                $publisher = $publisherInfo[0];
                if (!empty($publisher->phone)) {
                    $eventData['organizer_phone'] = $publisher->phone;
                }
                if (!empty($publisher->society_name)) {
                    $eventData['organizer'] = $publisher->society_name;
                }
            }

            $publisherProfile = $publisherModel->getProfileData($eventData['created_by']);
            if ($publisherProfile) {
                if (!empty($publisherProfile->logo_url)) {
                    $eventData['organizer_photo'] = $publisherProfile->logo_url;
                }
                $eventData['organizer_role'] = !empty($publisherProfile->headline)
                    ? $publisherProfile->headline
                    : 'Event Organizer';
            }
        }

        return $eventData;
    }
}
