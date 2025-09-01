<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Innovation Society - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisherpublic-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Organization Header -->
        <header class="org-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img id="coverPhoto" src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=cover&w=1200&q=80" alt="Cover Photo">
                    <div class="cover-gradient"></div>
                </div>
                
                <!-- Organization Logo positioned to overlap -->
                <div class="org-logo">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80" alt="Tech Innovation Society Logo" id="orgLogo">
                    <!-- Verification Badge -->
                    <div class="verification-badge" title="Verified Organization">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="org-info-section">
                <!-- Verified Button in top right -->
                <button class="btn btn-verified-topright" disabled>
                    <i class="fas fa-check-circle"></i>
                    Verified Organization
                </button>
                
                <div class="org-main-info">
                    <div class="org-title-container">
                        <h1 id="orgName" class="org-title">Tech Innovation Society</h1>
                        <div class="org-subtitle">
                            <span class="org-tagline">Fostering Innovation • Building Tomorrow</span>
                        </div>
                    </div>
                    
                    <div class="org-meta-professional">
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="meta-content">
                                    <span class="meta-label">university</span>
                                    <span class="meta-value">University of Colombo</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-calendar-plus"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Faculty</span>
                                    <span class="meta-value">University of Colombo School of Computing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="org-description-professional">
                        <p class="description-text" id="orgDescription">
                            Leading student organization dedicated to fostering innovation and technological advancement. 
                            We organize workshops, hackathons, and networking events to bridge the gap between academia and industry.
                        </p>
                    </div>
                </div> 
            </div>
        </header>

        <!-- Main Content - All Sections Visible -->
        <main class="public-content">
            <!-- About Section -->
            <section id="about-section" class="content-section">
                <!-- Organization Details -->
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-building"></i> Organization Information</h3>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <label>Organization Type:</label>
                                <span>Student Organization</span>
                            </div>
                            <div class="info-item">
                                <label>Established Year:</label>
                                <span>2018</span>
                            </div>
                            <div class="info-item">
                                <label>Current Members:</label>
                                <span>245 Active Members</span>
                            </div>
                            <div class="info-item">
                                <label>Address:</label>
                                <span>123 Tech Lane, Berkeley, CA 94720</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mission & Vision -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-bullseye"></i> Mission Statement</h3>
                        </div>
                        <div class="info-content">
                            <p>To create a vibrant community of tech enthusiasts, fostering innovation, collaboration, and professional development through hands-on learning experiences and industry partnerships.</p>
                        </div>
                    </div>
                </div>

                <!-- Focus Areas -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-tags"></i> Focus Areas & Interests</h3>
                    </div>
                    <div class="focus-areas">
                        <span class="focus-tag">Technology</span>
                        <span class="focus-tag">Innovation</span>
                        <span class="focus-tag">Entrepreneurship</span>
                        <span class="focus-tag">AI & Machine Learning</span>
                        <span class="focus-tag">Web Development</span>
                        <span class="focus-tag">Networking</span>
                        <span class="focus-tag">Research</span>
                        <span class="focus-tag">Startups</span>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events-section" class="content-section">
                <div class="section-title">
                    <h2>Events</h2>
                </div>
                
                <!-- Upcoming Events -->
                <div class="events-subsection">
                    <div class="subsection-header">
                        <h3>Upcoming Events</h3>
                        <div class="scroll-controls">
                            <button class="scroll-btn scroll-left" onclick="scrollEvents('upcoming', 'left')">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="scroll-btn scroll-right" onclick="scrollEvents('upcoming', 'right')">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="events-scroll-container" id="upcomingEventsContainer">
                        <div class="event-card upcoming">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop" alt="AI Innovation Summit 2024">
                                <span class="event-badge upcoming">Upcoming</span>
                            </div>
                            <div class="event-info">
                                <h4>AI Innovation Summit 2024</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Friday, December 15, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Campus Center
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    150 attendees
                                </p>
                                <p class="event-description">Annual summit featuring AI innovations and industry partnerships.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                    <button class="btn btn-secondary btn-small">Register</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card upcoming">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop" alt="Startup Pitch Night">
                                <span class="event-badge upcoming">Upcoming</span>
                            </div>
                            <div class="event-info">
                                <h4>Startup Pitch Night</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Wednesday, November 20, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Student Innovation Lab
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    80 attendees
                                </p>
                                <p class="event-description">Monthly pitch competition for student entrepreneurs.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                    <button class="btn btn-secondary btn-small">Register</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card upcoming">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop" alt="Tech Networking Mixer">
                                <span class="event-badge upcoming">Upcoming</span>
                            </div>
                            <div class="event-info">
                                <h4>Tech Networking Mixer</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Friday, October 25, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Engineering Building
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    120 attendees
                                </p>
                                <p class="event-description">Connect with industry professionals and fellow students.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                    <button class="btn btn-secondary btn-small">Register</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Past Events -->
                <div class="events-subsection">
                    <div class="subsection-header">
                        <h3>Past Events</h3>
                        <div class="scroll-controls">
                            <button class="scroll-btn scroll-left" onclick="scrollEvents('past', 'left')">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="scroll-btn scroll-right" onclick="scrollEvents('past', 'right')">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="events-scroll-container" id="pastEventsContainer">
                        <div class="event-card past">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=400&h=250&fit=crop" alt="Hackathon 2024">
                                <span class="event-badge past">Past Event</span>
                            </div>
                            <div class="event-info">
                                <h4>Hackathon 2024</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Thursday, August 15, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Computer Science Building
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    200 attendees
                                </p>
                                <p class="event-description">48-hour hackathon with $10,000 in prizes.</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Results</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card past">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=400&h=250&fit=crop" alt="Web Development Workshop">
                                <span class="event-badge past">Past Event</span>
                            </div>
                            <div class="event-info">
                                <h4>Web Development Workshop</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Saturday, July 20, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Online & Berkeley Lab
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    60 attendees
                                </p>
                                <p class="event-description">Comprehensive workshop covering modern web development.</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Resources</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card past">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=400&h=250&fit=crop" alt="Industry Career Fair">
                                <span class="event-badge past">Past Event</span>
                            </div>
                            <div class="event-info">
                                <h4>Industry Career Fair</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Monday, June 10, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Memorial Stadium
                                </p>
                                <p class="event-attendees">
                                    <i class="fas fa-users"></i> 
                                    300 attendees
                                </p>
                                <p class="event-description">Connect with top tech companies for opportunities.</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Recap</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Members Section -->
            <section id="members-section" class="content-section">
                <div class="section-title">
                    <h2>Our Team</h2>
                </div>
                <!-- Leadership Team -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-crown"></i>Executive Committee</h3>
                    </div>
                    <div class="leadership-showcase">
                        <div class="leader-card">
                            <div class="leader-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=150&h=150&fit=crop&crop=face" alt="Sarah Johnson">
                            </div>
                            <div class="leader-info">
                                <h4>Sarah Johnson</h4>
                                <p class="leader-role">President</p>
                            </div>
                        </div>

                        <div class="leader-card">
                            <div class="leader-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Michael Chen">
                            </div>
                            <div class="leader-info">
                                <h4>Michael Chen</h4>
                                <p class="leader-role">Vice President</p>
                            </div>
                        </div>

                        <div class="leader-card">
                            <div class="leader-avatar">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Emily Rodriguez">
                            </div>
                            <div class="leader-info">
                                <h4>Emily Rodriguez</h4>
                                <p class="leader-role">Secretary</p>
                            </div>
                        </div>

                        <div class="leader-card">
                            <div class="leader-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="David Park">
                            </div>
                            <div class="leader-info">
                                <h4>David Park</h4>
                                <p class="leader-role">Treasurer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery-section" class="content-section">
                <div class="section-title">
                    <h2>Gallery</h2>
                </div>
                <div class="gallery-grid">
                    <!-- Gallery with Carousel Items -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-images"></i> Events Gallery</h3>
                        </div>
                        <div class="gallery-carousel-container">
                            <!-- Gallery Items with Carousel -->
                            <div class="gallery-item" data-gallery-id="1">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop" alt="Hackathon 2024 Winners 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=400&fit=crop" alt="Hackathon 2024 Winners 2" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop" alt="Hackathon 2024 Winners 3" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Hackathon 2024 Winners</h4>
                                        <p>Celebrating our winning teams at the annual hackathon</p>
                                    </div>
                                    <div class="carousel-controls">
                                        <button class="carousel-btn prev" onclick="changeCarouselImage(1, -1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="carousel-btn next" onclick="changeCarouselImage(1, 1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="carousel-indicators">
                                        <span class="indicator active" onclick="setCarouselImage(1, 0)"></span>
                                        <span class="indicator" onclick="setCarouselImage(1, 1)"></span>
                                        <span class="indicator" onclick="setCarouselImage(1, 2)"></span>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4>Hackathon 2024 Winners</h4>
                                    <p>Celebrating our winning teams at the annual hackathon</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="2">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Tech Workshop Session 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=600&h=400&fit=crop" alt="Tech Workshop Session 2" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Tech Workshop Session</h4>
                                        <p>Students learning cutting-edge technologies</p>
                                    </div>
                                    <div class="carousel-controls">
                                        <button class="carousel-btn prev" onclick="changeCarouselImage(2, -1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="carousel-btn next" onclick="changeCarouselImage(2, 1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="carousel-indicators">
                                        <span class="indicator active" onclick="setCarouselImage(2, 0)"></span>
                                        <span class="indicator" onclick="setCarouselImage(2, 1)"></span>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4>Tech Workshop Session</h4>
                                    <p>Students learning cutting-edge technologies</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="3">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Industry Networking Event" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Industry Networking Event</h4>
                                        <p>Connecting with tech industry professionals</p>
                                    </div>
                                    <div class="carousel-controls">
                                        <button class="carousel-btn prev" onclick="changeCarouselImage(3, -1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="carousel-btn next" onclick="changeCarouselImage(3, 1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="carousel-indicators">
                                        <span class="indicator active" onclick="setCarouselImage(3, 0)"></span>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4>Industry Networking Event</h4>
                                    <p>Connecting with tech industry professionals</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="4">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop" alt="Innovation Lab Session 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop" alt="Innovation Lab Session 2" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop" alt="Innovation Lab Session 3" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=400&fit=crop" alt="Innovation Lab Session 4" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Innovation Lab Session</h4>
                                        <p>Working on cutting-edge projects</p>
                                    </div>
                                    <div class="carousel-controls">
                                        <button class="carousel-btn prev" onclick="changeCarouselImage(4, -1)">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="carousel-btn next" onclick="changeCarouselImage(4, 1)">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="carousel-indicators">
                                        <span class="indicator active" onclick="setCarouselImage(4, 0)"></span>
                                        <span class="indicator" onclick="setCarouselImage(4, 1)"></span>
                                        <span class="indicator" onclick="setCarouselImage(4, 2)"></span>
                                        <span class="indicator" onclick="setCarouselImage(4, 3)"></span>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4>Innovation Lab Session</h4>
                                    <p>Working on cutting-edge projects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section id="contact-section" class="content-section">
                <div class="section-title">
                    <h2>Contact</h2>
                </div>
                <div class="contact-grid">
                    <!-- Contact Information -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                        </div>
                        <div class="contact-info">
                            <div class="contact-item">
                                <!-- <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div> -->
                                <div class="contact-details">
                                    <h4>Email Address</h4>
                                    <p>contact@techinnovationsociety.org</p>
                                    <!-- <a href="mailto:contact@techinnovationsociety.org" class="contact-link">Send Email</a> -->
                                </div>
                            </div>
                            <div class="contact-item">
                                <!-- <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div> -->
                                <div class="contact-details">
                                    <h4>Phone Number</h4>
                                    <p>+1 (510) 555-0123</p>
                                    <!-- <a href="tel:+15105550123" class="contact-link">Call Now</a> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                        </div>
                        <div class="social-links">
                            <a href="https://techinnovationsociety.berkeley.edu" target="_blank" class="social-link website">
                                <i class="fas fa-globe"></i>
                                <span>Official Website</span>
                            </a>
                            <a href="https://github.com/berkeley-tech-society" target="_blank" class="social-link github">
                                <i class="fab fa-github"></i>
                                <span>GitHub</span>
                            </a>
                            <a href="https://linkedin.com/company/berkeley-tech-innovation-society" target="_blank" class="social-link linkedin">
                                <i class="fab fa-linkedin"></i>
                                <span>LinkedIn</span>
                            </a>
                            <a href="https://twitter.com/BerkeleyTechSoc" target="_blank" class="social-link twitter">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>
                            <a href="https://instagram.com/berkeley_tech_society" target="_blank" class="social-link instagram">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="mailto:contact@techinnovationsociety.org" class="social-link email">
                                <i class="fas fa-envelope"></i>
                                <span>Email Us</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="/UniPulse/public/assets/js/publisherpublic-app.js"></script>
</body>
</html>
