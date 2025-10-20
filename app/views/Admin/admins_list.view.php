<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Admin Management</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-management {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        .management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .btn-create {
            background: #1e3a8a;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-create:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }
        .admin-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-header {
            background: #1e3a8a;
            color: white;
            padding: 1rem;
        }
        .table-content table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-content th,
        .table-content td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .table-content th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-active {
            background: #dcfce7;
            color: #16a34a;
        }
        .status-inactive {
            background: #fee2e2;
            color: #dc2626;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-action {
            padding: 0.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .btn-edit {
            background: #dbeafe;
            color: #1e3a8a;
        }
        .btn-edit:hover {
            background: #bfdbfe;
        }
        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }
        .btn-delete:hover {
            background: #fecaca;
        }
        .btn-activate {
            background: #dcfce7;
            color: #16a34a;
        }
        .btn-activate:hover {
            background: #bbf7d0;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .message-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .message-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #1e3a8a;
            text-decoration: none;
            margin-bottom: 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
        }
        .back-link:hover {
            color: #1e40af;
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
                                        <tr>
                                            <td><?php echo htmlspecialchars($admin->full_name); ?></td>
                                            <td><?php echo htmlspecialchars($admin->email); ?></td>
                                            <td><?php echo htmlspecialchars($admin->phone ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $admin->is_active ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo $admin->is_active ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($admin->created_at)); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/unipulse/public/admin/admin_edit/<?php echo $admin->id; ?>" 
                                                       class="btn-action btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($admin->is_active): ?>
                                                        <a href="/unipulse/public/admin/admins_list/deactivate/<?php echo $admin->id; ?>" 
                                                           class="btn-action btn-delete" title="Deactivate"
                                                           onclick="return confirm('Are you sure you want to deactivate this admin?')">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="/unipulse/public/admin/admins_list/activate/<?php echo $admin->id; ?>" 
                                                           class="btn-action btn-activate" title="Activate">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="/unipulse/public/admin/admins_list/delete/<?php echo $admin->id; ?>" 
                                                       class="btn-action btn-delete" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
</body>
</html>
