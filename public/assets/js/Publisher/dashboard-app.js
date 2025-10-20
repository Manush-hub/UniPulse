// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    try {
        initializeDashboard();
        loadEventsManagement();
        loadVolunteerData();
        loadRecentActivity();
        setupEventListeners();
        animateProgressBars();
        
        // Load comments last and independently
        setTimeout(() => {
            try {
                loadRecentComments();
            } catch (error) {
                console.error('Error loading comments:', error);
            }
        }, 100);
        
    } catch (error) {
        console.error('Error initializing dashboard:', error);
    }
});


const eventsData = [
    {
        id: 1,
        title: 'Annual Tech Summit 2024: Innovate Tomorrow',
        date: '2024-12-15',
        location: 'Tech Conference Center',
        status: 'upcoming',
        image: '/unipulse/public/assets/images/event1.jpg'
    },
    {
        id: 2,
        title: 'Spring Arts Festival: Creative Expressions',
        date: '2024-04-20',
        location: 'City Art Gallery Downtown',
        status: 'ongoing',
        image: '/unipulse/public/assets/images/event2.jpg'
    },
    {
        id: 3,
        title: 'Alumni Networking Gala 2024',
        date: '2024-11-18',
        location: 'Grand Hotel Ballroom',
        status: 'new',
        image: '/unipulse/public/assets/images/event3.jpg'
    },
    {
        id: 4,
        title: 'Student Innovation Challenge',
        date: '2025-01-25',
        location: 'Innovation Hub',
        status: 'upcoming',
        image: '/unipulse/public/assets/images/event4.jpg'
    },
    {
        id: 5,
        title: 'Winter Charity Run',
        date: '2024-12-10',
        location: 'City University Track',
        status: 'ongoing',
        image: '/unipulse/public/assets/images/event5.jpg'
    }
];

const volunteerApplications = [
    {
        id: 1,
        name: 'John Doe',
        initials: 'JD',
        role: 'Event Setup Crew',
        status: 'pending'
    },
    {
        id: 2,
        name: 'Alice Smith',
        initials: 'AS',
        role: 'Registration Desk',
        status: 'interviewed'
    },
    {
        id: 3,
        name: 'Robert Johnson',
        initials: 'RJ',
        role: 'Guest Services',
        status: 'accepted'
    },
    {
        id: 4,
        name: 'Emma Wilson',
        initials: 'EW',
        role: 'Catering Assistant',
        status: 'rejected'
    }
];

const volunteerShifts = [
    {
        id: 1,
        name: 'Michael Jones',
        initials: 'MJ',
        shift: 'Morning Shift (9am-1pm)',
        status: 'confirmed'
    },
    {
        id: 2,
        name: 'Sarah Davis',
        initials: 'SD',
        shift: 'Afternoon Shift (1pm-5pm)',
        status: 'confirmed'
    },
    {
        id: 3,
        name: 'Thomas Brown',
        initials: 'TB',
        shift: 'Evening Shift (5pm-9pm)',
        status: 'pending'
    },
    {
        id: 4,
        name: 'Lisa White',
        initials: 'LW',
        shift: 'All Day (9am-5pm)',
        status: 'interviewed'
    }
];

const recentActivity = [
    {
        id: 1,
        type: 'ticket',
        title: 'New ticket type added to "Annual Tech Summit"',
        description: 'Early bird tickets now available',
        time: '2 hours ago',
        icon: 'ticket'
    },
    {
        id: 2,
        type: 'volunteer',
        title: 'New volunteer registered for the event',
        description: 'Alice Smith applied for Registration Desk role',
        time: 'Yesterday at 4:30 PM',
        icon: 'user-plus'
    },
    {
        id: 3,
        type: 'sponsorship',
        title: 'New sponsorship confirmed from Microsoft',
        description: 'Platinum tier sponsorship secured',
        time: 'Yesterday at 11:45 AM',
        icon: 'handshake'
    },
    {
        id: 4,
        type: 'report',
        title: 'Monthly financial report generated',
        description: 'October 2024 financial summary available',
        time: 'October 25, 2024 at 9:30 AM',
        icon: 'file-text'
    },
    {
        id: 5,
        type: 'update',
        title: 'Event "Winter Charity Run" schedule updated',
        description: 'Start time changed to 8:00 AM',
        time: 'October 24, 2024 at 3:15 PM',
        icon: 'calendar'
    }
];



// Dashboard initialization
function initializeDashboard() {
    console.log('Dashboard initialized');
    setupModals();
}

// Load organizer data


// Load events management
function loadEventsManagement() {
    const eventsList = document.querySelector('.events-list');
    if (!eventsList) {
        console.log('Events list container not found');
        return;
    }

    // Show loading state
    eventsList.innerHTML = `
        <div class="loading-events">
            <div class="spinner"></div>
            <p>Loading your events...</p>
        </div>
    `;

    fetch('/unipulse/public/publisher/dashboard/getMyEvents')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayEvents(data.events);
            } else {
                console.error('Events API error:', data.error);
                eventsList.innerHTML = `
                    <div class="error-message">
                        <p>Failed to load events: ${data.error}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading events:', error);
            // Fallback to static data for now
            displayStaticEvents();
        });
}

// Display events from API
function displayEvents(events) {
    const eventsList = document.querySelector('.events-list');
    
    if (events.length === 0) {
        eventsList.innerHTML = `
            <div class="no-events">
                <div class="no-events-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <h3>No Events Yet</h3>
                <p>Start creating events to manage them from your dashboard.</p>
                <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/publisher/createevent'">
                    Create Your First Event
                </button>
            </div>
        `;
        return;
    }

    eventsList.innerHTML = '';
    events.forEach(event => {
        const eventItem = document.createElement('div');
        eventItem.className = 'event-item';
        
        const statusClass = getStatusClass(event.status);
        const eventImage = event.image_url || '/unipulse/public/assets/images/default-event.jpg';
        
        eventItem.innerHTML = `
            <div class="event-image" style="background-image: url('${eventImage}'); background-size: cover; background-position: center;"></div>
            <div class="event-details">
                <h3 class="event-title">${event.title}</h3>
                <div class="event-meta">
                    <span><i class="fas fa-calendar"></i> ${event.formatted_date}</span>
                    <span><i class="fas fa-map-marker-alt"></i> ${event.location}</span>
                </div>
                <div class="event-stats">
                    <span><i class="fas fa-comments"></i> ${event.comment_count} comments</span>
                    ${event.avg_rating ? `<span><i class="fas fa-star"></i> ${event.avg_rating}/5</span>` : ''}
                </div>
                <span class="event-status ${statusClass}">${capitalizeFirstLetter(event.status)}</span>
            </div>
            <div class="event-actions">
                <button class="action-btn" onclick="viewEvent(${event.id})">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="action-btn" onclick="editEvent(${event.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn delete" onclick="showDeleteModal(${event.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        `;
        eventsList.appendChild(eventItem);
    });
}

// Fallback to display static events
function displayStaticEvents() {
    const eventsList = document.querySelector('.events-list');
    eventsList.innerHTML = '';

    eventsData.forEach(event => {
        const eventItem = document.createElement('div');
        eventItem.className = 'event-item';
        eventItem.innerHTML = `
            <div class="event-image" style="background: linear-gradient(45deg, #1E3A8A, #F97316);"></div>
            <div class="event-details">
                <h3 class="event-title">${event.title}</h3>
                <div class="event-meta">
                    <span>${formatDate(event.date)}</span>
                    <span>${event.location}</span>
                </div>
                <span class="event-status status-${event.status}">${capitalizeFirstLetter(event.status)}</span>
            </div>
            <div class="event-actions">
                <button class="action-btn" onclick="editEvent(${event.id})">Edit</button>
                <button class="action-btn delete" onclick="showDeleteModal(${event.id})">Delete</button>
            </div>
        `;
        eventsList.appendChild(eventItem);
    });
}

// Get status class for styling
function getStatusClass(status) {
    const statusClasses = {
        'active': 'status-active',
        'upcoming': 'status-upcoming',
        'ongoing': 'status-ongoing',
        'completed': 'status-completed',
        'cancelled': 'status-cancelled',
        'draft': 'status-draft'
    };
    return statusClasses[status] || 'status-unknown';
}

// Load volunteer data
function loadVolunteerData() {
    const applicationsList = document.querySelector('.volunteer-list.applications');
    const shiftsList = document.querySelector('.volunteer-list.shifts');

    if (applicationsList) {
        applicationsList.innerHTML = '';
        volunteerApplications.forEach(volunteer => {
            const volunteerItem = document.createElement('div');
            volunteerItem.className = 'volunteer-item';
            volunteerItem.innerHTML = `
                <div class="volunteer-info">
                    <div class="volunteer-avatar">${volunteer.initials}</div>
                    <div>
                        <div class="volunteer-name">${volunteer.name}</div>
                        <div>${volunteer.role}</div>
                    </div>
                </div>
                <span class="volunteer-status status-${volunteer.status}">${capitalizeFirstLetter(volunteer.status)}</span>
            `;
            applicationsList.appendChild(volunteerItem);
        });
    }

    if (shiftsList) {
        shiftsList.innerHTML = '';
        volunteerShifts.forEach(volunteer => {
            const volunteerItem = document.createElement('div');
            volunteerItem.className = 'volunteer-item';
            volunteerItem.innerHTML = `
                <div class="volunteer-info">
                    <div class="volunteer-avatar">${volunteer.initials}</div>
                    <div>
                        <div class="volunteer-name">${volunteer.name}</div>
                        <div>${volunteer.shift}</div>
                    </div>
                </div>
                <span class="volunteer-status status-${volunteer.status}">${capitalizeFirstLetter(volunteer.status)}</span>
            `;
            shiftsList.appendChild(volunteerItem);
        });
    }
}

// Load recent activity
function loadRecentActivity() {
    const activityList = document.querySelector('.activity-list');
    if (!activityList) return;

    activityList.innerHTML = '';

    recentActivity.forEach(activity => {
        const activityItem = document.createElement('div');
        activityItem.className = 'activity-item';
        activityItem.innerHTML = `
            <div class="activity-icon">
                <i class="fas fa-${activity.icon}"></i>
            </div>
            <div class="activity-content">
                <h4>${activity.title}</h4>
                <p>${activity.description}</p>
                <span class="activity-time">${activity.time}</span>
            </div>
        `;
        activityList.appendChild(activityItem);
    });
}



// Setup event listeners
function setupEventListeners() {
    // Quick action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            handleQuickAction(action);
        });
    });

    // Close modal buttons
    const closeButtons = document.querySelectorAll('.close-button, .cancel-delete');
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    });
}




// Setup modals
function setupModals() {
    const deleteModal = document.getElementById('deleteModal');
    const closeButtons = document.querySelectorAll('.close-button, .cancel-delete');

    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (deleteModal) {
                deleteModal.classList.remove('show');
            }
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function (e) {
        if (deleteModal && e.target === deleteModal) {
            deleteModal.classList.remove('show');
        }
    });
}

// Handle quick actions
function handleQuickAction(action) {
    switch (action) {
        case 'create-event':
            window.location.href = '/unipulse/organizer/create-event.html';
            break;
        case 'manage-volunteers':
            window.location.href = '/unipulse/organizer/volunteers.html';
            break;
        case 'view-reports':
            window.location.href = '/unipulse/organizer/reports.html';
            break;
        case 'sponsorships':
            window.location.href = '/unipulse/organizer/sponsors.html';
            break;
        case 'ticket-sales':
            window.location.href = '/unipulse/organizer/tickets.html';
            break;
        case 'event-settings':
            window.location.href = '/unipulse/organizer/settings.html';
            break;
        default:
            console.log('Action not implemented:', action);
    }
}

// Show delete confirmation modal
function showDeleteModal(eventId) {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.add('show');

        // Set up the confirm button
        const confirmBtn = modal.querySelector('.confirm-delete');
        if (confirmBtn) {
            confirmBtn.onclick = function () {
                deleteEvent(eventId);
                modal.classList.remove('show');
            };
        }
    }
}

function deleteEvent(eventId) {
    console.log('Deleting event with ID:', eventId);
    // In a real application, this would make an API call to delete the event

    // Show success message
    if (typeof showToast === 'function') {
        showToast('Event deleted successfully', 'success');
    }

    // Remove from UI
    const eventItem = document.querySelector(`.event-item[data-id="${eventId}"]`);
    if (eventItem) {
        eventItem.remove();
    }

    // Reload events list
    loadEventsManagement();
}

function viewEvent(eventId) {
    console.log('Viewing event with ID:', eventId);
    if (typeof showToast === 'function') {
        showToast('Loading event details...', 'info');
    }
    // Redirect to event view page
    window.location.href = `/unipulse/public/publisher/eventview?id=${eventId}`;
}

function editEvent(eventId) {
    console.log('Editing event with ID:', eventId);
    if (typeof showToast === 'function') {
        showToast('Loading event editor...', 'info');
    }
    // Redirect to edit event page
    window.location.href = `/unipulse/public/publisher/editevent/${eventId}`;
}



// Animate progress bars
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill, .donation-fill');

    progressBars.forEach(bar => {
        const targetWidth = bar.getAttribute('data-width') || '75%';
        bar.style.width = targetWidth;
    });
}



// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Capitalize first letter
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


function exportReport(type) {
    console.log(`Exporting ${type} report...`);
    if (typeof showToast === 'function') {
        showToast(`Preparing ${type} report for download`, 'info');
    }

    // In a real application, this would generate and download a report
    setTimeout(() => {
        if (typeof showToast === 'function') {
            showToast(`${type} report downloaded successfully`, 'success');
        }
    }, 2000);
}

function changeVolunteerStatus(volunteerId, newStatus) {
    console.log(`Changing volunteer ${volunteerId} status to ${newStatus}`);
    if (typeof showToast === 'function') {
        showToast(`Volunteer status updated to ${newStatus}`, 'success');
    }
}

function updateSponsorship(sponsorId, action) {
    console.log(`${action} sponsorship for sponsor ${sponsorId}`);
    if (typeof showToast === 'function') {
        showToast(`Sponsorship ${action} successfully`, 'success');
    }
}





// Initialize charts (placeholder for future implementation)
function initializeCharts() {
    console.log('Initializing charts...');
    // This would initialize data visualization charts using a library like Chart.js
}

// Search functionality
function searchEvents(query) {
    console.log('Searching events for:', query);
    // This would filter events based on the search query
}

// Filter events by status
function filterEvents(status) {
    console.log('Filtering events by status:', status);
    // This would filter events based on the selected status
}

// Sort events
function sortEvents(criteria) {
    console.log('Sorting events by:', criteria);
    // This would sort events based on the selected criteria
}

// Handle form submissions
function handleFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (typeof callback === 'function') {
                callback(form);
            }
        });
    }
}

// Validate form fields
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            highlightError(input, 'This field is required');
        } else {
            clearError(input);
        }
    });

    return isValid;
}

// Highlight field error
function highlightError(field, message) {
    field.style.borderColor = '#ef4444';

    let errorElement = field.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.style.color = '#ef4444';
        errorElement.style.fontSize = '0.8rem';
        errorElement.style.marginTop = '0.25rem';
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }

    errorElement.textContent = message;
}

// Clear field error
function clearError(field) {
    field.style.borderColor = '';

    const errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }
}

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Format numbers with commas
function formatNumber(number) {
    return new Intl.NumberFormat('en-US').format(number);
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (typeof showToast === 'function') {
            showToast('Copied to clipboard', 'success');
        }
    }).catch(err => {
        console.error('Failed to copy: ', err);
        if (typeof showToast === 'function') {
            showToast('Failed to copy to clipboard', 'error');
        }
    });
}

// Get URL parameters
function getUrlParams() {
    const params = {};
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    for (const [key, value] of urlParams) {
        params[key] = value;
    }

    return params;
}

// Set URL parameter
function setUrlParam(key, value) {
    const url = new URL(window.location);
    url.searchParams.set(key, value);
    window.history.pushState({}, '', url);
}

// Remove URL parameter
function removeUrlParam(key) {
    const url = new URL(window.location);
    url.searchParams.delete(key);
    window.history.pushState({}, '', url);
}



// Show loading state
function showLoading(element) {
    element.classList.add('loading');
    element.disabled = true;
}

// Hide loading state
function hideLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;
}

function handleApiError(error) {
    console.error('API Error:', error);
    if (typeof showToast === 'function') {
        showToast('An error occurred. Please try again.', 'error');
    }
}

// Generate random ID
function generateId() {
    return Math.random().toString(36).substr(2, 9);
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Load recent comments on publisher's events
function loadRecentComments() {
    const container = document.getElementById('recentCommentsContainer');
    if (!container) {
        console.log('Recent comments container not found, skipping comments load');
        return;
    }

    // Show loading state
    container.innerHTML = `
        <div class="loading-comments">
            <div class="spinner"></div>
            <p>Loading recent comments...</p>
        </div>
    `;

    fetch('/unipulse/public/publisher/dashboard/getRecentComments')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayRecentComments(data.comments, data.stats);
            } else {
                console.error('Comments API error:', data.error);
                container.innerHTML = `
                    <div class="error-message">
                        <p>Failed to load comments: ${data.error}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            container.innerHTML = `
                <div class="error-message">
                    <p>Unable to load comments at this time.</p>
                </div>
            `;
        });
}

// Display recent comments
function displayRecentComments(comments, stats) {
    const container = document.getElementById('recentCommentsContainer');
    const totalCommentsEl = document.getElementById('totalComments');
    const averageRatingEl = document.getElementById('averageRating');

    // Update stats
    if (totalCommentsEl) totalCommentsEl.textContent = stats.total_comments;
    if (averageRatingEl) averageRatingEl.textContent = stats.average_rating.toFixed(1);

    if (comments.length === 0) {
        container.innerHTML = `
            <div class="no-comments">
                <div class="no-comments-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3>No Comments Yet</h3>
                <p>Comments from users, publishers, admins, and moderators on your events will appear here.</p>
            </div>
        `;
        return;
    }

    const commentsHtml = comments.map(comment => {
        const userTypeIcon = getUserTypeIcon(comment.user_type);
        const ratingStars = comment.rating > 0 ? generateStarRating(comment.rating) : '';
        
        return `
            <div class="comment-item" data-user-type="${comment.user_type}">
                <div class="comment-header">
                    <div class="comment-user">
                        <div class="user-avatar">
                            ${userTypeIcon}
                        </div>
                        <div class="user-info">
                            <span class="user-name">${comment.user_name}</span>
                            <span class="user-type">${formatUserType(comment.user_type)}</span>
                        </div>
                    </div>
                    <div class="comment-meta">
                        <span class="comment-date">${comment.formatted_date}</span>
                        ${ratingStars}
                    </div>
                </div>
                <div class="comment-content">
                    <div class="comment-event">
                        <span class="event-label">Event:</span>
                        <span class="event-title">${comment.event_title}</span>
                    </div>
                    <p class="comment-text">${comment.comment_text}</p>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = `<div class="comments-list">${commentsHtml}</div>`;
}

// Get user type icon
function getUserTypeIcon(userType) {
    const icons = {
        'university': '<i class="fas fa-graduation-cap"></i>',
        'public': '<i class="fas fa-user"></i>',
        'publisher': '<i class="fas fa-users"></i>',
        'sponsor': '<i class="fas fa-handshake"></i>',
        'admin': '<i class="fas fa-user-shield"></i>',
        'moderator': '<i class="fas fa-user-check"></i>'
    };
    return icons[userType] || '<i class="fas fa-user"></i>';
}

// Format user type for display
function formatUserType(userType) {
    const types = {
        'university': 'University Student',
        'public': 'Public User',
        'publisher': 'Event Publisher',
        'sponsor': 'Sponsor',
        'admin': 'Administrator',
        'moderator': 'Moderator'
    };
    return types[userType] || 'User';
}

// Generate star rating display
function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;
    let starsHtml = '';
    
    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        starsHtml += '<i class="fas fa-star-half-alt"></i>';
    }
    
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        starsHtml += '<i class="far fa-star"></i>';
    }
    
    return `<div class="rating-stars">${starsHtml}</div>`;
}

// Export functions for use in other modules
window.EventOrganizerDashboard = {
    exportReport,
    showDeleteModal,
    viewEvent,
    editEvent,
    changeVolunteerStatus,
    updateSponsorship,
    copyToClipboard,
    formatCurrency,
    formatNumber
};