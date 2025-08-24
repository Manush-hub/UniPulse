<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Society/Club Registration</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/publisherreg-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

            <!--Registration Form-->
            <form method="POST" action="" enctype="multipart/form-data">
                <h3 class="section-header">Society/Club Information</h3>

                <div class="form-group">
                    <label for="society-name">Society/Club Name</label>
                    <input type="text" id="society-name" name="society-name" placeholder="Enter your society/club name" required>
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

                <!-- Updated Confirmation Section -->
                <div class="form-group file-upload-group">
                    <label for="confirmation">
                        Confirmation 
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

        // Faculty options change based on university selection
        document.getElementById('university').addEventListener('change', function() {
            const facultySelect = document.getElementById('faculty');
            // Reset faculty options
            facultySelect.innerHTML = '<option value="">Select your faculty/department</option>';
            
            // Add faculty options based on selected university
            const commonFaculties = [
                { value: 'faculty-of-arts', text: 'Faculty of Arts' },
                { value: 'faculty-of-science', text: 'Faculty of Science' },
                { value: 'faculty-of-engineering', text: 'Faculty of Engineering' },
                { value: 'faculty-of-medicine', text: 'Faculty of Medicine' },
                { value: 'faculty-of-law', text: 'Faculty of Law' },
                { value: 'faculty-of-management', text: 'Faculty of Management' },
                { value: 'faculty-of-education', text: 'Faculty of Education' },
                { value: 'faculty-of-agriculture', text: 'Faculty of Agriculture' },
                { value: 'faculty-of-applied-sciences', text: 'Faculty of Applied Sciences' }
            ];

            commonFaculties.forEach(faculty => {
                const option = document.createElement('option');
                option.value = faculty.value;
                option.textContent = faculty.text;
                facultySelect.appendChild(option);
            });
        });
    </script>
</body>
</html>