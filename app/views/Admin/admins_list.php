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
            background: var(--primary-color);
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
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .admin-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-header {
            background: var(--primary-color);
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
            color: var(--text-primary);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-active {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .status-inactive {
            background: #ffebee;
            color: #c62828;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .message-success {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .message-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
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
                <a href="/unipulse/public/admin/moderators">Moderators</a>
                <a href="/unipulse/public/admin/admins" class="active">Admins</a>
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
        <section class="admin-management">
            <div class="container">
                <div class="management-header">
                    <div>
                        <h1>Admin Management</h1>
                        <p>Manage system administrators</p>
                    </div>
                    <a href="/unipulse/public/admin/admins/create" class="btn-create">
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
                                    <th>Last Login</th>
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
                                            <td><?php echo $admin->last_login ? date('M j, Y H:i', strtotime($admin->last_login)) : 'Never'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem;">
                                            No administrators found. <a href="/unipulse/public/admin/admins/create">Add the first admin</a>
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
