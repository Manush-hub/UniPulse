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
    <style>
        /* Remove gap between header and content */
        body {
            margin: 0;
            padding: 0;
        }
        
        .navigation-bar {
            margin-top: 0;
        }
        
        .container {
            margin-top: 0;
        }
        
        /* Gallery Carousel Styles */
        .gallery-carousel-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 0;
        }
        
        .gallery-carousel {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 12px;
            background: #f8fafc;
        }
        
        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .carousel-item.active {
            opacity: 1;
            z-index: 1;
        }
        
        .carousel-image-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .carousel-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }
        
        .carousel-nav:hover {
            background: rgba(0, 0, 0, 0.8);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            transform: translateY(-50%) scale(1.1);
        }
        
        .carousel-prev {
            left: 15px;
        }
        
        .carousel-next {
            right: 15px;
        }
        
        .carousel-nav i {
            color: #ffffff;
            font-size: 18px;
        }
        
        .carousel-indicators {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        
        /* Hide navigation for single image galleries */
        .gallery-carousel-container.single-image .carousel-nav,
        .gallery-carousel-container.single-image .carousel-indicators {
            display: none;
        }
        
        /* News Card Styles */
        .news-card {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .news-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .news-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .news-category-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .news-category-badge.press-release {
            background: rgba(59, 130, 246, 0.9);
            color: white;
        }
        
        .news-category-badge.award {
            background: rgba(234, 179, 8, 0.9);
            color: white;
        }
        
        .news-category-badge.news-article {
            background: rgba(139, 92, 246, 0.9);
            color: white;
        }
        
        .news-content {
            padding: 20px;
        }
        
        .news-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #64748b;
        }
        
        .news-content h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .news-content p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .news-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }
        
        .read-full-article {
            color: #3b82f6;
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
        }
        
        .read-full-article:hover {
            color: #2563eb;
        }
        
        /* Sponsorship Badge Styles */
        .sponsorship-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 8px;
        }
        
        .sponsorship-badge.bronze {
            background: linear-gradient(135deg, #cd7f32 0%, #b87333 100%);
            color: white;
        }
        
        .sponsorship-badge.silver {
            background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
            color: #333;
        }
        
        .sponsorship-badge.gold {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
        }
        
        .sponsorship-badge.platinum {
            background: linear-gradient(135deg, #e5e4e2 0%, #b9b9b9 100%);
            color: #333;
        }
        
        .sponsorship-badge.custom {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
        }
        
        .event-status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .event-status-badge.upcoming {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .event-status-badge.past {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .gallery-carousel-container {
                max-width: 100%;
            }
            
            .carousel-nav {
                width: 45px;
                height: 45px;
            }
            
            .carousel-prev {
                left: 10px;
            }
            
            .carousel-next {
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include_once(__DIR__ . '/../header.php'); ?>
    
    <!-- Back Navigation -->
    <nav class="navigation-bar">
        <div class="container-nav">
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Sponsors
            </a>
        </div>
    </nav>
    
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
    
    <?php include_once(__DIR__ . '/../footer.php'); ?>
</body>
</html>
