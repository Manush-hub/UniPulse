<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Create Admin</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Admin_admin_create.css">
</head>
<body>
    <!-- Header -->
    <?php $pageConfig = ['activeNav' => 'dashboard']; ?>
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="create-admin">
            <div class="container">
                <div class="form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-users-cog"></i> Create New Administrator</h1>
                        <p>Add a new system administrator</p>
                    </div>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle warning-icon"></i>
                        <strong>Warning:</strong> Administrators have full system access. Only create admin accounts for trusted individuals.
                    </div>

                    <form method="POST" action="/unipulse/public/admin/admins/create">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
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
                            <label for="email">Email Address</label>
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
                            <label for="phone">Phone Number (Optional)</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="form-error"><?php echo $errors['phone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required>
                            <small style="color: #666;">Password must be at least 6 characters long</small>
                            <?php if (isset($errors['password'])): ?>
                                <div class="form-error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($errors['general'])): ?>
                            <div class="form-error" style="margin-bottom: 1rem;"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Administrator
                            </button>
                            <a href="/unipulse/public/admin/admins" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
