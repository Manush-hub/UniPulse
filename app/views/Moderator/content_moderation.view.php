<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Content Moderation</h1>
                        <p>Review and approve events submitted by organizers</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingEvents">15</span>
                                <span class="stat-label">Pending Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="reviewedToday">8</span>
                                <span class="stat-label">Reviewed Today</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="approvalRate">92%</span>
                                <span class="stat-label">Approval Rate</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="filterEvents('pending')">
                            <i class="fas fa-clock"></i>
                            Pending Only
                        </button>
                        <button class="btn btn-outline" onclick="filterEvents('all')">
                            <i class="fas fa-list"></i>
                            View All
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events List -->
        <section class="pending-reviews">
            <div class="container">
                <div class="section-header">
                    <h2>Events Pending Review</h2>
                    <span class="badge" id="pendingCount">15 events</span>
                </div>
                <div class="reviews-list" id="eventsList">
                    <!-- Event items will be loaded here -->
                    <div class="review-item">
                        <div class="review-info">
                            <div class="review-title">Annual Tech Symposium 2024</div>
                            <div class="review-meta">
                                <span class="review-organizer">
                                    <i class="fas fa-user"></i>
                                    Computer Science Department
                                </span>
                                <span class="review-category">
                                    <i class="fas fa-tag"></i>
                                    Academic
                                </span>
                                <span class="review-date">
                                    <i class="fas fa-calendar"></i>
                                    Submitted: 2 hours ago
                                </span>
                            </div>
                            <div class="event-description">
                                Annual technology conference featuring guest speakers from industry leaders...
                            </div>
                        </div>
                        <div class="review-actions">
                            <button class="review-btn view" onclick="viewEventDetails(1)">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="review-btn approve" onclick="approveEvent(1)">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="review-btn reject" onclick="rejectEvent(1)">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        </div>
                    </div>

                    <div class="review-item">
                        <div class="review-info">
                            <div class="review-title">Spring Music Festival</div>
                            <div class="review-meta">
                                <span class="review-organizer">
                                    <i class="fas fa-user"></i>
                                    Music Club
                                </span>
                                <span class="review-category">
                                    <i class="fas fa-tag"></i>
                                    Cultural
                                </span>
                                <span class="review-date">
                                    <i class="fas fa-calendar"></i>
                                    Submitted: 5 hours ago
                                </span>
                            </div>
                            <div class="event-description">
                                Outdoor music festival featuring student bands and local artists...
                            </div>
                        </div>
                        <div class="review-actions">
                            <button class="review-btn view" onclick="viewEventDetails(2)">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="review-btn approve" onclick="approveEvent(2)">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="review-btn reject" onclick="rejectEvent(2)">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bulk Actions -->
        <section class="recent-activity">
            <div class="container">
                <div class="bulk-actions">
                    <h3>Bulk Actions</h3>
                    <div class="action-buttons">
                        <button class="btn btn-outline" onclick="selectAllEvents()">
                            <i class="fas fa-check-square"></i>
                            Select All
                        </button>
                        <button class="btn btn-primary" onclick="approveSelected()">
                            <i class="fas fa-check-double"></i>
                            Approve Selected
                        </button>
                        <button class="btn reject" onclick="rejectSelected()">
                            <i class="fas fa-times-circle"></i>
                            Reject Selected
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Event Details Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('eventModal')">&times;</span>
            <h3 id="modalTitle">Event Details</h3>
            <div class="modal-body" id="modalBody">
                <!-- Event details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/content-moderation.js"></script>
    <script src="/unipulse/public/assets/js/Moderator/header.js"></script>
</body>

</html>