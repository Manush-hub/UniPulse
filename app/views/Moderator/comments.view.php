<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator - Event Comments</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/moderator-style.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/comments-style.css">
</head>
<body>
    <?php include '../app/views/moderator-header.php'; ?>
    
    <div class="moderator-container">
        <div class="moderator-sidebar">
            <h3>University Comments</h3>
            <ul>
                <li><a href="#university-comments" class="tab-link active" data-tab="university-comments">Comments</a></li>
                <li><a href="#comment-overview" class="tab-link" data-tab="comment-overview">Overview</a></li>
            </ul>
        </div>
        
        <div class="moderator-content">
            <!-- University Comments Tab -->
            <div id="university-comments" class="tab-content active">
                <div class="comments-header">
                    <h2>Comments on <?= htmlspecialchars($_SESSION['user']['university'] ?? 'University') ?> Events</h2>
                    <div class="filters">
                        <select id="event-filter">
                            <option value="">All Events</option>
                        </select>
                        <select id="rating-filter">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                        <input type="text" id="search-comments" placeholder="Search comments...">
                    </div>
                </div>
                
                <div class="comments-list" id="moderator-comments-list">
                    <div class="loading">Loading comments...</div>
                </div>
            </div>
            
            <!-- Overview Tab -->
            <div id="comment-overview" class="tab-content">
                <div class="overview-header">
                    <h2>Comments Overview</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 id="total-university-comments">0</h3>
                        <p>Total Comments</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="average-university-rating">0.0</h3>
                        <p>Average Rating</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="active-university-events">0</h3>
                        <p>Events with Comments</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="university-publishers">0</h3>
                        <p>Active Publishers</p>
                    </div>
                </div>
                
                <div class="recent-activity">
                    <h3>Recent Comment Activity</h3>
                    <div id="recent-comments-list"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= ROOT ?>/assets/js/Moderator/comments-dashboard.js"></script>
</body>
</html>