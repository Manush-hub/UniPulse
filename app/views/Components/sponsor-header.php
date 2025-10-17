<?php
// Sponsor-specific header with business controls
$headerData = HeaderService::getCurrentUserData();
$sponsorData = HeaderController::getSponsorHeaderData();
?>

<link rel="stylesheet" href="/unipulse/public/assets/css/components/sponsor-header.css">

<header class="sponsor-header">
    <div class="sponsor-header-container">
        <!-- Sponsor Brand -->
        <div class="sponsor-brand">
            <a href="/unipulse/public/sponsor/dashboard">
                <img src="/unipulse/public/assets/images/sponsor-logo.png" alt="UniPulse Sponsor" class="sponsor-logo">
            </a>
            <div class="brand-info">
                <span class="brand-title">Sponsor Portal</span>
                <span class="company-name"><?= htmlspecialchars($sponsorData['company_name']) ?></span>
            </div>
        </div>
        
        <!-- Sponsor Stats -->
        <div class="sponsor-stats">
            <div class="stat-item">
                <span class="stat-value"><?= number_format($sponsorData['active_sponsorships']) ?></span>
                <span class="stat-label">Active Sponsorships</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">$<?= number_format($sponsorData['monthly_spend'], 2) ?></span>
                <span class="stat-label">Monthly Spend</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= number_format($sponsorData['reach']) ?></span>
                <span class="stat-label">Total Reach</span>
            </div>
        </div>
        
        <!-- Sponsor Navigation -->
        <nav class="sponsor-nav">
            <a href="/unipulse/public/sponsor/dashboard" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
            <a href="/unipulse/public/sponsor/events" class="<?= $activeNav === 'events' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Events
            </a>
            <a href="/unipulse/public/sponsor/campaigns" class="<?= $activeNav === 'campaigns' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                </svg>
                Campaigns
            </a>
            <a href="/unipulse/public/sponsor/analytics" class="<?= $activeNav === 'analytics' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Analytics
            </a>
            <a href="/unipulse/public/sponsor/billing" class="<?= $activeNav === 'billing' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                Billing
            </a>
        </nav>
        
        <!-- Sponsor Actions -->
        <div class="sponsor-actions">
            <!-- Campaign Quick Actions -->
            <div class="quick-sponsor-actions">
                <button class="sponsor-action-btn primary" onclick="createNewCampaign()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Campaign
                </button>
                <button class="sponsor-action-btn secondary" onclick="viewSponsorship()">
                    View Opportunities
                </button>
            </div>
            
            <!-- Sponsor User Menu -->
            <div class="sponsor-user">
                <img src="<?= htmlspecialchars($headerData['avatar_url']) ?>" 
                     alt="Sponsor Avatar" class="sponsor-avatar">
                <div class="sponsor-info">
                    <span class="sponsor-name"><?= htmlspecialchars($headerData['full_name']) ?></span>
                    <span class="sponsor-role"><?= htmlspecialchars($sponsorData['position']) ?></span>
                    <span class="sponsor-company"><?= htmlspecialchars($sponsorData['company_name']) ?></span>
                </div>
                <button class="sponsor-dropdown-btn" onclick="toggleSponsorMenu()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <div class="sponsor-dropdown" id="sponsorDropdown">
                    <a href="/unipulse/public/sponsor/profile">Company Profile</a>
                    <a href="/unipulse/public/sponsor/branding">Branding Assets</a>
                    <a href="/unipulse/public/sponsor/contracts">Contracts</a>
                    <a href="/unipulse/public/sponsor/reports">Performance Reports</a>
                    <hr>
                    <a href="/unipulse/public/sponsor/settings">Account Settings</a>
                    <a href="/unipulse/public/sponsor/support">Support</a>
                    <hr>
                    <a href="/unipulse/public/logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Budget Alert Banner (if applicable) -->
<?php if ($sponsorData['budget_alert']): ?>
<div class="budget-alert-banner">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
        <line x1="12" y1="9" x2="12" y2="13"></line>
        <line x1="12" y1="17" x2="12.01" y2="17"></line>
    </svg>
    <span>Budget Alert: You've reached <?= $sponsorData['budget_usage_percent'] ?>% of your monthly budget.</span>
    <button onclick="manageBudget()">Manage Budget</button>
    <button onclick="dismissAlert()" class="close-alert">×</button>
</div>
<?php endif; ?>

<script src="/unipulse/public/assets/js/components/sponsor-header.js"></script>