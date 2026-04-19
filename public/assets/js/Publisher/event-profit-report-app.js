(function () {
    const fromDateEl = document.getElementById('fromDate');
    const toDateEl = document.getElementById('toDate');
    const refreshBtn = document.getElementById('refreshProfitReportBtn');
    const downloadBtn = document.getElementById('downloadProfitReportBtn');
    const insightsEl = document.getElementById('profitInsights');
    const cardsEl = document.getElementById('completedEventCards');
    const selectedEventDetailsEl = document.getElementById('selectedEventDetails');

    if (!fromDateEl || !toDateEl || !refreshBtn || !downloadBtn || !insightsEl || !cardsEl || !selectedEventDetailsEl) {
        return;
    }

    let reportEvents = [];
    let selectedEventId = null;

    const completedEventsCountEl = document.getElementById('completedEventsCount');
    const totalTicketsSoldEl = document.getElementById('totalTicketsSold');
    const grossSalesTotalEl = document.getElementById('grossSalesTotal');
    const commissionTotalEl = document.getElementById('commissionTotal');
    const profitTotalEl = document.getElementById('profitTotal');

    const formatCurrency = value => {
        const number = Number(value || 0);
        return `LKR ${number.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const formatNumber = value => Number(value || 0).toLocaleString('en-LK');

    const formatDate = value => {
        if (!value) return '-';
        const date = new Date(`${value}T00:00:00`);
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleDateString('en-LK', { year: 'numeric', month: 'short', day: 'numeric' });
    };

    const validateRange = () => {
        const fromDate = fromDateEl.value;
        const toDate = toDateEl.value;

        if (!fromDate || !toDate) {
            throw new Error('Please select both From and To dates.');
        }

        if (new Date(fromDate) > new Date(toDate)) {
            throw new Error('From date cannot be later than To date.');
        }

        return { fromDate, toDate };
    };

    const renderSummary = summary => {
        if (completedEventsCountEl) completedEventsCountEl.textContent = formatNumber(summary.events_count || 0);
        if (totalTicketsSoldEl) totalTicketsSoldEl.textContent = formatNumber(summary.tickets_count || 0);
        if (grossSalesTotalEl) grossSalesTotalEl.textContent = formatCurrency(summary.gross_sales || 0);
        if (commissionTotalEl) commissionTotalEl.textContent = formatCurrency(summary.commission_total || 0);
        if (profitTotalEl) profitTotalEl.textContent = formatCurrency(summary.profit_total || 0);
    };

    const renderInsights = (insights, summary) => {
        const topProfit = insights.top_profit_event;
        const topTickets = insights.top_ticket_event;
        const avgProfit = Number(summary.avg_profit_per_event || 0);

        insightsEl.innerHTML = `
            <div class="profit-insight-card">
                <h4>Top Profit Event</h4>
                <p>${topProfit ? topProfit.event_name : 'No data in selected period'}</p>
                <span>${topProfit ? formatCurrency(topProfit.profit_total) : 'LKR 0.00'}</span>
            </div>
            <div class="profit-insight-card">
                <h4>Highest Ticket Volume</h4>
                <p>${topTickets ? topTickets.event_name : 'No data in selected period'}</p>
                <span>${topTickets ? formatNumber(topTickets.total_tickets_sold) : '0'} tickets</span>
            </div>
            <div class="profit-insight-card">
                <h4>Average Profit Per Event</h4>
                <p>Performance baseline for completed events</p>
                <span>${formatCurrency(avgProfit)}</span>
            </div>
        `;
    };

    const renderSelectedEvent = () => {
        const selected = reportEvents.find(event => Number(event.event_id) === Number(selectedEventId));

        if (!selected) {
            selectedEventDetailsEl.innerHTML = '<div class="empty-state">Select an event card to see total profit and details.</div>';
            return;
        }

        const margin = Number(selected.profit_margin || 0);
        const marginClass = margin >= 40 ? 'margin-strong' : (margin >= 20 ? 'margin-medium' : 'margin-low');

        selectedEventDetailsEl.innerHTML = `
            <div class="selected-event-header">
                <h4>${selected.event_name || 'Untitled Event'}</h4>
                <div class="selected-event-meta">Completed on ${formatDate(selected.event_date)}</div>
            </div>
            <div class="selected-event-metrics">
                <div class="selected-metric-card"><span>Total Profit</span><strong>${formatCurrency(selected.profit_total || 0)}</strong></div>
                <div class="selected-metric-card"><span>Total Tickets Sold</span><strong>${formatNumber(selected.total_tickets_sold || 0)}</strong></div>
                <div class="selected-metric-card"><span>Gross Sales</span><strong>${formatCurrency(selected.gross_sales || 0)}</strong></div>
                <div class="selected-metric-card"><span>Platform Commission</span><strong>${formatCurrency(selected.commission_total || 0)}</strong></div>
                <div class="selected-metric-card"><span>Free Tickets</span><strong>${formatNumber(selected.free_tickets || 0)}</strong></div>
                <div class="selected-metric-card"><span>Paid Tickets</span><strong>${formatNumber(selected.paid_tickets || 0)}</strong></div>
            </div>
            <div class="selected-event-footer">
                <span>Profit Margin</span>
                <span class="profit-margin-badge ${marginClass}">${margin.toFixed(2)}%</span>
            </div>
        `;
    };

    const downloadSelectedEventReport = eventId => {
        let range;
        try {
            range = validateRange();
        } catch (_) {
            return;
        }

        const params = new URLSearchParams({
            event_id: String(eventId),
            from_date: range.fromDate,
            to_date: range.toDate
        });

        window.location.href = `/unipulse/public/publisher/dashboard/downloadEventProfitByEvent?${params.toString()}`;
    };

    const renderEventCards = events => {
        reportEvents = events;

        if (!events.length) {
            cardsEl.innerHTML = '<div class="empty-state">No completed events found for this date range.</div>';
            selectedEventId = null;
            renderSelectedEvent();
            return;
        }

        if (!selectedEventId || !events.some(event => Number(event.event_id) === Number(selectedEventId))) {
            selectedEventId = Number(events[0].event_id);
        }

        cardsEl.innerHTML = events.map(event => {
            const isActive = Number(event.event_id) === Number(selectedEventId);
            return `
                <button type="button" class="event-profit-card ${isActive ? 'active' : ''}" data-event-id="${event.event_id}">
                    <div class="event-profit-card-top">
                        <h4>${event.event_name || 'Untitled Event'}</h4>
                        <span>${formatDate(event.event_date)}</span>
                    </div>
                    <div class="event-profit-card-metrics">
                        <div><small>Tickets Sold</small><strong>${formatNumber(event.total_tickets_sold || 0)}</strong></div>
                        <div><small>Total Profit</small><strong>${formatCurrency(event.profit_total || 0)}</strong></div>
                    </div>
                </button>
            `;
        }).join('');

        cardsEl.querySelectorAll('.event-profit-card').forEach(card => {
            card.addEventListener('click', () => {
                selectedEventId = Number(card.getAttribute('data-event-id'));
                renderEventCards(reportEvents);
                renderSelectedEvent();
                downloadSelectedEventReport(selectedEventId);
            });
        });

        renderSelectedEvent();
    };

    const loadReport = () => {
        let range;
        try {
            range = validateRange();
        } catch (error) {
            cardsEl.innerHTML = `<div class="empty-state">${error.message}</div>`;
            insightsEl.innerHTML = `<div class="empty-state">${error.message}</div>`;
            selectedEventDetailsEl.innerHTML = `<div class="empty-state">${error.message}</div>`;
            return;
        }

        cardsEl.innerHTML = '<div class="loading-state">Loading completed events...</div>';
        insightsEl.innerHTML = '<div class="loading-state">Loading insights...</div>';
        selectedEventDetailsEl.innerHTML = '<div class="loading-state">Loading selected event details...</div>';

        const params = new URLSearchParams({ from_date: range.fromDate, to_date: range.toDate });

        fetch(`/unipulse/public/publisher/dashboard/getEventProfitReport?${params.toString()}`)
            .then(response => response.json())
            .then(payload => {
                if (!payload.success) {
                    throw new Error(payload.error || 'Failed to load completed event profit report.');
                }

                const reportData = payload.data || {};
                const summary = reportData.summary || {};
                const events = Array.isArray(reportData.events) ? reportData.events : [];
                const insights = reportData.insights || {};

                renderSummary(summary);
                renderInsights(insights, summary);
                renderEventCards(events);
            })
            .catch(error => {
                const message = error.message || 'Failed to load completed event profit report.';
                cardsEl.innerHTML = `<div class="empty-state">${message}</div>`;
                insightsEl.innerHTML = `<div class="empty-state">${message}</div>`;
                selectedEventDetailsEl.innerHTML = `<div class="empty-state">${message}</div>`;
            });
    };

    refreshBtn.addEventListener('click', loadReport);
    fromDateEl.addEventListener('change', loadReport);
    toDateEl.addEventListener('change', loadReport);

    downloadBtn.addEventListener('click', () => {
        let range;
        try {
            range = validateRange();
        } catch (error) {
            alert(error.message);
            return;
        }

        const params = new URLSearchParams({ from_date: range.fromDate, to_date: range.toDate });
        window.location.href = `/unipulse/public/publisher/dashboard/downloadEventProfitReport?${params.toString()}`;
    });

    loadReport();
})();
