<?php
$pageConfig = isset($pageConfig) ? $pageConfig : [];
$activeNav = isset($pageConfig['activeNav']) ? $pageConfig['activeNav'] : '';
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">

<header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="dashboard.html">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/admin/landing" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
                <a href="/unipulse/public/admin/allevents" class="<?= $activeNav === 'allevents' ? 'active' : '' ?>">All Events</a>
                <a href="/unipulse/public/admin/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            </nav>
            <div class="header-actions">
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
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
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <!-- <a href="profile.html"><i class="fas fa-user-cog"></i> Profile Settings</a>
                        <a href="auditlog.html"><i class="fas fa-clipboard-list"></i> Audit Log</a>
                        <a href="help.html"><i class="fas fa-question-circle"></i> Help & Support</a> -->
                        <hr>
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
<script src="/unipulse/public/assets/js/admin/header-app.js"></script>
