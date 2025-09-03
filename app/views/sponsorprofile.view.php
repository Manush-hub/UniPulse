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
                        <!-- <div class="form-group">
                            <label for="tagline">Tagline / Short Description</label>
                            <textarea id="tagline" rows="2" placeholder="1-2 lines summarizing the sponsor">Empowering innovation through strategic partnerships and technology advancement.</textarea>
                        </div> -->
                        <div class="form-group">
                            <label for="industry">Industry / Sector</label>
                            <input type="text" id="industry" value="Technology & Software" placeholder="For companies: Industry/Sector">
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
                        <!-- <div class="form-group">
                            <label for="sponsorWebsite">Website</label>
                            <input type="url" id="sponsorWebsite" value="https://techinnovation.com" placeholder="Official website URL">
                        </div> -->
                        <!-- <div class="form-group">
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
                        </div> -->
                        
                        <!-- Fifth Row: Address -->
                        <div class="form-group full-width">
                            <label for="sponsorAddress">Address (Optional)</label>
                            <textarea id="sponsorAddress" rows="2" placeholder="Business address">123 Innovation Drive, Tech Valley, CA 94043</textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="headline">Headline</label>
                            <textarea id="headline" rows="1">To drive technological advancement and foster innovation through strategic partnerships with educational institutions and emerging talent.</textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="about">About Sponsor</label>
                            <textarea id="about" rows="4">To drive technological advancement and foster innovation through strategic partnerships with educational institutions and emerging talent.</textarea>
                        </div>

                        <!-- Mission/Vision for Companies -->
                        <div class="form-group full-width">
                            <label for="mission">Mission Statement</label>
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

                <!-- Interests & Sponsorship Focus Areas -->
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

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header">
                        <h3>Connect With Us</h3>
                    </div>
                    <form id="contact-form" class="form">
                        <!-- Social Media Links -->
                        <div class="form-group">
                            <label for="website">
                                <i class="fas fa-globe"></i> Organization Website
                            </label>
                            <input type="url" id="website" value="https://techinnovationsociety.berkeley.edu" placeholder="https://yourorganization.com">
                        </div>
                        <div class="form-group">
                            <label for="facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </label>
                            <input type="url" id="facebook" value="https://facebook.com/BerkeleyTechSociety" placeholder="https://facebook.com/orgname">
                        </div>
                        
                        <!-- 2nd Row: Instagram and Telegram -->
                        <div class="form-group">
                            <label for="instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </label>
                            <input type="url" id="instagram" value="https://instagram.com/berkeley_tech_society" placeholder="https://instagram.com/orgname">
                        </div>
                        <div class="form-group">
                            <label for="telegram">
                                <i class="fab fa-telegram"></i> Telegram
                            </label>
                            <input type="url" id="telegram" value="" placeholder="https://t.me/channelname">
                        </div>
                        
                        <!-- 3rd Row: LinkedIn and GitHub -->
                        <div class="form-group">
                            <label for="linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </label>
                            <input type="url" id="linkedin" value="https://linkedin.com/company/berkeley-tech-innovation-society" placeholder="https://linkedin.com/company/orgname">
                        </div>
                        <div class="form-group">
                            <label for="github">
                                <i class="fab fa-github"></i> GitHub
                            </label>
                            <input type="url" id="github" value="" placeholder="https://github.com/orgname">
                        </div>
                        
                        <!-- 4th Row: X and Discord -->
                        <div class="form-group">
                            <label for="twitter">
                                <i class="fab fa-x-twitter"></i> X (Twitter)
                            </label>
                            <input type="url" id="twitter" value="https://twitter.com/BerkeleyTechSoc" placeholder="https://x.com/orgname">
                        </div>
                        <div class="form-group">
                            <label for="discord">
                                <i class="fab fa-discord"></i> Discord Server
                            </label>
                            <input type="url" id="discord" value="https://discord.gg/berkeley-tech" placeholder="https://discord.gg/serverinvite">
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
            </div>

            <!-- Sponsorship Details Tab -->
            <div id="sponsorship" class="tab-content">
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
                        <h3>Latest News & Updates</h3>
                        <button class="btn btn-small" onclick="addPressRelease()">
                            <i class="fas fa-plus"></i> Add News Article
                        </button>
                    </div>
                    <div class="press-releases">
                        <!-- Filter Section -->
                        <!-- <div class="press-section-header">
                            <h4>Latest News & Updates</h4>
                            <div class="press-filter">
                                <span class="filter-tag active" data-filter="all">All</span>
                                <span class="filter-tag" data-filter="press-release">Press Releases</span>
                                <span class="filter-tag" data-filter="news">News Articles</span>
                                <span class="filter-tag" data-filter="awards">Awards</span>
                            </div>
                        </div> -->

                        <div class="press-grid">
                            <!-- Press Release Article -->
                            <div class="press-item" data-type="press-release">
                                <div class="press-image">
                                    <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=400&h=250&fit=crop" alt="Tech Summit Partnership">
                                    <div class="press-type-badge badge-press-release">Press Release</div>
                                </div>
                                <div class="press-content">
                                    <div class="press-meta">
                                        <div class="press-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            August 15, 2024
                                        </div>
                                        <div class="press-source">
                                            <i class="fas fa-building"></i>
                                            Tech Innovation Corp
                                        </div>
                                    </div>
                                    <h3 class="press-title">Tech Innovation Corp Partners with University for Annual Tech Summit</h3>
                                    <p class="press-excerpt">Tech Innovation Corp announced today a major partnership with the University of California to sponsor the largest student technology conference on the West Coast. This collaboration will provide students with unprecedented access to industry experts, cutting-edge technology, and career opportunities in the tech sector.</p>
                                    <div class="press-footer">
                                        <a href="#" class="press-link">
                                            Read Full Article
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <div class="press-actions">
                                            <button class="press-action-btn edit" onclick="editPressRelease(1)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="press-action-btn delete" onclick="deletePressRelease(1)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- News Article -->
                            <div class="press-item" data-type="news">
                                <div class="press-image">
                                    <img src="https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=400&h=200&fit=crop" alt="Best Corporate Partner Award">
                                    <div class="press-type-badge badge-award">Award</div>
                                </div>
                                <div class="press-content">
                                    <div class="press-meta">
                                        <div class="press-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            June 22, 2024
                                        </div>
                                        <div class="press-source">
                                            <i class="fas fa-newspaper"></i>
                                            Tech Business Journal
                                        </div>
                                    </div>
                                    <h4 class="press-title">Local Company Receives "Best Corporate Partner" Award</h4>
                                    <p class="press-excerpt">Tech Innovation Corp has been recognized with the prestigious "Best Corporate Partner" award by the University Tech Alliance for their outstanding contribution to student development programs and educational initiatives.</p>
                                    <div class="press-footer">
                                        <a href="#" class="press-link">
                                            Read Full Article
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <div class="press-actions">
                                            <button class="press-action-btn edit" onclick="editPressRelease(2)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="press-action-btn delete" onclick="deletePressRelease(2)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="press-item" data-type="news">
                                <div class="press-image">
                                    <img src="https://images.unsplash.com/photo-1553484771-371a605b060b?w=400&h=200&fit=crop" alt="Innovation Summit">
                                    <div class="press-type-badge badge-news-article">News Article</div>
                                </div>
                                <div class="press-content">
                                    <div class="press-meta">
                                        <div class="press-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            May 10, 2024
                                        </div>
                                        <div class="press-source">
                                            <i class="fas fa-newspaper"></i>
                                            Innovation Weekly
                                        </div>
                                    </div>
                                    <h4 class="press-title">Leading the Future: Tech Innovation's Impact on Student Entrepreneurship</h4>
                                    <p class="press-excerpt">An in-depth look at how Tech Innovation Corp's mentorship programs and startup incubators are shaping the next generation of entrepreneurs and technology leaders.</p>
                                    <div class="press-footer">
                                        <a href="#" class="press-link">
                                            Read Full Article
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <div class="press-actions">
                                            <button class="press-action-btn edit" onclick="editPressRelease(3)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="press-action-btn delete" onclick="deletePressRelease(3)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="press-item" data-type="interview">
                                <div class="press-image">
                                    <div class="press-image-placeholder">
                                        <i class="fas fa-microphone-alt"></i>
                                    </div>
                                    <div class="press-type-badge badge-interview">Interview</div>
                                </div>
                                <div class="press-content">
                                    <div class="press-meta">
                                        <div class="press-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            April 3, 2024
                                        </div>
                                        <div class="press-source">
                                            <i class="fas fa-podcast"></i>
                                            Business Leadership Podcast
                                        </div>
                                    </div>
                                    <h4 class="press-title">CEO Interview: Building Sustainable Partnerships in Education</h4>
                                    <p class="press-excerpt">Tech Innovation Corp's CEO discusses the company's commitment to educational partnerships and the importance of investing in student development programs.</p>
                                    <div class="press-footer">
                                        <a href="#" class="press-link">
                                            Listen to Interview
                                            <i class="fas fa-play"></i>
                                        </a>
                                        <div class="press-actions">
                                            <button class="press-action-btn edit" onclick="editPressRelease(4)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="press-action-btn delete" onclick="deletePressRelease(4)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State (for companies with no press coverage) -->
                        <!-- 
                        <div class="press-empty-state">
                            <div class="press-empty-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <h4>No Press Coverage Yet</h4>
                            <p>Start building your media presence by adding press releases, news articles, and company announcements.</p>
                            <button class="btn btn-primary" onclick="addPressRelease()">
                                <i class="fas fa-plus"></i> Add Your First Article
                            </button>
                        </div>
                        -->
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
                            <button class="btn btn-danger" onclick="deleteSponsorAccount()">Delete</button>
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
                <h3 id="pressModalTitle">Add Press Release</h3>
                <button class="close-modal" onclick="closePressReleaseModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="pressReleaseForm" class="form">
                    <!-- First Row: Title and Type -->
                    <div class="form-group">
                        <label for="pressTitle">Article Title</label>
                        <input type="text" id="pressTitle" placeholder="Enter article title" maxlength="100" required>
                        <small>Maximum 100 characters</small>
                    </div>
                    <div class="form-group">
                        <label for="pressType">Content Type</label>
                        <select id="pressType" required>
                            <option value="">Select type</option>
                            <option value="press-release">Press Release</option>
                            <option value="news">News Article</option>
                            <option value="interview">Interview</option>
                            <option value="award">Award/Recognition</option>
                            <option value="announcement">Company Announcement</option>
                        </select>
                    </div>

                    <!-- Second Row: Date and Source -->
                    <div class="form-group">
                        <label for="pressDate">Publication Date</label>
                        <input type="date" id="pressDate" required>
                    </div>
                    <div class="form-group">
                        <label for="pressSource">Source/Publisher</label>
                        <input type="text" id="pressSource" placeholder="e.g., Tech Innovation Corp, Business Journal" required>
                    </div>

                    <!-- Third Row: Image Upload -->
                    <div class="form-group full-width">
                        <label for="pressImage">Featured Image (Optional)</label>
                        <div class="press-image-upload">
                            <input type="file" id="pressImage" accept="image/*" style="display: none;" onchange="previewPressImage(event)">
                            <div class="upload-area" onclick="document.getElementById('pressImage').click()">
                                <div class="upload-content" id="pressUploadContent">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload image</p>
                                    <small>PNG, JPG up to 5MB</small>
                                </div>
                                <img id="pressImagePreview" class="image-preview" style="display: none;" alt="Preview">
                            </div>
                        </div>
                    </div>

                    <!-- Fourth Row: Excerpt -->
                    <div class="form-group full-width">
                        <label for="pressExcerpt">Article Summary</label>
                        <textarea id="pressExcerpt" rows="4" placeholder="Write a brief summary of the article (2-3 sentences)" maxlength="300" required></textarea>
                        <small>Maximum 300 characters</small>
                    </div>

                    <!-- Fifth Row: Full Content -->
                    <div class="form-group full-width">
                        <label for="pressContent">Full Content (Optional)</label>
                        <textarea id="pressContent" rows="6" placeholder="Write the full article content or leave empty if linking to external article"></textarea>
                    </div>

                    <!-- Sixth Row: External Link -->
                    <div class="form-group full-width">
                        <label for="pressLink">External Link (Optional)</label>
                        <input type="url" id="pressLink" placeholder="https://example.com/full-article">
                        <small>Link to full article on external website</small>
                    </div>

                    <!-- Seventh Row: Tags -->
                    <div class="form-group full-width">
                        <label for="pressTags">Tags (Optional)</label>
                        <input type="text" id="pressTags" placeholder="e.g., partnership, innovation, award (comma-separated)">
                        <small>Separate multiple tags with commas</small>
                    </div>
                </form>
                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="savePressRelease()">
                        Save Article
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