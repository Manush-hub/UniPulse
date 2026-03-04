// Initialize sponsor dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeSponsorDashboard();
    loadSponsorStats();
    loadActiveSponsorships();
});

// Initialize sponsor dashboard
function initializeSponsorDashboard() {
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    setupScrollAnimations();
}

// Load sponsor statistics
async function loadSponsorStats() {
    try {
        const response = await fetch('/unipulse/public/sponsor/dashboard/getStats');
        const data = await response.json();
        
        if (data.success) {
            // Update statistics in the welcome section
            document.getElementById('totalSponsorships').textContent = data.stats.active_sponsorships;
            document.getElementById('pendingRequests').textContent = data.stats.pending_requests;
            document.getElementById('totalInvestment').textContent = `LKR ${data.stats.total_investment.toLocaleString()}`;
        }
    } catch (error) {
        console.error('Error loading sponsor stats:', error);
    }
}

// Load active sponsorships (completed sponsorships for upcoming/ongoing events)
async function loadActiveSponsorships() {
    const grid = document.getElementById('sponsorshipsGrid');
    grid.innerHTML = '<div class="loading-spinner">Loading sponsorships...</div>';
    
    try {
        const response = await fetch('/unipulse/public/sponsor/dashboard/getActiveSponsorships');
        const data = await response.json();
        
        if (data.success) {
            grid.innerHTML = '';
            
            if (data.sponsorships && data.sponsorships.length > 0) {
                data.sponsorships.forEach(sponsorship => {
                    const card = createSponsorshipCard(sponsorship);
                    grid.appendChild(card);
                });
            } else {
                grid.innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <div class="empty-icon" style="font-size: 4rem; margin-bottom: 1rem;">🎯</div>
                        <h3 style="color: #1e293b; margin-bottom: 0.5rem;">No Active Sponsorships</h3>
                        <p style="color: #64748b; margin-bottom: 1.5rem;">You don't have any active sponsorships for upcoming events.</p>
                        <a href="/unipulse/public/sponsor/events?view=sponsor" class="btn btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Find Events to Sponsor
                        </a>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Error loading active sponsorships:', error);
        grid.innerHTML = '<div class="error-message">Failed to load sponsorships. Please try again.</div>';
    }
}

// Create sponsorship card
function createSponsorshipCard(sponsorship) {
    const card = document.createElement('div');
    card.className = 'sponsorship-card';
    card.onclick = () => viewEventDetails(sponsorship.event_id);

    const statusBadge = sponsorship.event_status === 'ongoing' 
        ? '<div class="sponsorship-badge status-ongoing">ongoing</div>'
        : '<div class="sponsorship-badge status-upcoming">upcoming</div>';

    const eventDate = new Date(sponsorship.event_date);
    const dateDisplay = formatDateFull(eventDate);

    card.innerHTML = `
        <div class="sponsorship-header">
            <h3 class="sponsorship-title">${escapeHtml(sponsorship.event_title)}</h3>
            <div class="sponsorship-organizer">${escapeHtml(sponsorship.organizer_name || 'Event Organizer')}</div>
            ${statusBadge}
        </div>
        <div class="sponsorship-content">
            <div class="sponsorship-details">
                <div class="sponsorship-detail">
                    <span class="detail-label">Package</span>
                    <span class="detail-value">${escapeHtml(sponsorship.package_name)} (${escapeHtml(sponsorship.package_type)})</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">Investment</span>
                    <span class="detail-value">LKR ${parseFloat(sponsorship.amount).toLocaleString()}</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">Event Date</span>
                    <span class="detail-value">${dateDisplay}</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">Location</span>
                    <span class="detail-value">${escapeHtml(sponsorship.venue_name || sponsorship.city || 'TBA')}</span>
                </div>
            </div>
            <div class="sponsorship-actions">
                <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); viewEventDetails(${sponsorship.event_id})">View Event</button>
                ${sponsorship.organizer_id ? `<button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); contactOrganizer(${sponsorship.organizer_id}, '${escapeHtml(sponsorship.organizer_name || 'Event Organizer')}')">Contact Organizer</button>` : ''}
            </div>
        </div>
    `;

    return card;
}

// View event details
function viewEventDetails(eventId) {
    window.location.href = `/unipulse/public/sponsor/eventview/${eventId}`;
}

// Contact organizer - redirect to messages
function contactOrganizer(publisherId, organizerName) {
    // Redirect to messages page with publisher parameter
    window.location.href = `/unipulse/public/sponsor/messages?publisher=${publisherId}&name=${encodeURIComponent(organizerName)}`;
}

// Update date and time
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    console.log('Current time:', now.toLocaleDateString('en-US', options));
}

// Format date (short format)
function formatDate(dateString) {
    const options = {
        month: 'short',
        day: 'numeric'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Format date (full format)
function formatDateFull(date) {
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    return date.toLocaleDateString('en-US', options);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}

// Setup scroll animations
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe sections for animation
    document.querySelectorAll('.sponsorship-card, .performance-card').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
}