<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Create Moderator</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .create-moderator {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        select.form-control {
            background-color: white;
            cursor: pointer;
        }
        .form-error {
            color: #d32f2f;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .permission-group {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .permission-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .permission-item input {
            margin-right: 0.5rem;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }
        .btn-primary {
            background: var(--primary-color);
            color: blue;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .required-asterisk {
            color: #dc2626;
            margin-left: 3px;
        }
        .form-control.error {
            border-color: #dc2626;
            background-color: #fef2f2;
        }
        .validation-error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .validation-error.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <!-- <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/admin/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/admin/dashboard">Dashboard</a>
                <a href="/unipulse/public/admin/moderators" class="active">Moderators</a>
                <a href="/unipulse/public/admin/admins">Admins</a>
            </nav>
            <div class="header-actions">
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/admin.png" alt="Admin" class="admin-avatar">
                    <div class="user-info">
                        <span class="username"><?php echo htmlspecialchars($user['name']); ?></span>
                        <span class="user-role">System Administrator</span>
                    </div>
                </div>
            </div>
        </div>
    </header> -->

    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'moderator_create'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="create-moderator">
            <div class="container">
                <div class="form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-user-shield"></i> Create New Moderator</h1>
                        <p>Add a new moderator to help manage the platform</p>
                    </div>

                    <form method="POST" action="/unipulse/public/admin/moderator_create">
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="required-asterisk">*</span></label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['full_name']) ? htmlspecialchars($old_data['full_name']) : ''; ?>" 
                                   required>
                            <div class="validation-error" id="full_name_error"></div>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="form-error"><?php echo $errors['full_name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required-asterisk">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>" 
                                   required>
                            <div class="validation-error" id="email_error"></div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="form-error"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number (Optional)</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   placeholder="e.g., 0771234567 or +94771234567"
                                   value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>">
                            <div class="validation-error" id="phone_error"></div>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="form-error"><?php echo $errors['phone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="university">University <span class="required-asterisk">*</span></label>
                            <select id="university" 
                                    name="university" 
                                    class="form-control" 
                                    required>
                                <option value="">Select University</option>
                                <!-- State Universities -->
                                <optgroup label="State Universities">
                                    <option value="university-of-colombo" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-colombo') ? 'selected' : '' ?>>University of Colombo</option>
                                    <option value="university-of-peradeniya" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-peradeniya') ? 'selected' : '' ?>>University of Peradeniya</option>
                                    <option value="university-of-sri-jayewardenepura" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-sri-jayewardenepura') ? 'selected' : '' ?>>University of Sri Jayewardenepura</option>
                                    <option value="university-of-kelaniya" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-kelaniya') ? 'selected' : '' ?>>University of Kelaniya</option>
                                    <option value="university-of-moratuwa" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-moratuwa') ? 'selected' : '' ?>>University of Moratuwa</option>
                                    <option value="university-of-jaffna" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-jaffna') ? 'selected' : '' ?>>University of Jaffna</option>
                                    <option value="university-of-ruhuna" <?= (isset($old_data['university']) && $old_data['university'] === 'university-of-ruhuna') ? 'selected' : '' ?>>University of Ruhuna</option>
                                    <option value="eastern-university" <?= (isset($old_data['university']) && $old_data['university'] === 'eastern-university') ? 'selected' : '' ?>>Eastern University, Sri Lanka</option>
                                    <option value="south-eastern-university" <?= (isset($old_data['university']) && $old_data['university'] === 'south-eastern-university') ? 'selected' : '' ?>>South Eastern University of Sri Lanka</option>
                                    <option value="rajarata-university" <?= (isset($old_data['university']) && $old_data['university'] === 'rajarata-university') ? 'selected' : '' ?>>Rajarata University of Sri Lanka</option>
                                    <option value="sabaragamuwa-university" <?= (isset($old_data['university']) && $old_data['university'] === 'sabaragamuwa-university') ? 'selected' : '' ?>>Sabaragamuwa University of Sri Lanka</option>
                                    <option value="wayamba-university" <?= (isset($old_data['university']) && $old_data['university'] === 'wayamba-university') ? 'selected' : '' ?>>Wayamba University of Sri Lanka</option>
                                    <option value="uva-wellassa-university" <?= (isset($old_data['university']) && $old_data['university'] === 'uva-wellassa-university') ? 'selected' : '' ?>>Uva Wellassa University</option>
                                    <option value="open-university" <?= (isset($old_data['university']) && $old_data['university'] === 'open-university') ? 'selected' : '' ?>>Open University of Sri Lanka</option>
                                    <option value="buddhist-and-pali-university" <?= (isset($old_data['university']) && $old_data['university'] === 'buddhist-and-pali-university') ? 'selected' : '' ?>>Buddhist and Pali University of Sri Lanka</option>
                                </optgroup>
                                <!-- Private Universities -->
                                <optgroup label="Private Universities">
                                    <option value="sliit" <?= (isset($old_data['university']) && $old_data['university'] === 'sliit') ? 'selected' : '' ?>>Sri Lanka Institute of Information Technology (SLIIT)</option>
                                    <option value="nsbm" <?= (isset($old_data['university']) && $old_data['university'] === 'nsbm') ? 'selected' : '' ?>>NSBM Green University</option>
                                    <option value="cinec" <?= (isset($old_data['university']) && $old_data['university'] === 'cinec') ? 'selected' : '' ?>>CINEC Campus</option>
                                    <option value="apiit" <?= (isset($old_data['university']) && $old_data['university'] === 'apiit') ? 'selected' : '' ?>>Asia Pacific Institute of Information Technology (APIIT)</option>
                                    <option value="metropolitan-campus" <?= (isset($old_data['university']) && $old_data['university'] === 'metropolitan-campus') ? 'selected' : '' ?>>KIU (Kaatsu International University)</option>
                                </optgroup>
                            </select>
                            <div class="validation-error" id="university_error"></div>
                            <?php if (isset($errors['university'])): ?>
                                <div class="form-error"><?php echo $errors['university']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="required-asterisk">*</span></label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required>
                            <div class="validation-error" id="password_error"></div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="form-error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="permission-group">
                                <div class="permission-item">
                                    <input type="checkbox" id="view_events" name="permissions[view_events]" value="1" checked>
                                    <label for="view_events">View Events</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="edit_events" name="permissions[edit_events]" value="1" checked>
                                    <label for="edit_events">Edit Events</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="view_users" name="permissions[view_users]" value="1" checked>
                                    <label for="view_users">View Users</label>
                                </div>
                                <div class="permission-item">
                                    <input type="checkbox" id="moderate_content" name="permissions[moderate_content]" value="1" checked>
                                    <label for="moderate_content">Moderate Content</label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field to store university name -->
                        <input type="hidden" id="university_name" name="university_name" value="">

                        <?php if (isset($errors['general'])): ?>
                            <div class="form-error" style="margin-bottom: 1rem;"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Moderator
                            </button>
                            <a href="/unipulse/public/admin/moderators_list" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
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

        // Initialize form validation on page load
        document.addEventListener('DOMContentLoaded', function() {
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
            
            document.getElementById('email').addEventListener('blur', function() {
                validateField('email', this.value, {
                    required: true,
                    email: true,
                    requiredMessage: 'Email address is required'
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
            
            document.getElementById('password').addEventListener('blur', function() {
                validateField('password', this.value, {
                    required: true,
                    minLength: 6,
                    requiredMessage: 'Password is required',
                    minLengthMessage: 'Password must be at least 6 characters'
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
                
                if (!validateField('email', document.getElementById('email').value, {
                    required: true,
                    email: true,
                    requiredMessage: 'Email address is required'
                })) {
                    isValid = false;
                }
                
                if (!validateField('university', document.getElementById('university').value, {
                    required: true,
                    requiredMessage: 'Please select a university'
                })) {
                    isValid = false;
                }
                
                if (!validateField('password', document.getElementById('password').value, {
                    required: true,
                    minLength: 6,
                    requiredMessage: 'Password is required',
                    minLengthMessage: 'Password must be at least 6 characters'
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
    </script>
</body>
</html>
