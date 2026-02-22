document.addEventListener('DOMContentLoaded', function() {
    loadSponsorData();
    loadNotifications();
    setupEventListeners();
});

function formatDisplayName(name) {
    if (!name) return '';

    const trimmed = String(name).trim();
    if (!trimmed) return '';

    if (trimmed === trimmed.toLowerCase()) {
        return trimmed.replace(/\b\w/g, c => c.toUpperCase());
    }

    return trimmed;
}

// Load sponsor data from backend
function loadSponsorData() {
    fetch('/unipulse/public/sponsor/dashboard/getUserProfile')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch sponsor data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const usernameElement = document.getElementById('username');
                const userRoleElement = document.getElementById('userRole');
                const apiName = formatDisplayName(data.displayName || data.companyName || data.name || '');
                
                if (usernameElement) {
                    const currentName = formatDisplayName(usernameElement.textContent || '');
                    const isPlaceholder = !currentName || currentName === 'Sponsor' || currentName === 'TechCorp Ltd';

                    if (isPlaceholder && apiName) {
                        usernameElement.textContent = apiName;
                    } else if (apiName && currentName.toLowerCase() === apiName.toLowerCase() && currentName !== apiName) {
                        usernameElement.textContent = apiName;
                    }
                }
                
                if (userRoleElement) {
                    userRoleElement.textContent = 'Sponsor';
                }
            }
        })
        .catch(error => {
            console.error('Error loading sponsor data:', error);
        });
}


// Load notifications from backend
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    if (!notificationList || !notificationBadge) return;

    fetch('/unipulse/public/sponsor/dashboard/getNotifications')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch notifications');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.notifications) {
                displayNotifications(data.notifications);
            } else {
                notificationList.innerHTML = '<div class="no-data">No notifications</div>';
                notificationBadge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = '<div class="no-data">Failed to load notifications</div>';
            notificationBadge.style.display = 'none';
        });
}

// Display notifications
function displayNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    notificationList.innerHTML = '';

    const unreadCount = notifications.filter(n => n.unread).length;
    notificationBadge.textContent = unreadCount;
    notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';

    if (notifications.length === 0) {
        notificationList.innerHTML = '<div class="no-data">No notifications</div>';
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
    item.className = `notification-item ${notification.unread ? 'unread' : ''}`;
    item.onclick = () => markNotificationAsRead(notification.id);

    item.innerHTML = `
        <div class="notification-content">
            <h4>${notification.title}</h4>
            <p>${notification.message}</p>
            <div class="notification-time">${notification.time}</div>
        </div>
    `;

    return item;
}

// Setup event listeners
function setupEventListeners() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.notifications')) {
            document.getElementById('notificationDropdown').classList.remove('show');
        }
        if (!e.target.closest('.user-menu')) {
            document.getElementById('userDropdown').classList.remove('show');
        }
    });
}

// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
}

// Toggle user menu dropdown
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Mark notification as read
function markNotificationAsRead(notificationId) {
    fetch('/unipulse/public/sponsor/dashboard/markNotificationRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ notificationId: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('/unipulse/public/sponsor/dashboard/markAllNotificationsRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}