<?php

class PublisherCreateevent extends Controller
{

    private $eventModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index($a = '', $b = '', $c = '')
    {
        // Check if user is logged in and is a publisher
        $isLoggedIn = AuthService::isLoggedIn();
        $currentUser = AuthService::getCurrentUser();

        if (!$isLoggedIn || ($currentUser['type'] ?? '') !== 'publisher') {
            // For AJAX requests, return JSON error instead of redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'You must be logged in as a publisher to create events. Please login and try again.'],
                    'redirect' => '/unipulse/public/signin'
                ]);
                exit();
            }

            header('Location: /unipulse/public/signin');
            exit();
        }

        $data = [
            'currentUser' => $currentUser
        ];

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
            return;
        }

        $this->view('createevent', $data);
    }

    private function handleFormSubmission()
    {
        // Start output buffering to prevent any accidental output
        ob_start();

        // More robust AJAX detection
        $isAjax = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
            (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (isset($_POST['ajax']) && $_POST['ajax'] == '1')
        );

        // Set JSON header early for AJAX requests
        if ($isAjax) {
            // Clear any previous output
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
        }

        try {
            $user = AuthService::getCurrentUser();

            // Validate form data
            $validationErrors = $this->validateFormData($_POST, $_FILES);
            if (!empty($validationErrors)) {
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Validation failed. Please check the errors below.',
                        'errors' => $validationErrors
                    ]);
                    exit();
                } else {
                    $data = [
                        'errors' => $validationErrors,
                        'old_data' => $_POST
                    ];
                    $this->view('createevent', $data);
                    return;
                }
            }

            // Get form data
            $locationType = $_POST['location-type'] ?? 'inside-university';

            // Set location based on location type
            $location = '';
            $university = '';
            $universityName = '';
            $facultyDepartment = $_POST['faculty_department'] ?? '';
            $visibility = $this->mapVisibilityValue($_POST['event_visibility'] ?? 'university-only');

            if ($locationType === 'outside-university') {
                // For outside university, use venue_name as primary location
                $location = $_POST['venue_name'] ?? '';
                // For outside events, use publisher's university or set as 'external'
                $university = $user['university'] ?? 'external';
                $universityName = 'External Event';
            } else {
                // For inside university, use event_location
                $location = $_POST['event_location'] ?? '';
                $university = $_POST['selected_university'] ?? ($user['university'] ?? 'unknown');
                $universityName = $this->getUniversityName($university);
            }

            if (in_array($visibility, ['faculty-only', 'university-only'])) {
                $publisherUniversity = $user['university'] ?? null;
                $publisherFaculty = $user['faculty'] ?? null;

                if (empty($publisherUniversity) || ($visibility === 'faculty-only' && empty($publisherFaculty))) {
                    $publisherModel = new Publisher();
                    $publisher = $publisherModel->findById($user['id'] ?? 0);
                    if ($publisher) {
                        $publisherUniversity = $publisherUniversity ?: $publisher->university;
                        $publisherFaculty = $publisherFaculty ?: $publisher->faculty;
                    }
                }

                if (!empty($publisherUniversity)) {
                    $university = $publisherUniversity;
                    if ($locationType === 'inside-university') {
                        $universityName = $this->getUniversityName($university);
                    }
                }

                if ($visibility === 'faculty-only' && !empty($publisherFaculty)) {
                    $facultyDepartment = $publisherFaculty;
                }
            }

            $formData = [
                'title' => $_POST['event_name'] ?? '',
                'description' => $_POST['event_description'] ?? '',
                'category' => $_POST['event_category'] ?? '',
                'event_date' => $_POST['event_date'] ?? '',
                'event_time' => $_POST['event_time'] ?? '',
                'event_end_time' => !empty($_POST['event_end_time']) ? $_POST['event_end_time'] : null,
                'location' => $location,
                'location_type' => $locationType,
                'venue_name' => $_POST['venue_name'] ?? '',
                'street_address' => $_POST['street_address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'district_province' => $_POST['district_province'] ?? '',
                'faculty_department' => $facultyDepartment,
                'university' => $university,
                'university_name' => $universityName,
                'organizer' => $user['name'] ?? $user['full_name'] ?? '',
                'organizer_email' => $user['email'] ?? '',
                'max_participants' => null,
                'participants' => 0,
                'status' => 'upcoming',
                'ticket_type' => $_POST['ticketType'] ?? 'free-all',
                'registration_limit' => !empty($_POST['registration_limit']) ? (int)$_POST['registration_limit'] : null,
                'needs_volunteers' => isset($_POST['volunteerToggle']) && $_POST['volunteerToggle'] == '1' ? 1 : 0,
                'volunteers_needed' => !empty($_POST['volunteers_needed']) ? (int)$_POST['volunteers_needed'] : null,
                'accepts_donations' => isset($_POST['donationToggle']) && $_POST['donationToggle'] == '1' ? 1 : 0,
                'accepts_sponsorships' => isset($_POST['sponsorshipToggle']) && $_POST['sponsorshipToggle'] == '1' ? 1 : 0,
                'sponsorship_bank_name' => $_POST['sponsorship_bank_name'] ?? null,
                'sponsorship_account_name' => $_POST['sponsorship_account_name'] ?? null,
                'sponsorship_account_number' => $_POST['sponsorship_account_number'] ?? null,
                'sponsorship_branch' => $_POST['sponsorship_branch'] ?? null,
                'sponsorship_swift_code' => $_POST['sponsorship_swift_code'] ?? null,
                'sponsorship_instructions' => $_POST['sponsorship_instructions'] ?? null,
                'sponsorship_proposal' => null, // Will be set after file upload
                'created_by' => $user['id'] ?? null,
                'created_by_type' => 'publisher', // Always set to publisher for events created in publisher section
                'visibility' => $visibility
            ];

            // Handle registration/sale dates based on ticket type
            $ticketType = $_POST['ticketType'] ?? 'free-all';

            if ($ticketType === 'free-all') {
                // For free events, use registration dates (convert empty strings to null)
                $formData['registration_start_date'] = !empty($_POST['registration_start_date']) ? $_POST['registration_start_date'] : null;
                $formData['registration_start_time'] = !empty($_POST['registration_start_time']) ? $_POST['registration_start_time'] : null;
                $formData['registration_end_date'] = !empty($_POST['registration_end_date']) ? $_POST['registration_end_date'] : null;
                $formData['registration_end_time'] = !empty($_POST['registration_end_time']) ? $_POST['registration_end_time'] : null;

                // Set requires_registration based on whether registration period is filled
                $formData['requires_registration'] = (!empty($_POST['registration_start_date']) ||
                    !empty($_POST['registration_end_date']) ||
                    !empty($_POST['registration_start_time']) ||
                    !empty($_POST['registration_end_time'])) ? 1 : 0;
            } elseif ($ticketType === 'paid-all') {
                // For paid events, map sale dates to registration dates (convert empty strings to null)
                $formData['registration_start_date'] = !empty($_POST['sale_start_date']) ? $_POST['sale_start_date'] : null;
                $formData['registration_start_time'] = !empty($_POST['sale_start_time']) ? $_POST['sale_start_time'] : null;
                $formData['registration_end_date'] = !empty($_POST['sale_end_date']) ? $_POST['sale_end_date'] : null;
                $formData['registration_end_time'] = !empty($_POST['sale_end_time']) ? $_POST['sale_end_time'] : null;

                // Paid events always require registration (ticket purchase)
                $formData['requires_registration'] = 1;
            } elseif ($ticketType === 'mixed') {
                // For mixed events, use registration dates for university students
                // and sale dates for outside users (we'll use sale dates as primary, convert empty strings to null)
                $regStartDate = !empty($_POST['mixed_registration_start_date']) ? $_POST['mixed_registration_start_date'] : null;
                $saleStartDate = !empty($_POST['mixed_sale_start_date']) ? $_POST['mixed_sale_start_date'] : null;
                $formData['registration_start_date'] = $regStartDate ?? $saleStartDate;

                $regStartTime = !empty($_POST['mixed_registration_start_time']) ? $_POST['mixed_registration_start_time'] : null;
                $saleStartTime = !empty($_POST['mixed_sale_start_time']) ? $_POST['mixed_sale_start_time'] : null;
                $formData['registration_start_time'] = $regStartTime ?? $saleStartTime;

                $regEndDate = !empty($_POST['mixed_registration_end_date']) ? $_POST['mixed_registration_end_date'] : null;
                $saleEndDate = !empty($_POST['mixed_sale_end_date']) ? $_POST['mixed_sale_end_date'] : null;
                $formData['registration_end_date'] = $regEndDate ?? $saleEndDate;

                $regEndTime = !empty($_POST['mixed_registration_end_time']) ? $_POST['mixed_registration_end_time'] : null;
                $saleEndTime = !empty($_POST['mixed_sale_end_time']) ? $_POST['mixed_sale_end_time'] : null;
                $formData['registration_end_time'] = $regEndTime ?? $saleEndTime;

                // Set requires_registration based on whether university registration period is filled
                $formData['requires_registration'] = (!empty($_POST['mixed_registration_start_date']) ||
                    !empty($_POST['mixed_registration_end_date']) ||
                    !empty($_POST['mixed_registration_start_time']) ||
                    !empty($_POST['mixed_registration_end_time'])) ? 1 : 0;
            }

            // Handle requirements if provided
            if (!empty($_POST['requirements'])) {
                $formData['requirements'] = explode("\n", $_POST['requirements']);
            }

            // Handle schedule if provided
            if (!empty($_POST['schedule'])) {
                $formData['schedule'] = json_decode($_POST['schedule'], true);
            }

            // Handle ticket types if provided
            if (!empty($_POST['ticket_types'])) {
                $formData['ticket_types'] = json_decode($_POST['ticket_types'], true);
            }

            // Handle custom fields if provided
            if (!empty($_POST['custom_fields'])) {
                $formData['custom_fields'] = json_decode($_POST['custom_fields'], true);
            }

            // Handle volunteer sources if volunteers are needed
            if ($formData['needs_volunteers']) {
                $volunteerSources = [];
                if (isset($_POST['volunteer-source'])) {
                    $volunteerSources = $_POST['volunteer-source'];
                }
                $formData['volunteer_sources'] = $volunteerSources;

                // Handle volunteer positions
                if (!empty($_POST['volunteer_positions'])) {
                    $formData['volunteer_positions'] = json_decode($_POST['volunteer_positions'], true);
                }
            }

            // Handle file upload for cover image
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['cover_image']);
                if ($uploadResult['success']) {
                    $formData['cover_image'] = $uploadResult['path'];
                    $formData['image_url'] = $uploadResult['path']; // Keep backward compatibility
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Handle sponsorship proposal file upload (required when accepts_sponsorships is true)
            if ($formData['accepts_sponsorships']) {
                error_log("Sponsorship is enabled, checking for proposal file");
                error_log("FILES array: " . print_r($_FILES, true));

                if (!isset($_FILES['sponsorship_proposal']) || $_FILES['sponsorship_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
                    error_log("No proposal file found or file upload error");
                    throw new Exception('Sponsorship proposal document is required when accepting sponsorships');
                }

                error_log("Processing proposal file upload");
                $uploadResult = $this->handleProposalUpload($_FILES['sponsorship_proposal']);
                error_log("Upload result: " . print_r($uploadResult, true));

                if ($uploadResult['success']) {
                    $formData['sponsorship_proposal'] = $uploadResult['path'];
                    error_log("Proposal path set to: " . $uploadResult['path']);
                } else {
                    throw new Exception($uploadResult['error']);
                }
            } elseif (isset($_FILES['sponsorship_proposal']) && $_FILES['sponsorship_proposal']['error'] === UPLOAD_ERR_OK) {
                // Optional upload if sponsorships not enabled
                error_log("Optional proposal file upload");
                $uploadResult = $this->handleProposalUpload($_FILES['sponsorship_proposal']);
                if ($uploadResult['success']) {
                    $formData['sponsorship_proposal'] = $uploadResult['path'];
                    error_log("Optional proposal path set to: " . $uploadResult['path']);
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Log final formData before creating event
            error_log("Final formData sponsorship_proposal: " . ($formData['sponsorship_proposal'] ?? 'NULL'));

            // Create the event
            $result = $this->eventModel->createEvent($formData);

            if ($result['success']) {
                $eventId = $result['event_id'];

                // Handle sponsorship packages if sponsorships are enabled
                if (!empty($_POST['sponsorship_packages']) && !empty($formData['accepts_sponsorships'])) {
                    $packagesJson = $_POST['sponsorship_packages'];
                    if ($packagesJson && $packagesJson !== '[]') {
                        $packages = json_decode($packagesJson, true);
                        if (is_array($packages) && count($packages) > 0) {
                            $this->saveSponsorshipPackages($eventId, $packages);
                        }
                    }
                }

                // Return JSON response for AJAX requests
                if ($isAjax) {
                    // Ensure no output before JSON
                    if (ob_get_level()) ob_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Event created successfully!',
                        'event_id' => $result['event_id'] ?? null,
                        'redirect' => '/unipulse/public/publisher/events'
                    ]);
                    exit();
                } else {
                    // Redirect to events page for non-AJAX requests
                    header('Location: /unipulse/public/publisher/events?success=Event created successfully');
                    exit();
                }
            } else {
                // Return error response
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create event. Please check the errors below.',
                        'errors' => $result['errors'] ?? ['general' => 'Unknown error occurred']
                    ]);
                    exit();
                } else {
                    // Show form with errors
                    $data = [
                        'errors' => $result['errors'],
                        'old_data' => $formData
                    ];
                    $this->view('createevent', $data);
                }
            }
        } catch (Exception $e) {
            error_log("Error creating event: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            if ($isAjax) {
                // Ensure clean JSON output
                if (ob_get_level()) ob_clean();
                echo json_encode([
                    'success' => false,
                    'errors' => ['general' => 'Server error: ' . $e->getMessage()],
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
                exit();
            } else {
                $data = [
                    'errors' => ['general' => 'Server error: ' . $e->getMessage()],
                    'old_data' => $_POST
                ];
                $this->view('createevent', $data);
            }
        }
    }

    private function handleImageUpload($file)
    {
        // Use relative path from controller (which is in app/controllers/Publisher/)
        // to reach the public directory at the project root
        $uploadDir = __DIR__ . '/../../../public/uploads/event_covers/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Please upload a valid image.'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('event_cover_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'uploads/event_covers/' . $filename];
        }

        return ['success' => false, 'error' => 'Failed to upload image.'];
    }

    private function handleProposalUpload($file)
    {
        // Upload directory for sponsorship proposals
        $uploadDir = __DIR__ . '/../../../public/uploads/sponsorship_proposals/';
        $allowedTypes = [
            'application/pdf',
            'application/msword', // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-powerpoint', // .ppt
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' // .pptx
        ];
        $maxSize = 10 * 1024 * 1024; // 10MB

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Please upload PDF, DOC, DOCX, PPT, or PPTX files only.'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size is 10MB.'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('sponsorship_proposal_') . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'uploads/sponsorship_proposals/' . $filename];
        }

        return ['success' => false, 'error' => 'Failed to upload proposal file.'];
    }

    private function getUniversityName($universityCode)
    {
        $universities = [
            'university-of-colombo' => 'University of Colombo',
            'university-of-moratuwa' => 'University of Moratuwa',
            'university-of-peradeniya' => 'University of Peradeniya',
            'university-of-sri-jayewardenepura' => 'University of Sri Jayewardenepura',
            'other' => 'Other University',
            'unknown' => 'Unknown University'
        ];

        return $universities[$universityCode] ?? $universityCode;
    }

    private function validateFormData($postData, $files)
    {
        $errors = [];

        // Validate Event Name
        if (empty($postData['event_name']) || trim($postData['event_name']) === '') {
            $errors['event_name'] = 'Event name is required';
        } elseif (strlen(trim($postData['event_name'])) < 3) {
            $errors['event_name'] = 'Event name must be at least 3 characters long';
        } elseif (strlen(trim($postData['event_name'])) > 200) {
            $errors['event_name'] = 'Event name must be less than 200 characters';
        }

        // Validate Event Description
        if (empty($postData['event_description']) || trim($postData['event_description']) === '') {
            $errors['event_description'] = 'Event description is required';
        } elseif (strlen(trim($postData['event_description'])) < 10) {
            $errors['event_description'] = 'Event description must be at least 10 characters long';
        } elseif (strlen(trim($postData['event_description'])) > 5000) {
            $errors['event_description'] = 'Event description must be less than 5000 characters';
        }

        // Validate Category
        if (empty($postData['event_category'])) {
            $errors['event_category'] = 'Event category is required';
        }

        // Validate Event Visibility
        if (empty($postData['event_visibility'])) {
            $errors['event_visibility'] = 'Event visibility is required';
        } else {
            $allowedVisibility = ['faculty-only', 'university-only', 'all-universities', 'public'];
            if (!in_array($postData['event_visibility'], $allowedVisibility)) {
                $errors['event_visibility'] = 'Invalid visibility option selected';
            }
        }


        // Validate Event Visibility
        if (empty($postData['event_visibility'])) {
            $errors['event_visibility'] = 'Event visibility is required';
        } else {
            $allowedVisibility = ['faculty-only', 'university-only', 'all-universities', 'public'];
            if (!in_array($postData['event_visibility'], $allowedVisibility)) {
                $errors['event_visibility'] = 'Invalid visibility option selected';
            }
        }

        // Validate Event Date
        if (empty($postData['event_date'])) {
            $errors['event_date'] = 'Event date is required';
        } else {
            $eventDate = strtotime($postData['event_date']);
            $today = strtotime(date('Y-m-d'));

            if ($eventDate < $today) {
                $errors['event_date'] = 'Event date cannot be in the past';
            }

            // Check if date is too far in the future (more than 2 years)
            $twoYearsFromNow = strtotime('+2 years');
            if ($eventDate > $twoYearsFromNow) {
                $errors['event_date'] = 'Event date cannot be more than 2 years in the future';
            }
        }

        // Validate Event Time
        if (empty($postData['event_time'])) {
            $errors['event_time'] = 'Event time is required';
        } elseif (!empty($postData['event_date'])) {
            // If event is today, check if time is in the future
            $eventDate = strtotime($postData['event_date']);
            $today = strtotime(date('Y-m-d'));

            if ($eventDate === $today) {
                $eventDateTime = strtotime($postData['event_date'] . ' ' . $postData['event_time']);
                if ($eventDateTime < time()) {
                    $errors['event_time'] = 'Event time cannot be in the past for today\'s events';
                }
            }
        }

        // Validate Location based on location type
        $locationType = $postData['location-type'] ?? 'inside-university';

        if ($locationType === 'inside-university') {
            // Validate University selection
            if (empty($postData['selected_university']) || trim($postData['selected_university']) === '') {
                $errors['selected_university'] = 'University selection is required for inside university events';
            }

            // Validate Faculty/Department
            if (empty($postData['faculty_department']) || trim($postData['faculty_department']) === '') {
                $errors['faculty_department'] = 'Faculty/Department is required for inside university events';
            }

            // Validate Event Location
            if (empty($postData['event_location']) || trim($postData['event_location']) === '') {
                $errors['event_location'] = 'Event location is required (e.g., Main Auditorium, Hall A)';
            }
        } else {
            // Outside university - venue name and city are required
            if (empty($postData['venue_name']) || trim($postData['venue_name']) === '') {
                $errors['venue_name'] = 'Venue name is required for outside university events';
            }
            if (empty($postData['city']) || trim($postData['city']) === '') {
                $errors['city'] = 'City is required for outside university events';
            }
        }

        // Validate Category
        if (empty($postData['event_category'])) {
            $errors['event_category'] = 'Event category is required';
        }

        // Validate Cover Image - NOW REQUIRED
        if (!isset($files['cover_image']) || $files['cover_image']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors['cover_image'] = 'Event cover image is required';
        } elseif ($files['cover_image']['error'] !== UPLOAD_ERR_OK) {
            $errors['cover_image'] = 'Error uploading cover image';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($files['cover_image']['type'], $allowedTypes)) {
                $errors['cover_image'] = 'Invalid image type. Please upload JPEG, PNG, GIF, or WebP';
            }

            if ($files['cover_image']['size'] > $maxSize) {
                $errors['cover_image'] = 'Image too large. Maximum size is 5MB';
            }
        }

        // Validate Ticket Type and related fields
        $ticketType = $postData['ticketType'] ?? 'free-all';

        if ($ticketType === 'paid-all' || $ticketType === 'mixed') {
            // Validate ticket types exist
            if (empty($postData['ticket_types'])) {
                $errors['ticket_types'] = 'At least one ticket type is required for paid events';
            } else {
                $ticketTypes = json_decode($postData['ticket_types'], true);
                if (empty($ticketTypes) || !is_array($ticketTypes)) {
                    $errors['ticket_types'] = 'Invalid ticket types data';
                } else {
                    foreach ($ticketTypes as $index => $ticket) {
                        if (empty($ticket['name'])) {
                            $errors["ticket_type_{$index}_name"] = "Ticket type " . ($index + 1) . ": Name is required";
                        }
                        if (empty($ticket['quantity']) || (int)$ticket['quantity'] < 1) {
                            $errors["ticket_type_{$index}_quantity"] = "Ticket type " . ($index + 1) . ": Quantity must be at least 1";
                        }
                        if (!isset($ticket['price']) || (float)$ticket['price'] < 0) {
                            $errors["ticket_type_{$index}_price"] = "Ticket type " . ($index + 1) . ": Price must be 0 or greater";
                        }
                    }
                }
            }
        }

        // Validate Sale/Registration Periods based on ticket type
        if ($ticketType === 'paid-all') {
            // Require sale period for paid events
            if (empty($postData['sale_start_date'])) {
                $errors['sale_start_date'] = 'Sale start date is required for paid events';
            }
            if (empty($postData['sale_start_time'])) {
                $errors['sale_start_time'] = 'Sale start time is required for paid events';
            }
            if (empty($postData['sale_end_date'])) {
                $errors['sale_end_date'] = 'Sale end date is required for paid events';
            }
            if (empty($postData['sale_end_time'])) {
                $errors['sale_end_time'] = 'Sale end time is required for paid events';
            }
        } elseif ($ticketType === 'mixed') {
            // Registration period for university students is optional (not required)

            // Require sale period for outside users
            if (empty($postData['mixed_sale_start_date'])) {
                $errors['mixed_sale_start_date'] = 'Sale start date is required for outside users';
            }
            if (empty($postData['mixed_sale_start_time'])) {
                $errors['mixed_sale_start_time'] = 'Sale start time is required for outside users';
            }
            if (empty($postData['mixed_sale_end_date'])) {
                $errors['mixed_sale_end_date'] = 'Sale end date is required for outside users';
            }
            if (empty($postData['mixed_sale_end_time'])) {
                $errors['mixed_sale_end_time'] = 'Sale end time is required for outside users';
            }
        }

        // Validate Registration/Sale Periods
        $today = strtotime(date('Y-m-d'));
        $eventDate = !empty($postData['event_date']) ? strtotime($postData['event_date']) : null;

        // Validate Registration Start Date
        if (!empty($postData['registration_start_date']) && $eventDate) {
            $regStart = strtotime($postData['registration_start_date']);

            // Registration start must be today or in the future
            if ($regStart < $today) {
                $errors['registration_start_date'] = 'Registration start date cannot be in the past (must be today or later)';
            }

            // Registration start must be before or on event date
            if ($regStart > $eventDate) {
                $errors['registration_start_date'] = 'Registration start date cannot be after the event date';
            }
        }

        // Validate Registration End Date
        if (!empty($postData['registration_end_date'])) {
            $regEnd = strtotime($postData['registration_end_date']);

            // Registration end must not be in the past
            if ($regEnd < $today) {
                $errors['registration_end_date'] = 'Registration end date cannot be in the past';
            }

            // Registration end must be before or on event date
            if ($eventDate && $regEnd > $eventDate) {
                $errors['registration_end_date'] = 'Registration period must close before or on the event date';
            }

            // Registration end must be after start
            if (!empty($postData['registration_start_date'])) {
                $regStart = strtotime($postData['registration_start_date']);
                if ($regEnd < $regStart) {
                    $errors['registration_end_date'] = 'Registration end date must be after start date';
                }
            }
        }

        // Validate complete registration period
        if (!empty($postData['registration_start_date']) && !empty($postData['registration_end_date']) && $eventDate) {
            $regStart = strtotime($postData['registration_start_date']);
            $regEnd = strtotime($postData['registration_end_date']);

            // Registration period must be between publish date (today) and event date
            if ($regStart < $today || $regEnd > $eventDate) {
                $errors['registration_period'] = 'Registration period must be between today (publish date) and the event date';
            }
        }

        // Validate Ticket Sale Periods (similar logic)
        if (!empty($postData['sale_start_date']) && $eventDate) {
            $saleStart = strtotime($postData['sale_start_date']);

            if ($saleStart < $today) {
                $errors['sale_start_date'] = 'Ticket sale start date cannot be in the past (must be today or later)';
            }

            if ($saleStart > $eventDate) {
                $errors['sale_start_date'] = 'Ticket sale start date cannot be after the event date';
            }
        }

        if (!empty($postData['sale_end_date'])) {
            $saleEnd = strtotime($postData['sale_end_date']);

            if ($saleEnd < $today) {
                $errors['sale_end_date'] = 'Ticket sale end date cannot be in the past';
            }

            if ($eventDate && $saleEnd > $eventDate) {
                $errors['sale_end_date'] = 'Ticket sale must end before or on the event date';
            }

            if (!empty($postData['sale_start_date'])) {
                $saleStart = strtotime($postData['sale_start_date']);
                if ($saleEnd < $saleStart) {
                    $errors['sale_end_date'] = 'Ticket sale end date must be after start date';
                }
            }
        }

        // Validate complete sale period
        if (!empty($postData['sale_start_date']) && !empty($postData['sale_end_date']) && $eventDate) {
            $saleStart = strtotime($postData['sale_start_date']);
            $saleEnd = strtotime($postData['sale_end_date']);

            // Sale period must be between publish date (today) and event date
            if ($saleStart < $today || $saleEnd > $eventDate) {
                $errors['sale_period'] = 'Ticket sale period must be between today (publish date) and the event date';
            }
        }

        // Validate Volunteers
        if (isset($postData['volunteerToggle']) && $postData['volunteerToggle'] == '1') {
            if (empty($postData['volunteers_needed'])) {
                $errors['volunteers_needed'] = 'Number of volunteers needed is required';
            } elseif (!is_numeric($postData['volunteers_needed']) || (int)$postData['volunteers_needed'] < 1) {
                $errors['volunteers_needed'] = 'Number of volunteers must be at least 1';
            } elseif ((int)$postData['volunteers_needed'] > 1000) {
                $errors['volunteers_needed'] = 'Number of volunteers cannot exceed 1,000';
            }

            // Check if at least one volunteer source is selected
            if (empty($postData['volunteer-source']) || !is_array($postData['volunteer-source'])) {
                $errors['volunteer_sources'] = 'Please select at least one volunteer source';
            }
        }

        // Validate Sponsorship
        if (isset($postData['sponsorshipToggle']) && $postData['sponsorshipToggle'] == '1') {
            // Bank details are required when sponsorship is enabled
            if (empty($postData['sponsorship_bank_name']) || trim($postData['sponsorship_bank_name']) === '') {
                $errors['sponsorship_bank_name'] = 'Bank name is required when requesting sponsorships';
            }

            if (empty($postData['sponsorship_account_name']) || trim($postData['sponsorship_account_name']) === '') {
                $errors['sponsorship_account_name'] = 'Account holder name is required when requesting sponsorships';
            }

            if (empty($postData['sponsorship_account_number']) || trim($postData['sponsorship_account_number']) === '') {
                $errors['sponsorship_account_number'] = 'Account number is required when requesting sponsorships';
            }

            // Optional: Validate that at least one package is added
            if (empty($postData['sponsorship_packages']) || $postData['sponsorship_packages'] === '[]') {
                $errors['sponsorship_packages'] = 'Please add at least one sponsorship package';
            }
        }

        return $errors;
    }

    /**
     * Maps form visibility values to database ENUM values
     * With new visibility categories, all 4 values are supported directly
     * Form sends: faculty-only, university-only, all-universities, public
     * Database accepts: faculty-only, university-only, all-universities, public
     * 
     * @param string $formValue The visibility value from the form
     * @return string The mapped database ENUM value
     */
    private function mapVisibilityValue($formValue)
    {
        $allowedValues = ['faculty-only', 'university-only', 'all-universities', 'public'];

        // Return value if valid, otherwise default to university-only
        return in_array($formValue, $allowedValues) ? $formValue : 'university-only';
    }

    /**
     * Save sponsorship packages for an event
     * 
     * @param int $eventId The event ID
     * @param array $packages Array of sponsorship packages
     * @return bool Success status
     */
    private function saveSponsorshipPackages($eventId, $packages)
    {
        try {
            // Use the event model to access database
            foreach ($packages as $index => $package) {
                $sql = "INSERT INTO event_sponsorship_packages 
                        (event_id, package_name, package_type, amount, description, 
                         benefits, terms_conditions, available_slots, display_order, is_active) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

                $params = [
                    $eventId,
                    $package['name'] ?? '',
                    $package['type'] ?? 'custom',
                    $package['amount'] ?? 0,
                    $package['description'] ?? null,
                    $package['benefits'] ?? null,
                    $package['terms'] ?? null,
                    $package['slots'] ?? 1,
                    $index // Use array index as display order
                ];

                $this->eventModel->query($sql, $params);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error saving sponsorship packages: " . $e->getMessage());
            return false;
        }
    }
}
