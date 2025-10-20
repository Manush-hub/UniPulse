// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard DOMContentLoaded - Starting initialization...');
    
    // Add a small delay to ensure DOM is fully ready
    setTimeout(() => {
        console.log('Running delayed initialization...');
        try {
            initializeDashboard();
            loadModeratorData();
            loadPendingReviews();
            loadRecentActivity();
            loadUserReports();
            setupEventListeners();
            console.log('Dashboard initialization complete');
        } catch (error) {
            console.error('Error during dashboard initialization:', error);
        }
    }, 100);
});

// Sample data for moderator dashboard
const moderatorData = {
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



// Dashboard initialization
function initializeDashboard() {
    setupModals();
}

// Load moderator data
function loadModeratorData() {
    console.log('Loading moderator data...');
    // Update welcome section
    // const welcomeUsername = document.getElementById('welcomeUsername');
    // if (welcomeUsername) {
    //     welcomeUsername.textContent = moderatorData.displayName;
    // }
    
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
    
    console.log('Moderator data loaded successfully');
}

// Load pending reviews
function loadPendingReviews() {
    console.log('Loading pending reviews...');
    const reviewsList = document.getElementById('reviewsList');
    if (!reviewsList) {
        console.log('Reviews list element not found - this is expected on dashboard page');
        return;
    }
    
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
    
    console.log('Pending reviews loaded successfully');
}

// Load recent activity
function loadRecentActivity() {
    try {
        console.log('Loading recent activity...');
        const activityList = document.getElementById('activityList');
        if (!activityList) {
            console.error('Activity list element not found!');
            return;
        }
        
        console.log('Activity list element found, populating with', recentActivity.length, 'activities');
        activityList.innerHTML = '';
        
        // Show first 2 activities initially
        for (let i = 0; i < recentActivity.length; i++) {
            const activity = recentActivity[i];
            const activityRow = document.createElement('tr');
            
            // Hide rows after the first 2
            if (i >= 2) {
                activityRow.classList.add('hidden-row');
                activityRow.style.display = 'none';
            }
            
            // Determine type and status
            let typeClass = 'type-' + activity.type;
            let statusClass = 'status-completed';
            let statusText = 'Completed';
            
            if (activity.type === 'report') {
                statusClass = 'status-resolved';
                statusText = 'Resolved';
            }
            
            activityRow.innerHTML = `
                <td class="activity-title">
                    <i class="fas fa-${activity.icon}"></i>
                    ${activity.title}
                </td>
                <td><span class="activity-type ${typeClass}">${activity.type.charAt(0).toUpperCase() + activity.type.slice(1)}</span></td>
                <td>${activity.description}</td>
                <td>${activity.time}</td>
                <td><span class="activity-status ${statusClass}">${statusText}</span></td>
            `;
            activityList.appendChild(activityRow);
        }
        
        console.log('Recent activity loaded successfully. Added', recentActivity.length, 'activity rows');
        
    } catch (error) {
        console.error('Error loading recent activity:', error);
    }
}

// Load user reports
function loadUserReports() {
    try {
        console.log('Loading user reports...');
        const reportsTableBody = document.getElementById('reportsTableBody');
        if (!reportsTableBody) {
            console.error('Reports table body element not found!');
            return;
        }
        
        console.log('Reports table body found, populating with', userReports.length, 'reports');
        reportsTableBody.innerHTML = '';
        
        // Show first 2 reports initially
        for (let i = 0; i < userReports.length; i++) {
            const report = userReports[i];
            const reportRow = document.createElement('tr');
            
            // Hide rows after the first 2
            if (i >= 2) {
                reportRow.classList.add('hidden-row');
                reportRow.style.display = 'none';
            }
            
            // Determine type and status classes
            let typeClass = 'type-' + report.type;
            let statusClass = 'status-' + report.status;
            let statusText = report.status.charAt(0).toUpperCase() + report.status.slice(1);
            
            if (report.status === 'in-progress') {
                statusText = 'In Progress';
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
        }
        
        console.log('User reports loaded successfully. Added', userReports.length, 'report rows');
        
    } catch (error) {
        console.error('Error loading user reports:', error);
    }
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


// Export functions for use in other modules
window.ModeratorDashboard = {
    approveEvent,
    rejectEvent,
    requestChanges,
    viewReport,
    resolveReport,
    deleteReport,
};