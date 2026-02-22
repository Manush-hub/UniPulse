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
                        <p>Manage your event sponsorship requests</p>
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
                            <div class="sponsorship-card" data-status="<?= $sponsorship['status'] ?>">
                                <div class="card-header">
                                    <span class="status-badge status-<?= $sponsorship['status'] ?>">
                                        <?= ucfirst($sponsorship['status']) ?>
                                    </span>
                                    <span class="package-type package-<?= $sponsorship['package_type'] ?>">
                                        <?= ucfirst($sponsorship['package_type']) ?>
                                    </span>
                                </div>
                                
                                <div class="card-body">
                                    <h3 class="event-title"><?= htmlspecialchars($sponsorship['event_title']) ?></h3>
                                    
                                    <div class="sponsorship-details">
                                        <div class="detail-item">
                                            <i class="fas fa-box"></i>
                                            <span><?= htmlspecialchars($sponsorship['package_name']) ?> Package</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?= date('M d, Y', strtotime($sponsorship['event_date'])) ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>
                                                <?php if ($sponsorship['university_name']): ?>
                                                    <?= htmlspecialchars($sponsorship['university_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($sponsorship['venue_name'] ?? $sponsorship['city'] ?? 'Location TBA') ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <i class="fas fa-building"></i>
                                            <span><?= htmlspecialchars($sponsorship['organizer_name']) ?></span>
                                        </div>
                                        
                                        <div class="detail-item amount">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <span>LKR <?= number_format($sponsorship['amount'], 2) ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span>Submitted: <?= date('M d, Y', strtotime($sponsorship['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($sponsorship['payment_proof']): ?>
                                        <div class="payment-proof-indicator">
                                            <i class="fas fa-file-invoice"></i>
                                            <span>Payment receipt attached</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-footer">
                                    <a href="/unipulse/public/sponsor/sponsorship/detail/<?= $sponsorship['id'] ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    <?php if ($sponsorship['status'] === 'pending'): ?>
                                        <span class="status-text">
                                            <i class="fas fa-hourglass-half"></i> Awaiting approval
                                        </span>
                                    <?php elseif ($sponsorship['status'] === 'approved'): ?>
                                        <span class="status-text approved">
                                            <i class="fas fa-check-circle"></i> Approved
                                        </span>
                                    <?php elseif ($sponsorship['status'] === 'completed'): ?>
                                        <span class="status-text completed">
                                            <i class="fas fa-check-double"></i> Completed
                                        </span>
                                    <?php elseif ($sponsorship['status'] === 'rejected'): ?>
                                        <span class="status-text rejected">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>

</html>
