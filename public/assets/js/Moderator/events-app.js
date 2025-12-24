// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/moderator/events/getEvents';

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
    document.getElementById('statusFilter').addEventListener('change', filterEvents);
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
    
    // Show loading spinner
    loadingSpinner.style.display = 'flex';
    noEvents.style.display = 'none';
    
    if (useAjax) {
        // Make AJAX call for filtered/searched events
        const params = new URLSearchParams();
        
        // Add filters
        if (activeFilters.category) params.append('category', activeFilters.category);
        if (activeFilters.university) params.append('university', activeFilters.university);
        if (activeFilters.status) params.append('status', activeFilters.status);
        if (activeFilters.search) params.append('search', activeFilters.search);
        
        params.append('page', currentPage);
        params.append('limit', eventsPerPage);
        
        fetch(`${apiEndpoint}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    filteredEvents = data.events;
                    displayEvents(data.events);
                    updatePagination(data.pagination);
                } else {
                    console.error('Failed to fetch events:', data.error);
                    displayNoEvents();
                }
                loadingSpinner.style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                displayNoEvents();
                loadingSpinner.style.display = 'none';
            });
    } else {
        // Use initial server data
        setTimeout(() => {
            loadingSpinner.style.display = 'none';
            
            if (filteredEvents.length === 0) {
                displayNoEvents();
                return;
            }
            
            displayEvents(filteredEvents);
            updatePagination();
        }, 500);
    }
}

function displayEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadMoreSection = document.getElementById('loadMoreSection');
    
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
    const loadMoreSection = document.getElementById('loadMoreSection');
    
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

// Create event card HTML
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    
    // Handle different field names from database vs JavaScript
    const universityName = event.university_name || event.universityName;
    const maxParticipants = event.max_participants || event.maxParticipants;
    const imageUrl = event.image_url || event.image;
    
    card.innerHTML = `
        <div class="event-image">
            ${imageUrl ? 
                `<img src="${imageUrl}" alt="${event.title}">` : 
                `<svg class="placeholder-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>`
            }
            <div class="event-category">${capitalizeFirstLetter(event.category)}</div>
            <div class="event-status ${event.status}">${event.status}</div>
        </div>
        <div class="event-content" onclick="viewEventDetails(${event.id})" style="cursor: pointer;">
            <h3 class="event-title">${event.title}</h3>
            <p class="event-description">${event.description}</p>
            <div class="event-meta">
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${formatDate(event.date)} at ${event.time}</span>
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
            <div class="event-footer">
                <div class="event-organizer">
                    Organized by ${event.organizer}
                </div>
                <div class="event-participants">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>${event.participants}/${maxParticipants}</span>
                </div>
            </div>
        </div>
        <div class="event-actions">
            <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); showHideEventModal(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                <i class="fas fa-eye-slash"></i> Hide Event
            </button>
            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); viewEventDetails(${event.id})">
                <i class="fas fa-eye"></i> View Details
            </button>
        </div>
    `;
    
    return card;
}

// Search events
function searchEvents() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    activeFilters.search = searchTerm;
    currentPage = 1;
    loadEvents(true);
}

// Filter events
function filterEvents() {
    // Get filter values
    activeFilters.category = document.getElementById('categoryFilter').value;
    activeFilters.university = document.getElementById('universityFilter').value;
    activeFilters.status = document.getElementById('statusFilter').value;
    
    currentPage = 1;
    loadEvents(true);
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('universityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    activeFilters = {};
    currentPage = 1;
    
    // Reset to initial server data
    filteredEvents = window.serverData?.events || [];
    loadEvents(false);
}

// Load more events
function loadMoreEvents() {
    currentPage++;
    loadEvents(true);
}

// View event details - redirect to event view page
function viewEventDetails(eventId) {
    // Redirect to event view page using MVC routing
    window.location.href = `/unipulse/public/publisher/eventview?id=${eventId}`;
}

// Utility functions
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Add some animation effects
function addScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all event cards
    document.querySelectorAll('.event-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
}

// Call animation function after events are loaded
setTimeout(addScrollAnimations, 600);

// Show message function
function showMessage(message, type = 'info') {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.innerHTML = `
        <div class="message-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        max-width: 400px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, 4000);
}
// Show hide event modal
function showHideEventModal(eventId, eventTitle) {
    // Create modal HTML
    const modalHTML = `
        <div id="hideEventModal" class="modal-overlay" onclick="closeHideEventModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2>Hide Event</h2>
                    <button class="modal-close" onclick="closeHideEventModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Event:</strong> ${eventTitle}</p>
                    <p class="warning-text">
                        <i class="fas fa-exclamation-triangle"></i>
                        This action will hide the event from the website. The publisher will be notified with your reason. You can moderate any event regardless of university.
                    </p>
                    <div class="form-group">
                        <label for="hideReason">Reason for hiding this event: <span class="required">*</span></label>
                        <textarea 
                            id="hideReason" 
                            rows="5" 
                            placeholder="Please provide a detailed reason for hiding this event. This will be sent to the publisher."
                            required
                        ></textarea>
                        <small class="char-count"><span id="charCount">0</span>/500 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeHideEventModal()">Cancel</button>
                    <button class="btn btn-danger" onclick="confirmHideEvent(${eventId})">
                        <i class="fas fa-eye-slash"></i> Hide Event
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add character counter
    const textarea = document.getElementById('hideReason');
    const charCount = document.getElementById('charCount');
    textarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        if (count > 500) {
            this.value = this.value.substring(0, 500);
            charCount.textContent = 500;
        }
    });
    
    // Focus textarea
    setTimeout(() => textarea.focus(), 100);
}

// Close hide event modal
function closeHideEventModal(event) {
    if (event && event.target.className !== 'modal-overlay') {
        return;
    }
    const modal = document.getElementById('hideEventModal');
    if (modal) {
        modal.remove();
    }
}

// Confirm hide event
async function confirmHideEvent(eventId) {
    const reason = document.getElementById('hideReason').value.trim();
    
    if (!reason) {
        showMessage('Please provide a reason for hiding this event', 'error');
        return;
    }
    
    if (reason.length < 10) {
        showMessage('Reason must be at least 10 characters long', 'error');
        return;
    }
    
    // Disable button to prevent double submission
    const hideButton = document.querySelector('.modal-footer .btn-danger');
    hideButton.disabled = true;
    hideButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hiding...';
    
    try {
        const response = await fetch('/unipulse/public/moderator/events/hideEvent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                event_id: eventId,
                reason: reason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message || 'Event hidden successfully. Publisher has been notified.', 'success');
            closeHideEventModal();
            
            // Reload events after 1 second
            setTimeout(() => {
                loadEvents(true);
            }, 1000);
        } else {
            showMessage(data.error || 'Failed to hide event', 'error');
            hideButton.disabled = false;
            hideButton.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Event';
        }
    } catch (error) {
        console.error('Error hiding event:', error);
        showMessage('An error occurred while hiding the event', 'error');
        hideButton.disabled = false;
        hideButton.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Event';
    }
}
