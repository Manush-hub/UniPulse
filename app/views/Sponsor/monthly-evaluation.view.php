<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Monthly Evaluation</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/monthly-evaluation-style.css">
</head>

<body>
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-container">
        <section class="page-title-section">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/unipulse/public/sponsor/dashboard">Dashboard</a>
                    <span>/</span>
                    <span>Monthly Evaluation</span>
                </div>
                <h1>Sponsor Monthly Evaluation</h1>
                <p>Review your sponsorship requests, track budget commitment, and download a month-end evaluation PDF for internal review or stakeholder reporting.</p>
            </div>
        </section>

        <section class="monthly-evaluation-page">
            <div class="container">
                <div class="evaluation-panel">
                    <div class="evaluation-controls">
                        <div class="month-selector">
                            <label for="monthPicker">Select Month:</label>
                            <input type="month" id="monthPicker" value="<?= date('Y-m') ?>" max="<?= date('Y-m') ?>">
                        </div>
                        <button class="btn-download" id="downloadReportBtn" type="button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download PDF Report
                        </button>
                    </div>

                    <div class="evaluation-summary">
                        <div class="summary-card">
                            <h3>Requests Submitted</h3>
                            <div class="summary-value" id="totalRequests">0</div>
                            <div class="summary-note">All sponsorship requests in the selected month</div>
                        </div>
                        <div class="summary-card">
                            <h3>Approved / Completed</h3>
                            <div class="summary-value" id="approvedRequests">0</div>
                            <div class="summary-note" id="approvalRate">Approval rate: 0%</div>
                        </div>
                        <div class="summary-card">
                            <h3>Pending Review</h3>
                            <div class="summary-value" id="pendingRequests">0</div>
                            <div class="summary-note">Requests waiting for publisher action</div>
                        </div>
                        <div class="summary-card">
                            <h3>Committed Budget</h3>
                            <div class="summary-value" id="committedAmount">LKR 0.00</div>
                            <div class="summary-note">Approved or completed sponsorship value</div>
                        </div>
                    </div>

                    <div class="evaluation-details">
                        <div class="evaluation-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                    <path d="M7 8h10"></path>
                                    <path d="M7 12h10"></path>
                                    <path d="M7 16h6"></path>
                                </svg>
                                Monthly Sponsorship Activity
                            </h3>
                            <div class="section-content">
                                <div class="table-wrap">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Event</th>
                                                <th>Package</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recordsBody">
                                            <tr>
                                                <td colspan="5">
                                                    <div class="loading-state">Loading monthly evaluation data...</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        window.sponsorMonthlyEvaluationConfig = {
            defaultMonth: '<?= date('Y-m') ?>'
        };
    </script>
    <script src="/unipulse/public/assets/js/Sponsor/monthly-evaluation-app.js"></script>
</body>

</html>