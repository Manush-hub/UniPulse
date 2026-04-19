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
    <?php $pageConfig = ['activeNav' => 'dashboard']; ?>
    <?php include __DIR__ . '/components/header.php'; ?>

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
