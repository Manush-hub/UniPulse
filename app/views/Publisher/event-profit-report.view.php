<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Completed Event Profit Report</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/dashboard-style.css">
</head>

<body>
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="main-container">
        <section class="page-title-section profit-report-title">
            <div class="container">
                <div class="breadcrumb">
                    <a href="/unipulse/public/publisher/dashboard">Dashboard</a>
                    <span>/</span>
                    <span>Completed Event Profit Report</span>
                </div>
                <h1>Completed Event Profit Report</h1>
                <p>Review the financial performance of finished events with ticket volume and true organizer profit.</p>
            </div>
        </section>

        <section class="monthly-evaluation-page event-profit-page">
            <div class="container">
                <div class="evaluation-panel profit-report-panel">
                    <div class="evaluation-controls profit-report-controls">
                        <div class="profit-range-grid">
                            <div class="month-selector">
                                <label for="fromDate">From Date:</label>
                                <input type="date" id="fromDate" value="<?= date('Y-m-d', strtotime('-12 months')) ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="month-selector">
                                <label for="toDate">To Date:</label>
                                <input type="date" id="toDate" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="profit-control-actions">
                            <button class="btn-download btn-refresh" id="refreshProfitReportBtn" type="button">Refresh Report</button>
                            <button class="btn-download" id="downloadProfitReportBtn" type="button">Download PDF Report</button>
                        </div>
                    </div>

                    <div class="evaluation-summary profit-summary-grid">
                        <div class="summary-card">
                            <h3>Completed Events</h3>
                            <div class="summary-value" id="completedEventsCount">0</div>
                            <div class="summary-note">Events completed in selected period</div>
                        </div>
                        <div class="summary-card">
                            <h3>Total Tickets Sold</h3>
                            <div class="summary-value" id="totalTicketsSold">0</div>
                            <div class="summary-note">Paid + free registrations</div>
                        </div>
                        <div class="summary-card">
                            <h3>Gross Sales</h3>
                            <div class="summary-value" id="grossSalesTotal">LKR 0.00</div>
                            <div class="summary-note">Before platform deductions</div>
                        </div>
                        <div class="summary-card">
                            <h3>Platform Commission</h3>
                            <div class="summary-value" id="commissionTotal">LKR 0.00</div>
                            <div class="summary-note">Total marketplace commission</div>
                        </div>
                        <div class="summary-card profit-highlight-card">
                            <h3>Total Profit</h3>
                            <div class="summary-value" id="profitTotal">LKR 0.00</div>
                            <div class="summary-note">Net earnings from completed events</div>
                        </div>
                    </div>

                    <div class="profit-insights" id="profitInsights">
                        <div class="loading-state">Loading insights...</div>
                    </div>

                    <div class="evaluation-details">
                        <div class="evaluation-section">
                            <h3>Completed Event Cards</h3>
                            <div class="section-content">
                                <div id="completedEventCards" class="completed-event-cards">
                                    <div class="loading-state">Loading completed events...</div>
                                </div>
                            </div>
                        </div>

                        <div class="evaluation-section selected-event-panel">
                            <h3>Selected Event Profit Details</h3>
                            <div class="section-content">
                                <div id="selectedEventDetails" class="selected-event-details">
                                    <div class="empty-state">Select an event card to see total profit and details.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="/unipulse/public/assets/js/Publisher/event-profit-report-app.js"></script>
</body>

</html>