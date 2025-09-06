// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadAdminData();
    loadRecentActivity();
    loadPendingApprovals();
    loadUserTable();
    loadNotifications();
    setupEventListeners();
    animateProgressBars();
});

// Sample data for admin dashboard
const adminData = {
    username: 'Robert Johnson',
    displayName: 'Robert',
    role: 'System Administrator',
    totalUsers: 2847,
    activeEvents: 124,
    pendingApprovals: 18,
    systemHealth: 98,
    newUsersThisWeek: 127,
    userActiveRate: 94,
    eventsThisWeek: 42,
    attendanceRate: 78,
    systemUptime: 98,
    avgResponseTime: '1.2s',
    errorRate: '0.2%'
};

const recentActivity = [
    {
        id: 1,
        type: 'user',
        title: 'New user registration',
        description: 'Sarah Connor registered as Event Organizer',
        time: '10 minutes ago',
        icon: 'user-plus'
    },
    {
        id: 2,
        type: 'event',
        title: 'Event published',
        description: 'Tech Workshop 2025 published by UCSC IEEE',
        time: '45 minutes ago',
        icon: 'calendar'
    },
    {
        id: 3,
        type: 'moderation',
        title: 'Content flagged for review',
        description: 'Event "Summer Festival" flagged by user',
        time: '1 hour ago',
        icon: 'flag'
    },
    {
        id: 4,
        type: 'system',
        title: 'System backup completed',
        description: 'Nightly database backup successful',
        time: '2 hours ago',
        icon: 'database'
    },
    {
        id: 5,
        type: 'security',
        title: 'Security update installed',
        description: 'Applied latest security patches',
        time: '5 hours ago',
        icon: 'shield'
    }
];

const pendingApprovals = [
    {
        id: 1,
        name: 'UCSC Coding Club',
        type: 'Organization Verification',
        submitted: '2 hours ago'
    },
    {
        id: 2,
        name: 'Tech Innovation Summit',
        type: 'Event Approval',
        submitted: '5 hours ago'
    },
    {
        id: 3,
        name: 'John Smith',
        type: 'Moderator Application',
        submitted: '1 day ago'
    },
    {
        id: 4,
        name: 'Annual Music Festival',
        type: 'Event Approval',
        submitted: '1 day ago'
    }
];

const userData = [
    {
        id: 1,
        name: 'Sarah Connor',
        email: 'sarah.connor@example.com',
        role: 'Event Organizer',
        registrationDate: '2025-03-15',
        status: 'active',
        avatar: 'SC'
    },
    {
        id: 2,
        name: 'Mike Johnson',
        email: 'mike.johnson@example.com',
        role: 'Student',
        registrationDate: '2025-03-14',
        status: 'active',
        avatar: 'MJ'
    },
    {
        id: 3,
        name: 'Lisa Chen',
        email: 'lisa.chen@example.com',
        role: 'Sponsor',
        registrationDate: '2025-03-13',
        status: 'pending',
        avatar: 'LC'
    },
    {
        id: 4,
        name: 'David Wilson',
        email: 'david.wilson@example.com',
        role: 'Moderator',
        registrationDate: '2025-03-12',
        status: 'active',
        avatar: 'DW'
    },
    {
        id: 5,
        name: 'Emma Thompson',
        email: 'emma.thompson@example.com',
        role: 'Event Organizer',
        registrationDate: '2025-03-11',
        status: 'inactive',
        avatar: 'ET'
    }
];

const notifications = [
    {
        id: 1,
        title: 'System Update',
        message: 'New system update available for installation',
        time: '30 min ago',
        read: false
    },
    {
        id: 2,
        title: 'New Registration',
        message: '5 new user registrations in the last hour',
        time: '1 hour ago',
        read: false
    },
    {
        id: 3,
        title: 'High Traffic',
        message: 'Unusual traffic spike detected',
        time: '2 hours ago',
        read: true
    },
    {
        id: 4,
        title: 'Backup Completed',
        message: 'Nightly backup completed successfully',
        time: '5 hours ago',
        read: true
    },
    {
        id: 5,
        title: 'Security Alert',
        message: 'Multiple failed login attempts detected',
        time: 'Yesterday',
        read: false
    }
];

// Dashboard initialization
function initializeDashboard() {
    console.log('Admin Dashboard initialized');
    updateAdminProfile();
    setupDropdowns();
    setupModals();
}

// Load admin data
function loadAdminData() {
    // Update welcome section
    const welcomeUsername = document.getElementById('welcomeUsername');
    if (welcomeUsername) {
        welcomeUsername.textContent = adminData.displayName;
    }
    
    // Update quick stats
    const statElements = {
        totalUsers: document.getElementById('totalUsers'),
        activeEvents: document.getElementById('activeEvents'),
        pendingApprovals: document.getElementById('pendingApprovals'),
        systemHealth: document.getElementById('systemHealth')
    };
    
    if (statElements.totalUsers) statElements.totalUsers.textContent = adminData.totalUsers.toLocaleString();
    if (statElements.activeEvents) statElements.activeEvents.textContent = adminData.activeEvents;
    if (statElements.pendingApprovals) statElements.pendingApprovals.textContent = adminData.pendingApprovals;
    if (statElements.systemHealth) statElements.systemHealth.textContent = `${adminData.systemHealth}%`;
    
    // Update system overview cards
    const userStats = document.querySelectorAll('.overview-card:nth-child(1) .stat-value');
    if (userStats.length >= 3) {
        userStats[0].textContent = adminData.totalUsers.toLocaleString();
        userStats[1].textContent = adminData.newUsersThisWeek;
        userStats[2].textContent = `${adminData.userActiveRate}%`;
    }
    
    const eventStats = document.querySelectorAll('.overview-card:nth-child(2) .stat-value');
    if (eventStats.length >= 3) {
        eventStats[0].textContent = adminData.activeEvents;
        eventStats[1].textContent = adminData.eventsThisWeek;
        eventStats[2].textContent = `${adminData.attendanceRate}%`;
    }
    
    const performanceStats = document.querySelectorAll('.overview-card:nth-child(3) .stat-value');
    if (performanceStats.length >= 3) {
        performanceStats[0].textContent = `${adminData.systemUptime}%`;
        performanceStats[1].textContent = adminData.avgResponseTime;
        performanceStats[2].textContent = adminData.errorRate;
    }
}

// Load recent activity
function loadRecentActivity() {
    const activityList = document.getElementById('activityList');
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

// Load pending approvals
function loadPendingApprovals() {
    const approvalList = document.getElementById('approvalList');
    if (!approvalList) return;
    
    approvalList.innerHTML = '';
    
    pendingApprovals.forEach(approval => {
        const approvalItem = document.createElement('div');
        approvalItem.className = 'approval-item';
        approvalItem.innerHTML = `
            <div class="approval-info">
                <div class="approval-name">${approval.name}</div>
                <div class="approval-type">${approval.type}</div>
            </div>
            <div class="approval-actions">
                <button class="approval-btn approve" onclick="approveRequest(${approval.id})">Approve</button>
                <button class="approval-btn reject" onclick="rejectRequest(${approval.id})">Reject</button>
            </div>
        `;
        approvalList.appendChild(approvalItem);
    });
}

// Load user table
function loadUserTable() {
    const userTableBody = document.getElementById('userTableBody');
    if (!userTableBody) return;
    
    userTableBody.innerHTML = '';
    
    userData.forEach(user => {
        const userRow = document.createElement('tr');
        
        // Determine status class
        let statusClass = '';
        let statusText = '';
        
        switch(user.status) {
            case 'active':
                statusClass = 'status-active';
                statusText = 'Active';
                break;
            case 'pending':
                statusClass = 'status-pending';
                statusText = 'Pending';
                break;
            case 'inactive':
                statusClass = 'status-inactive';
                statusText = 'Inactive';
                break;
        }
        
        userRow.innerHTML = `
            <td>
                <div class="user-info-cell">
                    <div class="user-avatar">${user.avatar}</div>
                    <div>
                        <div class="user-name">${user.name}</div>
                        <div class="user-email">${user.email}</div>
                    </div>
                </div>
            </td>
            <td>${user.role}</td>
            <td>${formatDate(user.registrationDate)}</td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td>
                <div class="table-actions">
                    <button class="action-btn edit" onclick="editUser(${user.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="showDeleteUserModal(${user.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn view" onclick="viewUser(${user.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        
        userTableBody.appendChild(userRow);
    });
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
    
    // Quick action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            handleQuickAction(action);
        });
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

// Setup modals
function setupModals() {
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('show');
        }
    });
}

// Update admin profile in the header
function updateAdminProfile() {
    const usernameElement = document.getElementById('username');
    const userRoleElement = document.getElementById('userRole');
    
    if (usernameElement) {
        usernameElement.textContent = adminData.username;
    }
    
    if (userRoleElement) {
        userRoleElement.textContent = adminData.role;
    }
}

// Animate progress bars
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.metric-fill');
    
    progressBars.forEach(bar => {
        const targetWidth = bar.getAttribute('data-width') || '0%';
        // Animate the progress bar
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 500);
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

// Handle quick actions
function handleQuickAction(action) {
    switch(action) {
        case 'user-management':
            window.location.href = 'user-management.html';
            break;
        case 'content-moderation':
            window.location.href = 'content-moderation.html';
            break;
        case 'approval-queue':
            window.location.href = 'approval-queue.html';
            break;
        case 'system-health':
            window.location.href = 'system-health.html';
            break;
        default:
            console.log('Action not implemented:', action);
    }
}

// Approve request
function approveRequest(requestId) {
    console.log('Approving request:', requestId);
    showToast('Request approved successfully', 'success');
    
    // Remove from UI
    const requestItem = document.querySelector(`.approval-item:nth-child(${requestId})`);
    if (requestItem) {
        requestItem.remove();
    }
    
    // Update pending approvals count
    adminData.pendingApprovals--;
    document.getElementById('pendingApprovals').textContent = adminData.pendingApprovals;
}

// Reject request
function rejectRequest(requestId) {
    console.log('Rejecting request:', requestId);
    showToast('Request rejected', 'info');
    
    // Remove from UI
    const requestItem = document.querySelector(`.approval-item:nth-child(${requestId})`);
    if (requestItem) {
        requestItem.remove();
    }
    
    // Update pending approvals count
    adminData.pendingApprovals--;
    document.getElementById('pendingApprovals').textContent = adminData.pendingApprovals;
}

// Edit user
function editUser(userId) {
    console.log('Editing user:', userId);
    const user = userData.find(u => u.id === userId);
    if (user) {
        showUserModal(user, 'Edit User');
    }
}

// View user
function viewUser(userId) {
    console.log('Viewing user:', userId);
    const user = userData.find(u => u.id === userId);
    if (user) {
        showUserModal(user, 'User Details');
    }
}

// Show user modal
function showUserModal(user, title) {
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (modal && modalTitle && modalBody) {
        modalTitle.textContent = title;
        
        // Determine status text
        let statusText = '';
        switch(user.status) {
            case 'active':
                statusText = 'Active';
                break;
            case 'pending':
                statusText = 'Pending';
                break;
            case 'inactive':
                statusText = 'Inactive';
                break;
        }
        
        modalBody.innerHTML = `
            <div class="user-modal-content">
                <div class="user-modal-avatar">${user.avatar}</div>
                <div class="user-modal-info">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">${user.name}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">${user.email}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Role:</span>
                        <span class="info-value">${user.role}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Registration Date:</span>
                        <span class="info-value">${formatDate(user.registrationDate)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">${statusText}</span>
                    </div>
                </div>
            </div>
            ${title === 'Edit User' ? `
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="saveUserChanges(${user.id})">Save Changes</button>
                <button class="btn btn-outline" onclick="closeModal('userModal')">Cancel</button>
            </div>
            ` : ''}
        `;
        
        modal.classList.add('show');
    }
}

// Save user changes
function saveUserChanges(userId) {
    console.log('Saving changes for user:', userId);
    showToast('User details updated successfully', 'success');
    closeModal('userModal');
}

// Show delete user modal
function showDeleteUserModal(userId) {
    const user = userData.find(u => u.id === userId);
    if (user) {
        if (confirm(`Are you sure you want to delete user "${user.name}"? This action cannot be undone.`)) {
            deleteUser(userId);
        }
    }
}

// Delete user
function deleteUser(userId) {
    console.log('Deleting user:', userId);
    showToast('User deleted successfully', 'success');
    
    // Remove from UI
    const userRow = document.querySelector(`#userTableBody tr:nth-child(${userId})`);
    if (userRow) {
        userRow.remove();
    }
}

// Close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
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

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
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

// Export functions for use in other modules
window.AdminDashboard = {
    logout,
    approveRequest,
    rejectRequest,
    editUser,
    viewUser,
    deleteUser,
    markAllAsRead,
    toggleNotifications,
    toggleUserMenu
};