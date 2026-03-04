<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';
?>

<?php if (!isset($headerCssLoaded)): ?>
<link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
<?php endif; ?>

<header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="dashboard.html">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderator/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
                <a href="/unipulse/public/moderator/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">Events</a>
                <a href="/unipulse/public/moderator/events/hiddenEvents" class="<?= $activeNav === 'hidden-events' ? 'active' : '' ?>">Hidden Events</a>
                <a href="/unipulse/public/moderator/comments" class="<?= $activeNav === 'comments' ? 'active' : '' ?>">Comments</a>
                <a href="/unipulse/public/moderator/messages" class="<?= $activeNav === 'messages' ? 'active' : '' ?>" style="position:relative;">
                    Messages
                    <span id="moderatorMsgBadge" style="display:none;position:absolute;top:-6px;right:-10px;background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 5px;border-radius:10px;min-width:16px;text-align:center;"></span>
                </a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button>Mark all as read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/moderator.png" alt="Moderator" class="avatar">
                    <div class="user-info">
                        <span class="username" id="username"><?php 
                            if (isset($moderator) && is_object($moderator) && !empty($moderator->full_name)) {
                                echo htmlspecialchars($moderator->full_name);
                            } elseif (isset($user) && is_array($user) && !empty($user['full_name'])) {
                                echo htmlspecialchars($user['full_name']);
                            } elseif (isset($user) && is_array($user) && !empty($user['name'])) {
                                echo htmlspecialchars($user['name']);
                            } else {
                                echo 'Moderator';
                            }
                        ?></span>
                        <span class="user-role" id="userRole">Moderator</span>
                    </div>
                    <button class="user-dropdown-btn">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
<script src="/unipulse/public/assets/js/Moderator/header.js"></script>
<script>
(function () {
    function updateModeratorMsgBadge() {
        fetch('/unipulse/public/moderator/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('moderatorMsgBadge');
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
    updateModeratorMsgBadge();
    setInterval(updateModeratorMsgBadge, 30000);
})();
</script>