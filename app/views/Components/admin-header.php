<?php
// Admin-specific header with enhanced controls
$headerData = HeaderService::getCurrentUserData();
$adminStats = HeaderController::getAdminHeaderData();
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/admin-header.css">

<header class="admin-header">
    <div class="admin-header-container">
        <!-- Admin Brand -->
        <div class="admin-brand">
            <img src="/unipulse/public/assets/images/admin-logo.png" alt="UniPulse Admin" class="admin-logo">
            <span class="admin-title">UniPulse Admin</span>
        </div>
        
        <!-- System Status Bar -->
        <div class="system-status">
            <div class="status-item">
                <span class="status-label">Total Users:</span>
                <span class="status-value"><?= number_format($adminStats['total_users']) ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">Active Events:</span>
                <span class="status-value"><?= number_format($adminStats['active_events']) ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">Pending Reviews:</span>
                <span class="status-value warning"><?= number_format($adminStats['pending_reviews']) ?></span>
            </div>
        </div>
        
        <!-- Admin Navigation -->
        <nav class="admin-nav">
            <a href="/unipulse/public/admin/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/unipulse/public/admin/moderators" class="<?= $activeNav === 'moderators' ? 'active' : '' ?>">Moderators</a>
            <a href="/unipulse/public/admin/users" class="<?= $activeNav === 'users' ? 'active' : '' ?>">Users</a>
            <a href="/unipulse/public/admin/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">Events</a>
            <a href="/unipulse/public/admin/reports" class="<?= $activeNav === 'reports' ? 'active' : '' ?>">Reports</a>
            <a href="/unipulse/public/admin/settings" class="<?= $activeNav === 'settings' ? 'active' : '' ?>">Settings</a>
        </nav>
        
        <!-- Admin Controls -->
        <div class="admin-controls">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="openQuickCreateModal()" title="Quick Create">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
                <button class="quick-action-btn" onclick="openSystemHealth()" title="System Health">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Admin User Info -->
            <div class="admin-user">
                <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                     alt="Admin Avatar" class="admin-avatar">
                <div class="admin-info">
                    <span class="admin-name"><?= htmlspecialchars($headerData['full_name']) ?></span>
                    <span class="admin-role">Super Administrator</span>
                </div>
                <button class="admin-dropdown-btn" onclick="toggleAdminMenu()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="admin-dropdown" id="adminDropdown">
                    <a href="/unipulse/public/admin/profile">Admin Profile</a>
                    <a href="/unipulse/public/admin/security">Security Settings</a>
                    <a href="/unipulse/public/admin/audit-logs">Audit Logs</a>
                    <a href="/unipulse/public/admin/backup">System Backup</a>
                    <hr>
                    <a href="/unipulse/public/">View Public Site</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Admin Quick Actions Modal -->
<div id="quickCreateModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Quick Create</h3>
        <div class="quick-create-options">
            <button onclick="createNewModerator()">Create Moderator</button>
            <button onclick="createSystemNotice()">System Notice</button>
            <button onclick="scheduleMaintenence()">Schedule Maintenance</button>
        </div>
    </div>
</div>

<script src="/unipulse/public/assets/js/components/admin-header.js"></script>