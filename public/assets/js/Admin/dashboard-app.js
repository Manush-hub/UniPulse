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
function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeHtmlAttribute(value) {
    return escapeHtml(value).replace(/`/g, '&#96;');
}

// Toggle Activity Log - show/hide additional activity items
function toggleActivityLog() {
    const activityList = document.getElementById('activityList');
    const hiddenItems = activityList.querySelectorAll('.activity-item.hidden-item');
    const btn = document.getElementById('activityLogBtn');
    const icon = btn.querySelector('.expand-icon');
    const btnText = btn.querySelector('.btn-text');
    
    if (hiddenItems.length > 0) {
        hiddenItems.forEach(item => {
            if (item.style.display === 'none') {
                item.style.display = 'flex';
                icon.style.transform = 'rotate(180deg)';
                btnText.textContent = 'Show Less';
            } else {
                item.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                btnText.textContent = 'View Full Log';
            }
        });
    }
}

// Toggle Pending Approvals - show/hide additional approval items
function togglePendingApprovals() {
    const approvalList = document.getElementById('approvalList');
    const hiddenItems = approvalList.querySelectorAll('.approval-item.hidden-item');
    const btn = document.getElementById('pendingApprovalsBtn');
    const icon = btn.querySelector('.expand-icon');
    const btnText = btn.querySelector('.btn-text');
    
    if (hiddenItems.length > 0) {
        hiddenItems.forEach(item => {
            if (item.style.display === 'none') {
                item.style.display = 'flex';
                icon.style.transform = 'rotate(180deg)';
                btnText.textContent = 'Show Less';
            } else {
                item.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                btnText.textContent = 'View All Pending';
            }
        });
    }
}

// Toggle User Management - open modal to show all users
function toggleUserManagement() {
    // Open the modal
    const modal = document.getElementById('allUsersModal');
    modal.style.display = 'flex';
    
    // Show loading message
    document.getElementById('allUsersLoadingMessage').style.display = 'block';
    document.getElementById('allUsersContent').style.display = 'none';
    
    // Fetch all users
    fetch('/unipulse/public/Admin/Dashboard/getAllUsers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store all users data globally for filtering
                window.allUsersData = data.users;
                
                // Hide loading, show content
                document.getElementById('allUsersLoadingMessage').style.display = 'none';
                document.getElementById('allUsersContent').style.display = 'block';
                
                // Display users
                displayAllUsers(data.users);
            } else {
                alert('Failed to load users: ' + (data.error || 'Unknown error'));
                closeAllUsersModal();
            }
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            alert('An error occurred while loading users');
            closeAllUsersModal();
        });
}

// Display all users in the modal table
function displayAllUsers(users) {
    const tbody = document.getElementById('allUsersTableBody');
    const noUsersMessage = document.getElementById('noUsersMessage');
    
    if (!users || users.length === 0) {
        tbody.innerHTML = '';
        noUsersMessage.style.display = 'block';
        return;
    }
    
    noUsersMessage.style.display = 'none';
    
    tbody.innerHTML = users.map(user => `
        <tr data-name="${user.name.toLowerCase()}" data-email="${user.email.toLowerCase()}" data-type="${user.userType.toLowerCase()}" data-status="${user.status}">
            <td style="padding: 12px;">
                <div class="user-info">
                    <div>
                        <div class="user-name" style="font-weight: 500;">${user.name}</div>
                        <div class="user-email" style="font-size: 0.85rem; color: #666;">${user.email}</div>
                    </div>
                </div>
            </td>
            <td style="padding: 12px;">
                <span class="role-badge role-${user.userType.toLowerCase()}" style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                    ${user.userType}
                </span>
            </td>
            <td style="padding: 12px;">${user.createdAt}</td>
            <td style="padding: 12px;">
                <span class="status-badge ${user.statusClass}" style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                    ${user.status}
                </span>
            </td>
            <td style="padding: 12px; text-align: center;">
                <div class="action-buttons">
                    ${user.isSuspended ? 
                        `<button class="btn-icon btn-activate" title="Reactivate Account" onclick="reactivateAccount(${user.id}, '${user.userType.toLowerCase()}')">
                            <i class="fas fa-check-circle"></i>
                        </button>
                        ${user.hasPendingAppeal ? `<button
                            class="btn-icon"
                            title="Review Appeal"
                            onclick="openAppealModalFromButton(this)"
                            data-appeal-id="${user.pendingAppealId}"
                            data-user-id="${user.id}"
                            data-user-type="${escapeHtmlAttribute(user.userType.toLowerCase())}"
                            data-user-name="${escapeHtmlAttribute(user.name)}"
                            data-suspension-reason="${escapeHtmlAttribute(user.suspensionReason || 'No reason provided')}"
                            data-appeal-message="${escapeHtmlAttribute(user.pendingAppealMessage || '')}"
                            data-submitted-at="${escapeHtmlAttribute(user.pendingAppealSubmittedAt || '')}"
                        >
                            <i class="fas fa-envelope-open-text"></i>
                        </button>` : ''}` : 
                        user.status !== 'Rejected' ?
                        `<button class="btn-icon btn-suspend" title="Suspend Account" onclick="suspendAccount(${user.id}, '${user.userType.toLowerCase()}', '${user.name.replace(/'/g, "\\'")}')">
                            <i class="fas fa-ban"></i>
                        </button>` : ''
                    }
                </div>
            </td>
        </tr>
    `).join('');
}

// Filter users based on search and dropdown filters
function filterUsers() {
    if (!window.allUsersData) return;
    
    const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
    const typeFilter = document.getElementById('userTypeFilter').value.toLowerCase();
    const statusFilter = document.getElementById('userStatusFilter').value;
    
    const filteredUsers = window.allUsersData.filter(user => {
        const matchesSearch = searchTerm === '' || 
            user.name.toLowerCase().includes(searchTerm) || 
            user.email.toLowerCase().includes(searchTerm) ||
            user.userType.toLowerCase().includes(searchTerm);
        
        const matchesType = typeFilter === '' || user.userType.toLowerCase() === typeFilter;
        const matchesStatus = statusFilter === '' || user.status === statusFilter;
        
        return matchesSearch && matchesType && matchesStatus;
    });
    
    displayAllUsers(filteredUsers);
}

// Close all users modal
function closeAllUsersModal() {
    document.getElementById('allUsersModal').style.display = 'none';
    // Reset filters
    document.getElementById('userSearchInput').value = '';
    document.getElementById('userTypeFilter').value = '';
    document.getElementById('userStatusFilter').value = '';
}

// Suspension system
let pendingSuspension = { userId: null, userType: null };
let pendingAppealReview = { appealId: null, userId: null, userType: null };

function suspendAccount(userId, userType, userName) {
    pendingSuspension = { userId, userType };
    document.getElementById('suspendUserName').textContent = userName;
    document.getElementById('suspensionModal').style.display = 'flex';
}

function closeSuspensionModal() {
    document.getElementById('suspensionModal').style.display = 'none';
    document.getElementById('suspensionReason').value = '';
    pendingSuspension = { userId: null, userType: null };
}

function openAppealModalFromButton(button) {
    pendingAppealReview = {
        appealId: parseInt(button.dataset.appealId, 10),
        userId: parseInt(button.dataset.userId, 10),
        userType: button.dataset.userType || ''
    };

    document.getElementById('appealUserName').textContent = button.dataset.userName || '-';
    document.getElementById('appealUserType').textContent = (button.dataset.userType || '-').toUpperCase();
    document.getElementById('appealSuspensionReason').textContent = button.dataset.suspensionReason || 'No reason provided';
    document.getElementById('appealMessageBody').textContent = button.dataset.appealMessage || 'No appeal message';
    document.getElementById('appealSubmittedAt').textContent = button.dataset.submittedAt || '-';
    document.getElementById('appealAdminResponse').value = '';
    document.getElementById('appealModal').style.display = 'flex';
}

function closeAppealModal() {
    document.getElementById('appealModal').style.display = 'none';
    document.getElementById('appealAdminResponse').value = '';
    pendingAppealReview = { appealId: null, userId: null, userType: null };
}

function submitAppealDecision(decision) {
    if (!pendingAppealReview.appealId) {
        alert('No appeal selected');
        return;
    }

    const adminResponse = document.getElementById('appealAdminResponse').value.trim();
    if (decision === 'rejected' && !adminResponse) {
        alert('Please provide a response when rejecting an appeal');
        return;
    }

    fetch('/unipulse/public/admin/dashboard/reviewAppeal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            appeal_id: pendingAppealReview.appealId,
            decision: decision,
            admin_response: adminResponse
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Appeal reviewed successfully');
            closeAppealModal();

            if (decision === 'approved' && pendingAppealReview.userId && pendingAppealReview.userType) {
                updateDashboardRow(pendingAppealReview.userId, pendingAppealReview.userType, false);
            }

            refreshAllUsersModal();
        } else {
            alert('Error: ' + (data.message || 'Failed to review appeal'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while reviewing the appeal');
    });
}

// Check if All Users Modal is currently open
function isAllUsersModalOpen() {
    const modal = document.getElementById('allUsersModal');
    return modal && modal.style.display === 'flex';
}

// Refresh the All Users Modal data
function refreshAllUsersModal() {
    // Show loading message
    document.getElementById('allUsersLoadingMessage').style.display = 'block';
    document.getElementById('allUsersContent').style.display = 'none';
    
    // Fetch all users
    fetch('/unipulse/public/Admin/Dashboard/getAllUsers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store all users data globally for filtering
                window.allUsersData = data.users;
                
                // Hide loading, show content
                document.getElementById('allUsersLoadingMessage').style.display = 'none';
                document.getElementById('allUsersContent').style.display = 'block';
                
                // Display users with current filters applied
                filterUsers();
            }
        })
        .catch(error => {
            console.error('Error refreshing users:', error);
        });
}

// Update a row in the dashboard table in-place
function updateDashboardRow(userId, userType, isSuspended) {
    const rowId = `dashboard-user-${userId}-${userType}`;
    const row = document.getElementById(rowId);
    if (!row) return;
    
    // Update status badge
    const statusBadge = row.querySelector('.status-badge');
    if (statusBadge) {
        if (isSuspended) {
            statusBadge.textContent = 'Suspended';
            statusBadge.className = 'status-badge status-inactive';
        } else {
            statusBadge.textContent = 'Active';
            statusBadge.className = 'status-badge status-active';
        }
    }
    
    // Update action button
    const actionButtons = row.querySelector('.action-buttons');
    if (actionButtons) {
        if (isSuspended) {
            actionButtons.innerHTML = `
                <button class="btn-icon btn-activate" title="Reactivate Account" onclick="reactivateAccount(${userId}, '${userType}')">
                    <i class="fas fa-check-circle"></i>
                </button>`;
        } else {
            const userName = row.querySelector('.user-name')?.textContent || '';
            const statusBadge = row.querySelector('.status-badge');
            const rowStatus = statusBadge ? statusBadge.textContent.trim() : '';
            if (rowStatus !== 'Rejected') {
            actionButtons.innerHTML = `
                <button class="btn-icon btn-suspend" title="Suspend Account" onclick="suspendAccount(${userId}, '${userType}', '${userName.replace(/'/g, "\\'")}')">
                    <i class="fas fa-ban"></i>
                </button>`;
            } else {
                actionButtons.innerHTML = '';
            }
        }
    }
}

function confirmSuspension() {
    const reason = document.getElementById('suspensionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for suspension');
        return;
    }
    
    const modalIsOpen = isAllUsersModalOpen();
    
    fetch('/unipulse/public/admin/dashboard/suspendUser', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: pendingSuspension.userId,
            user_type: pendingSuspension.userType,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        closeSuspensionModal();
        
        if (data.success) {
            alert('Account suspended successfully');
            
            // Update dashboard table row in-place
            updateDashboardRow(pendingSuspension.userId, pendingSuspension.userType, true);
            
            // If All Users Modal is open, refresh it instead of reloading page
            if (modalIsOpen) {
                refreshAllUsersModal();
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to suspend account'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while suspending the account');
        closeSuspensionModal();
    });
}

function reactivateAccount(userId, userType) {
    if (!confirm('Are you sure you want to reactivate this account?')) {
        return;
    }
    
    const modalIsOpen = isAllUsersModalOpen();
    
    fetch('/unipulse/public/admin/dashboard/reactivateUser', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            user_type: userType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account reactivated successfully');
            
            // Update dashboard table row in-place
            updateDashboardRow(userId, userType, false);
            
            // If All Users Modal is open, refresh it instead of reloading page
            if (modalIsOpen) {
                refreshAllUsersModal();
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to reactivate account'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while reactivating the account');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const suspensionModal = document.getElementById('suspensionModal');
    const allUsersModal = document.getElementById('allUsersModal');
    const appealModal = document.getElementById('appealModal');
    
    if (event.target == suspensionModal) {
        closeSuspensionModal();
    }

    if (event.target == appealModal) {
        closeAppealModal();
    }
    
    if (event.target == allUsersModal) {
        closeAllUsersModal();
    }
}

// Publisher approval functions
function approvePublisher(publisherId) {
    if (!confirm('Are you sure you want to approve this publisher?')) {
        return;
    }
    
    fetch('/unipulse/public/Admin/Dashboard/approvePublisher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ publisher_id: publisherId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Publisher approved successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve publisher'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the publisher');
    });
}

function rejectPublisher(publisherId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (!reason || reason.trim() === '') {
        alert('Rejection reason is required');
        return;
    }
    
    fetch('/unipulse/public/Admin/Dashboard/rejectPublisher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            publisher_id: publisherId,
            rejection_reason: reason 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Publisher rejected successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reject publisher'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the publisher');
    });
}

