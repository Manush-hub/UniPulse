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

            <div class="form-header">
                <h3>Company/Individual Information</h3>
            </div>

            <!--Registration Form-->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Company/Individual Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
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
</body>
</html>