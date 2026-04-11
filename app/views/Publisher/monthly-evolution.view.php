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
        (function() {
            const monthPicker = document.getElementById('monthPicker');
            const downloadBtn = document.getElementById('downloadReportBtn');
            const eventsCountEl = document.getElementById('eventsCount');
            const ticketsCountEl = document.getElementById('ticketsCount');
            const grandTotalSalesEl = document.getElementById('grandTotalSales');
            const detailsBody = document.getElementById('detailsBody');
            const eventTotalsBody = document.getElementById('eventTotalsBody');

            const formatCurrency = (value) => {
                const number = Number(value || 0);
                return `LKR ${number.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            };

            const formatNumber = (value) => {
                const number = Number(value || 0);
                return number.toLocaleString('en-LK', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };

            const loadReport = () => {
                const month = monthPicker.value || '<?= date('Y-m') ?>';

                detailsBody.innerHTML = '<div class="loading-state">Loading monthly evolution data...</div>';
                eventTotalsBody.innerHTML = '<tr><td colspan="2"><div class="loading-state">Loading total sales table...</div></td></tr>';

                fetch(`/unipulse/public/publisher/dashboard/getMonthlyEvolution?month=${encodeURIComponent(month)}`)
                    .then((response) => response.json())
                    .then((payload) => {
                        if (!payload.success) {
                            throw new Error(payload.error || 'Failed to load monthly report');
                        }

                        const reportData = payload.data || {};
                        const details = Array.isArray(reportData.details) ? reportData.details : [];
                        const eventTotals = Array.isArray(reportData.event_totals) ? reportData.event_totals : [];
                        const summary = reportData.summary || {};

                        eventsCountEl.textContent = Number(summary.events_count || 0).toLocaleString('en-LK');
                        ticketsCountEl.textContent = Number(summary.tickets_count || 0).toLocaleString('en-LK');
                        grandTotalSalesEl.textContent = formatCurrency(summary.grand_total_sales || 0);

                        renderDetailsTable(details);
                        renderEventTotalsTable(eventTotals, summary.grand_total_sales || 0);
                    })
                    .catch((error) => {
                        const message = error.message || 'Failed to load monthly report data.';
                        detailsBody.innerHTML = `<tr><td colspan="4"><div class="empty-state">${message}</div></td></tr>`;
                        eventTotalsBody.innerHTML = `<tr><td colspan="2"><div class="empty-state">${message}</div></td></tr>`;
                    });
            };

            const renderDetailsTable = (rows) => {
                if (!rows.length) {
                    detailsBody.innerHTML = '<div class="empty-state">No ticket sales activity recorded for this month.</div>';
                    return;
                }

                const grouped = rows.reduce((groups, row) => {
                    const eventName = row.event_name || 'Untitled Event';
                    if (!groups[eventName]) {
                        groups[eventName] = [];
                    }
                    groups[eventName].push(row);
                    return groups;
                }, {});

                const sectionsHtml = Object.entries(grouped).map(([eventName, eventRows]) => {
                    const subtotal = eventRows.reduce((sum, row) => sum + Number(row.sales || 0), 0);
                    const rowsHtml = eventRows.map((row) => `
                        <tr>
                            <td>${row.ticket_type || 'General Admission'}</td>
                            <td>${Number(row.ticket_amount || 0).toLocaleString('en-LK')}</td>
                            <td>${formatNumber(row.sales || 0)}</td>
                        </tr>
                    `).join('');

                    return `
                        <div class="event-sales-section">
                            <div class="event-sales-header">
                                <h4>${eventName}</h4>
                            </div>
                            <div class="table-wrap">
                                <table class="report-table event-sales-table">
                                    <thead>
                                        <tr>
                                            <th>Ticket Type</th>
                                            <th>Ticket Amount</th>
                                            <th>Sales(LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${rowsHtml}
                                        <tr class="group-total-row">
                                            <td colspan="2">Total Sales</td>
                                            <td>${formatNumber(subtotal)}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }).join('');

                detailsBody.innerHTML = sectionsHtml;
            };

            const renderEventTotalsTable = (rows, grandTotal) => {
                if (!rows.length) {
                    eventTotalsBody.innerHTML = '<tr><td colspan="2"><div class="empty-state">No event totals available for this month.</div></td></tr>';
                    return;
                }

                const rowHtml = rows.map((row) => `
                    <tr>
                        <td>${row.event_name || 'Untitled Event'}</td>
                        <td>${formatNumber(row.total_sales || 0)}</td>
                    </tr>
                `).join('');

                eventTotalsBody.innerHTML = `${rowHtml}
                    <tr class="grand-total-row">
                        <td>Grand Total</td>
                        <td>${formatNumber(grandTotal || 0)}</td>
                    </tr>
                `;
            };

            downloadBtn.addEventListener('click', () => {
                const month = monthPicker.value || '<?= date('Y-m') ?>';
                window.location.href = `/unipulse/public/publisher/dashboard/downloadMonthlyReport?month=${encodeURIComponent(month)}`;
            });

            monthPicker.addEventListener('change', loadReport);
            loadReport();
        })();
    </script>
</body>

</html>