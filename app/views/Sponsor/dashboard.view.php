<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Sponsor Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
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
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalSponsorships">8</span>
                                <span class="stat-label">Active Sponsorships</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="pendingRequests">5</span>
                                <span class="stat-label">Pending Requests</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalInvestment">LKR 4,200</span>
                                <span class="stat-label">Total Investment</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/sponsor/events?view=sponsor'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Find Events to Sponsor
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Messages -->
        <section class="recent-messages">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Messages from Publishers</h2>
                    <a href="/unipulse/public/sponsor/messages" class="view-all">View All</a>
                </div>
                <div class="messages-container">
                    <?php if (isset($recent_messages) && !empty($recent_messages)): ?>
                        <?php foreach ($recent_messages as $message): ?>
                            <div class="message-card <?= !$message->is_read ? 'unread' : '' ?>" onclick="window.location.href='/unipulse/public/sponsor/messages/details/<?= $message->id ?>'">
                                <div class="message-header">
                                    <div class="sender-info">
                                        <h4><?= htmlspecialchars($message->sender_name) ?></h4>
                                        <span class="message-date"><?= date('M j, Y g:i A', strtotime($message->created_at)) ?></span>
                                    </div>
                                    <?php if (!$message->is_read): ?>
                                        <span class="unread-indicator"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="message-preview">
                                    <h5><?= htmlspecialchars($message->subject) ?></h5>
                                    <p><?= htmlspecialchars(substr($message->message, 0, 150)) ?><?= strlen($message->message) > 150 ? '...' : '' ?></p>
                                </div>
                                <div class="message-footer">
                                    <span class="message-type">From: <?= ucfirst($message->from_user_type) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            <div class="no-messages-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <h3>No Messages Yet</h3>
                            <p>When publishers send you messages, they'll appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Sponsorship Requests -->
        <section class="sponsorship-requests">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Sponsorship Requests</h2>
                    <a href="sponsorship-requests.html" class="view-all">View All</a>
                </div>
                <div class="requests-table" id="requestsTable">
                    <!-- Requests will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Active Sponsorships -->
        <section class="active-sponsorships">
            <div class="container">
                <div class="section-header">
                    <h2>Your Active Sponsorships</h2>
                    <a href="current-sponsorships.html" class="view-all">View All</a>
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