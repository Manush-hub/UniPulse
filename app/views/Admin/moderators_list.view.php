<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Moderator Management</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/moderators-list-style.css">
</head>
<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'moderators_list'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <section class="moderator-management">
            <div class="container">
                <a href="/unipulse/public/admin/dashboard" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                
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
                                    <th>University</th>
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
                                            <td><?php echo htmlspecialchars($moderator->university_name ?? 'N/A'); ?></td>
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
                                                           onclick="return confirmModeratorDeactivation(<?php echo $moderator->id; ?>, '<?php echo htmlspecialchars($moderator->full_name, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($moderator->university_name, ENT_QUOTES); ?>')">
                                                            <i class="fas fa-user-slash"></i>
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

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Custom Modal for Deactivate Confirmation -->
    <div id="deleteModal" class="delete-modal" style="display: none;">
        <div class="modal-overlay" onclick="closeDeleteModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Moderator Deactivation</h3>
                <button onclick="closeDeleteModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <div id="pendingWarning" class="warning-box" style="display: none;">
                    <i class="fas fa-warning"></i>
                    <strong>Warning:</strong> This moderator has pending publisher approvals that need to be resolved first.
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeDeleteModal()" class="btn-cancel">Cancel</button>
                <button id="confirmDeleteBtn" onclick="proceedWithDeletion()" class="btn-confirm-delete">Deactivate Moderator</button>
            </div>
        </div>
    </div>

    

    <script src="/unipulse/public/assets/js/Admin/moderators-list-app.js"></script>
</body>
</html>
