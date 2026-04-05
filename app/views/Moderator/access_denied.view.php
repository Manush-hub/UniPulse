<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Access Denied</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/moderator/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderator/dashboard">Dashboard</a>
                <a href="/unipulse/public/logout">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <div class="access-denied-container">
            <div class="access-denied-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1>Access Denied</h1>
                <p class="error-message">
                    <?= htmlspecialchars($error ?? 'You do not have permission to access this page.') ?>
                </p>
                <div class="actions">
                    <a href="/unipulse/public/moderator/dashboard" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="/unipulse/public/logout" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Moderator_access_denied.css">
</body>

</html>