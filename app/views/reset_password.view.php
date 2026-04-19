<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signin-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/unipulse/public/assets/css/reset-password-style.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="signin-container">
            <!-- Icon -->
            <div class="user-avatar">
                <i class="fas fa-shield-alt"></i>
            </div>

            <!-- Welcome Text -->
            <div class="welcome-section">
                <h1>Reset Your Password</h1>
                <p>Create a new secure password for your account</p>
            </div>

            <!-- Reset Password Form -->
            <div class="signin-form-container">
                <?php if (isset($error)): ?>
                    <div class="error-message-box">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                    
                    <?php if (isset($expired) && $expired): ?>
                        <div class="expired-link">
                            <i class="fas fa-clock"></i>
                            <h3>Link Expired</h3>
                            <p>This password reset link has expired or has already been used.</p>
                            <a href="/unipulse/public/forgotpassword" class="signin-btn" style="display: inline-block; text-decoration: none;">
                                <i class="fas fa-redo" style="margin-right: 8px;"></i>
                                Request New Reset Link
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!isset($expired) || !$expired): ?>
                <form class="signin-form" method="POST" action="/unipulse/public/forgotpassword/reset?token=<?= htmlspecialchars($token ?? '') ?>" id="resetForm">
                    <div class="form-header">
                        <h2>Create New Password</h2>
                        <?php if (isset($email)): ?>
                            <p style="color: #666; font-size: 14px;">
                                <i class="fas fa-user"></i> 
                                <?= htmlspecialchars($email) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Password Requirements -->
                    <div class="password-requirements">
                        <h4>
                            <i class="fas fa-info-circle"></i>
                            Password Requirements:
                        </h4>
                        <ul>
                            <li>At least 8 characters long</li>
                            <li>Mix of uppercase and lowercase letters (recommended)</li>
                            <li>Include numbers and special characters (recommended)</li>
                        </ul>
                    </div>

                    <!-- New Password Field -->
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-container">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Enter new password"
                                required
                                autofocus
                            >
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="input-container">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="Re-enter new password"
                                required
                            >
                            <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" style="font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="signin-btn">
                        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                        <span class="btn-text">Reset Password</span>
                    </button>

                    <!-- Back to Sign In Link -->
                    <div class="create-account" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                        <a href="/unipulse/public/signin">Back to Sign In</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="/unipulse/public/assets/js/reset-password-app.js"></script>
</body>
</html>
