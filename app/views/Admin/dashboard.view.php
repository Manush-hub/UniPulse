<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Admin Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/suspension-system.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>
    

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername"><?php echo isset($user['name']) ? htmlspecialchars(explode(' ', $user['name'])[0]) : 'Admin'; ?></span>! 👋</h1>
                        <p>Monitor system performance and manage platform operations from your admin dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalUsers">2,847</span>
                                <span class="stat-label">Total Users</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalModerators"><?php echo isset($stats['total_moderators']) ? $stats['total_moderators'] : 0; ?></span>
                                <span class="stat-label">Total Moderators</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalAdmins"><?php echo isset($stats['total_admins']) ? $stats['total_admins'] : 1; ?></span>
                                <span class="stat-label">Total Admins</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/admin/moderators'">
                            <i class="fas fa-user-shield"></i>
                            Manage Moderators
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/admin/admins_list'">
                            <i class="fas fa-users-cog"></i>
                            Manage Admins
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='/unipulse/public/admin/settings'">
                            <i class="fas fa-cog"></i>
                            System Settings
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <!-- <section class="quick-actions">
            <div class="container">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <div class="action-card" onclick="window.location.href='/unipulse/public/admin/moderators_list'">
                        <div class="action-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>Moderator Management</h3>
                        <p>Add and manage platform moderators</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='/unipulse/public/admin/admins_list'">
                        <div class="action-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3>Admin Management</h3>
                        <p>Manage admin accounts and permissions</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='approval-queue.html'">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Approval Queue</h3>
                        <p>Review pending approvals</p>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- System Overview -->
        <section class="system-overview">
            <div class="container">
                <div class="section-header">
                    <h2>System Overview</h2>
                    <button onclick="toggleSystemReports()" class="view-all expand-btn" id="systemReportsBtn">
                        <span class="btn-text">View Detailed Reports</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="overview-cards">
                    <div class="overview-card">
                        <div class="overview-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="overview-content">
                            <h3>User Statistics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">2,847</span>
                                    <span class="stat-label">Total Users</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">127</span>
                                    <span class="stat-label">New This Week</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">94%</span>
                                    <span class="stat-label">Active Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overview-card">
                        <div class="overview-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="overview-content">
                            <h3>Event Statistics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">124</span>
                                    <span class="stat-label">Active Events</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">42</span>
                                    <span class="stat-label">This Week</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">78%</span>
                                    <span class="stat-label">Attendance Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden cards - shown when expanding -->
                    <div class="overview-card hidden-card" style="display: none;">
                        <div class="overview-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="overview-content">
                            <h3>System Health</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">99.9%</span>
                                    <span class="stat-label">Uptime</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">145ms</span>
                                    <span class="stat-label">Response Time</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">0.01%</span>
                                    <span class="stat-label">Error Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overview-card hidden-card" style="display: none;">
                        <div class="overview-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="overview-content">
                            <h3>Database Metrics</h3>
                            <div class="stats-grid">
                                <div class="stat">
                                    <span class="stat-value">125,847</span>
                                    <span class="stat-label">Total Records</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">2.4GB</span>
                                    <span class="stat-label">Storage Used</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">23ms</span>
                                    <span class="stat-label">Query Time</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Activity</h2>
                    <button onclick="toggleActivityLog()" class="view-all expand-btn" id="activityLogBtn">
                        <span class="btn-text">View Full Log</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="activity-list" id="activityList">
                    <!-- Initial 2 activity items will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Pending Approvals -->
        <section class="pending-approvals">
            <div class="container">
                <div class="section-header">
                    <h2>Pending Approvals</h2>
                    <button onclick="togglePendingApprovals()" class="view-all expand-btn" id="pendingApprovalsBtn">
                        <span class="btn-text">View All Pending</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="approval-list" id="approvalList">
                    <?php if (!empty($pending_approvals)): ?>
                        <?php foreach (array_slice($pending_approvals, 0, 2) as $approval): ?>
                            <div class="approval-item">
                                <div class="approval-info">
                                    <div class="info-group">
                                        <span class="label">Society Name:</span>
                                        <span class="value"><?= htmlspecialchars($approval->society_name ?? 'N/A') ?></span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">University:</span>
                                        <span class="value"><?= htmlspecialchars($approval->university ?? 'N/A') ?></span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Faculty:</span>
                                        <span class="value"><?= htmlspecialchars($approval->faculty ?? 'N/A') ?></span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Submitted:</span>
                                        <span class="value"><?= date('M d, Y', strtotime($approval->created_at)) ?></span>
                                    </div>
                                </div>
                                <div class="approval-actions">
                                    <button onclick="approvePublisher(<?= $approval->id ?>)" class="btn-approve">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button onclick="rejectPublisher(<?= $approval->id ?>)" class="btn-reject">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-approvals">
                            <i class="fas fa-check-circle"></i>
                            <p>No pending approvals at this time</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- User Management Preview -->
        <section class="user-management-preview">
            <div class="container">
                <div class="section-header">
                    <h2>Recent User Registrations</h2>
                    <button onclick="toggleUserManagement()" class="view-all expand-btn" id="userManagementBtn">
                        <span class="btn-text">Manage All Users</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="user-table">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <?php if (!empty($recent_registrations)): ?>
                                <?php foreach ($recent_registrations as $index => $registration): ?>
                                    <?php 
                                        // Determine status based on user type
                                        $status = 'Active';
                                        $statusClass = 'status-active';
                                        
                                        if (is_object($registration)) {
                                            $name = htmlspecialchars($registration->name ?? 'N/A');
                                            $email = htmlspecialchars($registration->email ?? 'N/A');
                                            $userType = ucfirst($registration->user_type ?? 'User');
                                            $createdAt = date('M j, Y', strtotime($registration->created_at));
                                            
                                            // Check status based on user type
                                            if ($registration->user_type === 'publisher' && isset($registration->approval_status)) {
                                                if ($registration->approval_status === 'pending') {
                                                    $status = 'Pending Approval';
                                                    $statusClass = 'status-pending';
                                                } elseif ($registration->approval_status === 'rejected') {
                                                    $status = 'Rejected';
                                                    $statusClass = 'status-rejected';
                                                } elseif ($registration->approval_status === 'approved') {
                                                    $status = 'Approved';
                                                    $statusClass = 'status-active';
                                                } else {
                                                    $status = 'Pending';
                                                    $statusClass = 'status-pending';
                                                }
                                            } elseif ($registration->user_type === 'sponsor' && isset($registration->verification_status)) {
                                                if ($registration->verification_status === 'pending') {
                                                    $status = 'Pending Verification';
                                                    $statusClass = 'status-pending';
                                                } elseif ($registration->verification_status === 'rejected') {
                                                    $status = 'Rejected';
                                                    $statusClass = 'status-rejected';
                                                } elseif ($registration->verification_status === 'verified') {
                                                    $status = 'Verified';
                                                    $statusClass = 'status-active';
                                                } else {
                                                    $status = 'Pending';
                                                    $statusClass = 'status-pending';
                                                }
                                            } else {
                                                // For university and public users - default to Registered
                                                $status = 'Registered';
                                                $statusClass = 'status-active';
                                            }
                                        } else {
                                            // Array format fallback
                                            $name = htmlspecialchars($registration['name'] ?? 'N/A');
                                            $email = htmlspecialchars($registration['email'] ?? 'N/A');
                                            $userType = ucfirst($registration['user_type'] ?? 'User');
                                            $createdAt = date('M j, Y', strtotime($registration['created_at']));
                                            
                                            if (isset($registration['is_active']) && !$registration['is_active']) {
                                                $status = 'Inactive';
                                                $statusClass = 'status-inactive';
                                            }
                                        }
                                        
                                        // Add hidden-row class for rows beyond the first 5
                                        $rowClass = $index >= 5 ? 'hidden-row' : '';
                                        $rowStyle = $index >= 5 ? 'style="display: none;"' : '';
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>" <?php echo $rowStyle; ?>>
                                        <td>
                                            <div class="user-info">
                                                <!-- <div class="user-avatar"><?php echo strtoupper(substr($name, 0, 1)); ?></div> -->
                                                <div>
                                                    <div class="user-name"><?php echo $name; ?></div>
                                                    <div class="user-email"><?php echo $email; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="role-badge role-<?php echo strtolower($userType); ?>"><?php echo $userType; ?></span></td>
                                        <td><?php echo $createdAt; ?></td>
                                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if (isset($registration->is_suspended) && $registration->is_suspended): ?>
                                                    <button class="btn-icon btn-activate" title="Reactivate Account" onclick="reactivateAccount(<?php echo $registration->id; ?>, '<?php echo $registration->user_type; ?>')">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn-icon btn-suspend" title="Suspend Account" onclick="suspendAccount(<?php echo $registration->id; ?>, '<?php echo $registration->user_type; ?>', '<?php echo htmlspecialchars($name); ?>')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px;">No recent registrations found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    

    <!-- Modals -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('userModal')">&times;</span>
            <h3 id="modalTitle">User Details</h3>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Suspension Modal -->
    <div id="suspensionModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeSuspensionModal()">&times;</span>
            <h3>Suspend Account</h3>
            <div class="modal-body">
                <p>You are about to suspend <strong id="suspendUserName"></strong>'s account.</p>
                <p>Please provide a reason for the suspension:</p>
                <textarea id="suspensionReason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Enter reason for suspension..."></textarea>
                <div style="margin-top: 20px; text-align: right;">
                    <button onclick="closeSuspensionModal()" style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button onclick="confirmSuspension()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Suspend Account</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Admin/dashboard-app.js"></script>
    <script>
        // Toggle System Reports - show/hide additional overview cards
        function toggleSystemReports() {
            const hiddenCards = document.querySelectorAll('.overview-card.hidden-card');
            const btn = document.getElementById('systemReportsBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            hiddenCards.forEach(card => {
                if (card.style.display === 'none') {
                    card.style.display = 'block';
                    icon.style.transform = 'rotate(180deg)';
                    btnText.textContent = 'Show Less';
                } else {
                    card.style.display = 'none';
                    icon.style.transform = 'rotate(0deg)';
                    btnText.textContent = 'View Detailed Reports';
                }
            });
        }

        // Toggle Activity Log - show/hide additional activity items
        function toggleActivityLog() {
            const activityList = document.getElementById('activityList');
            const hiddenItems = activityList.querySelectorAll('.activity-item.hidden-item');
            const btn = document.getElementById('activityLogBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            if (hiddenItems.length > 0) {
                hiddenItems.forEach(item => {
                    if (item.style.display === 'none') {
                        item.style.display = 'flex';
                        icon.style.transform = 'rotate(180deg)';
                        btnText.textContent = 'Show Less';
                    } else {
                        item.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        btnText.textContent = 'View Full Log';
                    }
                });
            }
        }

        // Toggle Pending Approvals - show/hide additional approval items
        function togglePendingApprovals() {
            const approvalList = document.getElementById('approvalList');
            const hiddenItems = approvalList.querySelectorAll('.approval-item.hidden-item');
            const btn = document.getElementById('pendingApprovalsBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            if (hiddenItems.length > 0) {
                hiddenItems.forEach(item => {
                    if (item.style.display === 'none') {
                        item.style.display = 'flex';
                        icon.style.transform = 'rotate(180deg)';
                        btnText.textContent = 'Show Less';
                    } else {
                        item.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        btnText.textContent = 'View All Pending';
                    }
                });
            }
        }

        // Toggle User Management - show/hide additional user rows
        function toggleUserManagement() {
            const userTable = document.getElementById('userTableBody');
            const hiddenRows = userTable.querySelectorAll('tr.hidden-row');
            const btn = document.getElementById('userManagementBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            if (hiddenRows.length > 0) {
                const firstHiddenRow = hiddenRows[0];
                const isCurrentlyHidden = firstHiddenRow.style.display === 'none' || !firstHiddenRow.style.display;
                
                hiddenRows.forEach(row => {
                    if (isCurrentlyHidden) {
                        row.style.display = 'table-row';
                        icon.style.transform = 'rotate(180deg)';
                        btnText.textContent = 'Show Less';
                    } else {
                        row.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        btnText.textContent = 'Manage All Users';
                    }
                });
            }
        }
    </script>
    
    <script>
        // Suspension system
        let pendingSuspension = { userId: null, userType: null };
        
        function suspendAccount(userId, userType, userName) {
            pendingSuspension = { userId, userType };
            document.getElementById('suspendUserName').textContent = userName;
            document.getElementById('suspensionModal').style.display = 'block';
        }
        
        function closeSuspensionModal() {
            document.getElementById('suspensionModal').style.display = 'none';
            document.getElementById('suspensionReason').value = '';
            pendingSuspension = { userId: null, userType: null };
        }
        
        function confirmSuspension() {
            const reason = document.getElementById('suspensionReason').value.trim();
            
            if (!reason) {
                alert('Please provide a reason for suspension');
                return;
            }
            
            fetch('/unipulse/public/admin/dashboard/suspendUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: pendingSuspension.userId,
                    user_type: pendingSuspension.userType,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account suspended successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to suspend account'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while suspending the account');
            });
            
            closeSuspensionModal();
        }
        
        function reactivateAccount(userId, userType) {
            if (!confirm('Are you sure you want to reactivate this account?')) {
                return;
            }
            
            fetch('/unipulse/public/admin/dashboard/reactivateUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    user_type: userType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account reactivated successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to reactivate account'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while reactivating the account');
            });
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('suspensionModal');
            if (event.target == modal) {
                closeSuspensionModal();
            }
        }
        
        // Publisher approval functions
        function approvePublisher(publisherId) {
            if (!confirm('Are you sure you want to approve this publisher?')) {
                return;
            }
            
            fetch('/unipulse/public/Admin/Dashboard/approvePublisher', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ publisher_id: publisherId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Publisher approved successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to approve publisher'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while approving the publisher');
            });
        }
        
        function rejectPublisher(publisherId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (!reason || reason.trim() === '') {
                alert('Rejection reason is required');
                return;
            }
            
            fetch('/unipulse/public/Admin/Dashboard/rejectPublisher', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    publisher_id: publisherId,
                    rejection_reason: reason 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Publisher rejected successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject publisher'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while rejecting the publisher');
            });
        }
    </script>
</body>

</html>