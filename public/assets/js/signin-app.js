// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initFormValidation();
    initPasswordToggle();
    initToastSystem();
    initFormSubmission();
    initKeyboardShortcuts();
});

// Form Validation
function initFormValidation() {
    const form = document.getElementById('signinForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    // Real-time validation
    emailInput.addEventListener('blur', () => validateEmail());
    emailInput.addEventListener('input', () => clearError('email'));
    
    passwordInput.addEventListener('blur', () => validatePassword());
    passwordInput.addEventListener('input', () => clearError('password'));
    
    // Validate email
    function validateEmail() {
        const email = emailInput.value.trim();
        const emailGroup = emailInput.closest('.form-group');
        const errorElement = document.getElementById('emailError');
        
        if (!email) {
            showFieldError('email', 'Email address is required');
            return false;
        }
        
        if (!isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email address');
            return false;
        }
        
        showFieldSuccess('email');
        return true;
    }
    
    // Validate password
    function validatePassword() {
        const password = passwordInput.value;
        const passwordGroup = passwordInput.closest('.form-group');
        const errorElement = document.getElementById('passwordError');
        
        if (!password) {
            showFieldError('password', 'Password is required');
            return false;
        }
        
        if (password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters');
            return false;
        }
        
        showFieldSuccess('password');
        return true;
    }
    
    // Validate entire form
    function validateForm() {
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        
        return isEmailValid && isPasswordValid;
    }
    
    // Public validation function
    window.validateSigninForm = validateForm;
}

// Password Toggle Functionality
function initPasswordToggle() {
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
}

// Toast Notification System
function initToastSystem() {
    window.showToast = function(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon');
        
        // Set message
        toastMessage.textContent = message;
        
        // Set type and icon
        toast.className = `toast ${type}`;
        if (type === 'success') {
            toastIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        } else if (type === 'error') {
            toastIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
        }
        
        // Show toast
        toast.classList.add('show');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            hideToast();
        }, 5000);
    };
    
    window.hideToast = function() {
        const toast = document.getElementById('toast');
        toast.classList.remove('show');
    };
    
    // Close toast button
    const toastClose = document.getElementById('toastClose');
    if (toastClose) {
        toastClose.addEventListener('click', hideToast);
    }
}

// Form Submission
function initFormSubmission() {
    const form = document.getElementById('signinForm');
    const signinBtn = document.getElementById('signinBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form
        if (!window.validateSigninForm()) {
            showToast('Please fix the errors in the form', 'error');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        // Get form data
        const formData = new FormData(form);
        const data = {
            email: formData.get('email'),
            password: formData.get('password')
        };
        
        try {
            // Simulate API call
            const response = await submitSignin(data);
            
            if (response.success) {
                showToast('Sign in successful! Redirecting...', 'success');
                
                // Redirect after success
                setTimeout(() => {
                    window.location.href = response.redirectUrl || 'dashboard.php';
                }, 1500);
            } else {
                throw new Error(response.message || 'Sign in failed');
            }
            
        } catch (error) {
            console.error('Sign in error:', error);
            showToast(error.message || 'Sign in failed. Please try again.', 'error');
            setLoadingState(false);
        }
    });
    
    // Set loading state
    function setLoadingState(loading) {
        const btn = document.getElementById('signinBtn');
        
        if (loading) {
            btn.classList.add('loading');
            btn.disabled = true;
        } else {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    }
}

// Simulate backend signin
async function submitSignin(data) {
    // Simulate network delay
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Simulate validation
    if (data.email === 'demo@unipulse.lk' && data.password === 'password123') {
        return {
            success: true,
            message: 'Sign in successful',
            redirectUrl: 'dashboard.php',
            user: {
                id: 1,
                email: data.email,
                name: 'Demo User'
            }
        };
    } else {
        throw new Error('Invalid email or password');
    }
}

// Keyboard Shortcuts
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Enter key to submit form
        if (e.key === 'Enter' && (e.target.tagName === 'INPUT')) {
            const form = document.getElementById('signinForm');
            const submitEvent = new Event('submit');
            form.dispatchEvent(submitEvent);
        }
        
        // Escape key to close toast
        if (e.key === 'Escape') {
            hideToast();
        }
    });
}

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(fieldName, message) {
    const input = document.getElementById(fieldName);
    const formGroup = input.closest('.form-group');
    const errorElement = document.getElementById(fieldName + 'Error');
    
    formGroup.classList.remove('success');
    formGroup.classList.add('error');
    errorElement.textContent = message;
}

function showFieldSuccess(fieldName) {
    const input = document.getElementById(fieldName);
    const formGroup = input.closest('.form-group');
    const errorElement = document.getElementById(fieldName + 'Error');
    
    formGroup.classList.remove('error');
    formGroup.classList.add('success');
    errorElement.textContent = '';
}

function clearError(fieldName) {
    const input = document.getElementById(fieldName);
    const formGroup = input.closest('.form-group');
    const errorElement = document.getElementById(fieldName + 'Error');
    
    if (formGroup.classList.contains('error')) {
        formGroup.classList.remove('error');
        errorElement.textContent = '';
    }
}

// Link Handlers
document.addEventListener('click', function(e) {
    // Forgot password link
    if (e.target.matches('a[href="#forgot-password"]')) {
        e.preventDefault();
        console.log('Forgot password clicked');
        showToast('Forgot password functionality coming soon!', 'success');
        // You could open a modal or redirect to forgot password page
    }
    
    // Create account link
    if (e.target.matches('a[href="#create-account"]')) {
        e.preventDefault();
        console.log('Create account clicked');
        // Redirect to signup page
        window.location.href = 'signup.php';
    }
    
    // Footer links
    if (e.target.matches('a[href="#terms"]')) {
        e.preventDefault();
        window.open('terms.html', '_blank');
    }
    
    if (e.target.matches('a[href="#privacy"]')) {
        e.preventDefault();
        window.open('privacy.html', '_blank');
    }
    
    if (e.target.matches('a[href="#contact"]')) {
        e.preventDefault();
        window.location.href = 'contact.php';
    }
});

// Auto-focus on email field
window.addEventListener('load', function() {
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.focus();
    }
});

// Console welcome message
console.log('%c🔐 UniPulse Sign In', 'color: #FF6B35; font-size: 16px; font-weight: bold;');
console.log('%cDemo credentials: demo@unipulse.lk / password123', 'color: #4A5BCC; font-size: 12px;');