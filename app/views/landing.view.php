<?php
// Define role-specific content
$roleConfig = [
    'User' => [
        'pageTitle' => 'UniPulse - Discover University Events',
        'bannerHeading' => 'Boost Your Events for Maximum Visibility!',
        'bannerDescription' => 'Stand out and reach more participants by boosting your events on UniPulse',
        'bannerButtonText' => 'Explore Events',
        'bannerButtonLink' => '/unipulse/public/user/events',
        'eventsLink' => '/unipulse/public/user/events',
        'showSearchSection' => false,
        'showCategoriesSection' => false
    ],
    'Publisher' => [
        'pageTitle' => 'UniPulse - Publisher Landing',
        'bannerHeading' => 'Boost Your Events for Maximum Visibility!',
        'bannerDescription' => 'Stand out and reach more participants by boosting your events on UniPulse',
        'bannerButtonText' => 'Go to Dashboard',
        'bannerButtonLink' => '/unipulse/public/publisher/dashboard',
        'eventsLink' => '/unipulse/public/publisher/events',
        'showSearchSection' => true,
        'showCategoriesSection' => true
    ],
    'Sponsor' => [
        'pageTitle' => 'UniPulse - Discover University Events',
        'bannerHeading' => 'Sponsor Boosted Events!',
        'bannerDescription' => 'Connect with top university events and maximize your brand exposure',
        'bannerButtonText' => 'Find Events',
        'bannerButtonLink' => '/unipulse/public/sponsor/events',
        'eventsLink' => '/unipulse/public/sponsor/events',
        'showSearchSection' => true,
        'showCategoriesSection' => true
    ],
    'Admin' => [
        'pageTitle' => 'UniPulse - Admin Home',
        'bannerHeading' => 'No Active Boosted Events',
        'bannerDescription' => 'Publishers can boost their events to appear here in the spotlight.',
        'bannerButtonText' => 'View All Events',
        'bannerButtonLink' => '/unipulse/public/admin/allevents',
        'eventsLink' => '/unipulse/public/admin/allevents',
        'showSearchSection' => false,
        'showCategoriesSection' => false
    ]
];

// Get current role from data or default to 'User'
$currentRoleRaw = strtolower((string)($userRole ?? 'User'));
$roleMap = [
    'admin' => 'Admin',
    'moderator' => 'Moderator',
    'publisher' => 'Publisher',
    'sponsor' => 'Sponsor',
    'public' => 'User',
    'university' => 'User',
    'user' => 'User'
];
$currentRole = $roleMap[$currentRoleRaw] ?? 'User';
$config = $roleConfig[$currentRole] ?? $roleConfig['User'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['pageTitle']; ?></title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/landing-style.css">
</head>

<body>
    <!-- Header -->
    <?php 
    $pageConfig = ['activeNav' => 'home'];
    include __DIR__ . '/' . $currentRole . '/components/header.php'; 
    ?>

    <!-- Hero Section with Boosted Events Carousel -->
    <section class="hero-section">
        <!-- Promotional Banner (Always Visible) -->
        <div class="boost-promo-banner" id="boostPromoBanner">
            <div class="banner-content">
                <div class="banner-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                    </svg>
                </div>
                <h2><?php echo $config['bannerHeading']; ?></h2>
                <p><?php echo $config['bannerDescription']; ?></p>
                <button onclick="location.href='<?php echo $config['bannerButtonLink']; ?>'" class="banner-cta-btn">
                    <?php echo $config['bannerButtonText']; ?>
                </button>
            </div>
        </div>
        
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

    

    <!-- Upcoming Events -->
    <section class="upcoming-section">
        <div class="container">
            <div class="section-header">
                <h2>Upcoming in <span class="highlight">24h</span></h2>
                <a href="<?php echo $config['eventsLink']; ?>" class="view-more">View more</a>
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
                <a href="<?php echo $config['eventsLink']; ?>" class="view-more">View more</a>
            </div>
            <div class="events-grid" id="moreEventsGrid">
                <!-- More events will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/Components/footer.php'; ?>

    <!-- Pass PHP data to JavaScript -->
        <script src="/unipulse/public/assets/js/landing-app.js"></script>
    <script src="/unipulse/public/assets/js/landing-app.js?v=<?= time() ?>"></script>
</body>
</html>
