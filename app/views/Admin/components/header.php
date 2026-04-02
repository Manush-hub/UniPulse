<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">

<header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/admin/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/admin/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
                <a href="/unipulse/public/admin/allevents" class="<?= $activeNav === 'allevents' ? 'active' : '' ?>">All Events</a>
                <a href="/unipulse/public/admin/messages" class="<?= $activeNav === 'messages' ? 'active' : '' ?>" style="position:relative;">
                    Messages
                    <span id="adminMsgBadge" style="display:none;position:absolute;top:-6px;right:-10px;background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 5px;border-radius:10px;min-width:16px;text-align:center;"></span>
                </a>
                <a href="/unipulse/public/admin/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge" id="notificationBadge">5</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button onclick="markAllAsRead()">Mark all as read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/admin.png" alt="Admin" class="avatar">
                    <div class="user-info">
                        <span class="username" id="username">admin</span>
                        <span class="user-role" id="userRole">System Administrator</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <!-- <a href="profile.html"><i class="fas fa-user-cog"></i> Profile Settings</a>
                        <a href="auditlog.html"><i class="fas fa-clipboard-list"></i> Audit Log</a>
                        <a href="help.html"><i class="fas fa-question-circle"></i> Help & Support</a> -->
                        <a href="/unipulse/public/logout" class="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
<script src="/unipulse/public/assets/js/Admin/header-app.js"></script>
<script>
(function () {
    function updateAdminMsgBadge() {
        fetch('/unipulse/public/admin/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('adminMsgBadge');
                if (!badge) return;
                if (data.success && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(() => {});
    }
    updateAdminMsgBadge();
    setInterval(updateAdminMsgBadge, 30000);
})();
</script>
