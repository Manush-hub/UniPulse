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
                                <span class="stat-number" id="totalUsers"><?php echo number_format((int)($stats['total_users'] ?? 0)); ?></span>
                                <span class="stat-label">Total Users</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo isset($stats['total_publishers']) ? $stats['total_publishers'] : 0; ?></span>
                                <span class="stat-label">Total Publishers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo isset($stats['total_sponsors']) ? $stats['total_sponsors'] : 0; ?></span>
                                <span class="stat-label">Total Sponsors</span>
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
                                    <span class="stat-value" id="overviewTotalUsers"><?php echo number_format((int)($stats['total_users'] ?? 0)); ?></span>
                                    <span class="stat-label">Total Users</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value" id="overviewUniversityUsers"><?php echo number_format((int)($stats['total_university_users'] ?? 0)); ?></span>
                                    <span class="stat-label">University Users</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value" id="overviewPublicUsers"><?php echo number_format((int)($stats['total_public_users'] ?? 0)); ?></span>
                                    <span class="stat-label">Public Users</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value" id="overviewPublisherUsers"><?php echo number_format((int)($stats['total_publishers'] ?? 0)); ?></span>
                                    <span class="stat-label">Publishers</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value" id="overviewSponsorUsers"><?php echo number_format((int)($stats['total_sponsors'] ?? 0)); ?></span>
                                    <span class="stat-label">Sponsors</span>
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
                                    <span class="stat-value" id="overviewActiveEvents"><?php echo number_format((int)($stats['active_events'] ?? 0)); ?></span>
                                    <span class="stat-label">Active Events</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value" id="overviewTotalEvents"><?php echo number_format((int)($stats['total_events'] ?? 0)); ?></span>
                                    <span class="stat-label">Total Events</span>
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
                    <button onclick="toggleUserManagement()" class="view-all expand-btn" id="userManagementBtn" title="View all users in a popup">
                        <span class="btn-text">Manage All Users</span>
                        <i class="fas fa-external-link-alt expand-icon"></i>
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
                                                    $status = 'Active';
                                                    $statusClass = 'status-active';
                                                } else {
                                                    $status = 'Pending';
                                                    $statusClass = 'status-pending';
                                                }
                                            } elseif ($registration->user_type === 'sponsor' && isset($registration->verification_status)) {
                                                if ($registration->verification_status === 'active') {
                                                    $status = 'Active';
                                                    $statusClass = 'status-active';
                                                } else {
                                                    $status = 'Pending Verification';
                                                    $statusClass = 'status-pending';
                                                }
                                            } else {
                                                $status = 'Active';
                                                $statusClass = 'status-active';
                                            }
                                            
                                            // Always override with suspended status — applies to ALL user types
                                            if (isset($registration->is_suspended) && $registration->is_suspended) {
                                                $status = 'Suspended';
                                                $statusClass = 'status-inactive';
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
                                    <tr class="<?php echo $rowClass; ?>" <?php echo $rowStyle; ?> id="dashboard-user-<?php echo $registration->id; ?>-<?php echo $registration->user_type; ?>">
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
                                                    <?php if (!empty($registration->has_pending_appeal)): ?>
                                                        <button
                                                            class="btn-icon"
                                                            title="Review Appeal"
                                                            onclick="openAppealModalFromButton(this)"
                                                            data-appeal-id="<?php echo (int)($registration->pending_appeal_id ?? 0); ?>"
                                                            data-user-id="<?php echo (int)$registration->id; ?>"
                                                            data-user-type="<?php echo htmlspecialchars($registration->user_type ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-user-name="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-suspension-reason="<?php echo htmlspecialchars($registration->suspension_reason ?? 'No reason provided', ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-appeal-message="<?php echo htmlspecialchars($registration->pending_appeal_message ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-submitted-at="<?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($registration->pending_appeal_submitted_at ?? 'now')), ENT_QUOTES, 'UTF-8'); ?>"
                                                        >
                                                            <i class="fas fa-envelope-open-text"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($status !== 'Rejected'): ?>
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

    <!-- Appeal Review Modal -->
    <div id="appealModal" class="modal">
        <div class="modal-content" style="max-width: 680px; width: 92%;">
            <span class="close-button" onclick="closeAppealModal()">&times;</span>
            <h3>Review Suspension Appeal</h3>
            <div class="modal-body">
                <p><strong>User:</strong> <span id="appealUserName">-</span></p>
                <p><strong>User Type:</strong> <span id="appealUserType">-</span></p>
                <p><strong>Suspension Reason:</strong></p>
                <div id="appealSuspensionReason" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; margin-bottom: 12px;"></div>

                <p><strong>Appeal Message:</strong></p>
                <div id="appealMessageBody" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; margin-bottom: 12px;"></div>

                <p><strong>Submitted At:</strong> <span id="appealSubmittedAt">-</span></p>

                <label for="appealAdminResponse" style="display: block; margin-top: 15px; margin-bottom: 6px;"><strong>Admin Response (required if rejecting):</strong></label>
                <textarea id="appealAdminResponse" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Write your response to this appeal..."></textarea>

                <div style="margin-top: 20px; text-align: right;">
                    <button onclick="closeAppealModal()" style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button onclick="submitAppealDecision('rejected')" style="padding: 10px 20px; margin-right: 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Reject Appeal</button>
                    <button onclick="submitAppealDecision('approved')" style="padding: 10px 20px; background: #198754; color: white; border: none; border-radius: 4px; cursor: pointer;">Approve Appeal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- All Users Modal -->
    <div id="allUsersModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 1200px; width: 95%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
            <span class="close-button" onclick="closeAllUsersModal()">&times;</span>
            <h3 style="margin-bottom: 20px;">All User Registrations</h3>
            <div id="allUsersLoadingMessage" style="text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <p>Loading all users...</p>
            </div>
            <div id="allUsersContent" style="display: none; overflow-y: auto; flex: 1;">
                <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
                    <input 
                        type="text" 
                        id="userSearchInput" 
                        placeholder="Search by name, email, or role..." 
                        style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                        onkeyup="filterUsers()"
                    >
                    <select id="userTypeFilter" onchange="filterUsers()" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All User Types</option>
                        <option value="university">University</option>
                        <option value="public">Public</option>
                        <option value="publisher">Publisher</option>
                        <option value="sponsor">Sponsor</option>
                    </select>
                    <select id="userStatusFilter" onchange="filterUsers()" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                        <option value="Suspended">Suspended</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div style="overflow-x: auto;">
                    <table class="user-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left;">User</th>
                                <th style="padding: 12px; text-align: left;">Role</th>
                                <th style="padding: 12px; text-align: left;">Registration Date</th>
                                <th style="padding: 12px; text-align: left;">Status</th>
                                <th style="padding: 12px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allUsersTableBody">
                            <!-- User rows will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="noUsersMessage" style="display: none; text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"></i>
                    <p>No users found matching your filters.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Admin/dashboard-app.js?v=<?= time() ?>"></script>
    <script src="<?php echo ROOT ?>/assets/js/extracted/Admin_dashboard.js"></script>
    
    
</body>

</html>