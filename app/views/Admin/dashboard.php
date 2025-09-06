<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Admin Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard.css">
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
                <a href="/unipulse/public/adminlanding">Home</a>
                <a href="/unipulse/public/users">User Management</a>
                <a href="/unipulse/public/admindashboard" class="active">Dashboard</a>
                <a href="/unipulse/public/systemsettings">System Settings</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">5</span>
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
                    <img src="/unipulse/public/assets/images/admin.jpg" alt="Admin" class="admin-avatar">
                    <div class="user-info">
                        <span class="username" id="username">Robert Johnson</span>
                        <span class="user-role" id="userRole">System Administrator</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.html"><i class="fas fa-user-cog"></i> Profile Settings</a>
                        <a href="auditlog.html"><i class="fas fa-clipboard-list"></i> Audit Log</a>
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
                        <h1>Welcome back, <span id="welcomeUsername">Robert</span>! 👋</h1>
                        <p>Monitor system performance and manage platform operations from your admin dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalUsers">2,847</span>
                                <span class="stat-label">Total Users</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="activeEvents">124</span>
                                <span class="stat-label">Active Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="pendingApprovals">18</span>
                                <span class="stat-label">Pending Approvals</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="systemHealth">98%</span>
                                <span class="stat-label">System Health</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='user-management.html'">
                            <i class="fas fa-users"></i>
                            Manage Users
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='system-reports.html'">
                            <i class="fas fa-chart-bar"></i>
                            System Reports
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='settings.html'">
                            <i class="fas fa-cog"></i>
                            System Settings
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
                    <div class="action-card" onclick="window.location.href='user-management.html'">
                        <div class="action-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h3>User Management</h3>
                        <p>Manage all system users and permissions</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='content-moderation.html'">
                        <div class="action-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Content Moderation</h3>
                        <p>Review and moderate platform content</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='approval-queue.html'">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Approval Queue</h3>
                        <p>Review pending approvals</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='system-health.html'">
                        <div class="action-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3>System Health</h3>
                        <p>Monitor system performance</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- System Overview -->
        <section class="system-overview">
            <div class="container">
                <div class="section-header">
                    <h2>System Overview</h2>
                    <a href="system-reports.html" class="view-all">View Detailed Reports</a>
                </div>
                <div class="overview-cards">
                    <div class="overview-card">
                        <div class="overview-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="overview-content">
                            <h3>User Statistics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">2,847</span>
                                    <span class="stat-label">Total Users</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">127</span>
                                    <span class="stat-label">New This Week</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">94%</span>
                                    <span class="stat-label">Active Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="overview-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="overview-content">
                            <h3>Event Statistics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">124</span>
                                    <span class="stat-label">Active Events</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">42</span>
                                    <span class="stat-label">This Week</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">78%</span>
                                    <span class="stat-label">Attendance Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="overview-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="overview-content">
                            <h3>Performance Metrics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">98%</span>
                                    <span class="stat-label">Uptime</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">1.2s</span>
                                    <span class="stat-label">Avg. Response</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">0.2%</span>
                                    <span class="stat-label">Error Rate</span>
                                </div>
                            </div>
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
                        <div class="section-header">
                            <h2>Recent Activity</h2>
                            <a href="auditlog.html" class="view-all">View Full Log</a>
                        </div>
                        <div class="activity-list" id="activityList">
                            <!-- Activity items will be loaded here -->
                        </div>
                    </div>
                    <div class="sidebar">
                        <div class="sidebar-widget">
                            <h3>Pending Approvals</h3>
                            <div class="approval-list" id="approvalList">
                                <!-- Approval items will be loaded here -->
                            </div>
                            <a href="approval-queue.html" class="view-all">View All Pending</a>
                        </div>
                        <div class="sidebar-widget">
                            <h3>System Health</h3>
                            <div class="health-status">
                                <div class="health-metric">
                                    <span class="metric-label">CPU Usage</span>
                                    <div class="metric-bar">
                                        <div class="metric-fill" data-width="65%"></div>
                                    </div>
                                    <span class="metric-value">65%</span>
                                </div>
                                <div class="health-metric">
                                    <span class="metric-label">Memory Usage</span>
                                    <div class="metric-bar">
                                        <div class="metric-fill" data-width="42%"></div>
                                    </div>
                                    <span class="metric-value">42%</span>
                                </div>
                                <div class="health-metric">
                                    <span class="metric-label">Storage</span>
                                    <div class="metric-bar">
                                        <div class="metric-fill" data-width="78%"></div>
                                    </div>
                                    <span class="metric-value">78%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Management Preview -->
        <section class="user-management-preview">
            <div class="container">
                <div class="section-header">
                    <h2>Recent User Registrations</h2>
                    <a href="user-management.html" class="view-all">Manage All Users</a>
                </div>
                <div class="user-table">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- User rows will be loaded here -->
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
                <span class="system-version">v2.4.1</span>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('userModal')">&times;</span>
            <h3 id="modalTitle">User Details</h3>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Admin/dashboard.js"></script>
</body>

</html>