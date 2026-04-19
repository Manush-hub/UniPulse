<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/sponsorships-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/donations-style.css">
</head>

<body>
    <?php
    $pageConfig = ['activeNav' => 'donations'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-container">
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-hand-holding-heart"></i> Donations</h1>
                        <p>Track donations received for your events</p>
                    </div>
                    <div class="header-stats">
                        <div class="stat-item pending">
                            <span class="stat-number"><?= $stats['pending'] ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                        <div class="stat-item completed">
                            <span class="stat-number"><?= $stats['accepted'] ?></span>
                            <span class="stat-label">Accepted</span>
                        </div>
                        <div class="stat-item rejected">
                            <span class="stat-number"><?= $stats['rejected'] ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sponsorships-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>

                <div class="tabs-navigation">
                    <button class="tab-btn active" data-tab="pending">
                        Pending (<?= $stats['pending'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="accepted">
                        Accepted (<?= $stats['accepted'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="rejected">
                        Rejected (<?= $stats['rejected'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="all">
                        All (<?= $stats['total'] ?>)
                    </button>
                </div>

                <div class="tabs-content">
                    <div class="tab-pane active" id="pending-tab">
                        <?php if (empty($grouped['pending'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>No pending donations</p>
                            </div>
                        <?php else: ?>
                            <?php $tableDonations = $grouped['pending']; ?>
                            <?php include __DIR__ . '/components/donation-table.php'; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane" id="accepted-tab">
                        <?php if (empty($grouped['accepted'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-check-double fa-3x"></i>
                                <p>No accepted donations</p>
                            </div>
                        <?php else: ?>
                            <?php $tableDonations = $grouped['accepted']; ?>
                            <?php include __DIR__ . '/components/donation-table.php'; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane" id="rejected-tab">
                        <?php if (empty($grouped['rejected'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-times-circle fa-3x"></i>
                                <p>No rejected donations</p>
                            </div>
                        <?php else: ?>
                            <?php $tableDonations = $grouped['rejected']; ?>
                            <?php include __DIR__ . '/components/donation-table.php'; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane" id="all-tab">
                        <?php if (empty($donations)): ?>
                            <div class="empty-state">
                                <i class="fas fa-hand-holding-heart fa-3x"></i>
                                <p>No donations yet</p>
                            </div>
                        <?php else: ?>
                            <?php $tableDonations = $donations; ?>
                            <?php include __DIR__ . '/components/donation-table.php'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Publisher/donations-app.js"></script>
</body>

</html>