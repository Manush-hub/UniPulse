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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/unipulse/public/logout" class="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
<script src="/unipulse/public/assets/js/Moderator/header.js"></script>
