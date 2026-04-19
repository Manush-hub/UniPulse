<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Create Admin</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/admin-create-style.css">
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
                <a href="/unipulse/public/admin/moderators_list">Moderators</a>
                <a href="/unipulse/public/admin/admins_list" class="active">Admins</a>
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
    $pageConfig = ['activeNav' => 'admin_create'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="create-admin">
            <div class="container">
                <a href="/unipulse/public/admin/admins_list" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Admins
                </a>
                
                <div class="form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-users-cog"></i> Create New Admin</h1>
                        <p>Add a new administrator to manage the platform</p>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                        <div class="form-error" style="background: #ffebee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <?php echo $errors['general']; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/unipulse/public/admin/admin_create">
                        <div class="form-group">
                            <label for="full_name">Full Name <span style="color: red;">*</span></label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['full_name']) ? htmlspecialchars($old_data['full_name']) : ''; ?>" 
                                   required>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="form-error"><?php echo $errors['full_name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span style="color: red;">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>" 
                                   required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="form-error"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>" 
                                   placeholder="+1 234 567 8900">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="form-error"><?php echo $errors['phone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span style="color: red;">*</span></label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required
                                   minlength="6">
                            <?php if (isset($errors['password'])): ?>
                                <div class="form-error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                            <small style="color: #666; font-size: 0.875rem;">Minimum 6 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password <span style="color: red;">*</span></label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-control" 
                                   required
                                   minlength="6">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="form-error"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <a href="/unipulse/public/admin/admins_list" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Create Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Admin/admin-create-app.js"></script>
</body>
</html>
