// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadModeratorData();
    loadPendingReviews();
    loadRecentActivity();
    loadUserReports();
    loadNotifications();
    setupEventListeners();
});

// Sample data for moderator dashboard
const moderatorData = {
    username: 'Lisa Chen',
    displayName: 'Lisa',
    role: 'Moderator',
    pendingReviews: 12,
    eventsReviewed: 84,
    reportsHandled: 23,
    approvalRate: 92,
    approvedEvents: 64,
    rejectedEvents: 8,
    editedEvents: 12,
    verifiedOrganizers: 15
};

const pendingReviews = [
    {
        id: 1,
        title: 'Tech Workshop 2025',
        organizer: 'UCSC IEEE Student Branch',
        submitted: '2 hours ago',
        category: 'Technology'
    },
    {
        id: 2,
        title: 'Annual Cultural Festival',
        organizer: 'Cultural Society',
        submitted: '5 hours ago',
        category: 'Cultural'
    },
    {
        id: 3,
        title: 'AI Research Symposium',
        organizer: 'Computer Science Department',
        submitted: '1 day ago',
        category: 'Academic'
    },
    {
        id: 4,
        title: 'Startup Pitch Competition',
        organizer: 'Entrepreneurship Club',
        submitted: '1 day ago',
        category: 'Business'
    }
];

const recentActivity = [
    {
        id: 1,
        type: 'approval',
        title: 'Event Approved',
        description: 'Tech Conference 2025 approved',
        time: '10 minutes ago',
        icon: 'check-circle'
    },
    {
        id: 2,
        type: 'rejection',
        title: 'Event Rejected',
        description: 'Inappropriate content in "Summer Party"',
        time: '45 minutes ago',
        icon: 'times-circle'
    },
    {
        id: 3,
        type: 'edit',
        title: 'Event Edited',
        description: 'Fixed date in "Career Fair" event',
        time: '1 hour ago',
        icon: 'edit'
    },
    {
        id: 4,
        type: 'verification',
        title: 'Organizer Verified',
        description: 'Verified credentials for Music Society',
        time: '2 hours ago',
        icon: 'user-check'
    },
    {
        id: 5,
        type: 'report',
        title: 'Report Handled',
        description: 'Resolved user report on event comments',
        time: '5 hours ago',
        icon: 'flag'
    }
];

const userReports = [
    {
        id: 1,
        content: 'Tech Workshop 2025',
        type: 'inappropriate',
        submitted: '2 hours ago',
        status: 'pending'
    },
    {
        id: 2,
        content: 'User comment on Cultural Festival',
        type: 'spam',
        submitted: '5 hours ago',
        status: 'in-progress'
    },
    {
        id: 3,
        content: 'AI Symposium description',
        type: 'misinformation',
        submitted: '1 day ago',
        status: 'resolved'
    },
    {
        id: 4,
        content: 'Startup Competition registration',
        type: 'inappropriate',
        submitted: '1 day ago',
        status: 'pending'
    }
];

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

// Dashboard initialization
function initializeDashboard() {
    console.log('Moderator Dashboard initialized');
    updateModeratorProfile();
    setupDropdowns();
    setupModals();
}

// Load moderator data
function loadModeratorData() {
    // Update welcome section
    const welcomeUsername = document.getElementById('welcomeUsername');
    if (welcomeUsername) {
        welcomeUsername.textContent = moderatorData.displayName;
    }
    
    // Update quick stats
    const statElements = {
        pendingReviews: document.getElementById('pendingReviews'),
        eventsReviewed: document.getElementById('eventsReviewed'),
        reportsHandled: document.getElementById('reportsHandled'),
        approvalRate: document.getElementById('approvalRate')
    };
    
    if (statElements.pendingReviews) statElements.pendingReviews.textContent = moderatorData.pendingReviews;
    if (statElements.eventsReviewed) statElements.eventsReviewed.textContent = moderatorData.eventsReviewed;
    if (statElements.reportsHandled) statElements.reportsHandled.textContent = moderatorData.reportsHandled;
    if (statElements.approvalRate) statElements.approvalRate.textContent = `${moderatorData.approvalRate}%`;
    
    // Update moderation stats
    const statCards = document.querySelectorAll('.stat-card .stat-number');
    if (statCards.length >= 4) {
        statCards[0].textContent = moderatorData.approvedEvents;
        statCards[1].textContent = moderatorData.rejectedEvents;
        statCards[2].textContent = moderatorData.editedEvents;
        statCards[3].textContent = moderatorData.verifiedOrganizers;
    }
}

// Load pending reviews
function loadPendingReviews() {
    const reviewsList = document.getElementById('reviewsList');
    if (!reviewsList) return;
    
    reviewsList.innerHTML = '';
    
    pendingReviews.forEach(review => {
        const reviewItem = document.createElement('div');
        reviewItem.className = 'review-item';
        reviewItem.innerHTML = `
            <div class="review-info">
                <h3 class="review-title">${review.title}</h3>
                <div class="review-meta">
                    <span class="review-organizer">
                        <i class="fas fa-user"></i>
                        ${review.organizer}
                    </span>
                    <span class="review-time">
                        <i class="fas fa-clock"></i>
                        ${review.submitted}
                    </span>
                    <span class="review-category">
                        <i class="fas fa-tag"></i>
                        ${review.category}
                    </span>
                </div>
            </div>
            <div class="review-actions">
                <button class="review-btn view" onclick="viewReview(${review.id})">
                    <i class="fas fa-eye"></i>
                    View
                </button>
                <button class="review-btn approve" onclick="approveEvent(${review.id})">
                    <i class="fas fa-check"></i>
                    Approve
                </button>
                <button class="review-btn reject" onclick="rejectEvent(${review.id})">
                    <i class="fas fa-times"></i>
                    Reject
                </button>
            </div>
        `;
        reviewsList.appendChild(reviewItem);
    });
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

// Load user reports
function loadUserReports() {
    const reportsTableBody = document.getElementById('reportsTableBody');
    if (!reportsTableBody) return;
    
    reportsTableBody.innerHTML = '';
    
    userReports.forEach(report => {
        const reportRow = document.createElement('tr');
        
        // Determine type and status classes
        let typeClass = '';
        let statusClass = '';
        let statusText = '';
        
        switch(report.type) {
            case 'inappropriate':
                typeClass = 'type-inappropriate';
                break;
            case 'spam':
                typeClass = 'type-spam';
                break;
            case 'misinformation':
                typeClass = 'type-misinformation';
                break;
        }
        
        switch(report.status) {
            case 'pending':
                statusClass = 'status-pending';
                statusText = 'Pending';
                break;
            case 'in-progress':
                statusClass = 'status-in-progress';
                statusText = 'In Progress';
                break;
            case 'resolved':
                statusClass = 'status-resolved';
                statusText = 'Resolved';
                break;
        }
        
        reportRow.innerHTML = `
            <td class="report-content">${report.content}</td>
            <td><span class="report-type ${typeClass}">${report.type}</span></td>
            <td>${report.submitted}</td>
            <td><span class="report-status ${statusClass}">${statusText}</span></td>
            <td>
                <div class="table-actions">
                    <button class="action-btn view" onclick="viewReport(${report.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn resolve" onclick="resolveReport(${report.id})">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteReport(${report.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        reportsTableBody.appendChild(reportRow);
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
        case 'content-moderation':
            window.location.href = 'content-moderation.html';
            break;
        case 'reports':
            window.location.href = 'reports.html';
            break;
        case 'organizer-verification':
            window.location.href = 'organizer-verification.html';
            break;
        case 'comments-moderation':
            window.location.href = 'comments-moderation.html';
            break;
        default:
            console.log('Action not implemented:', action);
    }
}

// View review
function viewReview(reviewId) {
    console.log('Viewing review:', reviewId);
    const review = pendingReviews.find(r => r.id === reviewId);
    if (review) {
        showReviewModal(review);
    }
}

// Show review modal
function showReviewModal(review) {
    const modal = document.getElementById('reviewModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (modal && modalTitle && modalBody) {
        modalTitle.textContent = 'Event Review: ' + review.title;
        
        modalBody.innerHTML = `
            <div class="review-modal-content">
                <div class="review-details">
                    <div class="detail-row">
                        <span class="detail-label">Organizer:</span>
                        <span class="detail-value">${review.organizer}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Submitted:</span>
                        <span class="detail-value">${review.submitted}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Category:</span>
                        <span class="detail-value">${review.category}</span>
                    </div>
                </div>
                <div class="event-preview">
                    <h4>Event Preview</h4>
                    <div class="preview-content">
                        <p>This is a preview of the event content. The moderator can review all details here before making a decision.</p>
                        <p>Event description, images, and other details would be displayed in this section.</p>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="approveEvent(${review.id})">
                        <i class="fas fa-check"></i>
                        Approve Event
                    </button>
                    <button class="btn btn-outline" onclick="rejectEvent(${review.id})">
                        <i class="fas fa-times"></i>
                        Reject Event
                    </button>
                    <button class="btn" onclick="requestChanges(${review.id})">
                        <i class="fas fa-edit"></i>
                        Request Changes
                    </button>
                </div>
            </div>
        `;
        
        modal.classList.add('show');
    }
}

// Approve event
function approveEvent(eventId) {
    console.log('Approving event:', eventId);
    showToast('Event approved successfully', 'success');
    closeModal('reviewModal');
    
    // Remove from UI
    const reviewItem = document.querySelector(`.review-item:nth-child(${eventId})`);
    if (reviewItem) {
        reviewItem.remove();
    }
    
    // Update pending reviews count
    moderatorData.pendingReviews--;
    document.getElementById('pendingReviews').textContent = moderatorData.pendingReviews;
}

// Reject event
function rejectEvent(eventId) {
    console.log('Rejecting event:', eventId);
    showToast('Event rejected', 'info');
    closeModal('reviewModal');
    
    // Remove from UI
    const reviewItem = document.querySelector(`.review-item:nth-child(${eventId})`);
    if (reviewItem) {
        reviewItem.remove();
    }
    
    // Update pending reviews count
    moderatorData.pendingReviews--;
    document.getElementById('pendingReviews').textContent = moderatorData.pendingReviews;
}

// Request changes for event
function requestChanges(eventId) {
    console.log('Requesting changes for event:', eventId);
    showToast('Changes requested from organizer', 'info');
    closeModal('reviewModal');
}

// View report
function viewReport(reportId) {
    console.log('Viewing report:', reportId);
    const report = userReports.find(r => r.id === reportId);
    if (report) {
        alert(`Viewing report: ${report.content}\nType: ${report.type}\nStatus: ${report.status}`);
    }
}

// Resolve report
function resolveReport(reportId) {
    console.log('Resolving report:', reportId);
    showToast('Report marked as resolved', 'success');
    
    // Update UI
    const statusElement = document.querySelector(`#reportsTableBody tr:nth-child(${reportId}) .report-status`);
    if (statusElement) {
        statusElement.textContent = 'Resolved';
        statusElement.className = 'report-status status-resolved';
    }
}

// Delete report
function deleteReport(reportId) {
    console.log('Deleting report:', reportId);
    if (confirm('Are you sure you want to delete this report?')) {
        showToast('Report deleted', 'info');
        
        // Remove from UI
        const reportRow = document.querySelector(`#reportsTableBody tr:nth-child(${reportId})`);
        if (reportRow) {
            reportRow.remove();
        }
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
window.ModeratorDashboard = {
    logout,
    approveEvent,
    rejectEvent,
    requestChanges,
    viewReport,
    resolveReport,
    deleteReport,
    markAllAsRead,
    toggleNotifications,
    toggleUserMenu
};