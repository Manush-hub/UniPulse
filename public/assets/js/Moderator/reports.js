// User Reports JavaScript
console.log('User Reports app loaded');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeUserReports();
    loadReports();
    setupEventListeners();
    setupFilters();
});

// Sample data for testing
const sampleReports = [
    {
        id: 1,
        content: '"Spring Party" Event Description',
        reason: 'Contains inappropriate language',
        type: 'inappropriate',
        priority: 'high',
        submitted: '2 hours ago',
        status: 'pending',
        assignedTo: null
    },
    {
        id: 2,
        content: 'User comment on "Tech Talk"',
        reason: 'Spam content',
        type: 'spam',
        priority: 'medium',
        submitted: '5 hours ago',
        status: 'in_progress',
        assignedTo: 'Lisa Chen'
    },
    {
        id: 3,
        content: 'AI Symposium description',
        reason: 'Contains misinformation',
        type: 'misinformation',
        priority: 'high',
        submitted: '1 day ago',
        status: 'resolved',
        assignedTo: 'Lisa Chen'
    },
    {
        id: 4,
        content: 'Student profile picture',
        reason: 'Inappropriate image',
        type: 'inappropriate',
        priority: 'medium',
        submitted: '2 days ago',
        status: 'pending',
        assignedTo: null
    }
];

let filteredReports = [...sampleReports];

function initializeUserReports() {
    console.log('Initializing user reports...');
    updateStats();
}

function loadReports() {
    const reportsTableBody = document.getElementById('reportsTableBody');
    if (!reportsTableBody) {
        console.log('Reports table body not found');
        return;
    }
    
    // Clear existing content
    reportsTableBody.innerHTML = '';
    
    // Load filtered reports
    filteredReports.forEach(report => {
        const reportRow = createReportRow(report);
        reportsTableBody.appendChild(reportRow);
    });
    
    console.log('Loaded', filteredReports.length, 'reports');
    updateReportsCount();
}

function createReportRow(report) {
    const row = document.createElement('tr');
    row.setAttribute('data-report-id', report.id);
    
    const priorityClass = `priority-${report.priority}`;
    const statusClass = `status-${report.status.replace('_', '-')}`;
    const typeClass = `type-${report.type}`;
    
    row.innerHTML = `
        <td><input type="checkbox" class="report-checkbox" value="${report.id}"></td>
        <td>
            <div class="report-content">${report.content}</div>
            <div class="report-reason">${report.reason}</div>
        </td>
        <td>
            <span class="report-type ${typeClass}">${report.type}</span>
        </td>
        <td>
            <span class="${priorityClass}">${report.priority.charAt(0).toUpperCase() + report.priority.slice(1)}</span>
        </td>
        <td>${report.submitted}</td>
        <td>
            <span class="report-status ${statusClass}">${report.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
        </td>
        <td>${report.assignedTo || '-'}</td>
        <td>
            <div class="table-actions">
                <button class="action-btn view" onclick="viewReport(${report.id})" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                ${report.status === 'pending' ? 
                    `<button class="action-btn resolve" onclick="assignToMe(${report.id})" title="Assign to Me">
                        <i class="fas fa-user-check"></i>
                    </button>` :
                    `<button class="action-btn resolve" onclick="resolveReport(${report.id})" title="Resolve">
                        <i class="fas fa-check"></i>
                    </button>`
                }
            </div>
        </td>
    `;
    
    return row;
}

function setupEventListeners() {
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAll);
    }
}

function setupFilters() {
    const filters = ['statusFilter', 'typeFilter', 'priorityFilter', 'dateFilter'];
    
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', filterReports);
        }
    });
}

function updateStats() {
    const pendingCount = sampleReports.filter(r => r.status === 'pending').length;
    const resolvedToday = sampleReports.filter(r => r.status === 'resolved' && r.submitted.includes('hour')).length;
    
    // Update stat elements
    const pendingElement = document.getElementById('pendingReports');
    const resolvedElement = document.getElementById('resolvedToday');
    
    if (pendingElement) pendingElement.textContent = pendingCount;
    if (resolvedElement) resolvedElement.textContent = resolvedToday;
}

function updateReportsCount() {
    const reportsCountElement = document.getElementById('reportsCount');
    if (reportsCountElement) {
        reportsCountElement.textContent = `${filteredReports.length} reports`;
    }
}

function filterReports() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    filteredReports = sampleReports.filter(report => {
        let match = true;
        
        if (statusFilter !== 'all' && report.status !== statusFilter) {
            match = false;
        }
        
        if (typeFilter !== 'all' && report.type !== typeFilter) {
            match = false;
        }
        
        if (priorityFilter !== 'all' && report.priority !== priorityFilter) {
            match = false;
        }
        
        // Simple date filtering (in real app, this would be more sophisticated)
        if (dateFilter !== 'all') {
            if (dateFilter === 'today' && !report.submitted.includes('hour')) {
                match = false;
            } else if (dateFilter === 'week' && report.submitted.includes('day') && !report.submitted.includes('1 day')) {
                match = false;
            }
        }
        
        return match;
    });
    
    loadReports();
    console.log('Filtered reports:', filteredReports.length);
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const reportCheckboxes = document.querySelectorAll('.report-checkbox');
    
    reportCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

function viewReport(reportId) {
    console.log('Viewing report ID:', reportId);
    const report = sampleReports.find(r => r.id === reportId);
    
    if (report) {
        showReportModal(report);
    }
}

function assignToMe(reportId) {
    console.log('Assigning report to me:', reportId);
    
    if (confirm('Assign this report to yourself?')) {
        // Make API call to assign report
        fetch(`/unipulse/public/moderator/userreports/assign/${reportId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Report assigned successfully!', 'success');
                // Update the report in the list
                const reportIndex = sampleReports.findIndex(r => r.id === reportId);
                if (reportIndex !== -1) {
                    sampleReports[reportIndex].status = 'in_progress';
                    sampleReports[reportIndex].assignedTo = 'You';
                    filterReports(); // Refresh the display
                }
            } else {
                showNotification(data.message || 'Failed to assign report', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while assigning the report', 'error');
        });
    }
}

function resolveReport(reportId) {
    console.log('Resolving report ID:', reportId);
    
    const resolution = prompt('Please provide a resolution summary:');
    if (resolution === null) return; // User cancelled
    
    const actionTaken = prompt('What action was taken?');
    if (actionTaken === null) return; // User cancelled
    
    // Make API call to resolve report
    const formData = new FormData();
    formData.append('resolution', resolution);
    formData.append('action_taken', actionTaken);
    
    fetch(`/unipulse/public/moderator/userreports/resolve/${reportId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Report resolved successfully!', 'success');
            // Update the report in the list
            const reportIndex = sampleReports.findIndex(r => r.id === reportId);
            if (reportIndex !== -1) {
                sampleReports[reportIndex].status = 'resolved';
                filterReports(); // Refresh the display
            }
            updateStats();
        } else {
            showNotification(data.message || 'Failed to resolve report', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while resolving the report', 'error');
    });
}

function showReportModal(report) {
    const modal = document.getElementById('reportModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (modal && modalTitle && modalBody) {
        modalTitle.textContent = 'Report Details';
        modalBody.innerHTML = `
            <div class="report-details">
                <h4>Report Information</h4>
                <p><strong>Reported Content:</strong> ${report.content}</p>
                <p><strong>Reason:</strong> ${report.reason}</p>
                <p><strong>Type:</strong> ${report.type}</p>
                <p><strong>Priority:</strong> ${report.priority}</p>
                <p><strong>Status:</strong> ${report.status}</p>
                <p><strong>Submitted:</strong> ${report.submitted}</p>
                <p><strong>Assigned To:</strong> ${report.assignedTo || 'Unassigned'}</p>
                
                <div class="modal-actions">
                    ${report.status === 'pending' ? 
                        `<button class="btn btn-primary" onclick="assignToMe(${report.id}); closeModal('reportModal');">
                            <i class="fas fa-user-check"></i> Assign to Me
                        </button>` :
                        `<button class="btn btn-success" onclick="resolveReport(${report.id}); closeModal('reportModal');">
                            <i class="fas fa-check"></i> Resolve
                        </button>`
                    }
                </div>
            </div>
        `;
        modal.classList.add('show');
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        min-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Bulk actions
function selectAllReports() {
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.checked = true;
    toggleSelectAll();
    console.log('Selected all reports');
}

function assignSelected() {
    const selectedReports = document.querySelectorAll('.report-checkbox:checked');
    console.log('Assigning', selectedReports.length, 'selected reports');
    
    if (selectedReports.length === 0) {
        showNotification('Please select reports to assign', 'error');
        return;
    }
    
    if (confirm(`Assign ${selectedReports.length} reports to yourself?`)) {
        selectedReports.forEach(checkbox => {
            const reportId = parseInt(checkbox.value);
            assignToMe(reportId);
        });
    }
}

function resolveSelected() {
    const selectedReports = document.querySelectorAll('.report-checkbox:checked');
    console.log('Resolving', selectedReports.length, 'selected reports');
    
    if (selectedReports.length === 0) {
        showNotification('Please select reports to resolve', 'error');
        return;
    }
    
    if (confirm(`Mark ${selectedReports.length} reports as resolved?`)) {
        selectedReports.forEach(checkbox => {
            const reportId = parseInt(checkbox.value);
            resolveReport(reportId);
        });
    }
}

// Export functions
function exportReports() {
    console.log('Exporting reports...');
    showNotification('Export functionality will be implemented soon', 'info');
}

function showReportGuidelines() {
    console.log('Showing report guidelines...');
    showNotification('Guidelines modal will be implemented soon', 'info');
}