// Admin Comments Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Comments Dashboard loaded');
    
    // Initialize the dashboard
    initAdminCommentsDashboard();
    
    // Load all comments initially
    loadAllComments();
    
    // Load statistics
    loadCommentStats();
    
    // Setup event listeners
    setupEventListeners();
});

function initAdminCommentsDashboard() {
    console.log('Initializing admin comments dashboard...');
    
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
            if (targetTab === 'all-comments') {
                loadAllComments();
            } else if (targetTab === 'comment-stats') {
                loadCommentStats();
            }
        });
    });
}

async function loadAllComments() {
    console.log('Loading all comments for admin...');
    
    try {
        const response = await fetch('/Admin/Comments/getAllComments', {
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
        console.log('Admin comments response:', data);
        
        if (data.success) {
            displayAllComments(data.comments);
            populateEventFilter(data.comments);
        } else {
            console.error('Failed to load comments:', data.error);
            showError('Failed to load comments: ' + data.error);
        }
        
    } catch (error) {
        console.error('Error loading admin comments:', error);
        showError('Error loading comments. Please try again.');
    }
}

function displayAllComments(comments) {
    const container = document.getElementById('admin-comments-list');
    
    if (!comments || comments.length === 0) {
        container.innerHTML = '<div class="no-comments">No comments found.</div>';
        return;
    }
    
    let html = '';
    comments.forEach(comment => {
        html += `
            <div class="comment-card" data-event-id="${comment.event_id}" data-user-type="${comment.user_type}">
                <div class="comment-header">
                    <div class="comment-meta">
                        <span class="event-title">${escapeHtml(comment.event_title)}</span>
                        <span class="user-badge ${comment.user_type}">${comment.user_type}</span>
                        <span class="comment-date">${comment.formatted_date}</span>
                    </div>
                    <div class="comment-actions">
                        ${comment.rating ? `<div class="rating">${generateStars(comment.rating)}</div>` : ''}
                        ${comment.is_edited ? '<span class="edited-badge">Edited</span>' : ''}
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
                    <small>Event Status: ${comment.event_status}</small>
                    <small>Publisher: ${escapeHtml(comment.publisher_name)}</small>
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

async function loadCommentStats() {
    console.log('Loading comment statistics...');
    
    try {
        const response = await fetch('/Admin/Comments/getStats', {
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
        console.log('Admin stats response:', data);
        
        if (data.success) {
            displayStats(data.stats);
        } else {
            console.error('Failed to load stats:', data.error);
            showError('Failed to load statistics: ' + data.error);
        }
        
    } catch (error) {
        console.error('Error loading admin stats:', error);
        showError('Error loading statistics. Please try again.');
    }
}

function displayStats(stats) {
    document.getElementById('total-comments').textContent = stats.total_comments || '0';
    document.getElementById('average-rating').textContent = stats.average_rating || '0.0';
    document.getElementById('active-events').textContent = stats.events_with_comments || '0';
    document.getElementById('recent-comments').textContent = stats.comments_this_week || '0';
}

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('search-comments');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterComments, 300));
    }
    
    // Filter functionality
    const eventFilter = document.getElementById('event-filter');
    const userTypeFilter = document.getElementById('user-type-filter');
    
    if (eventFilter) {
        eventFilter.addEventListener('change', filterComments);
    }
    
    if (userTypeFilter) {
        userTypeFilter.addEventListener('change', filterComments);
    }
}

function filterComments() {
    const searchTerm = document.getElementById('search-comments')?.value.toLowerCase() || '';
    const eventFilter = document.getElementById('event-filter')?.value || '';
    const userTypeFilter = document.getElementById('user-type-filter')?.value || '';
    
    const commentCards = document.querySelectorAll('.comment-card');
    
    commentCards.forEach(card => {
        const commentText = card.querySelector('.comment-text')?.textContent.toLowerCase() || '';
        const userName = card.querySelector('.comment-author strong')?.textContent.toLowerCase() || '';
        const eventId = card.getAttribute('data-event-id');
        const userType = card.getAttribute('data-user-type');
        
        let showCard = true;
        
        // Search filter
        if (searchTerm && !commentText.includes(searchTerm) && !userName.includes(searchTerm)) {
            showCard = false;
        }
        
        // Event filter
        if (eventFilter && eventId !== eventFilter) {
            showCard = false;
        }
        
        // User type filter
        if (userTypeFilter && userType !== userTypeFilter) {
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