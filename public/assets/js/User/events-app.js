// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/events/getEvents';

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    // Events are already sorted by backend, no need for client-side sorting
    loadEvents();
    setupEventListeners();
    
    // Add event listener for Load More button
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            loadMoreEvents();
            return false;
        });
    }
});

// Setup event listeners
function setupEventListeners() {
    // Search input listener
    const searchInput = document.getElementById('eventNameFilter');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(searchEvents, 300));
    }

    // Filter change listeners
    document.getElementById('categoryFilter').addEventListener('change', filterEvents);
    document.getElementById('universityFilter').addEventListener('change', filterEvents);
    document.getElementById('statusFilter').addEventListener('change', filterEvents);

    // Category container click listeners
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
    updateCategoryCounts(allEvents);
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

// Update category counts for ongoing and upcoming events only
function updateCategoryCounts(events) {
    const categoryCounts = {
        'technology': 0,
        'sports': 0,
        'cultural': 0,
        'academic': 0,
        'social': 0
    };

    // Count events by category for upcoming and ongoing events only
    events.forEach(event => {
        const status = getEventStatus(event.event_date || event.date, event.event_time || event.time, event.event_end_time);

        // Only count ongoing and upcoming events
        if (status === 'upcoming' || status === 'ongoing') {
            const category = (event.category || '').toLowerCase();
            if (categoryCounts.hasOwnProperty(category)) {
                categoryCounts[category]++;
            }
        }
    });

    // Update the display
    const categoriesContainer = document.getElementById('categoriesContainer');
    if (categoriesContainer) {
        categoriesContainer.querySelectorAll('p[data-category]').forEach(categoryItem => {
            const category = categoryItem.getAttribute('data-category');
            const countSpan = categoryItem.querySelector('.category-count');
            if (countSpan) {
                countSpan.textContent = categoryCounts[category] || 0;
            }
        });
    }
}

// Filter events by category from the category header
function filterByCategory(category) {
    // Set the category filter
    document.getElementById('categoryFilter').value = category;

    // Clear other filters
    document.getElementById('universityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('eventNameFilter').value = '';

    activeFilters.category = category;
    activeFilters.university = '';
    activeFilters.status = '';
    activeFilters.eventName = '';

    currentPage = 1;

    // Fetch filtered events
    loadEvents(true);
}

// Fetch all events without filters to get accurate category counts
function fetchAllEventsForCounting() {
    const params = new URLSearchParams();
    params.append('limit', 10000); // Get a large number to count all events

    fetch(`${apiEndpoint}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.events) {
                updateCategoryCounts(data.events);
            }
        })
        .catch(error => {
            console.error('Error fetching events for counting:', error);
        });
}

// Debounce function for search

// Load events
function loadEvents(useAjax = false, append = false) {
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
        if (activeFilters.eventName) params.append('eventName', activeFilters.eventName);
        if (activeFilters.search) params.append('search', activeFilters.search);

        params.append('page', currentPage);
        params.append('limit', eventsPerPage);

        fetch(`${apiEndpoint}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (append) {
                        filteredEvents = [...filteredEvents, ...data.events];
                    } else {
                        filteredEvents = data.events;
                    }
                    displayEvents(data.events, append);
                    updatePagination(data.pagination);
                    // Update category counts based on all events (for ongoing/upcoming only)
                    // Get all events without filters to count categories
                    if (!append) {
                        fetchAllEventsForCounting();
                    }
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
            updateCategoryCounts(allEvents);
        }, 500);
    }
}

function displayEvents(events, append = false) {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadMoreSection = document.getElementById('loadMoreSection');

    // Clear existing events if it's a new search/filter (not appending)
    if (!append) {
        eventsGrid.innerHTML = '';
    }

    // Events are already sorted by backend, no need for client-side sorting
    // Just display them in the order received
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

// Sort events by status: ongoing first, then upcoming, then completed
function sortEventsByStatus(events) {
    return events.sort((a, b) => {
        const statusA = getEventStatus(a.event_date || a.date, a.event_time || a.time, a.event_end_time);
        const statusB = getEventStatus(b.event_date || b.date, b.event_time || b.time, b.event_end_time);

        const statusOrder = { 'ongoing': 0, 'upcoming': 1, 'completed': 2 };
        return (statusOrder[statusA] || 3) - (statusOrder[statusB] || 3);
    });
}

function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => viewEventDetails(event.id);

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
        if (coverImage.startsWith('http')) {
            imagePath = coverImage;
        } else {
            imagePath = `/unipulse/public/${coverImage}`;
        }
    }

    // Determine actual status for display from event date and time
    const displayStatus = getEventStatus(eventDate, eventTime, event.event_end_time);

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
            <div class="event-status ${displayStatus}">${displayStatus}</div>
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
                    Organized by ${event.organizer_name || event.organizer}
                </div>
                <div class="event-footer-right">
                    ${getTicketPriceDisplay(event)}
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
        </div>
    `;

    return card;
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
    const searchInput = document.getElementById('eventNameFilter');
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
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
    activeFilters.eventName = document.getElementById('eventNameFilter').value;

    currentPage = 1;

    // Always use AJAX to fetch filtered events from backend
    loadEvents(true);
}

// Clear all filters
function clearFilters() {
    document.getElementById('eventNameFilter').value = '';
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
    // Redirect to event view page using MVC routing
    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
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