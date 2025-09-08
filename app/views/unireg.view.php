<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - University Student/Staff Registration</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/userreg-style.css">
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
            <span>Step 2 of 2</span>
            <span>Complete Registration</span>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar-left"></div>
            <div class="progress-bar-right"></div>
        </div>

        <div class="content-wrapper">
            <div class="form-header">
                <h2>Create Your Account</h2>
                <span>Discover and participate in university events across Sri Lanka</span>
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
            <form method="POST" action=""><?php
                function getValue($key, $formData = null) {
                    if ($formData && isset($formData[$key])) {
                        return htmlspecialchars($formData[$key]);
                    }
                    return '';
                }
                $formData = isset($form_data) ? $form_data : null;
            ?>
                <h3 class="section-header">Personal Information</h3>

                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" value="<?= getValue('full-name', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?= getValue('email', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone-number">Phone Number</label>
                    <div class="field">
                        <select id="country-code" name="country-code" required>
                            <option value="+94" <?= getValue('country-code', $formData) === '+94' ? 'selected' : '' ?>>LK +94</option>
                            <option value="+91" <?= getValue('country-code', $formData) === '+91' ? 'selected' : '' ?>>IN +91</option>
                            <option value="+44" <?= getValue('country-code', $formData) === '+44' ? 'selected' : '' ?>>UK +44</option>
                            <option value="+1" <?= getValue('country-code', $formData) === '+1' ? 'selected' : '' ?>>US +1</option>
                        </select>
                        <input type="tel" id="phone-number" name="phone" placeholder="Enter your phone number" value="<?= getValue('phone', $formData) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create your password" required>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>

                <h3 class="section-header">University Information</h3>

                <div class="form-group">
                    <label for="university">University</label>
                    <select id="university" name="university" required>
                        <option value="">Select your university</option>
                        <option value="university-of-colombo" <?= getValue('university', $formData) === 'university-of-colombo' ? 'selected' : '' ?>>University of Colombo</option>
                        <option value="university-of-peradeniya" <?= getValue('university', $formData) === 'university-of-peradeniya' ? 'selected' : '' ?>>University of Peradeniya</option>
                        <option value="university-of-kelaniya" <?= getValue('university', $formData) === 'university-of-kelaniya' ? 'selected' : '' ?>>University of Kelaniya</option>
                        <option value="university-of-moratuwa" <?= getValue('university', $formData) === 'university-of-moratuwa' ? 'selected' : '' ?>>University of Moratuwa</option>
                        <option value="university-of-sri-jayewardenepura" <?= getValue('university', $formData) === 'university-of-sri-jayewardenepura' ? 'selected' : '' ?>>University of Sri Jayewardenepura</option>
                        <option value="university-of-ruhuna" <?= getValue('university', $formData) === 'university-of-ruhuna' ? 'selected' : '' ?>>University of Ruhuna</option>
                        <option value="eastern-university" <?= getValue('university', $formData) === 'eastern-university' ? 'selected' : '' ?>>Eastern University</option>
                        <option value="university-of-jaffna" <?= getValue('university', $formData) === 'university-of-jaffna' ? 'selected' : '' ?>>University of Jaffna</option>
                        <option value="sabaragamuwa-university" <?= getValue('university', $formData) === 'sabaragamuwa-university' ? 'selected' : '' ?>>Sabaragamuwa University</option>
                        <option value="wayamba-university" <?= getValue('university', $formData) === 'wayamba-university' ? 'selected' : '' ?>>Wayamba University</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty/Department</label>
                    <select id="faculty" name="faculty" required>
                        <option value="">Select your faculty/department</option>
                        <option value="faculty-of-arts" <?= getValue('faculty', $formData) === 'faculty-of-arts' ? 'selected' : '' ?>>Faculty of Arts</option>
                        <option value="faculty-of-science" <?= getValue('faculty', $formData) === 'faculty-of-science' ? 'selected' : '' ?>>Faculty of Science</option>
                        <option value="faculty-of-engineering" <?= getValue('faculty', $formData) === 'faculty-of-engineering' ? 'selected' : '' ?>>Faculty of Engineering</option>
                        <option value="faculty-of-medicine" <?= getValue('faculty', $formData) === 'faculty-of-medicine' ? 'selected' : '' ?>>Faculty of Medicine</option>
                        <option value="faculty-of-law" <?= getValue('faculty', $formData) === 'faculty-of-law' ? 'selected' : '' ?>>Faculty of Law</option>
                        <option value="faculty-of-management" <?= getValue('faculty', $formData) === 'faculty-of-management' ? 'selected' : '' ?>>Faculty of Management</option>
                        <option value="faculty-of-education" <?= getValue('faculty', $formData) === 'faculty-of-education' ? 'selected' : '' ?>>Faculty of Education</option>
                        <option value="faculty-of-agriculture" <?= getValue('faculty', $formData) === 'faculty-of-agriculture' ? 'selected' : '' ?>>Faculty of Agriculture</option>
                        <option value="faculty-of-applied-sciences" <?= getValue('faculty', $formData) === 'faculty-of-applied-sciences' ? 'selected' : '' ?>>Faculty of Applied Sciences</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="student-staff-id">Student/Staff ID</label>
                    <input type="text" id="student-staff-id" name="student-staff-id" placeholder="Enter your student or staff id" value="<?= getValue('student-staff-id', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="academic-year">Academic Year</label>
                    <select id="academic-year" name="academic-year" required>
                        <option value="">Select your academic year</option>
                        <option value="1st-year" <?= getValue('academic-year', $formData) === '1st-year' ? 'selected' : '' ?>>1st Year</option>
                        <option value="2nd-year" <?= getValue('academic-year', $formData) === '2nd-year' ? 'selected' : '' ?>>2nd Year</option>
                        <option value="3rd-year" <?= getValue('academic-year', $formData) === '3rd-year' ? 'selected' : '' ?>>3rd Year</option>
                        <option value="4th-year" <?= getValue('academic-year', $formData) === '4th-year' ? 'selected' : '' ?>>4th Year</option>
                        <option value="5th-year" <?= getValue('academic-year', $formData) === '5th-year' ? 'selected' : '' ?>>5th Year</option>
                        <option value="postgraduate" <?= getValue('academic-year', $formData) === 'postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
                        <option value="phd" <?= getValue('academic-year', $formData) === 'phd' ? 'selected' : '' ?>>PhD</option>
                        <option value="staff" <?= getValue('academic-year', $formData) === 'staff' ? 'selected' : '' ?>>Staff Member</option>
                        <option value="faculty" <?= getValue('academic-year', $formData) === 'faculty' ? 'selected' : '' ?>>Faculty Member</option>
                    </select>
                </div>

                <h3 class="section-header">Additional Information</h3>

                <div class="form-group">
                    <label for="nic">NIC</label>
                    <input type="text" id="nic" name="nic" placeholder="Enter your NIC" value="<?= getValue('nic', $formData) ?>" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender (Optional)</label>
                    <select id="gender" name="gender">
                        <option value="">Select your gender</option>
                        <option value="male" <?= getValue('gender', $formData) === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= getValue('gender', $formData) === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= getValue('gender', $formData) === 'other' ? 'selected' : '' ?>>Other</option>
                        <option value="prefer-not-to-say" <?= getValue('gender', $formData) === 'prefer-not-to-say' ? 'selected' : '' ?>>Prefer not to say</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Interests</label>
                    <div class="checkbox-grid">
                        <div class="checkbox-item">
                            <input type="checkbox" id="academic" name="interests[]" value="academic" <?= (isset($formData['interests']) && in_array('academic', $formData['interests'])) ? 'checked' : '' ?>>
                            <label for="academic">Academic</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="sports" name="interests[]" value="sports" <?= (isset($formData['interests']) && in_array('sports', $formData['interests'])) ? 'checked' : '' ?>>
                            <label for="sports">Sports</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="cultural" name="interests[]" value="cultural" <?= (isset($formData['interests']) && in_array('cultural', $formData['interests'])) ? 'checked' : '' ?>>
                            <label for="cultural">Cultural</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="technology" name="interests[]" value="technology" <?= (isset($formData['interests']) && in_array('technology', $formData['interests'])) ? 'checked' : '' ?>>
                            <label for="technology">Technology</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="social" name="interests[]" value="social" <?= (isset($formData['interests']) && in_array('social', $formData['interests'])) ? 'checked' : '' ?>>
                            <label for="social">Social</label>
                        </div>
                    </div>
                </div>

                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="button">Create Account</button>

                <div class="toggle-form">Already have an account? <a href="/unipulse/public/signin">Log in</a></div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
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

    <script src="university-student-registration.js"></script>
</body>
</html>