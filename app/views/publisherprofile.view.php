<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Profile - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisherprofile-style.css">
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
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80" alt="Club Logo" id="profileImage">
                    <div class="avatar-overlay" onclick="uploadProfileImage()">
                        <i class="fas fa-camera"></i>
                        Change Logo
                    </div>
                    <!-- Verification Badge -->
                    <div class="verification-badge" title="Verified Organization">
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
                <i class="fas fa-info-circle"></i> Organization Information
            </button>
            <button class="nav-item" data-tab="events">
                <i class="fas fa-calendar"></i> Events
            </button>
            <button class="nav-item" data-tab="members">
                <i class="fas fa-users"></i> Executive Committee
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
                    <form id="organization-form" class="form">
                        <!-- First Row: Organization Name -->
                        <div class="form-group">
                            <label for="orgName">Organization Name</label>
                            <input type="text" id="orgName" value="Tech Innovation Society">
                        </div>
                        <div class="form-group">
                            <label for="orgType">Organization Type</label>
                            <select id="orgType">
                                <option value="student-org" selected>Student Organization</option>
                                <option value="academic-club">Academic Club</option>
                                <option value="sports-club">Sports Club</option>
                                <option value="cultural-club">Cultural Club</option>
                                <option value="professional-org">Professional Organization</option>
                            </select>
                        </div>

                        <!-- Second Row: Type and University -->
                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" value="University of California, Berkeley">
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" id="faculty" value="School of Engineering">
                        </div>

                        <!-- Third Row: Faculty and Department -->
                        <div class="form-group">
                            <label for="officialEmail">Official Email</label>
                            <input type="email" id="officialEmail" value="contact@techinnovationsociety.org">
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number</label>
                            <input type="tel" id="contactNumber" value="+1 (510) 642-1000">
                        </div>
                        
                        <!-- Fourth Row: Official Email and Phone -->
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" rows="2">123 Tech Lane, Berkeley, CA 94720</textarea>
                        </div>
                        
                        <!-- Fifth Row: Establishment Year and Member Count -->
                        <div class="form-group">
                            <label for="establishedYear">Established Year</label>
                            <input type="number" id="establishedYear" value="2018" min="1900" max="2024">
                        </div>
                        <div class="form-group">
                            <label for="memberCount">Current Members</label>
                            <input type="number" id="memberCount" value="245" min="0">
                        </div>
                        
                        <!-- Organization Responsive Person Details -->
                        <div class="form-group">
                            <label for="adminisName">Administrator Name</label>
                            <input type="text" id="adminisName" value="Sarah Johnson">
                        </div>
                        <div class="form-group">
                            <label for="adminRole">Administrator Role in Organization</label>
                            <input type="text" id="adminRole" value="President">
                        </div>
                        
                        <!-- Administrator Role and Email -->
                        <div class="form-group">
                            <label for="adminisContact">Administrator Contact Number</label>
                            <input type="tel" id="adminisContact" value="+1 (510) 123-4567">
                        </div>
                        <div class="form-group">
                            <label for="adminEmail">Administrator Email</label>
                            <input type="email" id="adminEmail" value="sarah.johnson@berkeley.edu">
                        </div>
                        
                        <!-- Bio and Mission -->
                         <div class="form-group full-width">
                            <label for="headline">Headline</label>
                            <textarea id="headline" rows="1">Student Organization</textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="bio">About Organization</label>
                            <textarea id="bio" rows="4">Leading student organization dedicated to fostering innovation and technological advancement. We organize workshops, hackathons, and networking events to bridge</textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="mission">Mission Statement</label>
                            <textarea id="mission" rows="3">To create a vibrant community of tech enthusiasts, fostering innovation, collaboration, and professional development through hands-on learning experiences and industry partnerships.</textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveOrganizationInfo()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-primary" onclick="cancelOrganizationInfo()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Focus Areas -->
                <div class="card">
                    <div class="card-header">
                        <h3>Organization Preferences</h3>
                    </div>
                    <div id="interests-section" class="interests-content">
                        <div class="preference-buttons" id="preferenceContainer">
                            <button type="button" class="preference-btn active" data-preference="technology">Technology</button>
                            <button type="button" class="preference-btn active" data-preference="innovation">Innovation</button>
                            <button type="button" class="preference-btn active" data-preference="entrepreneurship">Entrepreneurship</button>
                            <button type="button" class="preference-btn" data-preference="ai-ml">AI & Machine Learning</button>
                            <button type="button" class="preference-btn" data-preference="web-dev">Web Development</button>
                            <button type="button" class="preference-btn active" data-preference="networking">Networking</button>
                            <button type="button" class="preference-btn" data-preference="research">Research</button>
                        </div>
                    </div>
                </div>
            
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
                                    <h4 class="gallery-title">Organization Events</h4>
                                    <p class="gallery-description">Highlights from our recent tech conferences and networking events</p>
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
                                    <h4 class="gallery-title">Team Building</h4>
                                    <p class="gallery-description">Building stronger connections through collaborative activities</p>
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
                                    <h4 class="gallery-title">Community Outreach</h4>
                                    <p class="gallery-description">Making a positive impact in our local community</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card">
                    <div class="card-header">
                        <h3>Connect With Us</h3>
                    </div>
                    <form id="social-form" class="form">
                        <!-- 1st Row: Personal Website and Facebook -->
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
                            <button type="button" class="btn btn-primary" onclick="saveSocialLinks()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-primary" onclick="cancelSocialLinks()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Events Tab -->
            <div id="events" class="tab-content">
                <!-- Simplified Events Filter -->
                <div class="events-filter">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="upcoming">Upcoming Events</button>
                        <button class="filter-btn" data-filter="past">Past Events</button>
                    </div>
                    <div class="search-box">
                        <input type="text" placeholder="Search events..." id="eventSearch">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <!-- Events Grid -->
                <div class="events-grid" id="eventsContainer">
                    <!-- Events will be populated by JavaScript -->
                </div>
            </div>

            <!-- Members Tab -->
            <div id="members" class="tab-content">
                <!-- Executive Committee -->
                <div class="card">
                    <div class="card-header">
                        <h3>Executive Committee</h3>
                        <button class="btn btn-small" onclick="manageExecutiveCommittee()">
                            <i class="fas fa-user-cog"></i> Manage
                        </button>
                    </div>
                    <div class="leadership-grid">
                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=150&h=150&fit=crop&crop=face" alt="Sarah Johnson">
                            </div>
                            <div class="member-info">
                                <h4>Sarah Johnson</h4>
                                <p class="member-role">President</p>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Michael Chen">
                            </div>
                            <div class="member-info">
                                <h4>Michael Chen</h4>
                                <p class="member-role">Vice President</p>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Emily Rodriguez">
                            </div>
                            <div class="member-info">
                                <h4>Emily Rodriguez</h4>
                                <p class="member-role">Secretary</p>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="David Park">
                            </div>
                            <div class="member-info">
                                <h4>David Park</h4>
                                <p class="member-role">Treasurer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">

                <!-- Organization Verification Status -->
                <div class="card verification-status-card">
                    <div class="card-header">
                        <h3>Organization Verification Status</h3>
                    </div>
                    <div class="verification-content">
                        <div class="verification-info">
                            <div class="verification-badge verified">
                                <i class="fas fa-check-circle"></i>
                                <span>Verified Organization</span>
                            </div>
                            <!-- <p class="verification-description">Your organization has been verified and is eligible for premium features.</p> -->
                        </div>
                        <div class="verification-details">
                            <div class="verification-item">
                                <span class="label">Verification Date:</span>
                                <span class="value">August 15, 2024</span>
                            </div>
                            <div class="verification-item">
                                <span class="label">Verification Type:</span>
                                <span class="value">Educational Institution</span>
                            </div>
                            <div class="verification-item">
                                <span class="label">Status:</span>
                                <span class="value status-active">Active</span>
                            </div>
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
                                <h4>New Member Notifications</h4>
                                <p>Get notified when someone joins the organization</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Event Registration Notifications</h4>
                                <p>Get notified about event registrations</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>System Updates</h4>
                                <p>Receive notifications about platform updates</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Weekly Reports</h4>
                                <p>Receive weekly analytics and activity reports</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Access Control -->
                <div class="card">
                    <div class="card-header">
                        <h3>Access Control & Security</h3>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="adminEmail"><i class="fas fa-user-shield"></i> Primary Admin Email</label>
                            <input type="email" id="adminEmail" value="admin@techinnovationsociety.org">
                            <small>This email has full administrative access</small>
                        </div>
                        <div class="form-group">
                            <label for="currentPassword"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter current password">
                            <small>Required to change organization settings</small>
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
                            <button type="button" class="btn btn-primary" onclick="cancelSecuritySettings()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Admin Management -->
                <div class="card">
                    <div class="card-header">
                        <h3>Administrator</h3>
                        <button class="btn btn-small" onclick="addAdmin()">
                            <i class="fas fa-user-plus"></i> Add Admin
                        </button>
                    </div>
                    <div class="admin-list">
                        <div class="admin-item">
                            <div class="admin-info">
                                <div class="admin-avatar">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=60&h=60&fit=crop&crop=face" alt="Sarah Johnson">
                                </div>
                                <div class="admin-details">
                                    <h4>Sarah Johnson</h4>
                                    <p>Primary Administrator</p>
                                    <small>sarah.j@berkeley.edu</small>
                                </div>
                            </div>
                            <div class="admin-actions">
                                <button class="btn btn-small btn-secondary" onclick="addAdmin()">
                                    <i class="fas fa-exchange-alt"></i> Exchange
                                </button>
                            </div>
                        </div>
                        
                        <div class="admin-item">
                            <div class="admin-info">
                                <div class="admin-avatar">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face" alt="Michael Chen">
                                </div>
                                <div class="admin-details">
                                    <h4>Michael Chen</h4>
                                    <p>Co-Administrator</p>
                                    <small>michael.c@berkeley.edu</small>
                                </div>
                            </div>
                            <div class="admin-actions">
                                <button class="btn btn-small btn-danger" onclick="removeAdmin('michael')">
                                    <i class="fas fa-user-minus"></i> Remove
                                </button>
                            </div>
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
                                <h4>Deactivate Organization</h4>
                                <p>Temporarily disable organization profile and events</p>
                            </div>
                            <button class="btn btn-danger" onclick="deactivateOrganization()">Deactivate</button>
                        </div>
                        <div class="danger-item">
                            <div>
                                <h4>Transfer Ownership</h4>
                                <p>Transfer organization ownership to another member</p>
                            </div>
                            <button class="btn btn-danger" onclick="transferOwnership()">Transfer</button>
                        </div>
                        <div class="danger-item">
                            <div>
                                <h4>Delete Organization</h4>
                                <p>Permanently delete organization and all associated data</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteOrganization()">Delete</button>
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

    <!-- Executive Committee Management Modal -->
    <div id="executiveCommitteeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Manage Executive Committee</h3>
                <button class="close-modal" onclick="closeExecutiveCommitteeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="committee-management-grid">
                    <!-- President Section -->
                    <div class="committee-member-section">
                        <h4>President</h4>
                        <div class="member-form">
                            <div class="photo-upload-section">
                                <div class="committee-photo-upload" onclick="document.getElementById('presidentPhoto').click()">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Upload Photo</p>
                                        <small>JPG, PNG up to 5MB</small>
                                    </div>
                                    <img id="presidentPreview" class="photo-preview" style="display: none;" alt="President Photo">
                                </div>
                                <input type="file" id="presidentPhoto" accept="image/*" style="display: none;" onchange="previewCommitteePhoto(event, 'president')">
                            </div>
                            <div class="name-fields">
                                <input type="text" id="presidentFirstName" placeholder="First Name" maxlength="50">
                                <input type="text" id="presidentLastName" placeholder="Last Name" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- Vice President Section -->
                    <div class="committee-member-section">
                        <h4>Vice President</h4>
                        <div class="member-form">
                            <div class="photo-upload-section">
                                <div class="committee-photo-upload" onclick="document.getElementById('vicePresidentPhoto').click()">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Upload Photo</p>
                                        <small>JPG, PNG up to 5MB</small>
                                    </div>
                                    <img id="vicePresidentPreview" class="photo-preview" style="display: none;" alt="Vice President Photo">
                                </div>
                                <input type="file" id="vicePresidentPhoto" accept="image/*" style="display: none;" onchange="previewCommitteePhoto(event, 'vicePresident')">
                            </div>
                            <div class="name-fields">
                                <input type="text" id="vicePresidentFirstName" placeholder="First Name" maxlength="50">
                                <input type="text" id="vicePresidentLastName" placeholder="Last Name" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- Secretary Section -->
                    <div class="committee-member-section">
                        <h4>Secretary</h4>
                        <div class="member-form">
                            <div class="photo-upload-section">
                                <div class="committee-photo-upload" onclick="document.getElementById('secretaryPhoto').click()">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Upload Photo</p>
                                        <small>JPG, PNG up to 5MB</small>
                                    </div>
                                    <img id="secretaryPreview" class="photo-preview" style="display: none;" alt="Secretary Photo">
                                </div>
                                <input type="file" id="secretaryPhoto" accept="image/*" style="display: none;" onchange="previewCommitteePhoto(event, 'secretary')">
                            </div>
                            <div class="name-fields">
                                <input type="text" id="secretaryFirstName" placeholder="First Name" maxlength="50">
                                <input type="text" id="secretaryLastName" placeholder="Last Name" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- Treasurer Section -->
                    <div class="committee-member-section">
                        <h4>Treasurer</h4>
                        <div class="member-form">
                            <div class="photo-upload-section">
                                <div class="committee-photo-upload" onclick="document.getElementById('treasurerPhoto').click()">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-camera"></i>
                                        <p>Upload Photo</p>
                                        <small>JPG, PNG up to 5MB</small>
                                    </div>
                                    <img id="treasurerPreview" class="photo-preview" style="display: none;" alt="Treasurer Photo">
                                </div>
                                <input type="file" id="treasurerPhoto" accept="image/*" style="display: none;" onchange="previewCommitteePhoto(event, 'treasurer')">
                            </div>
                            <div class="name-fields">
                                <input type="text" id="treasurerFirstName" placeholder="First Name" maxlength="50">
                                <input type="text" id="treasurerLastName" placeholder="Last Name" maxlength="50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="saveExecutiveCommittee()">
                        Save Committee
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeExecutiveCommitteeModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div id="addAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Administrator</h3>
                <button class="close-modal" onclick="closeAddAdminModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm" class="form">
                    <!-- 1st Row: Profile Photo (centered) -->
                    <div class="admin-form-row photo-row">
                        <div class="form-group photo-group-centered">
                            <label for="adminPhoto">Profile Photo (Optional)</label>
                            <div class="admin-photo-upload" onclick="document.getElementById('adminPhoto').click()">
                                <div class="upload-placeholder">
                                    <p>Click to upload photo</p>
                                    <small>JPG, PNG up to 5MB</small>
                                </div>
                                <img id="adminPhotoPreview" class="photo-preview" style="display: none;" alt="Admin Photo Preview">
                            </div>
                            <input type="file" id="adminPhoto" accept="image/*" style="display: none;" onchange="previewAdminPhoto(event)">
                        </div>
                    </div>
                    
                    <!-- 2nd Row: Administrator Role -->
                    <div class="admin-form-row">
                        <div class="form-group">
                            <label for="adminRole">Administrator Role</label>
                            <select id="adminRole" required>
                                <option value="">Select role</option>
                                <option value="admin">Administrator</option>
                                <option value="co-admin">Co-Administrator</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- 3rd Row: First and Last Name -->
                    <div class="admin-form-row">
                        <div class="form-group">
                            <label for="adminFirstName">First Name</label>
                            <input type="text" id="adminFirstName" placeholder="Enter first name" maxlength="50" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="adminLastName">Last Name</label>
                            <input type="text" id="adminLastName" placeholder="Enter last name" maxlength="50" required>
                        </div>
                    </div>
                    
                    <!-- 4th Row: Email and Contact Number -->
                    <div class="admin-form-row">
                        <div class="form-group">
                            <label for="adminEmail">Email Address</label>
                            <input type="email" id="adminEmail" placeholder="Enter email address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="adminContact">Contact Number</label>
                            <input type="tel" id="adminContact" placeholder="Enter contact number" required>
                        </div>
                    </div>
                </form>

                <div class="modal-actions">
                    <button type="button" class="btn btn-primary" onclick="saveNewAdmin()">
                        Add
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeAddAdminModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/UniPulse/public/assets/js/publisherprofie-app.js"></script>
</body>
</html>