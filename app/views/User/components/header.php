<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';

// Get current user data from session
$currentUser = AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null;

// Get user name from session (full_name from profile) or fallback to session user_name or 'User'
$userName = 'User';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} elseif ($currentUser && isset($currentUser['name'])) {
    $userName = $currentUser['name'];
}

// Get profile photo from session or use default
$profilePhoto = '/unipulse/public/assets/images/default-avatar.png';
if (isset($_SESSION['user_profile_photo']) && !empty($_SESSION['user_profile_photo'])) {
    $profilePhoto = $_SESSION['user_profile_photo'];
}
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="/unipulse/public/user/landing">
                <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
            </a>
        </div>
        <nav class="nav">
            <a href="/unipulse/public/user/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
            <a href="/unipulse/public/user/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">All Events</a>
            <a href="/unipulse/public/user/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        </nav>
        <div class="header-actions">
            <div class="notifications">
                <button class="notification-btn" onclick="toggleNotifications()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge hidden" id="notificationBadge"></span>
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
                <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="User Avatar" class="avatar">
                <div class="user-info">
                    <span class="username" id="username"><?php echo htmlspecialchars($userName); ?></span>
                </div>
                <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="/unipulse/public/user/profile">Profile Settings</a>
                    <a href="#" id="openRegisteredEventsCalendar">Calendar</a>
                    <!-- <a href="/unipulse/public/user/preferences">Preferences</a>
                        <a href="/unipulse/public/user/help">Help & Support</a>
                        <hr> -->
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="/unipulse/public/assets/js/Common/registered-events-calendar.js"></script>
<script src="/unipulse/public/assets/js/User/header-app.js"></script>