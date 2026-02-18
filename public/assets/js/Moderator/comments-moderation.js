// Moderator Comments Moderation JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeCommentsModeration();
});

function initializeCommentsModeration() {
    console.log('Comments Moderation initialized');
    
    // Initialize filters
    setupFilters();
    
    // Load initial comments
    loadUniversityComments();
}

function setupFilters() {
    const filterElements = document.querySelectorAll('#statusFilter, #sentimentFilter, #eventFilter, #dateFilter');
    
    filterElements.forEach(element => {
        element.addEventListener('change', filterComments);
    });
}

/**
 * Load comments for the moderator's university
 */
function loadUniversityComments() {
    showLoadingState();
    
    fetch('/unipulse/public/moderator/comments/getUniversityComments', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);
        hideLoadingState();
        
        if (data.success) {
            console.log('Comments loaded:', data.comments.length);
            displayComments(data.comments);
            updateStats(data);
        } else {
            console.error('API Error:', data.error);
            showError('Failed to load comments: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoadingState();
        console.error('Error loading comments:', error);
        showError('Failed to load comments. Please try again.');
    });
}

/**
 * Display comments in the UI
 */
function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    const commentsCount = document.getElementById('commentsCount');
    
    if (!comments || comments.length === 0) {
        commentsList.innerHTML = `
            <div class="no-comments">
                <div class="no-comments-content">
                    <i class="fas fa-comments"></i>
                    <h3>No Comments Found</h3>
                    <p>There are no comments to moderate at this time.</p>
                </div>
            </div>
        `;
        commentsCount.textContent = '0 comments';
        return;
    }
    
    commentsCount.textContent = `${comments.length} comment${comments.length !== 1 ? 's' : ''}`;
    
    commentsList.innerHTML = comments.map(comment => createCommentCard(comment)).join('');
}

/**
 * Create HTML for a comment card
 */
function createCommentCard(comment) {
    const sentimentClass = getSentimentClass(comment.rating);
    const sentimentIcon = getSentimentIcon(comment.rating);
    const sentimentText = getSentimentText(comment.rating);
    const isHidden = comment.is_hidden;
    const hiddenClass = isHidden ? 'comment-hidden' : '';
    
    return `
        <div class="comment-card ${hiddenClass}" data-comment-id="${comment.id}">
            ${isHidden ? `
                <div class="hidden-banner">
                    <i class="fas fa-eye-slash"></i>
                    <span>Hidden by ${escapeHtml(comment.hidden_by_name || 'Moderator')}</span>
                    <span class="hidden-reason">Reason: ${escapeHtml(comment.hidden_reason)}</span>
                </div>
            ` : ''}
            <div class="comment-header">
                <div class="comment-user">
                    <div class="user-avatar">${getInitials(comment.user_name)}</div>
                    <div class="user-info">
                        <h4>${escapeHtml(comment.user_name)}</h4>
                        <div class="user-role">${capitalizeFirst(comment.user_type)}</div>
                    </div>
                </div>
                <div class="comment-meta">
                    <span><i class="fas fa-calendar"></i> ${comment.formatted_date}</span>
                    ${comment.rating ? `<span class="${sentimentClass}"><i class="${sentimentIcon}"></i> ${sentimentText}</span>` : ''}
                </div>
            </div>

            <div class="comment-content">
                ${escapeHtml(comment.comment_text)}
                ${comment.is_edited ? '<span class="edited-badge"><i class="fas fa-edit"></i> Edited</span>' : ''}
            </div>

            <div class="comment-event">
                <strong>Event:</strong> ${escapeHtml(comment.event_title)}
                <span class="event-status status-${comment.event_status}">${capitalizeFirst(comment.event_status)}</span>
            </div>

            <div class="comment-actions">
                <input type="checkbox" class="comment-checkbox" value="${comment.id}">
                ${isHidden ? `
                    <button class="review-btn unhide" onclick="unhideComment(${comment.id})">
                        <i class="fas fa-eye"></i>
                        Unhide
                    </button>
                ` : `
                    <button class="review-btn hide" onclick="hideComment(${comment.id})">
                        <i class="fas fa-eye-slash"></i>
                        Hide
                    </button>
                `}
                <button class="review-btn view" onclick="viewCommentContext(${comment.id})">
                    <i class="fas fa-eye"></i>
                    View Context
                </button>
            </div>
        </div>
    `;
}

/**
 * Filter comments based on selected criteria
 */
function filterComments() {
    const statusFilter = document.getElementById('statusFilter').value;
    const sentimentFilter = document.getElementById('sentimentFilter').value;
    const eventFilter = document.getElementById('eventFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const commentCards = document.querySelectorAll('.comment-card');
    let visibleCount = 0;
    
    commentCards.forEach(card => {
        let shouldShow = true;
        
        // Apply filters here (for now, show all)
        // You can implement specific filtering logic based on the filters
        
        if (shouldShow) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.getElementById('commentsCount').textContent = `${visibleCount} comment${visibleCount !== 1 ? 's' : ''}`;
}

/**
 * Approve a comment
 */
function approveComment(commentId) {
    if (!confirm('Are you sure you want to approve this comment?')) {
        return;
    }
    
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    const approveBtn = commentCard.querySelector('.approve');
    
    approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
    approveBtn.disabled = true;
    
    // Since there's no approval system in the current comments table,
    // we'll just show a success message for now
    setTimeout(() => {
        showSuccess('Comment approved successfully');
        commentCard.classList.add('approved');
        approveBtn.innerHTML = '<i class="fas fa-check"></i> Approved';
        approveBtn.classList.add('approved');
    }, 1000);
}

/**
 * Reject a comment
 */
function rejectComment(commentId) {
    if (!confirm('Are you sure you want to reject this comment?')) {
        return;
    }
    
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    const rejectBtn = commentCard.querySelector('.reject');
    
    rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rejecting...';
    rejectBtn.disabled = true;
    
    // For now, just hide the comment as rejected
    setTimeout(() => {
        showSuccess('Comment rejected successfully');
        commentCard.style.display = 'none';
        
        // Update count
        const currentCount = document.getElementById('commentsCount');
        const count = parseInt(currentCount.textContent.match(/\d+/)[0]) - 1;
        currentCount.textContent = `${count} comment${count !== 1 ? 's' : ''}`;
    }, 1000);
}

/**
 * View comment context (event details)
 */
function viewCommentContext(commentId) {
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    const eventTitle = commentCard.querySelector('.comment-event').textContent.replace('Event:', '').trim();
    
    // For now, just show an alert. In a real implementation, you'd load the event details
    alert(`Viewing context for: ${eventTitle}`);
}

/**
 * Bulk actions
 */
function selectAllComments() {
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

function approveSelectedComments() {
    const selectedComments = getSelectedComments();
    
    if (selectedComments.length === 0) {
        showError('Please select comments to approve');
        return;
    }
    
    if (!confirm(`Are you sure you want to approve ${selectedComments.length} comment(s)?`)) {
        return;
    }
    
    selectedComments.forEach(commentId => {
        approveComment(commentId);
    });
}

function rejectSelectedComments() {
    const selectedComments = getSelectedComments();
    
    if (selectedComments.length === 0) {
        showError('Please select comments to reject');
        return;
    }
    
    if (!confirm(`Are you sure you want to reject ${selectedComments.length} comment(s)?`)) {
        return;
    }
    
    selectedComments.forEach(commentId => {
        rejectComment(commentId);
    });
}

function getSelectedComments() {
    const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
    return Array.from(checkboxes).map(cb => parseInt(cb.value));
}

/**
 * Utility functions
 */
function getSentimentClass(rating) {
    if (!rating) return 'sentiment-neutral';
    if (rating >= 4) return 'sentiment-positive';
    if (rating <= 2) return 'sentiment-negative';
    return 'sentiment-neutral';
}

function getSentimentIcon(rating) {
    if (!rating) return 'fas fa-meh';
    if (rating >= 4) return 'fas fa-smile';
    if (rating <= 2) return 'fas fa-frown';
    return 'fas fa-meh';
}

function getSentimentText(rating) {
    if (!rating) return 'Neutral';
    if (rating >= 4) return 'Positive';
    if (rating <= 2) return 'Negative';
    return 'Neutral';
}

function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(word => word.charAt(0).toUpperCase()).slice(0, 2).join('');
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showLoadingState() {
    const commentsList = document.getElementById('commentsList');
    commentsList.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading comments...</p>
        </div>
    `;
}

function hideLoadingState() {
    // Loading state will be replaced by comments or error message
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function updateStats(data) {
    // Update stats if provided
    if (data.stats) {
        const pendingComments = document.getElementById('pendingComments');
        const flaggedToday = document.getElementById('flaggedToday');
        const moderatedToday = document.getElementById('moderatedToday');
        
        if (pendingComments) {
            // Show visible comments (not hidden) as pending review
            pendingComments.textContent = data.stats.visible_comments || 0;
        }
        
        if (flaggedToday) {
            // Show hidden comments as flagged
            flaggedToday.textContent = data.stats.hidden_comments || 0;
        }
        
        if (moderatedToday) {
            // Show comments moderated (hidden) today
            moderatedToday.textContent = data.stats.moderated_today || 0;
        }
    } else if (data.total !== undefined) {
        // Fallback for backward compatibility
        const pendingComments = document.getElementById('pendingComments');
        if (pendingComments) {
            pendingComments.textContent = data.total;
        }
    }
}

// Header dropdown functionality
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = dropdown.style.display === 'none' || !dropdown.style.display ? 'block' : 'none';
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'none' || !dropdown.style.display ? 'block' : 'none';
}

function markAllAsRead() {
    const badge = document.getElementById('notificationBadge');
    badge.style.display = 'none';
    toggleNotifications();
}

// Export functions for global access
window.filterComments = filterComments;
window.approveComment = approveComment;
window.rejectComment = rejectComment;
window.viewCommentContext = viewCommentContext;
window.selectAllComments = selectAllComments;
window.approveSelectedComments = approveSelectedComments;
window.rejectSelectedComments = rejectSelectedComments;
window.toggleNotifications = toggleNotifications;
window.toggleUserMenu = toggleUserMenu;
window.markAllAsRead = markAllAsRead;
window.hideComment = hideComment;
window.unhideComment = unhideComment;

/**
 * Hide a comment with reason
 */
function hideComment(commentId) {
    // Create modal for hide reason
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.id = 'hideCommentModal';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Hide Comment</h2>
                <button class="modal-close" onclick="closeHideModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p class="warning-text">
                    <i class="fas fa-exclamation-triangle"></i>
                    This comment will be hidden from public view. The user will be notified with your reason.
                </p>
                <div class="form-group">
                    <label for="hideReason">Reason for hiding this comment: <span class="required">*</span></label>
                    <textarea 
                        id="hideReason" 
                        rows="4" 
                        maxlength="500"
                        placeholder="E.g., Inappropriate language, spam, offensive content..."
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"
                    ></textarea>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        <span id="reasonCharCount">0</span> / 500 characters (minimum 10)
                    </small>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeHideModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmHideComment(${commentId})">
                    <i class="fas fa-eye-slash"></i> Hide Comment
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Setup character counter
    const textarea = document.getElementById('hideReason');
    const charCount = document.getElementById('reasonCharCount');
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}

/**
 * Close hide comment modal
 */
function closeHideModal() {
    const modal = document.getElementById('hideCommentModal');
    if (modal) {
        modal.remove();
    }
}

/**
 * Confirm hide comment action
 */
async function confirmHideComment(commentId) {
    const reason = document.getElementById('hideReason').value.trim();
    
    if (!reason) {
        showError('Please provide a reason for hiding this comment');
        return;
    }
    
    if (reason.length < 10) {
        showError('Reason must be at least 10 characters long');
        return;
    }
    
    const confirmBtn = document.querySelector('.modal-footer .btn-danger');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hiding...';
    
    try {
        const response = await fetch('/unipulse/public/moderator/comments/hideComment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                comment_id: commentId,
                reason: reason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Comment hidden successfully. User has been notified.');
            closeHideModal();
            // Reload comments
            loadUniversityComments();
        } else {
            showError(data.error || 'Failed to hide comment');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment';
        }
    } catch (error) {
        console.error('Error hiding comment:', error);
        showError('An error occurred while hiding the comment');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment';
    }
}

/**
 * Unhide a comment
 */
async function unhideComment(commentId) {
    if (!confirm('Are you sure you want to unhide this comment? It will become visible to everyone again.')) {
        return;
    }
    
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    const unhideBtn = commentCard.querySelector('.unhide');
    
    unhideBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Unhiding...';
    unhideBtn.disabled = true;
    
    try {
        const response = await fetch('/unipulse/public/moderator/comments/unhideComment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                comment_id: commentId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Comment unhidden successfully. User has been notified.');
            // Reload comments
            loadUniversityComments();
        } else {
            showError(data.error || 'Failed to unhide comment');
            unhideBtn.innerHTML = '<i class="fas fa-eye"></i> Unhide';
            unhideBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error unhiding comment:', error);
        showError('An error occurred while unhiding the comment');
        unhideBtn.innerHTML = '<i class="fas fa-eye"></i> Unhide';
        unhideBtn.disabled = false;
    }
}

window.closeHideModal = closeHideModal;
window.confirmHideComment = confirmHideComment;
