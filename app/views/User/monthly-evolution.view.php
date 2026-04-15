<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Monthly Evolution Report</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('dashboard-style.css'); ?>">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/User_monthly-evolution.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Title Section -->
        <section class="page-title-section">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/unipulse/public/user/dashboard">Dashboard</a>
                    <span>/</span>
                    <span>Monthly Evolution Report</span>
                </div>
                <h1>Monthly Activity Report</h1>
                <p>Track your volunteering, donations, and event participation for each month</p>
            </div>
        </section>

        <!-- Monthly Evolution Section -->
        <section class="monthly-evolution-page">
            <div class="container">
                <div class="evaluation-panel">
                    <div class="evolution-controls">
                        <div class="month-selector">
                            <label for="monthPicker">Select Month:</label>
                            <input type="month" id="monthPicker" value="<?php echo date('Y-m'); ?>" max="<?php echo date('Y-m'); ?>">
                        </div>
                        <button class="btn-download" id="downloadReportBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download PDF Report
                        </button>
                    </div>

                    <!-- Summary Cards -->
                    <div class="evolution-summary">
                        <div class="summary-card">
                            <div class="summary-icon volunteer-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="summary-content">
                                <h3>Volunteering</h3>
                                <p class="summary-count" id="volunteerCount">0</p>
                                <span class="summary-label">Events</span>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="summary-icon donation-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </div>
                            <div class="summary-content">
                                <h3>Donations</h3>
                                <p class="summary-count" id="donationTotal">LKR 0.00</p>
                                <span class="summary-label">Total Amount</span>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="summary-icon participation-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="summary-content">
                                <h3>Participated</h3>
                                <p class="summary-count" id="participationCount">0</p>
                                <span class="summary-label">Events</span>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="summary-icon spending-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div class="summary-content">
                                <h3>Event Spending</h3>
                                <p class="summary-count" id="eventSpending">LKR 0.00</p>
                                <span class="summary-label">Total Amount</span>
                            </div>
                        </div>
                    </div>

                    <!-- Evolution Details -->
                    <div class="evolution-details">
                        <!-- Volunteering Section -->
                        <div class="evolution-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Volunteering Activities
                            </h3>
                            <div class="section-content" id="volunteeringContent">
                                <p class="no-data">No volunteering activities for this month.</p>
                            </div>
                        </div>

                        <!-- Donations Section -->
                        <div class="evolution-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                Donations
                            </h3>
                            <div class="section-content" id="donationsContent">
                                <p class="no-data">No donations made this month.</p>
                            </div>
                        </div>

                        <!-- Participation Section -->
                        <div class="evolution-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Event Participation
                            </h3>
                            <div class="section-content" id="participationContent">
                                <p class="no-data">No event participation this month.</p>
                            </div>
                        </div>

                        <!-- Other Section -->
                        <div class="evolution-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Summary
                            </h3>
                            <div class="section-content" id="otherContent">
                                <div class="summary-stats">
                                    <p><strong>Total Activities:</strong> <span id="totalActivities">0</span></p>
                                    <p><strong>Total Financial Contribution:</strong> <span id="totalContribution">LKR 0.00</span></p>
                                    <p><strong>Most Active Category:</strong> <span id="mostActiveCategory">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Pass user data to JavaScript -->
    <script>
        window.userData = <?php echo json_encode([
                                'name' => $user['name'] ?? 'User',
                                'email' => $user['email'] ?? '',
                                'type' => $user['type'] ?? 'user',
                                'university' => $user['university'] ?? ''
                            ]); ?>;
    </script>
    <script src="<?php echo $controller->loadJS('monthly-evolution-app.js'); ?>"></script>
</body>

</html>