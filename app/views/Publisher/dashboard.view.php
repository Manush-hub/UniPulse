<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Event Organizer Dashboard</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('dashboard-style.css'); ?>">
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
                <a href="/unipulse/public/organizerlanding">Home</a>
                <a href="/unipulse/public/events">All Events</a>
                <a href="/unipulse/public/organizerdashboard" class="active">Dashboard</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
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
                    <img src="/unipulse/public/assets/images/organizer.jpg" alt="oranizer" class="organizer">
                    <div class="user-info">
                        <span class="username" id="username">Jennifer King</span>
                        <span class="user-role" id="userRole">Event Organizer</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.html">Profile Settings</a>
                        <a href="preferences.html">Preferences</a>
                        <a href="help.html">Help & Support</a>
                        <hr>
                        <a href="logout.php" class="logout">Logout</a>
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
                        <h1>Welcome back, <span id="welcomeUsername">Jennifer</span>! 👋</h1>
                        <p>Manage your events and track performance from your organizer dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="upcomingEvents">7</span>
                                <span class="stat-label">Upcoming Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="ticketsSold">1,280</span>
                                <span class="stat-label">Tickets Sold</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalVolunteers">45</span>
                                <span class="stat-label">Volunteers</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='create-event.html'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Create Event
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='create-event.html'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Find Sponsorships
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='all-events.html'">
                            View Reports
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
                    <div class="action-card" onclick="window.location.href='my-events.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <h3>Manage Events</h3>
                        <p>View and edit your events</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='volunteers.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3>Volunteers</h3>
                        <p>Manage volunteer applications</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='sponsorships.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                <path d="M12 18V6"></path>
                            </svg>
                        </div>
                        <h3>Sponsorships</h3>
                        <p>Track donations & sponsors</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='analytics.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 3v18h18"></path>
                                <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                            </svg>
                        </div>
                        <h3>Analytics</h3>
                        <p>View performance reports</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Management -->
        <section class="event-management">
            <div class="container">
                <div class="section-header">
                    <h2>Your Events</h2>
                    <a href="my-events.html" class="view-all">View All</a>
                </div>
                <div class="events-list" id="eventsManagementList">
                    <!-- Events will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Sales & Analytics -->
        <section class="sales-analytics">
            <div class="container">
                <div class="sales-layout">
                    <div class="sales-summary">
                        <h2>Sales & Ticketing</h2>
                        <div class="revenue-cards">
                            <div class="revenue-card">
                                <h3>$25,600</h3>
                                <p>Total Revenue</p>
                                <div class="revenue-change positive">+12% this month</div>
                            </div>
                            <div class="revenue-card">
                                <h3>1,280</h3>
                                <p>Tickets Sold</p>
                                <div class="revenue-change positive">+8% this week</div>
                            </div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-header">
                                <span>Revenue Goal Progress</span>
                                <span class="progress-percentage">75%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" data-width="75%"></div>
                            </div>
                            <p class="progress-text">$18,750 of $25,000 goal reached</p>
                        </div>
                    </div>
                    <div class="sidebar">
                        <div class="sidebar-widget">
                            <h3>Sponsorship Progress</h3>
                            <div class="sponsorship-stats">
                                <div class="sponsor-item">
                                    <span class="sponsor-name">Confirmed Sponsors</span>
                                    <span class="sponsor-count">12</span>
                                </div>
                                <div class="donation-progress">
                                    <div class="progress-header">
                                        <span>Donation Goal</span>
                                        <span>33%</span>
                                    </div>
                                    <div class="donation-bar">
                                        <div class="donation-fill" data-width="33%"></div>
                                    </div>
                                    <p class="progress-text">$25,000 of $75,000</p>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <h3>Sponsor Logos</h3>
                            <div class="sponsor-grid">
                                <div class="sponsor-logo">Microsoft</div>
                                <div class="sponsor-logo">Google</div>
                                <div class="sponsor-logo">Adobe</div>
                                <div class="sponsor-logo">AWS</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Volunteer Management -->
        <section class="volunteer-management">
            <div class="container">
                <div class="section-header">
                    <h2>Volunteer Management</h2>
                    <a href="volunteers.html" class="view-all">View All</a>
                </div>
                <div class="volunteer-layout">
                    <div class="volunteer-applications">
                        <h3>Recent Applications</h3>
                        <div class="volunteer-list" id="volunteerApplicationsList">
                            <!-- Volunteer applications will be loaded here -->
                        </div>
                    </div>
                    <div class="volunteer-shifts">
                        <h3>Upcoming Shifts</h3>
                        <div class="volunteer-list" id="volunteerShiftsList">
                            <!-- Volunteer shifts will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <div class="container">
                <div class="activity-layout">
                    <div class="activity-feed">
                        <h2>Recent Activity</h2>
                        <div class="activity-list" id="activityList">
                            <!-- Activity items will be loaded here -->
                        </div>
                    </div>
                    <div class="sidebar">
                        <div class="sidebar-widget">
                            <h3>Quick Reports</h3>
                            <div class="reports-list">
                                <div class="report-item" onclick="generateReport('performance')">
                                    <div class="report-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M3 3v18h18"></path>
                                            <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                                        </svg>
                                    </div>
                                    <div class="report-info">
                                        <span class="report-name">Event Performance</span>
                                        <span class="report-desc">Attendance & engagement</span>
                                    </div>
                                </div>
                                <div class="report-item" onclick="generateReport('financial')">
                                    <div class="report-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                            <path d="M12 18V6"></path>
                                        </svg>
                                    </div>
                                    <div class="report-info">
                                        <span class="report-name">Financial Summary</span>
                                        <span class="report-desc">Revenue & expenses</span>
                                    </div>
                                </div>
                                <div class="report-item" onclick="generateReport('demographics')">
                                    <div class="report-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div class="report-info">
                                        <span class="report-name">Demographics</span>
                                        <span class="report-desc">Audience breakdown</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeDeleteModal()">&times;</span>
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="confirm-delete" onclick="confirmDelete()">Yes, Delete</button>
                <button class="cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $controller->loadJS('dashboard-app.js'); ?>"></script>
</body>

</html>