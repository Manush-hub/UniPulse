<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Public User Registration</title>
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
                <span>Complete your registration to start discovering public events</span>
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

    <!-- <script src="public-user-registration.js"></script> -->
</body>
</html>