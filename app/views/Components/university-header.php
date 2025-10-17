<?php
// University user-specific header with academic features
$headerData = HeaderService::getCurrentUserData();
$universityData = HeaderController::getUniversityUserHeaderData();
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/university-header.css">

<header class="university-header">
    <div class="university-header-container">
        <!-- University Brand -->
        <div class="university-brand">
            <a href="/unipulse/public/user/dashboard">
                <img src="/unipulse/public/assets/images/unipulse-logo.png" alt="UniPulse" class="uni-logo">
            </a>
            <div class="brand-info">
                <span class="brand-title">UniPulse</span>
                <span class="university-name"><?= htmlspecialchars($universityData['university_name']) ?></span>
            </div>
        </div>
        
        <!-- University Event Stats -->
        <div class="university-stats">
            <div class="stat-item">
                <span class="stat-value"><?= number_format($universityData['upcoming_events']) ?></span>
                <span class="stat-label">Upcoming Events</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($universityData['my_events']) ?></span>
                <span class="stat-label">My Events</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($universityData['university_events']) ?></span>
                <span class="stat-label"><?= htmlspecialchars($universityData['university_short_name']) ?> Events</span>
            </div>
        </div>
        
        <!-- University Navigation -->
        <nav class="university-nav">
            <a href="/unipulse/public/user/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
            <a href="/unipulse/public/find_events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Find Events
            </a>
            <a href="/unipulse/public/user/my-events" class="<?= $activeNav === 'my-events' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7z"></path>
                </svg>
                My Events
            </a>
            <a href="/unipulse/public/user/calendar" class="<?= $activeNav === 'calendar' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Calendar
            </a>
            <a href="/unipulse/public/user/clubs" class="<?= $activeNav === 'clubs' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Clubs & Orgs
            </a>
            <a href="/unipulse/public/user/academic-calendar" class="<?= $activeNav === 'academic' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
                Academic Calendar
            </a>
        </nav>
        
        <!-- University Actions -->
        <div class="university-actions">
            <!-- Quick Event Actions -->
            <div class="quick-university-actions">
                <button class="university-action-btn primary" onclick="exploreEvents()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg>
                    Explore Events
                </button>
                <button class="university-action-btn secondary" onclick="joinClub()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    Join Club
                </button>
            </div>
            
            <!-- University Notifications -->
            <div class="university-notifications">
                <button class="notification-btn" onclick="toggleUniversityNotifications()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <?php if ($universityData['unread_notifications'] > 0): ?>
                        <span class="notification-badge"><?= $universityData['unread_notifications'] ?></span>
                    <?php endif; ?>
                </button>
                <div class="notification-dropdown" id="universityNotificationDropdown">
                    <div class="notification-header">
                        <h3>Campus Notifications</h3>
                        <button onclick="markAllAsRead()">Mark all as read</button>
                    </div>
                    <div class="notification-categories">
                        <button class="category-tab active" data-category="all">All</button>
                        <button class="category-tab" data-category="events">Events</button>
                        <button class="category-tab" data-category="clubs">Clubs</button>
                        <button class="category-tab" data-category="academic">Academic</button>
                    </div>
                    <div class="notification-list" id="universityNotificationList">
                        <!-- University-specific notifications will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- University User Menu -->
            <div class="university-user">
                <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                     alt="Student Avatar" class="university-avatar">
                <div class="university-info">
                    <span class="university-student-name"><?= htmlspecialchars($headerData['full_name']) ?></span>
                    <span class="university-student-id"><?= htmlspecialchars($universityData['student_id']) ?></span>
                    <span class="university-student-uni"><?= htmlspecialchars($universityData['university_short_name']) ?></span>
                </div>
                <button class="university-dropdown-btn" onclick="toggleUniversityMenu()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="university-dropdown" id="universityDropdown">
                    <div class="dropdown-section">
                        <h4>Academic</h4>
                        <a href="/unipulse/public/user/academic-profile">Academic Profile</a>
                        <a href="/unipulse/public/user/courses">My Courses</a>
                        <a href="/unipulse/public/user/grades">Grades & GPA</a>
                        <a href="/unipulse/public/user/transcript">Transcript</a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Campus Life</h4>
                        <a href="/unipulse/public/user/clubs-memberships">Club Memberships</a>
                        <a href="/unipulse/public/user/campus-services">Campus Services</a>
                        <a href="/unipulse/public/user/dining-plans">Dining Plans</a>
                        <a href="/unipulse/public/user/housing">Housing Info</a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Events & Activities</h4>
                        <a href="/unipulse/public/user/event-history">Event History</a>
                        <a href="/unipulse/public/user/rsvp-management">RSVP Management</a>
                        <a href="/unipulse/public/user/interests">Interest Preferences</a>
                    </div>
                    <hr>
                    <a href="/unipulse/public/user/profile">Profile Settings</a>
                    <a href="/unipulse/public/user/privacy">Privacy Settings</a>
                    <a href="/unipulse/public/user/help">Help & Support</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- University Announcements Banner -->
<?php if (!empty($universityData['university_announcements'])): ?>
<div class="university-announcements-banner">
    <div class="announcement-slider">
        <?php foreach ($universityData['university_announcements'] as $announcement): ?>
            <div class="university-announcement <?= htmlspecialchars($announcement['type']) ?>">
                <div class="announcement-content">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
                    </svg>
                    <span class="announcement-text"><?= htmlspecialchars($announcement['message']) ?></span>
                    <?php if (!empty($announcement['action_url'])): ?>
                        <a href="<?= htmlspecialchars($announcement['action_url']) ?>" class="announcement-action"><?= htmlspecialchars($announcement['action_text']) ?></a>
                    <?php endif; ?>
                </div>
                <button onclick="dismissUniversityAnnouncement('<?= htmlspecialchars($announcement['id']) ?>')" class="close-announcement">×</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script src="/unipulse/public/assets/js/components/university-header.js"></script>