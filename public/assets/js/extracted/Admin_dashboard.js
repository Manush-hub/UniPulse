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