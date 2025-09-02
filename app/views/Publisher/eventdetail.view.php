<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Publisher - UniPulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('eventdetail-style.css'); ?>">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Event Management Header -->
        <div class="event-management-header">
            <div class="container">
                <div class="management-controls">
                    <a href="/unipulse/public/publisher/events" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to My Events
                    </a>
                    <div class="event-actions">
                        <button class="btn btn-outline" onclick="editEvent()">
                            <i class="fas fa-edit"></i>
                            Edit Event
                        </button>
                        <button class="btn btn-primary" onclick="viewAnalytics()">
                            <i class="fas fa-chart-bar"></i>
                            Analytics
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Details Content -->
        <div class="event-details-content" id="eventContent">
            <!-- Event details will be loaded here dynamically -->
        </div>

        <!-- Loading State -->
        <div id="loadingContainer" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading event details...</p>
        </div>

        <!-- Error State -->
        <div id="errorContainer" class="error-container" style="display: none;">
            <div class="error-content">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Event Not Found</h2>
                <p>The event you're looking for could not be found or you don't have permission to view it.</p>
                <a href="/unipulse/public/publisher/events" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to My Events
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="<?php echo $controller->loadJS('eventdetail-app.js'); ?>"></script>
</body>
</html>
