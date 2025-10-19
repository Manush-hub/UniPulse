<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Verification - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .verification-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .verification-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            border-bottom-color: #1E3A8A;
            color: #1E3A8A;
            font-weight: 600;
        }

        .organizer-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .organizer-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .organizer-info {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .organizer-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .organizer-details h3 {
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .verification-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-verified {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-rejected {
            background: #fecaca;
            color: #dc2626;
        }

        .document-list {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }

        .document-item {
            border: 2px dashed #d1d5db;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            border-color: #1E3A8A;
        }
    </style>
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
                <a href="/unipulse/public/moderatordashboard">Dashboard</a>
                <a href="/unipulse/public/reports">Reports</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">2</span>
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
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Organizer Verification</h1>
                        <p>Verify event organizers and manage verification requests</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingVerifications">8</span>
                                <span class="stat-label">Pending Requests</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="verifiedToday">3</span>
                                <span class="stat-label">Verified Today</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalVerified">156</span>
                                <span class="stat-label">Total Verified</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="showVerificationForm()">
                            <i class="fas fa-plus"></i>
                            Add Organizer
                        </button>
                        <button class="btn btn-outline" onclick="exportVerifications()">
                            <i class="fas fa-download"></i>
                            Export List
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Overview -->
        <section class="quick-actions">
            <div class="container">
                <div class="verification-stats">
                    <div class="stat-card">
                        <div class="stat-number">45%</div>
                        <div class="stat-label">Verification Rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">1.2 days</div>
                        <div class="stat-label">Avg. Processing Time</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">92%</div>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Pending Documents</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tabs -->
        <section class="pending-reviews">
            <div class="container">
                <div class="verification-tabs">
                    <div class="tab active" onclick="switchTab('pending')">Pending Verification</div>
                    <div class="tab" onclick="switchTab('verified')">Verified Organizers</div>
                    <div class="tab" onclick="switchTab('rejected')">Rejected Requests</div>
                    <div class="tab" onclick="switchTab('all')">All Organizers</div>
                </div>

                <!-- Pending Verification Tab -->
                <div class="tab-content" id="pendingTab">
                    <div class="section-header">
                        <h2>Pending Verification Requests</h2>
                        <span class="badge">8 requests</span>
                    </div>

                    <div class="organizers-list">
                        <div class="organizer-card">
                            <div class="organizer-header">
                                <div class="organizer-info">
                                    <img src="/unipulse/public/assets/images/organizer1.jpg" alt="Organizer"
                                        class="organizer-avatar">
                                    <div class="organizer-details">
                                        <h3>Computer Science Department</h3>
                                        <p>University Official Department</p>
                                        <div class="contact-info">
                                            <span><i class="fas fa-envelope"></i> cs-dept@university.edu</span>
                                            <span><i class="fas fa-phone"></i> +1 (555) 123-4567</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="verification-badge badge-pending">Pending Review</span>
                            </div>

                            <div class="verification-documents">
                                <h4>Verification Documents</h4>
                                <div class="document-list">
                                    <div class="document-item" onclick="viewDocument('department_id')">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                        <div>Department ID</div>
                                    </div>
                                    <div class="document-item" onclick="viewDocument('authorization_letter')">
                                        <i class="fas fa-file-image fa-2x"></i>
                                        <div>Authorization Letter</div>
                                    </div>
                                </div>
                            </div>

                            <div class="verification-actions">
                                <button class="review-btn approve" onclick="verifyOrganizer(1)">
                                    <i class="fas fa-check"></i>
                                    Verify Organizer
                                </button>
                                <button class="review-btn reject" onclick="rejectOrganizer(1)">
                                    <i class="fas fa-times"></i>
                                    Reject Request
                                </button>
                                <button class="review-btn view" onclick="requestMoreInfo(1)">
                                    <i class="fas fa-info-circle"></i>
                                    Request Info
                                </button>
                            </div>
                        </div>

                        <div class="organizer-card">
                            <div class="organizer-header">
                                <div class="organizer-info">
                                    <img src="/unipulse/public/assets/images/organizer2.jpg" alt="Organizer"
                                        class="organizer-avatar">
                                    <div class="organizer-details">
                                        <h3>Student Music Club</h3>
                                        <p>Student Organization</p>
                                        <div class="contact-info">
                                            <span><i class="fas fa-envelope"></i> music-club@university.edu</span>
                                            <span><i class="fas fa-user"></i> John Doe (President)</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="verification-badge badge-pending">Pending Review</span>
                            </div>

                            <div class="verification-documents">
                                <h4>Verification Documents</h4>
                                <div class="document-list">
                                    <div class="document-item" onclick="viewDocument('club_registration')">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                        <div>Club Registration</div>
                                    </div>
                                    <div class="document-item" onclick="viewDocument('executive_board')">
                                        <i class="fas fa-file-image fa-2x"></i>
                                        <div>Executive Board List</div>
                                    </div>
                                </div>
                            </div>

                            <div class="verification-actions">
                                <button class="review-btn approve" onclick="verifyOrganizer(2)">
                                    <i class="fas fa-check"></i>
                                    Verify Organizer
                                </button>
                                <button class="review-btn reject" onclick="rejectOrganizer(2)">
                                    <i class="fas fa-times"></i>
                                    Reject Request
                                </button>
                                <button class="review-btn view" onclick="requestMoreInfo(2)">
                                    <i class="fas fa-info-circle"></i>
                                    Request Info
                                </button>
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

    <!-- Document Viewer Modal -->
    <div id="documentModal" class="modal">
        <div class="modal-content" style="max-width: 90%; height: 90%;">
            <span class="close-button" onclick="closeModal('documentModal')">&times;</span>
            <h3 id="modalTitle">Document Viewer</h3>
            <div class="modal-body" id="modalBody" style="height: calc(100% - 60px);">
                <!-- Document will be displayed here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/organizer-verification.js"></script>
</body>

</html>