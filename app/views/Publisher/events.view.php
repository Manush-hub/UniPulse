<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Events</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('events-style.css'); ?>">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1>My Events</h1>
                    <p>Manage and monitor your published events</p>
                    <a href="/unipulse/public/createevent" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create New Event
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="container">
                <div class="filter-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search your events..." class="search-input">
                        <button class="search-btn" onclick="searchEvents()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <select id="statusFilter" onchange="filterEvents()">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        
                        <select id="categoryFilter" onchange="filterEvents()">
                            <option value="">All Categories</option>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="technology">Technology</option>
                            <option value="social">Social</option>
                            <option value="workshop">Workshop</option>
                        </select>
                        
                        <button class="filter-btn" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="events-section">
            <div class="container">
                <div class="events-grid" id="eventsGrid">
                    <!-- Events will be loaded here dynamically -->
                </div>
                
                <!-- Loading Spinner -->
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                    <p>Loading your events...</p>
                </div>
                
                <!-- No Events Found -->
                <div class="no-events" id="noEvents" style="display: none;">
                    <div class="no-events-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h3>No Events Found</h3>
                    <p>Create your first event to get started</p>
                    <a href="/unipulse/public/createevent" class="btn btn-primary">Create Event</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="<?php echo $controller->loadJS('events-app.js'); ?>"></script>
</body>
</html>
