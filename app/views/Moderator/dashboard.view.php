<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Moderator Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    $headerCssLoaded = true;
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername"><?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?></span>! 👋</h1>
                        <p>Manage content moderation and ensure platform quality from your moderator dashboard.</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingReviews"><?= $publisher_stats->pending ?? 0 ?></span>
                                <span class="stat-label">Pending Publisher Approvals</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="eventsReviewed"><?= $publisher_stats->approved ?? 0 ?></span>
                                <span class="stat-label">Approved Publishers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="reportsHandled"><?= $publisher_stats->rejected ?? 0 ?></span>
                                <span class="stat-label">Rejected Publishers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="approvalRate"><?= $publisher_stats->total ?? 0 ?></span>
                                <span class="stat-label">Total Publishers</span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="welcome-actions">
                        <?php if (isset($permissions['approve_publishers']) && $permissions['approve_publishers']): ?>
                        <button class="btn btn-primary" onclick="window.location.href='/unipulse/public/moderator/publisherapproval'">
                            <i class="fas fa-user-check"></i>
                            Publisher Approvals
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-primary" onclick="window.location.href='content-moderation.html'">
                            <i class="fas fa-shield-alt"></i>
                            Review Content
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href='guidelines.html'">
                            <i class="fas fa-book"></i>
                            Guidelines
                        </button>
                    </div> -->
                </div>
            </div>
        </section>

        <!-- Publisher Approval Section -->
        <?php if (isset($permissions['approve_publishers']) && $permissions['approve_publishers']): ?>
        <section class="publisher-approval-section">
            <div class="container">
                <div class="section-header">
                    <h2><i class="fas fa-user-check"></i> Publisher Approvals</h2>
                    <div class="section-stats">
                        <span class="stat-badge pending"><?= $publisher_stats->pending ?? 0 ?> Pending</span>
                        <span class="stat-badge approved"><?= $publisher_stats->approved ?? 0 ?> Approved</span>
                        <span class="stat-badge rejected"><?= $publisher_stats->rejected ?? 0 ?> Rejected</span>
                    </div>
                </div>
                
                <?php if (isset($recent_pending_publishers) && !empty($recent_pending_publishers)): ?>
                <div class="publishers-grid" id="publishersGrid">
                    <?php foreach ($recent_pending_publishers as $publisher): ?>
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
                                    <strong>Document:</strong>
                                    <a href="/unipulse/public/<?= htmlspecialchars($publisher->confirmation_document) ?>" 
                                       target="_blank" class="document-link">
                                        <i class="fas fa-file-alt"></i> View Document
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="publisher-actions">
                            <button class="btn btn-success btn-sm btn-approve" data-publisher-id="<?= $publisher->id ?>">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm btn-reject" data-publisher-id="<?= $publisher->id ?>">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="togglePublisherDetails(<?= $publisher->id ?>)">
                                <i class="fas fa-chevron-down"></i> Details
                            </button>
                        </div>
                        
                        <div class="publisher-expanded" id="expanded-<?= $publisher->id ?>" style="display: none;">
                            <div class="expanded-content">
                                <h4>Additional Information</h4>
                                <div class="detail-row">
                                    <strong>University:</strong>
                                    <span><?= ucwords(str_replace('-', ' ', $publisher->university)) ?></span>
                                </div>
                                <div class="detail-row">
                                    <strong>Registration ID:</strong>
                                    <span>#PUB-<?= str_pad($publisher->id, 4, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <?php if ($publisher->confirmation_document): ?>
                                <div class="document-preview">
                                    <strong>Verification Document:</strong>
                                    <div class="document-info">
                                        <i class="fas fa-file-alt"></i>
                                        <span><?= basename($publisher->confirmation_document) ?></span>
                                        <a href="/unipulse/public/<?= htmlspecialchars($publisher->confirmation_document) ?>" 
                                           target="_blank" class="btn btn-outline btn-xs">
                                            <i class="fas fa-external-link-alt"></i> Open
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state-approval">
                    <div class="empty-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>All Caught Up!</h3>
                    <p>There are no pending publisher registrations for <?= htmlspecialchars($moderator->university_name ?? $moderator->university) ?> at the moment.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Recent Activity -->
        <section class="recent-activity">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Moderation Activity</h2>
                    <button onclick="toggleActivityLog()" class="view-all expand-btn" id="activityLogBtn">
                        <span class="btn-text">View Full Log</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="activity-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Moderator</th>
                                <th>University</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="activityList">
                            <!-- Recent moderation activities -->
                            <?php if (isset($recent_activities) && !empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $index => $activity): ?>
                                    <tr<?= $index >= 5 ? ' class="hidden-row" style="display: none;"' : '' ?>>
                                        <td>
                                            <div class="activity-info">
                                                <?php if ($activity->activity_type === 'hidden_event'): ?>
                                                    <i class="fas fa-eye-slash activity-icon" style="color: #f59e0b;"></i>
                                                    <span>Hid Event</span>
                                                <?php elseif ($activity->activity_type === 'restored_event'): ?>
                                                    <i class="fas fa-eye activity-icon" style="color: #3b82f6;"></i>
                                                    <span>Unhid Event</span>
                                                <?php elseif ($activity->activity_type === 'hidden_comment'): ?>
                                                    <i class="fas fa-comment-slash activity-icon" style="color: #8b5cf6;"></i>
                                                    <span>Hid Comment</span>
                                                <?php elseif ($activity->activity_type === 'publisher_approved'): ?>
                                                    <i class="fas fa-check-circle activity-icon" style="color: #10b981;"></i>
                                                    <span>Approved Publisher</span>
                                                <?php elseif ($activity->activity_type === 'publisher_rejected'): ?>
                                                    <i class="fas fa-times-circle activity-icon" style="color: #ef4444;"></i>
                                                    <span>Rejected Publisher</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($activity->activity_type === 'hidden_event'): ?>
                                                <span class="badge badge-warning">Event Hidden</span>
                                            <?php elseif ($activity->activity_type === 'restored_event'): ?>
                                                <span class="badge badge-info">Event Unhidden</span>
                                            <?php elseif ($activity->activity_type === 'hidden_comment'): ?>
                                                <span class="badge" style="background:#ede9fe;color:#6d28d9;">Comment Hidden</span>
                                            <?php elseif ($activity->activity_type === 'publisher_approved'): ?>
                                                <span class="badge badge-success">Publisher Approved</span>
                                            <?php elseif ($activity->activity_type === 'publisher_rejected'): ?>
                                                <span class="badge badge-danger">Publisher Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="activity-details">
                                                <strong><?= htmlspecialchars($activity->item_title) ?></strong>
                                                <br>
                                                <?php if (in_array($activity->activity_type, ['hidden_event', 'hidden_comment', 'publisher_rejected']) && $activity->activity_reason): ?>
                                                    <small>Reason: <?= htmlspecialchars(substr($activity->activity_reason, 0, 60)) ?><?= strlen($activity->activity_reason) > 60 ? '...' : '' ?></small>
                                                <?php elseif ($activity->activity_type === 'restored_event'): ?>
                                                    <small>Event restored / made visible again</small>
                                                <?php elseif ($activity->activity_type === 'publisher_approved'): ?>
                                                    <small>Approved by <?= htmlspecialchars($activity->moderator_name ?: 'Moderator') ?></small>
                                                <?php elseif ($activity->activity_type === 'publisher_rejected'): ?>
                                                    <small>Rejected by <?= htmlspecialchars($activity->moderator_name ?: 'Moderator') ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($activity->moderator_name ?: 'Unknown') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= htmlspecialchars($activity->university ?: 'N/A') ?></span>
                                        </td>
                                        <td>
                                            <span class="time-ago" data-time="<?= $activity->activity_time ?>">
                                                <?= $activity->activity_time ? date('M d, Y H:i', strtotime($activity->activity_time)) : '-' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($activity->activity_type === 'restored_event'): ?>
                                                <span class="status-badge" style="background:#dbeafe;color:#1d4ed8;padding:4px 10px;border-radius:6px;font-size:0.78rem;"><i class="fas fa-undo"></i> Restored</span>
                                            <?php elseif ($activity->activity_type === 'publisher_rejected'): ?>
                                                <span class="status-badge" style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:6px;font-size:0.78rem;"><i class="fas fa-times"></i> Rejected</span>
                                            <?php else: ?>
                                                <span class="status-badge status-completed"><i class="fas fa-check"></i> Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                                        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                        No moderation activities yet
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- User Reports -->
        <section class="user-reports">
            <div class="container">
                <div class="section-header">
                    <h2>Recent User Reports</h2>
                    <button onclick="toggleUserReports()" class="view-all expand-btn" id="userReportsBtn">
                        <span class="btn-text">View All Reports</span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </button>
                </div>
                <div class="reports-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Reported Content</th>
                                <th>Report Type</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody">
                            <?php if (!empty($user_reports)): ?>
                                <?php foreach ($user_reports as $index => $report): ?>
                                    <tr<?= $index >= 5 ? ' class="hidden-row" style="display:none;"' : '' ?>>
                                        <td class="report-content">
                                            <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $report->reported_content_type ?? 'Content'))) ?></strong>
                                            <?php if (!empty($report->reporter_name)): ?>
                                                <br><small>by <?= htmlspecialchars($report->reporter_name) ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($report->description)): ?>
                                                <br><small><?= htmlspecialchars(substr($report->description, 0, 60)) ?><?= strlen($report->description) > 60 ? '...' : '' ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $rtype = $report->report_type ?? 'other';
                                                $rtypeColors = ['spam'=>'#fef3c7;color:#92400e','inappropriate'=>'#fee2e2;color:#b91c1c','harassment'=>'#fce7f3;color:#9d174d','misinformation'=>'#ede9fe;color:#6d28d9','other'=>'#f3f4f6;color:#374151'];
                                                $rtypeStyle = $rtypeColors[$rtype] ?? $rtypeColors['other'];
                                            ?>
                                            <span class="badge" style="background:<?= $rtypeStyle ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $rtype))) ?></span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($report->created_at)) ?></td>
                                        <td>
                                            <?php
                                                $statusColors = ['pending'=>'badge-warning','in_progress'=>'badge-info','resolved'=>'badge-success'];
                                                $sc = $statusColors[$report->status] ?? 'badge-warning';
                                            ?>
                                            <span class="badge <?= $sc ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $report->status ?? 'pending'))) ?></span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="/unipulse/public/moderator/userreports" class="action-btn view" title="View Reports"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:2rem;color:#6b7280;">
                                        <i class="fas fa-flag" style="font-size:2rem;margin-bottom:0.5rem;display:block;"></i>
                                        No user reports yet
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

<!-- Moderation Stats and Guidelines Section -->
        <section class="moderation-overview">
            <div class="container">
                <div class="moderation-grid">
                    <div class="moderation-card">
                        <h3><i class="fas fa-chart-bar"></i> Moderation Stats</h3>
                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-eye-slash" style="color: #f59e0b;"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number"><?= $moderation_stats['hidden_events'] ?? 0 ?></span>
                                    <span class="stat-label">Hidden Events</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number"><?= $moderation_stats['approved_publishers'] ?? 0 ?></span>
                                    <span class="stat-label">Approved Publishers</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number"><?= $moderation_stats['rejected_publishers'] ?? 0 ?></span>
                                    <span class="stat-label">Rejected Publishers</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-tasks" style="color: #3b82f6;"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-number"><?= $moderation_stats['total_actions'] ?? 0 ?></span>
                                    <span class="stat-label">Total Actions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="moderation-card">
                        <h3><i class="fas fa-book"></i> Moderation Guidelines</h3>
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Ensure events follow university policies</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Verify organizer credentials and authenticity</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Check for appropriate and respectful content</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Ensure accurate event information and dates</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Verify event location and capacity details</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Review event descriptions for clarity</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Check registration requirements are reasonable</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Ensure images and media are appropriate</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Verify publisher documentation is valid</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check"></i>
                                <span>Respond to requests within 24-48 hours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Footer -->
    <!-- <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
                <a href="#about">About UniPulse</a>
            </div>
            <div class="footer-copyright">
                <span>&copy; 2025 UniPulse. All rights reserved.</span>
            </div>
        </div>
    </footer> -->

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Modals -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('reviewModal')">&times;</span>
            <h3 id="modalTitle">Event Review</h3>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Provide moderatorData so dashboard-app.js can reference it
        const moderatorData = {
            displayName: '<?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?>',
            pendingReviews: <?= intval($publisher_stats->pending ?? 0) ?>,
            eventsReviewed: <?= intval($publisher_stats->approved ?? 0) ?>,
            reportsHandled: <?= intval($publisher_stats->rejected ?? 0) ?>,
            approvalRate: <?= intval($publisher_stats->total ?? 0) ?>,
            approvedEvents: <?= intval($moderation_stats['approved_publishers'] ?? 0) ?>,
            rejectedEvents: <?= intval($moderation_stats['rejected_publishers'] ?? 0) ?>,
            editedEvents: <?= intval($moderation_stats['hidden_events'] ?? 0) ?>,
            verifiedOrganizers: <?= intval($moderation_stats['total_actions'] ?? 0) ?>
        };
    </script>
    <script src="/unipulse/public/assets/js/Moderator/dashboard-app.js"></script>
    <script>
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
    </script>
</body>

</html>