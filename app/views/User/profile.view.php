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
        <header class="profile-header">
            <div class="cover-photo-section">
                <!-- Cover Photo -->
                <div class="cover-photo" style="background-color: #f0f0f0; min-height: 300px; position: relative; overflow: hidden;">
                    <img id="coverPhoto" src="" alt="Cover Photo" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div class="cover-overlay" onclick="uploadCover()" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; background-color: rgba(0,0,0,0.3); cursor: pointer;">
                        <i class="fas fa-camera"></i>
                        Change Cover Photo
                    </div>
                </div>
                <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCoverImage(event)">
                <!-- Profile Photo -->
                <div class="profile-photo" style="width: 150px; height: 150px; border-radius: 50%; position: absolute; bottom: -75px; left: 30px; background-color: white; border: 4px solid white; overflow: hidden;">
                    <img id="profilePhoto" src="" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div class="profile-overlay" onclick="uploadProfileImage()" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; background-color: rgba(0,0,0,0.3); cursor: pointer;">
                        <i class="fas fa-camera" style="color: white;"></i>
                        <span style="color: white; font-size: 11px; text-align: center;">Change Photo</span>
                    </div>
                </div>
                <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
            </div>

            <!-- Profile Banner -->
            <div class="profile-banner">
            </div>
        </header>

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
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" placeholder="Enter your first name">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" placeholder="Enter your last name">
                        </div>

                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" id="university" placeholder="Enter your university name" disabled readonly>
                            <small class="form-text-muted">This field is auto-filled from your registration and cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" id="faculty" placeholder="Enter your faculty" disabled readonly>
                            <small class="form-text-muted">This field is auto-filled from your registration and cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="student_staff_id">Student/Staff ID</label>
                            <input type="text" id="student_staff_id" placeholder="Enter your student/staff id" disabled readonly>
                            <small class="form-text-muted">This field is auto-filled from your registration and cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="academic_year">Academic Year</label>
                            <input type="text" id="academic_year" placeholder="Enter your academic year">
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="gender-buttons">
                                <button type="button" class="gender-btn" data-gender="male">Male</button>
                                <button type="button" class="gender-btn" data-gender="female">Female</button>
                            </div>
                            <input type="hidden" id="gender">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" placeholder="Enter your email address" disabled readonly>
                            <small class="form-text-muted">This field is auto-filled from your registration and cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" placeholder="Enter your phone number">
                        </div>

                        <div class="form-group">
                            <label for="nic">NIC</label>
                            <input type="text" id="nic" placeholder="Enter your NIC" disabled readonly>
                            <small class="form-text-muted">This field is auto-filled from your registration and cannot be changed</small>
                        </div>

                        <div class="form-group full-width">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="4" placeholder="Tell us about yourself, your interests, and what you're passionate about"></textarea>
                        </div>

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
                        <h3>Privacy Settings</h3>
                    </div>
                    <div class="preferences-section">
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Public Profile</h4>
                                <p>Allow others to view your profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="publicProfile">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Show Event History</h4>
                                <p>Display your attended events on public profile</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="showEventHistory">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Email Notifications</h4>
                                <p>Receive updates about events and activities</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="emailNotifications">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Event Reminders</h4>
                                <p>Get reminded before upcoming events</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="eventReminders">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>New Event Suggestions</h4>
                                <p>Receive personalized event recommendations</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="eventSuggestions">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="preference-item">
                            <div class="preference-info">
                                <h4>Marketing Communications</h4>
                                <p>Updates about UniPulse features and news</p>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" id="marketingCommunications">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

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



    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        window.profileApi = {
            get: '/unipulse/public/user/profile/getProfile',
            update: '/unipulse/public/user/profile/updateProfile'
        };
    </script>
    <script src="/UniPulse/public/assets/js/userprofile-app.js"></script>
</body>

</html>