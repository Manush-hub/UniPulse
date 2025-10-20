<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Discover University Events</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('landing-style.css'); ?>">
</head>

<body>
    <!-- Header -->
    <?php 
    $pageConfig = ['activeNav' => 'home'];
    include __DIR__ .'/components/header.php'; 
    ?>

    <!-- Hero Section with Boosted Event Carousel -->
    <section class="hero-section">
        <div class="hero-carousel" id="heroCarousel">
            <!-- Hero slides will be dynamically loaded here -->
        </div>
        
        <!-- Hero Controls -->
        <div class="hero-controls">
            <button class="hero-nav-btn prev-btn" onclick="previousSlide()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15,18 9,12 15,6"></polyline>
                </svg>
            </button>
            <button class="hero-nav-btn next-btn" onclick="nextSlide()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9,18 15,12 9,6"></polyline>
                </svg>
            </button>
        </div>
        
        <!-- Hero Indicators -->
        <div class="hero-indicators" id="heroIndicators">
            <!-- Indicators will be dynamically created -->
        </div>
        
        <!-- Hero Progress Bar -->
        <div class="hero-progress">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" placeholder="Find the event you're interested in" class="search-input">
                </div>
                <div class="location-box">
                    <svg class="location-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <select class="location-select">
                        <option>All Universities</option>
                        <option>University of Colombo</option>
                        <option>University of Moratuwa</option>
                        <option>University of Peradeniya</option>
                        <option>University of Kelaniya</option>
                    </select>
                </div>
                <button class="search-btn" onclick="searchEvents()">
                    <span>Search</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2>Explore by <span class="highlight">categories</span></h2>
            <div class="categories-grid">
                <div class="category-card" onclick="filterByCategory('technology')">
                    <div class="category-icon tech">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <span>Technology</span>
                </div>
                <div class="category-card" onclick="filterByCategory('sports')">
                    <div class="category-icon sports">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                            <path d="M2 12h20"></path>
                        </svg>
                    </div>
                    <span>Sports</span>
                </div>
                <div class="category-card" onclick="filterByCategory('cultural')">
                    <div class="category-icon cultural">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2h-4m-6 0V9a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <span>Cultural</span>
                </div>
                <div class="category-card" onclick="filterByCategory('academic')">
                    <div class="category-icon academic">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <span>Academic</span>
                </div>
                <div class="category-card" onclick="filterByCategory('workshop')">
                    <div class="category-icon workshop">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                    </div>
                    <span>Workshop</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="upcoming-section">
        <div class="container">
            <div class="section-header">
                <h2>Upcoming in <span class="highlight">24h</span></h2>
                <a href="all-events.html" class="view-more">View more</a>
            </div>
            <div class="upcoming-grid" id="upcomingEventsGrid">
                <!-- Upcoming events will be loaded here -->
            </div>
        </div>
    </section>

    <!-- More Events -->
    <section class="more-events">
        <div class="container">
            <div class="section-header">
                <h2>More events</h2>
                <a href="all-events.html" class="view-more">View more</a>
            </div>
            <div class="events-grid" id="moreEventsGrid">
                <!-- More events will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="<?php echo $controller->loadJS('landing-app.js'); ?>"></script>
</body>
</html>