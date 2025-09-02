<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - UniPulse</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('eventdetail-style.css'); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Events</span>
                </button>
            </div>
            <div class="nav-brand">
                <div class="logo">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
                </div>
            </div>
            <div class="unp-nav-group">
                <div class="user-info">
                    <div class="profile-avatar">MH</div>
                    <span class="username">Manush-hub</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Loading State -->
    <div class="loading-container" id="loadingContainer">
        <div class="loading-spinner"></div>
        <p>Loading event details...</p>
    </div>

    <!-- Event Not Found -->
    <div class="error-container" id="errorContainer" style="display: none;">
        <div class="error-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Event Not Found</h2>
            <p>The event you're looking for doesn't exist or has been removed.</p>
            <button class="btn btn-primary" onclick="goBack()">Back to Events</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent" style="display: none;">
        <!-- Event Hero Section -->
        <section class="hero event-hero">
            <div class="container">
                <div class="hero-content">
                    <div class="event-badges">
                        <span class="category-badge" id="categoryBadge">Technology</span>
                        <span class="status-badge" id="statusBadge">upcoming</span>
                    </div>
                    <h1 id="eventTitle">Event Title</h1>
                    <p id="eventDescription">Event description will be loaded here...</p>
                    
                    <div class="event-meta-grid">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Date & Time</strong>
                                <span id="eventDateTime">Loading...</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Location</strong>
                                <span id="eventLocation">Loading...</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-university"></i>
                            <div>
                                <strong>University</strong>
                                <span id="eventUniversity">Loading...</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Participants</strong>
                                <span id="eventParticipants">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-buttons">
                        <button class="btn btn-primary" id="joinEventBtn">
                            <i class="fas fa-user-plus"></i>
                            Join Event
                        </button>
                        <button class="btn btn-secondary" id="shareBtn">
                            <i class="fas fa-share-alt"></i>
                            Share Event
                        </button>
                        <button class="btn btn-secondary" id="saveBtn">
                            <i class="fas fa-bookmark"></i>
                            Save Event
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Details Section -->
        <section class="features event-content">
            <div class="container">
                <div class="content-grid">
                    <!-- Main Content -->
                    <div class="main-details">
                        <!-- About Event -->
                        <div class="feature-card content-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3>About This Event</h3>
                            <div class="description">
                                <p id="fullEventDescription">Detailed event description will be loaded here...</p>
                                
                                <div class="event-highlights" id="eventHighlights">
                                    <!-- Dynamic highlights based on category -->
                                </div>
                            </div>
                        </div>

                        <!-- Event Timeline -->
                        <div class="feature-card content-card">
                            <div class="feature-icon orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>Event Timeline</h3>
                            <div class="timeline" id="eventTimeline">
                                <!-- Dynamic timeline will be generated -->
                            </div>
                        </div>

                        <!-- Related Events -->
                        <div class="feature-card content-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <h3>Related Events</h3>
                            <div class="related-events-grid" id="relatedEventsGrid">
                                <!-- Related events will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="sidebar">
                        <!-- Quick Info -->
                        <div class="feature-card sidebar-card">
                            <div class="feature-icon orange">
                                <i class="fas fa-info"></i>
                            </div>
                            <h3>Quick Information</h3>
                            <div class="info-list">
                                <div class="info-item">
                                    <div class="info-label">Category</div>
                                    <div class="info-value" id="sidebarCategory">Technology</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Date</div>
                                    <div class="info-value" id="sidebarDate">Loading...</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Time</div>
                                    <div class="info-value" id="sidebarTime">Loading...</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Location</div>
                                    <div class="info-value" id="sidebarLocation">Loading...</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Organizer</div>
                                    <div class="info-value" id="sidebarOrganizer">Loading...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Participation Info -->
                        <div class="feature-card sidebar-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Participation Status</h3>
                            <div class="participation-info">
                                <div class="progress-container">
                                    <div class="progress-bar">
                                        <div class="progress-fill" id="participationProgress"></div>
                                    </div>
                                    <div class="progress-text" id="participationText">Loading...</div>
                                    <div class="spots-remaining" id="spotsRemaining">Loading...</div>
                                </div>
                                <div class="participation-actions">
                                    <button class="btn btn-primary btn-full" id="joinEventBtnSidebar">
                                        <i class="fas fa-user-plus"></i>
                                        Join Event
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- University Info -->
                        <div class="feature-card sidebar-card">
                            <div class="feature-icon orange">
                                <i class="fas fa-university"></i>
                            </div>
                            <h3>University</h3>
                            <div class="university-info">
                                <div class="university-card">
                                    <div class="university-avatar" id="universityAvatar">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="university-details">
                                        <h4 id="universityName">University Name</h4>
                                        <p id="universityCode">university-code</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Organizer -->
                        <div class="feature-card sidebar-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3>Contact Organizer</h3>
                            <div class="contact-info">
                                <div class="organizer-info">
                                    <div class="organizer-avatar" id="organizerAvatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="organizer-details">
                                        <h4 id="organizerName">Organizer Name</h4>
                                        <p>Event Organizer</p>
                                    </div>
                                </div>
                                <button class="btn btn-outline btn-full">
                                    <i class="fas fa-envelope"></i>
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Join Event Modal -->
    <div class="modal" id="joinEventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Join Event</h3>
                <button class="close-btn" onclick="closeJoinModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="event-summary">
                    <h4 id="modalEventTitle">Event Title</h4>
                    <p id="modalEventDetails">Event date and location</p>
                </div>
                <form id="joinEventForm">
                    <div class="form-group">
                        <label for="participantName">Full Name</label>
                        <input type="text" id="participantName" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="participantEmail">Email Address</label>
                        <input type="email" id="participantEmail" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="participantUniversity">University</label>
                        <input type="text" id="participantUniversity" placeholder="Enter your university">
                    </div>
                    <div class="form-group">
                        <label for="participantNotes">Additional Notes (Optional)</label>
                        <textarea id="participantNotes" placeholder="Any special requirements or notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeJoinModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitJoinRequest()">
                    <i class="fas fa-check"></i>
                    Join Event
                </button>
            </div>
        </div>
    </div>

    <script src="<?php echo $controller->loadJS('eventdetail-app.js'); ?>"></script>
</body>
</html>