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
            <h1>Become a Volunteer</h1>
            <span>Join our team & help make this event a success!</span>
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

                <h3 class="section-header">University Information</h3>

                <div class="form-group">
                    <label for="university">University</label>
                    <select id="university" name="university" required>
                        <option value="">Select your university</option>
                        <option value="university-of-colombo">University of Colombo</option>
                        <option value="university-of-peradeniya">University of Peradeniya</option>
                        <option value="university-of-kelaniya">University of Kelaniya</option>
                        <option value="university-of-moratuwa">University of Moratuwa</option>
                        <option value="university-of-sri-jayewardenepura">University of Sri Jayewardenepura</option>
                        <option value="university-of-ruhuna">University of Ruhuna</option>
                        <option value="eastern-university">Eastern University</option>
                        <option value="university-of-jaffna">University of Jaffna</option>
                        <option value="sabaragamuwa-university">Sabaragamuwa University</option>
                        <option value="wayamba-university">Wayamba University</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty/Department</label>
                    <select id="faculty" name="faculty" required>
                        <option value="">Select your faculty/department</option>
                        <option value="faculty-of-arts">Faculty of Arts</option>
                        <option value="faculty-of-science">Faculty of Science</option>
                        <option value="faculty-of-engineering">Faculty of Engineering</option>
                        <option value="faculty-of-medicine">Faculty of Medicine</option>
                        <option value="faculty-of-law">Faculty of Law</option>
                        <option value="faculty-of-management">Faculty of Management</option>
                        <option value="faculty-of-education">Faculty of Education</option>
                        <option value="faculty-of-agriculture">Faculty of Agriculture</option>
                        <option value="faculty-of-applied-sciences">Faculty of Applied Sciences</option>
                    </select>
                </div>

                <h3 class="section-header">Additional Information</h3>
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


                <h3 class="section-header">Volunteering Information</h3>

                <div class="form-group">
                    <label for="position">Prefered Position</label>
                    <select id="position" name="position" required>
                        <option value="">Select Position</option>
                        <option value="reg">Registration & check-in</option>
                        <option value="logistic">Event Logistic</option>
                        <option value="technical">Technical Support</option>
                        <option value="guest-services">Guest Services</option>
                        <option value="marketing">Marketing & Social Meadia</option>
                        <option value="setup">Setup & Breakdown</option>
                        <option value="any">Any Position Needed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty">Availability</label>
                    <select id="Availability" name="Availability" required>
                        <option value="">Select your availability</option>
                        <option value="full-day">Full day (8+ hours)</option>
                        <option value="morning-shift">Morning shift (4-6 hours)</option>
                        <option value="Afternoon-shift">Afternoon shift (4-6 hours)</option>
                        <option value="Evening-shift">Evening shift (4-6 hours)</option>
                        <option value="Flexible">Flexible</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="Experience">Relevant Experience</label>
                    <input type="text" id="Experience" name="Experience" required>
                </div>

                <div class="form-group">
                    <label for="ask">Why do you want to be a volunteer?</label>
                    <input type="text" id="ask" name="ask" required>
                </div>

                <div class="form-group">
                    <label for="Skills">Special Skills</label>
                    <input type="text" id="Skills" name="Skills" required>
                </div>

                <div class="form-group">
                    <div class="checkbox-grid" style="display: contents;">
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="academic" name="interests[]" value="academic">
                            <label for="academic">I have reliable transportation to the event location.</label>
                        </div>
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="sports" name="interests[]" value="sports">
                            <label for="sports">I understand this is a commitment and will show up as schedule.</label>
                        </div>
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="cultural" name="interests[]" value="cultural">
                            <label for="cultural">Send me update about volunteer opportunities.</label>
                        </div>
                    </div>
                </div>

                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="button">Apply to Volunteer</button>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="university-student-registration.js"></script>
</body>

</html>