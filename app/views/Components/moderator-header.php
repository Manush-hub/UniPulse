<?php
// Moderator-specific header with moderation controls
$headerData = HeaderService::getCurrentUserData();
$moderatorData = HeaderController::getModeratorHeaderData();
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/moderator-header.css">

<header class="moderator-header">
    <div class="moderator-header-container">
        <!-- Moderator Brand -->
        <div class="moderator-brand">
            <a href="/unipulse/public/moderator/dashboard">
                <img src="/unipulse/public/assets/images/moderator-logo.png" alt="UniPulse Moderator" class="moderator-logo">
            </a>
            <div class="brand-info">
                <span class="brand-title">Content Moderator</span>
                <span class="moderator-level"><?= htmlspecialchars($moderatorData['level']) ?> Level</span>
            </div>
        </div>
        
        <!-- Moderation Queue Stats -->
        <div class="moderation-stats">
            <div class="stat-item urgent">
                <span class="stat-value"><?= number_format($moderatorData['urgent_reviews']) ?></span>
                <span class="stat-label">Urgent Reviews</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($moderatorData['pending_reviews']) ?></span>
                <span class="stat-label">Pending Reviews</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($moderatorData['today_reviewed']) ?></span>
                <span class="stat-label">Today Reviewed</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($moderatorData['reported_content']) ?></span>
                <span class="stat-label">Reported Content</span>
            </div>
        </div>
        
        <!-- Moderator Navigation -->
        <nav class="moderator-nav">
            <a href="/unipulse/public/moderator/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
            <a href="/unipulse/public/moderator/review-queue" class="<?= $activeNav === 'queue' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4"></path>
                    <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"></path>
                    <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"></path>
                    <path d="M12 3c0 1-1 3-3 3s-3-2-3-3 1-3 3-3 3 2 3 3"></path>
                    <path d="M12 21c0-1 1-3 3-3s3 2 3 3-1 3-3 3-3-2-3-3"></path>
                </svg>
                Review Queue
                <?php if ($moderatorData['urgent_reviews'] > 0): ?>
                    <span class="urgent-badge"><?= $moderatorData['urgent_reviews'] ?></span>
                <?php endif; ?>
            </a>
            <a href="/unipulse/public/moderator/reports" class="<?= $activeNav === 'reports' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14,2 14,8 20,8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10,9 9,9 8,9"></polyline>
                </svg>
                Reports
            </a>
            <a href="/unipulse/public/moderator/users" class="<?= $activeNav === 'users' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                User Management
            </a>
            <a href="/unipulse/public/moderator/analytics" class="<?= $activeNav === 'analytics' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Analytics
            </a>
        </nav>
        
        <!-- Moderator Actions -->
        <div class="moderator-actions">
            <!-- Quick Moderation Actions -->
            <div class="quick-mod-actions">
                <button class="mod-action-btn urgent" onclick="reviewUrgentContent()" title="Review Urgent Content">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Urgent (<?= $moderatorData['urgent_reviews'] ?>)
                </button>
                <button class="mod-action-btn primary" onclick="openNextReview()" title="Review Next Item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"></path>
                    </svg>
                    Review Next
                </button>
                <button class="mod-action-btn secondary" onclick="bulkActions()" title="Bulk Actions">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                    Bulk Actions
                </button>
            </div>
            
            <!-- Moderator Notifications -->
            <div class="moderator-notifications">
                <button class="notification-btn" onclick="toggleModeratorNotifications()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <?php if ($moderatorData['unread_notifications'] > 0): ?>
                        <span class="notification-badge"><?= $moderatorData['unread_notifications'] ?></span>
                    <?php endif; ?>
                </button>
            </div>
            
            <!-- Moderator User Menu -->
            <div class="moderator-user">
                <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                     alt="Moderator Avatar" class="moderator-avatar">
                <div class="moderator-info">
                    <span class="moderator-name"><?= htmlspecialchars($headerData['full_name']) ?></span>
                    <span class="moderator-role"><?= htmlspecialchars($moderatorData['level']) ?> Moderator</span>
                    <div class="moderator-stats-mini">
                        <span class="reviews-today"><?= $moderatorData['today_reviewed'] ?> reviews today</span>
                    </div>
                </div>
                <button class="moderator-dropdown-btn" onclick="toggleModeratorMenu()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="moderator-dropdown" id="moderatorDropdown">
                    <a href="/unipulse/public/moderator/profile">Moderator Profile</a>
                    <a href="/unipulse/public/moderator/guidelines">Moderation Guidelines</a>
                    <a href="/unipulse/public/moderator/training">Training Materials</a>
                    <a href="/unipulse/public/moderator/performance">Performance Metrics</a>
                    <hr>
                    <a href="/unipulse/public/moderator/escalation">Escalation Protocol</a>
                    <a href="/unipulse/public/moderator/tools">Moderation Tools</a>
                    <a href="/unipulse/public/moderator/appeals">Appeals Review</a>
                    <hr>
                    <a href="/unipulse/public/moderator/settings">Account Settings</a>
                    <a href="/unipulse/public/moderator/help">Help & Support</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Moderation Alerts Banner -->
<?php if (!empty($moderatorData['moderation_alerts'])): ?>
<div class="moderation-alerts-banner">
    <?php foreach ($moderatorData['moderation_alerts'] as $alert): ?>
        <div class="moderation-alert <?= htmlspecialchars($alert['priority']) ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <?php if ($alert['priority'] === 'high'): ?>
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                <?php else: ?>
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                <?php endif; ?>
            </svg>
            <span><?= htmlspecialchars($alert['message']) ?></span>
            <a href="<?= htmlspecialchars($alert['action_url']) ?>" class="alert-action"><?= htmlspecialchars($alert['action_text']) ?></a>
            <button onclick="dismissModerationAlert('<?= htmlspecialchars($alert['id']) ?>')" class="close-alert">×</button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script src="/unipulse/public/assets/js/components/moderator-header.js"></script>