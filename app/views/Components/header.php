<?php
// Get header data for current user
$headerData = HeaderService::getCurrentUserData();
$navigationItems = HeaderService::getNavigationItems();
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="<?= HeaderService::getDashboardUrl() ?>">
                <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
            </a>
        </div>
        
        <!-- Dynamic Navigation based on user type -->
        <nav class="nav">
            <?php foreach ($navigationItems as $navItem): ?>
                <a href="<?= htmlspecialchars($navItem['url']) ?>" 
                   class="<?= $activeNav === $navItem['key'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($navItem['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <div class="header-actions">
            <?php if ($headerData['is_authenticated']): ?>
                <!-- Notifications (only for authenticated users) -->
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge" id="notificationBadge">3</span>
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
                
                <!-- User Menu -->
                <div class="user-menu">
                    <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                         alt="User Avatar" class="avatar">
                    <div class="user-info">
                        <span class="username"><?= htmlspecialchars($headerData['full_name']) ?></span>
                        <span class="user-role"><?= htmlspecialchars($headerData['display_label']) ?></span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?= htmlspecialchars($headerData['profile_url']) ?>">Profile Settings</a>
                        
                        <?php if ($headerData['user_type'] === 'university' || $headerData['user_type'] === 'public'): ?>
                            <a href="/unipulse/public/user/preferences">Preferences</a>
                            <a href="/unipulse/public/user/help">Help & Support</a>
                        <?php elseif ($headerData['user_type'] === 'admin'): ?>
                            <a href="/unipulse/public/admin/settings">System Settings</a>
                            <a href="/unipulse/public/admin/logs">System Logs</a>
                        <?php elseif ($headerData['user_type'] === 'moderator'): ?>
                            <a href="/unipulse/public/moderator/tools">Moderation Tools</a>
                            <a href="/unipulse/public/moderator/help">Help Center</a>
                        <?php elseif ($headerData['user_type'] === 'sponsor'): ?>
                            <a href="/unipulse/public/sponsor/billing">Billing</a>
                            <a href="/unipulse/public/sponsor/analytics">Analytics</a>
                        <?php elseif ($headerData['user_type'] === 'publisher'): ?>
                            <a href="/unipulse/public/publisher/analytics">Event Analytics</a>
                            <a href="/unipulse/public/publisher/templates">Event Templates</a>
                        <?php endif; ?>
                        
                        <hr>
                        <a href="<?= htmlspecialchars($headerData['logout_url']) ?>" class="logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest User Actions -->
                <div class="guest-actions">
                    <a href="<?= htmlspecialchars($headerData['signin_url']) ?>" class="btn btn-primary">Sign In</a>
                    <a href="<?= htmlspecialchars($headerData['signup_url']) ?>" class="btn btn-secondary">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script src="/unipulse/public/assets/js/components/header.js"></script>