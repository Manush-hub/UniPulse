<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Unforgettable Events Start Here</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/home-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <div class="logo">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
                </div>
            </div>
                <div class="unp-nav-group">
                    <button onclick="location.href='/unipulse/public/signin'" class="login-btn">LogIn</button>
                    <button onclick="location.href='/unipulse/public/signup'" class="get-started-btn">Register</button>
                </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

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
                <h2>Boost Your Events for Maximum Visibility!</h2>
                <p>Stand out and reach more participants by boosting your events on UniPulse</p>
                <button onclick="location.href='/unipulse/public/signin'" class="banner-cta-btn">Get Started</button>
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

    <!-- Upcoming Events Section -->
    <section class="upcoming-events" id="events">
        <div class="container">
            <h2>Upcoming Events</h2>
            <p class="section-subtitle">Discover the most spectacular events happening at top universities.</p>
            <div class="events-grid">
                <?php if (!empty($upcoming_events)): ?>
                    <?php foreach ($upcoming_events as $event): ?>
                        <?php
                            $eventImage = $event->cover_image ?: $event->image_url;
                            if ($eventImage && (strpos($eventImage, '/uploads/') === 0 || strpos($eventImage, 'uploads/') === 0)) {
                                $eventImage = '/unipulse/public' . (strpos($eventImage, '/') === 0 ? $eventImage : '/' . $eventImage);
                            }
                            if (empty($eventImage)) {
                                $eventImage = 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=300&h=200&fit=crop';
                            }
                        ?>
                        <div class="event-card">
                            <div class="event-image">
                                <img src="<?= htmlspecialchars($eventImage) ?>" alt="<?= htmlspecialchars($event->title) ?>">
                            </div>
                            <div class="event-info">
                                <h3><?= htmlspecialchars($event->title) ?></h3>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('M d, Y', strtotime($event->event_date)) ?> - <?= date('h:i A', strtotime($event->event_time)) ?>
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($event->location ?: $event->university_name) ?>
                                </p>
                                <button class="btn btn-outline" onclick="location.href='/unipulse/public/signin'">View Details</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="event-card">
                        <div class="event-info">
                            <h3>No upcoming public events right now</h3>
                            <p class="event-date"><i class="fas fa-calendar"></i> Check back soon for new events.</p>
                            <p class="event-location"><i class="fas fa-map-marker-alt"></i> UniPulse</p>
                            <button class="btn btn-outline" onclick="location.href='/unipulse/public/signin'">Sign In</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="pagination">
                <span class="dot active"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Powerful Features</h2>
            <p class="section-subtitle">Everything you need to discover, publish and participate in university events.</p>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Event Discovery</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Registration</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3>Ticket Booking</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Sponsorship</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Event Management</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Real-time Notifications</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Built for Everyone Section -->
    <section class="built-for-everyone">
        <div class="container">
            <h2>Built for Everyone</h2>
            <p class="section-subtitle">UniPulse serves different user types with tailored experiences and benefits.</p>
            <div class="user-types-grid">
                <div class="user-type-card">
                    <div class="user-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Students</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Discover exciting events</li>
                        <li><i class="fas fa-check"></i> Easy registration process</li>
                        <li><i class="fas fa-check"></i> Get event notifications</li>
                        <li><i class="fas fa-check"></i> Track your event history</li>
                    </ul>
                </div>
                <div class="user-type-card highlighted">
                    <div class="user-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Event Organizers</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Organize and manage events</li>
                        <li><i class="fas fa-check"></i> Reach target audience</li>
                        <li><i class="fas fa-check"></i> Track event analytics</li>
                        <li><i class="fas fa-check"></i> Send event notifications</li>
                    </ul>
                </div>
                <div class="user-type-card">
                    <div class="user-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Sponsors</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Sponsor university events</li>
                        <li><i class="fas fa-check"></i> Connect with students</li>
                        <li><i class="fas fa-check"></i> Gain brand exposure</li>
                        <li><i class="fas fa-check"></i> Track sponsorship ROI</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Browse by Category Section -->
    <section class="categories">
        <div class="container">
            <h2>Browse by Category</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Academic</h3>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-theater-masks"></i>
                    </div>
                    <h3>Cultural</h3>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <h3>Sports</h3>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Technical</h3>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Social</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3 class="stat-number"><?= number_format((int)($stats['total_events'] ?? 0)) ?></h3>
                    <p>Total Events</p>
                </div>
                <div class="stat-item">
                    <h3 class="stat-number"><?= number_format((int)($stats['total_users'] ?? 0)) ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Transform Your University Event Experience?</h2>
            <p>Join thousands of students, organizers, and sponsors who are already using UniPulse to discover and create amazing university events.</p>
            <div class="cta-buttons">
                <a href="/unipulse/public/usersignup" class="btn btn-cta">
                    <i class="fas fa-user-graduate"></i>
                    <div>
                        <span class="btn-title">Users</span>
                        <span class="btn-subtitle">Discover Events</span>
                    </div>
                </a>
                <a href="/unipulse/public/publisherreg" class="btn btn-cta">
                    <i class="fas fa-users-cog"></i>
                    <div>
                        <span class="btn-title">Event Organizers</span>
                        <span class="btn-subtitle">Create Events</span>
                    </div>
                </a>
                <a href="/unipulse/public/sponsorreg" class="btn btn-cta">
                    <i class="fas fa-building"></i>
                    <div>
                        <span class="btn-title">Sponsors</span>
                        <span class="btn-subtitle">Partner with Us</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Pass PHP data to JavaScript -->
    <script>
        // Convert PHP boosted events data to JavaScript
        const boostedEventsFromDB = <?php echo json_encode($boosted_events ?? []); ?>;
    </script>
    <script src="/unipulse/public/assets/js/home-app.js"></script>
</body>

</html>