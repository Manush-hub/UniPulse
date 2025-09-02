<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Landing</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('landing-style.css'); ?>">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'home'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Welcome to UniPulse Publisher Portal</h1>
                    <p>Create, manage, and promote your university events with our comprehensive event management platform.</p>
                    <div class="hero-buttons">
                        <a href="/unipulse/public/publisher/dashboard" class="btn btn-primary">Go to Dashboard</a>
                        <a href="/unipulse/public/publisher/events" class="btn btn-secondary">View Events</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Stats Section -->
        <section class="quick-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Active Events</h3>
                        <div class="stat-number">12</div>
                        <p>Events currently running</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Participants</h3>
                        <div class="stat-number">1,234</div>
                        <p>Registered attendees</p>
                    </div>
                    <div class="stat-card">
                        <h3>Events This Month</h3>
                        <div class="stat-number">8</div>
                        <p>New events created</p>
                    </div>
                    <div class="stat-card">
                        <h3>Success Rate</h3>
                        <div class="stat-number">94%</div>
                        <p>Event completion rate</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="<?php echo $controller->loadJS('landing-app.js'); ?>"></script>
</body>
</html>
