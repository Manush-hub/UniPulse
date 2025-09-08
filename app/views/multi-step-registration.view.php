<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - UniPulse</title>
    <link rel="stylesheet" href="/assets/css/multi-step-registration-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <div class="header-breadcrumb">
                <a href="/index.php?url=onboarding/role_selection" class="breadcrumb-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Category Selection
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="registration-container">
            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <span>Choose Role</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step completed">
                    <div class="step-number">2</div>
                    <span>Select Category</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-number">3</div>
                    <span>Registration</span>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="registration-form-container">
                <!-- Step Progress -->
                <div class="step-progress" id="stepProgress">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <span>Personal Info</span>
                    </div>
                    <?php if (isset($user_category) && $user_category === 'university'): ?>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <span>University Details</span>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <span>Verification</span>
                    </div>
                    <?php else: ?>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <span>Account Setup</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Form Header -->
                <div class="form-header">
                    <h1 id="formTitle">Create Your Account</h1>
                    <p id="formSubtitle">Let's get you started with your UniPulse journey</p>
                </div>

                <!-- Multi-Step Form -->
                <form class="multi-step-form" id="registrationForm">
                    <input type="hidden" id="userRole" name="userRole" value="">
                    <input type="hidden" id="userCategory" name="userCategory" value="<?php echo $user_category ?? 'public'; ?>">
                    
                    <!-- Step 1: Personal Information -->
                    <div class="form-step active" id="step1">
                        <div class="step-content">
                            <h3>Personal Information</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name *</label>
                                    <div class="input-container">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                                    </div>
                                    <span class="error-message" id="firstNameError"></span>
                                </div>

                                <div class="form-group">
                                    <label for="lastName">Last Name *</label>
                                    <div class="input-container">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                                    </div>
                                    <span class="error-message" id="lastNameError"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <div class="input-container">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                                </div>
                                <span class="error-message" id="emailError"></span>
                                <div class="field-help">
                                    <?php if (isset($user_category) && $user_category === 'university'): ?>
                                    <i class="fas fa-info-circle"></i>
                                    Use your university email for automatic verification
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <div class="input-container">
                                        <i class="fas fa-phone"></i>
                                        <input type="tel" id="phone" name="phone" placeholder="+94 77 123 4567" required>
                                    </div>
                                    <span class="error-message" id="phoneError"></span>
                                </div>

                                <div class="form-group">
                                    <label for="dateOfBirth">Date of Birth</label>
                                    <div class="input-container">
                                        <i class="fas fa-calendar"></i>
                                        <input type="date" id="dateOfBirth" name="dateOfBirth">
                                    </div>
                                    <span class="error-message" id="dateOfBirthError"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password *</label>
                                <div class="input-container">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                                    <button type="button" class="password-toggle" id="passwordToggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <span class="strength-text">Password strength</span>
                                </div>
                                <span class="error-message" id="passwordError"></span>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password *</label>
                                <div class="input-container">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                                    <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <span class="error-message" id="confirmPasswordError"></span>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-next" onclick="nextStep()">
                                <span>Continue</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (isset($user_category) && $user_category === 'university'): ?>
                    <!-- Step 2: University Details (for university users) -->
                    <div class="form-step" id="step2">
                        <div class="step-content">
                            <h3>University Details</h3>
                            
                            <div class="form-group">
                                <label for="university">University *</label>
                                <div class="input-container">
                                    <i class="fas fa-university"></i>
                                    <select id="university" name="university" required>
                                        <option value="">Select your university</option>
                                        <option value="university-of-colombo">University of Colombo</option>
                                        <option value="university-of-peradeniya">University of Peradeniya</option>
                                        <option value="university-of-moratuwa">University of Moratuwa</option>
                                        <option value="university-of-kelaniya">University of Kelaniya</option>
                                        <option value="university-of-jaffna">University of Jaffna</option>
                                        <option value="university-of-ruhuna">University of Ruhuna</option>
                                        <option value="uva-wellassa-university">Uva Wellassa University</option>
                                        <option value="sabaragamuwa-university">Sabaragamuwa University</option>
                                        <option value="wayamba-university">Wayamba University</option>
                                        <option value="rajarata-university">Rajarata University</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <span class="error-message" id="universityError"></span>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="studentId">Student/Staff ID *</label>
                                    <div class="input-container">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" id="studentId" name="studentId" placeholder="e.g., 2021/CS/001 or EMP/2020/001" required>
                                    </div>
                                    <span class="error-message" id="studentIdError"></span>
                                </div>

                                <div class="form-group">
                                    <label for="faculty">Faculty/Department *</label>
                                    <div class="input-container">
                                        <i class="fas fa-building"></i>
                                        <select id="faculty" name="faculty" required>
                                            <option value="">Select faculty/department</option>
                                            <option value="engineering">Faculty of Engineering</option>
                                            <option value="science">Faculty of Science</option>
                                            <option value="medicine">Faculty of Medicine</option>
                                            <option value="arts">Faculty of Arts</option>
                                            <option value="management">Faculty of Management</option>
                                            <option value="law">Faculty of Law</option>
                                            <option value="education">Faculty of Education</option>
                                            <option value="agriculture">Faculty of Agriculture</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <span class="error-message" id="facultyError"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="academicYear">Academic Year/Position *</label>
                                    <div class="input-container">
                                        <i class="fas fa-calendar-alt"></i>
                                        <select id="academicYear" name="academicYear" required>
                                            <option value="">Select year/position</option>
                                            <option value="1st-year">1st Year</option>
                                            <option value="2nd-year">2nd Year</option>
                                            <option value="3rd-year">3rd Year</option>
                                            <option value="4th-year">4th Year</option>
                                            <option value="5th-year">5th Year</option>
                                            <option value="postgraduate">Postgraduate</option>
                                            <option value="phd">PhD Student</option>
                                            <option value="lecturer">Lecturer</option>
                                            <option value="senior-lecturer">Senior Lecturer</option>
                                            <option value="professor">Professor</option>
                                            <option value="staff">Administrative Staff</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <span class="error-message" id="academicYearError"></span>
                                </div>

                                <div class="form-group">
                                    <label for="degreeProgram">Degree Program/Department</label>
                                    <div class="input-container">
                                        <i class="fas fa-graduation-cap"></i>
                                        <input type="text" id="degreeProgram" name="degreeProgram" placeholder="e.g., Computer Science, Mechanical Engineering">
                                    </div>
                                    <span class="error-message" id="degreeProgramError"></span>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="previousStep()">
                                <i class="fas fa-arrow-left"></i>
                                <span>Previous</span>
                            </button>
                            <button type="button" class="btn-next" onclick="nextStep()">
                                <span>Continue</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Verification (for university users) -->
                    <div class="form-step" id="step3">
                        <div class="step-content">
                            <h3>University Verification</h3>
                            
                            <div class="verification-info">
                                <div class="info-card">
                                    <i class="fas fa-shield-alt"></i>
                                    <h4>Why do we need verification?</h4>
                                    <p>To ensure the integrity of our university community and provide you with exclusive access to university-specific events and benefits.</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="verificationMethod">Verification Method *</label>
                                <div class="verification-options">
                                    <label class="radio-option">
                                        <input type="radio" name="verificationMethod" value="university-email" checked>
                                        <span class="radio-custom"></span>
                                        <div class="option-content">
                                            <strong>University Email Verification</strong>
                                            <p>We'll send a verification link to your university email address</p>
                                        </div>
                                    </label>
                                    
                                    <label class="radio-option">
                                        <input type="radio" name="verificationMethod" value="document-upload">
                                        <span class="radio-custom"></span>
                                        <div class="option-content">
                                            <strong>Student/Staff ID Upload</strong>
                                            <p>Upload a photo of your student/staff ID for manual verification</p>
                                        </div>
                                    </label>
                                </div>
                                <span class="error-message" id="verificationMethodError"></span>
                            </div>

                            <div class="form-group" id="documentUploadSection" style="display: none;">
                                <label for="idDocument">Upload Student/Staff ID *</label>
                                <div class="file-upload-area" id="fileUploadArea">
                                    <input type="file" id="idDocument" name="idDocument" accept="image/*,.pdf" style="display: none;">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload or drag and drop</p>
                                        <span>Accepted formats: JPG, PNG, PDF (Max 5MB)</span>
                                    </div>
                                </div>
                                <span class="error-message" id="idDocumentError"></span>
                            </div>

                            <div class="form-group">
                                <label for="additionalInfo">Additional Information (Optional)</label>
                                <div class="input-container">
                                    <i class="fas fa-comment"></i>
                                    <textarea id="additionalInfo" name="additionalInfo" rows="3" placeholder="Any additional information that might help with verification..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="previousStep()">
                                <i class="fas fa-arrow-left"></i>
                                <span>Previous</span>
                            </button>
                            <button type="submit" class="btn-submit">
                                <span>Create Account</span>
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>

                    <?php else: ?>
                    <!-- Step 2: Account Setup (for public users) -->
                    <div class="form-step" id="step2">
                        <div class="step-content">
                            <h3>Complete Your Profile</h3>
                            
                            <div class="form-group">
                                <label for="interests">Areas of Interest</label>
                                <div class="interest-tags">
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="academic">
                                        <span class="tag">Academic Events</span>
                                    </label>
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="cultural">
                                        <span class="tag">Cultural Events</span>
                                    </label>
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="sports">
                                        <span class="tag">Sports Events</span>
                                    </label>
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="technical">
                                        <span class="tag">Technical Events</span>
                                    </label>
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="social">
                                        <span class="tag">Social Events</span>
                                    </label>
                                    <label class="tag-option">
                                        <input type="checkbox" name="interests[]" value="career">
                                        <span class="tag">Career Events</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="profession">Profession/Occupation</label>
                                    <div class="input-container">
                                        <i class="fas fa-briefcase"></i>
                                        <input type="text" id="profession" name="profession" placeholder="e.g., Software Engineer, Teacher, etc.">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="organization">Organization/Company</label>
                                    <div class="input-container">
                                        <i class="fas fa-building"></i>
                                        <input type="text" id="organization" name="organization" placeholder="Your workplace or organization">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alumniStatus">Alumni Status</label>
                                <div class="input-container">
                                    <i class="fas fa-graduation-cap"></i>
                                    <select id="alumniStatus" name="alumniStatus">
                                        <option value="">Not an alumni</option>
                                        <option value="university-of-colombo">University of Colombo Alumni</option>
                                        <option value="university-of-peradeniya">University of Peradeniya Alumni</option>
                                        <option value="university-of-moratuwa">University of Moratuwa Alumni</option>
                                        <option value="university-of-kelaniya">University of Kelaniya Alumni</option>
                                        <option value="university-of-jaffna">University of Jaffna Alumni</option>
                                        <option value="other-sri-lankan">Other Sri Lankan University Alumni</option>
                                        <option value="international">International University Alumni</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="howHeard">How did you hear about UniPulse?</label>
                                <div class="input-container">
                                    <i class="fas fa-question-circle"></i>
                                    <select id="howHeard" name="howHeard">
                                        <option value="">Select an option</option>
                                        <option value="social-media">Social Media</option>
                                        <option value="friend-referral">Friend/Colleague Referral</option>
                                        <option value="university-website">University Website</option>
                                        <option value="search-engine">Search Engine</option>
                                        <option value="event-promotion">Event Promotion</option>
                                        <option value="news-article">News Article</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="previousStep()">
                                <i class="fas fa-arrow-left"></i>
                                <span>Previous</span>
                            </button>
                            <button type="submit" class="btn-submit">
                                <span>Create Account</span>
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Terms and Conditions -->
                    <div class="terms-section">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="termsAccepted" name="termsAccepted" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    I agree to the <a href="#terms" target="_blank">Terms of Service</a> and 
                                    <a href="#privacy" target="_blank">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="emailUpdates" name="emailUpdates">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    Send me updates about events and new features
                                </span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
            </div>
            <div class="footer-copyright">
                © 2025 UniPulse. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Creating your account...</p>
        </div>
    </div>

    <!-- Success message -->
    <div class="toast" id="toast" style="display: none;">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span class="toast-message"></span>
        </div>
        <button class="toast-close" id="toastClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script src="/assets/js/multi-step-registration-app.js"></script>
</body>
</html>