<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - <?= htmlspecialchars($sponsor->company_name) ?></title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/sponsor-details-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'sponsors'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Breadcrumb -->
        <section class="breadcrumb">
            <div class="container">
                <nav class="breadcrumb-nav">
                    <a href="/unipulse/public/publisher/dashboard">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="/unipulse/public/publisher/sponsors">Sponsors</a>
                    <span class="separator">/</span>
                    <span class="current"><?= htmlspecialchars($sponsor->company_name) ?></span>
                </nav>
            </div>
        </section>

        <!-- Sponsor Profile -->
        <section class="sponsor-profile">
            <div class="container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($sponsor->company_name, 0, 2)) ?>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name"><?= htmlspecialchars($sponsor->company_name) ?></h1>
                        <div class="profile-meta">
                            <span class="sponsor-status <?= strtolower(str_replace(' ', '-', $sponsor->activity_status)) ?>">
                                <i class="fas fa-circle"></i>
                                <?= $sponsor->activity_status ?>
                            </span>
                            <span class="join-date">
                                <i class="fas fa-calendar-alt"></i>
                                Joined <?= date('M j, Y', strtotime($sponsor->created_at)) ?>
                            </span>
                            <?php if ($sponsor->last_login): ?>
                                <span class="last-login">
                                    <i class="fas fa-clock"></i>
                                    Last login <?= date('M j, Y g:i A', strtotime($sponsor->last_login)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="btn btn-primary" onclick="openContactModal(<?= $sponsor->id ?>)">
                            <i class="fas fa-envelope"></i>
                            Contact Sponsor
                        </button>
                        <button class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i>
                            Back to List
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Information -->
        <section class="contact-info">
            <div class="container">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-header">
                            <h3>Contact Information</h3>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-details">
                                    <span class="info-label">Email Address</span>
                                    <span class="info-value">
                                        <a href="mailto:<?= htmlspecialchars($sponsor->email) ?>">
                                            <?= htmlspecialchars($sponsor->email) ?>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="info-details">
                                    <span class="info-label">Phone Number</span>
                                    <span class="info-value">
                                        <a href="tel:<?= $sponsor->country_code ?><?= $sponsor->phone ?>">
                                            <?= $sponsor->country_code ?> <?= htmlspecialchars($sponsor->phone) ?>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-header">
                            <h3>Account Statistics</h3>
                        </div>
                        <div class="info-content">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Sponsorships</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">LKR 0</div>
                                    <div class="stat-label">Total Investment</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Events Supported</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php 
                                        $daysSinceJoined = floor((time() - strtotime($sponsor->created_at)) / (60 * 60 * 24));
                                        echo $daysSinceJoined;
                                        ?>
                                    </div>
                                    <div class="stat-label">Days Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Activity Timeline -->
        <section class="activity-timeline">
            <div class="container">
                <div class="timeline-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="timeline-content">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="timeline-content-item">
                            <h4>Account Created</h4>
                            <p>Sponsor account was created and activated</p>
                            <span class="timeline-date"><?= date('M j, Y g:i A', strtotime($sponsor->created_at)) ?></span>
                        </div>
                    </div>
                    
                    <?php if ($sponsor->last_login): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="timeline-content-item">
                                <h4>Last Login</h4>
                                <p>Sponsor last accessed their account</p>
                                <span class="timeline-date"><?= date('M j, Y g:i A', strtotime($sponsor->last_login)) ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item inactive">
                            <div class="timeline-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="timeline-content-item">
                                <h4>Never Logged In</h4>
                                <p>This sponsor has not yet logged into their account</p>
                                <span class="timeline-date">No login recorded</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Potential Sponsorship Opportunities -->
        <section class="sponsorship-opportunities">
            <div class="container">
                <div class="opportunities-header">
                    <h3>Potential Sponsorship Opportunities</h3>
                    <p>Events that might interest this sponsor</p>
                </div>
                <div class="opportunities-content">
                    <div class="opportunity-placeholder">
                        <i class="fas fa-lightbulb"></i>
                        <h4>No Current Opportunities</h4>
                        <p>Create events to see matching sponsorship opportunities for this sponsor.</p>
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/publisher/createevent'">
                            Create Event
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Contact <?= htmlspecialchars($sponsor->company_name) ?></h3>
                <span class="close-button" onclick="closeContactModal()">&times;</span>
            </div>
            <form id="contactForm" method="POST" action="/unipulse/public/publisher/sponsors/contact/<?= $sponsor->id ?>">
                <div class="modal-body">
                    <div class="message-edit-notice">
                        <i class="fas fa-info-circle"></i>
                        <div class="notice-content">
                            <strong>Note:</strong> You can edit your message until the sponsor reads it. 
                            <a href="/unipulse/public/publisher/messages">View and manage your messages</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Enter your message..." required></textarea>
                    </div>
                    <div class="contact-note">
                        <i class="fas fa-info-circle"></i>
                        <span>This message will be sent directly to the sponsor's email address: <?= htmlspecialchars($sponsor->email) ?></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeContactModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['contact'])): ?>
        <div id="messageAlert" class="alert <?= $_GET['contact'] === 'success' ? 'alert-success' : 'alert-error' ?>">
            <div class="alert-content">
                <i class="fas <?= $_GET['contact'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <span>
                    <?= $_GET['contact'] === 'success' 
                        ? 'Your message has been sent successfully!' 
                        : 'Failed to send message. Please try again.' ?>
                </span>
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <script src="/unipulse/public/assets/js/Publisher/sponsor-details-app.js?v=<?= time() ?>"></script>
</body>

</html>