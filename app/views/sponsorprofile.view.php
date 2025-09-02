<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Dashboard - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/sponsorprofile-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Profile Header -->
        <header class="profile-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img id="coverPhoto" src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=cover&w=1200&q=80" alt="Cover Photo">
                    <div class="cover-overlay" onclick="uploadCover()">
                        <i class="fas fa-camera"></i>
                        Change Cover Photo
                    </div>
                </div>
                
                <!-- Profile Avatar positioned to overlap -->
                <div class="profile-avatar profile-avatar-overlap">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80" alt="Sponsor Logo" id="profileImage">
                    <div class="avatar-overlay" onclick="uploadProfileImage()">
                        <i class="fas fa-camera"></i>
                        Change Logo
                    </div>
                    <!-- Verification Badge -->
                    <div class="verification-badge" title="Verified Sponsor">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
                <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCover(event)">
            </div>
            
            <div class="profile-banner">
                <!-- Empty banner section for spacing and design -->
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="about">
                <i class="fas fa-info-circle"></i> Sponsor Information
            </button>
            <button class="nav-item" data-tab="sponsorship">
                <i class="fas fa-handshake"></i> Sponsorship Details
            </button>
            <button class="nav-item" data-tab="portfolio">
                <i class="fas fa-trophy"></i> Portfolio & Media
            </button>
            <button class="nav-item" data-tab="testimonials">
                <i class="fas fa-star"></i> Testimonials
            </button>
            <button class="nav-item" data-tab="settings">
                <i class="fas fa-cog"></i> Settings
            </button>
        </nav>

        <!-- Main Content -->
        <main class="profile-content">
            <!-- About Tab -->
            <div id="about" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3>Basic Information</h3>
                    </div>
                    <form id="sponsor-form" class="form">
                        <!-- First Row: Sponsor Name and Type -->
                        <div class="form-group">
                            <label for="sponsorName">Sponsor Name</label>
                            <input type="text" id="sponsorName" value="Tech Innovation Corp" placeholder="Company name or individual sponsor name">
                        </div>
                        <div class="form-group">
                            <label for="sponsorType">Sponsor Type</label>
                            <select id="sponsorType">
                                <option value="company" selected>Company</option>
                                <option value="individual">Individual</option>
                                <option value="organization">Organization</option>
                                <option value="foundation">Foundation</option>
                            </select>
                        </div>

                        <!-- Second Row: Tagline and Industry -->
                        <div class="form-group">
                            <label for="tagline">Tagline / Short Description</label>
                            <textarea id="tagline" rows="2" placeholder="1-2 lines summarizing the sponsor">Empowering innovation through strategic partnerships and technology advancement.</textarea>
                        </div>
                        <div class="form-group">
                            <label for="industry">Industry / Sector</label>
                            <input type="text" id="industry" value="Technology & Software" placeholder="For companies: Industry/Sector">
                        </div>

                        <!-- Third Row: Email and Phone -->
                        <div class="form-group">
                            <label for="sponsorEmail">Email</label>
                            <input type="email" id="sponsorEmail" value="partnerships@techinnovation.com" placeholder="Contact email address">
                        </div>
                        <div class="form-group">
                            <label for="sponsorPhone">Phone Number</label>
                            <input type="tel" id="sponsorPhone" value="+1 (555) 123-4567" placeholder="Contact phone number">
                        </div>
                        
                        <!-- Fourth Row: Website and Company Size -->
                        <div class="form-group">
                            <label for="sponsorWebsite">Website</label>
                            <input type="url" id="sponsorWebsite" value="https://techinnovation.com" placeholder="Official website URL">
                        </div>
                        <div class="form-group">
                            <label for="companySize">Company Size</label>
                            <select id="companySize">
                                <option value="">Select size (for companies)</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200" selected>51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="501-1000">501-1000 employees</option>
                                <option value="1000+">1000+ employees</option>
                            </select>
                        </div>
                        
                        <!-- Fifth Row: Address -->
                        <div class="form-group full-width">
                            <label for="sponsorAddress">Address (Optional)</label>
                            <textarea id="sponsorAddress" rows="2" placeholder="Business address">123 Innovation Drive, Tech Valley, CA 94043</textarea>
                        </div>
                        
                        <!-- Mission/Vision for Companies -->
                        <div class="form-group full-width">
                            <label for="mission">Mission Statement (For Companies)</label>
                            <textarea id="mission" rows="3" placeholder="Company mission or vision statement">To drive technological advancement and foster innovation through strategic partnerships with educational institutions and emerging talent.</textarea>
                        </div>
                        
                        <!-- Personal Info for Individuals -->
                        <div class="form-group">
                            <label for="profession">Profession / Occupation (For Individuals)</label>
                            <input type="text" id="profession" placeholder="Professional occupation">
                        </div>
                        <div class="form-group">
                            <label for="achievements">Achievements / Credentials (For Individuals)</label>
                            <input type="text" id="achievements" placeholder="Notable achievements or credentials">
                        </div>
                        
                        <!-- Awards and Recognition -->
                        <div class="form-group full-width">
                            <label for="awards">Awards & Recognition</label>
                            <textarea id="awards" rows="2" placeholder="List any awards, recognitions, or notable achievements">Best Corporate Partner 2024 - University Tech Awards</textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveSponsorInfo()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelSponsorInfo()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header">
                        <h3>Contact Information & Social Media</h3>
                    </div>
                    <form id="contact-form" class="form">
                        <!-- Social Media Links -->
                        <div class="form-group">
                            <label for="linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </label>
                            <input type="url" id="linkedin" value="https://linkedin.com/company/tech-innovation-corp" placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                        <div class="form-group">
                            <label for="instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </label>
                            <input type="url" id="instagram" value="https://instagram.com/tech_innovation_corp" placeholder="https://instagram.com/yourcompany">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter">
                                <i class="fab fa-x-twitter"></i> X (Twitter)
                            </label>
                            <input type="url" id="twitter" value="https://twitter.com/TechInnovCorp" placeholder="https://x.com/yourcompany">
                        </div>
                        <div class="form-group">
                            <label for="facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </label>
                            <input type="url" id="facebook" value="https://facebook.com/TechInnovationCorp" placeholder="https://facebook.com/yourcompany">
                        </div>
                        
                        <div class="form-group">
                            <label for="youtube">
                                <i class="fab fa-youtube"></i> YouTube
                            </label>
                            <input type="url" id="youtube" placeholder="https://youtube.com/@yourcompany">
                        </div>
                        <div class="form-group">
                            <label for="github">
                                <i class="fab fa-github"></i> GitHub
                            </label>
                            <input type="url" id="github" placeholder="https://github.com/yourcompany">
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveContactInfo()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelContactInfo()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Interests & Causes -->
                <div class="card">
                    <div class="card-header">
                        <h3>Interests & Sponsorship Focus Areas</h3>
                    </div>
                    <div id="interests-section" class="interests-content">
                        <div class="preference-buttons" id="preferenceContainer">
                            <button type="button" class="preference-btn active" data-preference="technology">Technology</button>
                            <button type="button" class="preference-btn active" data-preference="education">Education</button>
                            <button type="button" class="preference-btn active" data-preference="innovation">Innovation</button>
                            <button type="button" class="preference-btn" data-preference="sports">Sports</button>
                            <button type="button" class="preference-btn" data-preference="arts">Arts & Culture</button>
                            <button type="button" class="preference-btn active" data-preference="entrepreneurship">Entrepreneurship</button>
                            <button type="button" class="preference-btn" data-preference="healthcare">Healthcare</button>
                            <button type="button" class="preference-btn" data-preference="environment">Environment</button>
                            <button type="button" class="preference-btn" data-preference="community">Community Service</button>
                            <button type="button" class="preference-btn active" data-preference="research">Research & Development</button>
                        </div>
                    </div>
                </div>

                <!-- Sponsorship Statistics & Overview -->
                <div class="card">
                    <div class="card-header">
                        <h3>Your Sponsorship Statistics</h3>
                    </div>
                    <div class="stats-overview">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number">25+</span>
                                <span class="stat-label">Events Sponsored</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">50K+</span>
                                <span class="stat-label">Students Reached</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">3</span>
                                <span class="stat-label">Years Active</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">$125K+</span>
                                <span class="stat-label">Total Invested</span>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <button class="btn btn-primary" onclick="downloadProfile()">
                                <i class="fas fa-download"></i> Export Profile (PDF)
                            </button>
                            <button class="btn btn-secondary" onclick="generateReport()">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sponsorship Details Tab -->
            <div id="sponsorship" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Sponsorship History & Contributions</h3>
                        <button class="btn btn-small" onclick="addSponsorshipRecord()">
                            <i class="fas fa-plus"></i> Add Record
                        </button>
                    </div>
                    <div class="sponsorship-records">
                        <div class="sponsorship-record">
                            <div class="record-header">
                                <h4>Annual Tech Conference 2024</h4>
                                <span class="sponsorship-badge cash">Cash Sponsor</span>
                            </div>
                            <div class="record-details">
                                <div class="detail-item">
                                    <span class="label">Event Date:</span>
                                    <span class="value">March 15-17, 2024</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Organizer:</span>
                                    <span class="value">Berkeley Computer Science Society</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Contribution Type:</span>
                                    <span class="value">Cash Sponsorship + Venue</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Amount:</span>
                                    <span class="value">$15,000</span>
                                </div>
                            </div>
                            <div class="record-actions">
                                <button class="btn btn-small btn-secondary" onclick="editSponsorshipRecord(1)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-small btn-danger" onclick="deleteSponsorshipRecord(1)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div class="sponsorship-record">
                            <div class="record-header">
                                <h4>Startup Pitch Competition 2024</h4>
                                <span class="sponsorship-badge product">Product Sponsor</span>
                            </div>
                            <div class="record-details">
                                <div class="detail-item">
                                    <span class="label">Event Date:</span>
                                    <span class="value">June 8, 2024</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Organizer:</span>
                                    <span class="value">Entrepreneurship Club</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Contribution Type:</span>
                                    <span class="value">Product Prizes + Mentorship</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Value:</span>
                                    <span class="value">$8,500</span>
                                </div>
                            </div>
                            <div class="record-actions">
                                <button class="btn btn-small btn-secondary" onclick="editSponsorshipRecord(2)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-small btn-danger" onclick="deleteSponsorshipRecord(2)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div class="sponsorship-record">
                            <div class="record-header">
                                <h4>Women in Tech Workshop Series</h4>
                                <span class="sponsorship-badge media">Media Sponsor</span>
                            </div>
                            <div class="record-details">
                                <div class="detail-item">
                                    <span class="label">Event Date:</span>
                                    <span class="value">September 2023 - May 2024</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Organizer:</span>
                                    <span class="value">Women Engineers Association</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Contribution Type:</span>
                                    <span class="value">Media Coverage + Platform</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Value:</span>
                                    <span class="value">$5,000</span>
                                </div>
                            </div>
                            <div class="record-actions">
                                <button class="btn btn-small btn-secondary" onclick="editSponsorshipRecord(3)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-small btn-danger" onclick="deleteSponsorshipRecord(3)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="card">
                    <div class="card-header">
                        <h3>Sponsorship Analytics</h3>
                    </div>
                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <div class="analytics-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="analytics-content">
                                <h4>25</h4>
                                <p>Total Events Sponsored</p>
                            </div>
                        </div>
                        <div class="analytics-card">
                            <div class="analytics-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="analytics-content">
                                <h4>$125K</h4>
                                <p>Total Contribution</p>
                            </div>
                        </div>
                        <div class="analytics-card">
                            <div class="analytics-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="analytics-content">
                                <h4>50K+</h4>
                                <p>Students Impacted</p>
                            </div>
                        </div>
                        <div class="analytics-card">
                            <div class="analytics-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="analytics-content">
                                <h4>12</h4>
                                <p>Partner Organizations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Partnership Collaborations -->
                <div class="card">
                    <div class="card-header">
                        <h3>Partnership Collaborations</h3>
                    </div>
                    <div class="partnerships-section">
                        <div class="partnership-item">
                            <div class="partner-logo">
                                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=80&h=80&fit=crop" alt="Partner Logo">
                            </div>
                            <div class="partner-info">
                                <h4>Future Tech Foundation</h4>
                                <p>Joint sponsor for Annual Innovation Summit</p>
                                <span class="partnership-status active">Active Partnership</span>
                            </div>
                        </div>
                        <div class="partnership-item">
                            <div class="partner-logo">
                                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=80&h=80&fit=crop" alt="Partner Logo">
                            </div>
                            <div class="partner-info">
                                <h4>Digital Skills Institute</h4>
                                <p>Collaborative sponsorship for skills development programs</p>
                                <span class="partnership-status active">Active Partnership</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio & Media Tab -->
            <div id="portfolio" class="tab-content"
                <!-- Gallery Section -->
                <div class="card">
                    <div class="card-header">
                        <h3>Photo Gallery</h3>
                        <div class="gallery-actions">
                            <button type="button" class="btn btn-small" onclick="addGalleryPhoto()">
                                <i class="fas fa-plus"></i> Add Photo
                            </button>
                        </div>
                    </div>
                    <div class="gallery-section">
                        <div class="gallery-upload-info">
                            <p><i class="fas fa-info-circle"></i> You can create gallery entries with up to 10 photos each. Each gallery entry should include a title and description.</p>
                        </div>
                        <div class="gallery-grid" id="galleryGrid">
                            <!-- Existing Gallery Items -->
                            <div class="gallery-item editable" data-gallery-id="1">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop" alt="Gallery Photo 1-1">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Gallery Photo 1-2">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Gallery Photo 1-3">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop" alt="Gallery Photo 1-4">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=600&h=400&fit=crop" alt="Gallery Photo 1-5">
                                        </div>
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
                                        <span class="indicator" onclick="setCarouselImage(1, 3)"></span>
                                        <span class="indicator" onclick="setCarouselImage(1, 4)"></span>
                                    </div>
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(1)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Sponsored Events</h4>
                                    <p class="gallery-description">Highlights from events we've proudly sponsored</p>
                                </div>
                            </div>

                            <div class="gallery-item editable" data-gallery-id="2">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Gallery Photo 2-1">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop" alt="Gallery Photo 2-2">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop" alt="Gallery Photo 2-3">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=600&h=400&fit=crop" alt="Gallery Photo 2-4">
                                        </div>
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
                                        <span class="indicator" onclick="setCarouselImage(2, 2)"></span>
                                        <span class="indicator" onclick="setCarouselImage(2, 3)"></span>
                                    </div>
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(2)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Awards & Recognition</h4>
                                    <p class="gallery-description">Certificates and appreciation awards received</p>
                                </div>
                            </div>

                            <div class="gallery-item editable" data-gallery-id="3">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Gallery Photo 3-1">
                                        </div>
                                        <div class="carousel-image">
                                            <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&h=400&fit=crop" alt="Gallery Photo 3-2">
                                        </div>
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
                                        <span class="indicator" onclick="setCarouselImage(3, 1)"></span>
                                    </div>
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(3)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Press Coverage</h4>
                                    <p class="gallery-description">Media mentions and press releases</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Press & Media -->
                <div class="card">
                    <div class="card-header">
                        <h3>Press Releases & Media Coverage</h3>
                        <button class="btn btn-small" onclick="addPressRelease()">
                            <i class="fas fa-plus"></i> Add Press Release
                        </button>
                    </div>
                    <div class="press-releases">
                        <div class="press-item">
                            <div class="press-date">Aug 15, 2024</div>
                            <div class="press-content">
                                <h4>Tech Innovation Corp Partners with University for Annual Tech Summit</h4>
                                <p>Major sponsorship announcement for the largest student technology conference...</p>
                                <a href="#" class="press-link">Read Full Article <i class="fas fa-external-link-alt"></i></a>
                            </div>
                        </div>
                        <div class="press-item">
                            <div class="press-date">Jun 22, 2024</div>
                            <div class="press-content">
                                <h4>Local Company Receives "Best Corporate Partner" Award</h4>
                                <p>Recognition for outstanding contribution to student development programs...</p>
                                <a href="#" class="press-link">Read Full Article <i class="fas fa-external-link-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Downloadable Resources -->
                <div class="card">
                    <div class="card-header">
                        <h3>Downloadable Resources</h3>
                    </div>
                    <div class="resources-section">
                        <div class="resource-item">
                            <div class="resource-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="resource-info">
                                <h4>Company Profile</h4>
                                <p>Comprehensive overview of our company and sponsorship approach</p>
                            </div>
                            <button class="btn btn-small btn-secondary" onclick="downloadResource('profile')">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                        <div class="resource-item">
                            <div class="resource-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="resource-info">
                                <h4>Sponsorship Portfolio</h4>
                                <p>Detailed portfolio of our sponsorship activities and impact</p>
                            </div>
                            <button class="btn btn-small btn-secondary" onclick="downloadResource('portfolio')">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonials Tab -->
            <div id="testimonials" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Testimonials & Feedback</h3>
                        <div class="rating-summary">
                            <div class="overall-rating">
                                <span class="rating-score">4.8</span>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="rating-count">(24 reviews)</span>
                            </div>
                        </div>
                    </div>
                    <div class="testimonials-section">
                        <div class="testimonial-item">
                            <div class="testimonial-header">
                                <div class="organizer-info">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=60&h=60&fit=crop&crop=face" alt="Sarah Johnson" class="organizer-avatar">
                                    <div class="organizer-details">
                                        <h4>Sarah Johnson</h4>
                                        <p>President, Computer Science Society</p>
                                        <span class="event-name">Annual Tech Conference 2024</span>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <p>"Tech Innovation Corp was an exceptional partner for our annual conference. Their support went beyond financial contribution - they provided mentors, technical expertise, and valuable networking opportunities for our students. Highly recommend!"</p>
                            </div>
                            <div class="testimonial-date">August 20, 2024</div>
                        </div>

                        <div class="testimonial-item">
                            <div class="testimonial-header">
                                <div class="organizer-info">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face" alt="Michael Chen" class="organizer-avatar">
                                    <div class="organizer-details">
                                        <h4>Michael Chen</h4>
                                        <p>Director, Entrepreneurship Hub</p>
                                        <span class="event-name">Startup Pitch Competition</span>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <p>"Outstanding partnership! Not only did they provide generous prizes, but their team also served as judges and mentors. The students gained invaluable insights from industry professionals."</p>
                            </div>
                            <div class="testimonial-date">June 15, 2024</div>
                        </div>

                        <div class="testimonial-item">
                            <div class="testimonial-header">
                                <div class="organizer-info">
                                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face" alt="Emily Rodriguez" class="organizer-avatar">
                                    <div class="organizer-details">
                                        <h4>Emily Rodriguez</h4>
                                        <p>Coordinator, Women Engineers Association</p>
                                        <span class="event-name">Women in Tech Workshop Series</span>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <p>"Great collaboration for our workshop series. Their media platform helped us reach a wider audience, and their commitment to supporting women in tech is genuine and impactful."</p>
                            </div>
                            <div class="testimonial-date">March 10, 2024</div>
                        </div>
                    </div>
                </div>

                <!-- Public Interaction Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Public Interaction Settings</h3>
                    </div>
                    <div class="interaction-settings">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Allow Public Comments</h4>
                                <p>Let event organizers leave public comments on your profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Show Rating & Reviews</h4>
                                <p>Display ratings and reviews from event organizers</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Public Contact Form</h4>
                                <p>Allow potential partners to send messages through your profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">
                <!-- Sponsor Verification Status -->
                <div class="card verification-status-card">
                    <div class="card-header">
                        <h3>Sponsor Verification Status</h3>
                    </div>
                    <div class="verification-content">
                        <div class="verification-info">
                            <div class="verification-badge verified">
                                <i class="fas fa-check-circle"></i>
                                <span>Verified Sponsor</span>
                            </div>
                        </div>
                        <div class="verification-details">
                            <div class="verification-item">
                                <span class="label">Verification Date:</span>
                                <span class="value">August 15, 2024</span>
                            </div>
                            <div class="verification-item">
                                <span class="label">Verification Type:</span>
                                <span class="value">Corporate Sponsor</span>
                            </div>
                            <div class="verification-item">
                                <span class="label">Status:</span>
                                <span class="value status-active">Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visibility Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Privacy & Visibility Settings</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Public Profile Visibility</h4>
                                <p>Make your sponsor profile visible to all users</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Contact Information</h4>
                                <p>Display email and phone number publicly</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Sponsorship Analytics</h4>
                                <p>Display contribution amounts and statistics</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Allow Direct Messages</h4>
                                <p>Let event organizers send direct messages</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Notification Settings</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Sponsorship Requests</h4>
                                <p>Get notified when organizations request sponsorship</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Event Updates</h4>
                                <p>Receive updates about events you sponsor</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>New Reviews & Testimonials</h4>
                                <p>Get notified when organizers leave reviews</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Monthly Reports</h4>
                                <p>Receive monthly sponsorship activity reports</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Security Settings</h3>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="primaryEmail"><i class="fas fa-user-shield"></i> Primary Contact Email</label>
                            <input type="email" id="primaryEmail" value="partnerships@techinnovation.com">
                            <small>This email receives all important notifications</small>
                        </div>
                        <div class="form-group">
                            <label for="currentPassword"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter current password">
                            <small>Required to change security settings</small>
                        </div>
                        <div class="form-group">
                            <label for="newPassword"><i class="fas fa-key"></i> New Password</label>
                            <input type="password" id="newPassword" placeholder="Enter new password">
                            <small>Must be at least 8 characters long</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword"><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password">
                            <small>Must match your new password</small>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveSecuritySettings()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelSecuritySettings()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Management -->
                <div class="card">
                    <div class="card-header">
                        <h3>Account Management</h3>
                    </div>
                    <div class="account-actions">
                        <div class="account-action-item">
                            <div class="action-info">
                                <h4>Export Sponsorship Data</h4>
                                <p>Download all your sponsorship records and analytics</p>
                            </div>
                            <button class="btn btn-secondary" onclick="exportSponsorshipData()">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                        </div>
                        <div class="account-action-item">
                            <div class="action-info">
                                <h4>Generate Sponsorship Report</h4>
                                <p>Create a comprehensive report of your sponsorship activities</p>
                            </div>
                            <button class="btn btn-secondary" onclick="generateReport()">
                                <i class="fas fa-file-pdf"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card danger-zone">
                    <div class="card-header">
                        <h3>Danger Zone</h3>
                    </div>
                    <div class="danger-actions">
                        <div class="danger-item">
                            <div>
                                <h4>Deactivate Sponsor Profile</h4>
                                <p>Temporarily disable your sponsor profile</p>
                            </div>
                            <button class="btn btn-danger" onclick="deactivateSponsor()">Deactivate</button>
                        </div>
                        <div class="danger-item">
                            <div>
                                <h4>Delete Sponsor Account</h4>
                                <p>Permanently delete your sponsor account and all data</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteSponsorAccount()">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="imageUploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Profile Picture</h3>
                <button class="close-modal" onclick="closeModal('imageUploadModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to upload or drag and drop</p>
                    <small>PNG, JPG up to 5MB</small>
                </div>
                <input type="file" id="fileInput" accept="image/*" style="display: none;">
            </div>
        </div>
    </div>

    <!-- Sponsorship Record Modal -->
    <div id="sponsorshipRecordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="recordModalTitle">Add Sponsorship Record</h3>
                <button class="close-modal" onclick="closeSponsorshipRecordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="sponsorshipRecordForm" class="form">
                    <div class="form-group">
                        <label for="eventName">Event Name</label>
                        <input type="text" id="eventName" placeholder="Name of the sponsored event" required>
                    </div>
                    <div class="form-group">
                        <label for="eventOrganizer">Event Organizer</label>
                        <input type="text" id="eventOrganizer" placeholder="Organization that organized the event" required>
                    </div>
                    <div class="form-group">
                        <label for="eventDate">Event Date</label>
                        <input type="date" id="eventDate" required>
                    </div>
                    <div class="form-group">
                        <label for="sponsorshipType">Sponsorship Type</label>
                        <select id="sponsorshipType" required>
                            <option value="">Select type</option>
                            <option value="cash">Cash Sponsor</option>
                            <option value="product">Product Sponsor</option>
                            <option value="media">Media Sponsor</option>
                            <option value="venue">Venue Sponsor</option>
                            <option value="mixed">Mixed Sponsorship</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="contributionDetails">Contribution Details</label>
                        <textarea id="contributionDetails" rows="3" placeholder="Describe what was contributed (e.g., Cash + Venue, Product prizes, etc.)" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contributionAmount">Contribution Value ($)</label>
                        <input type="number" id="contributionAmount" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="eventStatus">Event Status</label>
                        <select id="eventStatus" required>
                            <option value="completed">Completed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="saveSponsorshipRecord()">
                        Save Record
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeSponsorshipRecordModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Press Release Modal -->
    <div id="pressReleaseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Press Release</h3>
                <button class="close-modal" onclick="closePressReleaseModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="pressReleaseForm" class="form">
                    <div class="form-group">
                        <label for="pressTitle">Press Release Title</label>
                        <input type="text" id="pressTitle" placeholder="Enter press release title" required>
                    </div>
                    <div class="form-group">
                        <label for="pressDate">Publication Date</label>
                        <input type="date" id="pressDate" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="pressExcerpt">Excerpt/Summary</label>
                        <textarea id="pressExcerpt" rows="3" placeholder="Brief summary or excerpt from the press release" required></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="pressLink">Link to Full Article (Optional)</label>
                        <input type="url" id="pressLink" placeholder="https://example.com/press-release">
                    </div>
                </form>
                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="savePressRelease()">
                        Add Press Release
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closePressReleaseModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Photo Modal -->
    <div id="galleryPhotoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="galleryModalTitle">Add Photo to Gallery</h3>
                <button class="close-modal" onclick="closeGalleryModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="galleryPhotoForm" class="form">
                    <div class="form-group full-width">
                        <label for="galleryTitle">Photo Title</label>
                        <input type="text" id="galleryTitle" placeholder="Enter a title for your photo" maxlength="50" required>
                        <small>Maximum 50 characters</small>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="galleryDescription">Description</label>
                        <textarea id="galleryDescription" rows="3" placeholder="Write a brief description of your photo" maxlength="150" required></textarea>
                        <small>Maximum 150 characters</small>
                    </div>
                    
                    <div class="form-group full-width" id="galleryImageUpload">
                        <label>Photo Upload (Up to 10 photos)</label>
                        <div class="multi-photo-upload">
                            <!-- First Row: Required Photo (Full Width) -->
                            <div class="photo-upload-item">
                                <label for="galleryFile1" class="photo-upload-label">Photo 1 (Required)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile1').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview1" class="gallery-preview" style="display: none;" alt="Preview 1">
                                </div>
                                <input type="file" id="galleryFile1" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 1)" required>
                            </div>
                            
                            <!-- Second Row: Photos 2, 3, 4 -->
                            <div class="photo-upload-item">
                                <label for="galleryFile2" class="photo-upload-label">Photo 2 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile2').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview2" class="gallery-preview" style="display: none;" alt="Preview 2">
                                </div>
                                <input type="file" id="galleryFile2" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 2)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile3" class="photo-upload-label">Photo 3 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile3').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview3" class="gallery-preview" style="display: none;" alt="Preview 3">
                                </div>
                                <input type="file" id="galleryFile3" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 3)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile4" class="photo-upload-label">Photo 4 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile4').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview4" class="gallery-preview" style="display: none;" alt="Preview 4">
                                </div>
                                <input type="file" id="galleryFile4" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 4)">
                            </div>
                            
                            <!-- Third Row: Photos 5, 6, 7 -->
                            <div class="photo-upload-item">
                                <label for="galleryFile5" class="photo-upload-label">Photo 5 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile5').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview5" class="gallery-preview" style="display: none;" alt="Preview 5">
                                </div>
                                <input type="file" id="galleryFile5" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 5)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile6" class="photo-upload-label">Photo 6 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile6').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview6" class="gallery-preview" style="display: none;" alt="Preview 6">
                                </div>
                                <input type="file" id="galleryFile6" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 6)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile7" class="photo-upload-label">Photo 7 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile7').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview7" class="gallery-preview" style="display: none;" alt="Preview 7">
                                </div>
                                <input type="file" id="galleryFile7" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 7)">
                            </div>
                            
                            <!-- Fourth Row: Photos 8, 9, 10 -->
                            <div class="photo-upload-item">
                                <label for="galleryFile8" class="photo-upload-label">Photo 8 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile8').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview8" class="gallery-preview" style="display: none;" alt="Preview 8">
                                </div>
                                <input type="file" id="galleryFile8" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 8)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile9" class="photo-upload-label">Photo 9 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile9').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview9" class="gallery-preview" style="display: none;" alt="Preview 9">
                                </div>
                                <input type="file" id="galleryFile9" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 9)">
                            </div>
                            
                            <div class="photo-upload-item">
                                <label for="galleryFile10" class="photo-upload-label">Photo 10 (Optional)</label>
                                <div class="gallery-upload-area" onclick="document.getElementById('galleryFile10').click()">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>PNG, JPG up to 5MB</small>
                                    </div>
                                    <img id="galleryPreview10" class="gallery-preview" style="display: none;" alt="Preview 10">
                                </div>
                                <input type="file" id="galleryFile10" accept="image/*" style="display: none;" onchange="previewGalleryImage(event, 10)">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="saveGalleryPhoto()">
                        Save Gallery
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeGalleryModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/UniPulse/public/assets/js/sponsorprofile-app.js"></script>
</body>
</html>