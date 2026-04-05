// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    showVolunteerSuccessMessage();
    renderVolunteeringSection();
    startVolunteeringAutoRefresh();
    initializeDashboard();
    loadUserData();
    loadUpcomingEvents();
    loadRecentActivity();
    loadUserDonations();
    loadMyComments();
});

let volunteeringRefreshTimer = null;

function startVolunteeringAutoRefresh() {
    if (volunteeringRefreshTimer) {
        clearInterval(volunteeringRefreshTimer);
    }

    volunteeringRefreshTimer = setInterval(() => {
        renderVolunteeringSection();
    }, 30000);
}

function showVolunteerSuccessMessage() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('volunteer_applied') === '1') {
        const toast = document.createElement('div');
        toast.className = 'dashboard-success-toast';
        toast.innerHTML = `
            <div class="dashboard-success-toast-icon">✓</div>
            <div class="dashboard-success-toast-content">
                <h4>Volunteer Application Submitted</h4>
                <p>You’ve successfully applied as a volunteer. The organizer will contact you soon.</p>
            </div>
            <button class="dashboard-success-toast-close" aria-label="Close">×</button>
        `;

        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));

        const closeToast = () => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 250);
        };

        const closeBtn = toast.querySelector('.dashboard-success-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeToast);
        }

        setTimeout(closeToast, 5000);

        params.delete('volunteer_applied');
        const cleanQuery = params.toString();
        const cleanUrl = `${window.location.pathname}${cleanQuery ? `?${cleanQuery}` : ''}`;
        window.history.replaceState({}, document.title, cleanUrl);
    }
}

async function renderVolunteeringSection() {
    const volunteeringSection = document.getElementById('volunteeringSection');
    const volunteeringCard = document.getElementById('volunteeringCard');

    if (!volunteeringSection || !volunteeringCard) {
        return;
    }

    try {
        const response = await fetch('/unipulse/public/user/dashboard/getVolunteeringStatus', {
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Failed to fetch volunteering status');
        }

        const data = await response.json();
        if (!data.success || !data.hasApplication) {
            volunteeringSection.style.display = 'none';
            return;
        }

        const applications = Array.isArray(data.applications)
            ? data.applications
            : (data.application ? [data.application] : []);

        if (applications.length === 0) {
            volunteeringSection.style.display = 'none';
            return;
        }

        const rowsHtml = applications.map(application => {
            const eventDateText = formatFullDate(application.event_date);
            const eventDateTimeText = `${eventDateText}${application.event_time ? ` at ${application.event_time}` : ''}`;
            const statusText = formatVolunteerStatus(application.status);
            const statusClass = getVolunteerStatusClass(application.status);
            const appliedAt = formatDateTimeShort(application.applied_at);

            return `
                <tr>
                    <td>${escapeHtmlDash(application.event_title || 'Volunteer Application')}</td>
                    <td>${eventDateTimeText}</td>
                    <td>
                        <span class="volunteer-status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>${appliedAt}</td>
                </tr>
            `;
        }).join('');

        volunteeringCard.innerHTML = `
            <div class="section-header">
                <h2>Your Volunteering</h2>
                <a class="view-all" href="/unipulse/public/user/events">Browse More</a>
            </div>
            <div class="volunteering-table-container donations-table-container">
                <div class="volunteering-list-header">Submitted Applications (${applications.length})</div>
                <div class="donations-table-wrap">
                    <table class="donations-table volunteering-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        volunteeringSection.style.display = 'block';
    } catch (error) {
        console.error('Error loading volunteering section:', error);
        volunteeringSection.style.display = 'none';
    }
}

function formatVolunteerStatus(status) {
    const map = {
        pending: 'Pending',
        accepted: 'Accepted',
        rejected: 'Rejected',
        completed: 'Completed'
    };

    return map[status] || 'Application Sent';
}

function getVolunteerStatusClass(status) {
    const normalized = String(status || 'pending').toLowerCase();
    if (normalized === 'accepted' || normalized === 'completed') return 'approved';
    if (normalized === 'rejected') return 'rejected';
    return 'pending';
}

function formatFullDate(dateString) {
    if (!dateString) return 'Date TBA';

    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };

    return new Date(dateString).toLocaleDateString('en-US', options);
}

function formatDateTimeShort(dateTimeString) {
    if (!dateTimeString) return 'N/A';

    const parsed = new Date(dateTimeString);
    if (Number.isNaN(parsed.getTime())) return 'N/A';

    return parsed.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

// Initialize dashboard
function initializeDashboard() {
    // Set current date and time
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute

    // Add scroll animations
    setupScrollAnimations();
}

// Load user data
function loadUserData() {
    // Use user data passed from PHP
    if (window.userData && window.userData.name) {
        const welcomeUsername = document.getElementById('welcomeUsername');
        if (welcomeUsername) {
            welcomeUsername.textContent = window.userData.name;
        }
    }
}

// Load upcoming events from backend
function loadUpcomingEvents() {
    const carousel = document.getElementById('upcomingEventsCarousel');
    if (!carousel) return;

    carousel.innerHTML = '<div class="loading">Loading events...</div>';

    fetch('/unipulse/public/user/dashboard/getUpcomingEvents', {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch upcoming events');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.events) {
                displayUpcomingEvents(data.events);
            } else {
                carousel.innerHTML = '<div class="no-data">No upcoming events</div>';
            }
        })
        .catch(error => {
            console.error('Error loading upcoming events:', error);
            carousel.innerHTML = '<div class="no-data">Failed to load events</div>';
        });
}

// Display upcoming events
function displayUpcomingEvents(events) {
    const carousel = document.getElementById('upcomingEventsCarousel');
    const ticketsCarousel = document.getElementById('myTicketsCarousel');
    if (!carousel) return;

    const upcomingEvents = Array.isArray(events)
        ? events.filter(isFutureEvent)
        : [];

    if (upcomingEvents.length === 0) {
        carousel.innerHTML = '<div class="no-data">No upcoming events. Register for events to see them here!</div>';
        if (ticketsCarousel) {
            ticketsCarousel.innerHTML = '<div class="no-data">You have no tickets yet.</div>';
        }
        return;
    }

    carousel.innerHTML = '';
    if (ticketsCarousel) ticketsCarousel.innerHTML = '';

    let hasTickets = false;
    let seenEventIds = new Set();

    events.forEach(event => {
        // Add to Upcoming Events ONCE per event
        if (!seenEventIds.has(event.id)) {
            const eventCard = createUpcomingEventCard(event);
            carousel.appendChild(eventCard);
            seenEventIds.add(event.id);
        }

        // Add to My Tickets if they have an order_number (bought ticket)
        if (ticketsCarousel && event.order_number) {
            const ticketCard = createTicketCard(event);
            ticketsCarousel.appendChild(ticketCard);
            hasTickets = true;
        }
    });

    if (ticketsCarousel && !hasTickets) {
        ticketsCarousel.innerHTML = '<div class="no-data">You have no tickets yet.</div>';
    }
}

// Create a special smaller card for the tickets
function createTicketCard(event) {
    const card = document.createElement('div');
    // Using a distinct class to drop the standard event-card styling completely
    card.className = 'my-ticket-stub';
    card.onclick = () => viewEventDetails(event.id);
    
    // Apply boarding-pass-style CSS dynamically
    card.style.cssText = 'background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; height: 100%; position: relative;';
    
    card.addEventListener('mouseover', () => {
        card.style.transform = 'translateY(-4px)';
        card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
    });
    card.addEventListener('mouseout', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.06)';
    });

    const isPast = new Date(event.date) < new Date() ? true : false;
    const ticketStatus = isPast ? 'PAST EVENT' : 'ADMIT ONE';
    const statusColor = isPast ? '#94a3b8' : '#4338ca';
    const statusBg = isPast ? '#f1f5f9' : '#e0e7ff';

    card.innerHTML = `
        <div style="padding: 20px; border-bottom: 2px dashed #cbd5e1; position: relative; background: #ffffff;">
            <!-- Perforated cutouts on the ticket separator -->
            <div style="position: absolute; bottom: -12px; left: -1px; width: 24px; height: 24px; background: #f4f4f4; border-radius: 50%; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; transform: rotate(45deg); z-index: 1;"></div>
            <div style="position: absolute; bottom: -12px; right: -1px; width: 24px; height: 24px; background: #f4f4f4; border-radius: 50%; border-left: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; transform: rotate(-45deg); z-index: 1;"></div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span style="background: ${statusBg}; color: ${statusColor}; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">${ticketStatus}</span>
                <span style="color: #64748b; font-size: 13px; font-weight: 700;"><i class="far fa-calendar-alt"></i> ${formatDate(event.date)}</span>
            </div>
            
            <h3 style="margin: 0 0 10px 0; font-size: 18px; color: #0f172a; line-height: 1.3; font-weight: 700;">${event.title}</h3>
            
            <div style="color: #475569; font-size: 13px; display: flex; align-items: center; gap: 6px; font-weight: 600;">
                <i class="far fa-clock"></i> ${event.time}
            </div>
        </div>
        
        <div style="padding: 20px; background: #f8fafc; flex: 1; display: flex; flex-direction: column; position: relative;">
            <div style="margin-bottom: 16px;">
                <p style="margin: 0; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Location</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #1e293b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fas fa-map-marker-alt" style="color: #94a3b8; margin-right: 4px;"></i> ${event.location}</p>
            </div>
            
            <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
                <div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Order Ref</p>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #1e293b; font-family: 'Courier New', Courier, monospace; font-weight: 700;">${event.order_number}</p>
                </div>
                <div style="text-align: right;">
                    <i class="fas fa-qrcode" style="font-size: 32px; color: #cbd5e1; opacity: 0.6;"></i>
                </div>
            </div>
            
            <div style="margin-top: auto; padding-top: 10px;">
                <a href="/UniPulse/public/ticket/download?order=${encodeURIComponent(event.order_number)}" target="_blank" onclick="event.stopPropagation();" style="display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%; border: 2px dashed #cbd5e1; background: white; color: #475569; padding: 12px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 700; box-sizing: border-box; transition: all 0.2s;">
                    <i class="fas fa-print"></i> Download e-Ticket
                </a>
            </div>
        </div>
    `;

    // Add a simple hover effect for the button
    const btn = card.querySelector('a');
    if (btn) {
        btn.addEventListener('mouseover', () => { 
            btn.style.borderColor = '#3b82f6';
            btn.style.color = '#3b82f6';
            btn.style.background = '#eff6ff';
        });
        btn.addEventListener('mouseout', () => { 
            btn.style.borderColor = '#cbd5e1';
            btn.style.color = '#475569';
            btn.style.background = 'white';
        });
    }

    return card;
}

function isFutureEvent(event) {
    if (!event || !event.date) return false;

    const eventDateTime = new Date(`${event.date} ${event.time && String(event.time).trim() ? event.time : '23:59:59'}`);
    if (Number.isNaN(eventDateTime.getTime())) {
        return false;
    }

    return eventDateTime.getTime() >= Date.now();
}

// Create upcoming event card
function createUpcomingEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card-mini';
    card.onclick = () => viewEventDetails(event.id);

    const coverImageUrl = resolveEventImageUrl(event.image_url);

    card.innerHTML = `
        <div class="event-image-mini">
            ${coverImageUrl ? `<img src="${coverImageUrl}" alt="${event.title}" loading="lazy" onerror="this.remove()">` : `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>`}
            <div class="event-date-badge">${formatDate(event.date)}</div>
        </div>
        <div class="event-content-mini">
            <h3 class="event-title-mini">${event.title}</h3>
            <div class="event-time">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                ${event.time} • ${event.location}
            </div>
            <div class="event-organizer">${event.organizer_name || event.organizer || event.university}</div>
        </div>
    `;

    return card;
}

function resolveEventImageUrl(imageUrl) {
    if (!imageUrl) return null;

    const url = String(imageUrl).trim();
    if (!url) return null;

    if (/^(https?:)?\/\//i.test(url) || url.startsWith('data:') || url.startsWith('blob:')) {
        return url;
    }

    if (url.startsWith('/unipulse/public/')) {
        return url;
    }

    if (url.startsWith('/uploads/')) {
        return `/unipulse/public${url}`;
    }

    if (url.startsWith('/public/')) {
        return `/unipulse${url}`;
    }

    if (url.startsWith('uploads/')) {
        return `/unipulse/public/${url}`;
    }

    if (url.startsWith('public/')) {
        return `/unipulse/${url}`;
    }

    return `/unipulse/public/${url.replace(/^\/+/, '')}`;
}


// Load recent activity from backend
function loadRecentActivity() {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;

    activityList.innerHTML = '<div class="loading">Loading activity...</div>';

    fetch('/unipulse/public/user/dashboard/getRecentActivity', {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch recent activity');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.activities) {
                displayRecentActivity(data.activities);
            } else {
                activityList.innerHTML = '<div class="no-data">No recent activity</div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
            activityList.innerHTML = '<div class="no-data">Failed to load activity</div>';
        });
}

function loadUserDonations() {
    const container = document.getElementById('donationsTableContainer');
    if (!container) return;

    container.innerHTML = '<div class="loading">Loading donations...</div>';

    fetch('/unipulse/public/user/dashboard/getUserDonations', {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch donations');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.donations)) {
                renderDonationsTable(data.donations);
            } else {
                container.innerHTML = '<div class="no-data">No donations yet</div>';
            }
        })
        .catch(error => {
            console.error('Error loading donations:', error);
            container.innerHTML = '<div class="no-data">Failed to load donations</div>';
        });
}

function renderDonationsTable(donations) {
    const container = document.getElementById('donationsTableContainer');
    if (!container) return;

    if (!donations || donations.length === 0) {
        container.innerHTML = '<div class="no-data">No donations yet</div>';
        return;
    }

    const rows = donations.map(donation => {
        const amount = Number(donation.amount || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const donatedDate = donation.donated_date
            ? new Date(donation.donated_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            })
            : 'N/A';

        let statusClass = 'pending';
        if (donation.status === 'accepted' || donation.status === 'completed') {
            statusClass = 'approved';
        } else if (donation.status === 'rejected' || donation.status === 'failed' || donation.status === 'refunded') {
            statusClass = 'rejected';
        }

        return `
            <tr>
                <td>${escapeHtmlDash(donation.event_name || 'Event')}</td>
                <td>${donatedDate}</td>
                <td>${escapeHtmlDash(donation.currency || 'LKR')} ${amount}</td>
                <td>
                    <span class="donation-status-badge ${statusClass}">
                        ${escapeHtmlDash(donation.status_label || 'Pending')}
                    </span>
                </td>
            </tr>
        `;
    }).join('');

    container.innerHTML = `
        <div class="donations-table-wrap">
            <table class="donations-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Donated Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
    `;
}

// Display recent activity
function displayRecentActivity(activities) {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;

    if (!activities || activities.length === 0) {
        activityList.innerHTML = '<div class="no-data">No recent activity</div>';
        return;
    }

    activityList.innerHTML = '';

    activities.forEach(activity => {
        const activityItem = createActivityItem(activity);
        activityList.appendChild(activityItem);
    });
}

// Create activity item
function createActivityItem(activity) {
    const item = document.createElement('div');
    item.className = 'activity-item';

    const iconSvg = getActivityIcon(activity.icon);

    item.innerHTML = `
        <div class="activity-icon">
            ${iconSvg}
        </div>
        <div class="activity-content">
            <h4>${activity.title}</h4>
            <p>${activity.description}</p>
            <div class="activity-time">${activity.time}</div>
        </div>
    `;

    return item;
}

// Get activity icon
function getActivityIcon(iconType) {
    const icons = {
        calendar: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
        plus: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
        bell: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        award: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21,13.89 7,23 12,20 17,23 15.79,13.88"></polyline></svg>'
    };

    return icons[iconType] || icons.calendar;
}



// View event details
function viewEventDetails(eventId) {
    window.location.href = `/unipulse/public/user/eventview/${eventId}`;
}

// Update date and time
function updateDateTime() {
    // Reserved for optional date/time UI rendering
}

// Format date
function formatDate(dateString) {
    const options = {
        month: 'short',
        day: 'numeric'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
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
    document.querySelectorAll('.action-card, .event-card, .event-card-mini').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
}

// Add smooth scrolling for carousel
function scrollCarousel(direction) {
    const carousel = document.getElementById('upcomingEventsCarousel');
    const scrollAmount = 300;

    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// ── Your Comments ─────────────────────────────────────────
function loadMyComments() {
    const list = document.getElementById('myCommentsList');
    if (!list) return;

    list.innerHTML = '<div class="loading">Loading your comments…</div>';

    fetch('/unipulse/public/user/dashboard/getMyComments')
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                list.innerHTML = '<div class="my-comments-empty"><i class="fas fa-comment-slash"></i><p>Could not load comments.</p></div>';
                return;
            }
            if (!data.comments || data.comments.length === 0) {
                list.innerHTML = '<div class="my-comments-empty"><i class="fas fa-comments"></i><p>You haven\'t commented on any events yet.</p></div>';
                return;
            }
            list.innerHTML = data.comments.map(c => buildCommentCard(c)).join('');
        })
        .catch(() => {
            list.innerHTML = '<div class="my-comments-empty"><i class="fas fa-exclamation-circle"></i><p>Failed to load comments.</p></div>';
        });
}

function buildCommentCard(c) {
    const stars = c.rating
        ? ('★'.repeat(c.rating) + '☆'.repeat(5 - c.rating))
        : '';
    const editedTag = c.is_edited ? ' <span style="font-size:.75rem;color:#9ca3af;">(edited)</span>' : '';

    const textContent = c.is_hidden
        ? `<span style="opacity:.5; font-style:italic;">${escapeHtmlDash(c.comment_text)}</span>`
        : escapeHtmlDash(c.comment_text);

    const hiddenBadge = c.is_hidden
        ? `<span class="mc-hidden-badge" onclick="showHiddenReason(${c.id})" title="Click to see reason">
               <i class="fas fa-eye-slash"></i> Hidden by moderator
           </span>`
        : '';

    return `
        <div class="my-comment-card ${c.is_hidden ? 'is-hidden' : ''}" data-comment-id="${c.id}"
             data-hidden-reason="${escapeAttr(c.hidden_reason || '')}"
             data-hidden-by="${escapeAttr(c.hidden_by_name || 'Moderator')}">
            <div class="mc-top">
                <span class="mc-event">
                    <i class="fas fa-calendar-alt" style="margin-right:.3rem;"></i>${escapeHtmlDash(c.event_title)}
                </span>
                <span class="mc-meta">
                    ${stars ? `<span class="mc-rating">${stars}</span>` : ''}
                    <span>${c.formatted_date}</span>
                </span>
            </div>
            <div class="mc-text">${textContent}${editedTag}</div>
            ${hiddenBadge}
        </div>`;
}

function showHiddenReason(commentId) {
    const card = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!card) return;
    const reason = card.dataset.hiddenReason || 'No reason provided.';
    const hiddenBy = card.dataset.hiddenBy || 'Moderator';
    document.getElementById('hiddenReasonText').textContent = reason;
    document.getElementById('hiddenByLine').textContent = 'Hidden by: ' + hiddenBy;
    const modal = document.getElementById('hiddenReasonModal');
    modal.style.display = 'flex';
}

// close hidden reason modal on backdrop click
document.addEventListener('click', function (e) {
    const modal = document.getElementById('hiddenReasonModal');
    if (modal && e.target === modal) modal.style.display = 'none';
});

function escapeHtmlDash(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function escapeAttr(text) {
    return (text || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

/* Extracted from User/dashboard.view.php */

        window.userData = <?php echo json_encode([
                                'name' => $user['name'] ?? 'User',
                                'email' => $user['email'] ?? '',
                                'type' => $user['type'] ?? 'user',
                                'university' => $user['university'] ?? ''
                            ]); ?>;
    
