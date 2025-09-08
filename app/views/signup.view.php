<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/signup-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="signup-container">
            <!-- Welcome Section -->
            <h1 class="signup-title">Join UniPulse Community</h1>
            <div class="signup-subtitle">
                Discover and participate in university events across Sri Lanka
            </div>

            <!-- User Type Selection -->
            <div class="user-type-selection" id="userTypeSelection">
                <div class="user-type-grid">
                    <!-- Users Card -->
                    <a href="/unipulse/public/usersignup" class="user-type-card" data-type="user" style="text-decoration: none; color: inherit;">
                        <div class="card-icon">
                            <!-- Graduation Cap SVG -->
                            <svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                                <path d="M32 13L8 26L32 39L56 26L32 13Z" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M32 39V51" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" />
                                <path d="M20 33V43C20 47 44 47 44 43V33" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h3 class="user-type-title">Users</h3>
                        <p class="user-type-desc">
                            University students and outsiders who want to discover and participate in exciting events across universities.
                        </p>
                    </a>
                    <!-- Event Organizers Card -->
                    <a href="/unipulse/public/publisherreg" class="user-type-card" data-type="organizer">
                        <div class="card-icon">
                            <!-- Calendar SVG -->
                            <svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                                <rect x="12" y="21" width="40" height="31" rx="5" stroke="#E87C2B" stroke-width="3" />
                                <path d="M12 31H52" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" />
                                <path d="M22 16V24" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" />
                                <path d="M42 16V24" stroke="#E87C2B" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h3 class="user-type-title">Organizers</h3>
                        <p class="user-type-desc">
                            University clubs and organizations that create, manage, and promote events to engage the student community.
                        </p>
                    </a>
                    <!-- Sponsors Card -->
                    <a href="/unipulse/public/sponsorreg" class="user-type-card" data-type="sponsor">
                        <div class="card-icon">
                            <!-- Briefcase SVG -->
                            <svg width="78" height="78" viewBox="0 0 64 64" fill="none">
                                <rect x="12" y="23" width="40" height="25" rx="5" stroke="#E87C2B" stroke-width="3" />
                                <rect x="22" y="14" width="20" height="15" rx="3" stroke="#E87C2B" stroke-width="3" />
                            </svg>
                        </div>
                        <h3 class="user-type-title">Sponsors</h3>
                        <p class="user-type-desc">
                            Individuals or companies looking to support and gain visibility by sponsoring university events
                        </p>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="/assets/js/signup-app.js"></script>
</body>

</html>