let currentPublisherId = null;
        let currentAction = null;
        
        // Toggle Activity Log - show/hide additional activity items
        function toggleActivityLog() {
            const activityList = document.getElementById('activityList');
            const hiddenRows = activityList.querySelectorAll('tr.hidden-row');
            const btn = document.getElementById('activityLogBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            if (hiddenRows.length > 0) {
                hiddenRows.forEach(row => {
                    if (row.style.display === 'none') {
                        row.style.display = 'table-row';
                        icon.style.transform = 'rotate(180deg)';
                        btnText.textContent = 'Show Less';
                    } else {
                        row.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        btnText.textContent = 'View Full Log';
                    }
                });
            }
        }

        // Toggle User Reports - show/hide additional report items
        function toggleUserReports() {
            const reportsTable = document.getElementById('reportsTableBody');
            const hiddenRows = reportsTable.querySelectorAll('tr.hidden-row');
            const btn = document.getElementById('userReportsBtn');
            const icon = btn.querySelector('.expand-icon');
            const btnText = btn.querySelector('.btn-text');
            
            if (hiddenRows.length > 0) {
                hiddenRows.forEach(row => {
                    if (row.style.display === 'none') {
                        row.style.display = 'table-row';
                        icon.style.transform = 'rotate(180deg)';
                        btnText.textContent = 'Show Less';
                    } else {
                        row.style.display = 'none';
                        icon.style.transform = 'rotate(0deg)';
                        btnText.textContent = 'View All Reports';
                    }
                });
            }
        }
        
        // Publisher approval functions
        function approvePublisher(publisherId) {
            currentPublisherId = publisherId;
            currentAction = 'approve';
            
            if (!confirm('Are you sure you want to approve this publisher?')) {
                return;
            }
            
            performApprovalAction();
        }
        
        function rejectPublisher(publisherId) {
            currentPublisherId = publisherId;
            currentAction = 'reject';
            
            const reason = prompt('Please provide a reason for rejection (optional):');
            if (reason === null) return; // User cancelled
            
            performApprovalAction(reason);
        }
        
        function performApprovalAction(reason = '') {
            const formData = new FormData();
            if (reason) {
                formData.append('reason', reason);
            }
            
            const endpoint = currentAction === 'approve' ? 'approve' : 'reject';
            
            fetch(`/unipulse/public/moderator/publisherapproval/${endpoint}/${currentPublisherId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const actionText = currentAction === 'approve' ? 'approved' : 'rejected';
                    showNotification(`Publisher ${actionText} successfully!`, 'success');
                    
                    // Remove the publisher card from the list
                    const publisherCard = document.querySelector(`[data-publisher-id="${currentPublisherId}"]`);
                    if (publisherCard) {
                        publisherCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        publisherCard.style.opacity = '0';
                        publisherCard.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            publisherCard.remove();
                            // Check if no more publishers remain
                            const remainingCards = document.querySelectorAll('.publisher-card');
                            if (remainingCards.length === 0) {
                                showEmptyState();
                            }
                        }, 300);
                    }
                    
                    // Update stats
                    updateStats(currentAction);
                } else {
                    showNotification(data.message || `Failed to ${currentAction} publisher`, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(`An error occurred while ${currentAction}ing the publisher`, 'error');
            });
        }
        
        function togglePublisherDetails(publisherId) {
            const expandedSection = document.getElementById(`expanded-${publisherId}`);
            const button = document.querySelector(`[onclick="togglePublisherDetails(${publisherId})"] i`);
            
            if (expandedSection.style.display === 'none') {
                expandedSection.style.display = 'block';
                button.className = 'fas fa-chevron-up';
            } else {
                expandedSection.style.display = 'none';
                button.className = 'fas fa-chevron-down';
            }
        }
        
        function scrollToPublisherApprovals() {
            const section = document.querySelector('.publisher-approval-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        function updateStats(action) {
            // Update the pending count in the quick stats
            const pendingElement = document.getElementById('pendingReviews');
            if (pendingElement) {
                const currentCount = parseInt(pendingElement.textContent) || 0;
                const newCount = Math.max(0, currentCount - 1);
                pendingElement.textContent = newCount;
            }
            
            // Update the section stats badges
            const pendingBadge = document.querySelector('.stat-badge.pending');
            if (pendingBadge) {
                const currentPending = parseInt(pendingBadge.textContent.split(' ')[0]) || 0;
                const newPending = Math.max(0, currentPending - 1);
                pendingBadge.textContent = `${newPending} Pending`;
            }
            
            // Update approved/rejected count
            if (action === 'approve') {
                const approvedBadge = document.querySelector('.stat-badge.approved');
                if (approvedBadge) {
                    const currentApproved = parseInt(approvedBadge.textContent.split(' ')[0]) || 0;
                    approvedBadge.textContent = `${currentApproved + 1} Approved`;
                }
            } else if (action === 'reject') {
                const rejectedBadge = document.querySelector('.stat-badge.rejected');
                if (rejectedBadge) {
                    const currentRejected = parseInt(rejectedBadge.textContent.split(' ')[0]) || 0;
                    rejectedBadge.textContent = `${currentRejected + 1} Rejected`;
                }
            }
        }
        
        function showEmptyState() {
            const publishersGrid = document.getElementById('publishersGrid');
            if (publishersGrid) {
                publishersGrid.innerHTML = `
                    <div class="empty-state-approval">
                        <div class="empty-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>All Caught Up!</h3>
                        <p>There are no pending publisher registrations at the moment.</p>
                    </div>
                `;
            }
        }
        
        // Handle approve/reject button clicks using event delegation
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-approve') || e.target.closest('.btn-approve')) {
                const btn = e.target.classList.contains('btn-approve') ? e.target : e.target.closest('.btn-approve');
                const publisherId = btn.dataset.publisherId;
                approvePublisher(publisherId);
            }
            
            if (e.target.classList.contains('btn-reject') || e.target.closest('.btn-reject')) {
                const btn = e.target.classList.contains('btn-reject') ? e.target : e.target.closest('.btn-reject');
                const publisherId = btn.dataset.publisherId;
                rejectPublisher(publisherId);
            }
        });
        
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