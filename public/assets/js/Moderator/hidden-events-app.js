// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/moderator/events/getHiddenEvents';
let currentRequestId = 0;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    setupEventsListeners();
});

// Setup event listeners
function setupEventsListeners() {
    // Search input listener
    document.getElementById('searchInput').addEventListener('input', debounce(searchEvents, 300));
    
    // Filter change listeners
    document.getElementById('categoryFilter').addEventListener('change', filterEvents);
    document.getElementById('universityFilter').addEventListener('change', filterEvents);
}

// Debounce function for search
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

// Load events
function loadEvents(useAjax = false) {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');
    const requestId = ++currentRequestId;
    
    // Show loading spinner
    loadingSpinner.style.display = 'flex';
    noEvents.style.display = 'none';
    
    if (useAjax) {
        // Make AJAX call for filtered/searched events
        const params = new URLSearchParams();
        
        // Add filters
        if (activeFilters.category) params.append('category', activeFilters.category);
        if (activeFilters.university) params.append('university', activeFilters.university);
        if (activeFilters.search) params.append('search', activeFilters.search);
        
        params.append('page', currentPage);
        params.append('limit', eventsPerPage);
        
        fetch(`${apiEndpoint}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (requestId !== currentRequestId) {
                    return;
                }

                if (data.success) {
                    filteredEvents = data.events;

                    if (Array.isArray(data.events) && data.events.length > 0) {
                        displayEvents(data.events);
                        updatePagination(data.pagination);
                    } else {
                        displayNoEvents();
                    }
                } else {
                    console.error('Failed to fetch events:', data.error);
                    displayNoEvents();
                }
                loadingSpinner.style.display = 'none';
            })
            .catch(error => {
                if (requestId !== currentRequestId) {
                    return;
                }

                console.error('Error fetching events:', error);
                displayNoEvents();
                loadingSpinner.style.display = 'none';
            });
    } else {
        // Use initial server data
        loadingSpinner.style.display = 'none';

        if (!Array.isArray(filteredEvents) || filteredEvents.length === 0) {
            displayNoEvents();
            return;
        }

        displayEvents(filteredEvents);
        updatePagination();
    }
}

function displayEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');

    noEvents.style.display = 'none';
    
    // Clear existing events if it's a new search/filter
    if (currentPage === 1) {
        eventsGrid.innerHTML = '';
    }
    
    // Add events
    events.forEach(event => {
        eventsGrid.appendChild(createEventCard(event));
    });
    
    // Show/hide load more button
    if (events.length < eventsPerPage) {
        loadMoreSection.style.display = 'none';
    } else {
        loadMoreSection.style.display = 'block';
    }
}

function displayNoEvents() {
    const noEvents = document.getElementById('noEvents');
    const eventsGrid = document.getElementById('eventsGrid');
    const loadMoreSection = document.getElementById('loadMoreSection');

    if (eventsGrid) {
        eventsGrid.innerHTML = '';
    }
    
    noEvents.style.display = 'block';
    loadMoreSection.style.display = 'none';
}

function updatePagination(pagination = null) {
    const loadMoreSection = document.getElementById('loadMoreSection');
    
    if (pagination) {
        // Use server pagination data
        if (!pagination.hasMore) {
            loadMoreSection.style.display = 'none';
        } else {
            loadMoreSection.style.display = 'block';
        }
    } else {
        // Use local pagination logic
        const endIndex = currentPage * eventsPerPage;
        if (endIndex >= totalPages * eventsPerPage) {
            loadMoreSection.style.display = 'none';
        } else {
            loadMoreSection.style.display = 'block';
        }
    }
}

// Create event card HTML for hidden events
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card hidden-event-card';
    
    // Handle different field names from database vs JavaScript
    const universityName = event.university_name || event.universityName;
    const imageUrl = resolveEventImageUrl(event);
    const deletedAt = event.deleted_at || event.deletedAt;
    const deletionReason = event.deletion_reason || event.deletionReason;
    const moderatorName = event.moderator_name || event.moderatorName || 'Moderator';
    
    card.innerHTML = `
        <div class="event-image">
            ${imageUrl ? 
                `<img src="${imageUrl}" alt="${event.title}" onerror="this.style.display='none'; const icon=this.parentNode.querySelector('.placeholder-icon'); if(icon){icon.style.display='block';}">` : 
                `<svg class="placeholder-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>`
            }
            ${imageUrl ? `<svg class="placeholder-icon" style="display:none;" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>` : ''}
            <div class="event-category">${capitalizeFirstLetter(event.category)}</div>
            <div class="event-status hidden-badge">Hidden</div>
        ${event.postponed_count > 0 ? `<div style="position: absolute; top: 3.5rem; right: 1rem; background: rgba(234, 179, 8, 0.95); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); backdrop-filter: blur(4px);">POSTPONED</div>` : ''}
        </div>
        <div class="event-content">
            <h3 class="event-title">${event.title}</h3>
            <p class="event-description">${event.description}</p>
            
            <!-- Hidden Event Info -->
            <div class="hidden-info">
                <div class="info-row">
                    <i class="fas fa-clock"></i>
                    <span>Hidden on: ${formatDate(deletedAt)}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-user-shield"></i>
                    <span>Hidden by: ${moderatorName}</span>
                </div>
                ${deletionReason ? `
                <div class="info-row reason">
                    <i class="fas fa-info-circle"></i>
                    <span>Reason: ${deletionReason}</span>
                </div>
                ` : ''}
            </div>
            
            <div class="event-meta">
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${formatDate(event.date || event.event_date)} at ${event.time || formatTime(event.event_time)}</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${event.location}</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9,22 9,12 15,12 15,22"></polyline>
                    </svg>
                    <span>${universityName}</span>
                </div>
            </div>
        </div>
        <div class="event-actions">
            <button class="btn btn-primary btn-sm" onclick="viewEventDetails(${event.id})">
                <i class="fas fa-eye"></i> View Details
            </button>
            <button class="btn btn-success btn-sm" onclick="showRepostModal(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                <i class="fas fa-redo"></i> Repost
            </button>
        </div>
    `;
    
    return card;
}

function resolveEventImageUrl(event) {
    const raw = event.cover_image || event.image_url || event.image || '';
    if (!raw) return '';

    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('/')) {
        return raw;
    }

    return `/unipulse/public/${raw}`;
}

// Search events
function searchEvents() {
    const searchValue = document.getElementById('searchInput').value.trim();
    activeFilters.search = searchValue;
    currentPage = 1;
    loadEvents(true);
}

// Filter events
function filterEvents() {
    const category = document.getElementById('categoryFilter').value;
    const university = document.getElementById('universityFilter').value;
    
    activeFilters.category = category;
    activeFilters.university = university;
    currentPage = 1;
    loadEvents(true);
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('universityFilter').value = '';
    
    activeFilters = {};
    currentPage = 1;
    loadEvents(true);
}

// Load more events
function loadMoreEvents() {
    currentPage++;
    loadEvents(true);
}

// Show repost confirmation modal
function showRepostModal(eventId, eventTitle) {
    const modalHTML = `
        <div id="repostEventModal" class="modal-overlay" onclick="closeRepostModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2><i class="fas fa-redo"></i> Repost Event</h2>
                    <button class="modal-close" onclick="closeRepostModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="event-name"><strong>Event:</strong> ${eventTitle}</p>
                    <div class="warning-box">
                        <i class="fas fa-info-circle"></i>
                        <p>This will restore the event and make it visible to all users again. The publisher will be notified about the restoration.</p>
                    </div>
                    <p>Are you sure you want to repost this event?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeRepostModal()">Cancel</button>
                    <button class="btn btn-success" onclick="confirmRepost(${eventId})">
                        <i class="fas fa-redo"></i> Repost Event
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Close repost modal
function closeRepostModal(event) {
    if (event && event.target.className !== 'modal-overlay') {
        return;
    }
    const modal = document.getElementById('repostEventModal');
    if (modal) {
        modal.remove();
    }
}

// Confirm repost event
async function confirmRepost(eventId) {
    // Disable button to prevent double submission
    const repostButton = document.querySelector('.modal-footer .btn-success');
    repostButton.disabled = true;
    repostButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reposting...';
    
    try {
        const response = await fetch('/unipulse/public/moderator/events/restoreEvent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                event_id: eventId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message || 'Event has been reposted successfully!', 'success');
            closeRepostModal();
            
            // Reload events after 1 second
            setTimeout(() => {
                loadEvents(true);
            }, 1000);
        } else {
            showMessage(data.error || 'Failed to repost event', 'error');
            repostButton.disabled = false;
            repostButton.innerHTML = '<i class="fas fa-redo"></i> Repost Event';
        }
    } catch (error) {
        console.error('Error reposting event:', error);
        showMessage('An error occurred while reposting the event', 'error');
        repostButton.disabled = false;
        repostButton.innerHTML = '<i class="fas fa-redo"></i> Repost Event';
    }
}

// View event details (redirect to event details page)
function viewEventDetails(eventId) {
    window.location.href = `/unipulse/public/moderator/eventview/${eventId}`;
}

// Show message notification
function showMessage(message, type = 'info') {
    // Remove any existing messages
    const existingMessage = document.querySelector('.message-notification');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-notification ${type}`;
    messageDiv.innerHTML = `
        <div class="message-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="message-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentElement) {
            messageDiv.classList.add('fade-out');
            setTimeout(() => messageDiv.remove(), 300);
        }
    }, 5000);
}

// Utility function to capitalize first letter
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Format time
function formatTime(timeString) {
    if (!timeString) return 'N/A';
    
    // If already formatted, return as is
    if (timeString.includes('AM') || timeString.includes('PM')) {
        return timeString;
    }
    
    // Parse time string (assuming HH:MM:SS format)
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    
    return `${displayHour}:${minutes} ${ampm}`;
}
