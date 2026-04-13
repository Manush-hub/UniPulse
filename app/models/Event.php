<?php

class Event
{

    use Model;

    protected $table = 'events';
    protected $allowedColumns = [
        'title',
        'description',
        'category',
        'university',
        'university_name',
        'visibility',
        'status',
        'event_date',
        'event_time',
        'event_end_time',
        'location',
        'location_type',
        'venue_name',
        'street_address',
        'city',
        'district_province',
        'faculty_department',
        'organizer',
        'organizer_email',
        'created_by',
        'created_by_type',
        'participants',
        'current_participants',
        'max_participants',
        'requirements',
        'schedule',
        'ticket_type',
        'requires_registration',
        'registration_limit',
        'registration_start_date',
        'registration_start_time',
        'registration_end_date',
        'registration_end_time',
        'ticket_types',
        'custom_fields',
        'needs_volunteers',
        'volunteer_sources',
        'volunteers_needed',
        'volunteer_positions',
        'accepts_donations',
        'donation_bank_name',
        'donation_account_name',
        'donation_account_number',
        'donation_branch',
        'donation_swift_code',
        'donation_instructions',
        'accepts_sponsorships',
        'sponsorship_proposal',
        'sponsorship_bank_name',
        'sponsorship_account_name',
        'sponsorship_account_number',
        'sponsorship_branch',
        'sponsorship_swift_code',
        'sponsorship_instructions',
        'image_url',
        'cover_image'
    ];

    /**
     * Get all events with optional filters
     */
    public function getAllEvents($filters = [], $currentUser = null)
    {
        $includeDeleted = !empty($filters['include_deleted']);
        $whereClause = $includeDeleted ? [] : ['e.is_deleted = 0']; // Exclude soft-deleted events unless explicitly requested
        $params = [];
        $computedStatusSql = "CASE
                    WHEN e.is_deleted = 1 OR e.status = 'hidden' THEN 'hidden'
                    WHEN TIMESTAMP(e.event_date, e.event_time) > NOW() THEN 'upcoming'
                    WHEN e.event_end_time IS NOT NULL AND TIMESTAMP(e.event_date, e.event_end_time) <= NOW() THEN 'completed'
                    WHEN e.event_end_time IS NULL AND e.event_date < CURDATE() THEN 'completed'
                    ELSE 'ongoing'
                END";

        // Apply visibility filtering based on current user
        $visibilityClause = $this->buildVisibilityFilter($currentUser);
        if (!empty($visibilityClause['clause'])) {
            $whereClause[] = $visibilityClause['clause'];
            $params = array_merge($params, $visibilityClause['params']);
        }

        // Apply filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'e.category = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['university'])) {
            $whereClause[] = 'e.university = :university';
            $params['university'] = $filters['university'];
        }

        if (!empty($filters['status'])) {
            $whereClause[] = "({$computedStatusSql}) = :status_filter";
            $params['status_filter'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $whereClause[] = '(e.title LIKE :search OR e.description LIKE :search OR e.university_name LIKE :search OR e.organizer LIKE :search OR e.location LIKE :search OR p.society_name LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT e.*, ({$computedStatusSql}) as status, p.society_name as organizer_name, pp.logo_url as organizer_photo,
                CASE WHEN e.is_deleted = 1 OR e.status = 'hidden' THEN 1 ELSE 0 END as is_hidden_event,
                e.deleted_at as hidden_at,
                CONVERT(COALESCE(e.deletion_reason, '') USING utf8mb4) as hidden_reason,
                COALESCE(mod_hide.full_name, adm_hide.full_name) as hidden_by_name,
                CASE
                    WHEN mod_hide.id IS NOT NULL THEN 'moderator'
                    WHEN adm_hide.id IS NOT NULL THEN 'admin'
                    ELSE NULL
                END as hidden_by_role,
                CASE 
                    WHEN e.event_date = CURDATE() AND e.event_time <= CURTIME() AND (e.event_end_time IS NULL OR e.event_end_time > CURTIME()) THEN 1
                    WHEN e.event_date > CURDATE() OR (e.event_date = CURDATE() AND e.event_time > CURTIME()) THEN 2
                    ELSE 3
                END as event_status_order
                FROM {$this->table} e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                LEFT JOIN publisher_profiles pp ON p.id = pp.publisher_id
                LEFT JOIN moderators mod_hide ON e.deleted_by = mod_hide.id
                LEFT JOIN admins adm_hide ON e.deleted_by = adm_hide.id";

        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClause);
        }

        $sql .= " ORDER BY event_status_order ASC,
                CASE WHEN ({$computedStatusSql}) = 'completed' THEN e.event_date END DESC,
                CASE WHEN ({$computedStatusSql}) = 'completed' THEN e.event_time END DESC,
                CASE WHEN ({$computedStatusSql}) != 'completed' THEN e.event_date END ASC,
                CASE WHEN ({$computedStatusSql}) != 'completed' THEN e.event_time END ASC";

        // Add pagination if specified
        if (isset($filters['limit'])) {
            $sql .= ' LIMIT :limit';
            $params['limit'] = $filters['limit'];

            if (isset($filters['offset'])) {
                $sql .= ' OFFSET :offset';
                $params['offset'] = $filters['offset'];
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * Build visibility filter based on current user
     */
    public function buildVisibilityFilter($currentUser = null)
    {
        $visibilityConditions = [];
        $params = [];

        // If no user logged in, only show public events
        if (!$currentUser) {
            return [
                'clause' => "e.visibility = 'public'",
                'params' => []
            ];
        }

        $userType = $currentUser['type'] ?? null;
        $userUniversity = $currentUser['university'] ?? null;
        $userFaculty = $currentUser['faculty'] ?? null;

        // Admins can see all events
        if ($userType === 'admin') {
            return ['clause' => '', 'params' => []];
        }

        // Moderators can only see events from publishers of their university
        if ($userType === 'moderator') {
            if (!empty($userUniversity)) {
                return [
                    'clause' => 'p.university = :moderator_university',
                    'params' => ['moderator_university' => $userUniversity]
                ];
            } else {
                // If moderator has no university assigned, show no events
                return [
                    'clause' => '1 = 0',
                    'params' => []
                ];
            }
        }

        // Sponsors can only see public events
        if ($userType === 'sponsor') {
            return [
                'clause' => "e.visibility = 'public'",
                'params' => []
            ];
        }

        // Public events - everyone can see
        $visibilityConditions[] = "e.visibility = 'public'";

        // All universities events - university users and publishers can see
        if (in_array($userType, ['university', 'university_user', 'publisher'])) {
            $visibilityConditions[] = "e.visibility = 'all-universities'";

            // University-only events - only users from that university
            if (!empty($userUniversity)) {
                $visibilityConditions[] = "(e.visibility = 'university-only' AND e.university = :user_university)";
                $params['user_university'] = $userUniversity;

                // Faculty-only events - only users from that faculty and university
                if (!empty($userFaculty)) {
                    $visibilityConditions[] = "(e.visibility = 'faculty-only' AND e.university = :user_university2 AND e.faculty_department = :user_faculty)";
                    $params['user_university2'] = $userUniversity;
                    $params['user_faculty'] = $userFaculty;
                }
            }
        }

        // Combine all conditions with OR
        if (!empty($visibilityConditions)) {
            return [
                'clause' => '(' . implode(' OR ', $visibilityConditions) . ')',
                'params' => $params
            ];
        }

        // Default: only public events
        return [
            'clause' => "e.visibility = 'public'",
            'params' => []
        ];
    }

    /**
     * Get events that are seeking sponsors
     * These are typically upcoming events that accept donations or need funding
     */
    public function getEventsSeekingSponsors($filters = [], $currentUser = null)
    {
        $whereClause = ['e.status = :status', 'e.is_deleted = 0']; // Exclude soft-deleted events
        $params = ['status' => 'upcoming'];

        // Apply visibility filtering based on current user
        $visibilityClause = $this->buildVisibilityFilter($currentUser);
        if (!empty($visibilityClause['clause'])) {
            $whereClause[] = $visibilityClause['clause'];
            $params = array_merge($params, $visibilityClause['params']);
        }

        // Apply filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'e.category = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['university'])) {
            $whereClause[] = 'e.university = :university';
            $params['university'] = $filters['university'];
        }

        if (!empty($filters['search'])) {
            $whereClause[] = '(e.title LIKE :search OR e.description LIKE :search OR e.university_name LIKE :search OR e.organizer LIKE :search OR e.location LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT e.* FROM {$this->table} e";

        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClause);
        }

        $sql .= ' ORDER BY event_date ASC, event_time ASC';

        // Add pagination if specified
        if (isset($filters['limit'])) {
            $sql .= ' LIMIT :limit';
            $params['limit'] = $filters['limit'];

            if (isset($filters['offset'])) {
                $sql .= ' OFFSET :offset';
                $params['offset'] = $filters['offset'];
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * Get event by ID
     */
    public function getEventById($id)
    {
        // Join with publisher and publisher_profiles to get organizer info
        $sql = "SELECT e.*, p.society_name as organizer_name, pp.logo_url as organizer_photo
                FROM {$this->table} e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                LEFT JOIN publisher_profiles pp ON p.id = pp.publisher_id
                WHERE e.id = :id";

        $result = $this->query($sql, ['id' => $id]);

        if ($result && count($result) > 0) {
            $event = $result[0];
            // Decode JSON fields
            if ($event->requirements) {
                $event->requirements = json_decode($event->requirements, true);
            }
            if ($event->schedule) {
                $event->schedule = json_decode($event->schedule, true);
            }
            if ($event->ticket_types) {
                $event->ticket_types = json_decode($event->ticket_types, true);
            }
            if ($event->custom_fields) {
                $event->custom_fields = json_decode($event->custom_fields, true);
            }
            if ($event->volunteer_sources) {
                $event->volunteer_sources = json_decode($event->volunteer_sources, true);
            }
            if ($event->volunteer_positions) {
                $event->volunteer_positions = json_decode($event->volunteer_positions, true);
            }
            return $event;
        }
        return null;
    }

    /**
     * Get events by category
     */
    public function getEventsByCategory($category, $limit = null)
    {
        $filters = ['category' => $category];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }

    /**
     * Get events based on user role and permissions
     * Non-user roles (publisher, admin, moderator, sponsor) can see completed events
     * Regular users cannot see completed events unless specifically requested
     */
    public function getEventsByRole($userRole = 'user', $filters = [], $currentUser = null)
    {
        $allowCompletedEvents = in_array($userRole, ['admin', 'moderator']);
        $computedStatusSql = "CASE
                    WHEN e.is_deleted = 1 OR e.status = 'hidden' THEN 'hidden'
                    WHEN TIMESTAMP(e.event_date, e.event_time) > NOW() THEN 'upcoming'
                    WHEN e.event_end_time IS NOT NULL AND TIMESTAMP(e.event_date, e.event_end_time) <= NOW() THEN 'completed'
                    WHEN e.event_end_time IS NULL AND e.event_date < CURDATE() THEN 'completed'
                    ELSE 'ongoing'
                END";

        // For publishers and sponsors, exclude completed events by default unless specifically filtered
        if ($userRole === 'publisher' && !isset($filters['status'])) {
            // Add filter to exclude completed events for publishers
            $filters['status_exclude'] = 'completed';
        }

        // If user role can't see completed events and no specific status filter is set
        if (!$allowCompletedEvents && !isset($filters['status'])) {
            // Add filter to exclude completed events
            $whereClause = ['e.is_deleted = 0'];
            $params = [];

            // Apply visibility filtering based on current user
            $visibilityClause = $this->buildVisibilityFilter($currentUser);
            if (!empty($visibilityClause['clause'])) {
                $whereClause[] = $visibilityClause['clause'];
                $params = array_merge($params, $visibilityClause['params']);
            }

            // Apply existing filters
            foreach ($filters as $key => $value) {
                if (!empty($value) && $key !== 'status_exclude') {
                    switch ($key) {
                        case 'category':
                            $whereClause[] = 'e.category = :category';
                            $params['category'] = $value;
                            break;
                        case 'university':
                            $whereClause[] = 'e.university = :university';
                            $params['university'] = $value;
                            break;
                        case 'search':
                            $whereClause[] = '(e.title LIKE :search OR e.description LIKE :search OR e.university_name LIKE :search OR e.organizer LIKE :search OR e.location LIKE :search OR p.society_name LIKE :search)';
                            $params['search'] = '%' . $value . '%';
                            break;
                    }
                }
            }

            // Exclude completed events
            if (isset($filters['status_exclude'])) {
                $whereClause[] = "({$computedStatusSql}) != :status_exclude";
                $params['status_exclude'] = $filters['status_exclude'];
            } else {
                $whereClause[] = "({$computedStatusSql}) != 'completed'";
            }

            $sql = "SELECT e.*, ({$computedStatusSql}) as status, p.society_name as organizer_name, pp.logo_url as organizer_photo FROM {$this->table} e
                    LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                    LEFT JOIN publisher_profiles pp ON p.id = pp.publisher_id";

            if (!empty($whereClause)) {
                $sql .= ' WHERE ' . implode(' AND ', $whereClause);
            }

            $sql .= ' ORDER BY e.event_date ASC, e.event_time ASC';

            // Add pagination if specified
            if (isset($filters['limit'])) {
                $sql .= ' LIMIT :limit';
                $params['limit'] = $filters['limit'];

                if (isset($filters['offset'])) {
                    $sql .= ' OFFSET :offset';
                    $params['offset'] = $filters['offset'];
                }
            }

            return $this->query($sql, $params);
        } else {
            // Use existing getAllEvents method for admins/moderators or when status is specifically requested
            return $this->getAllEvents($filters, $currentUser);
        }
    }

    /**
     * Get events by university
     */
    public function getEventsByUniversity($university, $limit = null)
    {
        $filters = ['university' => $university];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($limit = null)
    {
        $filters = ['status' => 'upcoming'];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }

    /**
     * Get similar events (same category or university)
     */
    public function getSimilarEvents($eventId, $category, $university, $limit = 3, $currentUser = null)
    {
        $whereClause = [
            'e.id != :eventId',
            '(e.category = :category OR e.university = :university)',
            'e.is_deleted = 0'
        ];
        $params = [
            'eventId' => $eventId,
            'category' => $category,
            'university' => $university,
            'limit' => $limit
        ];

        // Apply visibility filtering based on current user
        $visibilityClause = $this->buildVisibilityFilter($currentUser);
        if (!empty($visibilityClause['clause'])) {
            $whereClause[] = $visibilityClause['clause'];
            $params = array_merge($params, $visibilityClause['params']);
        }

        $sql = "SELECT e.*, p.society_name as organizer_name, pp.logo_url as organizer_photo
                FROM {$this->table} e 
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                LEFT JOIN publisher_profiles pp ON p.id = pp.publisher_id
                WHERE " . implode(' AND ', $whereClause) . "
                ORDER BY e.event_date ASC 
                LIMIT :limit";

        return $this->query($sql, $params);
    }

    /**
     * Search events
     */
    public function searchEvents($searchTerm, $limit = null)
    {
        $filters = ['search' => $searchTerm];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }

    /**
     * Update participant count (legacy - for backward compatibility)
     */
    public function updateParticipants($id, $newCount)
    {
        return $this->update($id, ['participants' => $newCount]);
    }

    /**
     * Update current participants count
     */
    public function updateCurrentParticipants($id, $newCount)
    {
        $normalizedCount = max(0, (int)$newCount);
        $updateData = [
            'current_participants' => $normalizedCount,
            'participants' => $normalizedCount
        ];

        return $this->update($id, $updateData);
    }

    /**
     * Determine the active registration cap for an event.
     */
    public function getRegistrationLimitValue($event)
    {
        if (!$event) {
            return null;
        }

        if (isset($event->registration_limit) && $event->registration_limit !== null && $event->registration_limit !== '') {
            return (int)$event->registration_limit;
        }

        if (isset($event->max_participants) && $event->max_participants !== null && $event->max_participants !== '') {
            return (int)$event->max_participants;
        }

        return null;
    }

    /**
     * Increment current participants (when user registers or buys ticket)
     */
    public function incrementParticipants($id, $count = 1)
    {
        $count = max(1, (int)$count);
        $event = $this->getEventById($id);
        if (!$event) {
            return false;
        }

        $limit = $this->getRegistrationLimitValue($event);
        $currentParticipants = (int)($event->current_participants ?? 0);

        if ($limit !== null && ($currentParticipants + $count) > $limit) {
            return false;
        }

        return $this->updateCurrentParticipants($id, $currentParticipants + $count);
    }

    /**
     * Decrement current participants (when user cancels registration)
     */
    public function decrementParticipants($id, $count = 1)
    {
        $count = max(1, (int)$count);
        $event = $this->getEventById($id);
        if (!$event || (int)($event->current_participants ?? 0) <= 0) {
            return false;
        }

        $currentParticipants = max(0, (int)$event->current_participants - $count);
        return $this->updateCurrentParticipants($id, $currentParticipants);
    }

    /**
     * Check if event has available spots
     */
    public function hasAvailableSpots($id, $count = 1)
    {
        $count = max(1, (int)$count);
        $event = $this->getEventById($id);
        if (!$event) {
            return false;
        }

        $limit = $this->getRegistrationLimitValue($event);
        if ($limit === null) {
            return true;
        }

        return ((int)($event->current_participants ?? 0) + $count) <= $limit;
    }

    /**
     * Get available spots count
     */
    public function getAvailableSpots($id)
    {
        $event = $this->getEventById($id);
        if (!$event) {
            return 0;
        }

        $limit = $this->getRegistrationLimitValue($event);
        if ($limit === null) {
            return null;
        }

        return max(0, $limit - (int)($event->current_participants ?? 0));
    }

    /**
     * Apply a paid ticket purchase to the event inventory and participant counters.
     *
     * @param mixed $id
     * @param array $ticketSelections
     * @return object|false Updated event on success, false on validation failure
     */
    public function applyTicketPurchase($id, array $ticketSelections)
    {
        $event = $this->getEventById($id);
        if (!$event) {
            return false;
        }

        $ticketTypes = $event->ticket_types;
        if (is_string($ticketTypes)) {
            $ticketTypes = json_decode($ticketTypes, true);
        }

        if (!is_array($ticketTypes) || empty($ticketTypes)) {
            return false;
        }

        $normalizedSelections = [];
        $totalQuantity = 0;
        foreach ($ticketSelections as $selection) {
            $ticketName = strtolower(trim((string)($selection['name'] ?? '')));
            $quantity = max(0, (int)($selection['quantity'] ?? 0));
            if ($ticketName === '' || $quantity <= 0) {
                continue;
            }

            $normalizedSelections[] = [
                'name' => $ticketName,
                'quantity' => $quantity
            ];
            $totalQuantity += $quantity;
        }

        if ($totalQuantity <= 0) {
            return false;
        }

        $limit = $this->getRegistrationLimitValue($event);
        $currentParticipants = (int)($event->current_participants ?? 0);
        $ticketType = strtolower(trim((string)($event->ticket_type ?? '')));
        $shouldEnforceRegistrationLimit = $ticketType !== 'mixed';

        if ($shouldEnforceRegistrationLimit && $limit !== null && ($currentParticipants + $totalQuantity) > $limit) {
            return false;
        }

        $ticketIndexMap = [];
        foreach ($ticketTypes as $index => $ticketType) {
            $ticketName = strtolower(trim((string)($ticketType['name'] ?? '')));
            if ($ticketName !== '') {
                $ticketIndexMap[$ticketName] = $index;
            }
        }

        foreach ($normalizedSelections as $selection) {
            if (!array_key_exists($selection['name'], $ticketIndexMap)) {
                return false;
            }

            $ticketIndex = $ticketIndexMap[$selection['name']];
            $availableQuantity = max(0, (int)($ticketTypes[$ticketIndex]['quantity'] ?? 0));
            if ($availableQuantity < $selection['quantity']) {
                return false;
            }
        }

        foreach ($normalizedSelections as $selection) {
            $ticketIndex = $ticketIndexMap[$selection['name']];
            $ticketTypes[$ticketIndex]['quantity'] = max(0, (int)($ticketTypes[$ticketIndex]['quantity'] ?? 0) - $selection['quantity']);
        }

        $updateData = [
            'current_participants' => $currentParticipants + $totalQuantity,
            'ticket_types' => json_encode(array_values($ticketTypes))
        ];

        $updateData['participants'] = $currentParticipants + $totalQuantity;

        if ($this->update($id, $updateData)) {
            return $this->getEventById($id);
        }

        return false;
    }

    /**
     * Join event (increment participant count)
     * @deprecated Use incrementParticipants() instead
     */
    public function joinEvent($id)
    {
        return $this->incrementParticipants($id);
    }

    /**
     * Leave event (decrement participant count)
     * @deprecated Use decrementParticipants() instead
     */
    public function leaveEvent($id)
    {
        return $this->decrementParticipants($id);
    }

    /**
     * Get event statistics
     */
    public function getEventStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_events,
                    COUNT(CASE WHEN status = 'upcoming' THEN 1 END) as upcoming_events,
                    COUNT(CASE WHEN status = 'ongoing' THEN 1 END) as ongoing_events,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_events,
                    SUM(participants) as total_participants,
                    AVG(participants) as avg_participants
                FROM {$this->table}";

        $result = $this->query($sql);
        return $result ? $result[0] : null;
    }

    /**
     * Validate event data
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        }

        if (empty($data['category'])) {
            $errors['category'] = 'Category is required';
        }

        if (empty($data['event_date'])) {
            $errors['event_date'] = 'Event date is required';
        }

        if (empty($data['event_time'])) {
            $errors['event_time'] = 'Event time is required';
        }

        if (empty($data['location'])) {
            $errors['location'] = 'Location is required';
        }

        if (empty($data['organizer'])) {
            $errors['organizer'] = 'Organizer is required';
        }

        if (empty($data['university'])) {
            $errors['university'] = 'University is required';
        }

        if (empty($data['university_name'])) {
            $errors['university_name'] = 'University name is required';
        }

        return $errors;
    }

    /**
     * Create new event
     */
    public function createEvent($data)
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Encode JSON fields
        if (isset($data['requirements']) && is_array($data['requirements'])) {
            $data['requirements'] = json_encode($data['requirements']);
        }

        if (isset($data['schedule']) && is_array($data['schedule'])) {
            $data['schedule'] = json_encode($data['schedule']);
        }

        if (isset($data['ticket_types']) && is_array($data['ticket_types'])) {
            // Ensure each ticket has total_capacity set to initial quantity
            foreach ($data['ticket_types'] as &$ticket) {
                if (!isset($ticket['total_capacity'])) {
                    $ticket['total_capacity'] = $ticket['quantity'];
                }
            }
            $data['ticket_types'] = json_encode($data['ticket_types']);
        }

        if (isset($data['custom_fields']) && is_array($data['custom_fields'])) {
            $data['custom_fields'] = json_encode($data['custom_fields']);
        }

        if (isset($data['volunteer_sources']) && is_array($data['volunteer_sources'])) {
            $data['volunteer_sources'] = json_encode($data['volunteer_sources']);
        }

        if (isset($data['volunteer_positions']) && is_array($data['volunteer_positions'])) {
            $data['volunteer_positions'] = json_encode($data['volunteer_positions']);
        }

        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));

        try {
            $eventId = $this->insert($filteredData);
            if ($eventId) {
                return [
                    'success' => true,
                    'message' => 'Event created successfully',
                    'event_id' => intval($eventId)
                ];
            }

            return ['success' => false, 'errors' => ['general' => 'Failed to create event']];
        } catch (Exception $e) {
            error_log("Database error in Event::createEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Update existing event
     */
    public function updateEvent($eventId, $data)
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Encode JSON fields
        if (isset($data['requirements']) && is_array($data['requirements'])) {
            $data['requirements'] = json_encode($data['requirements']);
        }

        if (isset($data['schedule']) && is_array($data['schedule'])) {
            $data['schedule'] = json_encode($data['schedule']);
        }

        if (isset($data['ticket_types']) && is_array($data['ticket_types'])) {
            // Ensure each ticket has total_capacity set
            foreach ($data['ticket_types'] as &$ticket) {
                if (!isset($ticket['total_capacity'])) {
                    $ticket['total_capacity'] = $ticket['quantity'];
                }
            }
            $data['ticket_types'] = json_encode($data['ticket_types']);
        }

        if (isset($data['custom_fields']) && is_array($data['custom_fields'])) {
            $data['custom_fields'] = json_encode($data['custom_fields']);
        }

        if (isset($data['volunteer_sources']) && is_array($data['volunteer_sources'])) {
            $data['volunteer_sources'] = json_encode($data['volunteer_sources']);
        }

        if (isset($data['volunteer_positions']) && is_array($data['volunteer_positions'])) {
            $data['volunteer_positions'] = json_encode($data['volunteer_positions']);
        }

        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));

        try {
            if ($this->update($eventId, $filteredData)) {
                return [
                    'success' => true,
                    'message' => 'Event updated successfully',
                    'event_id' => intval($eventId)
                ];
            }

            return ['success' => false, 'errors' => ['general' => 'Failed to update event']];
        } catch (Exception $e) {
            error_log("Database error in Event::updateEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Delete event
     */
    public function deleteEvent($eventId, $userId)
    {
        try {
            // First verify the event exists and belongs to the user
            $event = $this->getEventById($eventId);
            if (!$event) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }

            if ($event->created_by != $userId) {
                return ['success' => false, 'errors' => ['general' => 'You can only delete your own events']];
            }

            // Delete the event
            if ($this->delete($eventId)) {
                return [
                    'success' => true,
                    'message' => 'Event deleted successfully'
                ];
            }

            return ['success' => false, 'errors' => ['general' => 'Failed to delete event']];
        } catch (Exception $e) {
            error_log("Database error in Event::deleteEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Admin delete event (can delete any event)
     */
    public function deleteEventAdmin($eventId)
    {
        try {
            // First get the event to check if it exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $result = $this->query($checkQuery, ['id' => $eventId]);

            if (empty($result)) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }

            // Delete the event
            $query = "DELETE FROM events WHERE id = :id";
            $this->query($query, ['id' => $eventId]);

            return ['success' => true, 'message' => 'Event deleted successfully'];
        } catch (Exception $e) {
            error_log("Database error in Event::deleteEventAdmin: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Hide event (admin only)
     */
    public function hideEvent($eventId, $moderatorOrAdminId = null, $reason = '')
    {
        try {
            // First check if the event exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $result = $this->query($checkQuery, ['id' => $eventId]);

            if (empty($result)) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }

            // Persist hidden metadata so admin list can show who hid the event and why.
            $query = "UPDATE events
                      SET status = 'hidden',
                          is_deleted = 1,
                          deleted_at = NOW(),
                          deleted_by = :hidden_by,
                          deletion_reason = :reason,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            $this->query($query, [
                'id' => $eventId,
                'hidden_by' => $moderatorOrAdminId,
                'reason' => $reason
            ]);

            return ['success' => true, 'message' => 'Event hidden successfully'];
        } catch (Exception $e) {
            error_log("Database error in Event::hideEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Show event (admin only)
     */
    public function showEvent($eventId)
    {
        try {
            // First check if the event exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $result = $this->query($checkQuery, ['id' => $eventId]);

            if (empty($result)) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }

            // Restore visibility and clear hidden metadata.
            $query = "UPDATE events
                      SET status = 'upcoming',
                          is_deleted = 0,
                          deleted_at = NULL,
                          deleted_by = NULL,
                          deletion_reason = NULL,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            $this->query($query, ['id' => $eventId]);

            return ['success' => true, 'message' => 'Event shown successfully'];
        } catch (Exception $e) {
            error_log("Database error in Event::showEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Get pending events for moderation by university
     */
    public function getPendingEventsForUniversity($university, $limit = 20)
    {
        $query = "SELECT e.*, 
                         p.society_name as organizer_name,
                         p.email as organizer_email
                  FROM events e
                  LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                  WHERE e.university = :university 
                  AND e.status = 'pending'
                  ORDER BY e.created_at DESC
                  LIMIT :limit";

        return $this->query($query, [
            'university' => $university,
            'limit' => $limit
        ]);
    }

    /**
     * Get moderation statistics for a university
     */
    public function getModerationStatsForUniversity($university)
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' OR status = 'upcoming' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN (status = 'approved' OR status = 'rejected') AND DATE(updated_at) = CURDATE() THEN 1 ELSE 0 END) as reviewed_today
                  FROM events 
                  WHERE university = :university";

        return $this->getRow($query, ['university' => $university]);
    }

    /**
     * Approve an event
     */
    public function approve($eventId, $moderatorId)
    {
        $query = "UPDATE events 
                  SET status = 'approved',
                      moderated_by = :moderator_id,
                      moderated_at = NOW(),
                      updated_at = NOW()
                  WHERE id = :event_id";

        $result = $this->query($query, [
            'event_id' => $eventId,
            'moderator_id' => $moderatorId
        ]);

        if ($result !== false) {
            // Create notification for event organizer
            $this->createModerationNotification($eventId, $moderatorId, 'approved');
            return true;
        }
        return false;
    }

    /**
     * Reject an event
     */
    public function reject($eventId, $moderatorId, $reason = '')
    {
        $query = "UPDATE events 
                  SET status = 'rejected',
                      moderated_by = :moderator_id,
                      moderation_reason = :reason,
                      moderated_at = NOW(),
                      updated_at = NOW()
                  WHERE id = :event_id";

        $result = $this->query($query, [
            'event_id' => $eventId,
            'moderator_id' => $moderatorId,
            'reason' => $reason
        ]);

        if ($result !== false) {
            // Create notification for event organizer
            $this->createModerationNotification($eventId, $moderatorId, 'rejected', $reason);
            return true;
        }
        return false;
    }

    /**
     * Create moderation notification
     */
    private function createModerationNotification($eventId, $moderatorId, $type, $message = '')
    {
        $query = "INSERT INTO event_moderation_notifications 
                  (event_id, moderator_id, notification_type, message) 
                  VALUES (:event_id, :moderator_id, :type, :message)";

        $this->query($query, [
            'event_id' => $eventId,
            'moderator_id' => $moderatorId,
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Soft delete (hide) an event
     */
    public function softDelete($eventId, $moderatorId, $reason = '')
    {
        try {
            $conn = $this->connect();

            $query = "UPDATE events 
                      SET is_deleted = 1,
                          deleted_at = NOW(),
                          deleted_by = :moderator_id,
                          deletion_reason = :reason,
                          updated_at = NOW()
                      WHERE id = :event_id";

            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
                'event_id' => $eventId,
                'moderator_id' => $moderatorId,
                'reason' => $reason
            ]);

            if ($result) {
                // Notify publisher about the deletion
                $this->notifyPublisherOfDeletion($eventId, $moderatorId, $reason);
                error_log("softDelete successful for event_id: $eventId");
                return true;
            }

            error_log("softDelete failed - query returned false for event_id: $eventId");
            return false;
        } catch (Exception $e) {
            error_log("softDelete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all hidden (soft-deleted) events
     */
    public function getHiddenEvents($filters = [], $currentUser = null)
    {
        $whereClause = ['e.is_deleted = 1']; // Only soft-deleted events
        $params = [];

        // For moderators, only show hidden events from publishers of their university
        if ($currentUser && ($currentUser['type'] ?? null) === 'moderator') {
            if (!empty($currentUser['university'])) {
                $whereClause[] = 'p.university = :moderator_university';
                $params['moderator_university'] = $currentUser['university'];
            } else {
                // If moderator has no university, show no events
                $whereClause[] = '1 = 0';
            }
        }

        // Apply filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'e.category = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['university'])) {
            $whereClause[] = 'e.university = :university';
            $params['university'] = $filters['university'];
        }

        if (!empty($filters['search'])) {
            $whereClause[] = '(e.title LIKE :search OR e.description LIKE :search OR e.university_name LIKE :search OR e.organizer LIKE :search OR e.location LIKE :search OR p.society_name LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT e.*, 
                       m.full_name as moderator_name,
                       m.email as moderator_email,
                       p.society_name as organizer_name,
                       p.university as publisher_university
                FROM {$this->table} e
                LEFT JOIN moderators m ON e.deleted_by = m.id
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'";

        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClause);
        }

        $sql .= ' ORDER BY e.deleted_at DESC';

        // Add pagination if specified
        if (isset($filters['limit'])) {
            $sql .= ' LIMIT :limit';
            $params['limit'] = $filters['limit'];

            if (isset($filters['offset'])) {
                $sql .= ' OFFSET :offset';
                $params['offset'] = $filters['offset'];
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * Restore a soft-deleted event
     */
    public function restore($eventId, $moderatorId = null)
    {
        try {
            $conn = $this->connect();

            // Preferred path when restore-tracking columns exist.
            $queryWithTracking = "UPDATE events 
                                  SET is_deleted = 0,
                                      deleted_at = NULL,
                                      deleted_by = NULL,
                                      deletion_reason = NULL,
                                      restored_by = :restored_by,
                                      restored_at = NOW(),
                                      updated_at = NOW()
                                  WHERE id = :event_id";

            $stmt = $conn->prepare($queryWithTracking);

            try {
                $result = $stmt->execute(['event_id' => $eventId, 'restored_by' => $moderatorId]);
            } catch (Exception $trackingError) {
                // Backward compatibility for databases without restored_by/restored_at columns.
                error_log("restore fallback (without tracking columns): " . $trackingError->getMessage());

                $queryFallback = "UPDATE events 
                                  SET is_deleted = 0,
                                      deleted_at = NULL,
                                      deleted_by = NULL,
                                      deletion_reason = NULL,
                                      updated_at = NOW()
                                  WHERE id = :event_id";

                $stmtFallback = $conn->prepare($queryFallback);
                $result = $stmtFallback->execute(['event_id' => $eventId]);
            }

            if ($result) {
                // Optionally notify the publisher that their event has been restored
                $this->notifyPublisherOfRestoration($eventId);
            }

            return $result;
        } catch (Exception $e) {
            error_log("restore error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify publisher of event restoration
     */
    private function notifyPublisherOfRestoration($eventId)
    {
        try {
            // Get event and publisher details
            $event = $this->getEventWithPublisher($eventId);

            if (!$event) {
                error_log("notifyPublisherOfRestoration: Event not found - $eventId");
                return false;
            }

            // Create notification in database (if the table exists)
            $conn = $this->connect();
            $query = "INSERT INTO event_moderation_notifications 
                      (event_id, notification_type, message, created_at) 
                      VALUES (:event_id, 'restored', :message, NOW())";

            $stmt = $conn->prepare($query);
            $message = "Your event '{$event->title}' has been restored and is now visible again.";

            $stmt->execute([
                'event_id' => $eventId,
                'message' => $message
            ]);

            return true;
        } catch (Exception $e) {
            // If notification fails, just log it - don't prevent restoration
            error_log("notifyPublisherOfRestoration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get event with publisher details (for notifications)
     */
    public function getEventWithPublisher($eventId)
    {
        $query = "SELECT e.*, p.email as publisher_email, p.society_name as publisher_name, p.university as publisher_university
                  FROM events e
                  LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                  WHERE e.id = :event_id";

        $result = $this->query($query, ['event_id' => $eventId]);
        return $result ? $result[0] : null;
    }

    /**
     * Notify publisher of event deletion
     */
    private function notifyPublisherOfDeletion($eventId, $moderatorId, $reason)
    {
        try {
            // Get event and publisher details
            $event = $this->getEventWithPublisher($eventId);

            if (!$event) {
                error_log("notifyPublisherOfDeletion: Event not found - $eventId");
                return false;
            }

            // Only publisher-owned events should trigger publisher notifications.
            $publisherId = (int)($event->created_by ?? 0);
            $createdByType = (string)($event->created_by_type ?? '');
            if ($createdByType !== 'publisher' || $publisherId <= 0) {
                return true;
            }

            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($moderatorId);
            $moderatorName = trim((string)($moderator->full_name ?? ''));
            if ($moderatorName === '') {
                $moderatorName = 'a moderator';
            }

            $cleanReason = trim((string)$reason);
            if ($cleanReason === '') {
                $cleanReason = 'No reason provided';
            }

            $eventTitle = (string)($event->title ?? 'Untitled Event');
            $message = "Your event '{$eventTitle}' was hidden by {$moderatorName}. Reason: {$cleanReason}";

            // Primary notification path used by publisher dashboard/header.
            $notificationType = $this->resolveNotificationType('event_hidden');

            $notificationModel = new Notification();
            $notificationResult = $notificationModel->sendNotification([
                'recipient_id' => $publisherId,
                'recipient_type' => 'publisher',
                'type' => $notificationType,
                'title' => 'Event Hidden by Moderator',
                'message' => $message,
                'related_id' => (int)$eventId,
                'related_type' => 'event',
                'is_read' => 0
            ]);

            // Legacy moderation notification record (best-effort).
            $conn = $this->connect();
            $query = "INSERT INTO event_moderation_notifications 
                      (event_id, moderator_id, notification_type, message, created_at) 
                      VALUES (:event_id, :moderator_id, 'deleted', :message, NOW())";

            $stmt = $conn->prepare($query);
            $legacyResult = $stmt->execute([
                'event_id' => $eventId,
                'moderator_id' => $moderatorId,
                'message' => $message
            ]);

            if ($notificationResult || $legacyResult) {
                error_log("Publisher hidden-event notification created for event $eventId");
            }

            return (bool)($notificationResult || $legacyResult);
        } catch (Exception $e) {
            error_log("notifyPublisherOfDeletion error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve a notification type that exists in current DB enum definition.
     */
    private function resolveNotificationType($preferredType)
    {
        $preferredType = trim((string)$preferredType);
        if ($preferredType === '') {
            return 'event_comment';
        }

        try {
            $columns = $this->query("SHOW COLUMNS FROM notifications WHERE Field = 'type'");
            if (!$columns || !isset($columns[0]->Type)) {
                return $preferredType;
            }

            $typeDef = (string)$columns[0]->Type;
            if (!preg_match('/^enum\((.*)\)$/i', $typeDef, $matches)) {
                return $preferredType;
            }

            $allowed = array_map(function ($item) {
                return trim($item, "' \t\n\r\0\x0B");
            }, explode(',', (string)$matches[1]));

            if (in_array($preferredType, $allowed, true)) {
                return $preferredType;
            }

            foreach (['event_comment', 'new_comment', 'comment_hidden', 'comment_edited', 'comment_deleted'] as $fallback) {
                if (in_array($fallback, $allowed, true)) {
                    return $fallback;
                }
            }

            return !empty($allowed[0]) ? $allowed[0] : $preferredType;
        } catch (Exception $e) {
            error_log('resolveNotificationType warning: ' . $e->getMessage());
            return $preferredType;
        }
    }

    /**
     * Check if moderator can moderate this event (same university)
     */
    public function canModeratorModerateEvent($eventId, $moderatorUniversity)
    {
        $query = "SELECT e.*, p.university as publisher_university
                  FROM events e
                  LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                  WHERE e.id = :event_id";

        $result = $this->query($query, ['event_id' => $eventId]);

        if (!$result || count($result) === 0) {
            return false;
        }

        $event = $result[0];

        // Check if event belongs to moderator's university
        return $event->publisher_university === $moderatorUniversity;
    }

    /**
     * Get events starting within the next 24 hours
     */
    public function getEventsStartingIn24Hours($limit = 10, $currentUser = null)
    {
        try {
            $query = "
                SELECT 
                    e.id,
                    e.title,
                    e.description,
                    e.category,
                    e.event_date,
                    e.event_time,
                    e.event_end_time,
                    e.location,
                    e.location_type,
                    e.university_name,
                    e.cover_image,
                    e.image_url,
                    e.current_participants,
                    e.max_participants,
                    e.ticket_type,
                    e.ticket_types,
                    e.organizer,
                    e.created_by as publisher_id,
                    e.created_by_type,
                    p.society_name as organizer_name
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                WHERE e.is_deleted = 0
                    AND TIMESTAMP(e.event_date, e.event_time) > NOW()
                    AND TIMESTAMP(e.event_date, e.event_time) <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
            ";

            $visibilityClause = $this->buildVisibilityFilter($currentUser);
            if (!empty($visibilityClause['clause'])) {
                $query .= ' AND ' . $visibilityClause['clause'];
            }

            $query .= " ORDER BY e.event_date ASC, e.event_time ASC LIMIT :limit";

            $conn = $this->connect();
            $stmt = $conn->prepare($query);

            if (!empty($visibilityClause['params'])) {
                foreach ($visibilityClause['params'] as $param => $value) {
                    $stmt->bindValue(':' . $param, $value, PDO::PARAM_STR);
                }
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            error_log("getEventsStartingIn24Hours: Found " . count($results) . " events");
            error_log("Query used: " . $query);
            if (count($results) > 0) {
                error_log("First event: ID=" . $results[0]->id . ", Title=" . $results[0]->title . ", Date=" . $results[0]->event_date . " " . $results[0]->event_time);
            }

            return $results;
        } catch (Exception $e) {
            error_log("getEventsStartingIn24Hours error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get next upcoming public events for "More Events" section
     */
    public function getNextUpcomingPublicEvents($limit = 3, $currentUser = null)
    {
        try {
            $query = "
                SELECT 
                    e.id,
                    e.title,
                    e.description,
                    e.category,
                    e.event_date,
                    e.event_time,
                    e.event_end_time,
                    e.location,
                    e.location_type,
                    e.university_name,
                    e.cover_image,
                    e.image_url,
                    e.current_participants,
                    e.max_participants,
                    e.ticket_type,
                    e.ticket_types,
                    e.organizer,
                    e.created_by as publisher_id,
                    e.created_by_type,
                    p.society_name as organizer_name
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                WHERE e.is_deleted = 0
                    AND TIMESTAMP(e.event_date, e.event_time) > NOW()
            ";

            $visibilityClause = $this->buildVisibilityFilter($currentUser);
            if (!empty($visibilityClause['clause'])) {
                $query .= ' AND ' . $visibilityClause['clause'];
            }

            $query .= " ORDER BY e.event_date ASC, e.event_time ASC LIMIT :limit";

            $conn = $this->connect();
            $stmt = $conn->prepare($query);

            if (!empty($visibilityClause['params'])) {
                foreach ($visibilityClause['params'] as $param => $value) {
                    $stmt->bindValue(':' . $param, $value, PDO::PARAM_STR);
                }
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("getNextUpcomingPublicEvents error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active boosted events for landing page carousel
     */
    public function getActiveBoostedEvents($limit = 10, $currentUser = null)
    {
        try {
            $query = "
                SELECT 
                    e.id,
                    e.title,
                    e.description,
                    e.category,
                    e.event_date,
                    e.event_time,
                    e.location,
                    e.university_name,
                    e.cover_image,
                    e.image_url,
                    e.current_participants,
                    e.max_participants,
                    e.ticket_types,
                    e.organizer,
                    e.created_by as publisher_id,
                    e.created_by_type,
                    p.society_name as organizer_name,
                    eb.boost_end_date,
                    eb.priority_level,
                    eb.impressions
                FROM events e
                INNER JOIN event_boosts eb ON e.id = eb.event_id
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                WHERE e.is_boosted = 1
                    AND e.is_deleted = 0
                    AND eb.boost_status = 'active'
                    AND eb.payment_status = 'completed'
                    AND eb.boost_start_date <= NOW()
                    AND eb.boost_end_date >= NOW()
                    AND e.event_date >= CURDATE()
            ";

            $visibilityClause = $this->buildVisibilityFilter($currentUser);
            if (!empty($visibilityClause['clause'])) {
                $query .= ' AND ' . $visibilityClause['clause'];
            }

            $query .= " ORDER BY eb.priority_level DESC, eb.boost_start_date DESC LIMIT :limit";

            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            if (!empty($visibilityClause['params'])) {
                foreach ($visibilityClause['params'] as $param => $value) {
                    $stmt->bindValue(':' . $param, $value, PDO::PARAM_STR);
                }
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("getActiveBoostedEvents error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent moderation activities
     */
    public function getRecentModerationActivities($moderatorId = null, $limit = 10)
    {
        try {
            $conn = $this->connect();

            // CONVERT all text columns to utf8mb4 to avoid collation mismatch across tables
            $query = "
                (SELECT
                    e.id                                                            AS item_id,
                    CONVERT(e.title          USING utf8mb4)                         AS item_title,
                    e.deleted_at                                                    AS activity_time,
                    CONVERT(COALESCE(e.deletion_reason,'') USING utf8mb4)           AS activity_reason,
                    CONVERT(COALESCE(m.full_name,'')       USING utf8mb4)           AS moderator_name,
                    CONVERT(COALESCE(m.university_name,'') USING utf8mb4)           AS university,
                    CONVERT('hidden_event'                 USING utf8mb4)           AS activity_type
                FROM events e
                LEFT JOIN moderators m ON e.deleted_by = m.id
                WHERE e.is_deleted = 1 AND e.deleted_at IS NOT NULL";
            if ($moderatorId) {
                $query .= " AND e.deleted_by = :moderator_id1";
            }
            $query .= ")

                UNION ALL
                (SELECT
                    e.id,
                    CONVERT(e.title          USING utf8mb4),
                    e.restored_at,
                    CONVERT(''               USING utf8mb4),
                    CONVERT(COALESCE(m.full_name,'')       USING utf8mb4),
                    CONVERT(COALESCE(m.university_name,'') USING utf8mb4),
                    CONVERT('restored_event' USING utf8mb4)
                FROM events e
                LEFT JOIN moderators m ON e.restored_by = m.id
                WHERE e.restored_at IS NOT NULL";
            if ($moderatorId) {
                $query .= " AND e.restored_by = :moderator_id_r";
            }
            $query .= ")

                UNION ALL
                (SELECT
                    c.id,
                    CONVERT(CONCAT('Comment on \"', LEFT(COALESCE(e.title,''), 40), '\"') USING utf8mb4),
                    c.hidden_at,
                    CONVERT(COALESCE(c.hidden_reason,'')   USING utf8mb4),
                    CONVERT(COALESCE(m.full_name,'')       USING utf8mb4),
                    CONVERT(COALESCE(m.university_name,'') USING utf8mb4),
                    CONVERT('hidden_comment' USING utf8mb4)
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                LEFT JOIN moderators m ON c.hidden_by = m.id
                WHERE c.is_hidden = 1 AND c.hidden_at IS NOT NULL";
            if ($moderatorId) {
                $query .= " AND c.hidden_by = :moderator_id_hc";
            }
            $query .= ")

                UNION ALL
                (SELECT
                    pub.id,
                    CONVERT(pub.society_name USING utf8mb4),
                    pub.approved_at,
                    CONVERT(''               USING utf8mb4),
                    CONVERT(COALESCE(m.full_name,'')       USING utf8mb4),
                    CONVERT(COALESCE(m.university_name,'') USING utf8mb4),
                    CONVERT('publisher_approved' USING utf8mb4)
                FROM publishers pub
                LEFT JOIN moderators m ON pub.approved_by = m.id
                WHERE pub.approval_status = 'approved' AND pub.approved_at IS NOT NULL";
            if ($moderatorId) {
                $query .= " AND pub.approved_by = :moderator_id2";
            }
            $query .= ")

                UNION ALL
                (SELECT
                    pub.id,
                    CONVERT(pub.society_name USING utf8mb4),
                    pub.approved_at,
                    CONVERT(COALESCE(pub.rejection_reason,'') USING utf8mb4),
                    CONVERT(COALESCE(m.full_name,'')       USING utf8mb4),
                    CONVERT(COALESCE(m.university_name,'') USING utf8mb4),
                    CONVERT('publisher_rejected' USING utf8mb4)
                FROM publishers pub
                LEFT JOIN moderators m ON pub.approved_by = m.id
                WHERE pub.approval_status = 'rejected' AND pub.approved_at IS NOT NULL";
            if ($moderatorId) {
                $query .= " AND pub.approved_by = :moderator_id3";
            }
            $query .= ")

                ORDER BY activity_time DESC
                LIMIT :limit";

            $stmt = $conn->prepare($query);

            if ($moderatorId) {
                $stmt->bindValue(':moderator_id1',   $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id_r',  $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id_hc', $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id2',   $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id3',   $moderatorId, PDO::PARAM_INT);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("getRecentModerationActivities error: " . $e->getMessage());
            return [];
        }
    }
}
