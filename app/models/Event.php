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
        'max_participants',
        'target_audience',
        'requirements',
        'schedule',
        'ticket_type',
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
        'image_url',
        'cover_image'
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
            $whereClause[] = '(title LIKE :search OR description LIKE :search OR university_name LIKE :search OR organizer LIKE :search OR location LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
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
        
        if (empty($data['organizer'])) {
            $errors['organizer'] = 'Organizer is required';  
        }
        
        if (empty($data['university'])) {
            $errors['university'] = 'University is required';
        }
        
        if (empty($data['university_name'])) {
            $errors['university_name'] = 'University name is required';
        }
        
        // Validate max_participants
        if (isset($data['max_participants'])) {
            if (!is_numeric($data['max_participants']) || $data['max_participants'] <= 0) {
                $errors['max_participants'] = 'Maximum participants must be a valid positive number';
            }
        } else {
            $errors['max_participants'] = 'Maximum participants is required';
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
        
        if (isset($data['ticket_types']) && is_array($data['ticket_types'])) {
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
    public function updateEvent($eventId, $data) {
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
    public function deleteEvent($eventId, $userId) {
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
    public function deleteEventAdmin($eventId) {
        try {
            // First get the event to check if it exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }
            
            // Delete the event
            $query = "DELETE FROM events WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Event deleted successfully'];
            } else {
                return ['success' => false, 'errors' => ['general' => 'Failed to delete event']];
            }
        } catch (Exception $e) {
            error_log("Database error in Event::deleteEventAdmin: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Hide event (admin only)
     */
    public function hideEvent($eventId) {
        try {
            // First check if the event exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }
            
            // Update the event to set status as hidden (using status field for now)
            $query = "UPDATE events SET status = 'hidden', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Event hidden successfully'];
            } else {
                return ['success' => false, 'errors' => ['general' => 'Failed to hide event']];
            }
        } catch (Exception $e) {
            error_log("Database error in Event::hideEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Show event (admin only)
     */
    public function showEvent($eventId) {
        try {
            // First check if the event exists
            $checkQuery = "SELECT id FROM events WHERE id = :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                return ['success' => false, 'errors' => ['general' => 'Event not found']];
            }
            
            // Update the event to set status back to upcoming (restore visibility)
            $query = "UPDATE events SET status = 'upcoming', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Event shown successfully'];
            } else {
                return ['success' => false, 'errors' => ['general' => 'Failed to show event']];
            }
        } catch (Exception $e) {
            error_log("Database error in Event::showEvent: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Database error: ' . $e->getMessage()]];
        }
    }
}