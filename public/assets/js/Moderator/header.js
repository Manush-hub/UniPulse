document.addEventListener('DOMContentLoaded', function() {
    updateModeratorProfile();
    loadNotifications();
    setupDropdowns();
    setupEventListeners();
});

const moderatorData = {
    username: 'Moderator',
    displayName: 'Moderator',
    role: 'Moderator',
}

const notifications = [
    {
        id: 1,
        title: 'New Event Submission',
        message: '3 new events waiting for review',
        time: '30 min ago',
        read: false
    },
    {
        id: 2,
        title: 'User Report',
        message: 'New user report submitted',
        time: '1 hour ago',
        read: false
    },
    {
        id: 3,
        title: 'Guidelines Updated',
        message: 'Moderation guidelines have been updated',
        time: '2 hours ago',
        read: true
    },
    {
        id: 4,
        title: 'Weekly Summary',
        message: 'Your weekly moderation summary is ready',
        time: 'Yesterday',
        read: true
    }
];

// Update moderator profile in the header
function updateModeratorProfile() {
    const usernameElement = document.getElementById('username');
    const userRoleElement = document.getElementById('userRole');
    
    if (usernameElement) {
        usernameElement.textContent = moderatorData.username;
    }
    
    if (userRoleElement) {
        userRoleElement.textContent = moderatorData.role;
    }
}


// Load notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    notificationList.innerHTML = '';
    
    const unreadCount = notifications.filter(n => !n.read).length;
    const notificationBadge = document.getElementById('notificationBadge');
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
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });
    }
    
    // User dropdown toggle
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
        
        if (userDropdown && !userDropdown.contains(e.target) && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });
}



// Setup dropdowns
function setupDropdowns() {
    // User dropdown
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function() {
            userDropdown.classList.toggle('show');
        });
    }
    
    // Notification dropdown
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function() {
            notificationDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (userDropdown && !userDropdown.contains(e.target) && userMenu && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
        
        if (notificationDropdown && !notificationDropdown.contains(e.target) && notificationBtn && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
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

// Mark all notifications as read
function markAllAsRead() {
    notifications.forEach(notification => {
        notification.read = true;
    });
    
    loadNotifications();
    showToast('All notifications marked as read', 'success');
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Add styles if not already added
    if (!document.querySelector('#toast-styles')) {
        const styles = document.createElement('style');
        styles.id = 'toast-styles';
        styles.textContent = `
            .toast {
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 8px;
                color: white;
                z-index: 3000;
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.3s, transform 0.3s;
            }
            .toast.show {
                opacity: 1;
                transform: translateY(0);
            }
            .toast-success {
                background: #10b981;
            }
            .toast-error {
                background: #ef4444;
            }
            .toast-info {
                background: #3b82f6;
            }
            .toast-warning {
                background: #f59e0b;
            }
        `;
        document.head.appendChild(styles);
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
        window.location.href = '/unipulse/index.html';
    }, 1000);
}

