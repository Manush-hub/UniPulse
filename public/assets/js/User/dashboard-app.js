// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    // loadUserData();
    loadUpcomingEvents();
    loadFeaturedEvents();
    loadRecentActivity();

});

// Initialize dashboard
function initializeDashboard() {
    // Set current date and time
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Add scroll animations
    setupScrollAnimations();
}

// Load user data
// function loadUserData() {
//     document.getElementById('username').textContent = userData.username;
//     document.getElementById('welcomeUsername').textContent = userData.displayName;
// }

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
    
    carousel.innerHTML = '';
    
    if (events.length === 0) {
        carousel.innerHTML = '<div class="no-data">No upcoming events</div>';
        return;
    }
    
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
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
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
        </div>
    `;
    
    return card;
}

// Load featured events from backend
function loadFeaturedEvents() {
    const grid = document.getElementById('featuredEventsGrid');
    if (!grid) return;
    
    grid.innerHTML = '<div class="loading">Loading featured events...</div>';
    
    fetch('/unipulse/public/user/dashboard/getFeaturedEvents')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch featured events');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.events) {
                displayFeaturedEvents(data.events);
            } else {
                grid.innerHTML = '<div class="no-data">No featured events</div>';
            }
        })
        .catch(error => {
            console.error('Error loading featured events:', error);
            grid.innerHTML = '<div class="no-data">Failed to load events</div>';
        });
}

// Display featured events
function displayFeaturedEvents(events) {
    const grid = document.getElementById('featuredEventsGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    if (events.length === 0) {
        grid.innerHTML = '<div class="no-data">No featured events</div>';
        return;
    }
    
    events.forEach(event => {
        const eventCard = createFeaturedEventCard(event);
        grid.appendChild(eventCard);
    });
}

// Create featured event card
function createFeaturedEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => viewEventDetails(event.id);
    
    card.innerHTML = `
        <div class="event-image">
            <svg class="placeholder-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <div class="event-category">${event.category}</div>
        </div>
        <div class="event-content">
            <h3 class="event-title">${event.title}</h3>
            <p class="event-description">${event.description}</p>
            <div class="event-meta">
                <span>${event.university}</span>
                <span>${formatDate(event.date)}</span>
            </div>
        </div>
    `;
    
    return card;
}

// Load recent activity from backend
function loadRecentActivity() {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    
    activityList.innerHTML = '<div class="loading">Loading activity...</div>';
    
    fetch('/unipulse/public/user/dashboard/getRecentActivity')
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
    
    activityList.innerHTML = '';
    
    if (activities.length === 0) {
        activityList.innerHTML = '<div class="no-data">No recent activity</div>';
        return;
    }
    
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
    window.location.href = `event-details.html?id=${eventId}`;
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