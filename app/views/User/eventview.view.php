<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - UniPulse</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('eventview-style.css'); ?>">
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
        <div id="errorContainer" class="error-container" style="display: none;">
            <div class="error-content">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Event Not Found</h2>
                <p>The event you're looking for could not be found.</p>
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
                        <span>Back to My Events</span>
                    </a>
                    <div class="event-actions">
                        <button class="btn btn-outline" id="shareBtn">
                            <i class="fas fa-share"></i>
                            Share Event
                        </button>
                        <button class="btn btn-primary" id="joinBtn">
                            <i class="fas fa-calendar-plus"></i>
                            Join Event
                        </button>
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
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Share Event</h3>
                <button class="close-btn" onclick="closeShareModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Share this event with others:</p>
                <div class="share-options">
                    <button class="share-btn" onclick="shareViaFacebook()">
                        <i class="fab fa-facebook"></i>
                        Facebook
                    </button>
                    <button class="share-btn" onclick="shareViaTwitter()">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </button>
                    <button class="share-btn" onclick="shareViaWhatsApp()">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </button>
                    <button class="share-btn" onclick="copyEventLink()">
                        <i class="fas fa-link"></i>
                        Copy Link
                    </button>
                </div>
                <div class="form-group">
                    <label for="eventLink">Event Link</label>
                    <div class="input-group">
                        <input type="text" id="eventLink" readonly>
                        <button class="btn btn-outline" onclick="copyEventLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="<?php echo $controller->loadJS('eventview-app.js'); ?>"></script>
</body>
</html>
