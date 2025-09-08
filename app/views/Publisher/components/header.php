<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/publisher/components/header-style.css">

<header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/publisher/landing">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/publisher/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
                <a href="/unipulse/public/publisher/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">My Events</a>
                <a href="/unipulse/public/publisher/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
                <a href="/unipulse/public/createevent" class="<?= $activeNav === 'create' ? 'active' : '' ?>">Create Event</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge" id="notificationBadge">2</span>
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
                    <img src="/unipulse/public/assets/images/default-avatar.png" alt="User Avatar" class="avatar">
                    <div class="user-info">
                        <span class="username" id="username">Publisher</span>
                        <span class="user-role">Event Organizer</span>
                    </div>
                    <button class="user-dropdown-btn" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.html">Profile Settings</a>
                        <a href="preferences.html">Preferences</a>
                        <a href="help.html">Help & Support</a>
                        <hr>
                        <a href="/unipulse/public/logout" class="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
</header>
<script src="/unipulse/public/assets/js/publisher/header-app.js"></script>
