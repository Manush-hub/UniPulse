<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation form</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/userreg-style.css">
    <style>
        /* Custom CSS for donation amount buttons */
        .submit-item input[type="button"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #F97316;
        }

        .submit-item input[type="button"]:hover {
            background-color: #4804c7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .submit-item input[type="button"]:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #custom-amount {
            margin-top: 10px;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
            background-color: #fafafa;
            width: 100%;
        }

        #custom-amount:focus {
            outline: none;
            border-color: #1E3A8A;
            background-color: white;
        }
    </style>
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

                <h3 class="section-header">Donation Amount</h3>
                <div class="form-group">
                    <div class="checkbox-grid" style="padding: 3px;">
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation100" value="LKR 100" data-amount="100" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation500" value="LKR 500" data-amount="500" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation1000" value="LKR 1000" data-amount="1000" style="background-color: #F97316;">
                        </div>
                        <div class="submit-item" style="padding: 3px;">
                            <input type="button" id="donation2000" value="LKR 2000" data-amount="2000" style="background-color: #F97316;">
                        </div>
                    </div>
                    <input type="text" id="custom-amount" name="custom-amount" placeholder="Enter custom amount (e.g., 50)">
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
                            <input type="checkbox" id="d1" name="d1" value="anonymous">
                            <label for="d1">Make this donation anonymous.</label>
                        </div>
                        <div class="checkbox-item" style="padding: 3px;">
                            <input type="checkbox" id="d2" name="d2" value="updates">
                            <label for="d2">Send me update about how my donation is being used.</label>
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

    <script>
        // JavaScript to handle donation amount buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Get all donation buttons
            const donationButtons = document.querySelectorAll('.submit-item input[type="button"]');
            const customAmountInput = document.getElementById('custom-amount');

            // Add click event to each button
            donationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get the amount from the data-attribute
                    const amount = this.getAttribute('data-amount');

                    // Set the custom amount input value
                    customAmountInput.value = amount;

                    // Optional: Add visual feedback for selected button
                    donationButtons.forEach(btn => {
                        btn.style.backgroundColor = '#F97316'; // Reset all buttons
                    });
                    this.style.backgroundColor = '#ea580c'; // Highlight selected button
                });
            });

            // Clear selection when custom amount is entered
            customAmountInput.addEventListener('focus', function() {
                donationButtons.forEach(button => {
                    button.style.backgroundColor = '#F97316'; // Reset all buttons
                });
            });
        });
    </script>
</body>

</html>