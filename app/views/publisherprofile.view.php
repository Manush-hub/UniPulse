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
                <div class="profile-info profile-info-shifted">
                    <h1 id="profileName">Tech Innovation Society</h1>
                    <div class="club-meta">
                        <span class="club-type"><i class="fas fa-users"></i> Student Organization</span>
                        <span class="member-count"><i class="fas fa-user-friends"></i> 245 Members</span>
                        <span class="establishment-year"><i class="fas fa-calendar-alt"></i> Est. 2018</span>
                    </div>
                    <p id="profileBio">Leading student organization dedicated to fostering innovation and technological advancement. We organize workshops, hackathons, and networking events to bridge the gap between academia and industry.</p>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-primary" onclick="toggleMode()" id="modeToggle">
                        <i class="fas fa-eye" id="modeIcon"></i>
                        <span id="modeText">Public View</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="about">
                <i class="fas fa-info-circle"></i> About
            </button>
            <button class="nav-item" data-tab="events">
                <i class="fas fa-calendar"></i> Events
            </button>
            <button class="nav-item" data-tab="members">
                <i class="fas fa-users"></i> Members
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
                        <h3>Organization Information</h3>
                        <button class="btn btn-small" onclick="toggleEdit('organization-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="organization-form" class="form">
                        <!-- First Row: Organization Name -->
                        <div class="form-group full-width">
                            <label for="orgName">Organization Name</label>
                            <input type="text" id="orgName" value="Tech Innovation Society" readonly>
                        </div>
                        
                        <!-- Second Row: Type and University -->
                        <div class="form-group">
                            <label for="orgType">Organization Type</label>
                            <select id="orgType" disabled>
                                <option value="student-org" selected>Student Organization</option>
                                <option value="academic-club">Academic Club</option>
                                <option value="sports-club">Sports Club</option>
                                <option value="cultural-club">Cultural Club</option>
                                <option value="professional-org">Professional Organization</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" value="University of California, Berkeley" readonly>
                        </div>
                        
                        <!-- Third Row: Faculty and Department -->
                        <div class="form-group">
                            <label for="faculty">Faculty/School</label>
                            <input type="text" id="faculty" value="School of Engineering" readonly>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" value="Computer Science & Engineering" readonly>
                        </div>
                        
                        <!-- Fourth Row: Contact Email and Phone -->
                        <div class="form-group">
                            <label for="contactEmail">Contact Email
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" checked id="emailPrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Public</small>
                                </span>
                            </label>
                            <input type="email" id="contactEmail" value="contact@techinnovationsociety.org" readonly>
                        </div>
                        <div class="form-group">
                            <label for="contactPhone">Contact Phone
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" id="phonePrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Private</small>
                                </span>
                            </label>
                            <input type="tel" id="contactPhone" value="+1 (510) 642-1000" readonly>
                        </div>
                        
                        <!-- Fifth Row: Establishment Year and Member Count -->
                        <div class="form-group">
                            <label for="establishedYear">Established Year</label>
                            <input type="number" id="establishedYear" value="2018" readonly min="1900" max="2024">
                        </div>
                        <div class="form-group">
                            <label for="memberCount">Current Members</label>
                            <input type="number" id="memberCount" value="245" readonly min="0">
                        </div>
                        
                        <!-- Bio and Mission -->
                        <div class="form-group full-width">
                            <label for="bio">About Organization</label>
                            <textarea id="bio" rows="4" readonly>Leading student organization dedicated to fostering innovation and technological advancement. We organize workshops, hackathons, and networking events to bridge the gap between academia and industry.</textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="mission">Mission Statement</label>
                            <textarea id="mission" rows="3" readonly>To create a vibrant community of tech enthusiasts, fostering innovation, collaboration, and professional development through hands-on learning experiences and industry partnerships.</textarea>
                        </div>
                        
                        <div class="form-actions" style="display: none;">
                            <button type="button" class="btn btn-primary" onclick="saveOrganizationInfo()">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit('organization-form')">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Focus Areas -->
                <div class="card">
                    <div class="card-header">
                        <h3>Focus Areas & Interests</h3>
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

                <!-- Verification Status -->
                <div class="card verification-card">
                    <div class="card-header">
                        <h3>Verification Status</h3>
                        <span class="verification-status verified">
                            <i class="fas fa-check-circle"></i> Verified
                        </span>
                    </div>
                    <div class="verification-content">
                        <div class="verification-item">
                            <div class="verification-info">
                                <h4><i class="fas fa-university"></i> University Affiliation</h4>
                                <p>Verified by University of California, Berkeley</p>
                                <small>Verified on March 15, 2024</small>
                            </div>
                            <span class="verification-check verified">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="verification-item">
                            <div class="verification-info">
                                <h4><i class="fas fa-id-card"></i> Student Organization Registration</h4>
                                <p>Registered with Student Affairs Office</p>
                                <small>Registration ID: SO-2024-TIS-001</small>
                            </div>
                            <span class="verification-check verified">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="verification-item">
                            <div class="verification-info">
                                <h4><i class="fas fa-envelope"></i> Contact Information</h4>
                                <p>Email and contact details verified</p>
                                <small>Last verified on August 20, 2024</small>
                            </div>
                            <span class="verification-check verified">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="verification-item">
                            <div class="verification-info">
                                <h4><i class="fas fa-users"></i> Leadership Verification</h4>
                                <p>Organization leadership verified</p>
                                <small>President: Sarah Johnson (ID: 123456789)</small>
                            </div>
                            <span class="verification-check verified">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card">
                    <div class="card-header">
                        <h3>Social Media & Links</h3>
                        <button class="btn btn-small" onclick="toggleEdit('social-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="social-form" class="form">
                        <div class="form-group">
                            <label for="website"><i class="fas fa-globe"></i> Official Website</label>
                            <input type="url" id="website" value="https://techinnovationsociety.berkeley.edu" readonly>
                        </div>
                        <div class="form-group">
                            <label for="instagram"><i class="fab fa-instagram"></i> Instagram</label>
                            <input type="url" id="instagram" value="https://instagram.com/berkeley_tech_society" readonly>
                        </div>
                        <div class="form-group">
                            <label for="facebook"><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="url" id="facebook" value="https://facebook.com/BerkeleyTechSociety" readonly>
                        </div>
                        <div class="form-group">
                            <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn</label>
                            <input type="url" id="linkedin" value="https://linkedin.com/company/berkeley-tech-innovation-society" readonly>
                        </div>
                        <div class="form-group">
                            <label for="twitter"><i class="fab fa-twitter"></i> Twitter</label>
                            <input type="url" id="twitter" value="https://twitter.com/BerkeleyTechSoc" readonly>
                        </div>
                        <div class="form-group">
                            <label for="discord"><i class="fab fa-discord"></i> Discord Server</label>
                            <input type="url" id="discord" value="https://discord.gg/berkeley-tech" readonly>
                        </div>
                        <div class="form-actions" style="display: none;">
                            <button type="button" class="btn btn-primary" onclick="saveSocialLinks()">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit('social-form')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Events Tab -->
            <div id="events" class="tab-content">
                <!-- Events Header with Filter -->
                <div class="events-header">
                    <h3>Organization Events</h3>
                    <div class="events-stats">
                        <div class="stat-item">
                            <span class="stat-number" id="totalEvents">47</span>
                            <span class="stat-label">Total Events</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="upcomingEvents">8</span>
                            <span class="stat-label">Upcoming</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="pastEvents">39</span>
                            <span class="stat-label">Past Events</span>
                        </div>
                    </div>
                </div>

                <!-- Events Filter -->
                <div class="events-filter">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Events</button>
                        <button class="filter-btn" data-filter="upcoming">Upcoming</button>
                        <button class="filter-btn" data-filter="past">Past Events</button>
                        <button class="filter-btn" data-filter="organized">Organized by Us</button>
                        <button class="filter-btn" data-filter="featured">Featured</button>
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

                <!-- Load More Button -->
                <div class="load-more-container" style="text-align: center; margin-top: 30px;">
                    <button class="btn btn-secondary" id="loadMoreEvents">
                        <i class="fas fa-plus"></i> Load More Events
                    </button>
                </div>
            </div>

            <!-- Members Tab -->
            <div id="members" class="tab-content">
                <!-- Members Header -->
                <div class="members-header">
                    <h3>Organization Members</h3>
                    <div class="members-stats">
                        <div class="stat-item">
                            <span class="stat-number">245</span>
                            <span class="stat-label">Total Members</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">12</span>
                            <span class="stat-label">Leadership</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">233</span>
                            <span class="stat-label">Active Members</span>
                        </div>
                    </div>
                </div>

                <!-- Leadership Team -->
                <div class="card">
                    <div class="card-header">
                        <h3>Leadership Team</h3>
                        <button class="btn btn-small" onclick="manageLeadership()">
                            <i class="fas fa-user-cog"></i> Manage
                        </button>
                    </div>
                    <div class="leadership-grid">
                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=150&h=150&fit=crop&crop=face" alt="Sarah Johnson">
                                <div class="member-status online"></div>
                            </div>
                            <div class="member-info">
                                <h4>Sarah Johnson</h4>
                                <p class="member-role">President</p>
                                <p class="member-details">Computer Science, Senior</p>
                                <div class="member-contact">
                                    <a href="mailto:sarah.j@berkeley.edu"><i class="fas fa-envelope"></i></a>
                                    <a href="https://linkedin.com/in/sarahjohnson"><i class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Michael Chen">
                                <div class="member-status online"></div>
                            </div>
                            <div class="member-info">
                                <h4>Michael Chen</h4>
                                <p class="member-role">Vice President</p>
                                <p class="member-details">Electrical Engineering, Junior</p>
                                <div class="member-contact">
                                    <a href="mailto:michael.c@berkeley.edu"><i class="fas fa-envelope"></i></a>
                                    <a href="https://linkedin.com/in/michaelchen"><i class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Emily Rodriguez">
                                <div class="member-status away"></div>
                            </div>
                            <div class="member-info">
                                <h4>Emily Rodriguez</h4>
                                <p class="member-role">Secretary</p>
                                <p class="member-details">Information Systems, Junior</p>
                                <div class="member-contact">
                                    <a href="mailto:emily.r@berkeley.edu"><i class="fas fa-envelope"></i></a>
                                    <a href="https://linkedin.com/in/emilyrodriguez"><i class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="member-card leadership">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="David Park">
                                <div class="member-status online"></div>
                            </div>
                            <div class="member-info">
                                <h4>David Park</h4>
                                <p class="member-role">Treasurer</p>
                                <p class="member-details">Business Administration, Senior</p>
                                <div class="member-contact">
                                    <a href="mailto:david.p@berkeley.edu"><i class="fas fa-envelope"></i></a>
                                    <a href="https://linkedin.com/in/davidpark"><i class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Member Directory -->
                <div class="card">
                    <div class="card-header">
                        <h3>Member Directory</h3>
                        <div class="members-controls">
                            <div class="search-box">
                                <input type="text" placeholder="Search members..." id="memberSearch">
                                <i class="fas fa-search"></i>
                            </div>
                            <select id="memberFilter" class="filter-select">
                                <option value="all">All Members</option>
                                <option value="leadership">Leadership</option>
                                <option value="active">Active Members</option>
                                <option value="new">New Members</option>
                            </select>
                        </div>
                    </div>
                    <div class="members-grid" id="membersContainer">
                        <!-- Members will be loaded dynamically -->
                        <div class="member-card">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&h=150&fit=crop&crop=face" alt="Alexandra Smith">
                                <div class="member-status online"></div>
                            </div>
                            <div class="member-info">
                                <h4>Alexandra Smith</h4>
                                <p class="member-role">Member</p>
                                <p class="member-details">Computer Science, Sophomore</p>
                                <p class="member-joined">Joined: March 2024</p>
                            </div>
                        </div>

                        <div class="member-card">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=150&h=150&fit=crop&crop=face" alt="James Wilson">
                                <div class="member-status away"></div>
                            </div>
                            <div class="member-info">
                                <h4>James Wilson</h4>
                                <p class="member-role">Event Coordinator</p>
                                <p class="member-details">Mechanical Engineering, Junior</p>
                                <p class="member-joined">Joined: September 2023</p>
                            </div>
                        </div>

                        <div class="member-card">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150&h=150&fit=crop&crop=face" alt="Maria Garcia">
                                <div class="member-status online"></div>
                            </div>
                            <div class="member-info">
                                <h4>Maria Garcia</h4>
                                <p class="member-role">Marketing Lead</p>
                                <p class="member-details">Marketing, Senior</p>
                                <p class="member-joined">Joined: January 2023</p>
                            </div>
                        </div>

                        <div class="member-card">
                            <div class="member-avatar">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face" alt="Robert Kim">
                                <div class="member-status offline"></div>
                            </div>
                            <div class="member-info">
                                <h4>Robert Kim</h4>
                                <p class="member-role">Tech Lead</p>
                                <p class="member-details">Software Engineering, Senior</p>
                                <p class="member-joined">Joined: August 2022</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Load More Members -->
                    <div class="load-more-container">
                        <button class="btn btn-secondary" id="loadMoreMembers">
                            <i class="fas fa-users"></i> Load More Members
                        </button>
                    </div>
                </div>

                <!-- Membership Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h3>Membership Analytics</h3>
                    </div>
                    <div class="membership-analytics">
                        <div class="analytics-item">
                            <div class="analytics-chart">
                                <canvas id="membershipChart" width="100" height="100"></canvas>
                            </div>
                            <div class="analytics-info">
                                <h4>Growth Over Time</h4>
                                <p>+23% increase in membership this semester</p>
                                <small>245 total members as of August 2024</small>
                            </div>
                        </div>
                        
                        <div class="analytics-breakdown">
                            <div class="breakdown-item">
                                <span class="breakdown-label">Computer Science</span>
                                <div class="breakdown-bar">
                                    <div class="breakdown-fill" style="width: 45%"></div>
                                </div>
                                <span class="breakdown-percentage">45%</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Engineering</span>
                                <div class="breakdown-bar">
                                    <div class="breakdown-fill" style="width: 30%"></div>
                                </div>
                                <span class="breakdown-percentage">30%</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Business</span>
                                <div class="breakdown-bar">
                                    <div class="breakdown-fill" style="width: 15%"></div>
                                </div>
                                <span class="breakdown-percentage">15%</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-label">Other</span>
                                <div class="breakdown-bar">
                                    <div class="breakdown-fill" style="width: 10%"></div>
                                </div>
                                <span class="breakdown-percentage">10%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">
                <!-- Organization Privacy Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Organization Privacy Settings</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Public Organization Profile</h4>
                                <p>Allow anyone to view your organization profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="publicProfile">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Event History</h4>
                                <p>Display past events on public profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="showEventHistory">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Member Count</h4>
                                <p>Display current member count publicly</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="showMemberCount">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Member Directory</h4>
                                <p>Allow members to view other members</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="showMemberDirectory">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Leadership Team</h4>
                                <p>Display leadership information publicly</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="showLeadership">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Allow Event Registration</h4>
                                <p>Let non-members register for your events</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="allowEventRegistration">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Contact Information</h4>
                                <p>Display contact email and phone publicly</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="showContactInfo">
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
                        <button class="btn btn-small" onclick="toggleEdit('security-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="adminEmail"><i class="fas fa-user-shield"></i> Primary Admin Email</label>
                            <input type="email" id="adminEmail" value="admin@techinnovationsociety.org" readonly>
                            <small>This email has full administrative access</small>
                        </div>
                        <div class="form-group">
                            <label for="currentPassword"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter current password" readonly>
                            <small>Required to change organization settings</small>
                        </div>
                        <div class="form-group">
                            <label for="newPassword"><i class="fas fa-key"></i> New Password</label>
                            <input type="password" id="newPassword" placeholder="Enter new password" readonly>
                            <small>Must be at least 8 characters long</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword"><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password" readonly>
                            <small>Must match your new password</small>
                        </div>
                        <div class="form-actions" style="display: none;">
                            <button type="button" class="btn btn-primary" onclick="saveSecuritySettings()">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit('security-form')">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Admin Management -->
                <div class="card">
                    <div class="card-header">
                        <h3>Administrator Management</h3>
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
                            <div class="admin-role">
                                <span class="role-badge primary">Primary Admin</span>
                            </div>
                            <div class="admin-actions">
                                <span class="admin-status active">Active</span>
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
                            <div class="admin-role">
                                <span class="role-badge secondary">Co-Admin</span>
                            </div>
                            <div class="admin-actions">
                                <button class="btn btn-small btn-danger" onclick="removeAdmin('michael')">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Export -->
                <div class="card">
                    <div class="card-header">
                        <h3>Data Management</h3>
                    </div>
                    <div class="data-management">
                        <div class="data-item">
                            <div class="data-info">
                                <h4><i class="fas fa-download"></i> Export Organization Data</h4>
                                <p>Download all organization data including members, events, and analytics</p>
                            </div>
                            <button class="btn btn-secondary" onclick="exportData()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="data-item">
                            <div class="data-info">
                                <h4><i class="fas fa-users"></i> Export Member List</h4>
                                <p>Download current member directory with contact information</p>
                            </div>
                            <button class="btn btn-secondary" onclick="exportMembers()">
                                <i class="fas fa-users"></i> Export
                            </button>
                        </div>
                        <div class="data-item">
                            <div class="data-info">
                                <h4><i class="fas fa-calendar"></i> Export Event History</h4>
                                <p>Download complete event history and attendance records</p>
                            </div>
                            <button class="btn btn-secondary" onclick="exportEvents()">
                                <i class="fas fa-calendar"></i> Export
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

        <!-- Public View Overlay -->
        <div id="publicViewOverlay" class="public-overlay">
            <div class="public-indicator">
                <i class="fas fa-eye"></i>
                <span>Public View Mode</span>
                <button onclick="toggleMode()" class="close-public">×</button>
            </div>
        </div>
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

    <script src="/UniPulse/public/assets/js/publisherprofie-app.js"></script>
</body>
</html>