<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Admin Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
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
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername"><?php echo isset($user['name']) ? htmlspecialchars(explode(' ', $user['name'])[0]) : 'Admin'; ?></span>! 👋</h1>
                        <p>Monitor system performance and manage platform operations from your admin dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalUsers">2,847</span>
                                <span class="stat-label">Total Users</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalModerators"><?php echo isset($stats['total_moderators']) ? $stats['total_moderators'] : 0; ?></span>
                                <span class="stat-label">Total Moderators</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalAdmins"><?php echo isset($stats['total_admins']) ? $stats['total_admins'] : 1; ?></span>
                                <span class="stat-label">Total Admins</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/admin/moderators'">
                            <i class="fas fa-user-shield"></i>
                            Manage Moderators
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/admin/admins'">
                            <i class="fas fa-users-cog"></i>
                            Manage Admins
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
                    <div class="action-card" onclick="window.location.href='/unipulse/public/admin/moderators_list'">
                        <div class="action-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>Moderator Management</h3>
                        <p>Add and manage platform moderators</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='/unipulse/public/admin/admins'">
                        <div class="action-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3>Admin Management</h3>
                        <p>Manage admin accounts and permissions</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='approval-queue.html'">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Approval Queue</h3>
                        <p>Review pending approvals</p>
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
    <?php include __DIR__ . '/../components/footer.php'; ?>
    

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

    <script src="/unipulse/public/assets/js/Admin/dashboard-app.js"></script>
</body>

</html>