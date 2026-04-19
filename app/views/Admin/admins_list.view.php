<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Admin Management</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/admins-list-style.css">
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
                    <div class="user-dropdown">
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header> -->

    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'admins_list'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="admin-management">
            <div class="container">
                <a href="/unipulse/public/admin/dashboard" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                
                <div class="management-header">
                    <div>
                        <h1>Admin Management</h1>
                        <p>Manage admin accounts and their permissions</p>
                    </div>
                    <a href="/unipulse/public/admin/admin_create" class="btn-create">
                        <i class="fas fa-plus"></i>
                        Add New Admin
                    </a>
                </div>

                <?php if (isset($message) && !empty($message)): ?>
                    <div class="message message-<?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="admin-table">
                    <div class="table-header">
                        <h3>All Administrators</h3>
                    </div>
                    <div class="table-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($admins) && !empty($admins)): ?>
                                    <?php foreach ($admins as $admin): ?>
                                        <?php $isPermanentSystemAdmin = isset($system_admin_id) && ((int)$admin->id === (int)$system_admin_id); ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($admin->full_name); ?></td>
                                            <td><?php echo htmlspecialchars($admin->email); ?></td>
                                            <td><?php echo htmlspecialchars($admin->phone ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $admin->is_active ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php if ($isPermanentSystemAdmin): ?>
                                                        System Administrator
                                                    <?php else: ?>
                                                        <?php echo $admin->is_active ? 'Active' : 'Inactive'; ?>
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($admin->created_at)); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/unipulse/public/admin/admin_edit/<?php echo $admin->id; ?>" 
                                                       class="btn-action btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if (!$isPermanentSystemAdmin && $admin->is_active): ?>
                                                        <a href="/unipulse/public/admin/admins_list/deactivate/<?php echo $admin->id; ?>" 
                                                           class="btn-action btn-delete" title="Deactivate"
                                                           onclick="return confirm('Are you sure you want to deactivate this admin?')">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    <?php elseif (!$isPermanentSystemAdmin): ?>
                                                        <a href="/unipulse/public/admin/admins_list/activate/<?php echo $admin->id; ?>" 
                                                           class="btn-action btn-activate" title="Activate">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem;">
                                            No admins found. <a href="/unipulse/public/admin/admin_create">Add the first admin</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
