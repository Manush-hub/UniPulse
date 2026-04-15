(function () {
    const monthPicker = document.getElementById('monthPicker');
    const downloadBtn = document.getElementById('downloadReportBtn');
    const eventsCountEl = document.getElementById('eventsCount');
    const ticketsCountEl = document.getElementById('ticketsCount');
    const grandTotalSalesEl = document.getElementById('grandTotalSales');
    const detailsBody = document.getElementById('detailsBody');
    const eventTotalsBody = document.getElementById('eventTotalsBody');

    if (!monthPicker || !downloadBtn || !eventsCountEl || !ticketsCountEl || !grandTotalSalesEl || !detailsBody || !eventTotalsBody) {
        return;
    }

    const defaultMonth = (window.publisherMonthlyEvolutionConfig && window.publisherMonthlyEvolutionConfig.defaultMonth) || '';

    const formatCurrency = value => {
        const number = Number(value || 0);
        return `LKR ${number.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const formatNumber = value => {
        const number = Number(value || 0);
        return number.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const renderDetailsTable = rows => {
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
            const rowsHtml = eventRows.map(row => `
                <tr>
                    <td>${row.ticket_type || 'General Admission'}</td>
                    <td>${Number(row.ticket_amount || 0).toLocaleString('en-LK')}</td>
                    <td>${formatNumber(row.sales || 0)}</td>
                </tr>
            `).join('');

            return `
                <div class="event-sales-section">
                    <div class="event-sales-header"><h4>${eventName}</h4></div>
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

        const rowHtml = rows.map(row => `
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

    const loadReport = () => {
        const month = monthPicker.value || defaultMonth;

        detailsBody.innerHTML = '<div class="loading-state">Loading monthly evolution data...</div>';
        eventTotalsBody.innerHTML = '<tr><td colspan="2"><div class="loading-state">Loading total sales table...</div></td></tr>';

        fetch(`/unipulse/public/publisher/dashboard/getMonthlyEvolution?month=${encodeURIComponent(month)}`)
            .then(response => response.json())
            .then(payload => {
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
            .catch(error => {
                const message = error.message || 'Failed to load monthly report data.';
                detailsBody.innerHTML = `<tr><td colspan="4"><div class="empty-state">${message}</div></td></tr>`;
                eventTotalsBody.innerHTML = `<tr><td colspan="2"><div class="empty-state">${message}</div></td></tr>`;
            });
    };

    downloadBtn.addEventListener('click', () => {
        const month = monthPicker.value || defaultMonth;
        window.location.href = `/unipulse/public/publisher/dashboard/downloadMonthlyReport?month=${encodeURIComponent(month)}`;
    });

    monthPicker.addEventListener('change', loadReport);
    loadReport();
})();
