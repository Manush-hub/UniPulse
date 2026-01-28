<?php

class PublisherDashboard extends Controller{

    use Database;

    public function index($a = '', $b = '' , $c = ''){
        // Allow publishers, admins, and moderators
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        // Pass user data to view
        $data = [
            'user' => $currentUser
        ];
        
        $this->view('Publisher/dashboard', $data);
    }

    /**
     * Get recent comments for dashboard
     */
    public function getRecentComments() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get recent comments on publisher's events
            $query = "
                SELECT 
                    c.*,
                    e.title as event_title,
                    e.status as event_status,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.full_name
                        WHEN c.user_type = 'public' THEN pu.full_name
                        WHEN c.user_type = 'publisher' THEN pub.society_name
                        WHEN c.user_type = 'sponsor' THEN s.company_name
                        WHEN c.user_type = 'admin' THEN 'Admin'
                        WHEN c.user_type = 'moderator' THEN m.full_name
                    END as user_name,
                    CASE 
                        WHEN c.user_type = 'university' THEN uu.email
                        WHEN c.user_type = 'public' THEN pu.email
                        WHEN c.user_type = 'publisher' THEN pub.email
                        WHEN c.user_type = 'sponsor' THEN s.email
                        WHEN c.user_type = 'admin' THEN 'system@unipulse.com'
                        WHEN c.user_type = 'moderator' THEN m.email
                    END as user_email
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                LEFT JOIN university_users uu ON c.user_type = 'university' AND c.user_id = uu.id
                LEFT JOIN public_users pu ON c.user_type = 'public' AND c.user_id = pu.id
                LEFT JOIN publishers pub ON c.user_type = 'publisher' AND c.user_id = pub.id
                LEFT JOIN sponsors s ON c.user_type = 'sponsor' AND c.user_id = s.id
                LEFT JOIN moderators m ON c.user_type = 'moderator' AND c.user_id = m.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
                ORDER BY c.created_at DESC
                LIMIT 10
            ";
            
            $stmt = $this->connect()->prepare($query);
            $stmt->execute(['publisher_id' => $currentUser['id']]);
            $comments = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Get comment statistics
            $statsQuery = "
                SELECT 
                    COUNT(c.id) as total_comments,
                    ROUND(AVG(CASE WHEN c.rating > 0 THEN c.rating END), 1) as average_rating
                FROM event_comments c
                LEFT JOIN events e ON c.event_id = e.id
                WHERE c.is_deleted = 0
                AND e.created_by_type = 'publisher'
                AND e.created_by = :publisher_id
            ";
            
            $statsStmt = $this->connect()->prepare($statsQuery);
            $statsStmt->execute(['publisher_id' => $currentUser['id']]);
            $stats = $statsStmt->fetch(PDO::FETCH_OBJ);
            
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id' => $comment->id,
                    'event_id' => $comment->event_id,
                    'event_title' => $comment->event_title,
                    'event_status' => $comment->event_status,
                    'user_name' => $comment->user_name,
                    'user_type' => $comment->user_type,
                    'comment_text' => $comment->comment_text,
                    'rating' => $comment->rating,
                    'created_at' => $comment->created_at,
                    'formatted_date' => $this->formatDate($comment->created_at)
                ];
            }
            
            echo json_encode([
                'success' => true,
                'comments' => $formattedComments,
                'stats' => [
                    'total_comments' => (int)$stats->total_comments,
                    'average_rating' => $stats->average_rating ? (float)$stats->average_rating : 0.0
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting recent comments: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load comments']);
        }
    }

    /**
     * Get publisher's events for dashboard
     */
    public function getMyEvents() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get filter from request (upcoming or past)
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            
            // Build query based on user role
            $whereClause = "";
            $params = [];
            
            if ($currentUser['type'] === 'publisher') {
                // Publishers see only their own events
                $whereClause = "WHERE e.created_by_type = 'publisher' AND e.created_by = :publisher_id";
                $params['publisher_id'] = $currentUser['id'];
            } else {
                // Admins and moderators see all events (including completed ones)
                $whereClause = "WHERE 1=1"; // No filtering
            }
            
            // Add date filter
            if ($filter === 'upcoming') {
                $whereClause .= " AND e.event_date >= CURDATE()";
            } elseif ($filter === 'past') {
                $whereClause .= " AND e.event_date < CURDATE()";
            }
            
            $query = "
                SELECT 
                    e.*,
                    p.society_name as organizer_name,
                    COUNT(ec.id) as comment_count,
                    COUNT(CASE WHEN ec.rating > 0 THEN 1 END) as rating_count,
                    AVG(CASE WHEN ec.rating > 0 THEN ec.rating END) as avg_rating
                FROM events e
                LEFT JOIN publishers p ON e.created_by = p.id AND e.created_by_type = 'publisher'
                LEFT JOIN event_comments ec ON e.id = ec.event_id AND ec.is_deleted = 0
                $whereClause
                GROUP BY e.id
                ORDER BY e.event_date DESC
                LIMIT 50
            ";
            
            $stmt = $this->connect()->prepare($query);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $formattedEvents = [];
            $currentDate = date('Y-m-d');
            
            foreach ($events as $event) {
                // Calculate actual status based on event date
                $eventStatus = $event->status;
                if ($event->event_date < $currentDate) {
                    $eventStatus = 'past';
                } elseif ($event->event_date == $currentDate) {
                    $eventStatus = 'ongoing';
                } elseif ($event->event_date > $currentDate) {
                    $eventStatus = 'upcoming';
                }
                
                $formattedEvents[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'event_date' => $event->event_date,
                    'event_time' => $event->event_time,
                    'location_type' => $event->location_type,
                    'exact_location' => $event->location ?? '', // Use 'location' field
                    'venue_name' => $event->venue_name ?? '',
                    'city' => $event->city ?? '',
                    'university_name' => $event->university_name ?? '',
                    'faculty_department' => $event->faculty_department ?? '',
                    'status' => $eventStatus,
                    'category' => $event->category,
                    'cover_image' => $event->cover_image ?? $event->image_url ?? '',
                    'organizer_name' => $event->organizer_name ?? $event->organizer ?? '',
                    'ticket_type' => $event->ticket_type ?? 'free-all',
                    'ticket_price_free' => 0, // Not stored separately in database
                    'ticket_price_paid' => 0, // Not stored separately in database
                    'current_participants' => $event->current_participants ?? 0,
                    'max_participants' => $event->max_participants ?? null,
                    'comment_count' => (int)($event->comment_count ?? 0),
                    'rating_count' => (int)($event->rating_count ?? 0),
                    'avg_rating' => $event->avg_rating ? round((float)$event->avg_rating, 1) : null
                ];
            }
            
            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'total' => count($formattedEvents)
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting publisher events: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load events']);
        }
    }

    /**
     * Format date for display
     */
    private function formatDate($dateString) {
        $date = new DateTime($dateString);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                return $diff->i . ' minutes ago';
            }
            return $diff->h . ' hours ago';
        } elseif ($diff->days == 1) {
            return 'Yesterday';
        } elseif ($diff->days < 7) {
            return $diff->days . ' days ago';
        } else {
            return $date->format('M j, Y');
        }
    }

    /**
     * Get boost pricing tiers
     */
    public function getBoostPricing() {
        header('Content-Type: application/json');
        
        try {
            $query = "SELECT * FROM boost_pricing WHERE is_active = 1 ORDER BY duration_days ASC";
            $result = $this->query($query);
            
            echo json_encode([
                'success' => true,
                'pricing' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error getting boost pricing: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load pricing']);
        }
    }

    /**
     * Get publisher events for boosting
     */
    public function getEventsForBoosting() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            // Get publisher's upcoming events only (not ongoing, completed, or cancelled)
            $query = "
                SELECT 
                    e.id,
                    e.title,
                    e.event_date,
                    e.event_time,
                    e.status,
                    e.is_boosted,
                    e.boost_expires_at
                FROM events e
                WHERE e.created_by = :publisher_id 
                AND e.created_by_type = 'publisher'
                AND e.status = 'upcoming'
                AND e.is_deleted = 0
                AND e.event_date >= CURDATE()
                ORDER BY e.event_date ASC
            ";
            
            $events = $this->query($query, ['publisher_id' => $currentUser['id']]);
            
            // Debug logging
            error_log("Publisher ID: " . $currentUser['id']);
            error_log("Events found: " . count($events));
            
            echo json_encode([
                'success' => true,
                'events' => $events,
                'publisher_id' => $currentUser['id'],
                'count' => count($events)
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting events for boosting: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load events', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get active boosts
     */
    public function getActiveBoosts() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $query = "
                SELECT 
                    eb.*,
                    e.title as event_title,
                    e.event_date,
                    e.cover_image
                FROM event_boosts eb
                JOIN events e ON eb.event_id = e.id
                WHERE eb.publisher_id = :publisher_id
                AND eb.boost_status = 'active'
                AND eb.boost_end_date > NOW()
                AND eb.payment_status = 'completed'
                ORDER BY eb.boost_end_date ASC
            ";
            
            $boosts = $this->query($query, ['publisher_id' => $currentUser['id']]);
            
            // Calculate remaining time for each boost
            foreach ($boosts as &$boost) {
                $endDate = new DateTime($boost['boost_end_date']);
                $now = new DateTime();
                $diff = $now->diff($endDate);
                
                if ($diff->days > 0) {
                    $boost['time_remaining'] = $diff->days . ' days';
                } elseif ($diff->h > 0) {
                    $boost['time_remaining'] = $diff->h . ' hours';
                } else {
                    $boost['time_remaining'] = $diff->i . ' minutes';
                }
            }
            
            echo json_encode([
                'success' => true,
                'boosts' => $boosts
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting active boosts: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to load active boosts']);
        }
    }

    /**
     * Create a new boost request
     */
    public function createBoost() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $eventId = $data['event_id'] ?? null;
            $durationDays = $data['duration_days'] ?? null;
            $amount = $data['amount'] ?? null;
            $paymentMethod = $data['payment_method'] ?? 'card';
            
            if (!$eventId || !$durationDays || !$amount) {
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                return;
            }

            // Verify event belongs to publisher
            $eventQuery = "SELECT * FROM events WHERE id = :event_id AND created_by = :publisher_id AND created_by_type = 'publisher' AND is_deleted = 0";
            $event = $this->query($eventQuery, [
                'event_id' => $eventId,
                'publisher_id' => $currentUser['id']
            ]);
            
            if (empty($event)) {
                echo json_encode(['success' => false, 'error' => 'Event not found or unauthorized']);
                return;
            }

            // Calculate boost dates
            $startDate = new DateTime();
            $endDate = clone $startDate;
            $endDate->modify("+{$durationDays} days");
            
            // Generate transaction ID
            $transactionId = 'BOOST-' . time() . '-' . rand(1000, 9999);
            
            // Insert boost record
            $insertQuery = "
                INSERT INTO event_boosts 
                (event_id, publisher_id, boost_start_date, boost_end_date, duration_days, 
                 amount_paid, payment_method, transaction_id, payment_status, boost_status, priority_level)
                VALUES 
                (:event_id, :publisher_id, :start_date, :end_date, :duration_days,
                 :amount, :payment_method, :transaction_id, 'completed', 'active', 1)
            ";
            
            $this->query($insertQuery, [
                'event_id' => $eventId,
                'publisher_id' => $currentUser['id'],
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s'),
                'duration_days' => $durationDays,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId
            ]);
            
            // Update event boost status
            $updateEventQuery = "
                UPDATE events 
                SET is_boosted = 1, 
                    boost_expires_at = :expires_at,
                    boost_priority = 1
                WHERE id = :event_id
            ";
            
            $this->query($updateEventQuery, [
                'event_id' => $eventId,
                'expires_at' => $endDate->format('Y-m-d H:i:s')
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Event boosted successfully!',
                'transaction_id' => $transactionId,
                'boost_end_date' => $endDate->format('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            error_log("Error creating boost: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to create boost']);
        }
    }

    /**
     * Cancel an active boost
     */
    public function cancelBoost() {
        header('Content-Type: application/json');
        
        $currentUser = AuthService::getCurrentUser();
        $allowedRoles = ['publisher', 'admin', 'moderator'];
        
        if (!$currentUser || !in_array($currentUser['type'], $allowedRoles)) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $boostId = $data['boost_id'] ?? null;
            
            if (!$boostId) {
                echo json_encode(['success' => false, 'error' => 'Boost ID required']);
                return;
            }

            // Update boost status
            $query = "
                UPDATE event_boosts 
                SET boost_status = 'cancelled'
                WHERE id = :boost_id 
                AND publisher_id = :publisher_id
            ";
            
            $this->query($query, [
                'boost_id' => $boostId,
                'publisher_id' => $currentUser['id']
            ]);
            
            // Update event boost status if no other active boosts
            $checkQuery = "
                SELECT COUNT(*) as count 
                FROM event_boosts 
                WHERE event_id = (SELECT event_id FROM event_boosts WHERE id = :boost_id)
                AND boost_status = 'active'
                AND id != :boost_id
            ";
            
            $result = $this->query($checkQuery, ['boost_id' => $boostId]);
            
            if ($result[0]['count'] == 0) {
                $updateEventQuery = "
                    UPDATE events 
                    SET is_boosted = 0, 
                        boost_expires_at = NULL,
                        boost_priority = 0
                    WHERE id = (SELECT event_id FROM event_boosts WHERE id = :boost_id)
                ";
                
                $this->query($updateEventQuery, ['boost_id' => $boostId]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Boost cancelled successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("Error cancelling boost: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to cancel boost']);
        }
    }
}
