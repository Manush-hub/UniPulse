<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Approvals</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/publisher-approval-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/moderator/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderator/dashboard">Dashboard</a>
                <a href="/unipulse/public/moderator/publisherapproval" class="active">Publisher Approvals</a>
                <a href="/unipulse/public/moderator/events">Events</a>
                <a href="/unipulse/public/moderator/reports">Reports</a>
            </nav>
            <div class="header-actions">
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/moderator.png" alt="Moderator" class="moderator-avatar">
                    <div class="user-info">
                        <span class="username"><?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?></span>
                        <span class="user-role">Moderator</span>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/unipulse/public/moderator/profile"><i class="fas fa-user-cog"></i> Profile</a>
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1><i class="fas fa-user-check"></i> Publisher Approvals</h1>
                    <p>Review and approve publisher registrations for <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?></p>
                </div>
                <div class="header-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($pending_publishers) ?></div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Publishers List -->
        <section class="publishers-section">
            <div class="container">
                <?php if (empty($pending_publishers)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>All Caught Up!</h3>
                        <p>There are no pending publisher registrations for your university at the moment.</p>
                        <a href="/unipulse/public/moderator/dashboard" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="publishers-grid">
                        <?php foreach ($pending_publishers as $publisher): ?>
                            <div class="publisher-card" data-publisher-id="<?= $publisher->id ?>">
                                <div class="publisher-header">
                                    <div class="publisher-info">
                                        <h3><?= htmlspecialchars($publisher->society_name) ?></h3>
                                        <p class="university-info">
                                            <i class="fas fa-university"></i>
                                            <?= htmlspecialchars($publisher->faculty) ?>
                                        </p>
                                    </div>
                                    <div class="publisher-status">
                                        <span class="status-badge pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="publisher-details">
                                    <div class="detail-row">
                                        <strong>Email:</strong>
                                        <span><?= htmlspecialchars($publisher->email) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <strong>Phone:</strong>
                                        <span><?= htmlspecialchars($publisher->country_code . ' ' . $publisher->phone) ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <strong>Registration Date:</strong>
                                        <span><?= date('M j, Y \a\t g:i A', strtotime($publisher->created_at)) ?></span>
                                    </div>
                                    <?php if ($publisher->confirmation_document): ?>
                                        <div class="detail-row">
                                            <strong>Verification Document:</strong>
                                            <a href="/unipulse/public/<?= htmlspecialchars($publisher->confirmation_document) ?>" 
                                               target="_blank" class="document-link">
                                                <i class="fas fa-file-alt"></i> View Document
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="publisher-actions">
                                    <button class="btn btn-success btn-approve" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-reject" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <button class="btn btn-outline btn-view" data-publisher-id="<?= $publisher->id ?>">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Publisher Registration</h3>
                <span class="close-button" onclick="closeModal('rejectionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Please provide a reason for rejecting this publisher registration:</p>
                <textarea id="rejectionReason" rows="4" placeholder="Enter rejection reason..."></textarea>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeModal('rejectionModal')">Cancel</button>
                    <button class="btn btn-danger" onclick="confirmRejection()">
                        <i class="fas fa-times"></i> Reject Publisher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="confirmationTitle">Confirm Action</h3>
                <span class="close-button" onclick="closeModal('confirmationModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage">Are you sure you want to perform this action?</p>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeModal('confirmationModal')">Cancel</button>
                    <button class="btn btn-primary" id="confirmationButton" onclick="performAction()">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .close-button {
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .close-button:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            font-family: inherit;
        }

        .modal-actions {
            margin-top: 20px;
            text-align: right;
        }

        .modal-actions .btn {
            margin-left: 10px;
        }

        .notification {
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
            animation: slideIn 0.3s ease forwards;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-content button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-left: 10px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</body>

</html>