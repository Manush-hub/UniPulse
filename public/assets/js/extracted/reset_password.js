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