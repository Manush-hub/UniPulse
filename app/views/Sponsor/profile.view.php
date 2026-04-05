<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Profile - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/UniPulse/public/assets/css/sponsor/profile-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once(__DIR__ . '/components/header.php'); ?>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img id="coverPhoto" src="<?= isset($profile->cover_photo_url) && $profile->cover_photo_url ? $profile->cover_photo_url : 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1200&h=300&fit=crop' ?>" alt="Cover Photo" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    <div class="cover-overlay" onclick="uploadCover()">
                        <i class="fas fa-camera"></i>
                        Change Cover Photo
                    </div>
                </div>
                
                <!-- Profile Avatar positioned to overlap -->
                <div class="profile-avatar profile-avatar-overlap">
                    <img id="profileImage" src="<?= isset($profile->logo_url) && $profile->logo_url ? $profile->logo_url : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=150&h=150&fit=crop' ?>" alt="Sponsor Logo" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    <div class="avatar-overlay" onclick="uploadProfileImage()">
                        <i class="fas fa-camera"></i>
                        Change Logo
                    </div>
                </div>
                <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
                <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCover(event)">
            </div>
            
            <!-- Profile Info Below Cover -->
            <div class="profile-info-section">
                <div class="profile-name-email">
                    <h1 class="profile-name"><?= isset($sponsor->company_name) ? htmlspecialchars($sponsor->company_name) : 'Sponsor Name' ?></h1>
                    <?php if(isset($sponsor->email)): ?>
                        <p class="profile-email"><?= htmlspecialchars($sponsor->email) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="about">
                <i class="fas fa-info-circle"></i> Sponsor Information
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
                            <label for="sponsorName">Company Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" id="sponsorName" placeholder="Company or individual sponsor name" required>
                        </div>
                        <div class="form-group">
                            <label for="sponsorType">Company Type <span style="color: #dc3545;">*</span></label>
                            <select id="sponsorType" required>
                                <option value="">Select sponsor type</option>
                                <option value="company">Company</option>
                                <option value="individual">Individual</option>
                                <option value="organization">Organization</option>
                                <option value="foundation">Foundation</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="industry">Industry / Sector <span style="color: #dc3545;">*</span></label>
                            <input type="text" id="industry" placeholder="Industry or sector" required>
                        </div>
                        <div class="form-group">
                            <label for="companySize">Company Size</label>
                            <select id="companySize">
                                <option value="">Select size</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200">51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="501-1000">501-1000 employees</option>
                                <option value="1000+">1000+ employees</option>
                            </select>
                        </div>

                        <!-- Third Row: Email and Phone -->
                        <div class="form-group">
                            <label for="sponsorEmail">Email</label>
                            <input type="email" id="sponsorEmail" placeholder="Contact email" required readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label for="sponsorPhone">Phone Number <span style="color: #dc3545;">*</span></label>
                            <input type="tel" id="sponsorPhone" placeholder="Contact phone" required>
                        </div>
                        
                        
                        <!-- Fifth Row: Address -->
                        <div class="form-group full-width">
                            <label for="sponsorAddress">Address</label>
                            <textarea id="sponsorAddress" rows="2" placeholder="Business address"></textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="headline">Headline</label>
                            <textarea id="headline" rows="1" placeholder="Brief tagline or mission statement"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="sponsorAbout">About <span style="color: #dc3545;">*</span></label>
                            <textarea id="sponsorAbout" rows="4" placeholder="Tell us about your organization" required></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveSponsorInfo()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelSponsorInfo()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Interests & Sponsorship Focus Areas -->
                <div class="card">
                    <div class="card-header">
                        <h3>Sponsorship Focus Areas</h3>
                    </div>
                    <div class="preferences-content">
                        <div class="preference-buttons" id="preferenceContainer">
                            <button type="button" class="preference-btn" data-preference="technology">
                                <i class="fas fa-microchip"></i> Technology
                            </button>
                            <button type="button" class="preference-btn" data-preference="education">
                                <i class="fas fa-graduation-cap"></i> Education
                            </button>
                            <button type="button" class="preference-btn" data-preference="innovation">
                                <i class="fas fa-lightbulb"></i> Innovation
                            </button>
                            <button type="button" class="preference-btn" data-preference="sports">
                                <i class="fas fa-futbol"></i> Sports
                            </button>
                            <button type="button" class="preference-btn" data-preference="arts">
                                <i class="fas fa-palette"></i> Arts & Culture
                            </button>
                            <button type="button" class="preference-btn" data-preference="entrepreneurship">
                                <i class="fas fa-rocket"></i> Entrepreneurship
                            </button>
                            <button type="button" class="preference-btn" data-preference="healthcare">
                                <i class="fas fa-heartbeat"></i> Healthcare
                            </button>
                            <button type="button" class="preference-btn" data-preference="environment">
                                <i class="fas fa-leaf"></i> Environment
                            </button>
                            <button type="button" class="preference-btn" data-preference="community">
                                <i class="fas fa-hands-helping"></i> Community Service
                            </button>
                            <button type="button" class="preference-btn" data-preference="research">
                                <i class="fas fa-flask"></i> Research & Development
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header">
                        <h3>Connect With Us</h3>
                    </div>
                    <form id="contact-form" class="form">
                        <!-- Row 1 -->
                        <div class="form-group">
                            <label for="website">
                                <i class="fas fa-globe"></i> Website
                            </label>
                            <input type="url" id="website" placeholder="https://yourcompany.com">
                        </div>
                        <div class="form-group">
                            <label for="facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </label>
                            <input type="url" id="facebook" placeholder="https://facebook.com/company">
                        </div>
                        
                        <!-- Row 2 -->
                        <div class="form-group">
                            <label for="instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </label>
                            <input type="url" id="instagram" placeholder="https://instagram.com/company">
                        </div>
                        <div class="form-group">
                            <label for="linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </label>
                            <input type="url" id="linkedin" placeholder="https://linkedin.com/company">
                        </div>
                        
                        <!-- Row 3 -->
                        <div class="form-group">
                            <label for="twitter">
                                <i class="fab fa-x-twitter"></i> X (Twitter)
                            </label>
                            <input type="url" id="twitter" placeholder="https://x.com/company">
                        </div>
                        <div class="form-group">
                            <label for="youtube">
                                <i class="fab fa-youtube"></i> YouTube
                            </label>
                            <input type="url" id="youtube" placeholder="https://youtube.com/@company">
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="saveContactInfo()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelContactInfo()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">

                <!-- Access Control & Security -->
                <div class="card">
                    <div class="card-header">
                        <h3>Access Control & Security</h3>
                    </div>
                    <form id="password-form" class="form">
                        <!-- Primary Admin Email -->
                        <div class="form-group full-width">
                            <label for="adminEmail">
                                <i class="fas fa-user-shield"></i> Primary Admin Email
                            </label>
                            <input type="email" id="adminEmail" value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                            <small class="form-text">This email has full administrative access</small>
                        </div>

                        <div class="form-group">
                            <label for="currentPassword">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <input type="password" id="currentPassword" placeholder="Enter current password">
                            <small class="form-text">Required to change organization settings</small>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <input type="password" id="newPassword" placeholder="Enter new password">
                            <small class="form-text">Must be at least 8 characters long</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">
                                <i class="fas fa-check-circle"></i> Confirm New Password
                            </label>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password">
                            <small class="form-text">Must match your new password</small>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="changePassword()">
                                Change Password
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
                            <div class="danger-info">
                                <h4>Delete Account</h4>
                                <p>Permanently delete your sponsor account and all associated data</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteAccount()">
                                Delete
                            </button>
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
                <h3>Upload Image</h3>
                <button class="close-modal" onclick="closeModal('imageUploadModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to upload or drag and drop</p>
                    <small>PNG, JPG up to 5MB</small>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    
        <script src="/UniPulse/public/assets/js/sponsorprofile-app.js"></script>
    <script src="/unipulse/public/assets/js/Sponsor/profile-app.js?v=<?= time() ?>"></script>
</body>
</html>
