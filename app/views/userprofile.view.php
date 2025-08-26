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
                    <img src="https://avatars.githubusercontent.com/u/vinujawakishta?v=4" alt="Profile Picture" id="profileImage">
                    <div class="avatar-overlay" onclick="uploadProfileImage()">
                        <i class="fas fa-camera"></i>
                        Change Photo
                    </div>
                </div>
                <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
            </div>
            
            <div class="profile-banner">
                <div class="profile-info profile-info-shifted">
                    <h1 id="profileName">Vinuja Wakishta</h1>
                    <p id="profileBio">Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.</p>
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
                        <!-- First Row: Full Name -->
                        <div class="form-group full-width">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" value="Vinuja Wakishta" readonly>
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
                        
                        <!-- Third Row: Email and Phone -->
                        <div class="form-group">
                            <label for="email">Email
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" checked id="emailPrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Public</small>
                                </span>
                            </label>
                            <input type="email" id="email" value="vinuja@unipulse.com" readonly>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" id="phonePrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Private</small>
                                </span>
                            </label>
                            <input type="tel" id="phone" value="+1 (555) 123-4567" readonly>
                        </div>
                        
                        <!-- Fourth Row: Current Town/City and Home Town -->
                        <div class="form-group">
                            <label for="currentCity">Current Town/City
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" checked id="currentCityPrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Public</small>
                                </span>
                            </label>
                            <input type="text" id="currentCity" value="San Francisco, CA" readonly>
                        </div>
                        <div class="form-group">
                            <label for="homeTown">Home Town
                                <span class="privacy-toggle">
                                    <label class="toggle-small">
                                        <input type="checkbox" id="homeTownPrivacy">
                                        <span class="slider-small"></span>
                                    </label>
                                    <small>Private</small>
                                </span>
                            </label>
                            <input type="text" id="homeTown" value="Los Angeles, CA" readonly>
                        </div>
                        
                        <!-- Fifth Row: Role -->
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
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Login & Recovery</h3>
                        <button class="btn btn-small" onclick="toggleEdit('security-form')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" id="username" value="vinujawakishta" readonly>
                            <small>This is how others will find you on UniPulse</small>
                        </div>
                        <div class="form-group">
                            <label for="currentPassword"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter current password" readonly>
                            <small>Required to change your password</small>
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
                            <button class="btn btn-danger" onclick="deactivateAccount()">Deactivate</button>
                        </div>
                        <div class="danger-item">
                            <div>
                                <h4>Delete Account</h4>
                                <p>Permanently delete your account and all data</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteAccount()">Delete</button>
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