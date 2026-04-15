let currentPublisherId = null;
        let currentAction = null;

        // Handle approve button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-approve') || e.target.closest('.btn-approve')) {
                const btn = e.target.classList.contains('btn-approve') ? e.target : e.target.closest('.btn-approve');
                currentPublisherId = btn.dataset.publisherId;
                currentAction = 'approve';
                
                document.getElementById('confirmationTitle').textContent = 'Approve Publisher';
                document.getElementById('confirmationMessage').textContent = 'Are you sure you want to approve this publisher registration?';
                document.getElementById('confirmationButton').innerHTML = '<i class="fas fa-check"></i> Approve';
                document.getElementById('confirmationButton').className = 'btn btn-success';
                
                openModal('confirmationModal');
            }
        });

        // Handle reject button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-reject') || e.target.closest('.btn-reject')) {
                const btn = e.target.classList.contains('btn-reject') ? e.target : e.target.closest('.btn-reject');
                currentPublisherId = btn.dataset.publisherId;
                currentAction = 'reject';
                
                openModal('rejectionModal');
            }
        });

        // Handle view button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-view') || e.target.closest('.btn-view')) {
                const btn = e.target.classList.contains('btn-view') ? e.target : e.target.closest('.btn-view');
                const publisherId = btn.dataset.publisherId;
                window.location.href = `/unipulse/public/moderator/publisherapproval/view/${publisherId}`;
            }
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (modalId === 'rejectionModal') {
                document.getElementById('rejectionReason').value = '';
            }
        }

        function performAction() {
            if (currentAction === 'approve') {
                approvePublisher(currentPublisherId);
            }
            closeModal('confirmationModal');
        }

        function confirmRejection() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }
            
            rejectPublisher(currentPublisherId, reason);
            closeModal('rejectionModal');
        }

        function approvePublisher(publisherId) {
            const formData = new FormData();
            formData.append('action', 'approve');
            
            fetch(`/unipulse/public/moderator/publisherapproval/approve/${publisherId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Publisher approved successfully!', 'success');
                    removePublisherCard(publisherId);
                } else {
                    showNotification(data.message || 'Failed to approve publisher', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while approving the publisher', 'error');
            });
        }

        function rejectPublisher(publisherId, reason) {
            const formData = new FormData();
            formData.append('reason', reason);
            
            fetch(`/unipulse/public/moderator/publisherapproval/reject/${publisherId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Publisher rejected successfully!', 'success');
                    removePublisherCard(publisherId);
                } else {
                    showNotification(data.message || 'Failed to reject publisher', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while rejecting the publisher', 'error');
            });
        }

        function removePublisherCard(publisherId) {
            const card = document.querySelector(`[data-publisher-id="${publisherId}"]`);
            if (card) {
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    
                    // Check if there are no more publishers
                    const remainingCards = document.querySelectorAll('.publisher-card');
                    if (remainingCards.length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 300);
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
            
            // Add to page
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }