<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Monthly Evolution Report</title>
    <link rel="stylesheet" href="<?php echo $controller->loadCSS('dashboard-style.css'); ?>">
    <style>
        .page-title-section {
            padding: 2.5rem 0 1rem;
        }

        .page-title-section .breadcrumb {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .page-title-section h1 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.8rem);
            color: #0f172a;
        }

        .page-title-section p {
            margin: 0.65rem 0 0;
            color: #475569;
            max-width: 760px;
        }

        .monthly-evolution-page {
            padding: 1rem 0 3rem;
        }

        .evaluation-panel {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .evolution-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1.35rem 1.5rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #ea580c 100%);
            color: #fff;
        }

        .month-selector {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .month-selector label {
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .month-selector input {
            border: 0;
            border-radius: 999px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            min-width: 170px;
            color: #0f172a;
            background: #fff;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.1);
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            border: 0;
            border-radius: 999px;
            padding: 0.8rem 1.2rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .btn-download:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.24);
        }

        .evolution-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.5rem;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 1.1rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #1e3a8a;
            flex-shrink: 0;
        }

        .donation-icon {
            background: #fff7ed;
            color: #c2410c;
        }

        .participation-icon {
            background: #ecfeff;
            color: #0e7490;
        }

        .spending-icon {
            background: #fef3c7;
            color: #92400e;
        }

        .summary-content h3 {
            margin: 0;
            font-size: 0.92rem;
            color: #64748b;
            font-weight: 700;
        }

        .summary-count {
            margin: 0.45rem 0 0;
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .summary-label {
            display: inline-block;
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 0.88rem;
        }

        .evolution-details {
            padding: 0 1.5rem 1.5rem;
            display: grid;
            gap: 1rem;
        }

        .evolution-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
        }

        .evolution-section h3 {
            margin: 0;
            padding: 1rem 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-bottom: 1px solid #e2e8f0;
            color: #1e3a8a;
            font-size: 1rem;
        }

        .section-content {
            padding: 1rem 1.1rem 1.1rem;
            color: #334155;
        }

        .no-data {
            color: #64748b;
            margin: 0;
        }

        .summary-stats p {
            margin: 0.35rem 0;
        }

        @media (max-width: 1024px) {
            .evolution-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .evolution-controls {
                padding: 1.1rem;
            }

            .evolution-summary {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .evolution-details {
                padding: 0 1rem 1rem;
            }
        }
    </style>
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