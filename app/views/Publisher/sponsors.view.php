<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Sponsors - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/sponsor-style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    $pageConfig = ['activeNav' => 'sponsors'];
    include __DIR__ . '/components/header.php'; 
    ?>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <h1>
                        <i class="fas fa-handshake"></i>
                        Browse Sponsors
                    </h1>
                    <p>Connect with potential sponsors for your events and initiatives</p>
                </div>
                
                <?php if (!empty($stats)): ?>
                <div class="header-stats">
                    <div class="stat-card">
                        <h3><?= $stats->total_sponsors ?? 0 ?></h3>
                        <p>Total Sponsors</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= $stats->new_sponsors ?? 0 ?></h3>
                        <p>New This Month</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content">
            <!-- Search -->
            <div class="search-section">
                <div class="search-bar">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search sponsors by company name...">
                    </div>
                </div>
            </div>

            <!-- Sponsors Grid -->
            <div class="sponsors-grid" id="sponsorsGrid">
            <?php if (!empty($sponsors) && count($sponsors) > 0): ?>
                <?php foreach($sponsors as $sponsor): ?>
                <div class="sponsor-card" 
                     data-name="<?= strtolower(htmlspecialchars($sponsor->company_name ?? '')) ?>"
                     onclick="viewSponsorProfile(<?= $sponsor->id ?>)">
                    <div class="sponsor-card-header" style="<?= !empty($sponsor->cover_photo_url) ? 'background: url(' . htmlspecialchars($sponsor->cover_photo_url) . ') center/cover; background-size: cover;' : '' ?>">
                        <div class="sponsor-card-logo">
                            <?php if (!empty($sponsor->logo_url)): ?>
                                <img src="<?= htmlspecialchars($sponsor->logo_url) ?>" alt="<?= htmlspecialchars($sponsor->company_name) ?>">
                            <?php else: ?>
                                <?= strtoupper(substr($sponsor->company_name ?? 'S', 0, 2)) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="sponsor-card-body">
                        <h3 class="sponsor-name"><?= htmlspecialchars($sponsor->company_name ?? 'Unknown Sponsor') ?></h3>
                        <div class="sponsor-info">
                            <?php if (!empty($sponsor->email)): ?>
                            <div class="sponsor-info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars($sponsor->email) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($sponsor->phone)): ?>
                            <div class="sponsor-info-item">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars(($sponsor->country_code ?? '') . ' ' . $sponsor->phone) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="sponsor-info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Joined <?= date('M j, Y', strtotime($sponsor->created_at)) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="sponsor-card-footer">
                        <button class="view-profile-btn" onclick="event.stopPropagation(); viewSponsorProfile(<?= $sponsor->id ?>)">
                            <i class="fas fa-user"></i>
                            View Profile
                        </button>
                        <button class="contact-btn" onclick="event.stopPropagation(); contactSponsor(<?= $sponsor->id ?>, '<?= htmlspecialchars($sponsor->company_name ?? 'Sponsor', ENT_QUOTES) ?>')">
                            <i class="fas fa-envelope"></i>
                            Contact
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h3>No Sponsors Found</h3>
                    <p>There are currently no sponsors registered in the system.</p>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Publisher/sponsors-app.js"></script>
</body>
</html>
