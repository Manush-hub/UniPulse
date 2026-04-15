// Browse Events App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initializeFilters();
    initializeEventActions();
    initializeSearch();
});

/**
 * Initialize filter functionality
 */
function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const universityFilter = document.getElementById('universityFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
    
    if (universityFilter) {
        universityFilter.addEventListener('change', applyFilters);
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    
    if (searchInput) {
        // Search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', applyFilters);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch();
            }
        });
    }

    // Filter functionality
    if (categoryFilter) {
        categoryFilter.addEventListener('change', handleFilters);
    }

    if (universityFilter) {
        universityFilter.addEventListener('change', handleFilters);
    }

    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }

    // Sponsor buttons
    sponsorBtns.forEach(btn => {
        btn.addEventListener('click', handleSponsorClick);
    });

    // View details buttons
    viewDetailsBtns.forEach(btn => {
        btn.addEventListener('click', handleViewDetails);
    });
}

function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        const searchValue = searchInput.value.trim();
        updateURL({ search: searchValue });
    }
}

function handleFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const universityFilter = document.getElementById('universityFilter');
    
    const filters = {};
    
    if (categoryFilter && categoryFilter.value) {
        filters.category = categoryFilter.value;
    }
    
    if (universityFilter && universityFilter.value) {
        filters.university = universityFilter.value;
    }
    
    // Keep existing search value
    const searchInput = document.getElementById('searchInput');
    if (searchInput && searchInput.value.trim()) {
        filters.search = searchInput.value.trim();
    }
    
    updateURL(filters);
}

function clearAllFilters() {
    // Clear all form inputs
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const universityFilter = document.getElementById('universityFilter');
    
    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (universityFilter) universityFilter.value = '';
    
    // Redirect to clean URL
    window.location.href = window.location.pathname;
}

function updateURL(filters) {
    const url = new URL(window.location);
    
    // Clear existing parameters
    url.searchParams.delete('search');
    url.searchParams.delete('category');
    url.searchParams.delete('university');
    url.searchParams.delete('page'); // Reset to first page when filtering
    
    // Add new parameters
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            url.searchParams.set(key, filters[key]);
        }
    });
    
    // Navigate to new URL
    window.location.href = url.toString();
}

function handleSponsorClick(event) {
    const eventId = event.currentTarget.getAttribute('data-event-id');
    
    if (!eventId) {
        showNotification('Error: Unable to identify event', 'error');
        return;
    }
    
    // Show confirmation dialog
    const confirmed = confirm('Are you interested in sponsoring this event? This will initiate contact with the event organizer.');
    
    if (confirmed) {
        // Here you would typically send a request to express interest
        // For now, we'll redirect to a contact/messaging system
        window.location.href = `/unipulse/public/sponsor/messages?action=compose&event_id=${eventId}`;
    }
}

function handleViewDetails(event) {
    const eventId = event.currentTarget.getAttribute('data-event-id');
    
    if (!eventId) {
        showNotification('Error: Unable to identify event', 'error');
        return;
    }
    
    // Redirect to event details page
    window.location.href = `/unipulse/public/sponsor/events/view/${eventId}`;
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#dc2626' : type === 'success' ? '#059669' : '#0ea5e9'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Handle close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.parentElement.removeChild(notification);
            }
        }, 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.parentElement.removeChild(notification);
                }
            }, 300);
        }
    }, 5000);
}

// Add necessary CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }
    
    .notification-close:hover {
        opacity: 0.8;
    }
`;
document.head.appendChild(style);