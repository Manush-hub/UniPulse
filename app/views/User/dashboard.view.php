<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Dashboard</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('dashboard-style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/User/dashboard-style.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></span>! 👋</h1>
                        <p>Ready to discover amazing events and connect with your university community?</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <!-- <section class="quick-actions">
            <div class="container">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <div class="action-card" onclick="window.location.href='/unipulse/public/events'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <h3>Browse Events</h3>
                        <p>Discover events across universities</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='my-events.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h3>My Events</h3>
                        <p>View your registered events</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='achievements.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                <path d="M4 22h16"></path>
                                <path d="M10 14.66V17c0 .55.47.98.97 1.21C12.11 18.79 14 20 14 20s1.89-1.21 3.03-1.79c.5-.23.97-.66.97-1.21v-2.34"></path>
                                <path d="M6 11h12l-6-6-6 6z"></path>
                            </svg>
                        </div>
                        <h3>Achievements</h3>
                        <p>View your badges & rewards</p>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- Upcoming Events -->
        <section class="upcoming-events">
            <div class="container">
                <div class="section-header">
                    <h2>Your Upcoming Events</h2>
                    <a href="/unipulse/public/user/events" class="view-all">View All</a>
                </div>
                <div class="events-carousel" id="upcomingEventsCarousel">
                    <!-- Events will be loaded here -->
                </div>
            </div>
        </section>

        <!-- My Tickets Section -->
        <section class="my-tickets-section" style="margin-top: 30px; margin-bottom: 30px;">
            <div class="container">
                <div class="section-header">
                    <h2>My Tickets</h2>
                </div>
                <div class="events-carousel" id="myTicketsCarousel" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <!-- Tickets will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <div class="container">
                <div class="activity-layout">
                    <div class="activity-feed">
                        <h2>Recent Activities</h2>
                        <div class="activity-list" id="activityList">
                            <!-- Activity items will be loaded here -->
                        </div>

                        <h2 style="margin-top: 2rem;">Your Donations</h2>
                        <div class="donations-table-container" id="donationsTableContainer">
                            <div class="loading">Loading donations...</div>
                        </div>

                        <section class="volunteering-section" id="volunteeringSection" style="display: none;">
                            <div id="volunteeringCard"></div>
                        </section>
                    </div>
                    <!-- <div class="sidebar">
                        <div class="sidebar-widget">
                            <h3>Popular Categories</h3>
                            <div class="category-list">
                                <div class="category-item">
                                    <span class="category-name">Technology</span>
                                    <span class="category-count">24 events</span>
                                </div>
                                <div class="category-item">
                                    <span class="category-name">Sports</span>
                                    <span class="category-count">18 events</span>
                                </div>
                                <div class="category-item">
                                    <span class="category-name">Cultural</span>
                                    <span class="category-count">15 events</span>
                                </div>
                                <div class="category-item">
                                    <span class="category-name">Academic</span>
                                    <span class="category-count">12 events</span>
                                </div>
                                <div class="category-item">
                                    <span class="category-name">Social</span>
                                    <span class="category-count">9 events</span>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <h3>Trending Universities</h3>
                            <div class="university-list">
                                <div class="university-item">
                                    <span class="university-name">University of Moratuwa</span>
                                    <span class="university-events">8 events</span>
                                </div>
                                <div class="university-item">
                                    <span class="university-name">University of Colombo</span>
                                    <span class="university-events">6 events</span>
                                </div>
                                <div class="university-item">
                                    <span class="university-name">University of Peradeniya</span>
                                    <span class="university-events">5 events</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>

        <!-- Your Comments History -->
        <section class="your-comments-section">
            <div class="container">
                <div class="section-header">
                    <h2>Your Comments</h2>
                </div>
                <div id="myCommentsList" class="my-comments-list">
                    <div class="loading">Loading your comments&hellip;</div>
                </div>
            </div>
        </section>

        <!-- Hidden Comment Reason Modal -->
        <div id="hiddenReasonModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:12px; padding:2rem; max-width:480px; width:92%; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.25);">
                <button onclick="document.getElementById('hiddenReasonModal').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280;">&times;</button>
                <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1rem;">
                    <span style="background:#fee2e2; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-eye-slash" style="color:#dc2626;"></i>
                    </span>
                    <h3 style="margin:0; color:#111827; font-size:1.1rem;">Comment Hidden by Moderator</h3>
                </div>
                <p style="color:#374151; font-size:.925rem; margin-bottom:.75rem;">Your comment was hidden for the following reason:</p>
                <blockquote id="hiddenReasonText" style="margin:0; padding:.75rem 1rem; background:#f9fafb; border-left:4px solid #e74c3c; border-radius:0 8px 8px 0; color:#4b5563; font-size:.9rem; line-height:1.6;"></blockquote>
                <p id="hiddenByLine" style="margin-top:.75rem; font-size:.8rem; color:#9ca3af;"></p>
            </div>
        </div>

        <!-- Monthly Evolution Button -->
        <section class="monthly-evolution-btn-section">
            <div class="container">
                <a href="/unipulse/public/user/dashboard/monthlyEvolution" class="evolution-btn">
                    <div class="evolution-btn-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"></path>
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        </svg>
                    </div>
                    <div class="evolution-btn-content">
                        <h3>View Monthly Evolution Report</h3>
                        <p>Track your volunteering, donations, and events participation for each month</p>
                    </div>
                    <div class="evolution-btn-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Pass user data to JavaScript -->
    <script>
        window.userData = <?php echo json_encode([
                                'name' => $user['name'] ?? 'User',
                                'email' => $user['email'] ?? '',
                                'type' => $user['type'] ?? 'user',
                                'university' => $user['university'] ?? ''
                            ]); ?>;
    </script>
    <script src="<?php echo $controller->loadJS('dashboard-app.js'); ?>"></script>
</body>

</html>