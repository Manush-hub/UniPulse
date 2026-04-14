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
            loadPublisherPerformanceReport();
            setupDashboardListeners();
            console.log('Dashboard initialization complete');
        } catch (error) {
            console.error('Error during dashboard initialization:', error);
        }
    }, 100);
});

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
    if (statElements.approvalRate) statElements.approvalRate.textContent = moderatorData.approvalRate;
    
    // Update moderation stats
    const statCards = document.querySelectorAll('.stat-card .stat-number');
    if (statCards.length >= 4) {
        statCards[0].textContent = moderatorData.hiddenEvents;
        statCards[1].textContent = moderatorData.approvedPublishers;
        statCards[2].textContent = moderatorData.rejectedPublishers;
        statCards[3].textContent = moderatorData.totalActions;
    }
    
    console.log('Moderator data loaded successfully');
}

// Load pending reviews from backend
function loadPendingReviews() {
    console.log('Loading pending reviews...');
    const reviewsList = document.getElementById('reviewsList');
    if (!reviewsList) {
        console.log('Reviews list element not found - this is expected on dashboard page');
        return;
    }
    
    reviewsList.innerHTML = '<div class="loading">Loading reviews...</div>';
    
    fetch('/unipulse/public/moderator/dashboard/getPendingReviews')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch pending reviews');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.reviews) {
                displayPendingReviews(data.reviews);
            } else {
                reviewsList.innerHTML = '<div class="no-data">No pending reviews</div>';
            }
        })
        .catch(error => {
            console.error('Error loading pending reviews:', error);
            reviewsList.innerHTML = '<div class="no-data">Failed to load reviews</div>';
        });
}

// Display pending reviews
function displayPendingReviews(reviews) {
    const reviewsList = document.getElementById('reviewsList');
    if (!reviewsList) return;
    
    reviewsList.innerHTML = '';
    
    if (reviews.length === 0) {
        reviewsList.innerHTML = '<div class="no-data">No pending reviews</div>';
        return;
    }
    
    reviews.forEach(review => {
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

// Load recent activity from backend
function loadRecentActivity() {
    try {
        console.log('Loading recent activity...');
        const activityList = document.getElementById('activityList');
        if (!activityList) {
            console.error('Activity list element not found!');
            return;
        }

        // Activity table is already server-rendered — skip the AJAX fetch
        const existingRows = activityList.querySelectorAll('tr');
        if (existingRows.length > 0) {
            console.log('Activity table already populated by server — skipping fetch.');
            return;
        }

        activityList.innerHTML = '<tr><td colspan="7" class="loading">Loading activities...</td></tr>';

        fetch('/unipulse/public/moderator/dashboard/getActivities')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch recent activity');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.activities) {
                    displayRecentActivity(data.activities);
                } else {
                    activityList.innerHTML = '<tr><td colspan="5" class="no-data">No recent activities</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading recent activity:', error);
                activityList.innerHTML = '<tr><td colspan="5" class="no-data">Failed to load activities</td></tr>';
            });
    } catch (error) {
        console.error('Error in loadRecentActivity:', error);
    }
}

// Display recent activity
function displayRecentActivity(activities) {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    
    activityList.innerHTML = '';
    
    if (activities.length === 0) {
        activityList.innerHTML = '<tr><td colspan="5" class="no-data">No recent activities</td></tr>';
        return;
    }
    
    // Show first 2 activities initially
    for (let i = 0; i < activities.length; i++) {
        const activity = activities[i];
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
    
    console.log('Recent activity loaded successfully. Added', activities.length, 'activity rows');
}

// Load user reports from backend
function loadUserReports() {
    try {
        console.log('Loading user reports...');
        const reportsTableBody = document.getElementById('reportsTableBody');
        if (!reportsTableBody) {
            console.error('Reports table body element not found!');
            return;
        }

        // Reports table is already server-rendered — skip the AJAX fetch
        const existingRows = reportsTableBody.querySelectorAll('tr');
        if (existingRows.length > 0) {
            console.log('Reports table already populated by server — skipping fetch.');
            return;
        }

        reportsTableBody.innerHTML = '<tr><td colspan="5" class="loading">Loading reports...</td></tr>';

        fetch('/unipulse/public/moderator/dashboard/getUserReports')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch user reports');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.reports) {
                    displayUserReports(data.reports);
                } else {
                    reportsTableBody.innerHTML = '<tr><td colspan="5" class="no-data">No reports found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading user reports:', error);
                reportsTableBody.innerHTML = '<tr><td colspan="5" class="no-data">Failed to load reports</td></tr>';
            });
    } catch (error) {
        console.error('Error in loadUserReports:', error);
    }
}

// Display user reports
function displayUserReports(reports) {
    const reportsTableBody = document.getElementById('reportsTableBody');
    if (!reportsTableBody) return;
    
    reportsTableBody.innerHTML = '';
    
    if (reports.length === 0) {
        reportsTableBody.innerHTML = '<tr><td colspan="5" class="no-data">No reports found</td></tr>';
        return;
    }
    
    // Show first 2 reports initially
    for (let i = 0; i < reports.length; i++) {
        const report = reports[i];
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
    
    console.log('User reports loaded successfully. Added', reports.length, 'report rows');
}



// Setup event listeners
function setupDashboardListeners() {
    // Quick action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            handleQuickAction(action);
        });
    });

    const downloadBtn = document.getElementById('downloadPublisherPerformanceBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            window.location.href = '/unipulse/public/moderator/dashboard/downloadPublisherPerformanceReport';
        });
    }
}

function loadPublisherPerformanceReport() {
    const tableBody = document.getElementById('publisherPerformanceBody');
    if (!tableBody) {
        return;
    }

    tableBody.innerHTML = '<tr><td colspan="5" class="report-loading">Loading publisher performance report...</td></tr>';

    fetch('/unipulse/public/moderator/dashboard/getPublisherPerformanceReport')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load publisher performance report');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Unable to load report data');
            }

            renderPublisherPerformanceSummary(data.summary || {});
            renderPublisherPerformanceRows(Array.isArray(data.rows) ? data.rows : []);
        })
        .catch(error => {
            console.error('Error loading publisher performance report:', error);
            tableBody.innerHTML = '<tr><td colspan="5" class="report-empty">Failed to load report data.</td></tr>';
        });
}

function renderPublisherPerformanceSummary(summary) {
    const publishersEl = document.getElementById('reportTotalPublishers');
    const eventsEl = document.getElementById('reportTotalEvents');
    const ticketsEl = document.getElementById('reportTicketsSold');
    const ratingEl = document.getElementById('reportAvgRating');

    if (publishersEl) publishersEl.textContent = Number(summary.publisher_count || 0).toLocaleString();
    if (eventsEl) eventsEl.textContent = Number(summary.total_events || 0).toLocaleString();
    if (ticketsEl) ticketsEl.textContent = Number(summary.total_tickets_sold || 0).toLocaleString();

    if (ratingEl) {
        const avg = summary.overall_average_rating;
        ratingEl.textContent = (avg === null || avg === undefined) ? 'N/A' : Number(avg).toFixed(2);
    }
}

function renderPublisherPerformanceRows(rows) {
    const tableBody = document.getElementById('publisherPerformanceBody');
    if (!tableBody) {
        return;
    }

    tableBody.innerHTML = '';

    if (!rows.length) {
        tableBody.innerHTML = '<tr><td colspan="5" class="report-empty">No publisher data found for your university.</td></tr>';
        return;
    }

    rows.forEach(row => {
        const tr = document.createElement('tr');
        const avgRating = row.average_rating === null || row.average_rating === undefined
            ? 'N/A'
            : Number(row.average_rating).toFixed(2);

        tr.innerHTML = `
            <td>
                <div class="publisher-cell">
                    <strong>${escapeHtml(row.society_name || 'Unknown Publisher')}</strong>
                    <small>${escapeHtml(row.email || '')}</small>
                </div>
            </td>
            <td>${Number(row.total_events_posted || 0).toLocaleString()}</td>
            <td>${Number(row.tickets_sold || 0).toLocaleString()}</td>
            <td>${Number(row.total_ratings || 0).toLocaleString()}</td>
            <td>${avgRating}</td>
        `;

        tableBody.appendChild(tr);
    });
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = String(value || '');
    return div.innerHTML;
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