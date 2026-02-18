<?php

class SponsorEvents extends Controller
{

    private $eventModel;

    public function __construct()
    {
        parent::__construct();
        // Initialize Event model
        $this->eventModel = new Event();
    }

    public function index($a = '', $b = '', $c = '')
    {
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
            $limit = 12; // Events per page (increased for sponsors)
            $offset = ($page - 1) * $limit;

            $filters['limit'] = $limit;
            $filters['offset'] = $offset;

            // Get current user
            $currentUser = AuthService::getCurrentUser();

            // Get paginated events for this page
            $eventsObj = $this->eventModel->getAllEvents($filters, $currentUser);

            // Convert events objects to arrays
            $events = [];
            if ($eventsObj && is_array($eventsObj)) {
                foreach ($eventsObj as $event) {
                    $events[] = is_object($event) ? (array) $event : $event;
                }
            }

            // Get events requesting sponsorships (separate section)
            $sponsorshipEvents = $this->getEventsWithSponsorships($currentUser);

            // Debug logging - ENHANCED
            error_log("=== SPONSOR EVENTS DEBUG ===");
            error_log("SponsorEvents::index - Sponsorship events count: " . count($sponsorshipEvents));
            error_log("SponsorEvents::index - Is array: " . (is_array($sponsorshipEvents) ? 'YES' : 'NO'));
            error_log("SponsorEvents::index - Is empty: " . (empty($sponsorshipEvents) ? 'YES' : 'NO'));
            if (!empty($sponsorshipEvents)) {
                error_log("SponsorEvents::index - First event ID: " . ($sponsorshipEvents[0]['id'] ?? 'NO ID'));
                error_log("SponsorEvents::index - First event title: " . ($sponsorshipEvents[0]['title'] ?? 'NO TITLE'));
            } else {
                error_log("SponsorEvents::index - No sponsorship events found!");
            }
            error_log("=========================");

            // Get total count for pagination (without limit)
            $totalEventsObj = $this->eventModel->getAllEvents([], $currentUser);
            $totalEvents = is_array($totalEventsObj) ? $totalEventsObj : [];
            $totalPages = ceil(count($totalEvents) / $limit);

            // Prepare data for view with server data for JavaScript
            $data = [
                'events' => $events,
                'sponsorshipEvents' => $sponsorshipEvents,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'apiEndpoint' => '/unipulse/public/sponsor/events/getEvents',
                'eventsPerPage' => $limit
            ];
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in SponsorEvents::index: " . $e->getMessage());
            $data = [
                'error' => 'Unable to load events. Please try again later.',
                'events' => [],
                'sponsorshipEvents' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => []
            ];
        }

        $data['userRole'] = 'Sponsor';

        $this->view('events', $data);
    }

    /**
     * View individual event details with sponsorship packages
     */
    public function event($eventId = null)
    {
        if (!$eventId) {
            header('Location: /unipulse/public/Sponsor/events');
            exit();
        }

        try {
            $currentUser = AuthService::getCurrentUser();

            // Get event details
            $eventObj = $this->eventModel->getEventById($eventId);

            if (!$eventObj) {
                $this->view('Sponsor/eventview', ['error' => 'Event not found']);
                return;
            }

            // Convert event object to array
            $event = is_object($eventObj) ? (array) $eventObj : $eventObj;

            // Get sponsorship packages if event accepts sponsorships
            $sponsorshipPackages = [];
            if (isset($event['accepts_sponsorships']) && $event['accepts_sponsorships'] == 1) {
                $sql = "SELECT * FROM event_sponsorship_packages 
                        WHERE event_id = ? 
                        AND is_active = 1 
                        AND (available_slots - filled_slots) > 0
                        ORDER BY 
                            CASE package_type
                                WHEN 'platinum' THEN 1
                                WHEN 'gold' THEN 2
                                WHEN 'silver' THEN 3
                                WHEN 'bronze' THEN 4
                                WHEN 'custom' THEN 5
                            END";
                $packagesObj = $this->eventModel->query($sql, [$eventId]);

                // Convert packages objects to arrays
                if ($packagesObj && is_array($packagesObj)) {
                    foreach ($packagesObj as $pkg) {
                        $sponsorshipPackages[] = (array) $pkg;
                    }
                }
            }

            // Get publisher info (if event has created_by field)
            $event['publisher'] = null;
            if (isset($event['created_by']) && $event['created_by'] && isset($event['created_by_type']) && $event['created_by_type'] === 'publisher') {
                $publisherSql = "SELECT u.*, pp.* FROM publishers u 
                                LEFT JOIN publisher_profiles pp ON u.id = pp.publisher_id 
                                WHERE u.id = ?";
                $publisherObj = $this->eventModel->query($publisherSql, [$event['created_by']]);

                // Convert publisher object to array
                if ($publisherObj && is_array($publisherObj) && count($publisherObj) > 0) {
                    $event['publisher'] = (array) $publisherObj[0];
                }
            }


            $data = [
                'event' => $event,
                'sponsorshipPackages' => $sponsorshipPackages,
                'isOwner' => false,
                'userRole' => 'Sponsor',
                'serverData' => [
                    'event' => $event,
                    'sponsorshipPackages' => $sponsorshipPackages,
                    'isOwner' => false
                ]
            ];

            $this->view('Sponsor/eventview', $data);
        } catch (Exception $e) {
            error_log("Error loading event details: " . $e->getMessage());
            $this->view('Sponsor/eventview', ['error' => 'Unable to load event details']);
        }
    }

    /**
     * API endpoint to get events as JSON
     */
    public function getEvents()
    {
        // Clean output buffer to prevent JSON corruption
        if (ob_get_length()) ob_clean();

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
            $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 12;
            $offset = ($page - 1) * $limit;

            $filters['limit'] = $limit;
            $filters['offset'] = $offset;

            // Get current user for visibility filtering
            $currentUser = AuthService::getCurrentUser();

            // Get events from database
            $eventsObj = $this->eventModel->getAllEvents($filters, $currentUser);

            // Convert and format events for JSON response
            $formattedEvents = [];
            if ($eventsObj && is_array($eventsObj) && count($eventsObj) > 0) {
                foreach ($eventsObj as $event) {
                    $formattedEvent = $this->formatEventForResponse($event);
                    $formattedEvents[] = $formattedEvent;
                }
            } elseif ($eventsObj === false) {
                throw new Exception("Database query failed");
            }

            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'pagination' => [
                    'currentPage' => $page,
                    'limit' => $limit,
                    'hasMore' => count($formattedEvents) == $limit
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
                    'limit' => 12,
                    'hasMore' => false
                ]
            ]);
        }

        exit;
    }

    /**
     * Helper method to format event data for API responses
     */
    private function formatEventForResponse($event)
    {
        $formattedEvent = (array) $event;

        // Decode JSON fields
        if (isset($formattedEvent['requirements']) && is_string($formattedEvent['requirements'])) {
            $formattedEvent['requirements'] = json_decode($formattedEvent['requirements'], true) ?: [];
        }
        if (isset($formattedEvent['schedule']) && is_string($formattedEvent['schedule'])) {
            $formattedEvent['schedule'] = json_decode($formattedEvent['schedule'], true) ?: [];
        }

        return $formattedEvent;
    }

    /**
     * Get events that are requesting sponsorships
     * Returns events where accepts_sponsorships = 1
     */
    private function getEventsWithSponsorships($currentUser = null)
    {
        try {
            error_log("getEventsWithSponsorships: Starting query");

            // Query for events accepting sponsorships (with or without packages)
            $sql = "SELECT e.id, e.title, e.description, e.category, e.event_date, e.event_time, 
                    e.event_end_time, e.location, e.university, e.university_name, 
                    e.organizer, e.image_url,
                    e.status, e.visibility, e.created_by, e.created_by_type, e.accepts_sponsorships,
                    e.requires_registration, e.is_deleted, e.max_participants, e.participants
                    FROM events e
                    WHERE e.accepts_sponsorships = 1 
                    AND e.is_deleted = 0
                    AND (e.visibility = 'public' OR e.visibility = 'all-universities' OR e.visibility = 'university-only')
                    AND e.status IN ('upcoming', 'ongoing')
                    AND e.event_date >= CURDATE()
                    ORDER BY e.event_date ASC
                    LIMIT 12";

            $events = $this->eventModel->query($sql);

            error_log("getEventsWithSponsorships: Query returned " . (is_array($events) ? count($events) : 0) . " events");

            // Convert objects to arrays and add package info if available
            if ($events && is_array($events)) {
                $eventsArray = [];
                foreach ($events as $event) {
                    $eventArray = (array) $event;
                    error_log("Event {$event->id} image_url: " . ($event->image_url ?? 'NULL'));

                    // Get sponsorship packages for this event (if any)
                    $packagesSql = "SELECT COUNT(*) as package_count, 
                                   SUM(available_slots - filled_slots) as total_slots_available
                                   FROM event_sponsorship_packages 
                                   WHERE event_id = ? 
                                   AND is_active = 1 
                                   AND (available_slots - filled_slots) > 0";
                    $packageInfo = $this->eventModel->query($packagesSql, [$event->id]);

                    if ($packageInfo && count($packageInfo) > 0) {
                        $eventArray['package_count'] = $packageInfo[0]->package_count ?? 0;
                        $eventArray['total_slots_available'] = $packageInfo[0]->total_slots_available ?? 0;
                    } else {
                        $eventArray['package_count'] = 0;
                        $eventArray['total_slots_available'] = 0;
                    }

                    $eventsArray[] = $eventArray;
                }
                return $eventsArray;
            }

            return [];
        } catch (Exception $e) {
            error_log("Error fetching sponsorship events: " . $e->getMessage());
            return [];
        }
    }
}
