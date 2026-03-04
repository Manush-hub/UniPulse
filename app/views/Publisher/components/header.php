<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';

// Get current user data from session
$currentUser = AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null;

// Get publisher name from session
$publisherName = 'Publisher';
$publisherRole = 'Publisher';

if ($currentUser) {
    // Try to get organization name first, fallback to user name
    if (isset($currentUser['organization_name']) && !empty($currentUser['organization_name'])) {
        $publisherName = $currentUser['organization_name'];
    } elseif (isset($currentUser['name']) && !empty($currentUser['name'])) {
        $publisherName = $currentUser['name'];
    }

    // Set role
    if (isset($currentUser['role'])) {
        $publisherRole = ucfirst($currentUser['role']);
    }
}

// Check session for organization name (might be stored separately)
if (isset($_SESSION['organization_name']) && !empty($_SESSION['organization_name'])) {
    $publisherName = $_SESSION['organization_name'];
}
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name']) && $publisherName === 'Publisher') {
    $publisherName = $_SESSION['user_name'];
}

// Get profile photo from session or use default
$profilePhoto = '/unipulse/public/assets/images/organizer.jpg';
if (isset($_SESSION['user_profile_photo']) && !empty($_SESSION['user_profile_photo'])) {
    $profilePhoto = $_SESSION['user_profile_photo'];
} elseif (isset($_SESSION['profile_photo']) && !empty($_SESSION['profile_photo'])) {
    $profilePhoto = $_SESSION['profile_photo'];
}
?>

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="/unipulse/public/publisher/landing">
                <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
            </a>
        </div>
        <nav class="nav">
            <a href="/unipulse/public/publisher/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
            <a href="/unipulse/public/publisher/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">All Events</a>
            <a href="/unipulse/public/publisher/sponsorships" class="<?= $activeNav === 'sponsorships' ? 'active' : '' ?>">Sponsorships</a>
            <a href="/unipulse/public/publisher/messages" class="<?= $activeNav === 'messages' ? 'active' : '' ?>" style="position:relative;">
                Messages
                <span id="publisherMsgBadge" style="display:none;position:absolute;top:-6px;right:-10px;background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 5px;border-radius:10px;min-width:16px;text-align:center;"></span>
            </a>
            <a href="/unipulse/public/publisher/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        </nav>
        <div class="header-actions">
            <div class="notifications">
                <button class="notification-btn" onclick="toggleNotifications()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge hidden" id="notificationBadge">0</span>
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
                <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Publisher Avatar" class="avatar" id="headerAvatar">
                <div class="user-info">
                    <span class="username" id="username"><?php echo htmlspecialchars($publisherName); ?></span>
                    <span class="user-role" id="userRole"><?php echo htmlspecialchars($publisherRole); ?></span>
                </div>
                <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="/unipulse/public/publisher/profile">Profile Settings</a>
                    <!-- <a href="preferences.html">Preferences</a>
                        <a href="help.html">Help & Support</a>
                        <hr> -->
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="/unipulse/public/assets/js/Publisher/header-app.js"></script>
<script>
(function () {
    function updatePublisherMsgBadge() {
        fetch('/unipulse/public/publisher/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('publisherMsgBadge');
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
    updatePublisherMsgBadge();
    setInterval(updatePublisherMsgBadge, 30000);
})();
</script>