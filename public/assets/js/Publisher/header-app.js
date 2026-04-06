document.addEventListener('DOMContentLoaded', function () {
    loadNotifications();
    setupEventListeners();
    if (typeof initializeRegisteredEventsCalendar === 'function') {
        initializeRegisteredEventsCalendar({
            apiEndpoint: '/unipulse/public/publisher/dashboard/getUpcomingEvents',
            eventDetailsBasePath: '/unipulse/public/publisher/eventview?id=',
            fallbackEventsPath: '/unipulse/public/publisher/events',
            modalTitle: 'My Registered Events Calendar',
            emptyMessage: 'No future registered events found.'
        });
    }
    startNotificationPolling();
});

let notifications = [];
let unreadNotificationsCount = 0;

async function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    if (!notificationList || !notificationBadge) return;

    try {
        const response = await fetch(`/unipulse/public/publisher/dashboard/getNotifications?t=${Date.now()}`, {
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Failed to fetch notifications');
        }

        const data = await response.json();
        notifications = data.success && Array.isArray(data.notifications) ? data.notifications : [];
        unreadNotificationsCount = data.success && Number.isInteger(data.unread_count)
            ? data.unread_count
            : notifications.filter(n => !n.read).length;
    } catch (error) {
        console.error('Error loading notifications:', error);
        notifications = [];
        unreadNotificationsCount = 0;
    }

    notificationList.innerHTML = '';

    notificationBadge.textContent = unreadNotificationsCount;
    notificationBadge.classList.toggle('hidden', unreadNotificationsCount <= 0);

    if (notifications.length === 0) {
        notificationList.innerHTML = '<div class="notification-item"><div class="notification-content"><p>No notifications</p></div></div>';
        return;
    }

    notifications.forEach(notification => {
        const notificationItem = createNotificationItem(notification);
        notificationList.appendChild(notificationItem);
    });
}

// Create notification item
function createNotificationItem(notification) {
    const item = document.createElement('div');
    item.className = `notification-item ${notification.read ? '' : 'unread'}`;
    item.onclick = () => {
        if (!notification.read) {
            notification.read = true;
            item.classList.remove('unread');
            updateBadgeCountFromCurrentList();
            markNotificationAsRead(notification);
        }

        const redirectUrl = typeof notification.redirect_url === 'string'
            ? notification.redirect_url.trim()
            : '';

        if (redirectUrl) {
            window.location.href = redirectUrl;
            return;
        }

        const category = String(notification.notification_category || '').toLowerCase();
        const title = String(notification.title || '').toLowerCase();
        if (category === 'donation_submitted' || title.includes('donation submitted')) {
            window.location.href = '/unipulse/public/publisher/donations';
            return;
        }

        const eventId = Number(notification.id || 0);
        if (eventId > 0) {
            window.location.href = `/unipulse/public/publisher/eventview?id=${eventId}`;
        } else {
            window.location.href = '/unipulse/public/publisher/events';
        }
    };

    item.innerHTML = `
        <div class="notification-content">
            <h4>${notification.title}</h4>
            <p>${notification.message}</p>
            <div class="notification-time">${notification.time || 'Just now'}</div>
        </div>
    `;

    return item;
}

function markNotificationAsRead(notification) {
    fetch('/unipulse/public/publisher/dashboard/markNotificationRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            event_id: Number(notification.id || 0),
            notification_id: Number(notification.notification_id || 0),
            created_at: notification.created_at || '',
            notification_key: notification.notification_key || ''
        }),
        keepalive: true
    }).catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function updateBadgeCountFromCurrentList() {
    const notificationBadge = document.getElementById('notificationBadge');
    if (!notificationBadge) {
        return;
    }

    unreadNotificationsCount = notifications.filter(n => !n.read).length;
    notificationBadge.textContent = unreadNotificationsCount;
    notificationBadge.classList.toggle('hidden', unreadNotificationsCount <= 0);
}

// Setup event listeners
function setupEventListeners() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userDropdown = document.getElementById('userDropdown');

        if (!e.target.closest('.notifications') && notificationDropdown) {
            notificationDropdown.classList.remove('show');
        }
        if (!e.target.closest('.user-menu') && userDropdown) {
            userDropdown.classList.remove('show');
        }
    });
}

// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    if (dropdown) {
        if (userDropdown) {
            userDropdown.classList.remove('show');
        }
        dropdown.classList.toggle('show');
    }
}

// Toggle user menu dropdown
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        if (notificationDropdown) {
            notificationDropdown.classList.remove('show');
        }
        dropdown.classList.toggle('show');
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    const previousNotifications = [...notifications];

    notifications = notifications.map(notification => ({
        ...notification,
        read: true
    }));
    unreadNotificationsCount = 0;
    const notificationBadge = document.getElementById('notificationBadge');
    if (notificationBadge) {
        notificationBadge.textContent = '0';
        notificationBadge.classList.add('hidden');
    }

    try {
        const response = await fetch('/unipulse/public/publisher/dashboard/markAllNotificationsRead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to mark notifications as read');
        }

        showToast('All notifications marked as read', 'success');
        loadNotifications();
    } catch (error) {
        console.error('Error marking notifications as read:', error);
        notifications = previousNotifications;
        updateBadgeCountFromCurrentList();
        loadNotifications();
        showToast('Failed to mark notifications as read', 'error');
    }
}

function startNotificationPolling() {
    setInterval(() => {
        loadNotifications();
    }, 60000);
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    // Add styles if not already added
    if (!document.querySelector('#toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            .toast {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 24px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s ease;
            }
            .toast.show {
                opacity: 1;
                transform: translateX(0);
            }
            .toast-success { background-color: #10b981; }
            .toast-error { background-color: #ef4444; }
            .toast-warning { background-color: #f59e0b; }
            .toast-info { background-color: #3b82f6; }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(toast);

    // Trigger reflow
    void toast.offsetWidth;

    // Show toast
    toast.classList.add('show');

    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Logout function
function logout() {
    console.log('Logging out...');
    showToast('Logging out...', 'info');

    // Simulate logout process
    setTimeout(() => {
        window.location.href = '/unipulse/public/logout';
    }, 1000);
}
