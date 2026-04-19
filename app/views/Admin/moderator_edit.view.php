<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Edit Moderator</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/moderator-edit-style.css">
</head>
<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'moderator_edit'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="edit-moderator">
            <div class="container">
                <a href="/unipulse/public/admin/moderators" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Moderators
                </a>
                
                <div class="form-container">
                    <div class="form-header">
                        <h1>Edit Moderator</h1>
                        <p>Update moderator information and permissions</p>
                    </div>
                    
                    <div class="form-content">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="error-message">
                                <ul style="margin: 0; padding-left: 1rem;">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($message) && !empty($message)): ?>
                            <div class="message message-<?php echo $message_type ?? 'success'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name">Full Name <span class="required-asterisk">*</span></label>
                                    <input type="text" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="<?php echo htmlspecialchars($moderator->full_name); ?>" 
                                           required>
                                    <div class="validation-error" id="full_name_error"></div>
                                    <?php if (isset($errors['full_name'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address <span class="required-asterisk">*</span></label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($moderator->email); ?>" 
                                           readonly>
                                    <div class="readonly-info">
                                        <i class="fas fa-info-circle"></i> Email cannot be changed after creation
                                    </div>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="e.g., 0771234567 or +94771234567"
                                           value="<?php echo htmlspecialchars($moderator->phone ?? ''); ?>">
                                    <div class="validation-error" id="phone_error"></div>
                                    <?php if (isset($errors['phone'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="university">University <span class="required-asterisk">*</span></label>
                                    <select id="university" name="university" required>
                                        <option value="">Select University</option>
                                        <?php foreach ($universities as $key => $name): ?>
                                            <option value="<?php echo htmlspecialchars($key); ?>" 
                                                    <?php echo ($moderator->university === $key) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="validation-error" id="university_error"></div>
                                    <?php if (isset($errors['university'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['university']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Hidden field to store university name -->
                            <input type="hidden" id="university_name" name="university_name" value="<?php echo htmlspecialchars($moderator->university_name ?? ''); ?>">
                            
                            <div class="form-group">
                                <label for="password">New Password (leave blank to keep current)</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter new password if you want to change it">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="error-message"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <select id="is_active" name="is_active">
                                    <option value="1" <?php echo ($moderator->is_active == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($moderator->is_active == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="btn-container">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Moderator
                                </button>
                                <a href="/unipulse/public/admin/moderators" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Admin/moderator-edit-app.js"></script>
</body>
</html>