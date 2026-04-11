<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Sponsor Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Sponsor_dashboard.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <?php
                        $welcomeNameRaw = $_SESSION['user_name'] ?? ($user['name'] ?? ($user['company_name'] ?? 'Sponsor'));
                        $welcomeName = trim((string) $welcomeNameRaw);
                        if ($welcomeName !== '' && $welcomeName === strtolower($welcomeName)) {
                            $welcomeName = ucwords($welcomeName);
                        }
                        ?>
                        <h1>Welcome back, <span id="welcomeUsername"><?= htmlspecialchars($welcomeName) ?></span>! 👋</h1>
                        <p>Manage your sponsorships and discover new opportunities to support university events.</p>
                    </div>
                    <div class="quick-stats">
                        <div class="stat-item">
                            <span class="stat-number" id="totalSponsorships">8</span>
                            <span class="stat-label">Active Sponsorships</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="pendingRequests">5</span>
                            <span class="stat-label">Pending Sponsorships</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="totalInvestment">LKR 4,200</span>
                            <span class="stat-label">Total Investment</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Active Sponsorships -->
        <section class="active-sponsorships">
            <div class="container">
                <div class="section-header">
                    <h2>Your Active Sponsorships</h2>
                    <a href="/unipulse/public/sponsor/sponsorships" class="view-all">View All</a>
                </div>
                <div class="sponsorships-grid" id="sponsorshipsGrid">
                    <!-- Active sponsorships will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Performance Overview -->
        <section class="performance-overview">
            <div class="container">
                <div class="section-header">
                    <h2>Performance Overview</h2>
                </div>
                <div class="performance-cards">
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #e0f2fe;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0369a1"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>24,589</h3>
                            <p>Total Audience Reach</p>
                        </div>
                    </div>
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #f0fdf4;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#15803d"
                                stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>4.8%</h3>
                            <p>Average Engagement Rate</p>
                        </div>
                    </div>
                    <div class="performance-card">
                        <div class="performance-icon" style="background-color: #ffedd5;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ea580c"
                                stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="performance-content">
                            <h3>3.2x</h3>
                            <p>Average ROI</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Monthly Evolution Button -->
        <section class="monthly-evolution-btn-section">
            <div class="container">
                <a href="/unipulse/public/sponsor/dashboard/monthlyEvaluation" class="evolution-btn">
                    <div class="evolution-btn-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"></path>
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        </svg>
                    </div>
                    <div class="evolution-btn-content">
                        <h3>View Monthly Evolution Report</h3>
                        <p>Track your volunteering, donations, and events participation for each month</p>
                    </div>
                    <div class="evolution-btn-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Sponsor/dashboard-app.js"></script>
</body>

</html>