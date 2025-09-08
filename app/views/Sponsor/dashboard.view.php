<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Sponsor Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
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
                <a href="/unipulse/public/userlanding">Home</a>
                <a href="/unipulse/public/events">All Events</a>
                <a href="/unipulse/public/sponsordashboard" class="active">Dashboard</a>
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
                    <img src="/unipulse/public/assets/images/default-avatar.png" alt="Sponsor Avatar" class="avatar">
                    <div class="user-info">
                        <span class="username" id="username">TechCorp Ltd</span>
                        <span class="user-role" id="userRole">Sponsor</span>
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
                        <a href="billing.html">Billing & Payments</a>
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
                        <h1>Welcome back, <span id="welcomeUsername">TechCorp Ltd</span>! 👋</h1>
                        <p>Manage your sponsorships and discover new opportunities to support university events.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalSponsorships">8</span>
                                <span class="stat-label">Active Sponsorships</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="pendingRequests">5</span>
                                <span class="stat-label">Pending Requests</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalInvestment">LKR 4,200</span>
                                <span class="stat-label">Total Investment</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='browse-events.html'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Find Events to Sponsor
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
                    <div class="action-card" onclick="window.location.href='sponsorship-requests.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h3>Sponsorship Requests</h3>
                        <p>Review event sponsorship proposals</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='current-sponsorships.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                        </div>
                        <h3>Current Sponsorships</h3>
                        <p>Manage your active sponsorships</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='analytics.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </div>
                        <h3>Performance Analytics</h3>
                        <p>View sponsorship ROI and metrics</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='messages.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <h3>Messages</h3>
                        <p>Communicate with event organizers</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsorship Requests -->
        <section class="sponsorship-requests">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Sponsorship Requests</h2>
                    <a href="sponsorship-requests.html" class="view-all">View All</a>
                </div>
                <div class="requests-table" id="requestsTable">
                    <!-- Requests will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Active Sponsorships -->
        <section class="active-sponsorships">
            <div class="container">
                <div class="section-header">
                    <h2>Your Active Sponsorships</h2>
                    <a href="current-sponsorships.html" class="view-all">View All</a>
                </div>
                <div class="sponsorships-grid" id="sponsorshipsGrid">
                    <!-- Active sponsorships will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Performance Overview -->
        <section class="performance-overview">
            <div class="container">
                <div class="section-header">
                    <h2>Performance Overview</h2>
                    <a href="analytics.html" class="view-all">Detailed Reports</a>
                </div>
                <div class="performance-cards">
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #e0f2fe;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0369a1"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>24,589</h3>
                            <p>Total Audience Reach</p>
                        </div>
                    </div>
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #f0fdf4;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#15803d"
                                stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>4.8%</h3>
                            <p>Average Engagement Rate</p>
                        </div>
                    </div>
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #ffedd5;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ea580c"
                                stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>3.2x</h3>
                            <p>Average ROI</p>
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
    <script src="/unipulse/public/assets/js/Sponsor/dashboard-app.js"></script>
</body>

</html>