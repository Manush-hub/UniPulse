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
                    <a href="#features" class="unp-nav-link">Features</a>
                    <a href="#users" class="unp-nav-link">Users</a>
                    <a href="/unipulse/public/signin" class="unp-nav-link">Events</a>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Unforgettable Events Start Here</h1>
            <p>Discover, connect and participate in university events across Sri Lanka through our centralized platform.</p>
            <div class="hero-buttons">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Find Events
                </button>
                <button class="btn btn-secondary">
                    <i class="fas fa-calendar-plus"></i>
                    Publish Event
                </button>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="upcoming-events" id="events">
        <div class="container">
            <h2>Upcoming Events</h2>
            <p class="section-subtitle">Discover the most spectacular events happening at top universities.</p>
            <div class="events-grid">
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=300&h=200&fit=crop" alt="Tech Innovation Summit">
                    </div>
                    <div class="event-info">
                        <h3>Tech Innovation Summit</h3>
                        <p class="event-date"><i class="fas fa-calendar"></i> Dec 15, 2024 - 09:00 AM</p>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> University of Colombo</p>
                        <button class="btn btn-outline">View Details</button>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=300&h=200&fit=crop" alt="Cultural Festival">
                    </div>
                    <div class="event-info">
                        <h3>Cultural Festival</h3>
                        <p class="event-date"><i class="fas fa-calendar"></i> Dec 20, 2024 - 06:00 PM</p>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> University of Peradeniya</p>
                        <button class="btn btn-outline">View Details</button>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=300&h=200&fit=crop" alt="Sports Championship">
                    </div>
                    <div class="event-info">
                        <h3>Sports Championship</h3>
                        <p class="event-date"><i class="fas fa-calendar"></i> Jan 5, 2025 - 08:00 AM</p>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> University of Moratuwa</p>
                        <button class="btn btn-outline">View Details</button>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=300&h=200&fit=crop" alt="Academic Conference">
                    </div>
                    <div class="event-info">
                        <h3>Academic Conference</h3>
                        <p class="event-date"><i class="fas fa-calendar"></i> Jan 10, 2025 - 10:00 AM</p>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> University of Kelaniya</p>
                        <button class="btn btn-outline">View Details</button>
                    </div>
                </div>
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
                    <h3 class="stat-number">2500+</h3>
                    <p>Total Events</p>
                </div>
                <div class="stat-item">
                    <h3 class="stat-number">20+</h3>
                    <p>Universities</p>
                </div>
                <div class="stat-item">
                    <h3 class="stat-number">1000+</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-item">
                    <h3 class="stat-number">500+</h3>
                    <p>Success Stories</p>
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
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">
                       <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
                </div>
                    <p>Powering the future university events across Sri Lanka. Connecting students, organizers and sponsors.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#events">Find Events</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#help">Help Center</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#privacy">Privacy Policy</a></li>
                        <li><a href="#terms">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-envelope"></i> info@unipulse.lk</p>
                    <p><i class="fas fa-phone"></i> +94 11 234 5678</p>
                    <p><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 UniPulse. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/unipulse/public/assets/js/home-app.js"></script>
</body>
</html>