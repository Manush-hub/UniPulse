<?php

class SponsorEvents extends Controller{

    private $eventModel;
    private $sponsorshipProposalModel;
    private $sponsorPostModel;
    private $registrationModel;
    
    public function __construct() {
        parent::__construct();
        // Initialize models
        $this->eventModel = new Event();
        $this->sponsorshipProposalModel = new SponsorshipProposal();
        $this->sponsorPostModel = new SponsorPost();
        
        // Initialize registration model if it exists
        if (class_exists('EventRegistration')) {
            $this->registrationModel = new EventRegistration();
        }
    }

    public function index($a = '', $b = '' , $c = ''){
        
        // Default to sponsor browse view - show events seeking sponsors
        return $this->browseForSponsors();
    }
    
    /**
     * API endpoint to get events as JSON
     */
    public function getEvents() {
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
            $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 6;
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Get events from database
            $events = $this->eventModel->getAllEvents($filters);
            
            // Format events for JSON response
            $formattedEvents = [];
            foreach ($events as $event) {
                $formattedEvent = $this->formatEventForResponse($event);
                $formattedEvents[] = $formattedEvent;
            }
            
            echo json_encode([
                'success' => true,
                'events' => $formattedEvents,
                'pagination' => [
                    'currentPage' => $page,
                    'limit' => $limit,
                    'hasMore' => count($events) == $limit
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
                    'limit' => 6,
                    'hasMore' => false
                ]
            ]);
        }
        
        exit;
    }
    
    /**
     * Helper method to format event data for API responses
     */
    private function formatEventForResponse($event) {
        $formattedEvent = (array) $event;
        
        // Decode JSON fields
        if (isset($formattedEvent['requirements']) && is_string($formattedEvent['requirements'])) {
            $formattedEvent['requirements'] = json_decode($formattedEvent['requirements'], true) ?: [];
        }
        if (isset($formattedEvent['schedule']) && is_string($formattedEvent['schedule'])) {
            $formattedEvent['schedule'] = json_decode($formattedEvent['schedule'], true) ?: [];
        }
        
        // Format date and time for frontend
        if (isset($formattedEvent['event_date'])) {
            $formattedEvent['date'] = $formattedEvent['event_date'];
        }
        
        if (isset($formattedEvent['event_time'])) {
            $formattedEvent['time'] = date('h:i A', strtotime($formattedEvent['event_time']));
        }
        
        return $formattedEvent;
    }
    
    /**
     * Browse events that are seeking sponsors
     */
    private function browseForSponsors() {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        // Temporarily disable auth for testing
        /*if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }*/
        
        // Set a default user for testing
        if (!$currentUser) {
            $currentUser = ['id' => 1, 'type' => 'sponsor', 'company_name' => 'Test Sponsor'];
        }

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
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 9; // Events per page
            $offset = ($page - 1) * $limit;
            
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
            
            // Debug log
            error_log("Browse For Sponsors - Filters: " . json_encode($filters));
            
            // Get events seeking sponsors from database
            $events = $this->eventModel->getEventsSeekingSponsors($filters);
            
            // Debug log results
            error_log("Browse For Sponsors - Found " . (is_array($events) ? count($events) : 0) . " events");
            
            // Ensure events is always an array
            if (!is_array($events)) {
                $events = [];
            }
            
            // Remove duplicate events by ID
            $seenIds = [];
            $uniqueEvents = [];
            foreach ($events as $event) {
                if (!in_array($event->id, $seenIds)) {
                    $seenIds[] = $event->id;
                    $uniqueEvents[] = $event;
                }
            }
            $events = $uniqueEvents;
            
            // Get total count for pagination
            $totalFilters = $filters;
            unset($totalFilters['limit'], $totalFilters['offset']);
            $totalEvents = $this->eventModel->getEventsSeekingSponsors($totalFilters);
            if (!is_array($totalEvents)) {
                $totalEvents = [];
            }
            
            // Remove duplicate events from total count
            $seenTotalIds = [];
            $uniqueTotalEvents = [];
            foreach ($totalEvents as $event) {
                if (!in_array($event->id, $seenTotalIds)) {
                    $seenTotalIds[] = $event->id;
                    $uniqueTotalEvents[] = $event;
                }
            }
            $totalEvents = $uniqueTotalEvents;
            
            $totalPages = count($totalEvents) > 0 ? ceil(count($totalEvents) / $limit) : 1;
            
            // Prepare data for view
            $data = [
                'events' => $events,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'user' => $currentUser,
                'page_title' => 'Find Events to Sponsor'
            ];
            
        } catch (Exception $e) {
            // Log error and show user-friendly message
            error_log("Database error in SponsorEvents::browse: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Show detailed error in development
            $errorMsg = 'Unable to load events. Please try again later.';
            if (defined('DEBUG') && DEBUG) {
                $errorMsg .= '<br><strong>Debug:</strong> ' . $e->getMessage();
            }
            
            $data = [
                'error' => $errorMsg,
                'events' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => [],
                'user' => $currentUser,
                'page_title' => 'Find Events to Sponsor'
            ];
        }
        
        $this->view('Sponsor/browse-events', $data);
    }

    /**
     * View detailed information about a specific event
     */
    public function viewEvent($eventId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit;
        }

        if (empty($eventId)) {
            $error = 'Event ID is required';
            $this->view('Sponsor/eventview', [
                'error' => $error,
                'serverData' => ['success' => false, 'error' => $error],
                'user' => $currentUser,
                'page_title' => 'Event Details'
            ]);
            return;
        }

        try {
            // Fetch event details
            $event = $this->eventModel->getEventById($eventId);
            
            if (!$event) {
                $error = 'Event not found';
                $this->view('Sponsor/eventview', [
                    'error' => $error,
                    'serverData' => ['success' => false, 'error' => $error],
                    'user' => $currentUser,
                    'page_title' => 'Event Details'
                ]);
                return;
            }

            // Prepare data for view
            $data = [
                'event' => $event,
                'serverData' => [
                    'event' => (array)$event,
                    'success' => true
                ],
                'user' => $currentUser,
                'page_title' => htmlspecialchars($event->title) . ' - Event Details'
            ];

            $this->view('Sponsor/eventview', $data);

        } catch (Exception $e) {
            error_log("Error viewing event: " . $e->getMessage());
            
            $error = 'Unable to load event details. Please try again later.';
            $this->view('Sponsor/eventview', [
                'error' => $error,
                'serverData' => ['success' => false, 'error' => $error],
                'user' => $currentUser,
                'page_title' => 'Event Details'
            ]);
        }
    }
    
    /**
     * Create a new sponsor post for an event
     */
    public function createPost($eventId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsorId = $currentUser['id'];
        
        // Validate event exists and sponsor can post
        if (!$eventId || !is_numeric($eventId)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
                exit;
            } else {
                header('Location: /unipulse/public/sponsor/events');
                exit();
            }
        }
        
        $event = $this->eventModel->getEventById($eventId);
        if (!$event) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Event not found']);
                exit;
            } else {
                header('Location: /unipulse/public/sponsor/events');
                exit();
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->submitPost($eventId, $sponsorId, $event);
            exit; // Ensure we never fall through
        } else {
            // Show post creation form
            $this->view('Sponsor/create-post', [
                'user' => $currentUser,
                'event' => $event,
                'page_title' => 'Create Sponsor Post - ' . $event->title
            ]);
        }
    }
    
    /**
     * Submit sponsor post
     */
    private function submitPost($eventId, $sponsorId, $event) {
        // Clear any previous output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        header('Content-Type: application/json');
        
        try {
            // Validate sponsor profile is complete
            $profileValidation = SponsorPost::validateSponsorProfile($sponsorId);
            if (!$profileValidation['valid']) {
                ob_end_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $profileValidation['message']]);
                exit;
            }
            
            // Get POST data
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $website_url = trim($_POST['website_url'] ?? '');
            $call_to_action_text = trim($_POST['cta_text'] ?? '');
            $call_to_action_url = trim($_POST['cta_url'] ?? '');
            
            // Validation
            if (empty($title) || empty($content)) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Title and content are required']);
                exit;
            }
            
            // Validate content against guidelines
            $contentValidation = SponsorPost::validateContent($title, $content);
            if (!$contentValidation['valid']) {
                ob_end_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Content violates guidelines',
                    'errors' => $contentValidation['errors'] ?? []
                ]);
                exit;
            }
            
            // Handle image upload if provided
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadPostImage($_FILES['image']);
                if (!$uploadResult['success']) {
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
                $image_url = $uploadResult['path'];
            }
            
            // Handle logo upload if provided
            $brand_logo_url = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadPostImage($_FILES['logo'], 'logo');
                if (!$uploadResult['success']) {
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                    exit;
                }
                $brand_logo_url = $uploadResult['path'];
            }
            
            // Validate URLs
            if (!empty($website_url) && !filter_var($website_url, FILTER_VALIDATE_URL)) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Invalid website URL']);
                exit;
            }
            
            if (!empty($call_to_action_url) && !filter_var($call_to_action_url, FILTER_VALIDATE_URL)) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Invalid CTA URL']);
                exit;
            }
            
            // Create post
            $sponsorPost = new SponsorPost();
            $sponsor = new Sponsor();
            $sponsorData = $sponsor->getSponsorById($sponsorId);
            
        $postData = [
            'event_id' => $eventId,
            'sponsor_id' => $sponsorId,
            'sponsor_name' => ($sponsorData && isset($sponsorData->company_name)) ? $sponsorData->company_name : 'Sponsor',
            'title' => $title,
            'content' => $content,
            'image_url' => $image_url,
            'brand_logo_url' => $brand_logo_url,
            'website_url' => $website_url,
            'call_to_action_text' => $call_to_action_text,
            'call_to_action_url' => $call_to_action_url
        ];
        
        $result = $sponsorPost->createPost($postData);
        
        if ($result['success']) {
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => $result['message'],
                'post_id' => $result['id'],
                'redirect' => "/unipulse/public/sponsor/events?view=sponsor"
            ]);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        
        exit;
        } catch (Exception $e) {
            ob_end_clean();
            error_log("Error in submitPost: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred while saving your post. Please try again.',
                'debug' => $e->getMessage()
            ]);
            exit;
        }
    }

    
    /**
     * Upload post image or logo
     */
    private function uploadPostImage($file, $type = 'image') {
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }
        
        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return ['success' => false, 'message' => 'File type not allowed. Use: ' . implode(', ', $allowed)];
        }
        
        // Create upload directory
        $uploadDir = ROOT_PATH . '/public/uploads/sponsor-posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = $type . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'message' => 'Failed to upload file'];
        }
        
        return [
            'success' => true,
            'path' => '/unipulse/public/uploads/sponsor-posts/' . $filename
        ];
    }
    
    /**
     * View sponsor's posts
     */
    public function myPosts($a = '', $b = '', $c = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsorPost = new SponsorPost();
        $status = $_GET['status'] ?? null;
        
        $posts = $sponsorPost->getPostsBySponsor($currentUser['id'], $status);
        
        $data = [
            'user' => $currentUser,
            'posts' => $posts,
            'status' => $status,
            'page_title' => 'My Sponsor Posts'
        ];
        
        $this->view('Sponsor/my-posts', $data);
    }
    
    /**
     * Edit sponsor post
     */
    public function editPost($postId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$postId || !is_numeric($postId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        $sponsorPost = new SponsorPost();
        $post = $sponsorPost->getPostById($postId);
        
        if (!$post || $post->sponsor_id != $currentUser['id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        // Allow editing if post is pending or was rejected (so sponsor can revise and resubmit)
        if (!in_array($post->approval_status, ['pending', 'rejected'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Can only edit pending or rejected posts']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updatePost($postId, $currentUser['id']);
        } else {
            $this->view('Sponsor/edit-post', [
                'user' => $currentUser,
                'post' => $post,
                'page_title' => 'Edit Sponsor Post'
            ]);
        }
    }
    
    /**
     * Update sponsor post
     */
    private function updatePost($postId, $sponsorId) {
        header('Content-Type: application/json');
        
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $call_to_action_text = trim($_POST['cta_text'] ?? '');
        $call_to_action_url = trim($_POST['cta_url'] ?? '');
        
        if (empty($title) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Title and content are required']);
            exit;
        }
        
        // Validate content
        $contentValidation = SponsorPost::validateContent($title, $content);
        if (!$contentValidation['valid']) {
            echo json_encode([
                'success' => false,
                'message' => 'Content violates guidelines',
                'errors' => $contentValidation['errors'] ?? []
            ]);
            exit;
        }
        
        $updateData = [
            'title' => $title,
            'content' => $content,
            'website_url' => $website_url,
            'call_to_action_text' => $call_to_action_text,
            'call_to_action_url' => $call_to_action_url
        ];
        
        $sponsorPost = new SponsorPost();
        $result = $sponsorPost->updatePost($postId, $sponsorId, $updateData);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Delete sponsor post
     */
    public function deletePost($postId = '') {
        header('Content-Type: application/json');
        
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$postId || !is_numeric($postId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        $sponsorPost = new SponsorPost();
        $result = $sponsorPost->deletePost($postId, $currentUser['id']);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Create sponsorship proposal
     */
    public function proposeTerms($eventId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $sponsorId = $currentUser['id'];
        
        // Validate event exists
        if (!$eventId || !is_numeric($eventId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
            exit;
        }
        
        $event = $this->eventModel->getEventById($eventId);
        if (!$event) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Event not found']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->submitProposal($eventId, $sponsorId, $event);
        } else {
            // Show proposal form
            $this->view('Sponsor/propose-terms', [
                'user' => $currentUser,
                'event' => $event,
                'page_title' => 'Propose Sponsorship Terms - ' . $event->title
            ]);
        }
    }
    
    /**
     * Submit sponsorship proposal
     */
    private function submitProposal($eventId, $sponsorId, $event) {
        header('Content-Type: application/json');
        
        // Get current user for sponsor name
        $currentUser = AuthService::getCurrentUser();
        
        // Get POST data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $proposalType = trim($_POST['proposal_type'] ?? 'mixed');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $contactPhone = trim($_POST['contact_phone'] ?? '');
        $contactEmail = trim($_POST['contact_email'] ?? '');
        
        // Validate required fields
        if (empty($title) || empty($description) || empty($contactPerson) || empty($contactEmail)) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
            exit;
        }
        
        // Validate email
        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        // Get proposal type specific data
        $proposalData = [
            'event_id' => $eventId,
            'sponsor_id' => $sponsorId,
            'sponsor_name' => $currentUser['company_name'] ?? 'Sponsor',
            'title' => $title,
            'description' => $description,
            'proposal_type' => $proposalType,
            'contact_person' => $contactPerson,
            'contact_phone' => $contactPhone,
            'contact_email' => $contactEmail,
            'status' => 'draft'
        ];
        
        // Add type-specific fields
        switch ($proposalType) {
            case 'monetary':
                $proposalData['monetary_amount'] = floatval($_POST['monetary_amount'] ?? 0);
                $proposalData['currency'] = $_POST['currency'] ?? 'USD';
                $proposalData['payment_schedule'] = trim($_POST['payment_schedule'] ?? '');
                break;
            case 'in-kind':
                $items = isset($_POST['in_kind_items']) ? explode("\n", $_POST['in_kind_items']) : [];
                $proposalData['in_kind_items'] = array_map('trim', array_filter($items));
                $proposalData['estimated_value'] = floatval($_POST['estimated_value'] ?? 0);
                break;
            case 'service':
                $proposalData['service_description'] = trim($_POST['service_description'] ?? '');
                $proposalData['service_duration'] = trim($_POST['service_duration'] ?? '');
                break;
            case 'mixed':
                if (!empty($_POST['monetary_amount'])) {
                    $proposalData['monetary_amount'] = floatval($_POST['monetary_amount']);
                    $proposalData['currency'] = $_POST['currency'] ?? 'USD';
                    $proposalData['payment_schedule'] = trim($_POST['payment_schedule'] ?? '');
                }
                if (!empty($_POST['in_kind_items'])) {
                    $items = explode("\n", $_POST['in_kind_items']);
                    $proposalData['in_kind_items'] = array_map('trim', array_filter($items));
                    $proposalData['estimated_value'] = floatval($_POST['estimated_value'] ?? 0);
                }
                if (!empty($_POST['service_description'])) {
                    $proposalData['service_description'] = trim($_POST['service_description']);
                    $proposalData['service_duration'] = trim($_POST['service_duration'] ?? '');
                }
                break;
        }
        
        // Add deliverables and benefits
        if (!empty($_POST['deliverables'])) {
            $deliverables = explode("\n", $_POST['deliverables']);
            $proposalData['deliverables'] = array_map('trim', array_filter($deliverables));
        }
        
        if (!empty($_POST['expected_benefits'])) {
            $benefits = explode("\n", $_POST['expected_benefits']);
            $proposalData['expected_benefits'] = array_map('trim', array_filter($benefits));
        }
        
        $result = $this->sponsorshipProposalModel->createProposal($proposalData);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Proposal created successfully',
                'proposal_id' => $result['id'],
                'redirect' => "/unipulse/public/sponsor/events/myProposals"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        
        exit;
    }
    
    /**
     * View sponsor's proposals
     */
    public function myProposals($a = '', $b = '', $c = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        $status = $_GET['status'] ?? null;
        $proposals = $this->sponsorshipProposalModel->getProposalsBySponsor($currentUser['id'], $status);
        
        $data = [
            'user' => $currentUser,
            'proposals' => $proposals,
            'status' => $status,
            'page_title' => 'My Sponsorship Proposals'
        ];
        
        $this->view('Sponsor/my-proposals', $data);
    }
    
    /**
     * View proposal details
     */
    public function viewProposal($proposalId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            http_response_code(400);
            echo "Invalid proposal ID";
            exit;
        }
        
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        
        if (!$proposal || $proposal->sponsor_id != $currentUser['id']) {
            http_response_code(403);
            echo "Unauthorized";
            exit;
        }
        
        $this->view('Sponsor/view-proposal', [
            'user' => $currentUser,
            'proposal' => $proposal,
            'page_title' => 'Sponsorship Proposal'
        ]);
    }
    
    /**
     * Edit proposal
     */
    public function editProposal($proposalId = '') {
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            header('Location: /unipulse/public/signin');
            exit();
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        $proposal = $this->sponsorshipProposalModel->getProposalById($proposalId);
        
        if (!$proposal || $proposal->sponsor_id != $currentUser['id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if ($proposal->status !== 'draft') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Can only edit draft proposals']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updateProposal($proposalId, $currentUser['id']);
        } else {
            $this->view('Sponsor/edit-proposal', [
                'user' => $currentUser,
                'proposal' => $proposal,
                'page_title' => 'Edit Sponsorship Proposal'
            ]);
        }
    }
    
    /**
     * Update proposal
     */
    private function updateProposal($proposalId, $sponsorId) {
        header('Content-Type: application/json');
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($title) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Title and description are required']);
            exit;
        }
        
        $updateData = [
            'title' => $title,
            'description' => $description,
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'contact_phone' => trim($_POST['contact_phone'] ?? ''),
            'contact_email' => trim($_POST['contact_email'] ?? '')
        ];
        
        $result = $this->sponsorshipProposalModel->updateProposal($proposalId, $sponsorId, $updateData);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Submit proposal for review
     */
    public function submitProposalForReview($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        $result = $this->sponsorshipProposalModel->submitProposal($proposalId, $currentUser['id']);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Delete proposal
     */
    public function deleteProposal($proposalId = '') {
        header('Content-Type: application/json');
        
        // Require sponsor authentication
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (!$proposalId || !is_numeric($proposalId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid proposal ID']);
            exit;
        }
        
        $result = $this->sponsorshipProposalModel->deleteProposal($proposalId, $currentUser['id']);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Join event endpoint
     */
    public function joinEvent($eventId = '') {
        header('Content-Type: application/json');
        
        // Get event ID from parameter or POST request
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
        
        // Check if sponsor is logged in
        $currentUser = AuthService::getCurrentUser();
        if (!$currentUser || $currentUser['type'] !== 'sponsor') {
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in as a sponsor to join events'
            ]);
            exit;
        }
        
        $sponsorId = $currentUser['id'];
        
        // Validate event ID is numeric
        if (!is_numeric($eventId)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid event ID format'
            ]);
            exit;
        }
        
        try {
            // Check if registration model is available
            if (!$this->registrationModel) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Registration system not available'
                ]);
                exit;
            }
            
            // Check if sponsor is already registered
            if ($this->registrationModel->isUserRegistered($eventId, $sponsorId, 'sponsor')) {
                echo json_encode([
                    'success' => false,
                    'alreadyRegistered' => true,
                    'error' => 'You have already registered for this event'
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
            $registrationData = [
                'event_id' => $eventId,
                'user_id' => $sponsorId,
                'user_type' => 'sponsor',
                'notes' => $_POST['notes'] ?? '',
                'status' => 'registered'
            ];
            
            if (!$this->registrationModel->registerUser($registrationData)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to create registration record'
                ]);
                exit;
            }
            
            // Join the event by incrementing current participants
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
                    'participants' => $updatedEvent->participants ?? $updatedEvent->current_participants,
                    'current_participants' => $updatedEvent->current_participants,
                    'max_participants' => $updatedEvent->max_participants,
                    'availableSpots' => $availableSpots
                ]);
            } else {
                // Rollback registration if increment fails
                $this->registrationModel->cancelRegistration($eventId, $sponsorId, 'sponsor');
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to join event. Event may be full or an error occurred.'
                ]);
            }
            
        } catch (Exception $e) {
            // Log error and return generic error message
            error_log("Database error in SponsorEvents::joinEvent: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to join event. Please try again later.'
            ]);
        }
        
        exit;
    }
}
