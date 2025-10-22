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
                <a href="/unipulse/public/user/events" class="btn btn-primary">
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
                    <a href="/unipulse/public/user/events" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to All Events</span>
                    </a>
                    <div class="event-actions">
                        <!-- <button class="btn btn-outline" id="shareBtn">
                            <i class="fas fa-share"></i>
                            Share Event
                        </button> -->
                        <button class="btn btn-success" id="buyTicketBtn" style="display: none;">
                            <i class="fas fa-ticket-alt"></i>
                            Buy Ticket
                        </button>
                        <button class="btn btn-primary" id="joinBtn">
                            <i class="fas fa-calendar-plus"></i>
                            Join Event
                        </button>
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
                        <p class="event-summary" id="eventSummary">Loading event summary...</p>
                        
                        <div class="event-details-grid">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <strong>Date & Time</strong>
                                    <span id="eventDateTime">Loading...</span>
                                </div>
                            </div>
                            <div class="detail-item">
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
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Exact Location</strong>
                                    <span id="eventLocation">Loading...</span>
                                </div>
                            </div>
                            <div class="detail-item" id="participantsInfo" style="display: none;">
                                <i class="fas fa-users"></i>
                                <div>
                                    <strong>Participants</strong>
                                    <span id="eventParticipants">Loading...</span>
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
                    <div class="content-grid">
                        <div class="main-content">
                            <!-- Event Description -->
                            <div class="content-card">
                                <h3>
                                    <i class="fas fa-info-circle"></i>
                                    Event Description
                                </h3>
                                <div id="eventDescription" class="event-description">
                                    Loading event description...
                                </div>
                            </div>

                            <!-- Registration Period -->
                            <div class="content-card" id="registrationPeriodCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-calendar-check"></i>
                                    Registration Period
                                </h3>
                                <div id="registrationPeriod" class="registration-period">
                                    Loading registration period...
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

                            <!-- Requirements -->
                            <div class="content-card" id="requirementsCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-list-check"></i>
                                    Requirements & Prerequisites
                                </h3>
                                <div id="eventRequirements" class="event-requirements">
                                    Loading requirements...
                                </div>
                            </div>

                            <!-- Location Details -->
                            <div class="content-card" id="locationDetailsCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-map-marker-alt"></i>
                                    Location Details
                                </h3>
                                <div id="locationDetails" class="location-details">
                                    Loading location details...
                                </div>
                            </div>

                            <!-- Ticket Information -->
                            <div class="content-card" id="ticketDetailsCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-ticket-alt"></i>
                                    Ticket Information
                                </h3>
                                <div id="ticketDetails" class="ticket-details">
                                    Loading ticket information...
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

                            <!-- Volunteer Information -->
                            <div class="content-card" id="volunteerCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-hands-helping"></i>
                                    Volunteer Opportunities
                                </h3>
                                <div id="volunteerInfo" class="volunteer-info">
                                    Loading volunteer information...
                                </div>
                            </div>

                            <!-- Donation Information -->
                            <div class="content-card" id="donationCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-heart"></i>
                                    Support This Event
                                </h3>
                                <div id="donationInfo" class="donation-info">
                                    <p>This event accepts donations to help cover costs and improve the experience for all participants.</p>
                                    <button class="btn btn-primary" onclick="openDonationModal()">
                                        <i class="fas fa-heart"></i>
                                        Make a Donation
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar">
                            <!-- Organizer Info -->
                            <div class="content-card">
                                <h3>
                                    <i class="fas fa-user-tie"></i>
                                    Organizer
                                </h3>
                                <div class="organizer-info">
                                    <div class="organizer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="organizer-details">
                                        <h4 id="organizerName">Loading...</h4>
                                        <p id="organizerRole">Event Organizer</p>
                                        <button class="btn btn-outline btn-sm" onclick="contactOrganizer()">
                                            <i class="fas fa-envelope"></i>
                                            Contact
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Stats (Only shown when max_participants is set) -->
                            <div class="content-card" id="eventStatsCard" style="display: none;">
                                <h3>
                                    <i class="fas fa-chart-bar"></i>
                                    Event Statistics
                                </h3>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number" id="totalParticipants">0</div>
                                        <div class="stat-label">Total Participants</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" id="availableSpots">0</div>
                                        <div class="stat-label">Available Spots</div>
                                    </div>
                                </div>
                                <div class="participation-bar">
                                    <div class="participation-fill" id="participationFill"></div>
                                </div>
                                <p class="participation-text">
                                    <span id="participationPercentage">0%</span> filled
                                </p>
                            </div>

                            <!-- Similar Events -->
                            <div class="content-card">
                                <h3>
                                    <i class="fas fa-calendar"></i>
                                    Similar Events
                                </h3>
                                <div id="similarEvents" class="similar-events">
                                    Loading similar events...
                                </div>
                            </div>
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

                        <!-- Add Comment Form -->
                        <div class="add-comment-section" id="addCommentSection" style="display: none;">
                            <div class="comment-form">
                                <div class="form-group">
                                    <label for="commentText">Share your experience</label>
                                    <textarea id="commentText" placeholder="How was this event? Share your thoughts and feedback..." maxlength="1000"></textarea>
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
                <button class="btn btn-primary" id="updateCommentBtn">
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
                <div class="confirm-delete">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4>Are you sure?</h4>
                    <p>This action cannot be undone. Your comment and rating will be permanently deleted.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteCommentModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i>
                    Delete Comment
                </button>
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

    <!-- Pass PHP data to JavaScript -->
    <script>
        // Pass server data to JavaScript
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
    </script>
    <script src="<?php echo $controller->loadJS('eventview-app.js'); ?>"></script>
</body>
</html>
