<?php 
// Fetch organization details safely
$publisherId = $publisher->id ?? 0;
$orgName = $publisher->society_name ?? 'Organization';
$email = $publisher->email ?? '';
$phone = $publisher->phone ?? '';
$countryCode = $publisher->country_code ?? '';
$university = $publisher->university ?? '';
$faculty = $publisher->faculty ?? '';
$orgType = $publisherProfile->org_type ?? 'student-club';
$headline = $publisherProfile->headline ?? 'University Organization';
$bio = $publisherProfile->bio ?? 'No description available.';
$mission = $publisherProfile->mission ?? '';
$address = $publisherProfile->address ?? '';
$establishedYear = $publisherProfile->established_year ?? '';
$memberCount = $publisherProfile->member_count ?? 0;
$logoUrl = !empty($publisherProfile->logo_url) ? $publisherProfile->logo_url : 'https://via.placeholder.com/150';
$coverPhotoUrl = !empty($publisherProfile->cover_photo_url) ? $publisherProfile->cover_photo_url : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=400&fit=crop';

// Social media links
$website = $publisherProfile->website ?? '';
$facebook = $publisherProfile->facebook ?? '';
$linkedin = $publisherProfile->linkedin ?? '';
$twitter = $publisherProfile->twitter ?? '';
$instagram = $publisherProfile->instagram ?? '';
$discord = $publisherProfile->discord ?? '';
$youtube = $publisherProfile->youtube ?? '';

// Preferences
$preferences = !empty($publisherProfile->preferences) ? json_decode($publisherProfile->preferences, true) : [];

// Verification status
$isVerified = ($publisher->approval_status ?? '') === 'approved';

// Contact number
$contactNumber = !empty($phone) ? ($countryCode ? $countryCode . ' ' . $phone : $phone) : '';

// Prepare social links array
$socialLinks = [];
if (!empty($website)) $socialLinks['website'] = $website;
if (!empty($facebook)) $socialLinks['facebook'] = $facebook;
if (!empty($instagram)) $socialLinks['instagram'] = $instagram;
if (!empty($linkedin)) $socialLinks['linkedin'] = $linkedin;
if (!empty($twitter)) $socialLinks['twitter'] = $twitter;
if (!empty($youtube)) $socialLinks['youtube'] = $youtube;
if (!empty($discord)) $socialLinks['discord'] = $discord;

// Merge events
$events = array_merge($upcomingEvents ?? [], $pastEvents ?? []);

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
    <title><?php echo $orgName; ?> - UniPulse</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/public-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/UniPulse/public/assets/js/publisher/public-profile.js" defer></script>
    <style>
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
                Back to Events
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
                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($orgName) ?> Logo">
                </div>
            </div>
            
            <!-- Profile Info Below Cover -->
            <div class="profile-info-section">
                <div class="profile-name-email">
                    <h1 class="profile-name"><?= htmlspecialchars($orgName) ?></h1>
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
                    <!-- Headline Section -->
                    <?php if(!empty($bio)): ?>
                        <div class="info-card-highlight">
                            <h2 class="card-title">
                                <i class="fas fa-bullseye"></i> About Organization
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
                    
                    <!-- Organization Info -->
                    <div class="info-card">
                        <h2 class="card-title"><i class="fas fa-info-circle"></i> Organization Info</h2>
                        
                        <?php if(!empty($headline)): ?>
                        <div class="headline-section">
                            <h4>Headline</h4>
                            <p><?= nl2br(htmlspecialchars($headline)) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($orgType)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Type</div>
                                <div class="info-value"><?= htmlspecialchars(ucwords(str_replace('-', ' ', $orgType))) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($university)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">University</div>
                                <div class="info-value"><?= htmlspecialchars($university) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($faculty)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Faculty</div>
                                <div class="info-value"><?= htmlspecialchars($faculty) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($establishedYear)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Established</div>
                                <div class="info-value"><?= htmlspecialchars($establishedYear) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($memberCount)): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Members</div>
                                <div class="info-value"><?= number_format($memberCount) ?></div>
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
                                <div class="info-label">Official Email</div>
                                <div class="info-value"><?= htmlspecialchars($email) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Preferences -->
                    <?php if(!empty($preferences) && count($preferences) > 0): ?>
                    <div class="info-card">
                        <h2 class="card-title">Interests</h2>
                        <div class="tags-container">
                            <?php foreach($preferences as $pref): ?>
                            <span class="tag"><?= htmlspecialchars(ucwords(str_replace('-', ' ', $pref))) ?></span>
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
                                'youtube' => ['icon' => 'fab fa-youtube', 'color' => '#ff0000', 'bg' => '#fee2e2', 'label' => 'YouTube'],
                                'discord' => ['icon' => 'fab fa-discord', 'color' => '#5865f2', 'bg' => '#e0e7ff', 'label' => 'Discord']
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
                    <!-- Events Section with Tabs -->
                    <div class="events-section">
                        <!-- <div class="section-header">
                            <h2 class="section-title-large">
                                <i class="fas fa-calendar-alt"></i>
                                Events
                            </h2>
                        </div> -->
                        
                        <!-- Event Tabs -->
                        <div class="event-tabs">
                            <button class="event-tab" data-tab="gallery" onclick="switchEventTab('gallery')">
                                <i class="fas fa-images"></i>
                                Photo Gallery
                                <?php if(!empty($galleries)): ?>
                                <span class="tab-count"><?= count($galleries) ?></span>
                                <?php endif; ?>
                            </button>
                            <button class="event-tab active" data-tab="upcoming" onclick="switchEventTab('upcoming')">
                                <i class="fas fa-calendar-check"></i>
                                Upcoming Events
                                <?php if(!empty($upcomingEvents)): ?>
                                <span class="tab-count"><?= count($upcomingEvents) ?></span>
                                <?php endif; ?>
                            </button>
                            <button class="event-tab" data-tab="past" onclick="switchEventTab('past')">
                                <i class="fas fa-history"></i>
                                Past Events
                                <?php if(!empty($pastEvents)): ?>
                                <span class="tab-count"><?= count($pastEvents) ?></span>
                                <?php endif; ?>
                            </button>
                        </div>
                        
                        <!-- Upcoming Events Content -->
                        <div class="event-tab-content active" id="upcoming-events">
                            <?php if(!empty($upcomingEvents) && count($upcomingEvents) > 0): ?>
                                <div class="events-feed">
                                    <?php foreach($upcomingEvents as $event): ?>
                                    <div class="feed-event-card">
                                        <!-- Event Header -->
                                        <div class="feed-event-header">
                                            <div class="event-organizer-info">
                                                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($orgName) ?>" class="organizer-avatar">
                                                <div class="organizer-details">
                                                    <h4 class="organizer-name"><?= htmlspecialchars($orgName) ?></h4>
                                                    <p class="event-post-time">
                                                        <i class="far fa-calendar"></i> 
                                                        <?= date('F d, Y', strtotime($event->event_date)) ?>
                                                        <?php if(!empty($event->event_time)): ?>
                                                        at <?= htmlspecialchars($event->event_time) ?>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Event Title -->
                                        <div class="feed-event-title">
                                            <h3><?= htmlspecialchars($event->title) ?></h3>
                                        </div>
                                        
                                        <!-- Event Cover Image -->
                                        <?php if(!empty($event->image_url)): 
                                            $imagePath = '/UniPulse/public/' . ltrim($event->image_url, '/');
                                        ?>
                                        <div class="feed-event-image">
                                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($event->title) ?>">
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Event Details -->
                                        <div class="feed-event-body">
                                            <?php if(!empty($event->description)): ?>
                                            <p class="feed-event-description">
                                                <?= htmlspecialchars(strlen($event->description) > 200 ? substr($event->description, 0, 200) . '...' : $event->description) ?>
                                            </p>
                                            <?php endif; ?>
                                            
                                            <div class="feed-event-meta">
                                                <?php if(!empty($event->location)): ?>
                                                <div class="feed-meta-item">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span><?= htmlspecialchars($event->location) ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <?php if(!empty($event->category)): ?>
                                                <div class="feed-meta-item">
                                                    <i class="fas fa-tag"></i>
                                                    <span><?= htmlspecialchars($event->category) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Event Actions -->
                                        <div class="feed-event-actions">
                                            <button class="feed-action-btn primary" onclick="window.location.href='/unipulse/public/event/view?id=<?= $event->id ?>'">
                                                <i class="fas fa-eye"></i>
                                                View Event
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No upcoming events</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Past Events Content -->
                        <div class="event-tab-content" id="past-events">
                            <?php if(!empty($pastEvents) && count($pastEvents) > 0): ?>
                                <div class="events-feed">
                                    <?php foreach($pastEvents as $event): ?>
                                    <div class="feed-event-card past-event">
                                        <!-- Event Header -->
                                        <div class="feed-event-header">
                                            <div class="event-organizer-info">
                                                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($orgName) ?>" class="organizer-avatar">
                                                <div class="organizer-details">
                                                    <h4 class="organizer-name"><?= htmlspecialchars($orgName) ?></h4>
                                                    <p class="event-post-time">
                                                        <i class="far fa-calendar"></i> 
                                                        <?= date('F d, Y', strtotime($event->event_date)) ?>
                                                        <?php if(!empty($event->event_time)): ?>
                                                        at <?= htmlspecialchars($event->event_time) ?>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Event Title -->
                                        <div class="feed-event-title">
                                            <h3><?= htmlspecialchars($event->title) ?></h3>
                                        </div>
                                        
                                        <!-- Event Cover Image -->
                                        <?php if(!empty($event->cover_image)): 
                                            $imagePath = '/UniPulse/public/' . ltrim($event->cover_image, '/');
                                        ?>
                                        <div class="feed-event-image">
                                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($event->title) ?>">
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Event Details -->
                                        <div class="feed-event-body">
                                            <?php if(!empty($event->description)): ?>
                                            <p class="feed-event-description">
                                                <?= htmlspecialchars(strlen($event->description) > 200 ? substr($event->description, 0, 200) . '...' : $event->description) ?>
                                            </p>
                                            <?php endif; ?>
                                            
                                            <div class="feed-event-meta">
                                                <?php if(!empty($event->location)): ?>
                                                <div class="feed-meta-item">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span><?= htmlspecialchars($event->location) ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <?php if(!empty($event->category)): ?>
                                                <div class="feed-meta-item">
                                                    <i class="fas fa-tag"></i>
                                                    <span><?= htmlspecialchars($event->category) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Event Actions -->
                                        <div class="feed-event-actions">
                                            <button class="feed-action-btn secondary" onclick="window.location.href='/unipulse/public/event/view?id=<?= $event->id ?>'">
                                                <i class="fas fa-eye"></i>
                                                View Event
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No past events</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Photo Gallery Content -->
                        <div class="event-tab-content" id="gallery-events">
                        <?php if(!empty($galleries) && count($galleries) > 0): ?>
                            <div class="gallery-feed">
                                <?php foreach($galleries as $gallery): 
                                    $galleryId = $gallery['id'];
                                    $galleryTitle = htmlspecialchars($gallery['title']);
                                    $galleryDescription = htmlspecialchars($gallery['description']);
                                    $images = $gallery['images'];
                                ?>
                                <div class="feed-gallery-card">
                                    <!-- Gallery Header -->
                                    <div class="feed-gallery-header">
                                        <div class="gallery-info">
                                            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($orgName) ?>" class="organizer-avatar">
                                            <div class="gallery-details">
                                                <h4 class="organizer-name"><?= htmlspecialchars($orgName) ?></h4>
                                                <p class="gallery-subtitle">
                                                    <i class="fas fa-images"></i> 
                                                    <?= count($images) ?> <?= count($images) == 1 ? 'photo' : 'photos' ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Gallery Title -->
                                    <div class="feed-gallery-title">
                                        <h3><?= $galleryTitle ?></h3>
                                        <?php if(!empty($galleryDescription)): ?>
                                        <p class="gallery-description"><?= $galleryDescription ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Gallery Carousel -->
                                    <div class="gallery-carousel-container <?= count($images) <= 1 ? 'single-image' : '' ?>" data-gallery-id="<?= $galleryId ?>">
                                        <button class="carousel-nav carousel-prev" onclick="previousImage(<?= $galleryId ?>)" aria-label="Previous image">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        
                                        <div class="gallery-carousel">
                                            <?php foreach($images as $index => $image): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                                                <div class="carousel-image-wrapper">
                                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= $galleryTitle ?>" loading="lazy">
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <button class="carousel-nav carousel-next" onclick="nextImage(<?= $galleryId ?>)" aria-label="Next image">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        
                                        <div class="carousel-indicators">
                                            <span class="current-image">1</span> / <span class="total-images"><?= count($images) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-images"></i>
                                <p>No photos in gallery yet</p>
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
