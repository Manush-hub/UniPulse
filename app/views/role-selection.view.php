<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Category - UniPulse</title>
    <link rel="stylesheet" href="/assets/css/role-selection-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <div class="header-breadcrumb">
                <a href="/index.php?url=onboarding" class="breadcrumb-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Role Selection
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="role-selection-container">
            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <span>Choose Role</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-number">2</div>
                    <span>Select Category</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <span>Registration</span>
                </div>
            </div>

            <!-- Category Selection Section -->
            <div class="category-selection">
                <div class="selection-header">
                    <h1>Select Your Category</h1>
                    <p>Choose the option that best describes your affiliation to help us create the perfect experience for you</p>
                </div>

                <div class="category-cards">
                    <!-- University Students/Staff Card -->
                    <div class="category-card" data-category="university">
                        <div class="card-header">
                            <div class="card-icon university">
                                <i class="fas fa-university"></i>
                            </div>
                            <h3>University Students/Staff</h3>
                        </div>
                        <div class="card-content">
                            <p>Current students, faculty, or staff members of Sri Lankan universities</p>
                            <div class="card-benefits">
                                <h4>Benefits include:</h4>
                                <ul>
                                    <li><i class="fas fa-check-circle"></i> Access to exclusive university events</li>
                                    <li><i class="fas fa-check-circle"></i> Student discounts and special pricing</li>
                                    <li><i class="fas fa-check-circle"></i> University-specific notifications</li>
                                    <li><i class="fas fa-check-circle"></i> Academic calendar integration</li>
                                    <li><i class="fas fa-check-circle"></i> Priority registration for popular events</li>
                                </ul>
                            </div>
                            <div class="verification-note">
                                <i class="fas fa-info-circle"></i>
                                <span>University affiliation will be verified during registration</span>
                            </div>
                        </div>
                        <button class="category-btn university-btn" onclick="selectCategory('university')">
                            <span>Continue as University Member</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Public Users Card -->
                    <div class="category-card" data-category="public">
                        <div class="card-header">
                            <div class="card-icon public">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Public Users</h3>
                        </div>
                        <div class="card-content">
                            <p>General public, alumni, parents, or anyone interested in university events</p>
                            <div class="card-benefits">
                                <h4>Benefits include:</h4>
                                <ul>
                                    <li><i class="fas fa-check-circle"></i> Access to public university events</li>
                                    <li><i class="fas fa-check-circle"></i> Community event notifications</li>
                                    <li><i class="fas fa-check-circle"></i> Alumni networking opportunities</li>
                                    <li><i class="fas fa-check-circle"></i> Cultural and educational events</li>
                                    <li><i class="fas fa-check-circle"></i> Open lectures and seminars</li>
                                </ul>
                            </div>
                            <div class="verification-note">
                                <i class="fas fa-info-circle"></i>
                                <span>Quick registration with basic information</span>
                            </div>
                        </div>
                        <button class="category-btn public-btn" onclick="selectCategory('public')">
                            <span>Continue as Public User</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="help-section">
                    <div class="help-card">
                        <h4><i class="fas fa-question-circle"></i> Need Help Choosing?</h4>
                        <p>If you're unsure about your category, here are some guidelines:</p>
                        <ul>
                            <li><strong>Choose University:</strong> If you have a valid student ID, staff ID, or official university email</li>
                            <li><strong>Choose Public:</strong> If you're an alumnus, parent, community member, or don't have university credentials</li>
                        </ul>
                        <p>You can always contact our support team if you need assistance: <a href="mailto:support@unipulse.lk">support@unipulse.lk</a></p>
                    </div>
                </div>
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
                © 2025 UniPulse. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Store the selected role from the previous page
        const selectedRole = localStorage.getItem('selectedRole') || 'student';
        
        function selectCategory(category) {
            // Store the selected category
            localStorage.setItem('selectedCategory', category);
            
            // Navigate to appropriate registration flow
            if (category === 'university') {
                window.location.href = `/index.php?url=onboarding/university_registration&role=${selectedRole}`;
            } else {
                window.location.href = `/index.php?url=onboarding/public_registration&role=${selectedRole}`;
            }
        }

        // Update UI based on selected role
        document.addEventListener('DOMContentLoaded', function() {
            const roleCapitalized = selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1);
            document.querySelector('.selection-header h1').textContent = `${roleCapitalized} - Select Your Category`;
        });
    </script>
</body>
</html>