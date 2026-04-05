<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Monthly Evaluation</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Sponsor/dashboard-style.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Sponsor_monthly-evaluation.css">
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
        (function() {
            const monthPicker = document.getElementById('monthPicker');
            const downloadBtn = document.getElementById('downloadReportBtn');
            const totalRequests = document.getElementById('totalRequests');
            const approvedRequests = document.getElementById('approvedRequests');
            const pendingRequests = document.getElementById('pendingRequests');
            const committedAmount = document.getElementById('committedAmount');
            const approvalRate = document.getElementById('approvalRate');
            const recordsBody = document.getElementById('recordsBody');

            const formatCurrency = (value) => {
                const number = Number(value || 0);
                return `LKR ${number.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            };

            const formatDate = (value) => {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return '-';
                return date.toLocaleDateString('en-LK', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit'
                });
            };

            const statusClass = (status) => {
                const normalized = String(status || 'pending').toLowerCase();
                if (normalized === 'approved' || normalized === 'completed') return `status-${normalized}`;
                if (normalized === 'rejected') return 'status-rejected';
                return 'status-pending';
            };

            const loadEvaluation = () => {
                const month = monthPicker.value || '<?= date('Y-m') ?>';
                recordsBody.innerHTML = '<tr><td colspan="5"><div class="loading-state">Loading monthly evaluation data...</div></td></tr>';

                fetch(`/unipulse/public/sponsor/dashboard/getMonthlyEvaluationData?month=${encodeURIComponent(month)}`)
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) {
                            throw new Error(data.error || 'Failed to load data');
                        }

                        totalRequests.textContent = data.summary.total_requests || 0;
                        approvedRequests.textContent = (data.summary.approved_requests || 0) + (data.summary.completed_requests || 0);
                        pendingRequests.textContent = data.summary.pending_requests || 0;
                        committedAmount.textContent = formatCurrency(data.summary.committed_amount || 0);
                        approvalRate.textContent = `Approval rate: ${(data.summary.approval_rate || 0).toFixed(1)}%`;

                        const rows = Array.isArray(data.records) ? data.records : [];
                        if (rows.length === 0) {
                            recordsBody.innerHTML = '<tr><td colspan="5"><div class="empty-state">No sponsorship activity was recorded for this month.</div></td></tr>';
                            return;
                        }

                        recordsBody.innerHTML = rows.map((record) => `
                            <tr>
                                <td>
                                    <strong>${record.event_title || 'Event Sponsorship'}</strong><br>
                                    <span style="color:#64748b; font-size:0.9rem;">${record.organizer_name || ''}</span>
                                </td>
                                <td>${record.package_name || 'Package'}</td>
                                <td>${formatDate(record.created_at)}</td>
                                <td>${formatCurrency(record.amount)}</td>
                                <td><span class="status-badge ${statusClass(record.status)}">${record.status_label || record.status || 'Pending'}</span></td>
                            </tr>
                        `).join('');
                    })
                    .catch((error) => {
                        recordsBody.innerHTML = `<tr><td colspan="5"><div class="empty-state">${error.message}</div></td></tr>`;
                    });
            };

            downloadBtn.addEventListener('click', () => {
                const month = monthPicker.value || '<?= date('Y-m') ?>';
                window.location.href = `/unipulse/public/sponsor/dashboard/downloadMonthlyReport?month=${encodeURIComponent(month)}`;
            });

            monthPicker.addEventListener('change', loadEvaluation);
            loadEvaluation();
        })();
    </script>
</body>

</html>