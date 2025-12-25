<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Hidden Events</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/events-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/hidden-events-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'hidden-events'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1>Hidden Events</h1>
                <p>Manage events that have been hidden from public view. You can restore them if needed.</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="container">
                <div class="filter-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search hidden events..." class="search-input">
                        <button class="search-btn" onclick="searchEvents()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <select id="categoryFilter" onchange="filterEvents()">
                            <option value="">All Categories</option>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="technology">Technology</option>
                            <option value="social">Social</option>
                            <option value="workshop">Workshop</option>
                        </select>
                        
                        <select id="universityFilter" onchange="filterEvents()">
                            <option value="">All Universities</option>
                            <option value="university-of-colombo">University of Colombo</option>
                            <option value="university-of-peradeniya">University of Peradeniya</option>
                            <option value="university-of-kelaniya">University of Kelaniya</option>
                            <option value="university-of-moratuwa">University of Moratuwa</option>
                            <option value="university-of-sri-jayewardenepura">University of Sri Jayewardenepura</option>
                        </select>
                        
                        <button class="filter-btn" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="events-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <!-- Error Message -->
                    <div class="error-message" style="text-align: center; padding: 2rem; background: #fee; border: 1px solid #fcc; border-radius: 8px; margin-bottom: 2rem;">
                        <h3 style="color: #c33;">Database Error</h3>
                        <p style="color: #666;"><?php echo htmlspecialchars($error); ?></p>
                        <p style="color: #666;">Please try refreshing the page or contact support if the problem persists.</p>
                    </div>
                <?php endif; ?>
                
                <div class="events-grid" id="eventsGrid">
                    <!-- Events will be loaded here dynamically -->
                </div>
                
                <!-- Loading Spinner -->
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                    <p>Loading hidden events...</p>
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
                    <h3>No Hidden Events Found</h3>
                    <p>All events are currently visible or try adjusting your search criteria</p>
                </div>
                
                <!-- Load More Button -->
                <div class="load-more-section" id="loadMoreSection">
                    <button class="btn btn-outline" onclick="loadMoreEvents()">Load More Events</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Pass server data to JavaScript -->
    <script>
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
    </script>

    <script src="/unipulse/public/assets/js/Moderator/hidden-events-app.js"></script>
    <script src="/unipulse/public/assets/js/Moderator/header.js"></script>
</body>
</html>
