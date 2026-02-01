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
        
        /* Facebook Feed Style - Event Tabs */
        .events-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .event-tabs {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .event-tab {
            flex: 1;
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
        }
        
        .event-tab:hover {
            background: #f3f4f6;
            color: #374151;
        }
        
        .event-tab.active {
            color: #1e3a8a;
            background: white;
        }
        
        .event-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        
        .tab-count {
            background: #1e3a8a;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .event-tab.active .tab-count {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        
        .event-tab-content {
            display: none;
            padding: 1.5rem;
        }
        
        .event-tab-content.active {
            display: block;
        }
        
        /* Facebook Feed Style - Event Cards */
        .events-feed {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .feed-event-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .feed-event-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .feed-event-card.past-event {
            opacity: 0.95;
        }
        
        /* Event Header (Organizer Info) */
        .feed-event-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .event-organizer-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .organizer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        
        .organizer-details {
            display: flex;
            flex-direction: column;
        }
        
        .organizer-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        
        .event-post-time {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .event-status-badge {
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .event-status-badge.upcoming {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
        }
        
        .event-status-badge.past {
            background: #e5e7eb;
            color: #6b7280;
        }
        
        /* Event Title */
        .feed-event-title {
            padding: 1rem 1.25rem 0.5rem;
        }
        
        .feed-event-title h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
            line-height: 1.4;
        }
        
        /* Event Image */
        .feed-event-image {
            width: 100%;
            overflow: hidden;
            max-height: 500px;
        }
        
        .feed-event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        /* Event Body */
        .feed-event-body {
            padding: 1rem 1.25rem;
        }
        
        .feed-event-description {
            font-size: 0.95rem;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .feed-event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 0 0;
            border-top: 1px solid #f3f4f6;
        }
        
        .feed-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .feed-meta-item i {
            color: #1e3a8a;
            font-size: 1rem;
        }
        
        /* Event Actions */
        .feed-event-actions {
            padding: 1rem 1.25rem;
            border-top: 1px solid #f3f4f6;
            display: flex;
            gap: 0.75rem;
        }
        
        .feed-action-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }
        
        .feed-action-btn.primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }
        
        .feed-action-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        .feed-action-btn.secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .feed-action-btn.secondary:hover {
            background: #e5e7eb;
        }
        
        /* Gallery Section Styles */
        .gallery-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 2rem;
        }
        
        .gallery-section .section-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .gallery-feed {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .feed-gallery-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .feed-gallery-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .feed-gallery-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .gallery-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .gallery-details {
            display: flex;
            flex-direction: column;
        }
        
        .gallery-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .feed-gallery-title {
            padding: 1rem 1.25rem;
        }
        
        .feed-gallery-title h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 0.5rem 0;
        }
        
        .gallery-description {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }
        
        .feed-gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            background: #e5e7eb;
        }
        
        .feed-gallery-grid.two-items {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .gallery-grid-item {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            cursor: pointer;
        }
        
        .gallery-grid-item.full-width {
            grid-column: 1 / -1;
            aspect-ratio: 16/9;
        }
        
        .gallery-grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .gallery-grid-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }
        
        @media (max-width: 900px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .event-tabs {
                flex-direction: column;
            }
            
            .event-tab {
                border-bottom: 1px solid #e5e7eb;
            }
            
            .event-tab.active::after {
                bottom: 0;
                height: 100%;
                width: 4px;
                left: 0;
                right: auto;
            }
            
            .feed-gallery-grid {
                grid-template-columns: repeat(2, 1fr);
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
                    <!-- Events Section with Tabs -->
                    <div class="events-section">
                        <div class="section-header">
                            <h2 class="section-title-large">
                                <i class="fas fa-calendar-alt"></i>
                                Events
                            </h2>
                        </div>
                        
                        <!-- Event Tabs -->
                        <div class="event-tabs">
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
                                        <?php if(!empty($event->cover_image)): ?>
                                        <div class="feed-event-image">
                                            <img src="<?= htmlspecialchars($event->cover_image) ?>" alt="<?= htmlspecialchars($event->title) ?>">
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
                                        <?php if(!empty($event->cover_image)): ?>
                                        <div class="feed-event-image">
                                            <img src="<?= htmlspecialchars($event->cover_image) ?>" alt="<?= htmlspecialchars($event->title) ?>">
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
                    </div>
                    
                    <!-- Photo Gallery Section -->
                    <div class="gallery-section">
                        <div class="section-header">
                            <h2 class="section-title-large">
                                <i class="fas fa-images"></i>
                                Photo Gallery
                            </h2>
                        </div>
                        
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
                                    
                                    <!-- Gallery Images Grid -->
                                    <div class="feed-gallery-grid">
                                        <?php 
                                        $displayCount = min(count($images), 6); // Show max 6 images
                                        for($i = 0; $i < $displayCount; $i++): 
                                            $image = $images[$i];
                                        ?>
                                        <div class="gallery-grid-item <?= $displayCount == 1 ? 'full-width' : '' ?>">
                                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= $galleryTitle ?>" loading="lazy">
                                            <?php if($i == 5 && count($images) > 6): ?>
                                            <div class="gallery-overlay">
                                                <span>+<?= count($images) - 6 ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state" style="margin-top: 2rem;">
                                <i class="fas fa-images"></i>
                                <p>No photos in gallery yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    <script>
        // Tab Switching Function
        function switchEventTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.event-tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.event-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            const selectedContent = document.getElementById(tabName + '-events');
            if (selectedContent) {
                selectedContent.classList.add('active');
            }
            
            // Add active class to selected tab
            const selectedTab = document.querySelector(`.event-tab[data-tab="${tabName}"]`);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
        }
        
        // Initialize: Ensure upcoming events tab is active on load
        document.addEventListener('DOMContentLoaded', function() {
            switchEventTab('upcoming');
        });
    </script>
    
    <?php include_once(__DIR__ . '/../footer.php'); ?>
</body>
</html>
