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
if (!empty($publisherProfile->telegram)) $socialLinks['telegram'] = $publisherProfile->telegram;
if (!empty($publisherProfile->github)) $socialLinks['github'] = $publisherProfile->github;
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
    <link rel="stylesheet" href="/UniPulse/public/assets/css/publisher/profile-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 80px;
        }
        
        /* Back button for public view */
        .navigation-bar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 80px;
            z-index: 50;
        }
        
        .navigation-bar .container-nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 45px;
        }
        
        .back-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4A5BCC;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #f1f5f9;
        }
        
        /* Main Content Wrapper */
        .content-wrapper {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 0 2rem;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 1.5rem;
        }
        
        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border-radius: 50%;
            color: #1e3a8a;
            flex-shrink: 0;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.15rem;
        }
        
        .info-value {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }
        
        /* Tags */
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .tag {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* Social Links */
        .social-links {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .social-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .social-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 16px;
        }
        
        /* Section Dividers */
        .section-divider {
            margin: 2rem 0 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .section-title-large {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title-large i {
            color: #1e3a8a;
        }
        
        /* Event Cards */
        .section-header {
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
        }
        
        .event-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .event-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .event-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .event-badge.upcoming {
            background: #dcfce7;
            color: #166534;
        }
        
        .event-badge.past {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .event-content {
            padding: 16px;
        }
        
        .event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }
        
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .event-meta-item i {
            color: #1e3a8a;
            width: 16px;
        }
        
        .event-description {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .event-button {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .event-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            color: #9ca3af;
        }
        
        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            margin-top: 12px;
        }
        
        .gallery-item {
            aspect-ratio: 1;
            overflow: hidden;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s;
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        @media (max-width: 900px) {
            .content-grid {
                grid-template-columns: 1fr;
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
                
                <!-- Profile Avatar positioned to overlap -->
                <div class="profile-avatar profile-avatar-overlap">
                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($orgName) ?> Logo">
                    <?php if($isVerified): ?>
                    <div class="verified-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-banner">
                <div class="profile-info-shifted">
                    <h1><?= htmlspecialchars($orgName) ?></h1>
                    
                    <?php if(!empty($orgType) || !empty($university) || !empty($faculty)): ?>
                    <div class="club-meta">
                        <?php if(!empty($orgType)): ?>
                        <span><i class="fas fa-tag"></i> <?= htmlspecialchars(ucwords(str_replace('-', ' ', $orgType))) ?></span>
                        <?php endif; ?>
                        <?php if(!empty($university)): ?>
                        <span><i class="fas fa-university"></i> <?= htmlspecialchars($university) ?></span>
                        <?php endif; ?>
                        <?php if(!empty($faculty)): ?>
                        <span><i class="fas fa-building"></i> <?= htmlspecialchars($faculty) ?></span>
                        <?php endif; ?>
                    </div>
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
                    <?php if(!empty($bio) || !empty($mission)): ?>
                    <div class="info-card">
                        <h2 class="card-title">About</h2>
                        <?php if(!empty($bio)): ?>
                        <p style="color: #374151; line-height: 1.6; margin-bottom: 1rem;"><?= nl2br(htmlspecialchars($bio)) ?></p>
                        <?php endif; ?>
                        
                        <?php if(!empty($mission)): ?>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <h4 style="font-size: 0.9rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-bullseye" style="color: #1e3a8a;"></i> Mission
                            </h4>
                            <p style="color: #6b7280; line-height: 1.6; font-size: 0.9rem;"><?= nl2br(htmlspecialchars($mission)) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Organization Info -->
                    <div class="info-card">
                        <h2 class="card-title">Organization Info</h2>
                        
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
                                'telegram' => ['icon' => 'fab fa-telegram', 'color' => '#0088cc', 'bg' => '#dbeafe', 'label' => 'Telegram'],
                                'github' => ['icon' => 'fab fa-github', 'color' => '#333', 'bg' => '#f1f5f9', 'label' => 'GitHub'],
                                'discord' => ['icon' => 'fab fa-discord', 'color' => '#5865f2', 'bg' => '#e0e7ff', 'label' => 'Discord']
                            ];
                            
                            foreach($socialLinks as $platform => $url): 
                                if(!empty($url) && isset($socialConfig[$platform])): 
                                    $config = $socialConfig[$platform];
                            ?>
                            <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="social-link" style="background: <?= $config['bg'] ?>; color: <?= $config['color'] ?>;">
                                <span class="social-icon" style="background: white; color: <?= $config['color'] ?>;">
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
                    <!-- Events Section -->
                    <div class="section-divider">
                        <h2 class="section-title-large">
                            <i class="fas fa-calendar-alt"></i>
                            Events
                        </h2>
                    </div>
                    
                    <?php if(!empty($events) && count($events) > 0): ?>
                        <?php foreach($events as $event): ?>
                        <div class="event-card">
                            <?php if(!empty($event['cover_image'])): ?>
                            <div class="event-image">
                                <img src="<?= htmlspecialchars($event['cover_image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                                <span class="event-badge <?= strtolower($event['status']) ?>">
                                    <?= htmlspecialchars($event['status']) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="event-content">
                                <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                                
                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="far fa-calendar"></i>
                                        <span><?= date('M d, Y', strtotime($event['event_date'])) ?></span>
                                    </div>
                                    <?php if(!empty($event['event_time'])): ?>
                                    <div class="event-meta-item">
                                        <i class="far fa-clock"></i>
                                        <span><?= htmlspecialchars($event['event_time']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if(!empty($event['location'])): ?>
                                    <div class="event-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= htmlspecialchars($event['location']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if(!empty($event['description'])): ?>
                                <p class="event-description"><?= htmlspecialchars(substr($event['description'], 0, 150)) ?><?= strlen($event['description']) > 150 ? '...' : '' ?></p>
                                <?php endif; ?>
                                
                                <button onclick="window.location.href='/unipulse/public/event/view?id=<?= $event['id'] ?>'" class="event-button">
                                    View Event Details
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No events posted yet</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Gallery Section -->
                    <div class="section-divider">
                        <h2 class="section-title-large">
                            <i class="fas fa-images"></i>
                            Photo Gallery
                        </h2>
                    </div>
                    
                    <?php if(!empty($galleries) && count($galleries) > 0): ?>
                        <?php foreach($galleries as $gallery): ?>
                        <div class="info-card">
                            <h3 class="section-subtitle"><?= htmlspecialchars($gallery['title']) ?></h3>
                            <p style="color: #6b7280; margin-bottom: 1rem;"><?= htmlspecialchars($gallery['description']) ?></p>
                            <div class="gallery-grid">
                                <div class="gallery-item">
                                    <img src="<?= htmlspecialchars($gallery['first_image']) ?>" alt="<?= htmlspecialchars($gallery['title']) ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-image"></i>
                            <p>No photos in gallery yet</p>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    <?php include_once(__DIR__ . '/../footer.php'); ?>
</body>
</html>
    <!-- Header -->
    <?php include __DIR__ . '/../header.php'; ?>
    
    <!-- Navigation Bar -->
    <div class="navigation-bar">
        <div class="container">
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <!-- Profile Container -->
    <div class="profile-container">
        <!-- Cover Photo -->
        <div class="cover-photo-wrapper">
            <div class="cover-photo">
                <img src="<?php echo $coverPhotoUrl; ?>" alt="Cover Photo">
            </div>
        </div>
        
        <!-- Profile Header -->
        <div class="profile-info-section">
            <div class="profile-header">
                <div class="profile-picture-wrapper">
                    <div class="profile-picture">
                        <img src="<?php echo $logoUrl; ?>" alt="<?php echo $orgName; ?>">
                    </div>
                    <?php if ($publisher->approval_status === 'approved'): ?>
                    <div class="verified-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo $orgName; ?></h1>
                    <p class="profile-tagline"><?php echo $headline; ?></p>
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-graduation-cap"></i>
                            <span><?php echo $university; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-building"></i>
                            <span><?php echo $faculty; ?></span>
                        </div>
                        <?php if (!empty($memberCount)): ?>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo $memberCount; ?> members</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="content-grid">
            <!-- Left Column - About Info -->
            <div class="info-column">
                <!-- Intro Card -->
                <div class="info-card">
                    <h2 class="card-title">About</h2>
                    <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">
                        <?php echo $bio; ?>
                    </p>
                </div>
                
                <!-- Details Card -->
                <div class="info-card">
                    <h2 class="card-title">Details</h2>
                    
                    <?php if (!empty($orgType)): ?>
                    <div class="info-item">
                        <i class="fas fa-tag info-icon"></i>
                        <div class="info-text">
                            <div class="info-label">Organization Type</div>
                            <div class="info-value"><?php echo ucwords(str_replace('-', ' ', $orgType)); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($establishedYear)): ?>
                    <div class="info-item">
                        <i class="fas fa-calendar-plus info-icon"></i>
                        <div class="info-text">
                            <div class="info-label">Established</div>
                            <div class="info-value"><?php echo $establishedYear; ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($address)): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?php echo $address; ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope info-icon"></i>
                        <div class="info-text">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo $email; ?></div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone info-icon"></i>
                        <div class="info-text">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?php echo $countryCode . ' ' . $phone; ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($mission)): ?>
                <!-- Mission Card -->
                <div class="info-card">
                    <h2 class="card-title">Mission</h2>
                    <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">
                        <?php echo $mission; ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($preferences) && is_array($preferences)): ?>
                <!-- Interests Card -->
                <div class="info-card">
                    <h2 class="card-title">Focus Areas</h2>
                    <div class="tags-container">
                        <?php foreach ($preferences as $preference): ?>
                            <span class="tag"><?php echo htmlspecialchars($preference); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Social Links Card -->
                <?php if (!empty($website) || !empty($facebook) || !empty($instagram) || !empty($linkedin) || !empty($twitter) || !empty($discord) || !empty($youtube)): ?>
                <div class="info-card">
                    <h2 class="card-title">Connect</h2>
                    <div class="social-links">
                        <?php if (!empty($website)): ?>
                        <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="social-link">
                            <div class="social-icon website">
                                <i class="fas fa-globe"></i>
                            </div>
                            <span>Website</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($facebook)): ?>
                        <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" class="social-link">
                            <div class="social-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <span>Facebook</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($linkedin)): ?>
                        <a href="<?php echo htmlspecialchars($linkedin); ?>" target="_blank" class="social-link">
                            <div class="social-icon linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </div>
                            <span>LinkedIn</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($twitter)): ?>
                        <a href="<?php echo htmlspecialchars($twitter); ?>" target="_blank" class="social-link">
                            <div class="social-icon twitter">
                                <i class="fab fa-twitter"></i>
                            </div>
                            <span>Twitter</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($instagram)): ?>
                        <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" class="social-link">
                            <div class="social-icon instagram">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <span>Instagram</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($discord)): ?>
                        <a href="<?php echo htmlspecialchars($discord); ?>" target="_blank" class="social-link">
                            <div class="social-icon discord">
                                <i class="fab fa-discord"></i>
                            </div>
                            <span>Discord</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($youtube)): ?>
                        <a href="<?php echo htmlspecialchars($youtube); ?>" target="_blank" class="social-link">
                            <div class="social-icon youtube">
                                <i class="fab fa-youtube"></i>
                            </div>
                            <span>YouTube</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Events & Gallery -->
            <div class="posts-column">
                <!-- Events Section -->
                <div class="section-divider">
                    <h2 class="section-title-large">
                        <i class="fas fa-calendar"></i> Events
                    </h2>
                </div>
                
                <!-- Upcoming Events -->
                <?php if (!empty($upcomingEvents)): ?>
                <div class="section-header">
                    <h3 class="section-subtitle">Upcoming Events</h3>
                </div>
                
                <?php foreach ($upcomingEvents as $event): 
                    $eventId = $event->id;
                    $eventTitle = safeOutput($event->title);
                    $eventDate = safeOutput($event->event_date);
                    $eventLocation = safeOutput($event->location, 'TBD');
                    $eventDescription = safeOutput($event->description);
                    $eventImage = safeOutput($event->banner_image ?? null, 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop');
                    $maxParticipants = $event->max_participants ?? 0;
                    
                    $dateObj = new DateTime($eventDate);
                    $formattedDate = $dateObj->format('l, F j, Y');
                ?>
                <div class="event-card">
                    <div class="event-image">
                        <img src="<?php echo $eventImage; ?>" alt="<?php echo $eventTitle; ?>">
                        <span class="event-badge upcoming">Upcoming</span>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?php echo $eventTitle; ?></h3>
                        <div class="event-meta">
                            <div class="event-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo $formattedDate; ?></span>
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo $eventLocation; ?></span>
                            </div>
                            <?php if ($maxParticipants > 0): ?>
                            <div class="event-meta-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo $maxParticipants; ?> participants</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <p class="event-description"><?php echo substr($eventDescription, 0, 120) . '...'; ?></p>
                        <button class="event-button" onclick="window.location.href='/unipulse/public/user/eventview?id=<?php echo $eventId; ?>'">
                            View Event Details
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Past Events -->
                <?php if (!empty($pastEvents)): ?>
                <div class="section-header" style="margin-top: 2rem;">
                    <h3 class="section-subtitle">Past Events</h3>
                </div>
                
                <?php foreach ($pastEvents as $event): 
                    $eventId = $event->id;
                    $eventTitle = safeOutput($event->title);
                    $eventDate = safeOutput($event->event_date);
                    $eventLocation = safeOutput($event->location, 'TBD');
                    $eventDescription = safeOutput($event->description);
                    $eventImage = safeOutput($event->banner_image ?? null, 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=400&fit=crop');
                    
                    $dateObj = new DateTime($eventDate);
                    $formattedDate = $dateObj->format('F j, Y');
                ?>
                <div class="event-card">
                    <div class="event-image">
                        <img src="<?php echo $eventImage; ?>" alt="<?php echo $eventTitle; ?>">
                        <span class="event-badge past">Past Event</span>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?php echo $eventTitle; ?></h3>
                        <div class="event-meta">
                            <div class="event-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo $formattedDate; ?></span>
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo $eventLocation; ?></span>
                            </div>
                        </div>
                        <p class="event-description"><?php echo substr($eventDescription, 0, 120) . '...'; ?></p>
                        <button class="event-button" onclick="window.location.href='/unipulse/public/user/eventview?id=<?php echo $eventId; ?>'">
                            View Event
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (empty($upcomingEvents) && empty($pastEvents)): ?>
                <div class="info-card empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No events to display</p>
                </div>
                <?php endif; ?>
                
                <!-- Gallery Section -->
                <?php if (!empty($galleries)): ?>
                <div class="section-divider" style="margin-top: 3rem;">
                    <h2 class="section-title-large">
                        <i class="fas fa-images"></i> Gallery
                    </h2>
                </div>
                
                <div class="gallery-grid-public">
                    <?php foreach ($galleries as $gallery): 
                        $galleryId = $gallery['id'];
                        $galleryTitle = htmlspecialchars($gallery['title']);
                        $galleryDescription = htmlspecialchars($gallery['description']);
                        $images = $gallery['images'];
                        
                        // Display first image of each gallery
                        if (!empty($images)):
                            $firstImage = $images[0];
                    ?>
                    <div class="gallery-item-public">
                        <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo $galleryTitle; ?>" loading="lazy">
                        <div class="gallery-item-overlay">
                            <h4><?php echo $galleryTitle; ?></h4>
                            <?php if (count($images) > 1): ?>
                            <p><i class="fas fa-images"></i> <?php echo count($images); ?> photos</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
