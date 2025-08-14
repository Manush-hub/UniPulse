<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signup-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="signup-container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Join UniPulse Community</h1>
                <p>Discover and participate in university events across Sri Lanka</p>
            </div>

            <!-- User Type Selection -->
            <div class="user-type-selection" id="userTypeSelection">
                <div class="user-type-grid">
                    <!-- Students Card -->
                    <div class="user-type-card" data-type="student">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Students</h3>
                        <div class="card-overlay">
                            <div class="overlay-content">
                                <i class="fas fa-check-circle"></i>
                                <span>Selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Event Organizers Card -->
                    <div class="user-type-card" data-type="organizer">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Event Organizers</h3>
                        <div class="card-overlay">
                            <div class="overlay-content">
                                <i class="fas fa-check-circle"></i>
                                <span>Selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sponsors Card -->
                    <div class="user-type-card" data-type="sponsor">
                        <div class="card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Sponsors</h3>
                        <div class="card-overlay">
                            <div class="overlay-content">
                                <i class="fas fa-check-circle"></i>
                                <span>Selected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form (Hidden Initially) -->
            <div class="registration-form-container" id="registrationForm" style="display: none;">
                <div class="form-header">
                    <button class="back-btn" id="backBtn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <div class="form-title">
                        <h2 id="formTitle">Create Student Account</h2>
                        <p id="formSubtitle">Fill in your details to get started</p>
                    </div>
                </div>

                <form class="signup-form" id="signupForm">
                    <input type="hidden" id="userType" name="userType" value="">

                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <div class="input-container">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                                </div>
                                <span class="error-message" id="firstNameError"></span>
                            </div>

                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <div class="input-container">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                                </div>
                                <span class="error-message" id="lastNameError"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-container">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                            </div>
                            <span class="error-message" id="emailError"></span>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-container">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" placeholder="+94 77 123 4567" required>
                            </div>
                            <span class="error-message" id="phoneError"></span>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="form-section">
                        <h3>Account Security</h3>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-container">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <span class="strength-text">Password strength</span>
                            </div>
                            <span class="error-message" id="passwordError"></span>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <div class="input-container">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                                <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error-message" id="confirmPasswordError"></span>
                        </div>
                    </div>

                    <!-- User Type Specific Fields -->
                    <div class="form-section" id="specificFields">
                        <!-- Dynamic content based on user type -->
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="termsAccepted" name="termsAccepted" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    I agree to the <a href="#terms" target="_blank">Terms of Service</a> and 
                                    <a href="#privacy" target="_blank">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="emailUpdates" name="emailUpdates">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    Send me updates about events and new features
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="signup-btn" id="signupBtn">
                        <span class="btn-text">Create Account</span>
                        <div class="loading-spinner" id="loadingSpinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>

                    <!-- Sign In Link -->
                    <div class="signin-link">
                        <span>Already have an account? </span>
                        <a href="signin.php">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
            </div>
            <div class="footer-copyright">
                <span>&copy; 2025 UniPulse. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <i class="toast-icon"></i>
            <span class="toast-message"></span>
        </div>
        <button class="toast-close" id="toastClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script src="/unipulse/public/assets/js/signup-app.js"></script>
</body>
</html>