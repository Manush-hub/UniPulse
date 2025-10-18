// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/admin/allevents/getEvents';

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
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
    card.onclick = () => viewEventDetails(event.id);
    card.style.cursor = 'pointer';
    
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
        <div class="event-content">
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
            <div class="event-actions">
                <button type="button" class="hide-btn" onclick="event.stopPropagation(); event.preventDefault(); hideEvent(${event.id}); return false;" title="Hide Event">
                    <i class="fas fa-eye-slash"></i> Hide
                </button>
                <button type="button" class="delete-btn" onclick="event.stopPropagation(); event.preventDefault(); deleteEvent(${event.id}); return false;" title="Delete Event">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
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
    // Redirect to admin event view page using MVC routing
    window.location.href = `/unipulse/public/admin/eventview?id=${eventId}`;
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

// Hide event function for admin
function hideEvent(eventId) {
    if (confirm('Are you sure you want to hide this event? The publisher will be notified.')) {
        // Show loading state
        const hideBtn = document.querySelector(`button[onclick*="hideEvent(${eventId})"]`);
        if (hideBtn) {
            hideBtn.disabled = true;
            hideBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hiding...';
        }

        fetch('/unipulse/public/admin/hideevent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: eventId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage('Event hidden successfully! Publisher has been notified.', 'success');
                
                // Update the event card to show hidden status
                const eventCard = hideBtn.closest('.event-card');
                if (eventCard) {
                    eventCard.style.opacity = '0.6';
                    eventCard.classList.add('hidden-event');
                    const statusElement = eventCard.querySelector('.event-status');
                    if (statusElement) {
                        statusElement.textContent = 'Hidden';
                        statusElement.className = 'event-status hidden';
                    }
                }
                
                // Reset button
                if (hideBtn) {
                    hideBtn.disabled = false;
                    hideBtn.innerHTML = '<i class="fas fa-eye"></i> Show';
                    hideBtn.onclick = () => { event.stopPropagation(); event.preventDefault(); showEvent(eventId); return false; };
                    hideBtn.title = 'Show Event';
                    hideBtn.className = 'show-btn';
                }
            } else {
                // Handle authentication errors
                if (data.redirect) {
                    showMessage('Please log in as an admin to hide events.', 'error');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    const errorMessage = data.errors?.general || data.message || 'Failed to hide event';
                    throw new Error(errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Hide error:', error);
            showMessage('Error hiding event: ' + error.message, 'error');
            
            // Reset button state
            if (hideBtn) {
                hideBtn.disabled = false;
                hideBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
            }
        });
    }
}

// Show event function for admin
function showEvent(eventId) {
    if (confirm('Are you sure you want to show this event again?')) {
        // Show loading state
        const showBtn = document.querySelector(`button[onclick*="showEvent(${eventId})"]`);
        if (showBtn) {
            showBtn.disabled = true;
            showBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Showing...';
        }

        fetch('/unipulse/public/admin/showevent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: eventId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage('Event is now visible again!', 'success');
                
                // Update the event card to show visible status
                const eventCard = showBtn.closest('.event-card');
                if (eventCard) {
                    eventCard.style.opacity = '1';
                    eventCard.classList.remove('hidden-event');
                    const statusElement = eventCard.querySelector('.event-status');
                    if (statusElement) {
                        statusElement.textContent = 'Active';
                        statusElement.className = 'event-status active';
                    }
                }
                
                // Reset button
                if (showBtn) {
                    showBtn.disabled = false;
                    showBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
                    showBtn.onclick = () => { event.stopPropagation(); event.preventDefault(); hideEvent(eventId); return false; };
                    showBtn.title = 'Hide Event';
                    showBtn.className = 'hide-btn';
                }
            } else {
                const errorMessage = data.errors?.general || data.message || 'Failed to show event';
                throw new Error(errorMessage);
            }
        })
        .catch(error => {
            console.error('Show error:', error);
            showMessage('Error showing event: ' + error.message, 'error');
            
            // Reset button state
            if (showBtn) {
                showBtn.disabled = false;
                showBtn.innerHTML = '<i class="fas fa-eye"></i> Show';
            }
        });
    }
}

// Delete event function
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        // Show loading state
        const deleteBtn = document.querySelector(`button[onclick*="deleteEvent(${eventId})"]`);
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        }

        fetch('/unipulse/public/admin/deleteevent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: eventId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage('Event deleted successfully!', 'success');
                
                // Remove the event from the UI
                const eventCard = deleteBtn.closest('.event-card');
                if (eventCard) {
                    eventCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    eventCard.style.opacity = '0';
                    eventCard.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        eventCard.remove();
                        // Update events array
                        allEvents = allEvents.filter(event => event.id !== eventId);
                        filteredEvents = filteredEvents.filter(event => event.id !== eventId);
                        // Check if we need to show "no events" message
                        if (filteredEvents.length === 0) {
                            document.getElementById('noEvents').style.display = 'block';
                        }
                    }, 300);
                }
            } else {
                // Handle authentication errors
                if (data.redirect) {
                    showMessage('Please log in as an admin to delete events.', 'error');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    const errorMessage = data.errors?.general || data.message || 'Failed to delete event';
                    throw new Error(errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('Error deleting event: ' + error.message, 'error');
            
            // Reset button state
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
            }
        });
    }
}

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