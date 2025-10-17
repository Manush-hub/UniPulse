<?php
// Publisher-specific header with event management controls
$headerData = HeaderService::getCurrentUserData();
$publisherData = HeaderController::getPublisherHeaderData();
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/publisher-header.css">

<header class="publisher-header">
    <div class="publisher-header-container">
        <!-- Publisher Brand -->
        <div class="publisher-brand">
            <a href="/unipulse/public/publisher/dashboard">
                <img src="/unipulse/public/assets/images/publisher-logo.png" alt="UniPulse Publisher" class="publisher-logo">
            </a>
            <div class="brand-info">
                <span class="brand-title">Event Publisher</span>
                <span class="organization-name"><?= htmlspecialchars($publisherData['organization_name']) ?></span>
            </div>
        </div>
        
        <!-- Publisher Stats -->
        <div class="publisher-stats">
            <div class="stat-item">
                <span class="stat-value"><?= number_format($publisherData['total_events']) ?></span>
                <span class="stat-label">Total Events</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($publisherData['active_events']) ?></span>
                <span class="stat-label">Active Events</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($publisherData['total_attendees']) ?></span>
                <span class="stat-label">Total Attendees</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($publisherData['pending_reviews']) ?></span>
                <span class="stat-label">Pending Reviews</span>
            </div>
        </div>
        
        <!-- Publisher Navigation -->
        <nav class="publisher-nav">
            <a href="/unipulse/public/publisher/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
            <a href="/unipulse/public/publisher/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                My Events
            </a>
            <a href="/unipulse/public/publisher/create-event" class="<?= $activeNav === 'create' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Create Event
            </a>
            <a href="/unipulse/public/publisher/analytics" class="<?= $activeNav === 'analytics' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Analytics
            </a>
            <a href="/unipulse/public/publisher/templates" class="<?= $activeNav === 'templates' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M9 9h6v6H9z"></path>
                </svg>
                Templates
            </a>
            <a href="/unipulse/public/publisher/audience" class="<?= $activeNav === 'audience' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Audience
            </a>
        </nav>
        
        <!-- Publisher Actions -->
        <div class="publisher-actions">
            <!-- Event Quick Actions -->
            <div class="quick-publisher-actions">
                <button class="publisher-action-btn primary" onclick="createNewEvent()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Create Event
                </button>
                <button class="publisher-action-btn secondary" onclick="duplicateLastEvent()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    Duplicate
                </button>
                <button class="publisher-action-btn secondary" onclick="openTemplateLibrary()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14,2 14,8 20,8"></polyline>
                    </svg>
                    Templates
                </button>
            </div>
            
            <!-- Notifications for Publishers -->
            <div class="publisher-notifications">
                <button class="notification-btn" onclick="togglePublisherNotifications()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <?php if ($publisherData['unread_notifications'] > 0): ?>
                        <span class="notification-badge"><?= $publisherData['unread_notifications'] ?></span>
                    <?php endif; ?>
                </button>
            </div>
            
            <!-- Publisher User Menu -->
            <div class="publisher-user">
                <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                     alt="Publisher Avatar" class="publisher-avatar">
                <div class="publisher-info">
                    <span class="publisher-name"><?= htmlspecialchars($headerData['full_name']) ?></span>
                    <span class="publisher-role"><?= htmlspecialchars($publisherData['publisher_type']) ?> Publisher</span>
                    <span class="publisher-org"><?= htmlspecialchars($publisherData['organization_name']) ?></span>
                </div>
                <button class="publisher-dropdown-btn" onclick="togglePublisherMenu()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="publisher-dropdown" id="publisherDropdown">
                    <a href="/unipulse/public/publisher/profile">Publisher Profile</a>
                    <a href="/unipulse/public/publisher/organization">Organization Settings</a>
                    <a href="/unipulse/public/publisher/branding">Branding & Assets</a>
                    <a href="/unipulse/public/publisher/verification">Verification Status</a>
                    <hr>
                    <a href="/unipulse/public/publisher/templates">Event Templates</a>
                    <a href="/unipulse/public/publisher/audience-insights">Audience Insights</a>
                    <a href="/unipulse/public/publisher/promotion-tools">Promotion Tools</a>
                    <hr>
                    <a href="/unipulse/public/publisher/settings">Account Settings</a>
                    <a href="/unipulse/public/publisher/help">Help & Guides</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Event Status Alerts -->
<?php if (!empty($publisherData['event_alerts'])): ?>
<div class="event-alerts-banner">
    <?php foreach ($publisherData['event_alerts'] as $alert): ?>
        <div class="event-alert <?= htmlspecialchars($alert['type']) ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <?php if ($alert['type'] === 'warning'): ?>
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
            <button onclick="dismissEventAlert('<?= htmlspecialchars($alert['id']) ?>')" class="close-alert">×</button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script src="/unipulse/public/assets/js/components/publisher-header.js"></script>