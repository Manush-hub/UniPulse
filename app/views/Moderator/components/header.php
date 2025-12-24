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
                <!-- <a href="/unipulse/public/moderator/publisher" class="<?= $activeNav === 'publisher' ? 'active' : '' ?>">Publishers</a> -->
                <a href="/unipulse/public/moderator/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">Events</a>
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
                            if (isset($moderator) && is_object($moderator) && property_exists($moderator, 'full_name')) {
                                echo htmlspecialchars($moderator->full_name);
                            } elseif (isset($user) && is_array($user) && isset($user['full_name'])) {
                                echo htmlspecialchars($user['full_name']);
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