// Multi-Step Registration App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initMultiStepForm();
    initFormValidation();
    initPasswordFeatures();
    initFileUpload();
    initVerificationMethod();
    initFormSubmission();
});

// Global variables
let currentStep = 1;
let totalSteps = 2; // Default for public users
let userCategory = 'public';

// Initialize Multi-Step Form
function initMultiStepForm() {
    // Get user category from hidden input
    const userCategoryInput = document.getElementById('userCategory');
    if (userCategoryInput) {
        userCategory = userCategoryInput.value;
        totalSteps = userCategory === 'university' ? 3 : 2;
    }

    // Show first step
    showStep(1);
    updateStepProgress();
    
    // Set role from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role') || localStorage.getItem('selectedRole') || 'student';
    
    const userRoleInput = document.getElementById('userRole');
    if (userRoleInput) {
        userRoleInput.value = role;
    }
    
    // Update form title based on role and category
    updateFormTitle(role, userCategory);
}

// Show specific step
function showStep(step) {
    // Hide all steps
    const steps = document.querySelectorAll('.form-step');
    steps.forEach(stepEl => {
        stepEl.classList.remove('active');
    });
    
    // Show current step
    const currentStepEl = document.getElementById(`step${step}`);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
    }
    
    currentStep = step;
    updateStepProgress();
}

// Update step progress indicator
function updateStepProgress() {
    const stepElements = document.querySelectorAll('.step');
    
    stepElements.forEach((stepEl, index) => {
        const stepNumber = index + 1;
        stepEl.classList.remove('active', 'completed');
        
        if (stepNumber < currentStep) {
            stepEl.classList.add('completed');
        } else if (stepNumber === currentStep) {
            stepEl.classList.add('active');
        }
    });
}

// Next step function
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    }
}

// Previous step function
function previousStep() {
    if (currentStep > 1) {
        showStep(currentStep - 1);
    }
}

// Validate current step
function validateCurrentStep() {
    const currentStepEl = document.getElementById(`step${currentStep}`);
    if (!currentStepEl) return false;
    
    const requiredInputs = currentStepEl.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Additional validation for step 1 (password confirmation)
    if (currentStep === 1) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        
        if (password && confirmPassword) {
            if (password.value !== confirmPassword.value) {
                showFieldError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }
        }
    }
    
    return isValid;
}

// Update form title
function updateFormTitle(role, category) {
    const formTitle = document.getElementById('formTitle');
    const formSubtitle = document.getElementById('formSubtitle');
    
    if (!formTitle || !formSubtitle) return;
    
    const roleCapitalized = role.charAt(0).toUpperCase() + role.slice(1);
    const categoryText = category === 'university' ? 'University' : 'Public';
    
    formTitle.textContent = `Create Your ${roleCapitalized} Account`;
    formSubtitle.textContent = `Join the UniPulse community as a ${categoryText} ${roleCapitalized}`;
}

// Form Validation
function initFormValidation() {
    const inputs = document.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => clearFieldError(input));
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', validateEmail);
    }
    
    // Phone validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', validatePhone);
    }
}

// Individual field validation
function validateField(input) {
    const value = input.value.trim();
    
    if (!value && input.hasAttribute('required')) {
        showFieldError(input, `${getFieldLabel(input)} is required`);
        return false;
    }
    
    // Specific validations
    if (input.type === 'email' && value) {
        if (!isValidEmail(value)) {
            showFieldError(input, 'Please enter a valid email address');
            return false;
        }
        
        // University email validation for university users
        if (userCategory === 'university' && !isUniversityEmail(value)) {
            showFieldError(input, 'Please use your university email address for automatic verification');
            return false;
        }
    }
    
    if (input.type === 'tel' && value) {
        if (!isValidPhone(value)) {
            showFieldError(input, 'Please enter a valid phone number');
            return false;
        }
    }
    
    showFieldSuccess(input);
    return true;
}

// Email validation
function validateEmail() {
    const emailInput = document.getElementById('email');
    if (!emailInput) return;
    
    const email = emailInput.value.trim();
    
    if (!email) return;
    
    if (!isValidEmail(email)) {
        showFieldError(emailInput, 'Please enter a valid email address');
        return;
    }
    
    if (userCategory === 'university' && !isUniversityEmail(email)) {
        showFieldError(emailInput, 'Please use your university email address for automatic verification');
        return;
    }
    
    showFieldSuccess(emailInput);
}

// Phone validation
function validatePhone() {
    const phoneInput = document.getElementById('phone');
    if (!phoneInput) return;
    
    const phone = phoneInput.value.trim();
    
    if (!phone) return;
    
    if (!isValidPhone(phone)) {
        showFieldError(phoneInput, 'Please enter a valid phone number');
        return;
    }
    
    showFieldSuccess(phoneInput);
}

// Password Features
function initPasswordFeatures() {
    // Password toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Password strength
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', updatePasswordStrength);
    }
}

// Update password strength
function updatePasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (!passwordInput || !strengthIndicator) return;
    
    const password = passwordInput.value;
    const strength = calculatePasswordStrength(password);
    
    strengthIndicator.className = `password-strength ${strength.level}`;
    strengthIndicator.querySelector('.strength-text').textContent = strength.text;
}

// Calculate password strength
function calculatePasswordStrength(password) {
    if (password.length === 0) {
        return { level: '', text: 'Password strength' };
    }
    
    let score = 0;
    
    // Length
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    
    // Character types
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    if (score < 3) {
        return { level: 'weak', text: 'Weak password' };
    } else if (score < 5) {
        return { level: 'medium', text: 'Medium strength' };
    } else {
        return { level: 'strong', text: 'Strong password' };
    }
}

// File Upload
function initFileUpload() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('idDocument');
    
    if (!fileUploadArea || !fileInput) return;
    
    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = '#4A5BCC';
        fileUploadArea.style.backgroundColor = '#f8f9ff';
    });
    
    fileUploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = '#e1e5e9';
        fileUploadArea.style.backgroundColor = 'transparent';
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileUploadDisplay(files[0]);
        }
        fileUploadArea.style.borderColor = '#e1e5e9';
        fileUploadArea.style.backgroundColor = 'transparent';
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            updateFileUploadDisplay(e.target.files[0]);
        }
    });
}

// Update file upload display
function updateFileUploadDisplay(file) {
    const fileUploadArea = document.getElementById('fileUploadArea');
    if (!fileUploadArea) return;
    
    const uploadContent = fileUploadArea.querySelector('.upload-content');
    uploadContent.innerHTML = `
        <i class="fas fa-file-alt"></i>
        <p>${file.name}</p>
        <span>File selected (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
    `;
}

// Verification Method
function initVerificationMethod() {
    const verificationRadios = document.querySelectorAll('input[name="verificationMethod"]');
    const documentUploadSection = document.getElementById('documentUploadSection');
    
    verificationRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (documentUploadSection) {
                if (this.value === 'document-upload') {
                    documentUploadSection.style.display = 'block';
                } else {
                    documentUploadSection.style.display = 'none';
                }
            }
        });
    });
}

// Form Submission
function initFormSubmission() {
    const form = document.getElementById('registrationForm');
    
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateAllSteps()) {
            showToast('Please fix the errors in the form', 'error');
            return;
        }
        
        const termsCheckbox = document.getElementById('termsAccepted');
        if (!termsCheckbox || !termsCheckbox.checked) {
            showToast('Please accept the Terms of Service and Privacy Policy', 'error');
            return;
        }
        
        showLoadingOverlay(true);
        
        const formData = new FormData(form);
        
        // Add interest tags for public users
        const interests = [];
        const interestCheckboxes = document.querySelectorAll('input[name="interests[]"]:checked');
        interestCheckboxes.forEach(checkbox => {
            interests.push(checkbox.value);
        });
        
        if (interests.length > 0) {
            formData.set('interests', interests.join(','));
        }
        
        try {
            const response = await submitRegistration(formData);
            
            if (response.success) {
                showToast('Account created successfully! Please check your email for verification.', 'success');
                
                setTimeout(() => {
                    window.location.href = response.redirectUrl || '/signin';
                }, 2000);
            } else {
                throw new Error(response.message || 'Registration failed');
            }
            
        } catch (error) {
            console.error('Registration error:', error);
            showToast(error.message || 'Registration failed. Please try again.', 'error');
            showLoadingOverlay(false);
        }
    });
}

// Validate all steps
function validateAllSteps() {
    let isValid = true;
    
    for (let step = 1; step <= totalSteps; step++) {
        const stepEl = document.getElementById(`step${step}`);
        if (!stepEl) continue;
        
        const requiredInputs = stepEl.querySelectorAll('input[required], select[required]');
        requiredInputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
    }
    
    return isValid;
}

// Submit registration (simulate backend call)
async function submitRegistration(formData) {
    // Simulate API call
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                success: true,
                message: 'Account created successfully',
                redirectUrl: '/signin'
            });
        }, 2000);
    });
}

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isUniversityEmail(email) {
    const universityDomains = [
        'cmb.ac.lk', 'pdn.ac.lk', 'mrt.ac.lk', 'kln.ac.lk', 'jfn.ac.lk',
        'ruh.ac.lk', 'uwu.ac.lk', 'sab.ac.lk', 'wyb.ac.lk', 'rjt.ac.lk'
    ];
    
    const domain = email.split('@')[1];
    return universityDomains.some(uniDomain => domain && domain.toLowerCase().includes(uniDomain));
}

function isValidPhone(phone) {
    const phoneRegex = /^(\+94|0)?[1-9]\d{8}$/;
    return phoneRegex.test(phone.replace(/\s+/g, ''));
}

function getFieldLabel(input) {
    const label = input.parentElement.parentElement.querySelector('label');
    return label ? label.textContent.replace('*', '').trim() : 'Field';
}

function showFieldError(input, message) {
    clearFieldError(input);
    
    const errorElement = input.parentElement.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    input.style.borderColor = '#dc3545';
    input.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.1)';
}

function showFieldSuccess(input) {
    clearFieldError(input);
    input.style.borderColor = '#28a745';
    input.style.boxShadow = '0 0 0 3px rgba(40, 167, 69, 0.1)';
}

function clearFieldError(input) {
    const errorElement = input.parentElement.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
    
    input.style.borderColor = '#e1e5e9';
    input.style.boxShadow = 'none';
}

function showLoadingOverlay(show) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = show ? 'flex' : 'none';
    }
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    const toastMessage = toast.querySelector('.toast-message');
    const toastIcon = toast.querySelector('.toast-content i');
    
    if (toastMessage) {
        toastMessage.textContent = message;
    }
    
    if (toastIcon) {
        toastIcon.className = type === 'error' 
            ? 'fas fa-exclamation-circle' 
            : 'fas fa-check-circle';
        toastIcon.style.color = type === 'error' ? '#dc3545' : '#28a745';
    }
    
    toast.style.display = 'flex';
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 5000);
}

// Toast close button
document.addEventListener('click', function(e) {
    if (e.target.closest('.toast-close')) {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.style.display = 'none';
            }, 300);
        }
    }
});

// Global functions for button onclick events
window.nextStep = nextStep;
window.previousStep = previousStep;

// Console welcome message
console.log('%c🎓 UniPulse Multi-Step Registration', 'color: #FF6B35; font-size: 16px; font-weight: bold;');
console.log('%cCompleting your registration...', 'color: #4A5BCC; font-size: 12px;');