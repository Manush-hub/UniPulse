// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/moderator/events/getEvents';

// Add CSS animations for notifications
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
`;
document.head.appendChild(style);

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    console.log('Moderator Events Page Loaded');
    console.log('window.serverData:', window.serverData);
    console.log('allEvents count:', allEvents.length);
    console.log('apiEndpoint:', apiEndpoint);
    loadEvents();
    setupFilterListeners();

    // Add event listener for Load More button
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            loadMoreEvents();
            return false;
        });
    }
});

// Setup filter and search event listeners
function setupFilterListeners() {
    // Search input listener
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(searchEvents, 300));
    }

    // Filter change listeners
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterEvents);
    }

    const universityFilter = document.getElementById('universityFilter');
    if (universityFilter) {
        universityFilter.addEventListener('change', filterEvents);
    }

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', filterEvents);
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

// Load events
function loadEvents(useAjax = false, append = false) {
    console.log('loadEvents called, useAjax:', useAjax, 'append:', append);
    console.log('filteredEvents.length:', filteredEvents.length);

    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');

    // Show loading spinner only if not appending (not Load More)
    if (!append) {
        loadingSpinner.style.display = 'flex';
    }
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

        console.log('Fetching events from:', `${apiEndpoint}?${params.toString()}`);

        fetch(`${apiEndpoint}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                console.log('Fetch response:', data);
                if (data.success) {
                    if (append) {
                        filteredEvents = [...filteredEvents, ...data.events];
                    } else {
                        filteredEvents = data.events;
                    }
                    displayEvents(data.events, append);
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
        console.log('Using server data, events count:', filteredEvents.length);
        setTimeout(() => {
            loadingSpinner.style.display = 'none';

            if (filteredEvents.length === 0) {
                console.log('No events to display');
                displayNoEvents();
                return;
            }

            console.log('Displaying', filteredEvents.length, 'events');
            displayEvents(filteredEvents);
            updatePagination();
        }, 500);
    }
}

function displayEvents(events, append = false) {
    const eventsGrid = document.getElementById('eventsGrid');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');

    if (!eventsGrid) {
        console.error('eventsGrid element not found!');
        return;
    }

    console.log('displayEvents called with', events.length, 'events, append:', append);

    // Clear existing events only if not appending
    if (!append) {
        eventsGrid.innerHTML = '';
    }

    if (!append && (!events || events.length === 0)) {
        displayNoEvents();
        return;
    }

    if (noEvents) {
        noEvents.style.display = 'none';
    }

    // Add events to the grid
    events.forEach(event => {
        const card = createEventCard(event);
        eventsGrid.appendChild(card);
    });

    console.log('Successfully added', events.length, 'cards to grid. Total cards now:', eventsGrid.children.length);

    // Show/hide load more button
    if (loadMoreSection) {
        if (events.length < eventsPerPage) {
            loadMoreSection.style.display = 'none';
        } else {
            loadMoreSection.style.display = 'block';
        }
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

    if (!loadMoreSection) {
        console.warn('loadMoreSection element not found');
        return;
    }

    if (pagination) {
        // Use server pagination data
        console.log('Updating pagination from server:', pagination);
        if (!pagination.hasMore) {
            loadMoreSection.style.display = 'none';
        } else {
            loadMoreSection.style.display = 'block';
        }
    } else {
        // Use local pagination logic with totalPages
        console.log('Updating pagination locally - currentPage:', currentPage, 'totalPages:', totalPages);

        // Check if there are more pages to load
        if (currentPage >= totalPages) {
            console.log('Hiding load more - all pages displayed');
            loadMoreSection.style.display = 'none';
        } else {
            console.log('Showing load more button - more pages available');
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
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    const universityName = event.university_name || event.universityName;
    const maxParticipants = event.max_participants || event.maxParticipants;
    const currentParticipants = event.current_participants || event.currentParticipants || 0;
    const coverImage = event.cover_image || event.image_url || event.image;
    const facultyDepartment = event.faculty_department || event.facultyDepartment;
    const exactLocation = event.location || event.exact_location;
    const locationType = event.location_type || 'inside-university';
    const venueName = event.venue_name || event.venueName;
    const city = event.city;

    // Build location display based on location type
    let locationDisplay = '';
    let secondaryInfo = '';

    if (locationType === 'outside-university') {
        // Outside university: show "Venue, City"
        if (venueName && city) {
            locationDisplay = `${venueName}, ${city}`;
        } else if (venueName) {
            locationDisplay = venueName;
        } else if (city) {
            locationDisplay = city;
        } else {
            locationDisplay = 'Location TBA';
        }
        // No secondary info for outside events
        secondaryInfo = '';
    } else {
        // Inside university: show "Exact Location, University"
        if (exactLocation && universityName) {
            locationDisplay = `${exactLocation}, ${universityName}`;
        } else if (exactLocation) {
            locationDisplay = exactLocation;
        } else if (universityName) {
            locationDisplay = universityName;
        } else {
            locationDisplay = 'Location TBA';
        }
        // Secondary info shows "Faculty, University"
        if (facultyDepartment && universityName) {
            secondaryInfo = `${facultyDepartment}, ${universityName}`;
        } else if (facultyDepartment) {
            secondaryInfo = facultyDepartment;
        } else if (universityName) {
            secondaryInfo = universityName;
        }
    }

    // Build correct image path
    let imagePath = '';
    if (coverImage) {
        console.log('Cover image from DB:', coverImage); // Debug log
        if (coverImage.startsWith('http')) {
            imagePath = coverImage;
        } else if (coverImage.startsWith('/')) {
            // Absolute path from root
            imagePath = coverImage;
        } else {
            // Relative path from database - add /unipulse/public/ prefix
            imagePath = `/unipulse/public/${coverImage}`;
        }
        console.log('Constructed image path:', imagePath); // Debug log
    }

    // Calculate event status based on event date and time
    const calculatedStatus = getEventStatus(eventDate, eventTime, event.event_end_time);

    card.innerHTML = `
        <div class="event-image">
            ${imagePath ?
            `<img src="${imagePath}" alt="${event.title}">` :
            `<svg class="placeholder-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>`
        }
            <div class="event-category">${capitalizeFirstLetter(event.category)}</div>
            <div class="event-status ${calculatedStatus}">${calculatedStatus}</div>
        ${event.postponed_count > 0 ? `<div style="position: absolute; top: 3.5rem; right: 1rem; background: rgba(234, 179, 8, 0.95); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); backdrop-filter: blur(4px);">POSTPONED</div>` : ''}
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
                    <span>${formatDate(eventDate)} at ${eventTime}</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${locationDisplay}</span>
                </div>
                ${secondaryInfo ? `
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9,22 9,12 15,12 15,22"></polyline>
                    </svg>
                    <span>${secondaryInfo}</span>
                </div>
                ` : ''}
            </div>
            <div class="event-footer">
                <div class="event-organizer">
                    Organized by ${event.organizer_name || event.organizer || 'Unknown'}
                </div>
                <div class="event-footer-right">${getTicketPriceDisplay(event)}
                    ${maxParticipants !== null && maxParticipants !== undefined ? `
                    <div class="event-participants">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>${currentParticipants}/${maxParticipants}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
            <div class="event-actions" style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); showHideEventModal(${event.id}, '${event.title.replace(/'/g, "\\'")}')"
                    style="width: 100%; background: #ef4444; color: white; padding: 0.5rem; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-eye-slash"></i> Hide Event
                </button>
            </div>
        </div>
    `;

    return card;
}

// Calculate event status based on event date
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

    // Always use backend filtering so category/university/status combinations work together.
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
    console.log('loadMoreEvents called, currentPage:', currentPage);

    // Disable button and show loading state
    const loadMoreBtn = document.querySelector('#loadMoreSection button');
    if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    }

    currentPage++;
    console.log('Fetching page:', currentPage);
    loadEvents(true, true); // useAjax=true, append=true

    // Re-enable button after loading
    setTimeout(() => {
        if (loadMoreBtn && !loadMoreBtn.disabled) return; // Already re-enabled
        if (loadMoreBtn) {
            loadMoreBtn.disabled = false;
            loadMoreBtn.innerHTML = 'Load More Events';
        }
    }, 2000);
}

// View event details - redirect to event view page
function viewEventDetails(eventId) {
    console.log('viewEventDetails called with ID:', eventId);
    console.log('Redirecting to:', `/unipulse/public/moderator/eventview?id=${eventId}`);
    // Redirect to event view page using MVC routing
    window.location.href = `/unipulse/public/moderator/eventview?id=${eventId}`;
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

// Show hide event modal (Moderator)
function showHideEventModal(eventId, eventTitle) {
    const modal = document.createElement('div');
    modal.id = 'hideEventModal';
    modal.className = 'modal';
    modal.style.cssText = 'display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;';

    modal.innerHTML = `
        <div class="modal-content" style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b;">Hide Event</h2>
                <button class="close-btn" onclick="closeHideEventModal(event)" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
            </div>
            <div class="modal-body" style="margin-bottom: 1.5rem;">
                <p style="margin-bottom: 1rem; color: #475569;">Are you sure you want to hide this event?</p>
                <p style="margin-bottom: 1rem; font-weight: 600; color: #1e293b;">${eventTitle}</p>
                <label style="display: block; margin-bottom: 0.5rem; color: #475569; font-size: 14px;">Reason for hiding (required):</label>
                <textarea id="hideReason" placeholder="Please provide a reason (minimum 10 characters)" rows="4" 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; resize: vertical;"></textarea>
                <small style="display: block; margin-top: 0.5rem; color: #64748b;">The publisher will be notified with this reason.</small>
            </div>
            <div class="modal-footer" style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeHideEventModal(event)" 
                    style="padding: 0.625rem 1.25rem; border: 1px solid #e2e8f0; background: white; color: #475569; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button class="btn btn-danger" onclick="confirmHideEvent(${eventId})" 
                    style="padding: 0.625rem 1.25rem; border: none; background: #ef4444; color: white; border-radius: 6px; cursor: pointer; font-size: 14px;">Hide Event</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close modal on background click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeHideEventModal();
        }
    });
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
    const reasonInput = document.getElementById('hideReason');
    const reason = reasonInput?.value.trim() || '';

    // Validate reason
    if (!reason) {
        alert('Please provide a reason for hiding this event.');
        reasonInput?.focus();
        return;
    }

    if (reason.length < 10) {
        alert('Reason must be at least 10 characters long.');
        reasonInput?.focus();
        return;
    }

    try {
        const response = await fetch('/unipulse/public/moderator/events/hideEvent', {
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
            showMessage('Event hidden successfully. Publisher has been notified.', 'success');
            closeHideEventModal();
            // Reload events after short delay
            setTimeout(() => {
                loadEvents(true);
            }, 1000);
        } else {
            showMessage(data.error || 'Failed to hide event', 'error');
        }
    } catch (error) {
        console.error('Error hiding event:', error);
        showMessage('An error occurred. Please try again.', 'error');
    }
}

// Show message notification
function showMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-notification ${type}`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 7rem;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        max-width: 400px;
    `;
    messageDiv.textContent = message;

    document.body.appendChild(messageDiv);

    // Remove after 5 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => messageDiv.remove(), 300);
    }, 5000);
}

// Get ticket price display for event card
function getTicketPriceDisplay(event) {
    const ticketType = event.ticket_type || event.ticketType || 'free-all';

    if (ticketType === 'free-all') {
        return `<div class="event-price" style="color: #10B981; font-weight: 600; font-size: 14px;">
            <i class="fas fa-ticket-alt"></i> Free
        </div>`;
    }

    // For paid or mixed events, show ticket prices
    const ticketTypes = event.ticket_types || [];

    if (ticketTypes && ticketTypes.length > 0) {
        // Parse if it's a JSON string
        const tickets = typeof ticketTypes === 'string' ? JSON.parse(ticketTypes) : ticketTypes;

        if (Array.isArray(tickets) && tickets.length > 0) {
            // Get price range
            const prices = tickets.map(t => parseFloat(t.price)).filter(p => !isNaN(p));
            if (prices.length > 0) {
                const minPrice = Math.min(...prices);
                const maxPrice = Math.max(...prices);

                if (minPrice === maxPrice) {
                    return `<div class="event-price" style="color: #3b82f6; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-ticket-alt"></i> LKR ${minPrice}
                    </div>`;
                } else {
                    return `<div class="event-price" style="color: #3b82f6; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-ticket-alt"></i> LKR ${minPrice} - ${maxPrice}
                    </div>`;
                }
            }
        }
    }

    // Fallback for paid events without detailed ticket info
    if (ticketType === 'paid-all') {
        return `<div class="event-price" style="color: #3b82f6; font-weight: 600; font-size: 14px;">
            <i class="fas fa-ticket-alt"></i> Paid
        </div>`;
    } else if (ticketType === 'mixed') {
        return `<div class="event-price" style="color: #f59e0b; font-weight: 600; font-size: 14px;">
            <i class="fas fa-ticket-alt"></i> Mixed
        </div>`;
    }

    return '';
}
