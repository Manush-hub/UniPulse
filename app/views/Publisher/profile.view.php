<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Profile - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/profile-style.css">
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
                    <img id="profileImage" src="<?= isset($profile->logo_url) && $profile->logo_url ? $profile->logo_url : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=150&h=150&fit=crop' ?>" alt="Profile Logo" style="width: 100%; height: 100%; object-fit: cover; display: block;">
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
                    <h1 class="profile-name"><?= isset($publisher->society_name) ? htmlspecialchars($publisher->society_name) : 'Organization Name' ?></h1>
                    <?php if (isset($publisher->email)): ?>
                        <p class="profile-email"><?= htmlspecialchars($publisher->email) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="about">
                <i class="fas fa-info-circle"></i> Organization Information
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
                        <div class="form-group">
                            <label for="orgName">Organization Name</label>
                            <input type="text" id="orgName" placeholder="Enter organization name">
                        </div>
                        <div class="form-group">
                            <label for="orgType">Organization Type</label>
                            <select id="orgType">
                                <option value="">Select organization type</option>
                                <option value="student-org">Student Organization</option>
                                <option value="academic-club">Academic Club</option>
                                <option value="sports-club">Sports Club</option>
                                <option value="cultural-club">Cultural Club</option>
                                <option value="professional-org">Professional Organization</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" placeholder="Enter university name">
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" id="faculty" placeholder="Enter faculty or school name">
                        </div>

                        <div class="form-group">
                            <label for="officialEmail">Official Email</label>
                            <input type="email" id="officialEmail" placeholder="Enter official email address">
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number</label>
                            <input type="tel" id="contactNumber" placeholder="Enter contact number">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" rows="2" placeholder="Enter organization address"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="establishedYear">Established Year</label>
                            <input type="number" id="establishedYear" placeholder="YYYY" min="1900" max="2024">
                        </div>
                        <div class="form-group">
                            <label for="memberCount">Current Members</label>
                            <input type="number" id="memberCount" placeholder="Number of members" min="0">
                        </div>



                        <div class="form-group full-width">
                            <label for="headline">Headline</label>
                            <textarea id="headline" rows="1" placeholder="Enter a brief headline about your organization"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="bio">About Organization</label>
                            <textarea id="bio" rows="4" placeholder="Describe your organization's purpose, activities, and goals"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="mission">Mission Statement</label>
                            <textarea id="mission" rows="3" placeholder="Enter your organization's mission statement"></textarea>
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

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo htmlspecialchars($_SESSION['success']);
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-error">
                            <?php
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div id="interests-section" class="interests-content">
                        <div class="preference-buttons" id="preferenceContainer">
                            <?php
                            // Decode preferences from JSON
                            $selectedPreferences = [];
                            if (!empty($data['profile']->preferences)) {
                                $selectedPreferences = json_decode($data['profile']->preferences, true) ?? [];
                            }

                            // Define all available preferences
                            $allPreferences = [
                                'technology' => 'Technology',
                                'innovation' => 'Innovation',
                                'entrepreneurship' => 'Entrepreneurship',
                                'ai-ml' => 'AI & Machine Learning',
                                'web-dev' => 'Web Development',
                                'networking' => 'Networking',
                                'research' => 'Research'
                            ];

                            foreach ($allPreferences as $key => $label):
                                $activeClass = in_array($key, $selectedPreferences) ? ' active' : '';
                                $activeStyle = in_array($key, $selectedPreferences) ? ' style="background: linear-gradient(135deg, #4A5BCC 0%, #23387f 100%); border-color: #4A5BCC; color: white; box-shadow: 0 4px 15px rgba(74, 91, 204, 0.3);"' : '';
                            ?>
                                <button type="button" class="preference-btn-custom<?php echo $activeClass; ?>" data-preference="<?php echo htmlspecialchars($key); ?>" <?php echo $activeStyle; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </button>
                            <?php endforeach; ?>
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
                            <!-- Galleries will be loaded dynamically here -->
                            <p class="text-center" style="padding: 20px; color: #999;" id="noGalleriesMessage">
                                <i class="fas fa-images" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                No galleries yet. Click "Add Photo" to create your first gallery.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card">
                    <div class="card-header">
                        <h3>Connect With Us</h3>
                    </div>
                    <form id="social-form" class="form">
                        <div class="form-group">
                            <label for="website">
                                <i class="fas fa-globe"></i> Organization Website
                            </label>
                            <input type="url" id="website" placeholder="https://yourorganization.com">
                        </div>
                        <div class="form-group">
                            <label for="facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </label>
                            <input type="url" id="facebook" placeholder="https://facebook.com/orgname">
                        </div>

                        <div class="form-group">
                            <label for="instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </label>
                            <input type="url" id="instagram" placeholder="https://instagram.com/orgname">
                        </div>

                        <div class="form-group">
                            <label for="linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </label>
                            <input type="url" id="linkedin" placeholder="https://linkedin.com/company/orgname">
                        </div>

                        <div class="form-group">
                            <label for="twitter">
                                <i class="fab fa-x-twitter"></i> X (Twitter)
                            </label>
                            <input type="url" id="twitter" placeholder="https://x.com/orgname">
                        </div>

                        <div class="form-group">
                            <label for="youtube">
                                <i class="fab fa-youtube"></i> YouTube
                            </label>
                            <input type="url" id="youtube" placeholder="https://youtube.com/@channelname">
                        </div>

                        <div class="form-group">
                            <label for="discord">
                                <i class="fab fa-discord"></i> Discord Server
                            </label>
                            <input type="url" id="discord" placeholder="https://discord.gg/serverinvite">
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
                        </div>
                    </div>
                </div>

                <!-- Access Control & Security -->
                <div class="card">
                    <div class="card-header">
                        <h3>Access Control & Security</h3>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="adminEmail">
                                <i class="fas fa-user-shield"></i> Primary Admin Email
                            </label>
                            <input type="email" id="adminEmail" value="<?= htmlspecialchars($publisher->email ?? '') ?>" readonly style="background-color: #f5f5f5;">
                            <small style="color: #666; display: block; margin-top: 5px;">This email has full administrative access</small>
                        </div>

                        <div class="form-group">
                            <label for="currentPassword">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <input type="password" id="currentPassword" placeholder="Enter current password">
                            <small style="color: #666; display: block; margin-top: 5px;">Required to change organization settings</small>
                        </div>

                        <div class="form-group">
                            <label for="newPassword">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <input type="password" id="newPassword" placeholder="Enter new password">
                            <small style="color: #666; display: block; margin-top: 5px;">Must be at least 8 characters long</small>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">
                                <i class="fas fa-check-circle"></i> Confirm New Password
                            </label>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password">
                            <small style="color: #666; display: block; margin-top: 5px;">Must match your new password</small>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="changePassword()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelSecurityForm()">
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
                                <h4>Deactivate Organization</h4>
                                <p>The organization account will be marked inactive and can be reactivated by signing in again</p>
                            </div>
                            <button class="btn btn-danger" onclick="deleteOrganization()">Deactivate</button>
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

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        window.publisherProfileConfig = {
            publisherData: <?= $publisherJson ?? '{}' ?>,
            updatePreferencesUrl: '/unipulse/public/publisher/profile/updatePreferences'
        };
    </script>
    <script src="<?php echo ROOT ?>/assets/js/extracted/Publisher_profile.js"></script>
    <script src="/UniPulse/public/assets/js/publisherprofie-app.js"></script>
</body>

</html>