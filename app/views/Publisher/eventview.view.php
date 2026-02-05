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
    include __DIR__ . '/components/header.php';
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
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Event Not Found</h2>
                <p><?php echo isset($error) ? htmlspecialchars($error) : 'The event you\'re looking for could not be found.'; ?></p>
                <a href="/unipulse/public/publisher/events" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Events
                </a>
            </div>
        </div>

        <!-- Event Content -->
        <div id="eventContainer" class="event-container" style="display: none;">
            <!-- Navigation Bar -->
            <div class="navigation-bar">
                <div class="container">
                    <a href="/unipulse/public/publisher/events" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to All Events</span>
                    </a>
                    <div class="event-actions">
                        <!-- <button class="btn btn-outline" id="shareBtn">
                            <i class="fas fa-share"></i>
                            Share Event
                        </button> -->
                        
                        <?php if (isset($isOwner) && $isOwner): ?>
                            <!-- Publisher's own event - show edit/delete -->
                            <button class="btn btn-secondary" id="editBtn" onclick="editEvent()">
                                <i class="fas fa-edit"></i>
                                Edit Event
                            </button>
                            <button class="btn btn-danger" id="deleteBtn" onclick="deleteEvent()">
                                <i class="fas fa-trash"></i>
                                Delete Event
                            </button>
                        <?php else: ?>
                            <!-- Other events - show view only message -->
                            <button class="btn btn-primary" id="viewOnlyBtn" disabled>
                                <i class="fas fa-eye"></i>
                                View Only
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

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
                            
                            <!-- Inside University Fields -->
                            <div class="detail-item" id="exactLocationInfo" style="display: none;">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Exact Location</strong>
                                    <span id="eventLocation">Loading...</span>
                                </div>
                            </div>
                            
                            <!-- Outside University Fields -->
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
                    
                    <!-- Section 1: Description & Organizer -->
                    <div class="section-header">
                        <h2 class="section-title">About This Event</h2>
                    </div>
                    
                    <div class="description-organizer-grid">
                        
                        <div class="organizer-sidebar">
                            <div class="content-card organizer-card">
                                <h3>
                                    <i class="fas fa-user-tie"></i>
                                    Organized By
                                </h3>
                                <div class="organizer-info">
                                    <div class="organizer-avatar" id="organizerAvatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="organizer-details">
                                        <h4 id="organizerName">Loading...</h4>
                                        <!-- <p id="organizerRole" class="organizer-role"></p> -->
                                        <div class="organizer-actions">
                                            <button class="btn btn-outline btn-icon" id="visitProfileBtn" onclick="visitPublisherProfile()" title="Visit Profile">
                                                <i class="fas fa-user"></i>
                                            </button>
                                            <button class="btn btn-outline btn-icon" id="callOrganizerBtn" title="Call Organizer">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                            <button class="btn btn-outline btn-icon" onclick="contactOrganizer()" title="Email Organizer">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="description-content">
                            <div class="content-card">
                                <h3>
                                    <i class="fas fa-info-circle"></i>
                                    Event Description
                                </h3>
                                <div id="eventDescription" class="event-description">
                                    Loading event description...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Location & Event Details -->
                    <div class="section-header">
                        <h2 class="section-title">Event Details</h2>
                    </div>
                    
                    <div class="details-grid">
                        <!-- Location Details -->
                        <div class="content-card" id="locationDetailsCard" style="display: none;">
                            <h3>
                                <i class="fas fa-map-marker-alt"></i>
                                Location & Venue
                            </h3>
                            <div id="locationDetails" class="location-details">
                                Loading location details...
                            </div>
                        </div>

                        <!-- Event Schedule -->
                        <div class="content-card" id="scheduleCard" style="display: none;">
                            <h3>
                                <i class="fas fa-clock"></i>
                                Event Schedule
                            </h3>
                            <div id="eventSchedule" class="event-schedule">
                                <div class="schedule-item">
                                    <span class="time">Loading...</span>
                                    <span class="activity">Loading schedule...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements & Prerequisites -->
                        <div class="content-card" id="requirementsCard" style="display: none;">
                            <h3>
                                <i class="fas fa-list-check"></i>
                                Requirements & Prerequisites
                            </h3>
                            <div id="eventRequirements" class="event-requirements">
                                Loading requirements...
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <div class="content-card" id="customFieldsCard" style="display: none;">
                            <h3>
                                <i class="fas fa-list-ul"></i>
                                Additional Information
                            </h3>
                            <div id="customFields" class="custom-fields">
                                Loading additional information...
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Registration & Ticketing -->
                    <div class="section-header">
                        <h2 class="section-title">Registration & Ticketing</h2>
                    </div>
                    
                    <div class="registration-section">
                        <!-- Event Statistics - Show at top if max_participants is set -->
                        <div class="content-card event-stats-registration" id="eventStatsRegistration" style="display: none;">
                            <h3>
                                <i class="fas fa-chart-line"></i>
                                Event Capacity
                            </h3>
                            
                            <!-- Overall Statistics -->
                            <div class="stats-registration-grid" id="overallStatsGrid">
                                <div class="stat-box">
                                    <div class="stat-icon registered">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number" id="totalParticipantsReg">0</div>
                                        <div class="stat-label">Registered</div>
                                    </div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-icon available">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number" id="availableSpotsReg">0</div>
                                        <div class="stat-label">Available</div>
                                    </div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-icon capacity">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-number" id="maxCapacityReg">0</div>
                                        <div class="stat-label">Max Capacity</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ticket Type Breakdown (for mixed/paid events) -->
                            <div id="ticketTypeBreakdown" style="display: none;">
                                <div class="ticket-breakdown-divider"></div>
                                <h4 class="breakdown-title">
                                    <i class="fas fa-ticket-alt"></i>
                                    Ticket Availability by Type
                                </h4>
                                <div id="ticketTypeStats" class="ticket-type-stats"></div>
                            </div>
                            
                            <div class="capacity-bar-container">
                                <div class="capacity-bar">
                                    <div class="capacity-fill" id="capacityFill"></div>
                                </div>
                                <p class="capacity-text">
                                    <span id="capacityPercentage">0%</span> capacity filled
                                </p>
                            </div>
                        </div>

                        <div class="content-card registration-card">
                            <!-- Registration & Ticket Period -->
                            <div id="registrationTicketPeriodCard" style="display: none;">
                                <!-- Free Registration Period -->
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
                                
                                <!-- Ticket Buying Period -->
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

                            <!-- Free Registration -->
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
                                        
                                        <!-- Registration Required -->
                                        <div id="freeRegRequired" style="display: none;">
                                            <button class="btn btn-primary btn-large" onclick="registerForEvent()">
                                                <i class="fas fa-user-plus"></i>
                                                Register Now (Free)
                                            </button>
                                            <p class="registration-note">
                                                <i class="fas fa-info-circle"></i>
                                                Registration is required to attend this free event
                                            </p>
                                        </div>
                                        
                                        <!-- No Registration Required -->
                                        <div id="freeNoRegRequired" style="display: none;">
                                            <div class="open-entry-message">
                                                <i class="fas fa-door-open"></i>
                                                <h4>Open Entry - No Registration Needed</h4>
                                                <p>Simply show up at the event venue during the event time. No prior registration or tickets required!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paid Ticketing -->
                            <div id="paidTicketingSection" style="display: none;">
                                <div class="registration-type">
                                    <div class="registration-header">
                                        <i class="fas fa-ticket-alt registration-icon"></i>
                                        <div>
                                            <h3>Event Tickets</h3>
                                            <p class="registration-subtitle">Purchase your tickets to attend</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Ticket Information -->
                                    <div class="ticket-details-section" id="ticketDetailsCard">
                                        <div id="ticketDetails" class="ticket-details">
                                            Loading ticket information...
                                        </div>
                                    </div>
                                    
                                    <div class="ticket-purchase">
                                        <div class="ticket-price-box">
                                            <span class="price-label">Ticket Price:</span>
                                            <span class="price-value" id="ticketPrice">LKR 0</span>
                                        </div>
                                        <button class="btn btn-success btn-large" onclick="purchaseTicket()">
                                            <i class="fas fa-shopping-cart"></i>
                                            Buy Tickets
                                        </button>
                                        <p class="registration-note">
                                            <i class="fas fa-shield-alt"></i>
                                            Secure payment processing
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Mixed: Free for Students, Paid for Others -->
                            <div id="mixedTicketingSection" style="display: none;">
                                <div class="mixed-ticketing-grid">
                                    <!-- Students Section -->
                                    <div class="registration-type mixed-student">
                                        <div class="registration-header">
                                            <i class="fas fa-graduation-cap registration-icon"></i>
                                            <div>
                                                <h3>University Students</h3>
                                                <p class="registration-subtitle">Free entry for students</p>
                                            </div>
                                        </div>
                                        <div class="registration-content">
                                            <div class="price-display">
                                                <span class="price-label">Entry Fee:</span>
                                                <span class="price-value free">FREE</span>
                                            </div>
                                            
                                            <!-- Registration Required for Students -->
                                            <div id="studentRegRequired" style="display: none;">
                                                <button class="btn btn-primary btn-large" onclick="registerForEvent()">
                                                    <i class="fas fa-user-plus"></i>
                                                    Register Now
                                                </button>
                                                <p class="registration-note">
                                                    <i class="fas fa-id-card"></i>
                                                    Student ID verification required
                                                </p>
                                            </div>
                                            
                                            <!-- No Registration for Students -->
                                            <div id="studentNoRegRequired" style="display: none;">
                                                <div class="open-entry-message">
                                                    <i class="fas fa-door-open"></i>
                                                <h4>Open Entry - No Registration Needed</h4>
                                                <p>Simply show up at the event venue during the event time. No prior registration or tickets required!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Others/Public Section - Same structure as Paid Ticketing -->
                                    <div class="registration-type">
                                        <div class="registration-header">
                                            <i class="fas fa-users registration-icon"></i>
                                            <div>
                                                <h3>General Public</h3>
                                                <p class="registration-subtitle">Ticket purchase required</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Ticket Information -->
                                        <div class="ticket-details-section" id="mixedTicketDetailsCard">
                                            <div id="mixedTicketDetails" class="ticket-details">
                                                <!-- Ticket Type and Registration Period Info will be added here by JavaScript -->
                                                Loading ticket information...
                                            </div>
                                        </div>
                                        
                                        <div class="ticket-purchase">
                                            <div class="ticket-price-box">
                                                <span class="price-label">Ticket Price:</span>
                                                <span class="price-value" id="mixedTicketPrice">LKR 0.00</span>
                                            </div>
                                            <button class="btn btn-success btn-large" onclick="purchaseTicket()">
                                                <i class="fas fa-shopping-cart"></i>
                                                Buy Tickets
                                            </button>
                                            <p class="registration-note">
                                                <i class="fas fa-shield-alt"></i>
                                                Secure payment processing
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Volunteering & Donations -->
                    <div class="section-header" id="volunteerDonationHeader" style="display: none;">
                        <h2 class="section-title">Get Involved</h2>
                    </div>
                    
                    <div class="volunteer-donation-grid" id="volunteerDonationGrid" style="display: none;">
                        <!-- Volunteer Information -->
                        <div class="content-card volunteer-card" id="volunteerCard" style="display: none;">
                            <div class="card-icon volunteer-icon">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <h3>Volunteer Opportunities</h3>
                            <div id="volunteerInfo" class="volunteer-info">
                                <p>We're looking for enthusiastic volunteers to help make this event a success!</p>
                            </div>
                        </div>

                        <!-- Donation Information -->
                        <div class="content-card donation-card" id="donationCard" style="display: none;">
                            <div class="card-icon donation-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h3>Support This Event</h3>
                            <div id="donationInfo" class="donation-info">
                                <p>Your donation helps make this event possible and supports the organizers in creating amazing experiences for all participants.</p>
                            </div>
                            <button class="btn btn-primary btn-large" onclick="openDonationModal()">
                                <i class="fas fa-heart"></i>
                                Make a Donation
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Join Event Modal -->
    <div id="joinModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Join Event</h3>
                <button class="close-btn" onclick="closeJoinModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to join this event?</p>
                <div class="form-group">
                    <label for="participantNotes">Additional Notes (Optional)</label>
                    <textarea id="participantNotes" placeholder="Any special requirements or notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeJoinModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmJoinEvent()">
                    <i class="fas fa-check"></i>
                    Confirm Join
                </button>
            </div>
        </div>
    </div>

    <!-- Share Event Modal -->
        <!-- Share Event Modal -->
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Share Event</h3>
                <button class="close-btn" onclick="closeShareModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <button class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                    <button class="share-btn twitter">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </button>
                    <button class="share-btn linkedin">
                        <i class="fab fa-linkedin-in"></i>
                        LinkedIn
                    </button>
                    <button class="share-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </button>
                </div>
                <div class="share-link">
                    <input type="text" id="shareLink" readonly>
                    <button onclick="copyShareLink()">Copy Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section" id="commentsSection" style="display: none;">
        <div class="container">
            <div class="content-card">
                <div class="comments-header">
                    <h3>
                        <i class="fas fa-comments"></i>
                        Event Reviews & Comments
                    </h3>
                    <div class="comments-stats" id="commentsStats">
                        <span class="stat-item">
                            <i class="fas fa-comment"></i>
                            <span id="totalCommentsCount">0</span> comments
                        </span>
                        <span class="stat-item" id="averageRatingDisplay" style="display: none;">
                            <i class="fas fa-star"></i>
                            <span id="averageRatingValue">0</span> average rating
                        </span>
                    </div>
                </div>

                <!-- Add Comment Form (For Publishers/Admins/Moderators) -->
                <div class="add-comment-section" id="addCommentSection" style="display: none;">
                    <div class="comment-form">
                        <div class="form-group">
                            <label for="commentText">Share your thoughts</label>
                            <textarea id="commentText" placeholder="Share your thoughts about this event..." maxlength="1000"></textarea>
                            <div class="char-count">
                                <span id="charCount">0</span>/1000
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="eventRating">Rate this event (optional)</label>
                            <div class="rating-input" id="ratingInput">
                                <span class="star" data-rating="1">☆</span>
                                <span class="star" data-rating="2">☆</span>
                                <span class="star" data-rating="3">☆</span>
                                <span class="star" data-rating="4">☆</span>
                                <span class="star" data-rating="5">☆</span>
                            </div>
                            <div class="rating-text" id="ratingText">Click stars to rate</div>
                        </div>
                        
                        <div class="form-actions">
                            <button class="btn btn-secondary" id="cancelCommentBtn">Cancel</button>
                            <button class="btn btn-primary" id="submitCommentBtn">
                                <i class="fas fa-paper-plane"></i>
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Login Prompt -->
                <div class="login-prompt" id="loginPrompt" style="display: none;">
                    <div class="prompt-content">
                        <i class="fas fa-lock"></i>
                        <h4>Sign in to comment</h4>
                        <p>Please sign in to share your experience and rate this event.</p>
                        <a href="/unipulse/public/signin" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </a>
                    </div>
                </div>

                <!-- Add Comment Button -->
                <div class="add-comment-trigger" id="addCommentTrigger" style="display: none;">
                    <button class="btn btn-outline" onclick="showCommentForm()">
                        <i class="fas fa-plus"></i>
                        Add Your Review
                    </button>
                </div>

                <!-- Comments List -->
                <div class="comments-list" id="commentsList">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading comments...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Comment Modal -->
    <div id="editCommentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Comment</h3>
                <button class="close-btn" onclick="closeEditCommentModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="editCommentText">Update your comment</label>
                    <textarea id="editCommentText" maxlength="1000"></textarea>
                    <div class="char-count">
                        <span id="editCharCount">0</span>/1000
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editEventRating">Update your rating (optional)</label>
                    <div class="rating-input" id="editRatingInput">
                        <span class="star" data-rating="1">☆</span>
                        <span class="star" data-rating="2">☆</span>
                        <span class="star" data-rating="3">☆</span>
                        <span class="star" data-rating="4">☆</span>
                        <span class="star" data-rating="5">☆</span>
                    </div>
                    <div class="rating-text" id="editRatingText">Click stars to rate</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeEditCommentModal()">Cancel</button>
                <button class="btn btn-primary" onclick="updateComment()">
                    <i class="fas fa-save"></i>
                    Update Comment
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Comment Modal -->
    <div id="deleteCommentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Comment</h3>
                <button class="close-btn" onclick="closeDeleteCommentModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this comment? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteCommentModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeleteComment()">
                    <i class="fas fa-trash"></i>
                    Delete Comment
                </button>
            </div>
        </div>
    </div>

    <!-- Donation Modal -->
    <div id="donationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Support This Event</h3>
                <button class="close-btn" onclick="closeDonationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Your donation helps make this event possible and supports the organizers in creating amazing experiences.</p>
                <div class="donation-amounts">
                    <button class="donation-amount" data-amount="500">LKR 500</button>
                    <button class="donation-amount" data-amount="1000">LKR 1,000</button>
                    <button class="donation-amount" data-amount="2500">LKR 2,500</button>
                    <button class="donation-amount" data-amount="5000">LKR 5,000</button>
                </div>
                <div class="custom-amount">
                    <label>Custom Amount:</label>
                    <input type="number" id="customDonationAmount" placeholder="Enter amount (LKR)" min="100">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDonationModal()">Cancel</button>
                <button class="btn btn-primary" onclick="processDonation()">
                    <i class="fas fa-heart"></i>
                    Donate Now
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Pass server data to JavaScript -->
    <script>
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
    </script>

    <script src="/unipulse/public/assets/js/Publisher/eventview-app.js"></script>
</body>
</html>
