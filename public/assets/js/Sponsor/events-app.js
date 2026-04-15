// Sponsor Events App - JavaScript for sponsor's events page

// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let sponsorshipEvents = window.serverData?.sponsorshipEvents || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = window.serverData?.eventsPerPage || 12;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/sponsor/events/getEvents';

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    console.log('Sponsor Events Page Loaded');
    console.log('window.serverData:', window.serverData);
    console.log('allEvents count:', allEvents.length);
    console.log('sponsorshipEvents count:', sponsorshipEvents.length);
    console.log('apiEndpoint:', apiEndpoint);
    loadEvents();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
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
function loadEvents(useAjax = false) {
    console.log('loadEvents called, useAjax:', useAjax);
    console.log('filteredEvents.length:', filteredEvents.length);
    
    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');

    if (!eventsGrid) {
        console.error('eventsGrid element not found');
        return;
    }

    // Show loading
    if (loadingSpinner) loadingSpinner.style.display = 'flex';
    if (noEvents) noEvents.style.display = 'none';

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
                console.log('Received events data:', data);
                
                if (data.success) {
                    allEvents = data.events || [];
                    filteredEvents = [...allEvents];
                    totalPages = data.totalPages || 1;
                    currentPage = data.currentPage || 1;
                    
                    displayEvents(filteredEvents);
                } else {
                    console.error('Error loading events:', data.message);
                    if (loadingSpinner) loadingSpinner.style.display = 'none';
                    if (noEvents) {
                        noEvents.style.display = 'block';
                        noEvents.textContent = data.message || 'Failed to load events';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                if (loadingSpinner) loadingSpinner.style.display = 'none';
                if (noEvents) {
                    noEvents.style.display = 'block';
                    noEvents.textContent = 'Failed to load events. Please try again.';
                }
            });
    } else {
        // Use existing data
        displayEvents(filteredEvents);
    }
}

// Display events in grid
function displayEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');

    // Hide loading
    if (loadingSpinner) loadingSpinner.style.display = 'none';

    // Clear existing content
    if (!eventsGrid) {
        console.error('eventsGrid element not found');
        return;
    }

    if (!events || events.length === 0) {
        if (noEvents) noEvents.style.display = 'block';
        eventsGrid.innerHTML = '';
        return;
    }

    if (noEvents) noEvents.style.display = 'none';

    // Render event cards
    eventsGrid.innerHTML = '';
    events.forEach(event => {
        const eventCard = createEventCard(event);
        eventsGrid.appendChild(eventCard);
    });

    console.log(`Displayed ${events.length} events`);
    
    // Update pagination if needed
    updatePagination();
}

// Create event card HTML
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => window.location.href = `/unipulse/public/sponsor/events/event/${event.id}`;
    card.style.cursor = 'pointer';

    // Handle different field names from database
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    const universityName = event.university_name || event.universityName;
    const coverImage = event.cover_image || event.image_url || event.image;
    const facultyDepartment = event.faculty_department || event.facultyDepartment;
    const exactLocation = event.location || event.exact_location;
    const locationType = event.location_type || 'inside-university';
    const venueName = event.venue_name || event.venueName;
    const city = event.city;

    // Build location display
    let locationDisplay = '';
    if (locationType === 'outside-university') {
        locationDisplay = venueName && city ? `${venueName}, ${city}` : (venueName || city || 'Location TBA');
    } else {
        locationDisplay = exactLocation && universityName ? `${exactLocation}, ${universityName}` : (exactLocation || universityName || 'Location TBA');
    }

    // Build image path
    let imagePath = '';
    if (coverImage) {
        if (coverImage.startsWith('http') || coverImage.startsWith('/')) {
            imagePath = coverImage;
        } else {
            imagePath = `/unipulse/public/${coverImage}`;
        }
    }

    // Calculate event status
    const status = getEventStatus(eventDate, eventTime, event.event_end_time);

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
            <div class="event-status ${status}">${status}</div>
        ${event.postponed_count > 0 ? `<div style="position: absolute; top: 3.5rem; right: 1rem; background: rgba(234, 179, 8, 0.95); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); backdrop-filter: blur(4px);">POSTPONED</div>` : ''}
        </div>
        <div class="event-content">
            <h3 class="event-title">${event.title}</h3>
            <p class="event-description">${truncateText(event.description, 120)}</p>
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
            </div>
            <div class="event-footer">
                <div class="event-organizer">
                    Organized by ${event.organizer_name || event.organizer || 'Unknown'}
                </div>
            </div>
        </div>
    `;

    return card;
}

// Update pagination
function updatePagination() {
    const paginationContainer = document.getElementById('paginationContainer');
    if (!paginationContainer || totalPages <= 1) {
        if (paginationContainer) paginationContainer.innerHTML = '';
        return;
    }

    let paginationHTML = '<div class="pagination">';
    
    // Previous button
    if (currentPage > 1) {
        paginationHTML += `<button onclick="goToPage(${currentPage - 1})" class="pagination-btn">Previous</button>`;
    }
    
    // Page numbers
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        paginationHTML += `<button onclick="goToPage(${i})" class="pagination-btn ${i === currentPage ? 'active' : ''}">${i}</button>`;
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHTML += `<button onclick="goToPage(${currentPage + 1})" class="pagination-btn">Next</button>`;
    }
    
    paginationHTML += '</div>';
    paginationContainer.innerHTML = paginationHTML;
}

// Go to specific page
function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    loadEvents(true);
}

// Search events
function searchEvents() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.trim().toLowerCase();
    activeFilters.search = searchTerm || undefined;
    currentPage = 1;
    console.log('Searching events:', searchTerm);
    loadEvents(true);
}

// Filter events
function filterEvents() {
    const categoryFilter = document.getElementById('categoryFilter');
    const universityFilter = document.getElementById('universityFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Update active filters
    if (categoryFilter) {
        activeFilters.category = categoryFilter.value || undefined;
    }
    if (universityFilter) {
        activeFilters.university = universityFilter.value || undefined;
    }
    if (statusFilter) {
        activeFilters.status = statusFilter.value || undefined;
    }
    
    currentPage = 1;
    console.log('Filtering events:', activeFilters);
    loadEvents(true);
}

// View event details
function viewEventDetails(eventId) {
    window.location.href = `/unipulse/public/sponsor/eventview/${eventId}`;
}

// Handle sponsorship request
function requestSponsorship(eventId) {
    window.location.href = `/unipulse/public/sponsor/eventview/${eventId}#sponsorship`;
}

// Helper functions
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatDate(dateString) {
    if (!dateString) return 'Date TBA';
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

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
