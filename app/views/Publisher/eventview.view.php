<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/eventview-style.css">
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
                        <button class="btn btn-outline" id="shareBtn">
                            <i class="fas fa-share"></i>
                            Share Event
                        </button>
                        
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
            <div class="event-hero">
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
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Location</strong>
                                    <span id="eventLocation">Loading...</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-university"></i>
                                <div>
                                    <strong>University</strong>
                                    <span id="eventUniversity">Loading...</span>
                                </div>
                            </div>
                            <div class="detail-item">
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

                            <!-- Event Schedule -->
                            <div class="content-card">
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
                            <div class="content-card">
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

                            <!-- Event Stats -->
                            <div class="content-card">
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

    <!-- Pass server data to JavaScript -->
    <script>
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
    </script>

    <script src="<?php echo $controller->loadJS('eventview-app.js'); ?>"></script>
</body>
</html>
