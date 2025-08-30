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
                <!-- Empty banner section for spacing and design -->
            </div>
        </header>
        </header>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="personal">
                <i class="fas fa-user"></i> Personal Information
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
                        <h3>Basic Information</h3>
                    </div>
                    <form id="personal-form" class="form">
                        <!-- First Row: Full Name -->
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" value="Vinuja">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" value="Wakishta">
                        </div>
                        
                        <!-- Second Row: University and Faculty -->
                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" value="University of Example">
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" id="faculty" value="Faculty of Engineering">
                        </div>
                        
                        <!-- Third Row: Date of Birth and Gender -->
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" value="1995-06-15">
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="gender-buttons">
                                <button type="button" class="gender-btn active" data-gender="male">Male</button>
                                <button type="button" class="gender-btn" data-gender="female">Female</button>
                            </div>
                            <input type="hidden" id="gender" value="male">
                        </div>
                        
                        <!-- Fourth Row: Email and Phone -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="vinuja@unipulse.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" value="+1 (555) 123-4567">
                        </div>
                        
                        <!-- Fifth Row: Current Town/City and Home Town -->
                        <div class="form-group">
                            <label for="currentCity">Current Town/City</label>
                            <input type="text" id="currentCity" value="San Francisco, CA">
                        </div>
                        <div class="form-group">
                            <label for="homeTown">Home Town</label>
                            <input type="text" id="homeTown" value="Los Angeles, CA">
                        </div>
                        
                        <!-- Sixth Row: Role -->
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
                            <label for="headline">Headline</label>
                            <textarea id="headline" rows="1">Uni Student</textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="4">Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.</textarea>
                        </div>
                        
                        <!-- Save Changes Button -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="savePersonalInfo()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-primary" onclick="cancelPersonalInfo()">
                                Cancel
                            </button>
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
                            <p><i class="fas fa-info-circle"></i> You can create gallery entries with up to 5 photos each. Each gallery entry should include a title and description.</p>
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
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(1)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Hackathon Victory</h4>
                                    <p class="gallery-description">Celebrating 2nd place win at Berkeley Hackathon 2024</p>
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
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(2)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Research Presentation</h4>
                                    <p class="gallery-description">Presenting climate prediction research at symposium</p>
                                </div>
                            </div>

                            <div class="gallery-item editable" data-gallery-id="3">
                                <div class="gallery-images-container">
                                    <div class="gallery-image-carousel">
                                        <div class="carousel-image active">
                                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop" alt="Gallery Photo 3-1">
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
                                    </div>
                                    <div class="gallery-actions-overlay">
                                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(3)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="gallery-content">
                                    <h4 class="gallery-title">Team Collaboration</h4>
                                    <p class="gallery-description">Working with fellow students on group projects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card">
                    <div class="card-header">
                        <h3>Connect With Me</h3>
                    </div>
                    <form id="social-form" class="form">
                        <div class="form-group">
                            <label for="personal-website">
                                <i class="fas fa-globe"></i> Personal Website
                            </label>
                            <input type="url" id="personal-website" value="" placeholder="https://yourwebsite.com">
                        </div>
                        <div class="form-group">
                            <label for="facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </label>
                            <input type="url" id="facebook" value="https://facebook.com/vinujawakishta" placeholder="https://facebook.com/username">
                        </div>
                        <div class="form-group">
                            <label for="instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </label>
                            <input type="url" id="instagram" value="https://instagram.com/vinujawakishta" placeholder="https://instagram.com/username">
                        </div>
                        <div class="form-group">
                            <label for="telegram">
                                <i class="fab fa-telegram"></i> Telegram
                            </label>
                            <input type="url" id="telegram" value="" placeholder="https://t.me/username">
                        </div>
                        <div class="form-group">
                            <label for="linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </label>
                            <input type="url" id="linkedin" value="https://linkedin.com/in/vinujawakishta" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="form-group">
                            <label for="github">
                                <i class="fab fa-github"></i> GitHub
                            </label>
                            <input type="url" id="github" value="" placeholder="https://github.com/username">
                        </div>
                        <div class="form-group">
                            <label for="x-twitter">
                                <i class="fab fa-x-twitter"></i> X (Twitter)
                            </label>
                            <input type="url" id="x-twitter" value="" placeholder="https://x.com/username">
                        </div>
                        <div class="form-group">
                            <label for="discord">
                                <i class="fab fa-discord"></i> Discord
                            </label>
                            <input type="text" id="discord" value="" placeholder="username#1234">
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
                        <label>Photo Upload (Up to 5 photos)</label>
                        <div class="multi-photo-upload">
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
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-primary" onclick="saveGalleryPhoto()">
                            Save Gallery
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeGalleryModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/UniPulse/public/assets/js/userprofile-app.js"></script>
</body>
</html>