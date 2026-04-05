
/* Extracted from Sponsor/monthly-evaluation.view.php */

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
    
