<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Monthly Evolution</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/dashboard-style.css">
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
                    <a href="/unipulse/public/publisher/dashboard">Dashboard</a>
                    <span>/</span>
                    <span>Monthly Evolution Report</span>
                </div>
                <h1>Publisher Monthly Evolution Report</h1>
                <p>Track monthly ticket sales by ticket type for each event and download your report as PDF.</p>
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
                            <h3>Events With Sales</h3>
                            <div class="summary-value" id="eventsCount">0</div>
                            <div class="summary-note">Events with ticket activity in selected month</div>
                        </div>
                        <div class="summary-card">
                            <h3>Total Tickets</h3>
                            <div class="summary-value" id="ticketsCount">0</div>
                            <div class="summary-note">All ticket quantities combined</div>
                        </div>
                        <div class="summary-card">
                            <h3>Grand Total Sales</h3>
                            <div class="summary-value" id="grandTotalSales">LKR 0.00</div>
                            <div class="summary-note">Net sales after refunds</div>
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
                                Event-wise Ticket Type Sales
                            </h3>
                            <div class="section-content">
                                <div id="detailsBody" class="event-sales-container">
                                    <div class="loading-state">Loading monthly evolution data...</div>
                                </div>
                            </div>
                        </div>

                        <div class="evaluation-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Total Sales by Event
                            </h3>
                            <div class="section-content">
                                <div class="table-wrap">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Event Name</th>
                                                <th>Total Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody id="eventTotalsBody">
                                            <tr>
                                                <td colspan="2">
                                                    <div class="loading-state">Loading total sales table...</div>
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
        window.publisherMonthlyEvolutionConfig = {
            defaultMonth: '<?= date('Y-m') ?>'
        };
    </script>
    <script src="/unipulse/public/assets/js/Publisher/monthly-evolution-app.js"></script>
</body>

</html>