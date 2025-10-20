<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments Moderation - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .comments-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .filter-group select {
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
        }

        .comment-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border-left: 4px solid #e5e7eb;
        }

        .comment-card.flagged {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .comment-card.reviewed {
            border-left-color: #10b981;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .comment-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            margin-bottom: 0.25rem;
            color: #1f2937;
        }

        .user-role {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .comment-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .comment-content {
            margin-bottom: 1rem;
            line-height: 1.6;
            color: #374151;
        }

        .comment-event {
            background: #f8fafc;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .flag-reason {
            background: #fef3c7;
            padding: 0.5rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .sentiment-positive {
            color: #10b981;
        }

        .sentiment-negative {
            color: #ef4444;
        }

        .sentiment-neutral {
            color: #6b7280;
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
                        <span class="notification-badge" id="notificationBadge">7</span>
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
                        <h1>Comments Moderation</h1>
                        <p>Review and manage user comments across all events</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingComments">34</span>
                                <span class="stat-label">Pending Review</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="flaggedToday">12</span>
                                <span class="stat-label">Flagged Today</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="moderatedToday">28</span>
                                <span class="stat-label">Moderated Today</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="enableAutoModeration()">
                            <i class="fas fa-robot"></i>
                            Auto-Moderation
                        </button>
                        <button class="btn btn-outline" onclick="showCommentGuidelines()">
                            <i class="fas fa-book"></i>
                            Guidelines
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters -->
        <section class="quick-actions">
            <div class="container">
                <div class="comments-filters">
                    <div class="filter-group">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter" onchange="filterComments()">
                            <option value="all">All Comments</option>
                            <option value="pending">Pending Review</option>
                            <option value="flagged">Flagged</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sentimentFilter">Sentiment:</label>
                        <select id="sentimentFilter" onchange="filterComments()">
                            <option value="all">All Sentiments</option>
                            <option value="positive">Positive</option>
                            <option value="negative">Negative</option>
                            <option value="neutral">Neutral</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="eventFilter">Event:</label>
                        <select id="eventFilter" onchange="filterComments()">
                            <option value="all">All Events</option>
                            <option value="tech-symposium">Tech Symposium</option>
                            <option value="music-festival">Music Festival</option>
                            <option value="career-fair">Career Fair</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="dateFilter">Date Posted:</label>
                        <select id="dateFilter" onchange="filterComments()">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
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
                        <button class="btn btn-outline" onclick="selectAllComments()">
                            <i class="fas fa-check-square"></i>
                            Select All
                        </button>
                        <button class="btn btn-primary" onclick="approveSelectedComments()">
                            <i class="fas fa-check-double"></i>
                            Approve Selected
                        </button>
                        <button class="btn reject" onclick="rejectSelectedComments()">
                            <i class="fas fa-times-circle"></i>
                            Reject Selected
                        </button>
                        <button class="btn btn-outline" onclick="exportComments()">
                            <i class="fas fa-download"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comments List -->
        <section class="comments-moderation">
            <div class="container">
                <div class="section-header">
                    <h2>Comments for Review</h2>
                    <span class="badge" id="commentsCount">34 comments</span>
                </div>

                <div class="comments-list" id="commentsList">
                    <!-- Comment 1 -->
                    <div class="comment-card flagged">
                        <div class="comment-header">
                            <div class="comment-user">
                                <img src="/unipulse/public/assets/images/user1.jpg" alt="User" class="user-avatar">
                                <div class="user-info">
                                    <h4>Alex Johnson</h4>
                                    <div class="user-role">Student</div>
                                </div>
                            </div>
                            <div class="comment-meta">
                                <span><i class="fas fa-calendar"></i> 2 hours ago</span>
                                <span class="sentiment-negative"><i class="fas fa-frown"></i> Negative</span>
                            </div>
                        </div>

                        <div class="flag-reason">
                            <strong>Flagged for:</strong> Inappropriate language
                        </div>

                        <div class="comment-content">
                            "This event was completely disorganized and a waste of time. The organizers had no idea what
                            they were doing and the speakers were unprepared. Would not recommend to anyone!"
                        </div>

                        <div class="comment-event">
                            <strong>Event:</strong> Annual Tech Symposium 2024
                        </div>

                        <div class="comment-actions">
                            <input type="checkbox" class="comment-checkbox" value="1">
                            <button class="review-btn approve" onclick="approveComment(1)">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="review-btn reject" onclick="rejectComment(1)">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                            <button class="review-btn view" onclick="viewCommentContext(1)">
                                <i class="fas fa-eye"></i>
                                View Context
                            </button>
                            <button class="review-btn" onclick="warnUser(1)">
                                <i class="fas fa-exclamation-triangle"></i>
                                Warn User
                            </button>
                        </div>
                    </div>

                    <!-- Comment 2 -->
                    <div class="comment-card">
                        <div class="comment-header">
                            <div class="comment-user">
                                <img src="/unipulse/public/assets/images/user2.jpg" alt="User" class="user-avatar">
                                <div class="user-info">
                                    <h4>Sarah Miller</h4>
                                    <div class="user-role">Faculty</div>
                                </div>
                            </div>
                            <div class="comment-meta">
                                <span><i class="fas fa-calendar"></i> 4 hours ago</span>
                                <span class="sentiment-positive"><i class="fas fa-smile"></i> Positive</span>
                            </div>
                        </div>

                        <div class="comment-content">
                            "Excellent event! The guest speakers were very knowledgeable and the networking
                            opportunities were fantastic. Looking forward to the next one!"
                        </div>

                        <div class="comment-event">
                            <strong>Event:</strong> Annual Tech Symposium 2024
                        </div>

                        <div class="comment-actions">
                            <input type="checkbox" class="comment-checkbox" value="2">
                            <button class="review-btn approve" onclick="approveComment(2)">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="review-btn reject" onclick="rejectComment(2)">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                            <button class="review-btn view" onclick="viewCommentContext(2)">
                                <i class="fas fa-eye"></i>
                                View Context
                            </button>
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

    <!-- Comment Context Modal -->
    <div id="commentModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-button" onclick="closeModal('commentModal')">&times;</span>
            <h3 id="modalTitle">Comment Context</h3>
            <div class="modal-body" id="modalBody">
                <!-- Comment context will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/comments-moderation.js"></script>
</body>

</html>