// Auto-populate university name based on selection
        document.getElementById('university').addEventListener('change', function() {
            const universitySelect = this;
            const universityNameInput = document.getElementById('university_name');
            const selectedOption = universitySelect.options[universitySelect.selectedIndex];
            
            if (selectedOption.value) {
                universityNameInput.value = selectedOption.text;
            } else {
                universityNameInput.value = '';
            }
            
            // Clear validation error when selection changes
            clearValidationError('university');
        });

        // Initialize university name on page load
        document.addEventListener('DOMContentLoaded', function() {
            const universitySelect = document.getElementById('university');
            const universityNameInput = document.getElementById('university_name');
            const selectedOption = universitySelect.options[universitySelect.selectedIndex];
            
            if (selectedOption.value && !universityNameInput.value) {
                universityNameInput.value = selectedOption.text;
            }
            
            // Initialize form validation
            initFormValidation();
        });

        // Form validation functions
        function showValidationError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');
            
            field.classList.add('error');
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }

        function clearValidationError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');
            
            field.classList.remove('error');
            errorDiv.textContent = '';
            errorDiv.classList.remove('show');
        }

        function validateField(fieldId, value, rules) {
            // Clear previous error
            clearValidationError(fieldId);
            
            // Check required fields
            if (rules.required && (!value || value.trim() === '')) {
                showValidationError(fieldId, rules.requiredMessage || 'This field is required');
                return false;
            }
            
            // Check minimum length
            if (rules.minLength && value && value.trim().length < rules.minLength) {
                showValidationError(fieldId, rules.minLengthMessage || `Minimum ${rules.minLength} characters required`);
                return false;
            }
            
            // Check email format
            if (rules.email && value && value.trim() !== '') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showValidationError(fieldId, 'Please enter a valid email address');
                    return false;
                }
            }
            
            // Check phone format
            if (rules.phone && value && value.trim() !== '') {
                // Sri Lankan phone number validation
                // Supports formats: 0771234567, +94771234567, +94 77 123 4567, 077-123-4567, etc.
                const phoneRegex = /^(\+94|0)?[0-9\s\-\(\)]{9,15}$/;
                const cleanPhone = value.replace(/[\s\-\(\)]/g, ''); // Remove spaces, dashes, parentheses
                
                // Check if it's a valid Sri Lankan number
                const sriLankanRegex = /^(\+94|0)?(7[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]|6[0-9]|8[0-9]|9[0-9])[0-9]{7}$/;
                
                if (!phoneRegex.test(value) || !sriLankanRegex.test(cleanPhone)) {
                    showValidationError(fieldId, 'Please enter a valid Sri Lankan phone number (e.g., 0771234567 or +94771234567)');
                    return false;
                }
            }
            
            return true;
        }

        function initFormValidation() {
            const form = document.querySelector('form');
            
            // Real-time validation on blur
            document.getElementById('full_name').addEventListener('blur', function() {
                validateField('full_name', this.value, {
                    required: true,
                    minLength: 2,
                    requiredMessage: 'Full name is required',
                    minLengthMessage: 'Full name must be at least 2 characters'
                });
            });
            
            document.getElementById('phone').addEventListener('blur', function() {
                validateField('phone', this.value, {
                    phone: true
                });
            });
            
            document.getElementById('university').addEventListener('change', function() {
                validateField('university', this.value, {
                    required: true,
                    requiredMessage: 'Please select a university'
                });
            });
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validate all required fields
                if (!validateField('full_name', document.getElementById('full_name').value, {
                    required: true,
                    minLength: 2,
                    requiredMessage: 'Full name is required',
                    minLengthMessage: 'Full name must be at least 2 characters'
                })) {
                    isValid = false;
                }
                
                if (!validateField('university', document.getElementById('university').value, {
                    required: true,
                    requiredMessage: 'Please select a university'
                })) {
                    isValid = false;
                }
                
                // Validate optional phone if provided
                const phoneValue = document.getElementById('phone').value;
                if (phoneValue && !validateField('phone', phoneValue, { phone: true })) {
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.validation-error.show');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }