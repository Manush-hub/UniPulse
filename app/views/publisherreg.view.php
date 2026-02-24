<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Society/Club Registration</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/publisherreg-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .required {
            color: #dc3545;
            margin-left: 3px;
            font-weight: bold;
        }
        
        /* Dropdown with scroll - show 5 items */
        select#university,
        select#faculty,
        select#country-code {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        select#university option,
        select#faculty option,
        select#country-code option {
            padding: 10px;
        }
        
        /* Set size attribute to show 5 visible items when opened */
        select#university[size],
        select#faculty[size],
        select#country-code[size] {
            height: auto;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Container -->
    <div class="main-container">
        <div class="intro">
            <h1>Join UniPulse Community</h1>
            <span>Discover and participate in university events across Sri Lanka</span>
        </div>

        <div class="progress">
            <span>Step 1 of 1</span>
            <span>Complete Registration</span>
        </div>

        <div class="progress-bar"></div>

        <div class="content-wrapper">
            <div class="form-header">
                <h2>Create Your Account</h2>
                <span>Host and promote your university events to students across Sri Lanka</span>
            </div>

            <!-- Success Message -->
            <?php if (isset($success_message)): ?>
                <div class="success-message" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="error-messages" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!--Registration Form-->
            <form method="POST" action="" enctype="multipart/form-data"><?php
                function getValue($key, $formData = null) {
                    if ($formData && isset($formData[$key])) {
                        return htmlspecialchars($formData[$key]);
                    }
                    return '';
                }
                $formData = isset($form_data) ? $form_data : null;
            ?>
                <h3 class="section-header">Society/Club Information</h3>

                <div class="form-group">
                    <label for="society-name">Society/Club Name <span class="required">*</span></label>
                    <input type="text" id="society-name" name="society-name" placeholder="Enter your society/club name" value="<?= getValue('society-name', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?= getValue('email', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone-number">Phone Number <span class="required">*</span></label>
                    <div class="field">
                        <select id="country-code" name="country-code" size="1" required>
                            <option value="+94" <?= getValue('country-code', $formData) === '+94' ? 'selected' : '' ?>>LK +94</option>
                            <option value="+91" <?= getValue('country-code', $formData) === '+91' ? 'selected' : '' ?>>IN +91</option>
                            <option value="+44" <?= getValue('country-code', $formData) === '+44' ? 'selected' : '' ?>>UK +44</option>
                            <option value="+1" <?= getValue('country-code', $formData) === '+1' ? 'selected' : '' ?>>US +1</option>
                        </select>
                        <input type="tel" id="phone-number" name="phone" placeholder="Enter your phone number" value="<?= getValue('phone', $formData) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Create your password" required>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>

                <!-- Updated Confirmation Section -->
                <div class="form-group file-upload-group">
                    <label for="confirmation">
                        Confirmation <span class="required">*</span>
                        <span class="help-icon" onclick="toggleHelp()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </span>
                    </label>
                    <div class="help-tooltip" id="helpTooltip">
                        <h4>Club Verification Document Required:</h4>
                        <p>Upload <strong>one</strong> of the following documents to verify your club:</p>
                        <ul>
                            <li>Official registration certificate from your university</li>
                            <li>Society/Club constitution or charter document</li>
                            <li>Letter of recommendation from faculty advisor</li>
                            <li>University approval letter for the society/club</li>
                        </ul>
                        <p><strong>Accepted formats:</strong> PDF, JPG, PNG, DOC, DOCX (Max 5MB)</p>
                    </div>
                    <div class="file-upload-container">
                        <input type="text" class="file-upload-input" placeholder="Upload club verification document" readonly>
                        <label for="confirmation-file" class="file-upload-button">Upload</label>
                        <input type="file" id="confirmation-file" name="confirmation-file" class="file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                    </div>
                    <div class="file-info">
                        <small>Upload one document to verify your club. Maximum 5MB.</small>
                    </div>
                </div>

                <h3 class="section-header">University Information</h3>

                <div class="form-group">
                    <label for="university">University <span class="required">*</span></label>
                    <select id="university" name="university" size="1" required>
                        <option value="">Select your university</option>
                        <!-- State Universities (15) -->
                        <option value="university-of-colombo" <?= getValue('university', $formData) === 'university-of-colombo' ? 'selected' : '' ?>>University of Colombo</option>
                        <option value="university-of-peradeniya" <?= getValue('university', $formData) === 'university-of-peradeniya' ? 'selected' : '' ?>>University of Peradeniya</option>
                        <option value="university-of-sri-jayewardenepura" <?= getValue('university', $formData) === 'university-of-sri-jayewardenepura' ? 'selected' : '' ?>>University of Sri Jayewardenepura</option>
                        <option value="university-of-kelaniya" <?= getValue('university', $formData) === 'university-of-kelaniya' ? 'selected' : '' ?>>University of Kelaniya</option>
                        <option value="university-of-moratuwa" <?= getValue('university', $formData) === 'university-of-moratuwa' ? 'selected' : '' ?>>University of Moratuwa</option>
                        <option value="university-of-jaffna" <?= getValue('university', $formData) === 'university-of-jaffna' ? 'selected' : '' ?>>University of Jaffna</option>
                        <option value="university-of-ruhuna" <?= getValue('university', $formData) === 'university-of-ruhuna' ? 'selected' : '' ?>>University of Ruhuna</option>
                        <option value="eastern-university" <?= getValue('university', $formData) === 'eastern-university' ? 'selected' : '' ?>>Eastern University, Sri Lanka</option>
                        <option value="south-eastern-university" <?= getValue('university', $formData) === 'south-eastern-university' ? 'selected' : '' ?>>South Eastern University of Sri Lanka</option>
                        <option value="rajarata-university" <?= getValue('university', $formData) === 'rajarata-university' ? 'selected' : '' ?>>Rajarata University of Sri Lanka</option>
                        <option value="sabaragamuwa-university" <?= getValue('university', $formData) === 'sabaragamuwa-university' ? 'selected' : '' ?>>Sabaragamuwa University of Sri Lanka</option>
                        <option value="wayamba-university" <?= getValue('university', $formData) === 'wayamba-university' ? 'selected' : '' ?>>Wayamba University of Sri Lanka</option>
                        <option value="uva-wellassa-university" <?= getValue('university', $formData) === 'uva-wellassa-university' ? 'selected' : '' ?>>Uva Wellassa University</option>
                        <option value="open-university" <?= getValue('university', $formData) === 'open-university' ? 'selected' : '' ?>>Open University of Sri Lanka</option>
                        <option value="buddhist-and-pali-university" <?= getValue('university', $formData) === 'buddhist-and-pali-university' ? 'selected' : '' ?>>Buddhist and Pali University of Sri Lanka</option>
                        <!-- Private Universities (5 Main) -->
                        <option value="sliit" <?= getValue('university', $formData) === 'sliit' ? 'selected' : '' ?>>Sri Lanka Institute of Information Technology (SLIIT)</option>
                        <option value="nsbm" <?= getValue('university', $formData) === 'nsbm' ? 'selected' : '' ?>>NSBM Green University</option>
                        <option value="cinec" <?= getValue('university', $formData) === 'cinec' ? 'selected' : '' ?>>CINEC Campus</option>
                        <option value="apiit" <?= getValue('university', $formData) === 'apiit' ? 'selected' : '' ?>>Asia Pacific Institute of Information Technology (APIIT)</option>
                        <option value="metropolitan-campus" <?= getValue('university', $formData) === 'metropolitan-campus' ? 'selected' : '' ?>>KIU (Kaatsu International University)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty/Department <span class="required">*</span></label>
                    <select id="faculty" name="faculty" size="1" required>
                        <option value="">Select your faculty/department</option>
                        <!-- Most Famous Faculties -->
                        <option value="ucsc" <?= getValue('faculty', $formData) === 'ucsc' ? 'selected' : '' ?>>University of Colombo School of Computing (UCSC)</option>
                        <option value="faculty-of-engineering" <?= getValue('faculty', $formData) === 'faculty-of-engineering' ? 'selected' : '' ?>>Faculty of Engineering</option>
                        <option value="faculty-of-medicine" <?= getValue('faculty', $formData) === 'faculty-of-medicine' ? 'selected' : '' ?>>Faculty of Medicine</option>
                        <option value="faculty-of-science" <?= getValue('faculty', $formData) === 'faculty-of-science' ? 'selected' : '' ?>>Faculty of Science</option>
                        <option value="faculty-of-management" <?= getValue('faculty', $formData) === 'faculty-of-management' ? 'selected' : '' ?>>Faculty of Management and Finance</option>
                        <option value="faculty-of-arts" <?= getValue('faculty', $formData) === 'faculty-of-arts' ? 'selected' : '' ?>>Faculty of Arts</option>
                        <option value="faculty-of-law" <?= getValue('faculty', $formData) === 'faculty-of-law' ? 'selected' : '' ?>>Faculty of Law</option>
                        <option value="faculty-of-information-technology" <?= getValue('faculty', $formData) === 'faculty-of-information-technology' ? 'selected' : '' ?>>Faculty of Information Technology</option>
                        <option value="faculty-of-applied-sciences" <?= getValue('faculty', $formData) === 'faculty-of-applied-sciences' ? 'selected' : '' ?>>Faculty of Applied Sciences</option>
                        <option value="faculty-of-agriculture" <?= getValue('faculty', $formData) === 'faculty-of-agriculture' ? 'selected' : '' ?>>Faculty of Agriculture</option>
                        <option value="faculty-of-architecture" <?= getValue('faculty', $formData) === 'faculty-of-architecture' ? 'selected' : '' ?>>Faculty of Architecture</option>
                        <option value="faculty-of-education" <?= getValue('faculty', $formData) === 'faculty-of-education' ? 'selected' : '' ?>>Faculty of Education</option>
                        <option value="faculty-of-social-sciences" <?= getValue('faculty', $formData) === 'faculty-of-social-sciences' ? 'selected' : '' ?>>Faculty of Social Sciences</option>
                        <option value="faculty-of-allied-health-sciences" <?= getValue('faculty', $formData) === 'faculty-of-allied-health-sciences' ? 'selected' : '' ?>>Faculty of Allied Health Sciences</option>
                        <option value="faculty-of-dental-sciences" <?= getValue('faculty', $formData) === 'faculty-of-dental-sciences' ? 'selected' : '' ?>>Faculty of Dental Sciences</option>
                        <option value="other" <?= getValue('faculty', $formData) === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="/unipulse/public/terms" target="_blank" rel="noopener noreferrer">Terms & Conditions</a> and <a href="/unipulse/public/privacy_policy" target="_blank" rel="noopener noreferrer">Privacy Policy</a></label>
                </div>

                <button type="submit" class="button">Create Account</button>

                <div class="toggle-form">Already have an account? <a href="/unipulse/public/signin">Log in</a></div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        // Help tooltip functionality
        function toggleHelp() {
            const tooltip = document.getElementById('helpTooltip');
            tooltip.classList.toggle('active');
        }

        // Close tooltip when clicking outside
        document.addEventListener('click', function(e) {
            const helpIcon = document.querySelector('.help-icon');
            const tooltip = document.getElementById('helpTooltip');
            
            if (!helpIcon.contains(e.target) && !tooltip.contains(e.target)) {
                tooltip.classList.remove('active');
            }
        });

        // Single file upload functionality
        document.getElementById('confirmation-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInput = document.querySelector('.file-upload-input');
            
            if (file) {
                fileInput.value = file.name;
                fileInput.classList.add('has-files');
            } else {
                fileInput.value = 'Upload club verification document';
                fileInput.classList.remove('has-files');
            }
        });

        // File validation for single file
        document.getElementById('confirmation-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'];
            
            // Check file size
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                e.target.value = '';
                document.querySelector('.file-upload-input').value = 'Upload club verification document';
                document.querySelector('.file-upload-input').classList.remove('has-files');
                return;
            }
            
            // Check file type
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExtension)) {
                alert(`File "${file.name}" has an unsupported format. Please use: PDF, JPG, PNG, DOC, or DOCX.`);
                e.target.value = '';
                document.querySelector('.file-upload-input').value = 'Upload club verification document';
                document.querySelector('.file-upload-input').classList.remove('has-files');
                return;
            }
        });

        // Terms validation with improved feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const termsCheckbox = document.getElementById('terms');
            
            // Add event listener to form submission
            form.addEventListener('submit', function(e) {
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Add visual feedback
                    termsCheckbox.style.border = '2px solid #dc3545';
                    
                    // Show alert
                    alert('Please agree to the Terms & Conditions and Privacy Policy to continue.');
                    
                    // Focus on checkbox
                    termsCheckbox.focus();
                    
                    // Remove visual feedback after 3 seconds
                    setTimeout(() => {
                        termsCheckbox.style.border = '2px solid #ccc';
                    }, 3000);
                    
                    return false;
                }
            });
            
            // Remove error styling when checkbox is checked
            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    this.style.border = '2px solid #ccc';
                }
            });
        });
    </script>
</body>
</html>