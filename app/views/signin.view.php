<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signin-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .error-message-box, .success-message-box {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .error-message-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .success-message-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message-box i, .success-message-box i {
            margin-right: 10px;
            font-size: 16px;
        }
    </style>
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
                <?php endif; ?>

                <form class="signin-form" method="POST" action="/unipulse/public/signin">
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
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required
                            >
                        </div>
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
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="forgot-password">
                        <a href="#forgot-password">Forgot password?</a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="signin-btn">
                        <span class="btn-text">Sign In</span>
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

    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            
            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
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
            
            // Auto-focus on email field
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>