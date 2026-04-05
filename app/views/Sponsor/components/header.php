<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';

$sponsorDisplayNameRaw = $_SESSION['user_name'] ?? ((isset($user) && is_array($user) && isset($user['name'])) ? $user['name'] : 'Sponsor');
$sponsorDisplayName = trim((string) $sponsorDisplayNameRaw);
if ($sponsorDisplayName !== '' && $sponsorDisplayName === strtolower($sponsorDisplayName)) {
    $sponsorDisplayName = ucwords($sponsorDisplayName);
}
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="dashboard.html">
                <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
            </a>
        </div>
        <nav class="nav">
            <a href="/unipulse/public/sponsor/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
            <a href="/unipulse/public/sponsor/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">All Events</a>
            <a href="/unipulse/public/sponsor/sponsorships" class="<?= $activeNav === 'sponsorships' ? 'active' : '' ?>">My Sponsorships</a>
            <a href="/unipulse/public/sponsor/messages" class="<?= $activeNav === 'messages' ? 'active' : '' ?>" style="position:relative;">
                Messages
                <span id="sponsorMsgBadge" style="display:none;position:absolute;top:-6px;right:-10px;background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 5px;border-radius:10px;min-width:16px;text-align:center;"></span>
            </a>
            <a href="/unipulse/public/sponsor/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        </nav>
        <div class="header-actions">
            <div class="notifications">
                <button class="notification-btn" onclick="toggleNotifications()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
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
            <div class="user-menu">
                <img src="<?= $_SESSION['user_logo'] ?? '/unipulse/public/assets/images/default-avatar.png' ?>" alt="Sponsor Avatar" class="avatar" id="headerAvatar">
                <div class="user-info">
                    <span class="username" id="username"><?= htmlspecialchars($sponsorDisplayName) ?></span>
                    <span class="user-role" id="userRole">Sponsor</span>
                </div>
                <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="/unipulse/public/sponsor/profile">Profile Settings</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="/unipulse/public/assets/js/Sponsor/header-app.js"></script>
