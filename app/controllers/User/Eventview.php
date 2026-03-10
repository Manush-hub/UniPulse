<?php

class UserEventview extends Controller
{

    private $eventModel;
    private $registrationModel;
    private $freeRegistrationModel;
    private $volunteerRegistrationModel;
    private $donationModel;

    public function __construct()
    {
        // Initialize models
        $this->eventModel = new Event();
        $this->registrationModel = new EventRegistration();
        $this->freeRegistrationModel = new FreeEventRegistration();
        $this->volunteerRegistrationModel = new VolunteerRegistration();
        $this->donationModel = new Donation();
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
                        $volunteerApplication = null;
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

                            if ($isVolunteerApplied) {
                                $registration = $this->volunteerRegistrationModel->getRegistration(
                                    $eventId,
                                    $_SESSION['user_id'],
                                    $_SESSION['user_type']
                                );
                                $volunteerApplication = $this->formatVolunteerApplicationForResponse($registration);
                            }
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
                                'volunteerApplication' => $volunteerApplication,
                                'apiEndpoint' => '/unipulse/public/user/eventview/getEvent',
                                'joinEndpoint' => '/unipulse/public/user/eventview/joinEvent',
                                'volunteerApplyEndpoint' => '/unipulse/public/user/eventview/applyVolunteer',
                                'donationSubmitEndpoint' => '/unipulse/public/user/eventview/submitDonation'
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
            $volunteerApplication = null;
            if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
                $isVolunteerApplied = $this->volunteerRegistrationModel->isUserRegistered(
                    $eventId,
                    $_SESSION['user_id'],
                    $_SESSION['user_type']
                );

                if ($isVolunteerApplied) {
                    $registration = $this->volunteerRegistrationModel->getRegistration(
                        $eventId,
                        $_SESSION['user_id'],
                        $_SESSION['user_type']
                    );
                    $volunteerApplication = $this->formatVolunteerApplicationForResponse($registration);
                }
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
                'isVolunteerApplied' => $isVolunteerApplied,
                'volunteerApplication' => $volunteerApplication
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

        $volunteerPosition = trim((string)($_POST['volunteer_position'] ?? 'General Volunteer'));
        $availability = trim((string)($_POST['availability'] ?? 'Flexible'));
        $experience = trim((string)($_POST['experience'] ?? ''));
        $skills = trim((string)($_POST['skills'] ?? ''));
        $motivation = trim((string)($_POST['motivation'] ?? ''));
        $haveTransportation = isset($_POST['have_transportation']) && (string)$_POST['have_transportation'] === '1' ? 1 : 0;
        $commitmentUnderstanding = isset($_POST['commitment_understanding']) && (string)$_POST['commitment_understanding'] === '0' ? 0 : 1;
        $receiveUpdates = isset($_POST['receive_updates']) && (string)$_POST['receive_updates'] === '0' ? 0 : 1;

        if ($volunteerPosition === '') {
            $volunteerPosition = 'General Volunteer';
        }

        if ($availability === '') {
            $availability = 'Flexible';
        }

        // Backward compatibility for quick-apply clients without full form payload.
        if ($experience === '') {
            $experience = 'Submitted via quick apply';
        }

        if ($motivation === '') {
            $motivation = 'Interested in supporting this event';
        }

        if ($skills === '') {
            $skills = 'Not specified';
        }

        $truncate = function ($value, $length) {
            return function_exists('mb_substr')
                ? mb_substr($value, 0, $length)
                : substr($value, 0, $length);
        };

        $volunteerPosition = $truncate($volunteerPosition, 255);
        $availability = $truncate($availability, 100);
        $experience = $truncate($experience, 2000);
        $skills = $truncate($skills, 2000);
        $motivation = $truncate($motivation, 2000);

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
                $existing = $this->volunteerRegistrationModel->getRegistration($eventId, $userId, $userType);
                echo json_encode([
                    'success' => false,
                    'alreadyRegistered' => true,
                    'error' => 'You have already applied as a volunteer for this event',
                    'volunteers_needed' => $event->volunteers_needed,
                    'volunteerApplication' => $this->formatVolunteerApplicationForResponse($existing)
                ]);
                exit;
            }

            $volunteerData = [
                'user_id' => $userId,
                'user_type' => $userType,
                'event_id' => $eventId,
                'volunteer_position' => $volunteerPosition,
                'availability' => $availability,
                'experience' => $experience,
                'skills' => $skills,
                'motivation' => $motivation,
                'have_transportation' => $haveTransportation,
                'commitment_understanding' => $commitmentUnderstanding,
                'receive_updates' => $receiveUpdates,
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

            $savedApplication = $this->volunteerRegistrationModel->getRegistration($eventId, $userId, $userType);

            echo json_encode([
                'success' => true,
                'message' => 'Volunteer application submitted successfully',
                'volunteers_needed' => $event->volunteers_needed,
                'volunteerApplication' => $this->formatVolunteerApplicationForResponse($savedApplication)
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
     * Submit donation with payment slip
     */
    public function submitDonation($id = null)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in to donate'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'error' => 'Method not allowed'
            ]);
            exit;
        }

        $eventId = $id ?: ($_POST['event_id'] ?? null);
        $amount = $_POST['amount'] ?? null;

        if (!$eventId || !is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID'
            ]);
            exit;
        }

        if (!is_numeric($amount) || (float)$amount < 100) {
            echo json_encode([
                'success' => false,
                'error' => 'Minimum donation amount is LKR 100'
            ]);
            exit;
        }

        try {
            $event = $this->eventModel->getEventById($eventId);

            if (!$event) {
                throw new Exception('Event not found');
            }

            if (empty($event->accepts_donations) || (int)$event->accepts_donations !== 1) {
                throw new Exception('This event is not accepting donations');
            }

            if (!isset($_FILES['payment_slip']) || $_FILES['payment_slip']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Payment slip is required');
            }

            $file = $_FILES['payment_slip'];
            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                throw new Exception('Payment slip must be less than 5MB');
            }

            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimes)) {
                throw new Exception('Invalid file type. Only JPG, PNG, and PDF are allowed');
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/unipulse/public/uploads/donation_slips/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'donation_slip_' . (int)$_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            $dbPath = '/unipulse/public/uploads/donation_slips/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload payment slip');
            }

            $currentUser = AuthService::getCurrentUser();

            $donationData = [
                'user_id' => (int)$_SESSION['user_id'],
                'user_type' => (string)$_SESSION['user_type'],
                'event_id' => (int)$eventId,
                'amount' => round((float)$amount, 2),
                'currency' => 'LKR',
                'payment_method' => 'bank_transfer',
                'payment_id' => $dbPath,
                'transaction_reference' => !empty($_POST['transaction_reference']) ? trim($_POST['transaction_reference']) : null,
                'status' => 'pending',
                'donor_name' => $currentUser['name'] ?? $currentUser['full_name'] ?? null,
                'donor_email' => $currentUser['email'] ?? null,
                'donor_phone' => $currentUser['phone'] ?? null,
                'is_anonymous' => 0,
                'message' => !empty($_POST['message']) ? trim($_POST['message']) : null,
                'receipt_sent' => 0
            ];

            $insertId = $this->donationModel->createDonation($donationData);

            if (!$insertId) {
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                throw new Exception('Failed to save donation');
            }

            if (($event->created_by_type ?? '') === 'publisher' && !empty($event->created_by)) {
                try {
                    $activityModel = new Activity();
                    $donorName = $currentUser['name'] ?? $currentUser['full_name'] ?? 'A user';
                    $activityModel->logActivity(
                        (int)$event->created_by,
                        'publisher',
                        'event_registration',
                        'New Donation Submitted',
                        $donorName . ' submitted a donation for "' . ($event->title ?? 'your event') . '".',
                        'bell',
                        (int)$eventId,
                        (string)($event->title ?? ''),
                        [
                            'notification_category' => 'donation_submitted',
                            'donation_id' => (int)$insertId,
                            'amount' => round((float)$amount, 2),
                            'currency' => 'LKR'
                        ]
                    );
                } catch (Throwable $activityError) {
                    error_log('UserEventview::submitDonation activity log warning: ' . $activityError->getMessage());
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Donation submitted successfully. It will be reviewed by the publisher.'
            ]);
        } catch (Exception $e) {
            error_log('UserEventview::submitDonation error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
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

    /**
     * Helper method to format volunteer application data for API responses
     */
    private function formatVolunteerApplicationForResponse($registration)
    {
        if (!$registration) {
            return null;
        }

        $row = (array) $registration;

        return [
            'id' => isset($row['id']) ? (int)$row['id'] : null,
            'status' => $row['status'] ?? 'pending',
            'volunteer_position' => $row['volunteer_position'] ?? null,
            'availability' => $row['availability'] ?? null,
            'experience' => $row['experience'] ?? null,
            'skills' => $row['skills'] ?? null,
            'motivation' => $row['motivation'] ?? null,
            'have_transportation' => isset($row['have_transportation']) ? (int)$row['have_transportation'] : 0,
            'commitment_understanding' => isset($row['commitment_understanding']) ? (int)$row['commitment_understanding'] : 0,
            'receive_updates' => isset($row['receive_updates']) ? (int)$row['receive_updates'] : 0,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null
        ];
    }
}
