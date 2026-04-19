document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    loadNotifications();
    setupEventListeners();
    updateAdminMsgBadge();
    setInterval(updateAdminMsgBadge, 30000);

    if (window.adminContactMessageSentFlash) {
        showToast(String(window.adminContactMessageSentFlash), 'success');
    }
});

function updateAdminMsgBadge() {
    Promise.all([
        fetch('/unipulse/public/admin/messages/unreadCount').then(r => r.json()).catch(() => ({ success: false, count: 0 })),
        fetch('/unipulse/public/admin/dashboard/getNotifications').then(r => r.json()).catch(() => ({ success: false, notifications: [] }))
    ])
        .then(([chatData, notifData]) => {
            const badge = document.getElementById('adminMsgBadge');
            if (!badge) return;

            const chatUnread = (chatData && chatData.success) ? (parseInt(chatData.count, 10) || 0) : 0;
            const supportUnread = (notifData && notifData.success && Array.isArray(notifData.notifications))
                ? notifData.notifications.filter(n => n && n.type === 'support_message' && n.unread).length
                : 0;

            const totalUnread = chatUnread + supportUnread;

            if (totalUnread > 0) {
                badge.textContent = totalUnread;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {});
}

// Load user data from backend
function loadUserData() {
    fetch('/unipulse/public/admin/dashboard/getUserProfile')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch user data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const usernameElement = document.getElementById('username');
                if (usernameElement && data.username) {
                    usernameElement.textContent = data.username;
                }
            }
        })
        .catch(error => {
            console.error('Error loading user data:', error);
        });
}

// Load notifications from backend
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    
    if (!notificationList || !notificationBadge) return;
    
    fetch('/unipulse/public/admin/dashboard/getNotifications')
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
    
    if (!notificationList || !notificationBadge) return;
    
    notificationList.innerHTML = '';
    
    const unreadCount = notifications.filter(n => n.unread).length;
    if (unreadCount > 0) {
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = 'flex';
        notificationBadge.classList.remove('hidden');
    } else {
        notificationBadge.textContent = '';
        notificationBadge.style.display = 'none';
        notificationBadge.classList.add('hidden');
    }
    
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
    const isUnread = !!notification.unread;
    const isSupportNotification = (notification.type || '') === 'support_message';
    item.className = `notification-item ${isUnread ? 'unread' : ''} ${isUnread && isSupportNotification ? 'support-unread' : ''}`;
    item.onclick = () => handleNotificationClick(notification, item);
    
    item.innerHTML = `
        <div class="notification-content">
            <h4>${notification.title || 'Notification'}</h4>
            <p>${notification.message}</p>
            <div class="notification-time">${notification.time}</div>
        </div>
    `;
    
    return item;
}

function handleNotificationClick(notification, item = null) {
    if (!notification) return;

    const redirectLink = notification.link || null;
    const notificationId = notification.id || '';
    const notificationKey = notification.notification_key || '';
    const wasUnread = !!notification.unread;

    // Update UI immediately for better feedback.
    if (wasUnread && item) {
        notification.unread = false;
        item.classList.remove('unread');
        item.classList.remove('support-unread');
        updateUnreadBadgeFromDom();
    }

    // Persist read state in background; keepalive helps when redirecting quickly.
    markNotificationAsRead(notificationId, notificationKey);

    if (redirectLink) {
        window.location.href = redirectLink;
    }
}

function updateUnreadBadgeFromDom() {
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');

    if (!notificationBadge || !notificationList) return;

    const unreadCount = notificationList.querySelectorAll('.notification-item.unread').length;
    if (unreadCount > 0) {
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = 'flex';
        notificationBadge.classList.remove('hidden');
    } else {
        notificationBadge.textContent = '';
        notificationBadge.style.display = 'none';
        notificationBadge.classList.add('hidden');
    }
}

// Setup event listeners
function setupEventListeners() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
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
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Toggle user menu dropdown
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Mark notification as read
function markNotificationAsRead(notificationId, notificationKey = '') {
    return fetch('/unipulse/public/admin/dashboard/markNotificationRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            notificationId: notificationId,
            notification_key: notificationKey
        }),
        keepalive: true
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        loadNotifications();
    });
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('/unipulse/public/admin/dashboard/markAllNotificationsRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();

            // Hide badge immediately; keep notifications visible as read.
            const notificationBadge = document.getElementById('notificationBadge');
            if (notificationBadge) {
                notificationBadge.textContent = '';
                notificationBadge.style.display = 'none';
                notificationBadge.classList.add('hidden');
            }

            showToast('All notifications marked as read', 'success');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        showToast('Failed to mark notifications as read', 'error');
    });
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
