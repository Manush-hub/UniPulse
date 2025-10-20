<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Find Events to Sponsor</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/browse-events-style.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1>Find Events to Sponsor</h1>
                    <p>Discover upcoming university events seeking sponsorship opportunities</p>
                </div>
            </div>
        </section>

        <!-- Filters Section -->
        <section class="filters-section">
            <div class="container">
                <div class="filters-wrapper">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search events..." 
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        <button type="button" id="searchBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="academic" <?= ($filters['category'] ?? '') === 'academic' ? 'selected' : '' ?>>Academic</option>
                            <option value="sports" <?= ($filters['category'] ?? '') === 'sports' ? 'selected' : '' ?>>Sports</option>
                            <option value="cultural" <?= ($filters['category'] ?? '') === 'cultural' ? 'selected' : '' ?>>Cultural</option>
                            <option value="technology" <?= ($filters['category'] ?? '') === 'technology' ? 'selected' : '' ?>>Technology</option>
                            <option value="social" <?= ($filters['category'] ?? '') === 'social' ? 'selected' : '' ?>>Social</option>
                            <option value="workshop" <?= ($filters['category'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                            <option value="business" <?= ($filters['category'] ?? '') === 'business' ? 'selected' : '' ?>>Business</option>
                            <option value="music" <?= ($filters['category'] ?? '') === 'music' ? 'selected' : '' ?>>Music</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <select id="universityFilter">
                            <option value="">All Universities</option>
                            <option value="university-of-moratuwa" <?= ($filters['university'] ?? '') === 'university-of-moratuwa' ? 'selected' : '' ?>>University of Moratuwa</option>
                            <option value="university-of-peradeniya" <?= ($filters['university'] ?? '') === 'university-of-peradeniya' ? 'selected' : '' ?>>University of Peradeniya</option>
                            <option value="university-of-colombo" <?= ($filters['university'] ?? '') === 'university-of-colombo' ? 'selected' : '' ?>>University of Colombo</option>
                            <option value="university-of-kelaniya" <?= ($filters['university'] ?? '') === 'university-of-kelaniya' ? 'selected' : '' ?>>University of Kelaniya</option>
                            <option value="sabaragamuwa-university" <?= ($filters['university'] ?? '') === 'sabaragamuwa-university' ? 'selected' : '' ?>>Sabaragamuwa University</option>
                        </select>
                    </div>
                    
                    <button type="button" id="clearFilters" class="btn btn-secondary">Clear Filters</button>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section class="events-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php elseif (empty($events)): ?>
                    <div class="no-events">
                        <div class="no-events-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <h3>No Events Available</h3>
                        <p>There are currently no events seeking sponsors that match your criteria.</p>
                        <p>Try adjusting your filters or check back later for new opportunities.</p>
                    </div>
                <?php else: ?>
                    <div class="events-grid" id="eventsGrid">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <div class="event-image">
                                    <?php if (!empty($event->cover_image)): ?>
                                        <img src="<?= htmlspecialchars($event->cover_image) ?>" alt="<?= htmlspecialchars($event->title) ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21,15 16,10 5,21"></polyline>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="event-category"><?= ucfirst(htmlspecialchars($event->category)) ?></div>
                                </div>
                                
                                <div class="event-content">
                                    <h3 class="event-title"><?= htmlspecialchars($event->title) ?></h3>
                                    <p class="event-university"><?= htmlspecialchars($event->university_name) ?></p>
                                    
                                    <div class="event-details">
                                        <div class="detail-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <span><?= date('M j, Y', strtotime($event->event_date)) ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12,6 12,12 16,14"></polyline>
                                            </svg>
                                            <span><?= date('h:i A', strtotime($event->event_time)) ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?= htmlspecialchars($event->location) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="event-description">
                                        <p><?= htmlspecialchars(substr($event->description, 0, 120)) . (strlen($event->description) > 120 ? '...' : '') ?></p>
                                    </div>
                                    
                                    <div class="event-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Participants:</span>
                                            <span class="stat-value"><?= htmlspecialchars($event->participants) ?>/<?= htmlspecialchars($event->max_participants) ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Organizer:</span>
                                            <span class="stat-value"><?= htmlspecialchars($event->organizer) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="event-actions">
                                        <button class="btn btn-primary sponsor-btn" data-event-id="<?= $event->id ?>">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                                <line x1="8" y1="12" x2="16" y2="12"></line>
                                            </svg>
                                            Sponsor This Event
                                        </button>
                                        <button class="btn btn-secondary view-details-btn" data-event-id="<?= $event->id ?>">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['category']) ? '&category=' . urlencode($filters['category']) : '' ?><?= !empty($filters['university']) ? '&university=' . urlencode($filters['university']) : '' ?>" class="pagination-btn">Previous</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['category']) ? '&category=' . urlencode($filters['category']) : '' ?><?= !empty($filters['university']) ? '&university=' . urlencode($filters['university']) : '' ?>" 
                                   class="pagination-btn <?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['category']) ? '&category=' . urlencode($filters['category']) : '' ?><?= !empty($filters['university']) ? '&university=' . urlencode($filters['university']) : '' ?>" class="pagination-btn">Next</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../Components/footer.php'; ?>

    <!-- JavaScript -->
    <script src="/unipulse/public/assets/js/Sponsor/browse-events-app.js"></script>
</body>

</html>