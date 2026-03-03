document.addEventListener('DOMContentLoaded', function () {
    loadUserData();
    loadNotifications();
    setupEventListeners();
    startNotificationPolling();
});

let userData = {
    username: 'User',
    name: 'User',
    university: 'University of Colombo',
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

let notifications = [];
let unreadNotificationsCount = 0;

async function loadUserData() {
    try {
        const response = await fetch('/unipulse/public/user/dashboard/getUserData');
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.user) {
                userData = data.user;
                const usernameElement = document.getElementById('username');
                if (usernameElement) {
                    usernameElement.textContent = data.user.name || 'User';
                }
            }
        }
    } catch (error) {
        console.error('Error loading user data:', error);
    }
}

async function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    if (!notificationList || !notificationBadge) {
        return;
    }

    try {
        const response = await fetch(`/unipulse/public/user/dashboard/getNotifications?t=${Date.now()}`, {
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

function createNotificationItem(notification) {
    const item = document.createElement('div');
    item.className = `notification-item ${notification.read ? '' : 'unread'}`;
    item.onclick = () => {
        if (!notification.read) {
            notification.read = true;
            updateBadgeCount();
            item.classList.remove('unread');
            markNotificationAsRead(notification);
        }
        const eventId = Number(notification.id || 0);
        if (eventId > 0) {
            window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
        } else {
            window.location.href = '/unipulse/public/user/events';
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

function updateBadgeCount() {
    const notificationBadge = document.getElementById('notificationBadge');
    if (!notificationBadge) {
        return;
    }

    const unreadCount = notifications.filter(n => !n.read).length;
    unreadNotificationsCount = unreadCount;
    notificationBadge.textContent = unreadCount;
    notificationBadge.classList.toggle('hidden', unreadCount <= 0);
}

function setupEventListeners() {
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

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');

    if (!dropdown) {
        return;
    }

    if (userDropdown) {
        userDropdown.classList.remove('show');
    }

    dropdown.classList.toggle('show');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');

    if (!dropdown) {
        return;
    }

    if (notificationDropdown) {
        notificationDropdown.classList.remove('show');
    }

    dropdown.classList.toggle('show');
}

async function markAllAsRead() {
    const previousNotifications = [...notifications];

    notifications = notifications.map(notification => ({
        ...notification,
        read: true
    }));

    updateBadgeCount();

    try {
        const response = await fetch('/unipulse/public/user/dashboard/markAllNotificationsRead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to mark notifications as read');
        }
        loadNotifications();
    } catch (error) {
        console.error('Error marking notifications as read:', error);
        notifications = previousNotifications;
        updateBadgeCount();
        loadNotifications();
    }
}

function startNotificationPolling() {
    setInterval(() => {
        loadNotifications();
    }, 60000);
}

function markNotificationAsRead(notification) {
    fetch('/unipulse/public/user/dashboard/markNotificationRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            event_id: Number(notification.id || 0),
            created_at: notification.created_at || '',
            source: notification.source || 'activity'
        }),
        keepalive: true
    }).catch(error => {
        console.error('Error marking notification as read:', error);
    });
}