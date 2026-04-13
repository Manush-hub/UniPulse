<?php
// Define role-specific content
$roleConfig = [
    'User' => [
        'pageTitle' => 'UniPulse - All Events',
        'pageHeading' => 'All Events',
        'pageDescription' => 'Discover and participate in university events across Sri Lanka',
        'showCategories' => true,
        'cssFile' => '/unipulse/public/assets/css/events-style.css',
        'additionalCss' => null,
        'searchInputId' => 'eventNameFilter'
    ],
    'Publisher' => [
        'pageTitle' => 'UniPulse - Publisher Events',
        'pageHeading' => 'All Events',
        'pageDescription' => 'Discover and participate in university events across Sri Lanka',
        'showCategories' => false,
        'cssFile' => '/unipulse/public/assets/css/Publisher/events-style.css',
        'additionalCss' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'searchInputId' => 'searchInput'
    ],
    'Sponsor' => [
        'pageTitle' => 'UniPulse - All Events',
        'pageHeading' => 'All Events',
        'pageDescription' => 'Discover and participate in university events across Sri Lanka',
        'showCategories' => false,
        'cssFile' => '/unipulse/public/assets/css/events-style.css',
        'additionalCss' => null,
        'searchInputId' => 'searchInput'
    ],
    'Moderator' => [
        'pageTitle' => 'UniPulse - Moderator Events',
        'pageHeading' => 'All Events',
        'pageDescription' => 'Discover and participate in university events across Sri Lanka',
        'showCategories' => false,
        'cssFile' => '/unipulse/public/assets/css/Moderator/events-style.css',
        'additionalCss' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'searchInputId' => 'searchInput'
    ],
    'Admin' => [
        'pageTitle' => 'UniPulse - Admin Events',
        'pageHeading' => 'All Events',
        'pageDescription' => 'Manage all university events across Sri Lanka',
        'showCategories' => false,
        'cssFile' => '/unipulse/public/assets/css/Admin/events-style.css',
        'additionalCss' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'searchInputId' => 'searchInput'
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
$cssFilePath = $_SERVER['DOCUMENT_ROOT'] . $config['cssFile'];
$cssVersion = file_exists($cssFilePath) ? filemtime($cssFilePath) : time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['pageTitle']; ?></title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="<?php echo $config['cssFile'] . '?v=' . $cssVersion; ?>">
    <?php if ($config['additionalCss']): ?>
        <link rel="stylesheet" href="<?php echo $config['additionalCss']; ?>">
    <?php endif; ?>
    <?php if ($currentRole === 'Moderator'): ?>
    <?php endif; ?>
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => $currentRole === 'Admin' ? 'allevents' : 'events'];
    if ($currentRole === 'Moderator') {
        $headerCssLoaded = true;
    }
    include __DIR__ . '/' . $currentRole . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1><?php echo $config['pageHeading']; ?></h1>
                <p><?php echo $config['pageDescription']; ?></p>
                <!-- <?php if ($config['showCategories']): ?>
                <div class="container_categories" id="categoriesContainer">
                    <p data-category="technology">Technology <span class="category-count">0</span></p>
                    <p data-category="sports">Sports <span class="category-count">0</span></p>
                    <p data-category="cultural">Cultural <span class="category-count">0</span></p>
                    <p data-category="academic">Academic <span class="category-count">0</span></p>
                    <p data-category="social">Social <span class="category-count">0</span></p>
                </div>
                <?php endif; ?> -->
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="container">
                <div class="filter-controls">
                    <div class="search-box">
                        <input type="text" id="<?php echo $config['searchInputId']; ?>" placeholder="Search events..." class="filter-input search-input">
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

                        <select id="statusFilter" onchange="filterEvents()">
                            <option value="">All Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>

                        <button class="filter-btn" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sponsorship Opportunities Section (Sponsors Only) -->
        <?php if ($currentRole === 'Sponsor' && isset($sponsorshipEvents) && !empty($sponsorshipEvents)): ?>
            <div class="sponsorship-section" style="margin-bottom: 3rem;">
                <div class="container">
                    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <div>
                            <h2 style="color: #333; font-size: 1.8rem; margin-bottom: 0.5rem;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                                Sponsorship Opportunities
                            </h2>
                            <p style="color: #666; margin: 0;">Events accepting sponsorships from organizations and businesses</p>
                        </div>
                        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                            <?php echo count($sponsorshipEvents); ?> Events Available
                        </span>
                    </div>

                    <div class="sponsorship-events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                        <?php foreach ($sponsorshipEvents as $event): ?>
                            <div class="sponsorship-event-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease; border: 2px solid transparent; cursor: pointer;"
                                onclick="window.location.href='/unipulse/public/Sponsor/Events/event/<?php echo $event['id']; ?>'"
                                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 12px 24px rgba(102,126,234,0.3)'; this.style.borderColor='#667eea';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.borderColor='transparent';">

                                <div class="sponsorship-badge" style="position: relative; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0.75rem 1rem; color: white;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-weight: 600; font-size: 0.9rem;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.3rem;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                            Seeking Sponsors
                                        </span>
                                        <?php if (isset($event['package_count']) && $event['package_count'] > 0): ?>
                                            <span style="background: rgba(255,255,255,0.3); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                <?php echo $event['package_count']; ?> Packages
                                            </span>
                                        <?php else: ?>
                                            <span style="background: rgba(255,255,255,0.3); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                                Open for Offers
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php
                                // Get the event image - prepend base path if relative
                                $eventImage = null;
                                if (!empty($event['image_url'])) {
                                    $eventImage = $event['image_url'];
                                    // If the path doesn't start with http or /, prepend the base path
                                    if (!preg_match('#^(https?://|/)#', $eventImage)) {
                                        $eventImage = '/unipulse/public/' . $eventImage;
                                    }
                                }
                                ?>

                                <?php if ($eventImage): ?>
                                    <div class="event-image" style="height: 200px; overflow: hidden; position: relative;">
                                        <img src="<?php echo htmlspecialchars($eventImage); ?>"
                                            alt="<?php echo htmlspecialchars($event['title']); ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php if (isset($event['package_count']) && $event['package_count'] > 0 && isset($event['total_slots_available']) && $event['total_slots_available'] > 0): ?>
                                            <div style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.95); padding: 0.5rem 0.75rem; border-radius: 8px; font-weight: 600; color: #667eea; font-size: 0.9rem;">
                                                <?php echo $event['total_slots_available']; ?> Slots Available
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="event-placeholder" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <?php if (isset($event['package_count']) && $event['package_count'] > 0 && isset($event['total_slots_available']) && $event['total_slots_available'] > 0): ?>
                                            <div style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.95); padding: 0.5rem 0.75rem; border-radius: 8px; font-weight: 600; color: #667eea; font-size: 0.9rem;">
                                                <?php echo $event['total_slots_available']; ?> Slots Available
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="event-details" style="padding: 1.5rem;">
                                    <h3 style="color: #333; font-size: 1.2rem; margin-bottom: 0.75rem; line-height: 1.4;">
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </h3>

                                    <div class="event-meta" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
                                        <div style="display: flex; align-items: center; color: #666; font-size: 0.9rem;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                        </div>

                                        <div style="display: flex; align-items: center; color: #666; font-size: 0.9rem;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <?php echo htmlspecialchars($event['location'] ?? 'Location TBA'); ?>
                                        </div>

                                        <?php if (!empty($event['university'])): ?>
                                            <div style="display: flex; align-items: center; color: #667eea; font-size: 0.9rem; font-weight: 500;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                                </svg>
                                                <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $event['university']))); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="cta-section" style="display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #eee;">
                                        <span style="color: #667eea; font-weight: 600; font-size: 1.1rem;">
                                            View Packages
                                        </span>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="text-align: center; margin-top: 2rem;">
                        <hr style="border: none; border-top: 2px solid #eee; margin: 2rem 0;">
                        <h3 style="color: #333; font-size: 1.4rem; margin-bottom: 1rem;">All Public Events</h3>
                        <p style="color: #666;">Browse all available events below</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Show message if Sponsor but no sponsorship events -->
        <?php if ($currentRole === 'Sponsor' && (empty($sponsorshipEvents) || !isset($sponsorshipEvents))): ?>
            <div class="no-sponsorship-events" style="margin-bottom: 3rem;">
                <div class="container">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 2rem; text-align: center; color: white;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 1rem;">
                            <circle cx="12" cy="12" r="10" stroke="white"></circle>
                            <line x1="12" y1="8" x2="12" y2="12" stroke="white"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16" stroke="white"></line>
                        </svg>
                        <h3 style="color: white; font-size: 1.5rem; margin-bottom: 0.5rem;">No Sponsorship Opportunities Available</h3>
                        <p style="color: rgba(255,255,255,0.9); margin-bottom: 1rem;">There are currently no events seeking sponsors. Check back later for new opportunities!</p>
                        <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">In the meantime, you can browse all public events below.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Events Grid -->
        <div class="events-section">
            <div class="container">
                <?php if (isset($error)): ?>
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
                    <p>Loading events...</p>
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
                    <p>Try adjusting your search criteria or filters</p>
                </div>

                <!-- Load More Button -->
                <div class="load-more-section" id="loadMoreSection">
                    <button type="button" class="btn btn-outline" id="loadMoreBtn">Load More Events</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Pass PHP data to JavaScript -->
    <script>
        window.serverData = <?php echo json_encode($serverData ?? []); ?>;
        const userRole = '<?php echo $currentRole; ?>';
    </script>

    <!-- Load role-specific JavaScript -->
    <?php if ($currentRole === 'Publisher'): ?>
        <script src="/unipulse/public/assets/js/Publisher/events-app.js?v=<?= time() ?>"></script>
    <?php elseif ($currentRole === 'User'): ?>
        <script src="/unipulse/public/assets/js/User/events-app.js?v=<?= time() ?>"></script>
    <?php elseif ($currentRole === 'Moderator'): ?>
        <script src="/unipulse/public/assets/js/Moderator/events-app.js?v=<?= time() ?>"></script>
    <?php elseif ($currentRole === 'Sponsor'): ?>
        <script src="/unipulse/public/assets/js/Sponsor/events-app.js?v=<?= time() ?>"></script>
    <?php elseif ($currentRole === 'Admin'): ?>
        <script src="/unipulse/public/assets/js/events-app.js?v=<?= time() ?>"></script>
    <?php else: ?>
        <script src="/unipulse/public/assets/js/events-app.js?v=<?= time() ?>"></script>
    <?php endif; ?>
</body>

</html>