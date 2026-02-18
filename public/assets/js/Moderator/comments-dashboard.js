// Moderator Comments Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Moderator Comments Dashboard loaded');
    
    // Initialize the dashboard
    initModeratorCommentsDashboard();
    
    // Load university comments initially
    loadUniversityComments();
    
    // Setup event listeners
    setupCommentsDashboardListeners();
});

function initModeratorCommentsDashboard() {
    console.log('Initializing moderator comments dashboard...');
    
    // Tab switching
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabLinks.forEach(tl => tl.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Load content based on tab
            if (targetTab === 'university-comments') {
                loadUniversityComments();
            } else if (targetTab === 'comment-overview') {
                loadCommentOverview();
            }
        });
    });
}

async function loadUniversityComments() {
    console.log('Loading university comments for moderator...');
    
    try {
        const response = await fetch('/Moderator/Comments/getUniversityComments', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Moderator comments response:', data);
        
        if (data.success) {
            displayUniversityComments(data.comments);
            populateEventFilter(data.comments);
            updateOverviewStats(data);
        } else {
            console.error('Failed to load comments:', data.error);
            showError('Failed to load comments: ' + data.error);
        }
        
    } catch (error) {
        console.error('Error loading moderator comments:', error);
        showError('Error loading comments. Please try again.');
    }
}

function displayUniversityComments(comments) {
    const container = document.getElementById('moderator-comments-list');
    
    if (!comments || comments.length === 0) {
        container.innerHTML = '<div class="no-comments">No comments found for your university events.</div>';
        return;
    }
    
    let html = '';
    comments.forEach(comment => {
        html += `
            <div class="comment-card" data-event-id="${comment.event_id}" data-rating="${comment.rating || ''}">
                <div class="comment-header">
                    <div class="comment-meta">
                        <span class="event-title">${escapeHtml(comment.event_title)}</span>
                        <span class="publisher-name">by ${escapeHtml(comment.publisher_name)}</span>
                        <span class="comment-date">${comment.formatted_date}</span>
                    </div>
                    <div class="comment-actions">
                        ${comment.rating ? `<div class="rating">${generateStars(comment.rating)}</div>` : ''}
                        ${comment.is_edited ? '<span class="edited-badge">Edited</span>' : ''}
                        <span class="user-badge ${comment.user_type}">${comment.user_type}</span>
                    </div>
                </div>
                <div class="comment-content">
                    <div class="comment-author">
                        <strong>${escapeHtml(comment.user_name)}</strong>
                        <span class="user-email">(${escapeHtml(comment.user_email)})</span>
                    </div>
                    <div class="comment-text">${escapeHtml(comment.comment_text)}</div>
                </div>
                <div class="comment-footer">
                    <small>Event Status: <span class="status-${comment.event_status}">${comment.event_status}</span></small>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function populateEventFilter(comments) {
    const eventFilter = document.getElementById('event-filter');
    const events = new Map();
    
    // Extract unique events
    comments.forEach(comment => {
        if (!events.has(comment.event_id)) {
            events.set(comment.event_id, comment.event_title);
        }
    });
    
    // Clear and populate filter
    eventFilter.innerHTML = '<option value="">All Events</option>';
    events.forEach((title, id) => {
        eventFilter.innerHTML += `<option value="${id}">${escapeHtml(title)}</option>`;
    });
}

function updateOverviewStats(data) {
    // Calculate stats from the comments data
    const comments = data.comments || [];
    const totalComments = comments.length;
    const ratedComments = comments.filter(c => c.rating > 0);
    const averageRating = ratedComments.length > 0 ? 
        (ratedComments.reduce((sum, c) => sum + c.rating, 0) / ratedComments.length).toFixed(1) : '0.0';
    const uniqueEvents = new Set(comments.map(c => c.event_id)).size;
    const uniquePublishers = new Set(comments.map(c => c.publisher_name)).size;
    
    document.getElementById('total-university-comments').textContent = totalComments;
    document.getElementById('average-university-rating').textContent = averageRating;
    document.getElementById('active-university-events').textContent = uniqueEvents;
    document.getElementById('university-publishers').textContent = uniquePublishers;
}

async function loadCommentOverview() {
    console.log('Loading comment overview...');
    
    // Load recent comments for the overview
    await loadUniversityComments();
    
    // Display recent activity
    displayRecentActivity();
}

function displayRecentActivity() {
    const comments = Array.from(document.querySelectorAll('.comment-card'));
    const recentComments = comments.slice(0, 5); // Get first 5 comments
    
    const container = document.getElementById('recent-comments-list');
    
    if (recentComments.length === 0) {
        container.innerHTML = '<div class="no-recent-activity">No recent comment activity.</div>';
        return;
    }
    
    let html = '';
    recentComments.forEach(card => {
        const eventTitle = card.querySelector('.event-title').textContent;
        const userName = card.querySelector('.comment-author strong').textContent;
        const commentDate = card.querySelector('.comment-date').textContent;
        const rating = card.getAttribute('data-rating');
        
        html += `
            <div class="recent-activity-item">
                <div class="activity-meta">
                    <span class="activity-user">${escapeHtml(userName)}</span>
                    <span class="activity-action">commented on</span>
                    <span class="activity-event">${escapeHtml(eventTitle)}</span>
                </div>
                <div class="activity-details">
                    ${rating ? `<div class="activity-rating">${generateStars(parseInt(rating))}</div>` : ''}
                    <span class="activity-date">${commentDate}</span>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function setupCommentsDashboardListeners() {
    // Search functionality
    const searchInput = document.getElementById('search-comments');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterComments, 300));
    }
    
    // Filter functionality
    const eventFilter = document.getElementById('event-filter');
    const ratingFilter = document.getElementById('rating-filter');
    
    if (eventFilter) {
        eventFilter.addEventListener('change', filterComments);
    }
    
    if (ratingFilter) {
        ratingFilter.addEventListener('change', filterComments);
    }
}

function filterComments() {
    const searchTerm = document.getElementById('search-comments')?.value.toLowerCase() || '';
    const eventFilter = document.getElementById('event-filter')?.value || '';
    const ratingFilter = document.getElementById('rating-filter')?.value || '';
    
    const commentCards = document.querySelectorAll('.comment-card');
    
    commentCards.forEach(card => {
        const commentText = card.querySelector('.comment-text')?.textContent.toLowerCase() || '';
        const userName = card.querySelector('.comment-author strong')?.textContent.toLowerCase() || '';
        const eventId = card.getAttribute('data-event-id');
        const rating = card.getAttribute('data-rating');
        
        let showCard = true;
        
        // Search filter
        if (searchTerm && !commentText.includes(searchTerm) && !userName.includes(searchTerm)) {
            showCard = false;
        }
        
        // Event filter
        if (eventFilter && eventId !== eventFilter) {
            showCard = false;
        }
        
        // Rating filter
        if (ratingFilter && rating !== ratingFilter) {
            showCard = false;
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

// Utility functions
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<span class="star filled">★</span>';
        } else {
            stars += '<span class="star">☆</span>';
        }
    }
    return stars;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showError(message) {
    // Simple error display - you can enhance this
    console.error(message);
    alert(message);
}

function showSuccess(message) {
    // Simple success display - you can enhance this
    console.log(message);
    alert(message);
}