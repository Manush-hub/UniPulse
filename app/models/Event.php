<?php

class Event {
    
    use Model;
    
    protected $table = 'events';
    protected $allowedColumns = [
        'title',
        'description',
        'category',
        'university',
        'university_name',
        'status',
        'event_date',
        'event_time',
        'location',
        'organizer',
        'organizer_email',
        'participants',
        'max_participants',
        'requirements',
        'schedule',
        'image_url',
        'visibility'
    ];
    
    /**
     * Get all events with optional filters
     */
    public function getAllEvents($filters = []) {
        $whereClause = [];
        $params = [];
        
        // Apply filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'category = :category';
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['university'])) {
            $whereClause[] = 'university = :university';
            $params['university'] = $filters['university'];
        }
        
        if (!empty($filters['status'])) {
            $whereClause[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause[] = '(title LIKE :search1 OR description LIKE :search2 OR university_name LIKE :search3 OR organizer LIKE :search4 OR location LIKE :search5)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
            $params['search5'] = $searchTerm;
        }
        
        $sql = "SELECT * FROM {$this->table}";
        
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
     * Get events accessible to a specific user based on their university affiliation
     * Public users can see all public events
     * University users can see their university events + public events
     */
    public function getEventsForUser($userType, $userUniversity = null, $filters = []) {
        $whereClause = [];
        $params = [];
        
        // Base visibility rules
        if ($userType === 'public') {
            // Public users can only see public events (events with visibility = 'public')
            $whereClause[] = "(visibility = 'public' OR visibility IS NULL)";
        } elseif ($userType === 'university' && !empty($userUniversity)) {
            // University users can see their university events + public events
            $whereClause[] = "(university = :user_university OR visibility = 'public' OR visibility IS NULL)";
            $params['user_university'] = $userUniversity;
        } else {
            // Default: only public events if user type/university is unclear
            $whereClause[] = "(visibility = 'public' OR visibility IS NULL)";
        }
        
        // Apply additional filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'category = :category';
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['university'])) {
            $whereClause[] = 'university = :filter_university';
            $params['filter_university'] = $filters['university'];
        }
        
        if (!empty($filters['status'])) {
            $whereClause[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $whereClause[] = '(title LIKE :search1 OR description LIKE :search2 OR university_name LIKE :search3 OR organizer LIKE :search4 OR location LIKE :search5)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
            $params['search5'] = $searchTerm;
        }
        
        $sql = "SELECT * FROM {$this->table}";
        
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
    public function getEventById($id) {
        $result = $this->where(['id' => $id]);
        if ($result && count($result) > 0) {
            $event = $result[0];
            // Decode JSON fields
            if ($event->requirements) {
                $event->requirements = json_decode($event->requirements, true);
            }
            if ($event->schedule) {
                $event->schedule = json_decode($event->schedule, true);
            }
            return $event;
        }
        return null;
    }
    
    /**
     * Get events by category
     */
    public function getEventsByCategory($category, $limit = null) {
        $filters = ['category' => $category];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }
    
    /**
     * Get events by university
     */
    public function getEventsByUniversity($university, $limit = null) {
        $filters = ['university' => $university];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($limit = null) {
        $filters = ['status' => 'upcoming'];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }
    
    /**
     * Get similar events (same category or university)
     */
    public function getSimilarEvents($eventId, $category, $university, $limit = 3) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id != :eventId 
                AND (category = :category OR university = :university)
                ORDER BY event_date ASC 
                LIMIT :limit";
        
        $params = [
            'eventId' => $eventId,
            'category' => $category,
            'university' => $university,
            'limit' => $limit
        ];
        
        return $this->query($sql, $params);
    }
    
    /**
     * Search events
     */
    public function searchEvents($searchTerm, $limit = null) {
        $filters = ['search' => $searchTerm];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getAllEvents($filters);
    }
    
    /**
     * Update participant count
     */
    public function updateParticipants($id, $newCount) {
        return $this->update($id, ['participants' => $newCount]);
    }
    
    /**
     * Join event (increment participant count)
     */
    public function joinEvent($id) {
        $event = $this->getEventById($id);
        if ($event && $event->participants < $event->max_participants) {
            return $this->updateParticipants($id, $event->participants + 1);
        }
        return false;
    }
    
    /**
     * Leave event (decrement participant count)
     */
    public function leaveEvent($id) {
        $event = $this->getEventById($id);
        if ($event && $event->participants > 0) {
            return $this->updateParticipants($id, $event->participants - 1);
        }
        return false;
    }
    
    /**
     * Get event statistics
     */
    public function getEventStats() {
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
     * Get user university information based on user type and session data
     */
    public function getUserUniversity($userId, $userType, $userTable) {
        if ($userType === 'university') {
            $sql = "SELECT university FROM {$userTable} WHERE id = :id LIMIT 1";
            $result = $this->query($sql, ['id' => $userId]);
            if ($result && count($result) > 0) {
                return $result[0]->university;
            }
        }
        return null;
    }
    
    /**
     * Validate event data
     */
    public function validate($data) {
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
        
        if (empty($data['max_participants']) || !is_numeric($data['max_participants'])) {
            $errors['max_participants'] = 'Valid maximum participants number is required';
        }
        
        return $errors;
    }
    
    /**
     * Create new event
     */
    public function createEvent($data) {
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
        
        // Remove any non-allowed columns
        $filteredData = array_intersect_key($data, array_flip($this->allowedColumns));
        
        if ($this->insert($filteredData)) {
            return ['success' => true, 'message' => 'Event created successfully'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to create event']];
    }
}