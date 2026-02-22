<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signin-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/forgot-password-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="signin-container">
            <!-- Icon -->
            <div class="user-avatar">
                <i class="fas fa-key"></i>
            </div>

            <!-- Welcome Text -->
            <div class="welcome-section">
                <h1>Forgot Password?</h1>
                <p>No worries! Enter your email and we'll send you reset instructions</p>
            </div>

            <!-- Forgot Password Form -->
            <div class="signin-form-container">
                <?php if (isset($error)): ?>
                    <div class="error-message-box">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="success-message-box">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                    
                    
                <?php else: ?>
                
                <form class="signin-form" method="POST" action="/unipulse/public/forgotpassword/send_reset_link" id="forgotPasswordForm">
                    <div class="form-header">
                        <h2>Reset Your Password</h2>
                        <p>Enter your email address and we'll send you a link to reset your password</p>
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
                                placeholder="Enter your registered email address"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="signin-btn" id="sendResetBtn">
                        <i class="fas fa-paper-plane forgot-password-icon"></i>
                        <span class="btn-text">Send Reset Link</span>
                    </button>

                    <div id="formStatus" class="forgot-password-status"></div>

                    <!-- Back to Sign In Link -->
                    <div class="create-account forgot-password-back">
                        <i class="fas fa-arrow-left forgot-password-icon"></i>
                        <a href="/unipulse/public/signin">Back to Sign In</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="/unipulse/public/assets/js/forgot-password.js"></script>
</body>
</html>
