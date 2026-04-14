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
                        <p>Track your donations and event participation for each month</p>
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