<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Sponsor Registration</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/sponsorreg-style.css">
</head>

<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Container -->
    <div class="main-container">
        <h1 class="signup-title">Join UniPulse Community</h1>
        <div class="signup-subtitle">
            Discover and participate in university events across Sri Lanka
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

            <div class="form-header">
                <h3>Company/Individual Information</h3>
            </div>

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
                <div class="form-group">
                    <label for="name">Company/Individual Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" value="<?= getValue('name', $formData) ?>" required>
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
            const submitButton = document.querySelector('.button');
            
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