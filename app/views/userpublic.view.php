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
                        <h1 id="userName" class="user-title">John Smith</h1>
                        <div class="user-subtitle">
                            <span class="user-tagline">Computer Science Student • Innovation Enthusiast</span>
                        </div>
                    </div>
                    
                    <div class="user-meta-professional">
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Institution</span>
                                    <span class="meta-value">University of California, Berkeley</span>
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-book"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Major</span>
                                    <span class="meta-value">Computer Science</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="meta-group">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Academic Year</span>
                                    <span class="meta-value">Senior (4th Year)</span>
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="meta-content">
                                    <span class="meta-label">Location</span>
                                    <span class="meta-value">Berkeley, CA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-description-professional">
                        <p class="description-text" id="userDescription">
                            Passionate computer science student with a focus on artificial intelligence and machine learning. 
                            Actively involved in research projects and student organizations, seeking opportunities to apply technology for social impact.
                        </p>
                        
                        <div class="user-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-trophy"></i>
                                <span>Dean's List Student</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-code"></i>
                                <span>Full-Stack Developer</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-lightbulb"></i>
                                <span>Research Assistant</span>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </header>

        <!-- Main Content - All Sections Visible -->
        <main class="public-content">
            <!-- About Section -->
            <section id="about-section" class="content-section">
                <div class="section-title">
                    <h2>About Me</h2>
                </div>
                <!-- User Details -->
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-user"></i> Personal Information</h3>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <label>Student ID:</label>
                                <span>CS2024-001</span>
                            </div>
                            <div class="info-item">
                                <label>University:</label>
                                <span>University of California, Berkeley</span>
                            </div>
                            <div class="info-item">
                                <label>College/School:</label>
                                <span>College of Engineering</span>
                            </div>
                            <div class="info-item">
                                <label>Major:</label>
                                <span>Computer Science</span>
                            </div>
                            <div class="info-item">
                                <label>Minor:</label>
                                <span>Mathematics</span>
                            </div>
                            <div class="info-item">
                                <label>Expected Graduation:</label>
                                <span>Spring 2025</span>
                            </div>
                            <div class="info-item">
                                <label>GPA:</label>
                                <span>3.85/4.0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Career Goals -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-bullseye"></i> Career Goals</h3>
                        </div>
                        <div class="info-content">
                            <p>Aspiring to become a machine learning engineer working on cutting-edge AI applications that can make a positive impact on society. Particularly interested in healthcare technology and sustainable development solutions.</p>
                        </div>
                    </div>
                </div>

                <!-- Skills & Interests -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-tags"></i> Skills & Interests</h3>
                    </div>
                    <div class="focus-areas">
                        <span class="focus-tag">Python</span>
                        <span class="focus-tag">JavaScript</span>
                        <span class="focus-tag">Machine Learning</span>
                        <span class="focus-tag">React.js</span>
                        <span class="focus-tag">Node.js</span>
                        <span class="focus-tag">Data Science</span>
                        <span class="focus-tag">Research</span>
                        <span class="focus-tag">AI Ethics</span>
                    </div>
                </div>

                <!-- Verification Status -->
                <div class="info-card verification-card">
                    <div class="info-header">
                        <h3><i class="fas fa-shield-alt"></i> Verification Status</h3>
                        <span class="verification-badge-header verified">
                            <i class="fas fa-check-circle"></i> Verified Student
                        </span>
                    </div>
                    <div class="verification-list">
                        <div class="verification-item verified">
                            <i class="fas fa-check-circle"></i>
                            <span>University Enrollment Verified</span>
                        </div>
                        <div class="verification-item verified">
                            <i class="fas fa-check-circle"></i>
                            <span>Student ID Verification</span>
                        </div>
                        <div class="verification-item verified">
                            <i class="fas fa-check-circle"></i>
                            <span>Academic Standing Verified</span>
                        </div>
                        <div class="verification-item verified">
                            <i class="fas fa-check-circle"></i>
                            <span>Contact Information Verified</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events-section" class="content-section">
                <div class="section-title">
                    <h2>Events & Activities</h2>
                </div>
                
                <!-- Attending Events -->
                <div class="events-subsection">
                    <div class="subsection-header">
                        <h3>Attending Events</h3>
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
                                <span class="event-badge attending">Attending</span>
                                <span class="event-badge featured">Featured</span>
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
                                <span class="event-badge attending">Attending</span>
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
                                <span class="event-badge attending">Attending</span>
                                <span class="event-badge featured">Featured</span>
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

                <!-- Past Events -->
                <div class="events-subsection">
                    <div class="subsection-header">
                        <h3>Past Participation</h3>
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
                                <span class="event-badge past">Participated</span>
                                <span class="event-badge winner">Winner</span>
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
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    Tech Innovation Society
                                </p>
                                <p class="event-description">48-hour hackathon - Won 2nd place for AI Healthcare Solution.</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Project</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card past">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop" alt="Research Symposium">
                                <span class="event-badge past">Participated</span>
                                <span class="event-badge presenter">Presenter</span>
                            </div>
                            <div class="event-info">
                                <h4>Undergraduate Research Symposium</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Saturday, July 20, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Research Hall
                                </p>
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    UC Berkeley Research Office
                                </p>
                                <p class="event-description">Presented research on "Machine Learning for Climate Prediction".</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Presentation</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card past">
                            <div class="event-image">
                                <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop" alt="Tech Networking Mixer">
                                <span class="event-badge past">Participated</span>
                            </div>
                            <div class="event-info">
                                <h4>Tech Networking Mixer</h4>
                                <p class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    Monday, June 10, 2024
                                </p>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Berkeley Memorial Stadium
                                </p>
                                <p class="event-organizer">
                                    <i class="fas fa-users"></i> 
                                    Alumni Network
                                </p>
                                <p class="event-description">Connected with alumni and industry professionals.</p>
                                <div class="event-actions">
                                    <button class="btn btn-outline btn-small">View Connections</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Projects Section -->
            <section id="projects-section" class="content-section">
                <div class="section-title">
                    <h2>Projects & Achievements</h2>
                </div>
                
                <!-- Featured Projects -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-code"></i> Featured Projects</h3>
                    </div>
                    <div class="projects-showcase">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=250&fit=crop" alt="Healthcare AI Project">
                            </div>
                            <div class="project-info">
                                <h4>AI Healthcare Diagnosis Tool</h4>
                                <p class="project-tech">Python • TensorFlow • React • Node.js</p>
                                <p class="project-description">Machine learning application for early disease detection using medical imaging. Won 2nd place at Berkeley Hackathon 2024.</p>
                                <div class="project-links">
                                    <a href="#" class="project-link">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                    <a href="#" class="project-link">
                                        <i class="fas fa-external-link-alt"></i> Live Demo
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="project-card">
                            <div class="project-image">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=250&fit=crop" alt="Climate Prediction Model">
                            </div>
                            <div class="project-info">
                                <h4>Climate Change Prediction Model</h4>
                                <p class="project-tech">Python • scikit-learn • Pandas • Matplotlib</p>
                                <p class="project-description">Research project developing ML models to predict climate patterns. Presented at Undergraduate Research Symposium.</p>
                                <div class="project-links">
                                    <a href="#" class="project-link">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                    <a href="#" class="project-link">
                                        <i class="fas fa-file-pdf"></i> Research Paper
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="project-card">
                            <div class="project-image">
                                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop" alt="Student Portal App">
                            </div>
                            <div class="project-info">
                                <h4>UniPulse Student Portal</h4>
                                <p class="project-tech">React • Node.js • MongoDB • Express</p>
                                <p class="project-description">Full-stack web application connecting students with campus events and organizations. Currently serving 500+ active users.</p>
                                <div class="project-links">
                                    <a href="#" class="project-link">
                                        <i class="fab fa-github"></i> GitHub
                                    </a>
                                    <a href="#" class="project-link">
                                        <i class="fas fa-external-link-alt"></i> Live Site
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Achievements -->
                <div class="info-card">
                    <div class="info-header">
                        <h3><i class="fas fa-trophy"></i> Academic Achievements</h3>
                    </div>
                    <div class="achievements-list">
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="achievement-content">
                                <h4>Dean's List</h4>
                                <p>Fall 2023, Spring 2024 - Academic Excellence Recognition</p>
                            </div>
                        </div>
                        
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="achievement-content">
                                <h4>Berkeley Hackathon 2024 - 2nd Place</h4>
                                <p>AI Healthcare Solution - $5,000 prize</p>
                            </div>
                        </div>
                        
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="achievement-content">
                                <h4>Research Excellence Award</h4>
                                <p>Outstanding Undergraduate Research in Computer Science</p>
                            </div>
                        </div>
                        
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="achievement-content">
                                <h4>Academic Scholarship Recipient</h4>
                                <p>Merit-based scholarship for academic performance</p>
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
                <div class="gallery-grid" id="galleryContainer">
                    <!-- Static Gallery Items -->
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop" alt="Hackathon Victory" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Hackathon Victory</h4>
                            <p>Celebrating 2nd place win at Berkeley Hackathon 2024</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Research Presentation" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Research Presentation</h4>
                            <p>Presenting climate prediction research at symposium</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Team Collaboration" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Team Collaboration</h4>
                            <p>Working with fellow students on group projects</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop" alt="Lab Work" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Lab Work</h4>
                            <p>Conducting research in the AI laboratory</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop" alt="Networking Events" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Networking Events</h4>
                            <p>Building professional connections at tech events</p>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop" alt="Study Sessions" loading="lazy">
                        <div class="gallery-overlay">
                            <h4>Study Sessions</h4>
                            <p>Collaborative learning with classmates</p>
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
                    <!-- Personal Contact Information -->
                    <div class="info-card">
                        <div class="info-header">
                            <h3><i class="fas fa-envelope"></i> Get in Touch</h3>
                        </div>
                        <div class="contact-links">
                            <a href="mailto:john.smith@berkeley.edu" class="contact-link email">
                                <i class="fas fa-envelope"></i>
                                <span>john.smith@berkeley.edu</span>
                            </a>
                            <a href="tel:+15104861234" class="contact-link phone">
                                <i class="fas fa-phone"></i>
                                <span>+1 (510) 486-1234</span>
                            </a>
                        </div>
                    </div>

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
