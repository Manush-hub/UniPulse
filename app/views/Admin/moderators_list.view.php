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
        .moderator-table {
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
    </header> -->

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
                                                           class="btn-action btn-delete" title="Delete"
                                                           onclick="return confirmModeratorDeletion(<?php echo $moderator->id; ?>, '<?php echo htmlspecialchars($moderator->full_name, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($moderator->university_name, ENT_QUOTES); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="/unipulse/public/admin/moderators/activate/<?php echo $moderator->id; ?>" 
                                                           class="btn-action btn-activate" title="Activate">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="/unipulse/public/admin/moderators/deactivate/<?php echo $moderator->id; ?>" 
                                                           class="btn-action btn-delete" title="Delete"
                                                           onclick="return confirmModeratorDeletion(<?php echo $moderator->id; ?>, '<?php echo htmlspecialchars($moderator->full_name, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($moderator->university_name, ENT_QUOTES); ?>')">
                                                            <i class="fas fa-trash"></i>
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

    <!-- Custom Modal for Delete Confirmation -->
    <div id="deleteModal" class="delete-modal" style="display: none;">
        <div class="modal-overlay" onclick="closeDeleteModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Moderator Deletion</h3>
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
                <button id="confirmDeleteBtn" onclick="proceedWithDeletion()" class="btn-confirm-delete">Delete Moderator</button>
            </div>
        </div>
    </div>

    <style>
        .delete-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            min-width: 400px;
            max-width: 600px;
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #dc2626;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .warning-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            color: #92400e;
        }
        
        .warning-box i {
            color: #f59e0b;
            margin-top: 0.125rem;
        }
        
        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .btn-cancel {
            padding: 0.75rem 1.5rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        
        .btn-confirm-delete {
            padding: 0.75rem 1.5rem;
            border: none;
            background: #dc2626;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-confirm-delete:hover {
            background: #b91c1c;
        }
        
        .btn-confirm-delete:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
    </style>

    <script>
        let currentDeleteUrl = '';
        
        async function confirmModeratorDeletion(moderatorId, moderatorName, universityName) {
            try {
                // Check for pending approvals
                const response = await fetch(`/unipulse/public/admin/moderators/check_pending/${moderatorId}`);
                const data = await response.json();
                
                const modal = document.getElementById('deleteModal');
                const message = document.getElementById('modalMessage');
                const warningBox = document.getElementById('pendingWarning');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                
                message.innerHTML = `Are you sure you want to delete moderator <strong>${moderatorName}</strong> from <strong>${universityName}</strong>?<br><br>This action cannot be undone.`;
                
                if (data.hasPending && data.pendingCount > 0) {
                    warningBox.style.display = 'flex';
                    warningBox.innerHTML = `<i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Warning:</strong> This moderator has <strong>${data.pendingCount}</strong> pending publisher approval(s) 
                            for ${universityName}. These approvals need to be resolved first.
                        </div>`;
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Cannot Delete';
                    currentDeleteUrl = '';
                } else {
                    warningBox.style.display = 'none';
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Delete Moderator';
                    currentDeleteUrl = `/unipulse/public/admin/moderators/deactivate/${moderatorId}`;
                }
                
                modal.style.display = 'block';
                return false; // Prevent default action
            } catch (error) {
                console.error('Error checking pending approvals:', error);
                // Fallback to simple confirmation with server-side check
                const message = `Are you sure you want to delete moderator ${moderatorName}?\n\n` +
                              `Note: If this moderator has pending approvals, the deletion will be blocked by the server.\n\n` +
                              `This action cannot be undone.`;
                return confirm(message);
            }
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        function proceedWithDeletion() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl;
            }
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
