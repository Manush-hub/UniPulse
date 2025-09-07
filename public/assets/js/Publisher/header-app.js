document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    loadNotifications();
    setupEventListeners();
});

const userData = {
    username: 'Event Publisher',
    role: 'Event Organizer',
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

const notifications = [
    {
        id: 1,
        title: 'Event Published',
        message: 'Your event "Tech Conference 2025" has been published successfully',
        time: '2 hours ago',
        unread: true,
        type: 'success'
    },
    {
        id: 2,
        title: 'Registration Update',
        message: '25 new registrations for AI Workshop',
        time: '4 hours ago',
        unread: true,
        type: 'info'
    },
    {
        id: 3,
        title: 'Event Approval',
        message: 'Cultural Night event is pending approval',
        time: '1 day ago',
        unread: false,
        type: 'warning'
    }
];

function loadUserData() {
    const usernameElement = document.getElementById('username');
    if (usernameElement) {
        usernameElement.textContent = userData.username;
    }
}

// Load notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    
    if (!notificationList || !notificationBadge) return;
    
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
