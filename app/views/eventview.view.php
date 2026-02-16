<?php
// Define role-specific content
$roleConfig = [
    'User' => [
        'jsFile' => '/unipulse/public/assets/js/User/eventview-app.js',
        'purchaseTicketFunction' => true,
        'visitProfileFunction' => false
    ],
    'Publisher' => [
        'jsFile' => '/unipulse/public/assets/js/Publisher/eventview-app.js',
        'purchaseTicketFunction' => false,
        'visitProfileFunction' => false
    ],
    'Sponsor' => [
        'jsFile' => '/unipulse/public/assets/js/Sponsor/eventview-app.js',
        'purchaseTicketFunction' => false,
        'visitProfileFunction' => true
    ]
];

// Get current role from data or default to 'User'
$currentRole = $userRole ?? 'User';
$config = $roleConfig[$currentRole] ?? $roleConfig['User'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/eventview-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/User/comments-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/' . $currentRole . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Loading State -->
        <div id="loadingContainer" class="loading-container">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <p>Loading event details...</p>
        </div>

        <!-- Error State -->
        <div id="errorContainer" class="error-container" <?php echo isset($error) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
            <div class="error-content">
                <div class="error-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h2><?php echo isset($error) ? htmlspecialchars($error) : 'Event not found'; ?></h2>
                <p>The event you're looking for doesn't exist or has been removed.</p>
                <a href="/unipulse/public/<?php echo strtolower($currentRole); ?>/events" class="btn btn-primary">Back to Events</a>
            </div>
        </div>

        <!-- Event Content -->
        <div id="eventContainer" class="event-container" style="display: none;">
            <!-- Event Hero Section -->
            <div class="event-hero" id="eventHero">
                <div class="hero-image-container" id="heroImageContainer" style="display: none;">
                    <img id="heroImage" src="" alt="Event Cover" class="hero-cover-image">
                    <div class="hero-overlay"></div>
                </div>
                <div class="container">
                    <div class="hero-content">
                        <div class="event-meta">
                            <span class="event-category" id="eventCategory">Loading...</span>
                            <span class="event-status" id="eventStatus">Loading...</span>
                        </div>
                        <h1 class="event-title" id="eventTitle">Loading Event Title...</h1>
                        
                        <div class="event-details-grid">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <strong>Date & Time</strong>
                                    <span id="eventDateTime">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item" id="exactLocationInfo" style="display: none;">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Exact Location</strong>
                                    <span id="eventLocation">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item" id="venueInfo" style="display: none;">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Location</strong>
                                    <span id="eventVenueCity">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item" id="universityInfo" style="display: none;">
                                <i class="fas fa-university"></i>
                                <div>
                                    <strong>University</strong>
                                    <span id="eventUniversity">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item" id="facultyInfo" style="display: none;">
                                <i class="fas fa-building"></i>
                                <div>
                                    <strong>Faculty/Department</strong>
                                    <span id="eventFaculty">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-eye"></i>
                                <div>
                                    <strong>Target Audience</strong>
                                    <span id="eventAudience">Loading...</span>
                                </div>
                            </div>
                            
                            <div class="detail-item" id="ticketInfo" style="display: none;">
                                <i class="fas fa-ticket-alt"></i>
                                <div>
                                    <strong>Ticket Type</strong>
                                    <span id="eventTicketType">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Content Section -->
            <div class="event-content-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">About This Event</h2>
                    </div>
                    
                    <div class="description-organizer-grid">
                        <div class="description-content">
                            <div class="content-card">
                                <h3><i class="fas fa-info-circle"></i> Event Description</h3>
                                <div id="eventDescription" class="event-description">Loading event description...</div>
                            </div>
                        </div>
                    </div>

                    <div class="section-header">
                        <h2 class="section-title">Event Details</h2>
                    </div>
                    
                    <div class="details-grid">
                        <div class="content-card" id="locationDetailsCard" style="display: none;">
                            <h3><i class="fas fa-map-marker-alt"></i> Location & Venue</h3>
                            <div id="locationDetails" class="location-details">Loading location details...</div>
                        </div>

                        <div class="content-card" id="scheduleCard" style="display: none;">
                            <h3><i class="fas fa-clock"></i> Event Schedule</h3>
                            <div id="eventSchedule" class="event-schedule">
                                <div class="schedule-item">
                                    <span class="time">Loading...</span>
                                    <span class="activity">Loading schedule...</span>
                                </div>
                            </div>
                        </div>

                        <div class="content-card" id="requirementsCard" style="display: none;">
                            <h3><i class="fas fa-list-check"></i> Requirements & Prerequisites</h3>
                            <div id="eventRequirements" class="event-requirements">Loading requirements...</div>
                        </div>

                        <div class="content-card" id="customFieldsCard" style="display: none;">
                            <h3><i class="fas fa-list-ul"></i> Additional Information</h3>
                            <div id="customFields" class="custom-fields">Loading additional information...</div>
                        </div>
                    </div>

                    <div class="section-header">
                        <h2 class="section-title">Registration & Ticketing</h2>
                    </div>
                    
                    <div class="registration-section">
                        <div class="content-card registration-card">
                            <div id="registrationTicketPeriodCard" style="display: none;">
                                <div id="freeRegistrationPeriod" class="period-section" style="display: none;">
                                    <div class="period-header">
                                        <div class="period-icon free-period">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="period-content">
                                            <h4>Free Registration Period</h4>
                                            <div id="freeRegPeriodDates" class="period-dates"></div>
                                            <div id="freeRegPeriodStatus" class="period-status"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="ticketBuyingPeriod" class="period-section" style="display: none;">
                                    <div class="period-header">
                                        <div class="period-icon ticket-period">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="period-content">
                                            <h4>Ticket Sales Period</h4>
                                            <div id="ticketPeriodDates" class="period-dates"></div>
                                            <div id="ticketPeriodStatus" class="period-status"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="divider" id="periodDivider"></div>
                            </div>

                            <div id="freeRegistrationSection" style="display: none;">
                                <div class="registration-type">
                                    <div class="registration-header">
                                        <i class="fas fa-ticket-alt registration-icon"></i>
                                        <div>
                                            <h3>Free Entry</h3>
                                            <p class="registration-subtitle" id="freeEntrySubtitle">This event is free to attend</p>
                                        </div>
                                    </div>
                                    <div class="registration-content">
                                        <div class="price-display">
                                            <span class="price-label">Entry Fee:</span>
                                            <span class="price-value free">FREE</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="paidTicketingSection" style="display: none;">
                                <div class="ticketing-type">
                                    <h3>Ticket Purchase Required</h3>
                                    <div id="ticketDetailsCard" class="ticket-details-card">
                                        <div id="ticketDetails"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="mixedTicketingSection" style="display: none;">
                                <div class="mixed-ticketing-type">
                                    <div id="studentRegRequired" style="display: none;">
                                        <h3>Free for University Students (Registration Required)</h3>
                                    </div>
                                    <div id="studentNoRegRequired" style="display: none;">
                                        <h3>Free for University Students (Walk-in)</h3>
                                    </div>
                                    <div id="mixedTicketDetailsCard" class="mixed-ticket-details-card">
                                        <div id="mixedTicketDetails"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card" id="volunteerCard" style="display: none;">
                        <h3><i class="fas fa-hands-helping"></i> Volunteer Opportunities</h3>
                        <div id="volunteerInfo"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sponsorship Packages Section (Sponsor Role Only) -->
        <?php if ($currentRole === 'Sponsor'): ?>
        <div id="sponsorshipPackagesCard" class="sponsorship-packages-section" style="display: none;">
            <div class="container">
                <div class="section-header">
                    <h2><i class="fas fa-handshake"></i> Sponsorship Opportunities</h2>
                    <p>Support this event by becoming a sponsor</p>
                    <div class="event-details-link">
                        <button id="viewEventDetailsBtn" class="btn-view-event-details" onclick="viewProposalDetails()">
                            <i class="fas fa-file-alt"></i> View Details
                        </button>
                    </div>
                </div>
                <div id="sponsorshipPackagesContainer" class="sponsorship-packages-grid">
                    <!-- Sponsorship packages will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Join Event Modal -->
    <div id="joinModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Join Event</h2>
                <button class="close-btn" onclick="closeJoinModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to join this event?</p>
                <div class="event-join-info">
                    <p><strong>Event:</strong> <span id="modalEventTitle"></span></p>
                    <p><strong>Date:</strong> <span id="modalEventDate"></span></p>
                    <p><strong>Time:</strong> <span id="modalEventTime"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeJoinModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmJoinEvent()">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Share Event Modal -->
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Share Event</h2>
                <button class="close-btn" onclick="closeShareModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <button class="share-btn facebook" onclick="shareViaFacebook()">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                    <button class="share-btn twitter" onclick="shareViaTwitter()">
                        <i class="fab fa-twitter"></i> Twitter
                    </button>
                    <button class="share-btn whatsapp" onclick="shareViaWhatsApp()">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </button>
                </div>
                <div class="copy-link-section">
                    <input type="text" id="shareLink" readonly class="share-link-input">
                    <button class="btn btn-primary" onclick="copyShareLink()">Copy Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section" id="commentsSection" style="display: none;">
        <div class="container">
            <h2>Comments</h2>
            <div class="comments-container">
                <div class="add-comment-form">
                    <textarea id="commentText" placeholder="Write a comment..." rows="3"></textarea>
                    <button class="btn btn-primary" onclick="submitComment()">Post Comment</button>
                </div>
                <div class="comments-list" id="commentsList">
                    <!-- Comments will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Comment Modal -->
    <div id="editCommentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Comment</h2>
                <button class="close-btn" onclick="closeEditCommentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <textarea id="editCommentText" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeEditCommentModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmEditComment()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Delete Comment Modal -->
    <div id="deleteCommentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Comment</h2>
                <button class="close-btn" onclick="closeDeleteCommentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this comment? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteCommentModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeleteComment()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Donation Modal -->
    <div id="donationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Make a Donation</h2>
                <button class="close-btn" onclick="closeDonationModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Support this event with a donation:</p>
                <div class="donation-amounts">
                    <button class="donation-btn" data-amount="500">LKR 500</button>
                    <button class="donation-btn" data-amount="1000">LKR 1,000</button>
                    <button class="donation-btn" data-amount="2000">LKR 2,000</button>
                    <input type="number" id="customAmount" placeholder="Custom amount" class="custom-amount-input">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDonationModal()">Cancel</button>
                <button class="btn btn-primary" onclick="processDonation()">Donate</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Pass PHP data to JavaScript -->
    <script>
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
        const userRole = '<?php echo $currentRole; ?>';
        
        <?php if ($config['purchaseTicketFunction']): ?>
        // Purchase ticket function - redirects to payment gateway
        function purchaseTicket() {
            const eventId = window.currentEvent?.id;
            if (!eventId) {
                alert('Event information not available');
                return;
            }
            
            // Check if user is logged in
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                alert('Please log in to purchase tickets');
                window.location.href = '/unipulse/public/signin?redirect=' + encodeURIComponent(window.location.pathname);
                return;
            }
            
            // Check if event requires tickets
            const ticketType = window.currentEvent?.ticket_type;
            if (ticketType === 'free-all') {
                alert('This is a free event. No ticket purchase required.');
                return;
            }
            
            // Redirect to payment page
            window.location.href = `/unipulse/public/payment/ticket?event_id=${eventId}`;
        }
        <?php endif; ?>
        
        <?php if ($config['visitProfileFunction']): ?>
        // Function to visit publisher profile
        function visitPublisherProfile() {
            const publisherId = window.currentEvent?.publisher_id || window.currentEvent?.created_by;
            if (publisherId) {
                window.location.href = `/unipulse/public/publisher/public?id=${publisherId}`;
            } else {
                alert('Publisher profile not available');
            }
        }
        <?php endif; ?>
    </script>

    <script src="<?php echo $config['jsFile']; ?>"></script>
</body>
</html>
