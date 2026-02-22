<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sponsorships - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/sponsorships-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'sponsorships'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-handshake"></i> My Sponsorships</h1>
                        <p>Track and manage your sponsorship requests</p>
                    </div>
                    <div class="header-stats">
                        <div class="stat-item pending">
                            <span class="stat-number"><?= $stats['pending'] ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                        <div class="stat-item completed">
                            <span class="stat-number"><?= $stats['completed'] ?></span>
                            <span class="stat-label">Completed</span>
                        </div>
                        <div class="stat-item rejected">
                            <span class="stat-number"><?= $stats['rejected'] ?></span>
                            <span class="stat-label">Not Delivered</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsorships Section -->
        <section class="sponsorships-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Tabs Navigation -->
                <div class="tabs-navigation">
                    <button class="tab-btn active" data-tab="all">
                        All (<?= $stats['total'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="pending">
                        Pending (<?= $stats['pending'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="completed">
                        Completed (<?= $stats['completed'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="rejected">
                        Not Delivered (<?= $stats['rejected'] ?>)
                    </button>
                </div>

                <!-- Tabs Content -->
                <div class="tabs-content">
                    <!-- All Tab -->
                    <div class="tab-pane active" id="all-tab">
                        <?php if (empty($sponsorships)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-handshake fa-4x"></i>
                                </div>
                                <h2>No Sponsorships Yet</h2>
                                <p>You haven't submitted any sponsorship requests yet.</p>
                                <a href="/unipulse/public/sponsor/events" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Browse Events
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($sponsorships as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pending Tab -->
                    <div class="tab-pane" id="pending-tab">
                        <?php if (empty($grouped['pending'])): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-clock fa-4x"></i>
                                </div>
                                <h2>No Pending Requests</h2>
                                <p>You have no pending sponsorship requests at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($grouped['pending'] as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Completed Tab -->
                    <div class="tab-pane" id="completed-tab">
                        <?php if (empty($grouped['completed'])): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-check-double fa-4x"></i>
                                </div>
                                <h2>No Completed Sponsorships</h2>
                                <p>You have no completed sponsorships yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($grouped['completed'] as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Not Delivered Tab -->
                    <div class="tab-pane" id="rejected-tab">
                        <?php if (empty($grouped['rejected'])): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-times-circle fa-4x"></i>
                                </div>
                                <h2>No Undelivered Sponsorships</h2>
                                <p>None of your sponsorships have been marked as not delivered.</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($grouped['rejected'] as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Sponsor/sponsorships-app.js"></script>
</body>

</html>
