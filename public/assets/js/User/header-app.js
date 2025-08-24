document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    loadNotifications();
    setupEventListeners();
});

const userData = {
    username: 'Manush-hub',
    // displayName: 'Manush',
    university: 'University of Colombo',
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

const notifications = [
    {
        id: 1,
        title: 'New Event Available',
        message: 'Tech Conference 2025 registration is now open',
        time: '1 hour ago',
        unread: true,
        type: 'event'
    },
    {
        id: 2,
        title: 'Event Reminder',
        message: 'AI Workshop starts tomorrow at 2:00 PM',
        time: '6 hours ago',
        unread: true,
        type: 'reminder'
    },
    {
        id: 3,
        title: 'Event Update',
        message: 'Cultural Night venue has been changed to Arts Theatre',
        time: '1 day ago',
        unread: false,
        type: 'update'
    }
];

function loadUserData() {
    document.getElementById('username').textContent = userData.username;
    // document.getElementById('welcomeUsername').textContent = userData.displayName;
}

// Load notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    
    notificationList.innerHTML = '';
    
    const unreadCount = notifications.filter(n => n.unread).length;
    notificationBadge.textContent = unreadCount;
    notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
    
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
    document.addEventListener('click', function(e) {
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
    const notification = notifications.find(n => n.id === notificationId);
    if (notification) {
        notification.unread = false;
        loadNotifications();
    }
}

// Mark all notifications as read
function markAllAsRead() {
    notifications.forEach(n => n.unread = false);
    loadNotifications();
}