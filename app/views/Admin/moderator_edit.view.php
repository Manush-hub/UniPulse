<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Edit Moderator</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-moderator {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .form-header {
            background: #4a90e2;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .form-content {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #4a90e2;
            color: white;
        }
        .btn-primary:hover {
            background: #357abd;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #ffcdd2;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .success-message {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #c8e6c9;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a90e2;
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .back-link:hover {
            color: #357abd;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
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
                    <div class="user-dropdown">
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="<?php echo htmlspecialchars($moderator->full_name); ?>" 
                                           required>
                                    <?php if (isset($errors['full_name'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($moderator->email); ?>" 
                                           required>
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
                                           value="<?php echo htmlspecialchars($moderator->phone ?? ''); ?>">
                                    <?php if (isset($errors['phone'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="university">University *</label>
                                    <select id="university" name="university" required>
                                        <option value="">Select University</option>
                                        <?php foreach ($universities as $key => $name): ?>
                                            <option value="<?php echo htmlspecialchars($key); ?>" 
                                                    <?php echo ($moderator->university === $key) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['university'])): ?>
                                        <div class="error-message"><?php echo htmlspecialchars($errors['university']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="university_name">University Name *</label>
                                <input type="text" 
                                       id="university_name" 
                                       name="university_name" 
                                       value="<?php echo htmlspecialchars($moderator->university_name ?? ''); ?>" 
                                       required>
                                <?php if (isset($errors['university_name'])): ?>
                                    <div class="error-message"><?php echo htmlspecialchars($errors['university_name']); ?></div>
                                <?php endif; ?>
                            </div>
                            
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
        });
    </script>
</body>
</html>