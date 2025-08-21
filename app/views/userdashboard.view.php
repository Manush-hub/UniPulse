<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/userdashboard-style.css">
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
                <a href="/unipulse/public/userlanding" >Home</a>
                <a href="/unipulse/public/events" >All Events</a>
                <a href="/unipulse/public/userdashboard" class="active">Dashboard</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                    <img src="/unipulse/public/assets/images/default-avatar.png" alt="User Avatar" class="avatar">
                    <div class="user-info">
                        <span class="username" id="username">Manush-hub</span>
                        <span class="user-role" id="userRole">Student</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        <h1>Welcome back, <span id="welcomeUsername">Manush</span>! 👋</h1>
                        <p>Ready to discover amazing events and connect with your university community?</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalEvents">12</span>
                                <span class="stat-label">Events Joined</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="upcomingEvents">3</span>
                                <span class="stat-label">Upcoming</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="eventsCreated">2</span>
                                <span class="stat-label">Events Created</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='create-event.html'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Create Event
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='all-events.html'">
                            Browse Events
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
                    <div class="action-card" onclick="window.location.href='all-events.html'">
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
                    <div class="action-card" onclick="window.location.href='communities.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3>Communities</h3>
                        <p>Join university societies</p>
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
        </section>

        <!-- Upcoming Events -->
        <section class="upcoming-events">
            <div class="container">
                <div class="section-header">
                    <h2>Your Upcoming Events</h2>
                    <a href="my-events.html" class="view-all">View All</a>
                </div>
                <div class="events-carousel" id="upcomingEventsCarousel">
                    <!-- Events will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Featured Events -->
        <section class="featured-events">
            <div class="container">
                <div class="section-header">
                    <h2>Featured Events</h2>
                    <a href="all-events.html" class="view-all">View All</a>
                </div>
                <div class="events-grid" id="featuredEventsGrid">
                    <!-- Featured events will be loaded here -->
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

    <script src="/unipulse/public/assets/js/userdashboard-app.js"></script>
</body>
</html>