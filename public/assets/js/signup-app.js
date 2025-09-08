// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initUserTypeSelection();
    initFormValidation();
    initPasswordToggle();
    initPasswordStrength();
    initToastSystem();
    initFormSubmission();
    initKeyboardShortcuts();
});

// User Type Selection - Updated for Direct Navigation
// function initUserTypeSelection() {
//     const userTypeCards = document.querySelectorAll('.user-type-card');
//     const userTypeSelection = document.getElementById('userTypeSelection');
//     const registrationForm = document.getElementById('registrationForm');
//     const backBtn = document.getElementById('backBtn');

//     // Handle card selection - Direct navigation
//     userTypeCards.forEach(card => {
//         card.addEventListener('click', function() {
//             // Get selected type
//             const selectedType = this.getAttribute('data-type');
            
//             console.log('Selected user type:', selectedType);
            
//             // Immediately navigate to form
//             navigateToForm(selectedType);
//         });
        
//         // Add hover effect
//         card.addEventListener('mouseenter', function() {
//             this.style.transform = 'translateY(-8px)';
//             this.style.boxShadow = '0 15px 40px rgba(74, 91, 204, 0.4)';
//         });
        
//         card.addEventListener('mouseleave', function() {
//             this.style.transform = 'translateY(0)';
//             this.style.boxShadow = '0 10px 30px rgba(74, 91, 204, 0.3)';
//         });
//     });

//     // Handle back button
//     backBtn.addEventListener('click', function() {
//         // Show user type selection
//         userTypeSelection.style.display = 'block';
        
//         // Hide registration form
//         registrationForm.style.display = 'none';
        
//         // Scroll to top
//         window.scrollTo({ top: 0, behavior: 'smooth' });
//     });

//     // Navigate to form function
//     function navigateToForm(selectedType) {
//         // Hide user type selection with animation
//         userTypeSelection.style.opacity = '0';
//         userTypeSelection.style.transform = 'translateY(-20px)';
        
//         setTimeout(() => {
//             userTypeSelection.style.display = 'none';
            
//             // Show registration form
//             registrationForm.style.display = 'block';
//             registrationForm.style.opacity = '0';
//             registrationForm.style.transform = 'translateY(20px)';
            
//             // Set user type in hidden field
//             document.getElementById('userType').value = selectedType;
            
//             // Update form title based on user type
//             updateFormTitle(selectedType);
            
//             // Load user type specific fields
//             loadSpecificFields(selectedType);
            
//             // Animate form in
//             setTimeout(() => {
//                 registrationForm.style.opacity = '1';
//                 registrationForm.style.transform = 'translateY(0)';
//             }, 50);
            
//             // Scroll to top
//             window.scrollTo({ top: 0, behavior: 'smooth' });
            
//             // Focus on first input
//             setTimeout(() => {
//                 const firstInput = registrationForm.querySelector('input');
//                 if (firstInput) {
//                     firstInput.focus();
//                 }
//             }, 300);
            
//         }, 300);
//     }

//     // Update form title based on user type
//     function updateFormTitle(type) {
//         const formTitle = document.getElementById('formTitle');
//         const formSubtitle = document.getElementById('formSubtitle');
        
//         const titles = {
//             student: {
//                 title: 'Create Student Account',
//                 subtitle: 'Join thousands of students discovering amazing events'
//             },
//             organizer: {
//                 title: 'Create Organizer Account',
//                 subtitle: 'Start organizing and managing incredible events'
//             },
//             sponsor: {
//                 title: 'Create Sponsor Account',
//                 subtitle: 'Partner with universities and support great events'
//             }
//         };
        
//         if (titles[type]) {
//             formTitle.textContent = titles[type].title;
//             formSubtitle.textContent = titles[type].subtitle;
//         }
//     }

//     // Load user type specific fields
//     function loadSpecificFields(type) {
//         const specificFields = document.getElementById('specificFields');
        
//         const fieldTemplates = {
//             student: window.location.href = "/unipulse/public/usersignup",
//             organizer: `
//                 <h3>Organization Information</h3>
//                 <div class="form-group">
//                     <label for="organization">Organization/Department</label>
//                     <div class="input-container">
//                         <i class="fas fa-building"></i>
//                         <input type="text" id="organization" name="organization" placeholder="e.g., Computer Science Society" required>
//                     </div>
//                     <span class="error-message" id="organizationError"></span>
//                 </div>
//                 <div class="form-row">
//                     <div class="form-group">
//                         <label for="position">Position</label>
//                         <div class="input-container">
//                             <i class="fas fa-user-tie"></i>
//                             <input type="text" id="position" name="position" placeholder="e.g., President, Secretary" required>
//                         </div>
//                         <span class="error-message" id="positionError"></span>
//                     </div>
//                     <div class="form-group">
//                         <label for="experience">Experience Level</label>
//                         <div class="input-container">
//                             <i class="fas fa-star"></i>
//                             <select id="experience" name="experience" required>
//                                 <option value="">Select experience</option>
//                                 <option value="beginner">Beginner (0-1 years)</option>
//                                 <option value="intermediate">Intermediate (2-5 years)</option>
//                                 <option value="expert">Expert (5+ years)</option>
//                             </select>
//                         </div>
//                         <span class="error-message" id="experienceError"></span>
//                     </div>
//                 </div>
//             `,
//             sponsor: `
//                 <h3>Company Information</h3>
//                 <div class="form-group">
//                     <label for="companyName">Company Name</label>
//                     <div class="input-container">
//                         <i class="fas fa-building"></i>
//                         <input type="text" id="companyName" name="companyName" placeholder="Enter company name" required>
//                     </div>
//                     <span class="error-message" id="companyNameError"></span>
//                 </div>
//                 <div class="form-row">
//                     <div class="form-group">
//                         <label for="industry">Industry</label>
//                         <div class="input-container">
//                             <i class="fas fa-industry"></i>
//                             <select id="industry" name="industry" required>
//                                 <option value="">Select industry</option>
//                                 <option value="technology">Technology</option>
//                                 <option value="finance">Finance</option>
//                                 <option value="healthcare">Healthcare</option>
//                                 <option value="education">Education</option>
//                                 <option value="retail">Retail</option>
//                                 <option value="other">Other</option>
//                             </select>
//                         </div>
//                         <span class="error-message" id="industryError"></span>
//                     </div>
//                     <div class="form-group">
//                         <label for="companySize">Company Size</label>
//                         <div class="input-container">
//                             <i class="fas fa-users"></i>
//                             <select id="companySize" name="companySize" required>
//                                 <option value="">Select size</option>
//                                 <option value="startup">Startup (1-50)</option>
//                                 <option value="medium">Medium (51-500)</option>
//                                 <option value="large">Large (500+)</option>
//                             </select>
//                         </div>
//                         <span class="error-message" id="companySizeError"></span>
//                     </div>
//                 </div>
//                 <div class="form-group">
//                     <label for="website">Company Website</label>
//                     <div class="input-container">
//                         <i class="fas fa-globe"></i>
//                         <input type="url" id="website" name="website" placeholder="https://www.company.com">
//                     </div>
//                     <span class="error-message" id="websiteError"></span>
//                 </div>
//             `
//         };
        
//         if (fieldTemplates[type]) {
//             specificFields.innerHTML = fieldTemplates[type];
            
//             // Style select elements
//             const selects = specificFields.querySelectorAll('select');
//             selects.forEach(select => {
//                 select.style.paddingLeft = '2.5rem';
//                 select.style.appearance = 'none';
//                 select.style.backgroundImage = 'url("data:image/svg+xml;charset=US-ASCII,<svg viewBox=\'0 0 4 5\' xmlns=\'http://www.w3.org/2000/svg\'><path fill=\'%23999\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/></svg>")';
//                 select.style.backgroundRepeat = 'no-repeat';
//                 select.style.backgroundPosition = 'right 1rem center';
//                 select.style.backgroundSize = '12px';
//             });
            
//             // Re-initialize validation for new fields
//             const newInputs = specificFields.querySelectorAll('input[required], select[required]');
//             newInputs.forEach(input => {
//                 input.addEventListener('blur', () => validateField(input));
//                 input.addEventListener('input', () => clearFieldError(input));
//             });
//         }
//     }
// }

// Form Validation
function initFormValidation() {
    const form = document.getElementById('signupForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    // Add real-time validation to all required inputs
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => clearFieldError(input));
    });
    
    // Email specific validation
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', validateEmail);
    
    // Phone specific validation
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', formatPhoneNumber);
    phoneInput.addEventListener('blur', validatePhone);
    
    // Password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    confirmPasswordInput.addEventListener('blur', validatePasswordConfirmation);
    
    // Terms acceptance validation
    const termsCheckbox = document.getElementById('termsAccepted');
    termsCheckbox.addEventListener('change', validateTerms);
    
    // Public validation function
    window.validateSignupForm = function() {
        let isValid = true;
        
        // Validate all required fields
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        // Validate email
        if (!validateEmail()) isValid = false;
        
        // Validate phone
        if (!validatePhone()) isValid = false;
        
        // Validate password confirmation
        if (!validatePasswordConfirmation()) isValid = false;
        
        // Validate terms
        if (!validateTerms()) isValid = false;
        
        return isValid;
    };
}

// Individual field validation functions
function validateField(input) {
    const value = input.value.trim();
    const fieldName = input.name;
    
    if (!value) {
        showFieldError(input, `${getFieldLabel(input)} is required`);
        return false;
    }
    
    // Specific validations
    if (input.type === 'url' && value) {
        if (!isValidURL(value)) {
            showFieldError(input, 'Please enter a valid URL');
            return false;
        }
    }
    
    showFieldSuccess(input);
    return true;
}

function validateEmail() {
    const emailInput = document.getElementById('email');
    const email = emailInput.value.trim();
    
    if (!email) {
        showFieldError(emailInput, 'Email address is required');
        return false;
    }
    
    if (!isValidEmail(email)) {
        showFieldError(emailInput, 'Please enter a valid email address');
        return false;
    }
    
    showFieldSuccess(emailInput);
    return true;
}

function validatePhone() {
    const phoneInput = document.getElementById('phone');
    const phone = phoneInput.value.trim();
    
    if (!phone) {
        showFieldError(phoneInput, 'Phone number is required');
        return false;
    }
    
    if (!isValidPhone(phone)) {
        showFieldError(phoneInput, 'Please enter a valid phone number');
        return false;
    }
    
    showFieldSuccess(phoneInput);
    return true;
}

function validatePasswordConfirmation() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (!confirmPassword) {
        showFieldError(confirmPasswordInput, 'Please confirm your password');
        return false;
    }
    
    if (password !== confirmPassword) {
        showFieldError(confirmPasswordInput, 'Passwords do not match');
        return false;
    }
    
    showFieldSuccess(confirmPasswordInput);
    return true;
}

function validateTerms() {
    const termsCheckbox = document.getElementById('termsAccepted');
    
    if (!termsCheckbox.checked) {
        showToast('Please accept the Terms of Service to continue', 'error');
        return false;
    }
    
    return true;
}

// Password Toggle Functionality
function initPasswordToggle() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
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
    });
}

// Password Strength Indicator
function initPasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('passwordStrength');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Remove all strength classes
        strengthIndicator.classList.remove('weak', 'medium', 'strong');
        
        // Add appropriate class
        if (password.length > 0) {
            strengthIndicator.classList.add(strength.level);
            strengthIndicator.querySelector('.strength-text').textContent = strength.text;
        } else {
            strengthIndicator.querySelector('.strength-text').textContent = 'Password strength';
        }
    });
}

function calculatePasswordStrength(password) {
    let score = 0;
    
    // Length check
    if (password.length >= 8) score += 1;
    if (password.length >= 12) score += 1;
    
    // Character variety checks
    if (/[a-z]/.test(password)) score += 1;
    if (/[A-Z]/.test(password)) score += 1;
    if (/[0-9]/.test(password)) score += 1;
    if (/[^A-Za-z0-9]/.test(password)) score += 1;
    
    if (score < 3) {
        return { level: 'weak', text: 'Weak password' };
    } else if (score < 5) {
        return { level: 'medium', text: 'Medium password' };
    } else {
        return { level: 'strong', text: 'Strong password' };
    }
}

// Phone Number Formatting
function formatPhoneNumber(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    // Sri Lankan phone number formatting
    if (value.startsWith('94')) {
        value = value.substring(2);
    }
    
    if (value.startsWith('0')) {
        value = value.substring(1);
    }
    
    if (value.length >= 2) {
        value = '+94 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 9);
    } else {
        value = '+94 ' + value;
    }
    
    e.target.value = value;
}

// Toast System (reuse from signin)
function initToastSystem() {
    window.showToast = function(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon');
        
        toastMessage.textContent = message;
        
        toast.className = `toast ${type}`;
        if (type === 'success') {
            toastIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        } else if (type === 'error') {
            toastIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
        }
        
        toast.classList.add('show');
        
        setTimeout(() => {
            hideToast();
        }, 5000);
    };
    
    window.hideToast = function() {
        const toast = document.getElementById('toast');
        toast.classList.remove('show');
    };
    
    const toastClose = document.getElementById('toastClose');
    if (toastClose) {
        toastClose.addEventListener('click', hideToast);
    }
}

// Form Submission
function initFormSubmission() {
    const form = document.getElementById('signupForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!window.validateSignupForm()) {
            showToast('Please fix the errors in the form', 'error');
            return;
        }
        
        setLoadingState(true);
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        try {
            const response = await submitSignup(data);
            
            if (response.success) {
                showToast('Account created successfully! Please check your email for verification.', 'success');
                
                setTimeout(() => {
                    window.location.href = response.redirectUrl || 'signin.php';
                }, 2000);
            } else {
                throw new Error(response.message || 'Registration failed');
            }
            
        } catch (error) {
            console.error('Signup error:', error);
            showToast(error.message || 'Registration failed. Please try again.', 'error');
            setLoadingState(false);
        }
    });
    
    function setLoadingState(loading) {
        const btn = document.getElementById('signupBtn');
        
        if (loading) {
            btn.classList.add('loading');
            btn.disabled = true;
        } else {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    }
}

// Simulate backend signup
async function submitSignup(data) {
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Simulate successful registration
    return {
        success: true,
        message: 'Account created successfully',
        redirectUrl: 'signin.php',
        user: {
            id: Date.now(),
            email: data.email,
            userType: data.userType
        }
    };
}

// Keyboard Shortcuts
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
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

function isValidPhone(phone) {
    const phoneRegex = /^\+94\s\d{2}\s\d{3}\s\d{4}$/;
    return phoneRegex.test(phone);
}

function isValidURL(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

function getFieldLabel(input) {
    const label = input.closest('.form-group').querySelector('label');
    return label ? label.textContent.replace('*', '').trim() : input.name;
}

function showFieldError(input, message) {
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    formGroup.classList.remove('success');
    formGroup.classList.add('error');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

function showFieldSuccess(input) {
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    formGroup.classList.remove('error');
    formGroup.classList.add('success');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

function clearFieldError(input) {
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    if (formGroup.classList.contains('error')) {
        formGroup.classList.remove('error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
}

// Console welcome message
console.log('%c🎓 UniPulse Registration', 'color: #FF6B35; font-size: 16px; font-weight: bold;');
console.log('%cSelect your user type to get started!', 'color: #4A5BCC; font-size: 12px;');