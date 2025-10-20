// Publisher Comments Application
let allComments = [];
let filteredComments = [];
let notifications = [];
let currentFilter = 'all';

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    loadData();
});

// Initialize application
function initializeApp() {
    console.log('Publisher Comments App initialized');
}

// Setup event listeners
function setupEventListeners() {
    // Notification badge click
    document.getElementById('notificationBadge').addEventListener('click', toggleNotificationsPanel);
    
    // Filter select
    document.getElementById('filterSelect').addEventListener('change', function() {
        currentFilter = this.value;
        filterComments();
    });
    
    // Refresh button
    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadComments();
        loadNotifications();
    });
    
    // Notifications panel controls
    document.getElementById('closeNotificationsBtn').addEventListener('click', closeNotificationsPanel);
    document.getElementById('markAllReadBtn').addEventListener('click', markAllNotificationsRead);
    
    // Close notifications panel when clicking outside
    document.addEventListener('click', function(e) {
        const panel = document.getElementById('notificationsPanel');
        const badge = document.getElementById('notificationBadge');
        
        if (!panel.contains(e.target) && !badge.contains(e.target)) {
            closeNotificationsPanel();
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('commentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommentModal();
        }
    });
}

// Load all data
function loadData() {
    loadStats();
    loadComments();
    loadNotifications();
}

// Load statistics
async function loadStats() {
    try {
        const response = await fetch('/unipulse/public/publisher/comments/getStats');
        const data = await response.json();
        
        if (data.success) {
            updateStatsDisplay(data.stats);
        } else {
            console.error('Failed to load stats:', data.error);
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Update statistics display
function updateStatsDisplay(stats) {
    document.getElementById('totalComments').textContent = stats.total_comments;
    document.getElementById('eventsWithComments').textContent = stats.events_with_comments;
    document.getElementById('averageRating').textContent = stats.average_rating;
    document.getElementById('commentsToday').textContent = stats.comments_today;
    
    // Update notification badge
    const badgeCount = document.getElementById('badgeCount');
    if (stats.unread_notifications > 0) {
        badgeCount.textContent = stats.unread_notifications;
        badgeCount.classList.remove('hidden');
    } else {
        badgeCount.classList.add('hidden');
    }
}

// Load comments
async function loadComments() {
    const container = document.getElementById('commentsContainer');
    container.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading comments...</p>
        </div>
    `;
    
    try {
        const response = await fetch('/unipulse/public/publisher/comments/getComments');
        const data = await response.json();
        
        if (data.success) {
            allComments = data.comments;
            filterComments();
        } else {
            showError('Failed to load comments: ' + data.error);
            displayEmptyState('Error loading comments');
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        showError('Failed to load comments');
        displayEmptyState('Error loading comments');
    }
}

// Filter comments based on current filter
function filterComments() {
    const now = new Date();
    
    switch (currentFilter) {
        case 'today':
            filteredComments = allComments.filter(comment => {
                const commentDate = new Date(comment.created_at);
                return commentDate.toDateString() === now.toDateString();
            });
            break;
            
        case 'week':
            const oneWeekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
            filteredComments = allComments.filter(comment => {
                const commentDate = new Date(comment.created_at);
                return commentDate >= oneWeekAgo;
            });
            break;
            
        case 'month':
            const oneMonthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
            filteredComments = allComments.filter(comment => {
                const commentDate = new Date(comment.created_at);
                return commentDate >= oneMonthAgo;
            });
            break;
            
        case 'rated':
            filteredComments = allComments.filter(comment => comment.rating && comment.rating > 0);
            break;
            
        default:
            filteredComments = [...allComments];
    }
    
    displayComments();
}

// Display comments
function displayComments() {
    const container = document.getElementById('commentsContainer');
    
    if (filteredComments.length === 0) {
        displayEmptyState('No comments found');
        return;
    }
    
    const commentsHTML = filteredComments.map(comment => createCommentCard(comment)).join('');
    container.innerHTML = commentsHTML;
}

// Create comment card HTML
function createCommentCard(comment) {
    const userInitials = comment.user_name.split(' ').map(n => n[0]).join('').toUpperCase();
    const ratingStars = comment.rating ? '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating) : 'No rating';
    const editedBadge = comment.is_edited ? '<span class="edited-badge">Edited</span>' : '';
    
    return `
        <div class="comment-card" data-comment-id="${comment.id}">
            <div class="comment-header">
                <div class="comment-user">
                    <div class="user-avatar">${userInitials}</div>
                    <div class="user-info">
                        <h4>${escapeHtml(comment.user_name)}</h4>
                        <p>${comment.user_type.charAt(0).toUpperCase() + comment.user_type.slice(1)} User ${editedBadge}</p>
                    </div>
                </div>
                
                <div class="comment-meta">
                    ${comment.rating ? `
                        <div class="comment-rating">
                            <span class="stars">${ratingStars}</span>
                            <span>${comment.rating}/5</span>
                        </div>
                    ` : ''}
                    <p>${comment.formatted_date}</p>
                </div>
            </div>
            
            <div class="comment-content">
                ${escapeHtml(comment.comment_text)}
            </div>
            
            <div class="comment-footer">
                <div class="event-info">
                    <i class="fas fa-calendar"></i>
                    ${escapeHtml(comment.event_title)} - ${formatDate(comment.event_date)}
                </div>
                
                <div class="comment-actions">
                    <button class="btn-sm btn-view" onclick="viewCommentDetails(${comment.id})">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Display empty state
function displayEmptyState(message) {
    const container = document.getElementById('commentsContainer');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>${message}</h3>
            <p>Comments will appear here when users start commenting on your completed events.</p>
        </div>
    `;
}

// View comment details
function viewCommentDetails(commentId) {
    const comment = allComments.find(c => c.id === commentId);
    if (!comment) return;
    
    const modal = document.getElementById('commentModal');
    const modalBody = document.getElementById('commentModalBody');
    
    const userInitials = comment.user_name.split(' ').map(n => n[0]).join('').toUpperCase();
    const ratingStars = comment.rating ? '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating) : 'No rating provided';
    
    modalBody.innerHTML = `
        <div class="comment-details">
            <div class="comment-user-full">
                <div class="user-avatar-large">${userInitials}</div>
                <div class="user-info-full">
                    <h3>${escapeHtml(comment.user_name)}</h3>
                    <p class="user-type">${comment.user_type.charAt(0).toUpperCase() + comment.user_type.slice(1)} User</p>
                    ${comment.user_email ? `<p class="user-email">${escapeHtml(comment.user_email)}</p>` : ''}
                </div>
            </div>
            
            <div class="event-details">
                <h4>Event</h4>
                <p><strong>${escapeHtml(comment.event_title)}</strong></p>
                <p>Date: ${formatDate(comment.event_date)}</p>
            </div>
            
            ${comment.rating ? `
                <div class="rating-details">
                    <h4>Rating</h4>
                    <div class="rating-display">
                        <span class="stars-large">${ratingStars}</span>
                        <span class="rating-number">${comment.rating}/5</span>
                    </div>
                </div>
            ` : ''}
            
            <div class="comment-text-full">
                <h4>Comment</h4>
                <div class="comment-content-full">
                    ${escapeHtml(comment.comment_text)}
                </div>
            </div>
            
            <div class="comment-timestamps">
                <p><strong>Posted:</strong> ${formatDateTime(comment.created_at)}</p>
                ${comment.is_edited ? `<p><strong>Last edited:</strong> ${formatDateTime(comment.updated_at)}</p>` : ''}
            </div>
        </div>
        
        <style>
            .comment-details { padding: 1rem 0; }
            .comment-user-full { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
            .user-avatar-large { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.5rem; }
            .user-info-full h3 { margin: 0; color: #1f2937; }
            .user-type { margin: 0.25rem 0; color: #6b7280; font-size: 0.9rem; }
            .user-email { margin: 0.25rem 0; color: #6b7280; font-size: 0.85rem; }
            .event-details, .rating-details, .comment-text-full { margin-bottom: 1.5rem; }
            .event-details h4, .rating-details h4, .comment-text-full h4 { margin: 0 0 0.5rem 0; color: #374151; font-size: 1rem; }
            .rating-display { display: flex; align-items: center; gap: 0.5rem; }
            .stars-large { color: #fbbf24; font-size: 1.2rem; }
            .rating-number { font-weight: 600; color: #374151; }
            .comment-content-full { background: #f9fafb; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea; line-height: 1.6; }
            .comment-timestamps { background: #f3f4f6; padding: 1rem; border-radius: 8px; margin-top: 1rem; }
            .comment-timestamps p { margin: 0.25rem 0; font-size: 0.9rem; color: #6b7280; }
        </style>
    `;
    
    modal.classList.add('open');
}

// Close comment modal
function closeCommentModal() {
    document.getElementById('commentModal').classList.remove('open');
}

// Load notifications
async function loadNotifications() {
    try {
        const response = await fetch('/unipulse/public/publisher/comments/getNotifications');
        const data = await response.json();
        
        if (data.success) {
            notifications = data.notifications;
            displayNotifications();
            updateNotificationBadge(data.unread_count);
        } else {
            console.error('Failed to load notifications:', data.error);
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

// Display notifications
function displayNotifications() {
    const container = document.getElementById('notificationsContent');
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No notifications</h3>
                <p>You'll receive notifications when users comment on your events.</p>
            </div>
        `;
        return;
    }
    
    const notificationsHTML = notifications.map(notification => createNotificationItem(notification)).join('');
    container.innerHTML = notificationsHTML;
}

// Create notification item
function createNotificationItem(notification) {
    const unreadClass = notification.is_read ? '' : 'unread';
    
    return `
        <div class="notification-item ${unreadClass}" onclick="handleNotificationClick(${notification.id})">
            <div class="notification-header">
                <div class="notification-title">${escapeHtml(notification.title)}</div>
                <div class="notification-time">${notification.formatted_date}</div>
            </div>
            <div class="notification-message">
                ${escapeHtml(notification.message)}
            </div>
        </div>
    `;
}

// Handle notification click
async function handleNotificationClick(notificationId) {
    const notification = notifications.find(n => n.id === notificationId);
    if (!notification) return;
    
    // Mark as read if unread
    if (!notification.is_read) {
        await markNotificationRead(notificationId);
    }
    
    // Navigate to related content if applicable
    if (notification.related_type === 'event' && notification.related_id) {
        window.location.href = `/unipulse/public/publisher/eventview?id=${notification.related_id}`;
    }
}

// Mark notification as read
async function markNotificationRead(notificationId) {
    try {
        const response = await fetch(`/unipulse/public/publisher/comments/markNotificationRead/${notificationId}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update local notification
            const notification = notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.is_read = true;
            }
            
            // Refresh notifications display
            displayNotifications();
            loadStats(); // Refresh badge count
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all notifications as read
async function markAllNotificationsRead() {
    try {
        const response = await fetch('/unipulse/public/publisher/comments/markAllNotificationsRead', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update all local notifications
            notifications.forEach(notification => {
                notification.is_read = true;
            });
            
            // Refresh displays
            displayNotifications();
            loadStats(); // Refresh badge count
            showSuccess('All notifications marked as read');
        } else {
            showError('Failed to mark notifications as read');
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
        showError('Failed to mark notifications as read');
    }
}

// Toggle notifications panel
function toggleNotificationsPanel() {
    const panel = document.getElementById('notificationsPanel');
    panel.classList.toggle('open');
}

// Close notifications panel
function closeNotificationsPanel() {
    document.getElementById('notificationsPanel').classList.remove('open');
}

// Update notification badge
function updateNotificationBadge(count) {
    const badgeCount = document.getElementById('badgeCount');
    if (count > 0) {
        badgeCount.textContent = count;
        badgeCount.classList.remove('hidden');
    } else {
        badgeCount.classList.add('hidden');
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// Toast notifications
function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

// Auto-refresh data every 30 seconds
setInterval(() => {
    loadStats();
    loadNotifications();
}, 30000);