// Role-specific configuration
const roleConfig = {
    User: {
        apiEndpoint: '/unipulse/public/user/events/getEvents',
        searchInputId: 'eventNameFilter',
        eventDetailsUrl: '/unipulse/public/user/eventview/',
        showCategoryHeader: true,
        showHideButton: false
    },
    Publisher: {
        apiEndpoint: '/unipulse/public/publisher/events/getEvents',
        searchInputId: 'searchInput',
        eventDetailsUrl: '/unipulse/public/publisher/eventview/',
        showCategoryHeader: false,
        showHideButton: false
    },
    Sponsor: {
        apiEndpoint: '/unipulse/public/sponsor/events/getEvents',
        searchInputId: 'searchInput',
        eventDetailsUrl: '/unipulse/public/sponsor/eventview/',
        showCategoryHeader: false,
        showHideButton: false
    },
    Moderator: {
        apiEndpoint: '/unipulse/public/moderator/events/getEvents',
        searchInputId: 'searchInput',
        eventDetailsUrl: '/unipulse/public/moderator/eventview/',
        showCategoryHeader: false,
        showHideButton: true
    },
    Admin: {
        apiEndpoint: '/unipulse/public/admin/allevents/getEvents',
        searchInputId: 'searchInput',
        eventDetailsUrl: '/unipulse/public/admin/eventview/',
        showCategoryHeader: false,
        showHideButton: true
    }
};

// Get current role configuration
const currentRole = typeof userRole !== 'undefined' ? userRole : 'User';
const config = roleConfig[currentRole] || roleConfig.User;

// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = window.serverData?.eventsPerPage || 12;
const apiEndpoint = window.serverData?.apiEndpoint || config.apiEndpoint;

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    console.log('Events page loaded. Role:', currentRole);
    console.log('Server data:', window.serverData);
    console.log('Initial events count:', allEvents.length);
    loadEvents();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search input listener - use role-specific ID
    const searchInput = document.getElementById(config.searchInputId);
    if (searchInput) {
        searchInput.addEventListener('input', debounce(searchEvents, 300));
    }

    // Filter change listeners
    document.getElementById('categoryFilter').addEventListener('change', filterEvents);
    document.getElementById('universityFilter').addEventListener('change', filterEvents);
    document.getElementById('statusFilter').addEventListener('change', filterEvents);

    // Category container click listeners (User only)
    if (config.showCategoryHeader) {
        const categoriesContainer = document.getElementById('categoriesContainer');
        if (categoriesContainer) {
            categoriesContainer.querySelectorAll('p[data-category]').forEach(categoryItem => {
                categoryItem.addEventListener('click', function () {
                    const category = this.getAttribute('data-category');
                    filterByCategory(category);
                });
            });
        }
        // Update category counts initially
        // updateCategoryCounts(allEvents);
    }
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

// Update category counts for ongoing and upcoming events only (User role)
// function updateCategoryCounts(events) {
//     if (!config.showCategoryHeader) return;
    
//     const categoryCounts = {
//         'technology': 0,
//         'sports': 0,
//         'cultural': 0,
//         'academic': 0,
//         'social': 0
//     };

//     events.forEach(event => {
//         const status = getEventStatus(event.event_date || event.date);
//         if (status === 'upcoming' || status === 'ongoing' || status === 'completed') {
//             const category = (event.category || '').toLowerCase();
//             if (categoryCounts.hasOwnProperty(category)) {
//                 categoryCounts[category]++;
//             }
//         }
//     });

//     const categoriesContainer = document.getElementById('categoriesContainer');
//     if (categoriesContainer) {
//         categoriesContainer.querySelectorAll('p[data-category]').forEach(categoryItem => {
//             const category = categoryItem.getAttribute('data-category');
//             const countSpan = categoryItem.querySelector('.category-count');
//             if (countSpan) {
//                 countSpan.textContent = categoryCounts[category] || 0;
//             }
//         });
//     }
// }

// Filter events by category from the category header
function filterByCategory(category) {
    document.getElementById('categoryFilter').value = category;
    document.getElementById('universityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    const searchInput = document.getElementById(config.searchInputId);
    if (searchInput) searchInput.value = '';
    
    filterEvents();
}

// Fetch all events without filters to get accurate category counts
function fetchAllEventsForCounting() {
    if (!config.showCategoryHeader) return;
    
    fetch(apiEndpoint)
        .then(response => response.json())
        // .then(data => {
        //     if (data.success && data.events) {
        //         updateCategoryCounts(data.events);
        //     }
        // })
        .catch(error => console.error('Error fetching events for counting:', error));
}

// Load events
function loadEvents(useAjax = false) {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');

    loadingSpinner.style.display = 'flex';
    noEvents.style.display = 'none';

    if (!useAjax && allEvents.length > 0) {
        displayEvents(allEvents);
        loadingSpinner.style.display = 'none';
        updatePagination();
        if (config.showCategoryHeader) {
            fetchAllEventsForCounting();
        }
        return;
    }

    const searchValue = document.getElementById(config.searchInputId)?.value || '';
    const category = document.getElementById('categoryFilter').value;
    const university = document.getElementById('universityFilter').value;
    const status = document.getElementById('statusFilter').value;

    let url = `${apiEndpoint}?page=${currentPage}`;
    if (searchValue) url += `&search=${encodeURIComponent(searchValue)}`;
    if (category) url += `&category=${encodeURIComponent(category)}`;
    if (university) url += `&university=${encodeURIComponent(university)}`;
    if (status) url += `&status=${encodeURIComponent(status)}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            loadingSpinner.style.display = 'none';

            if (data.success && data.events && data.events.length > 0) {
                allEvents = data.events;
                displayEvents(allEvents);
                updatePagination(data.pagination);
            } else {
                displayNoEvents();
            }

            if (loadMoreSection) {
                loadMoreSection.style.display = (currentPage < totalPages) ? 'block' : 'none';
            }
        })
        .catch(error => {
            console.error('Error loading events:', error);
            loadingSpinner.style.display = 'none';
            displayNoEvents();
        });
}

function displayEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');
    eventsGrid.innerHTML = '';

    const sortedEvents = config.showCategoryHeader ? sortEventsByStatus(events) : events;

    sortedEvents.forEach(event => {
        const eventCard = createEventCard(event);
        eventsGrid.appendChild(eventCard);
    });

    if (config.showCategoryHeader) {
        setTimeout(addScrollAnimations, 100);
    }
}

function displayNoEvents() {
    const noEvents = document.getElementById('noEvents');
    const eventsGrid = document.getElementById('eventsGrid');
    eventsGrid.innerHTML = '';
    noEvents.style.display = 'flex';
}

function updatePagination(pagination = null) {
    if (pagination) {
        currentPage = pagination.currentPage || currentPage;
        totalPages = pagination.totalPages || totalPages;
    }

    const loadMoreSection = document.getElementById('loadMoreSection');
    if (loadMoreSection) {
        loadMoreSection.style.display = (currentPage < totalPages) ? 'block' : 'none';
    }
}

// Sort events by status: ongoing first, then upcoming, then completed
function sortEventsByStatus(events) {
    const statusOrder = { 'ongoing': 1, 'upcoming': 2, 'completed': 3 };
    
    return [...events].sort((a, b) => {
        const statusA = getEventStatus(a.event_date || a.date, a.event_time || a.time, a.event_end_time);
        const statusB = getEventStatus(b.event_date || b.date, b.event_time || b.time, b.event_end_time);
        return (statusOrder[statusA] || 999) - (statusOrder[statusB] || 999);
    });
}

// Create event card HTML
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => viewEventDetails(event.id);

    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    const eventStatus = getEventStatus(eventDate, eventTime, event.event_end_time);
    
    // Handle multiple possible image field names and ensure valid path
    let imageUrl = event.featured_image || event.cover_image || event.image_url || '';
    
    // If image URL is relative, ensure it has proper path
    if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
        imageUrl = '/unipulse/public/' + imageUrl;
    }
    
    // Use default if no image
    if (!imageUrl) {
        imageUrl = '/unipulse/public/assets/images/default-event.jpg';
    }

    card.innerHTML = `
        <div class="event-image" style="background-image: url('${imageUrl}'); background-size: cover; background-position: center;">
            <div class="event-category">${capitalizeFirstLetter(event.category || 'Event')}</div>
            <div class="event-status ${eventStatus}">${capitalizeFirstLetter(eventStatus)}</div>
            ${event.is_boosted == 1 ? '<div class="boosted-badge">⭐ Boosted</div>' : ''}
        </div>
        <div class="event-content">
            <h3 class="event-title">${event.title}</h3>
            <div class="event-meta">
                <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${formatDate(eventDate)}</span>
                </div>
                <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>${eventTime || 'TBA'}</span>
                </div>
                <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${event.university_name || event.venue || event.location || 'TBA'}</span>
                </div>
            </div>
            <div class="event-footer">
                <div class="event-organizer">
                    <span>By ${event.organizer_name || event.created_by_name || 'Organizer'}</span>
                </div>
                <div class="event-participants">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>${event.current_participants || 0}/${event.max_participants || 100}</span>
                </div>
            </div>
            ${config.showHideButton ? `
                <div class="event-actions">
                    <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); showHideEventModal(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                        Hide Event
                    </button>
                </div>
            ` : ''}
        </div>
    `;

    return card;
}

// Get ticket price display for event card
function getTicketPriceDisplay(event) {
    const ticketType = event.ticket_type || event.ticketType || 'free-all';
    
    if (ticketType === 'free-all') {
        return 'Free';
    }
    
    const ticketTypes = event.ticket_types || [];
    
    if (ticketTypes && ticketTypes.length > 0) {
        const tickets = typeof ticketTypes === 'string' ? JSON.parse(ticketTypes) : ticketTypes;
        
        if (Array.isArray(tickets) && tickets.length > 0) {
            const prices = tickets.map(t => parseFloat(t.price)).filter(p => !isNaN(p));
            if (prices.length > 0) {
                const minPrice = Math.min(...prices);
                const maxPrice = Math.max(...prices);
                
                if (minPrice === maxPrice) {
                    return `LKR ${minPrice}`;
                } else {
                    return `LKR ${minPrice} - ${maxPrice}`;
                }
            }
        }
    }
    
    if (ticketType === 'paid-all') {
        return 'Paid';
    } else if (ticketType === 'mixed') {
        return 'Mixed';
    }
    
    return event.price || 'Free';
}

// Calculate event status based on event date, start time, and end time
function getEventStatus(eventDate, eventTime, eventEndTime) {
    if (!eventDate) return 'upcoming';

    const now = new Date();
    const y = now.getFullYear();
    const mo = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const todayStr = `${y}-${mo}-${d}`;
    const eventDateStr = String(eventDate).slice(0, 10);

    if (eventDateStr > todayStr) {
        return 'upcoming';
    } else if (eventDateStr < todayStr) {
        return 'completed';
    } else {
        // Same date: compare times
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        const nowTimeStr = `${hh}:${mm}:${ss}`;
        const startTime = eventTime ? String(eventTime).slice(0, 8) : '00:00:00';
        const endTime = eventEndTime ? String(eventEndTime).slice(0, 8) : null;

        if (startTime > nowTimeStr) {
            return 'upcoming';
        } else if (endTime && endTime <= nowTimeStr) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }
}

// Search events
function searchEvents() {
    currentPage = 1;
    loadEvents(true);
}

// Filter events
function filterEvents() {
    currentPage = 1;
    loadEvents(true);
}

// Clear all filters
function clearFilters() {
    const searchInput = document.getElementById(config.searchInputId);
    if (searchInput) searchInput.value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('universityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    currentPage = 1;
    loadEvents(true);
}

// Load more events
function loadMoreEvents() {
    currentPage++;
    loadEvents(true);
}

// View event details - redirect to event view page
function viewEventDetails(eventId) {
    window.location.href = `${config.eventDetailsUrl}?id=${eventId}`;
}

// Utility functions
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function formatDate(dateString) {
    if (!dateString) return 'TBA';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Add some animation effects
function addScrollAnimations() {
    const cards = document.querySelectorAll('.event-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.3s ease';
        observer.observe(card);
    });
}

// Call animation function after events are loaded
setTimeout(addScrollAnimations, 600);

// Show message function (for Moderator/Admin)
function showMessage(message, type = 'info') {
    if (!config.showHideButton) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-toast message-${type}`;
    messageDiv.innerHTML = `
        <div class="message-content">
            <span class="message-icon">
                ${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}
            </span>
            <span class="message-text">${message}</span>
        </div>
    `;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(messageDiv);
        }, 300);
    }, 3000);
}

// Show hide event modal (Moderator/Admin)
function showHideEventModal(eventId, eventTitle) {
    if (!config.showHideButton) return;
    
    const modal = document.createElement('div');
    modal.id = 'hideEventModal';
    modal.className = 'modal show';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Hide Event</h2>
                <button class="close-btn" onclick="closeHideEventModal(event)">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to hide this event?</p>
                <p><strong>${eventTitle}</strong></p>
                <textarea id="hideReason" placeholder="Reason for hiding (optional)" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeHideEventModal(event)">Cancel</button>
                <button class="btn btn-danger" onclick="confirmHideEvent(${eventId})">Hide Event</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close hide event modal
function closeHideEventModal(event) {
    if (event) event.stopPropagation();
    const modal = document.getElementById('hideEventModal');
    if (modal) {
        modal.remove();
    }
}

// Confirm hide event
async function confirmHideEvent(eventId) {
    if (!config.showHideButton) return;
    
    const reason = document.getElementById('hideReason')?.value || '';
    
    try {
        const response = await fetch(`/${currentRole.toLowerCase()}/events/hideEvent`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: eventId,
                reason: reason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('Event hidden successfully', 'success');
            closeHideEventModal();
            loadEvents(true);
        } else {
            showMessage(data.message || 'Failed to hide event', 'error');
        }
    } catch (error) {
        console.error('Error hiding event:', error);
        showMessage('An error occurred. Please try again.', 'error');
    }
}
