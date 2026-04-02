<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Sponsor Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
    <style>
        .password-requests-mini {
            padding: 1.25rem 0;
        }

        .password-requests-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
        }

        .password-requests-box h3 {
            margin-bottom: 0.75rem;
            color: #1f2937;
        }

        .password-request-item {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 0.65rem;
            background: #f9fafb;
        }

        .password-request-item:last-child {
            margin-bottom: 0;
        }

        .password-request-top {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 0.45rem;
            flex-wrap: wrap;
        }

        .password-request-status {
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-new { background: #dbeafe; color: #1e40af; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .password-request-reply {
            margin-top: 0.45rem;
            padding: 0.55rem 0.65rem;
            border-left: 3px solid #1e3a8a;
            background: #eff6ff;
            border-radius: 6px;
            white-space: pre-wrap;
        }
    </style>
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
                    <a href="analytics.html" class="view-all">Detailed Reports</a>
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
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Sponsor/dashboard-app.js"></script>
</body>

</html>