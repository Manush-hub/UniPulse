<?php $this->view('header', $data); ?>

<div class="publisher-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-comments"></i>
                        Event Comments
                    </h1>
                    <p>Manage and view comments on your events</p>
                </div>
                
                <div class="header-right">
                    <div class="notification-badge" id="notificationBadge">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count" id="badgeCount">0</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Statistics Cards -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalComments">0</h3>
                        <p>Total Comments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="eventsWithComments">0</h3>
                        <p>Events with Comments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="averageRating">0.0</h3>
                        <p>Average Rating</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="commentsToday">0</h3>
                        <p>Comments Today</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comments Section -->
    <section class="comments-section">
        <div class="container">
            <div class="section-header">
                <h2>Recent Comments</h2>
                <div class="section-actions">
                    <select id="filterSelect" class="filter-select">
                        <option value="all">All Comments</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="rated">With Ratings</option>
                    </select>
                    
                    <button class="btn-refresh" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <div class="comments-container" id="commentsContainer">
                <div class="loading-spinner" id="loadingSpinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading comments...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifications Panel -->
    <div class="notifications-panel" id="notificationsPanel">
        <div class="panel-header">
            <h3>Notifications</h3>
            <div class="panel-actions">
                <button class="btn-text" id="markAllReadBtn">Mark All Read</button>
                <button class="btn-close" id="closeNotificationsBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="notifications-content" id="notificationsContent">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
    </div>

    <!-- Comment Details Modal -->
    <div class="modal" id="commentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Comment Details</h3>
                <button class="btn-close" onclick="closeCommentModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body" id="commentModalBody">
                <!-- Comment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Include the CSS and JavaScript -->
<link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/comments-style.css">
<script src="/unipulse/public/assets/js/Publisher/comments-app.js"></script>

<?php $this->view('footer'); ?>