// Initialize with server data or empty array
let allEvents = window.serverData?.events || [];
let filteredEvents = [...allEvents];
let currentPage = window.serverData?.currentPage || 1;
let totalPages = window.serverData?.totalPages || 1;
let activeFilters = window.serverData?.filters || {};
const eventsPerPage = 6;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/events/getEvents';

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
                    Organized by ${event.organizer}
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