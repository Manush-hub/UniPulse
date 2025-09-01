// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeDashboard();
    loadOrganizerData();
    loadEventsManagement();
    loadVolunteerData();
    loadRecentActivity();
    loadNotifications();
    setupEventListeners();
    animateProgressBars();
});

// Sample data for event organizer
const organizerData = {
    username: 'Jennifer King',
    displayName: 'Jennifer',
    role: 'Event Organizer',
    university: 'University of Moratuwa',
    upcomingEvents: 7,
    ticketsSold: 1280,
    totalVolunteers: 45,
    confirmedSponsors: 12,
    totalRevenue: 25600,
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

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

const notifications = [
    {
        id: 1,
        title: 'Volunteer Application',
        message: 'New volunteer application for Tech Summit',
        time: '10 min ago',
        read: false
    },
    {
        id: 2,
        title: 'Ticket Purchase',
        message: 'Early bird tickets sold out for Arts Festival',
        time: '45 min ago',
        read: false
    },
    {
        id: 3,
        title: 'Sponsorship Update',
        message: 'Microsoft confirmed platinum sponsorship',
        time: '1 hour ago',
        read: true
    },
    {
        id: 4,
        title: 'Event Reminder',
        message: 'Spring Arts Festival starts in 2 days',
        time: '3 hours ago',
        read: true
    }
];

// Dashboard initialization
function initializeDashboard() {
    console.log('Dashboard initialized');
    updateUserProfile();
    setupDropdowns();
    setupModals();
}

// Load organizer data
function loadOrganizerData() {
    const welcomeSection = document.querySelector('.welcome-section');
    if (welcomeSection) {
        const welcomeText = welcomeSection.querySelector('.welcome-text h1');
        if (welcomeText) {
            welcomeText.textContent = `Welcome back, ${organizerData.displayName}!`;
        }

        const stats = welcomeSection.querySelectorAll('.stat-number');
        if (stats.length >= 4) {
            stats[0].textContent = organizerData.upcomingEvents;
            stats[1].textContent = organizerData.ticketsSold.toLocaleString();
            stats[2].textContent = organizerData.totalVolunteers;
            stats[3].textContent = organizerData.confirmedSponsors;
        }
    }
}

// Load events management
function loadEventsManagement() {
    const eventsList = document.querySelector('.events-list');
    if (!eventsList) return;

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

// Load notifications
function loadNotifications() {
    const notificationList = document.querySelector('.notification-list');
    if (!notificationList) return;

    notificationList.innerHTML = '';

    const unreadCount = notifications.filter(n => !n.read).length;
    const notificationBadge = document.querySelector('.notification-badge');
    if (notificationBadge) {
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
    }

    notifications.forEach(notification => {
        const notificationItem = document.createElement('div');
        notificationItem.className = `notification-item ${notification.read ? '' : 'unread'}`;
        notificationItem.innerHTML = `
            <div class="notification-content">
                <h4>${notification.title}</h4>
                <p>${notification.message}</p>
                <span class="notification-time">${notification.time}</span>
            </div>
        `;
        notificationList.appendChild(notificationItem);
    });
}

// Setup event listeners
function setupEventListeners() {
    // Notification dropdown toggle
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });
    }

    // User dropdown toggle
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }

        if (userDropdown && !userDropdown.contains(e.target) && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });

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

// Setup dropdowns
function setupDropdowns() {
    // User dropdown
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function () {
            userDropdown.classList.toggle('show');
        });
    }

    // Notification dropdown
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function () {
            notificationDropdown.classList.toggle('show');
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (userDropdown && !userDropdown.contains(e.target) && userMenu && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('show');
        }

        if (notificationDropdown && !notificationDropdown.contains(e.target) && notificationBtn && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
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

// Delete event function
function deleteEvent(eventId) {
    console.log('Deleting event with ID:', eventId);
    // In a real application, this would make an API call to delete the event

    // Show success message
    showToast('Event deleted successfully', 'success');

    // Remove from UI
    const eventItem = document.querySelector(`.event-item[data-id="${eventId}"]`);
    if (eventItem) {
        eventItem.remove();
    }

    // Reload events list
    loadEventsManagement();
}

// Edit event function
function editEvent(eventId) {
    console.log('Editing event with ID:', eventId);
    // In a real application, this would redirect to the edit page
    showToast('Redirecting to edit event page', 'info');
    // window.location.href = `/unipulse/organizer/edit-event.html?id=${eventId}`;
}

// Update user profile in the header
function updateUserProfile() {
    const usernameElement = document.querySelector('.username');
    const userRoleElement = document.querySelector('.user-role');
    const avatarElement = document.querySelector('.avatar');

    if (usernameElement) {
        usernameElement.textContent = organizerData.username;
    }

    if (userRoleElement) {
        userRoleElement.textContent = organizerData.role;
    }

    if (avatarElement && organizerData.avatar) {
        avatarElement.src = organizerData.avatar;
        avatarElement.alt = organizerData.username;
    }
}

// Animate progress bars
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill, .donation-fill');

    progressBars.forEach(bar => {
        const targetWidth = bar.getAttribute('data-width') || '75%';
        bar.style.width = targetWidth;
    });
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.bottom = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '10000';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;

    toastContainer.appendChild(toast);

    // Trigger reflow
    void toast.offsetWidth;

    // Show toast
    toast.classList.add('show');

    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toastContainer.removeChild(toast);
        }, 300);
    }, 3000);
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

// Logout function
function logout() {
    console.log('Logging out...');
    // In a real application, this would clear authentication tokens and redirect
    showToast('Logging out...', 'info');

    // Simulate logout process
    setTimeout(() => {
        window.location.href = '/unipulse/index.html';
    }, 1000);
}

// Export data for reports
function exportReport(type) {
    console.log(`Exporting ${type} report...`);
    showToast(`Preparing ${type} report for download`, 'info');

    // In a real application, this would generate and download a report
    setTimeout(() => {
        showToast(`${type} report downloaded successfully`, 'success');
    }, 2000);
}

// Handle volunteer status change
function changeVolunteerStatus(volunteerId, newStatus) {
    console.log(`Changing volunteer ${volunteerId} status to ${newStatus}`);
    showToast(`Volunteer status updated to ${newStatus}`, 'success');
}

// Handle sponsorship status change
function updateSponsorship(sponsorId, action) {
    console.log(`${action} sponsorship for sponsor ${sponsorId}`);
    showToast(`Sponsorship ${action} successfully`, 'success');
}

// Mark all notifications as read
function markAllAsRead() {
    notifications.forEach(notification => {
        notification.read = true;
    });

    loadNotifications();
    showToast('All notifications marked as read', 'success');
}

// Toggle mobile menu
function toggleMobileMenu() {
    const nav = document.querySelector('.nav');
    if (nav) {
        nav.classList.toggle('show');
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

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard', 'success');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showToast('Failed to copy to clipboard', 'error');
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

// Check if user is logged in (placeholder)
function isLoggedIn() {
    // This would check authentication status
    return true;
}

// Redirect if not logged in
function requireAuth() {
    if (!isLoggedIn()) {
        window.location.href = '/unipulse/login.html';
        return false;
    }
    return true;
}

// Get user permissions
function getUserPermissions() {
    // This would return the current user's permissions
    return ['read_events', 'write_events', 'manage_volunteers', 'view_reports'];
}

// Check if user has permission
function hasPermission(permission) {
    const permissions = getUserPermissions();
    return permissions.includes(permission);
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

// Handle API errors
function handleApiError(error) {
    console.error('API Error:', error);
    showToast('An error occurred. Please try again.', 'error');
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

// Toggle dark mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDarkMode = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);

    showToast(isDarkMode ? 'Dark mode enabled' : 'Dark mode disabled', 'info');
}

// Initialize dark mode from localStorage
function initDarkMode() {
    const darkMode = localStorage.getItem('darkMode') === 'true';
    if (darkMode) {
        document.body.classList.add('dark-mode');
    }
}

// Add these to the initialization
document.addEventListener('DOMContentLoaded', function () {
    initDarkMode();
});

// Export functions for use in other modules
window.EventOrganizerDashboard = {
    logout,
    exportReport,
    showDeleteModal,
    editEvent,
    changeVolunteerStatus,
    updateSponsorship,
    markAllAsRead,
    toggleMobileMenu,
    copyToClipboard,
    toggleDarkMode,
    formatCurrency,
    formatNumber
};