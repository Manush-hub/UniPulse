<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Moderator Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="dashboard.html">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderatorlanding">Home</a>
                <a href="/unipulse/public/events">All Events</a>
                <a href="/unipulse/public/moderatordashboard" class="active">Dashboard</a>
                <a href="/unipulse/public/reports">Reports</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button onclick="markAllAsRead()">Mark all as read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/moderator.png" alt="Moderator" class="moderator-avatar">
                    <div class="user-info">
                        <span class="username" id="username">Lisa Chen</span>
                        <span class="user-role" id="userRole">Moderator</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.html"><i class="fas fa-user-cog"></i> Profile Settings</a>
                        <a href="moderation-guidelines.html"><i class="fas fa-book"></i> Guidelines</a>
                        <a href="help.html"><i class="fas fa-question-circle"></i> Help & Support</a>
                        <hr>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername">Lisa</span>! 👋</h1>
                        <p>Manage content moderation and ensure platform quality from your moderator dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingReviews">12</span>
                                <span class="stat-label">Pending Reviews</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="eventsReviewed">84</span>
                                <span class="stat-label">Events Reviewed</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="reportsHandled">23</span>
                                <span class="stat-label">Reports Handled</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="approvalRate">92%</span>
                                <span class="stat-label">Approval Rate</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='content-moderation.html'">
                            <i class="fas fa-shield-alt"></i>
                            Review Content
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='reports.html'">
                            <i class="fas fa-flag"></i>
                            View Reports
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='guidelines.html'">
                            <i class="fas fa-book"></i>
                            Guidelines
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <div class="container">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <div class="action-card" onclick="window.location.href='content-moderation.html'">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h3>Content Moderation</h3>
                        <p>Review and approve events</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='reports.html'">
                        <div class="action-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <h3>User Reports</h3>
                        <p>Handle user-reported content</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='organizer-verification.html'">
                        <div class="action-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3>Organizer Verification</h3>
                        <p>Verify event organizers</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='comments-moderation.html'">
                        <div class="action-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Comments Moderation</h3>
                        <p>Review user comments</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pending Reviews -->
        <section class="pending-reviews">
            <div class="container">
                <div class="section-header">
                    <h2>Pending Reviews</h2>
                    <a href="content-moderation.html" class="view-all">View All</a>
                </div>
                <div class="reviews-list" id="reviewsList">
                    <!-- Review items will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <div class="container">
                <div class="activity-layout">
                    <div class="activity-feed">
                        <div class="section-header">
                            <h2>Recent Moderation Activity</h2>
                            <a href="activity-log.html" class="view-all">View Full Log</a>
                        </div>
                        <div class="activity-list" id="activityList">
                            <!-- Activity items will be loaded here -->
                        </div>
                    </div>
                    <div class="sidebar">
                        <div class="sidebar-widget">
                            <h3>Moderation Stats</h3>
                            <div class="stats-container">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number">64</span>
                                        <span class="stat-label">Approved Events</span>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number">8</span>
                                        <span class="stat-label">Rejected Events</span>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number">12</span>
                                        <span class="stat-label">Events Edited</span>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number">15</span>
                                        <span class="stat-label">Organizers Verified</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <h3>Moderation Guidelines</h3>
                            <div class="guidelines-list">
                                <div class="guideline-item">
                                    <i class="fas fa-check"></i>
                                    <span>Ensure events follow university policies</span>
                                </div>
                                <div class="guideline-item">
                                    <i class="fas fa-check"></i>
                                    <span>Verify organizer credentials</span>
                                </div>
                                <div class="guideline-item">
                                    <i class="fas fa-check"></i>
                                    <span>Check for appropriate content</span>
                                </div>
                                <div class="guideline-item">
                                    <i class="fas fa-check"></i>
                                    <span>Ensure accurate event information</span>
                                </div>
                                <a href="guidelines.html" class="view-guidelines">View Complete Guidelines</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Reports -->
        <section class="user-reports">
            <div class="container">
                <div class="section-header">
                    <h2>Recent User Reports</h2>
                    <a href="reports.html" class="view-all">View All Reports</a>
                </div>
                <div class="reports-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Reported Content</th>
                                <th>Report Type</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody">
                            <!-- Report rows will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
                <a href="#about">About UniPulse</a>
            </div>
            <div class="footer-copyright">
                <span>&copy; 2025 UniPulse. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('reviewModal')">&times;</span>
            <h3 id="modalTitle">Event Review</h3>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/dashboard-app.js"></script>
</body>

</html>