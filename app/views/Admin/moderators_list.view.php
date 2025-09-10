<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Moderator Management</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .moderator-management {
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
            color: blue;
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
        .moderator-table {
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
            background: #e3f2fd;
            color: #1976d2;
        }
        .btn-edit:hover {
            background: #bbdefb;
        }
        .btn-delete {
            background: #ffebee;
            color: #d32f2f;
        }
        .btn-delete:hover {
            background: #ffcdd2;
        }
        .btn-activate {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .btn-activate:hover {
            background: #c8e6c9;
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
        <section class="moderator-management">
            <div class="container">
                <div class="management-header">
                    <div>
                        <h1>Moderator Management</h1>
                        <p>Manage platform moderators and their permissions</p>
                    </div>
                    <a href="/unipulse/public/admin/moderator_create" class="btn-create">
                        <i class="fas fa-plus"></i>
                        Add New Moderator
                    </a>
                </div>

                <?php if (isset($message) && !empty($message)): ?>
                    <div class="message message-<?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="moderator-table">
                    <div class="table-header">
                        <h3>All Moderators</h3>
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
                                <?php if (isset($moderators) && !empty($moderators)): ?>
                                    <?php foreach ($moderators as $moderator): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($moderator->full_name); ?></td>
                                            <td><?php echo htmlspecialchars($moderator->email); ?></td>
                                            <td><?php echo htmlspecialchars($moderator->phone ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $moderator->is_active ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo $moderator->is_active ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($moderator->created_at)); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/unipulse/public/admin/moderators/edit/<?php echo $moderator->id; ?>" 
                                                       class="btn-action btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($moderator->is_active): ?>
                                                        <a href="/unipulse/public/admin/moderators/deactivate/<?php echo $moderator->id; ?>" 
                                                           class="btn-action btn-delete" title="Deactivate"
                                                           onclick="return confirm('Are you sure you want to deactivate this moderator?')">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="/unipulse/public/admin/moderators/activate/<?php echo $moderator->id; ?>" 
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
                                            No moderators found. <a href="/unipulse/public/admin/moderator_create">Add the first moderator</a>
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
