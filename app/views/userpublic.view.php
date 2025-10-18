<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Smith - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/userpublic-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- User Header -->
        <header class="user-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img id="coverPhoto" src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=cover&w=1200&q=80" alt="Cover Photo">
                    <div class="cover-gradient"></div>
                </div>
                
                <!-- User Profile Picture positioned to overlap -->
                <div class="user-avatar">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=cover&w=400&q=80" alt="John Smith Profile Picture" id="userAvatar">
                    <!-- Verification Badge -->
                    <div class="verification-badge" title="Verified User">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="user-info-section">
                <div class="user-main-info">
                    <div class="user-title-container">
                        <h1 id="userName" class="user-title">Vinuja Wakishta</h1>
                        <div class="user-subtitle">
                            <span class="user-tagline">Computer Science Student</span>
                        </div>
                    </div>
                    
                    <div class="user-meta-professional">
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="meta-content">
                                    <span class="meta-label">University</span>
                                    <span class="meta-value">University of Colombo</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Faculty</span>
                                    <span class="meta-value">University of Colombo School of Computing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-description-professional">
                        <p class="description-text" id="userDescription">
                            Passionate computer science student with a focus on artificial intelligence and machine learning. 
                            Actively involved in research projects and student organizations, seeking opportunities to apply technology for social impact.
                        </p>
                    </div>
                </div> 
            </div>
        </header>

        <!-- Main Content - All Sections Visible -->
        <main class="public-content">
            <!-- About Section -->
            <section id="about-section" class="content-section">
                <!-- Skills & Interests -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-tags"></i>Event Preferences</h3>
                    </div>
                    <div class="focus-areas">
                        <span class="focus-tag">Cultural</span>
                        <span class="focus-tag">Social</span>
                        <span class="focus-tag">Academic</span>
                        <span class="focus-tag">Technical</span>
                        <span class="focus-tag">Sports</span>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events-section" class="content-section">
                <div class="section-title">
                    <h2>Events</h2>
                </div>
                
                <!-- Registered Events -->
                <div class="events-subsection">
                    <div class="subsection-header">
                        <h3>Registered Events</h3>
                        <div class="scroll-controls">
                            <button class="scroll-btn scroll-left" onclick="scrollEvents('attending', 'left')">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="scroll-btn scroll-right" onclick="scrollEvents('attending', 'right')">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="events-scroll-container" id="attendingEventsContainer">
                        <div class="event-card attending">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop" alt="AI Innovation Summit 2024">
                                <span class="event-badge attending">Registered</span>
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
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    Tech Innovation Society
                                </p>
                                <p class="event-description">Annual summit featuring AI innovations and industry partnerships.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card attending">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=400&h=250&fit=crop" alt="Web Development Workshop">
                                <span class="event-badge attending">Registered</span>
                            </div>
                            <div class="event-info">
                                <h4>Web Development Workshop</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Wednesday, November 20, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Student Innovation Lab
                                </p>
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    CS Student Association
                                </p>
                                <p class="event-description">Comprehensive workshop covering modern web development.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card attending">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=400&h=250&fit=crop" alt="Industry Career Fair">
                                <span class="event-badge attending">Registered</span>
                            </div>
                            <div class="event-info">
                                <h4>Industry Career Fair</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Friday, October 25, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Engineering Building
                                </p>
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    Career Services
                                </p>
                                <p class="event-description">Connect with top tech companies for internship and job opportunities.</p>
                                <div class="event-actions">
                                    <button class="btn btn-primary btn-small">View Details</button>
                                </div>
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
                            <h3><i class="fas fa-images"></i> My Gallery</h3>
                        </div>
                        <div class="gallery-carousel-container">
                            <!-- Gallery Items with Carousel -->
                            <div class="gallery-item" data-gallery-id="1">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop" alt="Hackathon Victory 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Hackathon Victory 2" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Hackathon Victory 3" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Hackathon Victory</h4>
                                        <p>Celebrating 2nd place win at Berkeley Hackathon 2024</p>
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
                                    <h4>Hackathon Victory</h4>
                                    <p>Celebrating 2nd place win at Berkeley Hackathon 2024</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="2">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Research Presentation 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop" alt="Research Presentation 2" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Research Presentation</h4>
                                        <p>Presenting climate prediction research at symposium</p>
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
                                    <h4>Research Presentation</h4>
                                    <p>Presenting climate prediction research at symposium</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="3">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Team Collaboration" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Team Collaboration</h4>
                                        <p>Working with fellow students on group projects</p>
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
                                    <h4>Team Collaboration</h4>
                                    <p>Working with fellow students on group projects</p>
                                </div>
                            </div>
                            
                            <div class="gallery-item" data-gallery-id="4">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop" alt="Lab Work 1" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop" alt="Lab Work 2" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop" alt="Lab Work 3" loading="lazy">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop" alt="Lab Work 4" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="gallery-overlay">
                                        <h4>Lab Work</h4>
                                        <p>Conducting research in the AI laboratory</p>
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
                                    <h4>Lab Work</h4>
                                    <p>Conducting research in the AI laboratory</p>
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
                    <!-- Social Media Links -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-share-alt"></i> Connect With Me</h3>
                        </div>
                        <div class="social-links">
                            <a href="https://johnsmith.dev" target="_blank" class="social-link website">
                                <i class="fas fa-globe"></i>
                                <span>Personal Website</span>
                            </a>
                            <a href="https://github.com/johnsmith" target="_blank" class="social-link github">
                                <i class="fab fa-github"></i>
                                <span>GitHub</span>
                            </a>
                            <a href="https://linkedin.com/in/johnsmith-berkeley" target="_blank" class="social-link linkedin">
                                <i class="fab fa-linkedin"></i>
                                <span>LinkedIn</span>
                            </a>
                            <a href="https://twitter.com/johnsmith_dev" target="_blank" class="social-link twitter">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>
                            <a href="https://instagram.com/johnsmith.codes" target="_blank" class="social-link instagram">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="mailto:john.smith@berkeley.edu" class="social-link email">
                                <i class="fas fa-envelope"></i>
                                <span>Email Me</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="/UniPulse/public/assets/js/userpublic-app.js"></script>
</body>
</html>
