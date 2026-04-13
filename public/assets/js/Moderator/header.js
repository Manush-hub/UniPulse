document.addEventListener('DOMContentLoaded', function() {
    // Username is already loaded from server-side PHP
    // No need to fetch from API
    loadNotifications();
    setupEventListeners();
});


// Load notifications from backend
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    fetch('/unipulse/public/moderator/dashboard/getNotifications')
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
                const notificationBadge = document.getElementById('notificationBadge');
                if (notificationBadge) {
                    notificationBadge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.log('Notifications endpoint not available:', error.message);
            // Don't show error to user, just hide notification badge silently
            notificationList.innerHTML = '<div class="no-data">No notifications</div>';
            const notificationBadge = document.getElementById('notificationBadge');
            if (notificationBadge) {
                notificationBadge.style.display = 'none';
            }
        });
}

// Display notifications
function displayNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    notificationList.innerHTML = '';
    
    const unreadCount = notifications.filter(n => !n.read).length;
    const notificationBadge = document.getElementById('notificationBadge');
    if (notificationBadge) {
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
    
    if (notifications.length === 0) {
        notificationList.innerHTML = '<div class="no-data">No notifications</div>';
        return;
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

// Setup all event listeners (consolidated)
function setupEventListeners() {
    // Get all necessary elements
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownBtn = document.querySelector('.user-dropdown-btn');
    
    // Notification dropdown toggle
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close user dropdown if open
            if (userDropdown) {
                userDropdown.classList.remove('show');
            }
            notificationDropdown.classList.toggle('show');
        });
    }
    
    // User dropdown toggle - use the dropdown button specifically
    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close notification dropdown if open
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
            userDropdown.classList.toggle('show');
        });
    }
    
    // Alternative: clicking on the user menu area
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close notification dropdown if open
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
            userDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationDropdown && !notificationDropdown.contains(e.target) && 
            notificationBtn && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
        
        if (userDropdown && !userDropdown.contains(e.target) && 
            userMenu && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });
    
    // Mark all as read button
    const markAllReadBtn = document.querySelector('.notification-header button');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            markAllAsRead();
        });
    }
}

// Toggle notifications dropdown (for backward compatibility with inline handlers)
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    if (dropdown) {
        // Close user dropdown if open
        if (userDropdown) {
            userDropdown.classList.remove('show');
        }
        dropdown.classList.toggle('show');
    }
}

// Toggle user menu dropdown (for backward compatibility with inline handlers)
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        // Close notification dropdown if open
        if (notificationDropdown) {
            notificationDropdown.classList.remove('show');
        }
        dropdown.classList.toggle('show');
    }
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('/unipulse/public/moderator/dashboard/markAllNotificationsRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
            showToast('All notifications marked as read', 'success');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        showToast('Failed to mark notifications as read', 'error');
    });
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

