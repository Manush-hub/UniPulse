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
            <form method="POST" action="" id="volunteerreg">

                <h3 class="section-header">Volunteering Information</h3>
                <div class="form-group">
                    <label for="optionsSelect">Choose a position:</label>
                    <select id="optionsSelect" name="volunteerPosition" required>
                        <option value="">-- Select a position --</option>
                        <!-- Options will be populated by JavaScript -->
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
    <script>
        // Function to populate the dropdown with options from localStorage
        function populateDropdown() {
            const optionsSelect = document.getElementById('optionsSelect');

            // Clear existing options except the first one
            while (optionsSelect.options.length > 1) {
                optionsSelect.remove(1);
            }

            // Load options from localStorage
            const savedOptions = JSON.parse(localStorage.getItem('customOptions')) || [];

            // Add options from localStorage
            savedOptions.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                optionsSelect.appendChild(optionElement);
            });

            // If no options are available, show a message
            if (savedOptions.length === 0) {
                const noOption = document.createElement('option');
                noOption.value = "";
                noOption.textContent = "No positions available yet - Please add positions in the event creation page";
                noOption.disabled = true;
                noOption.selected = true;
                optionsSelect.appendChild(noOption);
            }
        }

        // Run when the document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Populate the dropdown
            populateDropdown();

            // Add event listener for the Clear Positions button (if you add it)
            const clearPositionsBtn = document.getElementById('clearPositions');
            if (clearPositionsBtn) {
                clearPositionsBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear all positions?')) {
                        localStorage.removeItem('customOptions');
                        populateDropdown(); // Refresh the dropdown
                        alert('All positions have been cleared.');
                    }
                });
            }
        });
    </script>
</body>

</html>