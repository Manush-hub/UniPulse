<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signin-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .password-requirements {
            background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%);
            border-left: 4px solid #9C27B0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .password-requirements h4 {
            margin: 0 0 10px 0;
            color: #7B1FA2;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding: 0 0 0 20px;
        }
        
        .password-requirements li {
            color: #6A1B9A;
            font-size: 13px;
            margin: 5px 0;
        }
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { width: 33%; background: #f44336; }
        .strength-medium { width: 66%; background: #ff9800; }
        .strength-strong { width: 100%; background: #4caf50; }
        
        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .expired-link {
            text-align: center;
            padding: 30px;
        }
        
        .expired-link i {
            font-size: 60px;
            color: #f44336;
            margin-bottom: 20px;
        }
        
        .expired-link h3 {
            color: #f44336;
            margin-bottom: 15px;
        }
        
        .expired-link p {
            color: #666;
            margin-bottom: 25px;
        }
    </style>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                
                if (toggle && input) {
                    toggle.addEventListener('click', function() {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        
                        const icon = this.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }
            
            setupPasswordToggle('passwordToggle', 'password');
            setupPasswordToggle('confirmPasswordToggle', 'confirm_password');
            
            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const strengthContainer = document.getElementById('passwordStrength');
            
            if (passwordInput && strengthBar) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    
                    if (password.length === 0) {
                        strengthContainer.style.display = 'none';
                        strengthText.style.display = 'none';
                        return;
                    }
                    
                    strengthContainer.style.display = 'block';
                    strengthText.style.display = 'block';
                    
                    let strength = 0;
                    
                    // Length check
                    if (password.length >= 8) strength++;
                    if (password.length >= 12) strength++;
                    
                    // Character variety checks
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                    if (/\d/.test(password)) strength++;
                    if (/[^a-zA-Z0-9]/.test(password)) strength++;
                    
                    // Remove all strength classes
                    strengthBar.className = 'password-strength-bar';
                    
                    // Apply appropriate strength class
                    if (strength <= 2) {
                        strengthBar.classList.add('strength-weak');
                        strengthText.textContent = '❌ Weak password';
                        strengthText.style.color = '#f44336';
                    } else if (strength <= 4) {
                        strengthBar.classList.add('strength-medium');
                        strengthText.textContent = '⚠️ Medium password';
                        strengthText.style.color = '#ff9800';
                    } else {
                        strengthBar.classList.add('strength-strong');
                        strengthText.textContent = '✅ Strong password';
                        strengthText.style.color = '#4caf50';
                    }
                });
            }
            
            // Password match indicator
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordMatchDiv = document.getElementById('passwordMatch');
            
            if (confirmPasswordInput && passwordInput && passwordMatchDiv) {
                function checkPasswordMatch() {
                    const password = passwordInput.value;
                    const confirmPassword = confirmPasswordInput.value;
                    
                    if (confirmPassword.length === 0) {
                        passwordMatchDiv.style.display = 'none';
                        return;
                    }
                    
                    passwordMatchDiv.style.display = 'block';
                    
                    if (password === confirmPassword) {
                        passwordMatchDiv.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                        passwordMatchDiv.style.color = '#4caf50';
                    } else {
                        passwordMatchDiv.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
                        passwordMatchDiv.style.color = '#f44336';
                    }
                }
                
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
                passwordInput.addEventListener('input', checkPasswordMatch);
            }
            
            // Form validation
            const form = document.getElementById('resetForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = passwordInput.value;
                    const confirmPassword = confirmPasswordInput.value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match. Please make sure both password fields are identical.');
                        confirmPasswordInput.focus();
                        return false;
                    }
                    
                    if (password.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters long.');
                        passwordInput.focus();
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>
