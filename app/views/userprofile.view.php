<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/userprofile-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Profile Header -->
        <header class="profile-header">
            <div class="profile-banner">
                <div class="profile-avatar">
                    <img src="https://avatars.githubusercontent.com/u/vinujawakishta?v=4" alt="Profile Picture" id="profileImage">
                    <button class="avatar-edit-btn" onclick="uploadImage()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <div class="profile-info">
                    <h1 id="profileName">Vinuja Wakishta</h1>
                    <p id="profileBio">Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.</p>
                    <div class="profile-stats">
                        <div class="stat">
                            <span class="stat-number" id="eventsAttended">8</span>
                            <span class="stat-label">Events Attended</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number" id="eventsOrganized">3</span>
                            <span class="stat-label">Events Organized</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number" id="connections">42</span>
                            <span class="stat-label">Connections</span>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-secondary" onclick="editProfile()">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                    <button class="btn btn-primary" onclick="toggleMode()" id="modeToggle">
                        <i class="fas fa-eye" id="modeIcon"></i>
                        <span id="modeText">Public View</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="personal">
                <i class="fas fa-user"></i> Personal Info
            </button>
            <button class="nav-item" data-tab="events">
                <i class="fas fa-calendar"></i> Registered Events
            </button>
            <button class="nav-item" data-tab="preferences">
                <i class="fas fa-heart"></i> Preferences
            </button>
            <button class="nav-item" data-tab="settings">
                <i class="fas fa-cog"></i> Settings
            </button>
        </nav>

        <!-- Main Content -->
        <main class="profile-content">
            <!-- Personal Information Tab -->
            <div id="personal" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3>Personal Information</h3>
                        <button class="btn btn-small" onclick="toggleEdit('personal-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="personal-form" class="form">
                        <!-- First Row: First Name and Last Name -->
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" value="Vinuja" readonly>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" value="Wakishta" readonly>
                        </div>
                        
                        <!-- Second Row: University and Faculty -->
                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" value="University of Example" readonly>
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" id="faculty" value="Faculty of Engineering" readonly>
                        </div>
                        
                        <!-- Third Row: Email, Phone, and Role -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="vinuja@unipulse.com" readonly>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" value="+1 (555) 123-4567" readonly>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <div class="role-buttons">
                                <button type="button" class="role-btn active" data-role="student">Student</button>
                                <button type="button" class="role-btn" data-role="staff">Staff</button>
                            </div>
                            <input type="hidden" id="role" value="student">
                        </div>
                        
                        <!-- Bio and Location -->
                        <div class="form-group full-width">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="4" readonly>Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.</textarea>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" value="San Francisco, CA" readonly>
                        </div>
                        <div class="form-actions" style="display: none;">
                            <button type="button" class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit('personal-form')">Cancel</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Event Preferences</h3>
                    </div>
                    <div id="interests-section" class="interests-content">
                        <div class="preference-buttons" id="preferenceContainer">
                            <button type="button" class="preference-btn active" data-preference="cultural">Cultural</button>
                            <button type="button" class="preference-btn" data-preference="social">Social</button>
                            <button type="button" class="preference-btn active" data-preference="academic">Academic</button>
                            <button type="button" class="preference-btn" data-preference="technical">Technical</button>
                            <button type="button" class="preference-btn" data-preference="sports">Sports</button>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card">
                    <div class="card-header">
                        <h3>Social Links</h3>
                        <button class="btn btn-small" onclick="toggleEdit('social-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="social-form" class="form">
                        <div class="form-group">
                            <label for="instagram"><i class="fab fa-instagram"></i> Instagram</label>
                            <input type="url" id="instagram" value="https://instagram.com/vinujawakishta" readonly>
                        </div>
                        <div class="form-group">
                            <label for="facebook"><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="url" id="facebook" value="https://facebook.com/vinujawakishta" readonly>
                        </div>
                        <div class="form-group">
                            <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn</label>
                            <input type="url" id="linkedin" value="https://linkedin.com/in/vinujawakishta" readonly>
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
                <div class="events-grid" id="eventsContainer">
                    <!-- Events will be populated by JavaScript -->
                </div>
            </div>

            <!-- Preferences Tab -->
            <div id="preferences" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Event Preferences</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Preferred Event Types</h4>
                                <p>Select the types of events you're most interested in</p>
                            </div>
                        </div>
                        <div class="event-preferences">
                            <label class="checkbox-item">
                                <input type="checkbox" checked> Tech Conferences
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked> Networking Events
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox"> Workshops
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked> Meetups
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox"> Social Events
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox"> Career Fairs
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Notification Preferences</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Email Notifications</h4>
                                <p>Receive updates about events and activities</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Event Reminders</h4>
                                <p>Get reminded before upcoming events</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>New Event Suggestions</h4>
                                <p>Receive personalized event recommendations</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Marketing Communications</h4>
                                <p>Updates about UniPulse features and news</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Privacy Settings</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Public Profile</h4>
                                <p>Allow others to view your profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="publicProfile">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Event History</h4>
                                <p>Display your attended events on public profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked id="showEventHistory">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Contact Information</h4>
                                <p>Allow others to see your contact details</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="showContact">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Account Settings</h3>
                    </div>
                    <form class="form">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" value="vinujawakishta" required>
                            <small>This is how others will find you on UniPulse</small>
                        </div>
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone">
                                <option value="PST" selected>Pacific Standard Time (PST)</option>
                                <option value="EST">Eastern Standard Time (EST)</option>
                                <option value="CST">Central Standard Time (CST)</option>
                                <option value="MST">Mountain Standard Time (MST)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select id="language">
                                <option value="en" selected>English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>

                <div class="card danger-zone">
                    <div class="card-header">
                        <h3>Danger Zone</h3>
                    </div>
                    <div class="danger-actions">
                        <div class="danger-item">
                            <div>
                                <h4>Deactivate Account</h4>
                                <p>Temporarily disable your account</p>
                            </div>
                            <button class="btn btn-danger-outline" onclick="deactivateAccount()">Deactivate</button>
                        </div>
                        <div class="danger-item">
                            <div>
                                <h4>Delete Account</h4>
                                <p>Permanently delete your account and all data</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteAccount()">Delete Account</button>
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

    <script src="/UniPulse/public/assets/js/userprofile-app.js"></script>
</body>
</html>