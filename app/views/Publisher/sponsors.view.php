<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Current Sponsors</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/sponsors-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'sponsors'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-text">
                        <h1>Current Sponsors</h1>
                        <p>Connect with sponsors registered in the UniPulse system</p>
                    </div>
                    <!-- <div class="header-actions">
                        <button class="btn btn-secondary" onclick="exportSponsors()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Export List
                        </button>
                    </div> -->
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="sponsors-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?= $stats->total_sponsors ?? 0 ?></span>
                            <span class="stat-label">Total Sponsors</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="10 8 16 12 10 16 10 8"></polygon>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?= $stats->active_sponsors ?? 0 ?></span>
                            <span class="stat-label">Active Sponsors</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon new">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?= $stats->new_sponsors ?? 0 ?></span>
                            <span class="stat-label">New This Month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters and Search -->
        <section class="sponsors-filters">
            <div class="container">
                <div class="filters-row">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="M21 21l-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="searchSponsors" placeholder="Search sponsors by name or email...">
                    </div>
                    <div class="filter-controls">
                        <select id="activityFilter" class="filter-select">
                            <option value="">All Activity Status</option>
                            <option value="Active">Active</option>
                            <option value="Recently Active">Recently Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Never">Never Logged In</option>
                        </select>
                        <select id="sortBy" class="filter-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name">Company Name A-Z</option>
                            <option value="name_desc">Company Name Z-A</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsors List -->
        <section class="sponsors-list">
            <div class="container">
                <div class="sponsors-grid" id="sponsorsGrid">
                    <?php if (!empty($sponsors)): ?>
                        <?php foreach ($sponsors as $sponsor): ?>
                            <div class="sponsor-card" 
                                 data-name="<?= strtolower($sponsor->company_name) ?>"
                                 data-email="<?= strtolower($sponsor->email) ?>"
                                 data-activity="<?= $sponsor->activity_status ?>"
                                 data-created="<?= $sponsor->created_at ?>">
                                <div class="sponsor-header">
                                    <div class="sponsor-avatar">
                                        <?= strtoupper(substr($sponsor->company_name, 0, 2)) ?>
                                    </div>
                                    <div class="sponsor-status <?= strtolower(str_replace(' ', '-', $sponsor->activity_status)) ?>">
                                        <?= $sponsor->activity_status ?>
                                    </div>
                                </div>
                                <div class="sponsor-info">
                                    <h3 class="sponsor-name"><?= htmlspecialchars($sponsor->company_name) ?></h3>
                                    <p class="sponsor-email"><?= htmlspecialchars($sponsor->email) ?></p>
                                    <p class="sponsor-phone"><?= $sponsor->country_code ?> <?= htmlspecialchars($sponsor->phone) ?></p>
                                    <p class="sponsor-joined">
                                        Joined <?= date('M j, Y', strtotime($sponsor->created_at)) ?>
                                    </p>
                                    <?php if ($sponsor->last_login): ?>
                                        <p class="sponsor-last-login">
                                            Last login: <?= date('M j, Y', strtotime($sponsor->last_login)) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="sponsor-actions">
                                    <button class="btn btn-primary btn-sm" onclick="viewSponsor(<?= $sponsor->id ?>)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        View Details
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="contactSponsor(<?= $sponsor->id ?>)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22 6 12 13 2 6"></polyline>
                                        </svg>
                                        Contact
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sponsors">
                            <div class="no-sponsors-content">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                    <path d="M12 18V6"></path>
                                </svg>
                                <h3>No Sponsors Found</h3>
                                <p>There are no sponsors registered in the system yet.</p>
                                <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/sponsorreg'">
                                    Invite Sponsors
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Contact Sponsor Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Contact Sponsor</h3>
                <span class="close-button" onclick="closeContactModal()">&times;</span>
            </div>
            <form id="contactForm" method="POST" action="/unipulse/public/publisher/sponsors/contact">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Enter your message..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeContactModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Publisher/sponsors-app.js?v=<?= time() ?>"></script>
</body>

</html>