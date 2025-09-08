<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join UniPulse Community - Onboarding</title>
    <link rel="stylesheet" href="/assets/css/onboarding-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="onboarding-container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Welcome to UniPulse</h1>
                <p>Choose your role to get started with the perfect experience tailored for you</p>
            </div>

            <!-- Role Selection Cards -->
            <div class="role-selection" id="roleSelection">
                <div class="role-cards-grid">
                    <!-- Students Card -->
                    <div class="role-card" data-role="student">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Students</h3>
                        <p>Discover amazing events, join activities, and connect with your university community</p>
                        <div class="card-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Browse and register for events</li>
                                <li><i class="fas fa-check"></i> Get personalized recommendations</li>
                                <li><i class="fas fa-check"></i> Track your event history</li>
                                <li><i class="fas fa-check"></i> Connect with peers</li>
                            </ul>
                        </div>
                        <button class="role-btn" onclick="selectRole('student')">
                            <span>Get Started as Student</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Event Organizers Card -->
                    <div class="role-card" data-role="organizer">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Event Organizers</h3>
                        <p>Create, manage and promote events to reach your target audience effectively</p>
                        <div class="card-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Create and manage events</li>
                                <li><i class="fas fa-check"></i> Track registrations and analytics</li>
                                <li><i class="fas fa-check"></i> Send notifications and updates</li>
                                <li><i class="fas fa-check"></i> Manage attendee lists</li>
                            </ul>
                        </div>
                        <button class="role-btn" onclick="selectRole('organizer')">
                            <span>Get Started as Organizer</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Sponsors Card -->
                    <div class="role-card" data-role="sponsor">
                        <div class="card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Sponsors</h3>
                        <p>Partner with universities and events to showcase your brand to students</p>
                        <div class="card-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Sponsor university events</li>
                                <li><i class="fas fa-check"></i> Connect with students and organizers</li>
                                <li><i class="fas fa-check"></i> Track sponsorship ROI</li>
                                <li><i class="fas fa-check"></i> Brand exposure and marketing</li>
                            </ul>
                        </div>
                        <button class="role-btn" onclick="selectRole('sponsor')">
                            <span>Get Started as Sponsor</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
            </div>
            <div class="footer-copyright">
                © 2025 UniPulse. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="/assets/js/onboarding-app.js"></script>
</body>
</html>