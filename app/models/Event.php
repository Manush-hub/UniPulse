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
        'current_participants',
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
        $whereClause = ['is_deleted = 0']; // Exclude soft-deleted events
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
     * Get events that are seeking sponsors
     * These are typically upcoming events that accept donations or need funding
     */
    public function getEventsSeekingSponsors($filters = []) {
        $whereClause = ['status = :status', 'is_deleted = 0']; // Exclude soft-deleted events
        $params = ['status' => 'upcoming'];
        
        // Apply filters
        if (!empty($filters['category'])) {
            $whereClause[] = 'category = :category';
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['university'])) {
            $whereClause[] = 'university = :university';
            $params['university'] = $filters['university'];
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
     * Get events based on user role and permissions
     * Non-user roles (publisher, admin, moderator, sponsor) can see completed events
     * Regular users cannot see completed events unless specifically requested
     */
    public function getEventsByRole($userRole = 'user', $filters = []) {
        $allowCompletedEvents = in_array($userRole, ['publisher', 'admin', 'moderator', 'sponsor']);
        
        // If user role can't see completed events and no specific status filter is set
        if (!$allowCompletedEvents && !isset($filters['status'])) {
            // Add filter to exclude completed events
            $whereClause = [];
            $params = [];
            
            // Apply existing filters
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'category':
                            $whereClause[] = 'category = :category';
                            $params['category'] = $value;
                            break;
                        case 'university':
                            $whereClause[] = 'university = :university';
                            $params['university'] = $value;
                            break;
                        case 'search':
                            $whereClause[] = '(title LIKE :search OR description LIKE :search OR university_name LIKE :search OR organizer LIKE :search OR location LIKE :search)';
                            $params['search'] = '%' . $value . '%';
                            break;
                    }
                }
            }
            
            // Exclude completed events for regular users
            $whereClause[] = "status != 'completed'";
            
            // Exclude soft-deleted events
            $whereClause[] = "is_deleted = 0";
            
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
        } else {
            // Use existing getAllEvents method for non-users roles or when status is specifically requested
            return $this->getAllEvents($filters);
        }
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
     * Update participant count (legacy - for backward compatibility)
     */
    public function updateParticipants($id, $newCount) {
        return $this->update($id, ['participants' => $newCount]);
    }
    
    /**
     * Update current participants count
     */
    public function updateCurrentParticipants($id, $newCount) {
        return $this->update($id, ['current_participants' => $newCount]);
    }
    
    /**
     * Increment current participants (when user registers or buys ticket)
     */
    public function incrementParticipants($id) {
        $event = $this->getEventById($id);
        if (!$event) {
            return false;
        }
        
        // Check if max_participants is set and if we've reached the limit
        if ($event->max_participants !== null && $event->current_participants >= $event->max_participants) {
            return false; // Event is full
        }
        
        return $this->updateCurrentParticipants($id, $event->current_participants + 1);
    }
    
    /**
     * Decrement current participants (when user cancels registration)
     */
    public function decrementParticipants($id) {
        $event = $this->getEventById($id);
        if (!$event || $event->current_participants <= 0) {
            return false;
        }
        
        return $this->updateCurrentParticipants($id, $event->current_participants - 1);
    }
    
    /**
     * Check if event has available spots
     */
    public function hasAvailableSpots($id) {
        $event = $this->getEventById($id);
        if (!$event) {
            return false;
        }
        
        // If max_participants is not set (NULL), unlimited spots available
        if ($event->max_participants === null) {
            return true;
        }
        
        return $event->current_participants < $event->max_participants;
    }
    
    /**
     * Get available spots count
     */
    public function getAvailableSpots($id) {
        $event = $this->getEventById($id);
        if (!$event) {
            return 0;
        }
        
        // If max_participants is not set (NULL), return null to indicate unlimited
        if ($event->max_participants === null) {
            return null;
        }
        
        return max(0, $event->max_participants - $event->current_participants);
    }
    
    /**
     * Join event (increment participant count)
     * @deprecated Use incrementParticipants() instead
     */
    public function joinEvent($id) {
        return $this->incrementParticipants($id);
    }
    
    /**
     * Leave event (decrement participant count)
     * @deprecated Use decrementParticipants() instead
     */
    public function leaveEvent($id) {
        return $this->decrementParticipants($id);
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
    
    /**
     * Get pending events for moderation by university
     */
    public function getPendingEventsForUniversity($university, $limit = 20) {
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
    public function getModerationStatsForUniversity($university) {
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
    public function approve($eventId, $moderatorId) {
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
    public function reject($eventId, $moderatorId, $reason = '') {
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
    private function createModerationNotification($eventId, $moderatorId, $type, $message = '') {
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
    public function softDelete($eventId, $moderatorId, $reason = '') {
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
     * Restore a soft-deleted event
     */
    public function restore($eventId) {
        try {
            $conn = $this->connect();
            
            $query = "UPDATE events 
                      SET is_deleted = 0,
                          deleted_at = NULL,
                          deleted_by = NULL,
                          deletion_reason = NULL,
                          updated_at = NOW()
                      WHERE id = :event_id";
            
            $stmt = $conn->prepare($query);
            return $stmt->execute(['event_id' => $eventId]);
        } catch (Exception $e) {
            error_log("restore error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get event with publisher details (for notifications)
     */
    public function getEventWithPublisher($eventId) {
        $query = "SELECT e.*, p.email as publisher_email, p.full_name as publisher_name, p.university as publisher_university
                  FROM events e
                  LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                  WHERE e.id = :event_id";
        
        $result = $this->query($query, ['event_id' => $eventId]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Notify publisher of event deletion
     */
    private function notifyPublisherOfDeletion($eventId, $moderatorId, $reason) {
        try {
            // Get event and publisher details
            $event = $this->getEventWithPublisher($eventId);
            
            if (!$event) {
                error_log("notifyPublisherOfDeletion: Event not found - $eventId");
                return false;
            }
            
            // Get moderator details
            $moderatorModel = new Moderator();
            $moderator = $moderatorModel->findById($moderatorId);
            
            // Create notification in database
            $conn = $this->connect();
            $query = "INSERT INTO event_moderation_notifications 
                      (event_id, moderator_id, notification_type, message, created_at) 
                      VALUES (:event_id, :moderator_id, 'deleted', :message, NOW())";
            
            $message = "Your event '{$event->title}' has been hidden by a moderator. Reason: {$reason}";
            
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
                'event_id' => $eventId,
                'moderator_id' => $moderatorId,
                'message' => $message
            ]);
            
            if ($result) {
                error_log("Publisher notification created for event $eventId");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("notifyPublisherOfDeletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if moderator can moderate this event (same university)
     */
    public function canModeratorModerateEvent($eventId, $moderatorUniversity) {
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
     * Get recent moderation activities
     */
    public function getRecentModerationActivities($moderatorId = null, $limit = 10) {
        try {
            $conn = $this->connect();
            
            // Build UNION query to get hidden events, approved publishers, rejected publishers, and pending publishers
            $query = "
                (SELECT 
                    e.id as item_id,
                    e.title as item_title,
                    e.deleted_at as activity_time,
                    e.deletion_reason as activity_reason,
                    m.full_name as moderator_name,
                    m.university_name as university,
                    p.society_name as related_name,
                    'hidden_event' as activity_type
                FROM events e
                LEFT JOIN moderators m ON e.deleted_by = m.id
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                WHERE e.is_deleted = 1";
            
            if ($moderatorId) {
                $query .= " AND e.deleted_by = :moderator_id1";
            }
            
            $query .= ")
                UNION ALL
                (SELECT 
                    pub.id as item_id,
                    pub.society_name as item_title,
                    pub.approved_at as activity_time,
                    NULL as activity_reason,
                    m.full_name as moderator_name,
                    m.university_name as university,
                    pub.society_name as related_name,
                    'publisher_approved' as activity_type
                FROM publishers pub
                LEFT JOIN moderators m ON pub.approved_by = m.id
                WHERE pub.approval_status = 'approved' AND pub.approved_at IS NOT NULL";
            
            if ($moderatorId) {
                $query .= " AND pub.approved_by = :moderator_id2";
            }
            
            $query .= ")
                UNION ALL
                (SELECT 
                    pub.id as item_id,
                    pub.society_name as item_title,
                    pub.approved_at as activity_time,
                    pub.rejection_reason as activity_reason,
                    m.full_name as moderator_name,
                    m.university_name as university,
                    pub.society_name as related_name,
                    'publisher_rejected' as activity_type
                FROM publishers pub
                LEFT JOIN moderators m ON pub.approved_by = m.id
                WHERE pub.approval_status = 'rejected' AND pub.approved_at IS NOT NULL";
            
            if ($moderatorId) {
                $query .= " AND pub.approved_by = :moderator_id3";
            }
            
            $query .= ")
                UNION ALL
                (SELECT 
                    pub.id as item_id,
                    pub.society_name as item_title,
                    pub.created_at as activity_time,
                    NULL as activity_reason,
                    NULL as moderator_name,
                    NULL as university,
                    pub.society_name as related_name,
                    'publisher_pending' as activity_type
                FROM publishers pub
                WHERE pub.approval_status = 'pending'
                )
                ORDER BY activity_time DESC
                LIMIT :limit";
            
            $stmt = $conn->prepare($query);
            
            if ($moderatorId) {
                $stmt->bindValue(':moderator_id1', $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id2', $moderatorId, PDO::PARAM_INT);
                $stmt->bindValue(':moderator_id3', $moderatorId, PDO::PARAM_INT);
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
