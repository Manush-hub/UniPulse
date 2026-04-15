// Initialize monthly evolution page on load
document.addEventListener('DOMContentLoaded', function () {
    initializeMonthlyEvolution();
});

let currentMonth = new Date().toISOString().substring(0, 7);

function initializeMonthlyEvolution() {
    // Set up month picker
    const monthPicker = document.getElementById('monthPicker');
    if (monthPicker) {
        monthPicker.value = currentMonth;
        monthPicker.addEventListener('change', function () {
            currentMonth = this.value;
            loadMonthlyEvolution(currentMonth);
        });
    }

    // Set up download button
    const downloadBtn = document.getElementById('downloadReportBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', downloadMonthlyReport);
    }

    // Load initial data
    loadMonthlyEvolution(currentMonth);
}

function loadMonthlyEvolution(month) {
    fetch(`/unipulse/public/user/dashboard/getMonthlyEvolution?month=${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateEvolutionSummary(data.data);
                displayVolunteering(data.data.volunteering);
                displayDonations(data.data.donations);
                displayParticipation(data.data.participation);
                updateOtherStats(data.data);
            } else {
                console.error('Failed to load monthly evolution:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading monthly evolution:', error);
        });
}

function updateEvolutionSummary(data) {
    // Update volunteer count
    document.getElementById('volunteerCount').textContent = data.totals.volunteerCount;

    // Update donation total
    document.getElementById('donationTotal').textContent = `LKR ${formatCurrency(data.totals.donations)}`;

    // Update participation count
    document.getElementById('participationCount').textContent = data.totals.participationCount;

    // Update event spending
    document.getElementById('eventSpending').textContent = `LKR ${formatCurrency(data.totals.eventSpending)}`;
}

function displayVolunteering(volunteering) {
    const container = document.getElementById('volunteeringContent');
    if (!container) return;

    if (!volunteering || volunteering.length === 0) {
        container.innerHTML = '<p class="no-data">No volunteering activities for this month.</p>';
        return;
    }

    container.innerHTML = volunteering.map(vol => `
        <div class="event-item">
            <div class="event-item-content">
                <div class="event-item-title">${vol.title}</div>
                <div class="event-item-details">
                    <span class="event-item-detail">
                        📍 ${vol.location}
                    </span>
                    <span class="event-item-detail">
                        📅 ${formatFullDate(vol.event_date)}
                    </span>
                    <span class="event-item-detail">
                        🎯 ${vol.volunteer_position || 'General Volunteer'}
                    </span>
                    <span class="event-item-detail">
                        📊 Status: ${capitalizeFirst(vol.volunteer_status)}
                    </span>
                </div>
            </div>
        </div>
    `).join('');
}

function displayDonations(donations) {
    const container = document.getElementById('donationsContent');
    if (!container) return;

    if (!donations || donations.length === 0) {
        container.innerHTML = '<p class="no-data">No donations made this month.</p>';
        return;
    }

    container.innerHTML = donations.map(don => `
        <div class="event-item">
            <div class="event-item-content">
                <div class="event-item-title">${don.event_title}</div>
                <div class="event-item-details">
                    <span class="event-item-detail">
                        📅 ${formatFullDate(don.created_at)}
                    </span>
                    <span class="event-item-detail">
                        📊 ${capitalizeFirst(don.status)}
                    </span>
                </div>
                <div class="event-item-amount">LKR ${formatCurrency(don.amount)}</div>
            </div>
        </div>
    `).join('');
}

function displayParticipation(participation) {
    const container = document.getElementById('participationContent');
    if (!container) return;

    if (!participation || participation.length === 0) {
        container.innerHTML = '<p class="no-data">No event participation this month.</p>';
        return;
    }

    container.innerHTML = participation.map(part => `
        <div class="event-item">
            <div class="event-item-content">
                <div class="event-item-title">${part.title}</div>
                <div class="event-item-details">
                    <span class="event-item-detail">
                        📍 ${part.location}
                    </span>
                    <span class="event-item-detail">
                        📅 ${formatFullDate(part.event_date)}
                    </span>
                    <span class="event-item-detail">
                        🎫 ${capitalizeFirst(part.ticket_type || 'Free')}
                    </span>
                    <span class="event-item-detail">
                        📂 ${part.category}
                    </span>
                </div>
                ${part.amount_paid > 0 ? `<div class="event-item-amount">LKR ${formatCurrency(part.amount_paid)}</div>` : '<div class="event-item-amount">Free</div>'}
            </div>
        </div>
    `).join('');
}

function updateOtherStats(data) {
    const totalActivities = data.totals.volunteerCount + data.totals.participationCount;
    const totalContribution = data.totals.donations + data.totals.eventSpending;

    document.getElementById('totalActivities').textContent = totalActivities;
    document.getElementById('totalContribution').textContent = `LKR ${formatCurrency(totalContribution)}`;

    // Determine most active category
    const categories = {};
    if (data.participation) {
        data.participation.forEach(part => {
            categories[part.category] = (categories[part.category] || 0) + 1;
        });
    }
    if (data.volunteering) {
        data.volunteering.forEach(vol => {
            categories[vol.category] = (categories[vol.category] || 0) + 1;
        });
    }

    const mostActive = Object.keys(categories).reduce((a, b) =>
        categories[a] > categories[b] ? a : b, '-');

    document.getElementById('mostActiveCategory').textContent = mostActive;
}

function downloadMonthlyReport() {
    const month = document.getElementById('monthPicker').value;

    fetch(`/unipulse/public/user/dashboard/downloadMonthlyReport?month=${month}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('Response received:', text.substring(0, 200));
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    // Decode base64 PDF and create blob
                    const binaryString = atob(data.pdf);
                    const bytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        bytes[i] = binaryString.charCodeAt(i);
                    }
                    const blob = new Blob([bytes], { type: 'application/pdf' });

                    // Create a temporary link and trigger download
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);

                    link.setAttribute('href', url);
                    link.setAttribute('download', data.filename);
                    link.style.visibility = 'hidden';

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('Error generating report: ' + (data.error || 'Unknown error'));
                }
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Response text:', text);
                alert('Error: Invalid server response. Check browser console.');
            }
        })
        .catch(error => {
            console.error('Error downloading report:', error);
            alert('Failed to download report. Please try again.');
        });
}

// Utility functions
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatFullDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
