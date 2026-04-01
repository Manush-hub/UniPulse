// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadAdminData();
    loadRecentActivity();
    // loadPendingApprovals(); // Commented out - Pending approvals are now rendered server-side in PHP
    // loadUserTable(); // Commented out - User table is now rendered server-side in PHP
    setupEventListeners();
    animateProgressBars();
});

// Dashboard initialization
function initializeDashboard() {
    setupModals();
}

// Load admin data
function loadAdminData() {
    console.log('Loading admin data...');
    // Fetch real data from the server
    fetch('/unipulse/public/admin/dashboard/getStats')
        .then(response => {
            console.log('Stats response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Stats data received:', data);
            updateDashboardStats(data);
        })
        .catch(error => {
            console.error('Error loading admin data:', error);
            showToast('Failed to load dashboard statistics', 'error');
        });
}

// Update dashboard statistics
function updateDashboardStats(data) {
    // Update quick stats
    const statElements = {
        totalUsers: document.getElementById('totalUsers')
    };
    
    if (statElements.totalUsers) statElements.totalUsers.textContent = data.totalUsers.toLocaleString();

    // Update user statistics card (all user types)
    const overviewTotalUsers = document.getElementById('overviewTotalUsers');
    const overviewUniversityUsers = document.getElementById('overviewUniversityUsers');
    const overviewPublicUsers = document.getElementById('overviewPublicUsers');
    const overviewPublisherUsers = document.getElementById('overviewPublisherUsers');
    const overviewSponsorUsers = document.getElementById('overviewSponsorUsers');

    if (overviewTotalUsers) overviewTotalUsers.textContent = data.totalUsers.toLocaleString();
    if (overviewUniversityUsers) overviewUniversityUsers.textContent = (data.universityUsers || 0).toLocaleString();
    if (overviewPublicUsers) overviewPublicUsers.textContent = (data.publicUsers || 0).toLocaleString();
    if (overviewPublisherUsers) overviewPublisherUsers.textContent = (data.publisherUsers || 0).toLocaleString();
    if (overviewSponsorUsers) overviewSponsorUsers.textContent = (data.sponsorUsers || 0).toLocaleString();

    // Update event statistics card
    const overviewActiveEvents = document.getElementById('overviewActiveEvents');
    const overviewTotalEvents = document.getElementById('overviewTotalEvents');

    if (overviewActiveEvents) overviewActiveEvents.textContent = (data.activeEvents || 0).toLocaleString();
    if (overviewTotalEvents) overviewTotalEvents.textContent = (data.totalEvents || 0).toLocaleString();
}

// Load recent activity
function loadRecentActivity() {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    
    activityList.innerHTML = '<div class="loading">Loading activities...</div>';
    
    fetch('/unipulse/public/admin/dashboard/getRecentActivity')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(activities => {
            displayRecentActivity(activities);
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
            const activityList = document.getElementById('activityList');
            if (activityList) {
                activityList.innerHTML = '<div class="no-data">Failed to load activities</div>';
            }
        });
}

// Display recent activity
function displayRecentActivity(activities) {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    
    activityList.innerHTML = '';
    
    if (!activities || activities.length === 0) {
        activityList.innerHTML = '<div class="no-data">No recent activities</div>';
        return;
    }
    
    // Colour map per activity type
    const iconColorMap = {
        registration:  '#4a90e2',   // blue  – user signups
        admin_action:  '#e67e22',   // orange – admin management
        suspension:    '#e53e3e',   // red   – suspensions
        reactivation:  '#38a169',   // green – reactivations
        approval:      '#6966e0',   // purple – publisher approved
        rejection:     '#e53e3e',   // red   – publisher rejected
    };

    activities.forEach((activity, index) => {
        const activityItem = document.createElement('div');
        activityItem.className = 'activity-item';
        
        const iconColor = iconColorMap[activity.type] || '#6c757d';

        activityItem.innerHTML = `
            <div class="activity-icon" style="color:${iconColor};">
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
    
    approvalList.innerHTML = '<div class="loading">Loading approvals...</div>';
    
    fetch('/unipulse/public/admin/dashboard/getPendingApprovals')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(approvals => {
            displayPendingApprovals(approvals);
        })
        .catch(error => {
            console.error('Error loading pending approvals:', error);
            const approvalList = document.getElementById('approvalList');
            if (approvalList) {
                approvalList.innerHTML = '<div class="no-data">Failed to load approvals</div>';
            }
        });
}

// Display pending approvals
function displayPendingApprovals(approvals) {
    const approvalList = document.getElementById('approvalList');
    if (!approvalList) return;
    
    approvalList.innerHTML = '';
    
    if (approvals.length === 0) {
        approvalList.innerHTML = '<div class="no-data">No pending approvals</div>';
        return;
    }
    
    approvals.forEach((approval, index) => {
        const approvalItem = document.createElement('div');
        approvalItem.className = 'approval-item';
        
        // Hide items after the first 2
        if (index >= 2) {
            approvalItem.classList.add('hidden-item');
            approvalItem.style.display = 'none';
        }
        
        approvalItem.innerHTML = `
            <div class="approval-info">
                <div class="approval-name">${approval.name}</div>
                <div class="approval-type">${approval.type}</div>
                <div class="approval-time">${approval.submitted}</div>
            </div>
            <div class="approval-actions">
                <button class="approval-btn approve" onclick="approveRequest('${approval.id}')">Approve</button>
                <button class="approval-btn reject" onclick="rejectRequest('${approval.id}')">Reject</button>
            </div>
        `;
        approvalList.appendChild(approvalItem);
    });
}

// Load user table
function loadUserTable() {
    const userTableBody = document.getElementById('userTableBody');
    if (!userTableBody) return;
    
    userTableBody.innerHTML = '<tr><td colspan="5" class="loading">Loading users...</td></tr>';
    
    fetch('/unipulse/public/admin/dashboard/getRecentUsers')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(users => {
            displayUserTable(users);
        })
        .catch(error => {
            console.error('Error loading users:', error);
            const userTableBody = document.getElementById('userTableBody');
            if (userTableBody) {
                userTableBody.innerHTML = '<tr><td colspan="5" class="no-data">Failed to load users</td></tr>';
            }
        });
}

// Display user table
function displayUserTable(users) {
    const userTableBody = document.getElementById('userTableBody');
    if (!userTableBody) return;
    
    userTableBody.innerHTML = '';
    
    if (users.length === 0) {
        userTableBody.innerHTML = '<tr><td colspan="5" class="no-data">No users found</td></tr>';
        return;
    }
    
    users.forEach((user, index) => {
        const userRow = document.createElement('tr');
        
        // Hide rows after the first 2
        if (index >= 2) {
            userRow.classList.add('hidden-row');
            userRow.style.display = 'none';
        }
        
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
            default:
                statusClass = 'status-active';
                statusText = 'Active';
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

// Setup event listeners
function setupEventListeners() {
    // Quick action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            handleQuickAction(action);
        });
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
    
    // Find and remove the approval item
    const approvalItems = document.querySelectorAll('.approval-item');
    approvalItems.forEach((item) => {
        const buttons = item.querySelectorAll('.approval-btn');
        buttons.forEach(button => {
            if (button.getAttribute('onclick') && button.getAttribute('onclick').includes(requestId)) {
                item.remove();
            }
        });
    });
    
    // Refresh the pending approvals after a delay
    setTimeout(loadPendingApprovals, 1000);
}

// Reject request
function rejectRequest(requestId) {
    console.log('Rejecting request:', requestId);
    showToast('Request rejected', 'info');
    
    // Find and remove the approval item
    const approvalItems = document.querySelectorAll('.approval-item');
    approvalItems.forEach((item) => {
        const buttons = item.querySelectorAll('.approval-btn');
        buttons.forEach(button => {
            if (button.getAttribute('onclick') && button.getAttribute('onclick').includes(requestId)) {
                item.remove();
            }
        });
    });
    
    // Refresh the pending approvals after a delay
    setTimeout(loadPendingApprovals, 1000);
}

// Edit user
function editUser(userId) {
    console.log('Editing user:', userId);
    showToast('Edit functionality not implemented yet', 'info');
}

// View user
function viewUser(userId) {
    console.log('Viewing user:', userId);
    showToast('View functionality not implemented yet', 'info');
}

// Show delete user modal
function showDeleteUserModal(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        deleteUser(userId);
    }
}

// Delete user
function deleteUser(userId) {
    console.log('Deleting user:', userId);
    showToast('Delete functionality not implemented yet', 'info');
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
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
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
    approveRequest,
    rejectRequest,
    editUser,
    viewUser,
    deleteUser
};