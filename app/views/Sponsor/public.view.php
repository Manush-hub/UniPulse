<?php 
// Fetch sponsor details safely
$sponsorId = $sponsor->id ?? 0;
$companyName = $sponsor->company_name ?? 'Sponsor Company';
$email = $sponsor->email ?? '';
$phone = $sponsor->phone ?? '';
$countryCode = $sponsor->country_code ?? '';
$companyType = $sponsorProfile->sponsor_type ?? 'company';
$industry = $sponsorProfile->industry ?? 'Technology';
$companySize = $sponsorProfile->company_size ?? '';
$headline = $sponsorProfile->headline ?? 'Supporting Innovation and Education';
$bio = $sponsorProfile->about ?? 'No description available.';
$mission = $sponsorProfile->mission ?? '';
$address = $sponsorProfile->address ?? '';
$logoUrl = !empty($sponsorProfile->logo_url) ? $sponsorProfile->logo_url : 'https://via.placeholder.com/150';
$coverPhotoUrl = !empty($sponsorProfile->cover_photo_url) ? $sponsorProfile->cover_photo_url : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=cover&w=1200&q=80';

// Social media links
$website = $sponsorProfile->website ?? '';
$facebook = $sponsorProfile->facebook ?? '';
$linkedin = $sponsorProfile->linkedin ?? '';
$twitter = $sponsorProfile->twitter ?? '';
$instagram = $sponsorProfile->instagram ?? '';
$youtube = $sponsorProfile->youtube ?? '';

// Focus areas / preferences (stored as 'interests' in database)
$focusAreas = !empty($sponsorProfile->interests) ? json_decode($sponsorProfile->interests, true) : [];

// Verification status
$isVerified = ($sponsor->verification_status ?? '') === 'verified';

// Contact number
$contactNumber = !empty($phone) ? ($countryCode ? $countryCode . ' ' . $phone : $phone) : '';

// Determine event view URL based on current user type
$currentUser = AuthService::getCurrentUser();
$userType = $currentUser ? strtolower($currentUser['type']) : 'user';
$eventViewBaseUrl = '/unipulse/public/' . $userType . '/eventview';

// Prepare social links array
$socialLinks = [];
if (!empty($website)) $socialLinks['website'] = $website;
if (!empty($facebook)) $socialLinks['facebook'] = $facebook;
if (!empty($instagram)) $socialLinks['instagram'] = $instagram;
if (!empty($linkedin)) $socialLinks['linkedin'] = $linkedin;
if (!empty($twitter)) $socialLinks['twitter'] = $twitter;
if (!empty($youtube)) $socialLinks['youtube'] = $youtube;

// Safety function
function safeOutput($value, $default = '') {
    return !empty($value) ? htmlspecialchars($value) : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($companyName); ?> - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/public-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/UniPulse/public/assets/js/publisher/public-profile.js" defer></script>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/public-style.css">
</head>
<body>
    <?php
    $pageConfig = ['activeNav' => 'sponsors'];
    if ($currentUser && $userType === 'publisher') {
        include_once(__DIR__ . '/../Publisher/components/header.php');
    } elseif ($currentUser && $userType === 'sponsor') {
        include_once(__DIR__ . '/components/header.php');
    } else {
        include_once(__DIR__ . '/../header.php');
    }
    ?>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- Cover Photo Section -->
            <div class="cover-photo-section">
                <div class="cover-photo">
                    <img src="<?= $coverPhotoUrl ?>" alt="Cover Photo">
                </div>
                
                <!-- Profile Avatar positioned to overlap - Left side -->
                <div class="profile-avatar profile-avatar-overlap">
                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($companyName) ?> Logo">
                </div>
            </div>
            
            <!-- Profile Info Below Cover -->
            <div class="profile-info-section">
                <div class="profile-name-email">
                    <h1 class="profile-name"><?= htmlspecialchars($companyName) ?></h1>
                    <?php if(!empty($email)): ?>
                        <p class="profile-email"><?= htmlspecialchars($email) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">
                <!-- Left Sidebar -->
                <aside>
                    <!-- About Section -->
                    <?php if(!empty($bio)): ?>
                        <div class="info-card-highlight">
                            <h2 class="card-title">
                                <i class="fas fa-building"></i> About Company
                            </h2>
                            <p><?= nl2br(htmlspecialchars($bio)) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mission Statement -->
                    <?php if(!empty($mission)): ?>
                    <div class="info-card-highlight">
                        <h2 class="card-title">
                            <i class="fas fa-bullseye"></i> Mission Statement
                        </h2>
                        <p><?= nl2br(htmlspecialchars($mission)) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Sponsor Information -->
                    <div class="info-card">
                        <h2 class="card-title"><i class="fas fa-info-circle"></i> Sponsor Information</h2>
                        
                        <?php if(!empty($headline)): ?>
                        <div class="headline-section">
                            <h4>Headline</h4>
                            <p><?= nl2br(htmlspecialchars($headline)) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($companyType)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Company Type</div>
                                <div class="info-value"><?= htmlspecialchars($companyType) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($industry)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-industry"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Industry</div>
                                <div class="info-value"><?= htmlspecialchars($industry) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($companySize)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Company Size</div>
                                <div class="info-value"><?= htmlspecialchars($companySize) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($address)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?= htmlspecialchars($address) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($contactNumber)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Contact</div>
                                <div class="info-value"><?= htmlspecialchars($contactNumber) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($email)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?= htmlspecialchars($email) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Focus Areas -->
                    <?php if(!empty($focusAreas) && count($focusAreas) > 0): ?>
                    <div class="info-card">
                        <h2 class="card-title"><i class="fas fa-bullseye"></i> Sponsorship Focus Areas</h2>
                        <div class="tags-container">
                            <?php 
                            $interestIcons = [
                                'technology' => 'fas fa-microchip',
                                'education' => 'fas fa-graduation-cap',
                                'innovation' => 'fas fa-lightbulb',
                                'sports' => 'fas fa-futbol',
                                'arts' => 'fas fa-palette',
                                'entrepreneurship' => 'fas fa-rocket',
                                'healthcare' => 'fas fa-heartbeat',
                                'environment' => 'fas fa-leaf',
                                'community' => 'fas fa-hands-helping',
                                'research' => 'fas fa-flask'
                            ];
                            
                            foreach($focusAreas as $area): 
                                $iconClass = $interestIcons[$area] ?? 'fas fa-star';
                                $displayName = htmlspecialchars(ucwords(str_replace('-', ' ', $area)));
                            ?>
                            <span class="tag"><i class="<?= $iconClass ?>"></i> <?= $displayName ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Links -->
                    <?php if(!empty($socialLinks) && count($socialLinks) > 0): ?>
                    <div class="info-card">
                        <h2 class="card-title">Connect</h2>
                        <div class="social-links">
                            <?php 
                            $socialConfig = [
                                'website' => ['icon' => 'fas fa-globe', 'color' => '#64748b', 'bg' => '#f1f5f9', 'label' => 'Website'],
                                'facebook' => ['icon' => 'fab fa-facebook', 'color' => '#1877f2', 'bg' => '#dbeafe', 'label' => 'Facebook'],
                                'instagram' => ['icon' => 'fab fa-instagram', 'color' => '#e4405f', 'bg' => '#fce7f3', 'label' => 'Instagram'],
                                'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => '#0077b5', 'bg' => '#dbeafe', 'label' => 'LinkedIn'],
                                'twitter' => ['icon' => 'fab fa-x-twitter', 'color' => '#000000', 'bg' => '#f1f5f9', 'label' => 'X'],
                                'youtube' => ['icon' => 'fab fa-youtube', 'color' => '#ff0000', 'bg' => '#fee2e2', 'label' => 'YouTube']
                            ];
                            
                            foreach($socialLinks as $platform => $url): 
                                if(!empty($url) && isset($socialConfig[$platform])): 
                                    $config = $socialConfig[$platform];
                            ?>
                            <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="social-link" style="background: <?= $config['bg'] ?>; color: <?= $config['color'] ?>;">
                                <span class="social-icon" style="color: <?= $config['color'] ?>;">
                                    <i class="<?= $config['icon'] ?>"></i>
                                </span>
                                <?= $config['label'] ?>
                            </a>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </aside>
                
                <!-- Right Content -->
                <main>
                    <!-- Content Section with Tabs -->
                    <div class="events-section">
                        <!-- Event Tabs -->
                        <div class="event-tabs">
                            <div class="event-tab active" data-tab="sponsored">
                                <i class="fas fa-handshake"></i>
                                Sponsored Events
                                <?php if(!empty($sponsoredEvents)): ?>
                                <span class="tab-count"><?= count($sponsoredEvents) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <!-- Sponsored Events Content -->
                        <div class="event-tab-content active" id="sponsored-events" style="display: block !important;">
                        <?php if(!empty($sponsoredEvents) && count($sponsoredEvents) > 0): ?>
                            <div class="events-feed">
                                <?php foreach($sponsoredEvents as $event): 
                                    $eventId = $event->id ?? $event['id'];
                                    $eventTitle = htmlspecialchars($event->title ?? $event['title']);
                                    $eventDescription = $event->description ?? $event['description'];
                                    $eventImage = $event->image_url ?? $event['image_url'] ?? '';
                                    $eventLocation = $event->location ?? $event['location'] ?? 'TBA';
                                    $eventCategory = $event->category ?? $event['category'] ?? '';
                                    $startDate = $event->event_date ?? $event['event_date'] ?? null;
                                    $packageName = $event->package_name ?? $event['package_name'] ?? '';
                                    $packageType = $event->package_type ?? $event['package_type'] ?? '';
                                    $publisherName = $event->publisher_name ?? $event['publisher_name'] ?? '';
                                    $publisherLogo = $event->publisher_logo ?? $event['publisher_logo'] ?? 'https://via.placeholder.com/40';
                                    
                                    // Prepare image path
                                    $imagePath = '';
                                    if (!empty($eventImage)) {
                                        $imagePath = strpos($eventImage, 'http') === 0 ? $eventImage : '/UniPulse/public/' . ltrim($eventImage, '/');
                                    }
                                    
                                    // Format date
                                    $formattedDate = $startDate ? date('M d, Y', strtotime($startDate)) : 'TBA';
                                    
                                    // Event status
                                    $eventStatus = '';
                                    if ($startDate) {
                                        $eventStatus = strtotime($startDate) > time() ? 'upcoming' : 'past';
                                    }
                                ?>
                                <div class="feed-event-card">
                                    <!-- Event Header -->
                                    <div class="feed-event-header">
                                        <div class="event-organizer-info">
                                            <img src="<?= htmlspecialchars($publisherLogo) ?>" alt="<?= htmlspecialchars($publisherName) ?>" class="organizer-avatar">
                                            <div class="organizer-details">
                                                <h4 class="organizer-name"><?= htmlspecialchars($publisherName) ?></h4>
                                                <p class="event-date">
                                                    <i class="fas fa-calendar"></i> <?= $formattedDate ?>
                                                    <?php if (!empty($packageName)): ?>
                                                        <span class="sponsorship-badge <?= htmlspecialchars($packageType) ?>">
                                                            <i class="fas fa-star"></i> <?= htmlspecialchars($packageName) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if (!empty($eventStatus)): ?>
                                        <span class="event-status-badge <?= $eventStatus ?>"><?= ucfirst($eventStatus) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Event Title -->
                                    <div class="feed-event-title">
                                        <h3><?= $eventTitle ?></h3>
                                    </div>
                                    
                                    <!-- Event Cover Image -->
                                    <?php if(!empty($imagePath)): ?>
                                    <div class="feed-event-image">
                                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= $eventTitle ?>">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Event Details -->
                                    <div class="feed-event-body">
                                        <?php if(!empty($eventDescription)): ?>
                                        <p class="feed-event-description">
                                            <?= htmlspecialchars(strlen($eventDescription) > 200 ? substr($eventDescription, 0, 200) . '...' : $eventDescription) ?>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <div class="feed-event-meta">
                                            <?php if(!empty($eventLocation)): ?>
                                            <div class="feed-meta-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?= htmlspecialchars($eventLocation) ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if(!empty($eventCategory)): ?>
                                            <div class="feed-meta-item">
                                                <i class="fas fa-tag"></i>
                                                <?= htmlspecialchars(ucwords(str_replace('-', ' ', $eventCategory))) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Event Actions -->
                                    <div class="feed-event-actions">
                                        <button class="feed-action-btn primary" onclick="window.location.href='<?= $eventViewBaseUrl ?>?id=<?= $eventId ?>'">
                                            <i class="fas fa-eye"></i>
                                            View Event
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-alt"></i>
                                <p>No sponsored events yet</p>
                            </div>
                        <?php endif; ?>
                        </div>
                        
                        <!-- News & Updates Content -->
                        <div class="event-tab-content" id="news-events">
                        <?php if(!empty($newsItems) && count($newsItems) > 0): ?>
                            <div class="events-feed">
                                <?php foreach($newsItems as $news): 
                                    $newsId = $news['id'];
                                    $newsTitle = htmlspecialchars($news['title']);
                                    $newsDescription = htmlspecialchars($news['description']);
                                    $newsImage = $news['image_url'] ?? 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=250&fit=crop';
                                    $newsCategory = $news['category'] ?? 'news-article';
                                    $newsDate = !empty($news['published_date']) ? date('F j, Y', strtotime($news['published_date'])) : '';
                                    $newsSource = $news['source'] ?? $companyName;
                                ?>
                                <div class="news-card">
                                    <div class="news-image">
                                        <img src="<?= htmlspecialchars($newsImage) ?>" alt="<?= $newsTitle ?>">
                                        <div class="news-category-badge <?= htmlspecialchars($newsCategory) ?>">
                                            <?= strtoupper(str_replace('-', ' ', htmlspecialchars($newsCategory))) ?>
                                        </div>
                                    </div>
                                    <div class="news-content">
                                        <div class="news-meta">
                                            <span class="news-date"><i class="fas fa-calendar"></i> <?= $newsDate ?></span>
                                            <span class="news-source"><i class="fas fa-building"></i> <?= htmlspecialchars($newsSource) ?></span>
                                        </div>
                                        <h3><?= $newsTitle ?></h3>
                                        <p><?= $newsDescription ?></p>
                                        <div class="news-actions">
                                            <?php if(!empty($news['url'])): ?>
                                            <a href="<?= htmlspecialchars($news['url']) ?>" target="_blank" class="read-full-article">
                                                Read Full Article <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-newspaper"></i>
                                <p>No news updates yet</p>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    <?php include_once(__DIR__ . '/../components/footer.php'); ?>
</body>
</html>
