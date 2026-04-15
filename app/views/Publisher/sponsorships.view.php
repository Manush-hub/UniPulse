<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Requests - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/sponsorships-style.css">
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
                        <h1><i class="fas fa-handshake"></i> Sponsorship Requests</h1>
                        <p>Manage sponsorship requests for your events</p>
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
                            <span class="stat-label">Not Received</span>
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
                    <button class="tab-btn active" data-tab="pending">
                        Pending (<?= $stats['pending'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="completed">
                        Completed (<?= $stats['completed'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="rejected">
                        Not Received (<?= $stats['rejected'] ?>)
                    </button>
                    <button class="tab-btn" data-tab="all">
                        All (<?= $stats['total'] ?>)
                    </button>
                </div>

                <!-- Tabs Content -->
                <div class="tabs-content">
                    <!-- Pending Tab -->
                    <div class="tab-pane active" id="pending-tab">
                        <?php if (empty($grouped['pending'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>No pending sponsorship requests</p>
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
                                <i class="fas fa-check-double fa-3x"></i>
                                <p>No completed sponsorships</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($grouped['completed'] as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Not Received Tab -->
                    <div class="tab-pane" id="rejected-tab">
                        <?php if (empty($grouped['rejected'])): ?>
                            <div class="empty-state">
                                <i class="fas fa-times-circle fa-3x"></i>
                                <p>No payments marked as not received</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($grouped['rejected'] as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- All Tab -->
                    <div class="tab-pane" id="all-tab">
                        <?php if (empty($sponsorships)): ?>
                            <div class="empty-state">
                                <i class="fas fa-handshake fa-3x"></i>
                                <p>No sponsorship requests yet</p>
                            </div>
                        <?php else: ?>
                            <div class="sponsorships-grid">
                                <?php foreach ($sponsorships as $sponsorship): ?>
                                    <?php include __DIR__ . '/components/sponsorship-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Not Received Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Payment Not Received</h2>
                <button class="close-btn" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Please provide a reason for marking this payment as not received:</p>
                <textarea id="rejectReason" rows="4" placeholder="Enter reason..." required></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmReject()">Confirm Not Received</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Publisher/sponsorships-app.js"></script>
</body>

</html>
