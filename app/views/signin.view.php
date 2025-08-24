<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signin-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="signin-container">
            <!-- User Avatar -->
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>

            <!-- Welcome Text -->
            <div class="welcome-section">
                <h1>Welcome to UniPulse</h1>
                <p>Sign in to start discovering amazing university events</p>
            </div>

            <!-- Sign In Form -->
            <div class="signin-form-container">
                <form class="signin-form" id="signinForm">
                    <div class="form-header">
                        <h2>Sign In</h2>
                        <p>Enter your credentials to access your account</p>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-container">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="Enter your email address"
                                required
                            >
                        </div>
                        <span class="error-message" id="emailError"></span>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-container">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="error-message" id="passwordError"></span>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="forgot-password">
                        <a href="#forgot-password">Forgot password?</a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="signin-btn" id="signinBtn">
                        <span class="btn-text">Sign In</span>
                        <div class="loading-spinner" id="loadingSpinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>

                    <!-- Create Account Link -->
                    <div class="create-account">
                        <span>Don't have an account? </span>
                        <a href="/unipulse/public/signup">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Success/Error Messages -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <i class="toast-icon"></i>
            <span class="toast-message"></span>
        </div>
        <button class="toast-close" id="toastClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script src="/unipulse/public/assets/js/signin-app.js"></script>
</body>
</html>