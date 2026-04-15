<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Event Comments</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/admin-style.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/comments-style.css">
</head>
<body>
    <?php include __DIR__ . '/components/header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>Comments Management</h3>
            <ul>
                <li><a href="#all-comments" class="tab-link active" data-tab="all-comments">All Comments</a></li>
                <li><a href="#comment-stats" class="tab-link" data-tab="comment-stats">Statistics</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <!-- All Comments Tab -->
            <div id="all-comments" class="tab-content active">
                <div class="comments-header">
                    <h2>All Event Comments</h2>
                    <div class="filters">
                        <select id="event-filter">
                            <option value="">All Events</option>
                        </select>
                        <select id="user-type-filter">
                            <option value="">All User Types</option>
                            <option value="university">University Users</option>
                            <option value="public">Public Users</option>
                            <option value="publisher">Publishers</option>
                            <option value="sponsor">Sponsors</option>
                        </select>
                        <input type="text" id="search-comments" placeholder="Search comments...">
                    </div>
                </div>
                
                <div class="comments-list" id="admin-comments-list">
                    <div class="loading">Loading comments...</div>
                </div>
            </div>
            
            <!-- Statistics Tab -->
            <div id="comment-stats" class="tab-content">
                <div class="stats-header">
                    <h2>Comment Statistics</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 id="total-comments">0</h3>
                        <p>Total Comments</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="average-rating">0.0</h3>
                        <p>Average Rating</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="active-events">0</h3>
                        <p>Events with Comments</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="recent-comments">0</h3>
                        <p>Comments This Week</p>
                    </div>
                </div>
                
                <div class="charts-section">
                    <div class="chart-container">
                        <canvas id="ratingsChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="commentsTimelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= ROOT ?>/assets/js/Admin/comments-dashboard.js"></script>
</body>
</html>