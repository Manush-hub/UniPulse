// Sample events data (replace with actual API calls)
let allEvents = [
    {
        id: 1,
        title: "Tech Conference 2025",
        description: "Annual technology conference featuring latest innovations in AI, blockchain, and cloud computing. Join industry experts and students for networking and learning.",
        category: "technology",
        university: "university-of-moratuwa",
        universityName: "University of Moratuwa",
        status: "upcoming",
        date: "2025-09-15",
        time: "09:00 AM",
        location: "Main Auditorium, UOM",
        organizer: "Computer Society",
        participants: 250,
        maxParticipants: 300,
        image: null
    },
    {
        id: 2,
        title: "Inter-University Cricket Championship",
        description: "Annual cricket tournament between top universities in Sri Lanka. Support your university team in this exciting championship.",
        category: "sports",
        university: "university-of-peradeniya",
        universityName: "University of Peradeniya",
        status: "ongoing",
        date: "2025-08-25",
        time: "08:00 AM",
        location: "University Cricket Ground",
        organizer: "Sports Club",
        participants: 150,
        maxParticipants: 500,
        image: null
    },
    {
        id: 3,
        title: "Cultural Night 2025",
        description: "Celebrate diverse cultures with traditional dances, music performances, and cultural exhibitions from different communities.",
        category: "cultural",
        university: "university-of-colombo",
        universityName: "University of Colombo",
        status: "upcoming",
        date: "2025-09-20",
        time: "06:00 PM",
        location: "Arts Theatre",
        organizer: "Cultural Society",
        participants: 180,
        maxParticipants: 400,
        image: null
    },
    {
        id: 4,
        title: "Academic Research Symposium",
        description: "Present your research findings and learn from peer researchers across various academic disciplines.",
        category: "academic",
        university: "university-of-kelaniya",
        universityName: "University of Kelaniya",
        status: "upcoming",
        date: "2025-09-10",
        time: "09:30 AM",
        location: "Research Center",
        organizer: "Graduate School",
        participants: 120,
        maxParticipants: 200,
        image: null
    },
    {
        id: 5,
        title: "Community Service Day",
        description: "Join hands to make a difference in local communities through various volunteer activities and social initiatives.",
        category: "social",
        university: "university-of-sri-jayewardenepura",
        universityName: "University of Sri Jayewardenepura",
        status: "completed",
        date: "2025-08-15",
        time: "07:00 AM",
        location: "Campus & Local Communities",
        organizer: "Volunteer Club",
        participants: 200,
        maxParticipants: 200,
        image: null
    },
    {
        id: 6,
        title: "AI & Machine Learning Workshop",
        description: "Hands-on workshop covering fundamentals of artificial intelligence and machine learning with practical coding sessions.",
        category: "workshop",
        university: "university-of-moratuwa",
        universityName: "University of Moratuwa",
        status: "upcoming",
        date: "2025-09-05",
        time: "02:00 PM",
        location: "Computer Lab 1",
        organizer: "IEEE Student Branch",
        participants: 45,
        maxParticipants: 50,
        image: null
    }
];

let filteredEvents = [...allEvents];
let currentPage = 1;
const eventsPerPage = 6;

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
function loadEvents() {
    const eventsGrid = document.getElementById('eventsGrid');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noEvents = document.getElementById('noEvents');
    const loadMoreSection = document.getElementById('loadMoreSection');
    
    // Show loading spinner
    loadingSpinner.style.display = 'flex';
    noEvents.style.display = 'none';
    
    // Simulate API delay
    setTimeout(() => {
        loadingSpinner.style.display = 'none';
        
        if (filteredEvents.length === 0) {
            noEvents.style.display = 'block';
            loadMoreSection.style.display = 'none';
            return;
        }
        
        // Calculate events to show
        const startIndex = (currentPage - 1) * eventsPerPage;
        const endIndex = startIndex + eventsPerPage;
        const eventsToShow = filteredEvents.slice(0, endIndex);
        
        // Clear existing events if it's a new search/filter
        if (currentPage === 1) {
            eventsGrid.innerHTML = '';
        }
        
        // Add new events
        const newEvents = filteredEvents.slice(startIndex, endIndex);
        newEvents.forEach(event => {
            eventsGrid.appendChild(createEventCard(event));
        });
        
        // Show/hide load more button
        if (endIndex >= filteredEvents.length) {
            loadMoreSection.style.display = 'none';
        } else {
            loadMoreSection.style.display = 'block';
        }
    }, 500);
}

// Create event card HTML
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => viewEventDetails(event.id);
    
    card.innerHTML = `
        <div class="event-image">
            ${event.image ? 
                `<img src="${event.image}" alt="${event.title}">` : 
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
                    <span>${event.universityName}</span>
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
                    <span>${event.participants}/${event.maxParticipants}</span>
                </div>
            </div>
        </div>
    `;
    
    return card;
}

// Search events
function searchEvents() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    
    if (searchTerm === '') {
        filteredEvents = [...allEvents];
    } else {
        filteredEvents = allEvents.filter(event => 
            event.title.toLowerCase().includes(searchTerm) ||
            event.description.toLowerCase().includes(searchTerm) ||
            event.universityName.toLowerCase().includes(searchTerm) ||
            event.organizer.toLowerCase().includes(searchTerm) ||
            event.location.toLowerCase().includes(searchTerm)
        );
    }
    
    applyFilters();
    currentPage = 1;
    loadEvents();
}

// Filter events
function filterEvents() {
    applyFilters();
    currentPage = 1;
    loadEvents();
}

// Apply all active filters
function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const universityFilter = document.getElementById('universityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    
    filteredEvents = allEvents.filter(event => {
        const matchesSearch = searchTerm === '' || 
            event.title.toLowerCase().includes(searchTerm) ||
            event.description.toLowerCase().includes(searchTerm) ||
            event.universityName.toLowerCase().includes(searchTerm) ||
            event.organizer.toLowerCase().includes(searchTerm) ||
            event.location.toLowerCase().includes(searchTerm);
            
        const matchesCategory = categoryFilter === '' || event.category === categoryFilter;
        const matchesUniversity = universityFilter === '' || event.university === universityFilter;
        const matchesStatus = statusFilter === '' || event.status === statusFilter;
        
        return matchesSearch && matchesCategory && matchesUniversity && matchesStatus;
    });
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('universityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    filteredEvents = [...allEvents];
    currentPage = 1;
    loadEvents();
}

// Load more events
function loadMoreEvents() {
    currentPage++;
    loadEvents();
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