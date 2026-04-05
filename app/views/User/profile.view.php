<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/user/profile-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => ''];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img id="coverPhoto" src="https://images.unsplash.com/photo-1557683316-973673baf926?w=1200&h=300&fit=crop" alt="Cover Photo" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    <div class="cover-overlay" onclick="uploadCover()">
                        <i class="fas fa-camera"></i>
                        Change Cover Photo
                    </div>
                </div>
                
                <!-- Profile Avatar positioned to overlap -->
                <div class="profile-avatar profile-avatar-overlap">
                    <img id="profilePhoto" src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?w=150&h=150&fit=crop" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    <div class="avatar-overlay" onclick="uploadProfileImage()">
                        <i class="fas fa-camera"></i>
                        Change Photo
                    </div>
                </div>
                <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
                <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCoverImage(event)">
            </div>
            
            <!-- Profile Info Below Cover -->
            <div class="profile-info-section">
                <div class="profile-name-email">
                    <h1 class="profile-name" id="displayName">User Name</h1>
                    <p class="profile-email" id="displayEmail">user@email.com</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="profile-nav">
            <button class="nav-item active" data-tab="personal">
                <i class="fas fa-user"></i> Personal Information
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
                        <!-- Basic Information -->
                        <div class="form-group full-width">
                            <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" id="full_name" placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" id="email" placeholder="Enter your email address" disabled readonly>
                            <small class="form-text-muted">From registration - cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                            <div class="phone-input-group">
                                <select id="country_code" style="width: 120px; margin-right: 10px;">
                                    <option value="+94">LK +94</option>
                                </select>
                                <input type="tel" id="phone" placeholder="Enter your phone number" style="flex: 1;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="university"><i class="fas fa-university"></i> University</label>
                            <input type="text" id="university" placeholder="Enter your university name" disabled readonly>
                            <small class="form-text-muted">From registration - cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="faculty"><i class="fas fa-graduation-cap"></i> Faculty/Department</label>
                            <input type="text" id="faculty" placeholder="Enter your faculty" disabled readonly>
                            <small class="form-text-muted">From registration - cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="student_staff_id"><i class="fas fa-id-badge"></i> Student/Staff ID</label>
                            <input type="text" id="student_staff_id" placeholder="Enter your student/staff id" disabled readonly>
                            <small class="form-text-muted">From registration - cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="academic_year"><i class="fas fa-book"></i> Academic Year</label>
                            <select id="academic_year">
                                <option value="">Select your academic year</option>
                                <option value="1st-year">1st Year</option>
                                <option value="2nd-year">2nd Year</option>
                                <option value="3rd-year">3rd Year</option>
                                <option value="4th-year">4th Year</option>
                                <option value="5th-year">5th Year</option>
                                <option value="postgraduate">Postgraduate</option>
                                <option value="phd">PhD</option>
                                <option value="staff">Staff Member</option>
                                <option value="faculty">Faculty Member</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nic"><i class="fas fa-id-card"></i> NIC</label>
                            <input type="text" id="nic" placeholder="Enter your NIC" disabled readonly>
                            <small class="form-text-muted">From registration - cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth"><i class="fas fa-calendar"></i> Date of Birth</label>
                            <input type="date" id="date_of_birth">
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" onclick="savePersonalInfo()">
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelPersonalInfo()">
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
                            <button type="button" class="preference-btn" data-preference="cultural">Cultural</button>
                            <button type="button" class="preference-btn" data-preference="social">Social</button>
                            <button type="button" class="preference-btn" data-preference="academic">Academic</button>
                            <button type="button" class="preference-btn" data-preference="technical">Technical</button>
                            <button type="button" class="preference-btn" data-preference="sports">Sports</button>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Login & Recovery</h3>
                    </div>
                    <form id="security-form" class="form">
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" id="username" placeholder="Enter your username">
                            <small>This is how others will find you on UniPulse</small>
                        </div>
                        <div class="form-group">
                            <label for="currentPassword"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter your current password">
                            <small>Required to change your password</small>
                        </div>
                        <div class="form-group">
                            <label for="newPassword"><i class="fas fa-key"></i> New Password</label>
                            <input type="password" id="newPassword" placeholder="Enter your new password">
                            <small>Must be at least 8 characters long</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword"><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" id="confirmPassword" placeholder="Confirm your new password">
                            <small>Must match your new password</small>
                        </div>
                        <div class="form-actions">
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



    <?php include __DIR__ . '/../components/footer.php'; ?>

        <script src="/UniPulse/public/assets/js/userprofile-app.js"></script>
    <script src="/unipulse/public/assets/js/User/profile-app.js?v=<?= time() ?>"></script>
</body>

</html>