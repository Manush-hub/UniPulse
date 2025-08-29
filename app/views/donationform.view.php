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
            <h1>Make a Donation</h1>
            <span>Support student scholarship and event improvements!</span>
        </div>

        <div class="progress">
            <span>Step 2 of 2</span>
            <span>Complete Donation</span>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar-left"></div>
            <div class="progress-bar-right"></div>
        </div>

        <div class="content-wrapper">

            <!--Donation Form-->
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


                <h3 class="section-header">Donation Amount</h3>
                <div class="form-group">
                    <div class="checkbox-grid" style="padding: 3px;">
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation100" value="100" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation500" value="500" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation1000" value="1000" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation2000" value="2000" style="background-color: #F97316;">
                        </div>
                    </div>
                    <input type="text" id="custom-amount" placeholder="Enter custom amount">
                </div>


                <h3 class="section-header">Donation Purpose</h3>
                <div class="form-group">
                    <label for="Donation-For">Donation For</label>
                    <select id="Donation-For" name="Donation-For" required>
                        <option value="general-support">General Support</option>
                        <option value="event-imp">Event improvements</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="checkbox-grid" style="display: contents;">
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="d1" value="d1">
                            <label for="d1">Make this donation anonymous.</label>
                        </div>
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="d2" name="d2" value="d2">
                            <label for="sports">Send me update about how my donation is being used.</label>
                        </div>
                    </div>
                </div>

                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="button">Donate Now</button>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="university-student-registration.js"></script>
</body>

</html>