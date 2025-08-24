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

            <!--Registration Form-->
            <form method="POST" action="">
                <h3 class="section-header">Personal Information</h3>

                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="phone-number">Phone Number</label>
                    <div class="field">
                        <select id="country-code" name="country-code" required>
                            <option value="+94">LK +94</option>
                            <option value="+91">IN +91</option>
                            <option value="+44">UK +44</option>
                            <option value="+1">US +1</option>
                        </select>
                        <input type="tel" id="phone-number" name="phone" placeholder="Enter your phone number" required>
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
                    <label for="date-of-birth">Date of Birth</label>
                    <input type="text" id="date-of-birth" name="date-of-birth" placeholder="mm / dd / yyyy" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender (Optional)</label>
                    <select id="gender" name="gender">
                        <option value="">Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer-not-to-say">Prefer not to say</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Interests</label>
                    <div class="checkbox-grid">
                        <div class="checkbox-item">
                            <input type="checkbox" id="academic" name="interests[]" value="academic">
                            <label for="academic">Academic</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="sports" name="interests[]" value="sports">
                            <label for="sports">Sports</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="cultural" name="interests[]" value="cultural">
                            <label for="cultural">Cultural</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="technology" name="interests[]" value="technology">
                            <label for="technology">Technology</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="social" name="interests[]" value="social">
                            <label for="social">Social</label>
                        </div>
                    </div>
                </div>

                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="button">Create Account</button>

                <div class="toggle-form">Already have an account? <a href="#">Log in</a></div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- <script src="public-user-registration.js"></script> -->
</body>
</html>