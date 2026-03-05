<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Sponsors - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <!-- <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/public-style.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .main-container {
            /* Full width container */
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 4rem;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #1E3A8A 0%, #F97316 100%);
            color: white;
            padding: 4rem 2rem;
            width: 100%;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .header-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-left p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Stats Cards in Header */
        .header-stats {
            display: flex;
            gap: 1.5rem;
        }

        .stat-card {
            text-align: center;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            height: 100px;
            width: 140px;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }

        .stat-card p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        /* Search and Filters */
        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .search-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input-wrapper {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Sponsors Grid */
        .sponsors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Sponsor Card */
        .sponsor-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .sponsor-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .sponsor-card-header {
            height: 120px;
            background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
            position: relative;
        }

        .sponsor-card-logo {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #3B82F6;
            border: 4px solid white;
            overflow: hidden;
        }

        .sponsor-card-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sponsor-card-body {
            padding: 3.5rem 1.5rem 1.5rem;
            text-align: center;
        }

        .sponsor-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .sponsor-info {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .sponsor-info-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .sponsor-info-item i {
            color: #3B82F6;
        }

        .sponsor-card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 0.75rem;
        }

        .view-profile-btn, .contact-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .view-profile-btn {
            background: linear-gradient(135deg, #3B82F6, #1E3A8A);
            color: white;
        }

        .view-profile-btn:hover {
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .contact-btn {
            background: white;
            color: #3B82F6;
            border: 2px solid #3B82F6;
        }

        .contact-btn:hover {
            background: #3B82F6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-stats {
                width: 100%;
                justify-content: space-between;
            }

            .stat-card {
                min-width: auto;
                flex: 1;
                padding: 1rem;
            }

            .stat-card h3 {
                font-size: 2rem;
            }

            .page-header {
                padding: 2rem 1.5rem;
            }

            .header-left h1 {
                font-size: 2rem;
            }

            .main-content {
                padding: 1rem;
            }

            .sponsors-grid {
                grid-template-columns: 1fr;
            }

            .search-bar {
                flex-direction: column;
            }

            .search-input-wrapper {
                min-width: 100%;
            }
        }
    </style>
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

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const sponsorsGrid = document.getElementById('sponsorsGrid');

        searchInput.addEventListener('input', function() {
            searchSponsors();
        });

        function searchSponsors() {
            const searchTerm = searchInput.value.toLowerCase();
            const cards = sponsorsGrid.querySelectorAll('.sponsor-card');

            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                const matchesSearch = name.includes(searchTerm);

                if (matchesSearch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show empty state if no results
            const emptyState = sponsorsGrid.querySelector('.empty-state');
            if (visibleCount === 0 && !emptyState) {
                const noResults = document.createElement('div');
                noResults.className = 'empty-state no-results';
                noResults.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>No Sponsors Found</h3>
                    <p>Try adjusting your search criteria.</p>
                `;
                sponsorsGrid.appendChild(noResults);
            } else if (visibleCount > 0) {
                const noResults = sponsorsGrid.querySelector('.no-results');
                if (noResults) noResults.remove();
            }
        }

        function viewSponsorProfile(sponsorId) {
            window.location.href = `/unipulse/public/sponsor/public/${sponsorId}`;
        }

        function contactSponsor(sponsorId, sponsorName) {
            // Navigate to messages page with sponsor info in URL parameters
            window.location.href = `/unipulse/public/publisher/messages?recipient_id=${sponsorId}&recipient_type=sponsor&recipient_name=${encodeURIComponent(sponsorName)}`;
        }
    </script>
</body>
</html>
