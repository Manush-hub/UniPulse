// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    showVolunteerSuccessMessage();
    renderVolunteeringSection();
    startVolunteeringAutoRefresh();
    initializeDashboard();
    loadUserData();
    loadUpcomingEvents();
    loadRecentActivity();
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
        if (!data.success || !data.hasApplication || !data.application) {
            volunteeringSection.style.display = 'none';
            return;
        }

        const application = data.application;
        const eventDateText = formatFullDate(application.event_date);
        const statusText = formatVolunteerStatus(application.status);
        const eventDateTimeText = `${eventDateText}${application.event_time ? ` at ${application.event_time}` : ''}`;

        volunteeringCard.innerHTML = `
            <div class="section-header">
                <h2>Your Volunteering</h2>
                <a class="view-all" href="/unipulse/public/user/events">Browse More</a>
            </div>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon">🤝</div>
                    <div class="activity-content">
                        <h4>${application.event_title || 'Volunteer Application'}</h4>
                        <p>Status: <strong>${statusText}</strong></p>
                        <span class="activity-time">${eventDateTimeText}</span>
                    </div>
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
        pending: 'Pending Review',
        accepted: 'Accepted',
        rejected: 'Rejected',
        completed: 'Completed'
    };

    return map[status] || 'Application Sent';
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

    fetch('/unipulse/public/user/dashboard/getUpcomingEvents')
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
    if (!carousel) return;

    if (!events || events.length === 0) {
        carousel.innerHTML = '<div class="no-data">No upcoming events. Register for events to see them here!</div>';
        return;
    }

    carousel.innerHTML = '';

    events.forEach(event => {
        const eventCard = createUpcomingEventCard(event);
        carousel.appendChild(eventCard);
    });
}

// Create upcoming event card
function createUpcomingEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card-mini';
    card.onclick = () => viewEventDetails(event.id);

    card.innerHTML = `
        <div class="event-image-mini">
            ${event.image_url ? `<img src="${event.image_url}" alt="${event.title}">` : `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    // You can add a date/time display element if needed
    console.log('Current time:', now.toLocaleDateString('en-US', options));
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
