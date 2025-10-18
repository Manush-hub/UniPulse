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

// Logout function (placeholder)
function logout() {
    // This would handle the logout process
    if (confirm('Are you sure you want to logout?')) {
        // Clear session data
        localStorage.removeItem('userToken');
        localStorage.removeItem('userData');
        
        // Redirect to login page
        window.location.href = '/unipulse/signin';
    }
}

// Initialize header functionality
function initializeHeader() {
    updateUserProfile();
    setupDropdowns();
    initDarkMode();
}

// Initialize header on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeHeader();
});

// Export functions for use in other modules
window.HeaderModule = {
    updateUserProfile,
    loadNotifications,
    loadOrganizerData,
    setupDropdowns,
    markAllAsRead,
    toggleMobileMenu,
    toggleDarkMode,
    initDarkMode,
    logout,
    isLoggedIn,
    requireAuth,
    getUserPermissions,
    hasPermission,
    showToast,
    initializeHeader
};