// Boosted events data - will be loaded from PHP
let boostedEvents = [];

// Check if we have data from PHP, otherwise use default data
if (typeof boostedEventsFromDB !== 'undefined' && boostedEventsFromDB.length > 0) {
    // Transform PHP data to match expected format
    boostedEvents = boostedEventsFromDB.map(event => {
        // Parse ticket types to get price
        let priceText = 'Free Entry';
        if (event.ticket_types) {
            try {
                const ticketTypes = JSON.parse(event.ticket_types);
                if (ticketTypes && ticketTypes.length > 0) {
                    const minPrice = Math.min(...ticketTypes.map(t => parseFloat(t.price)));
                    priceText = `From LKR ${minPrice.toLocaleString()}`;
                }
            } catch (e) {
                // If parsing fails, keep default
            }
        }

        // Get image URL - handle both absolute and relative paths
        let imageUrl = event.cover_image || event.image_url;
        
        if (imageUrl) {
            // If it's a relative path (uploaded image), add the full path
            if (imageUrl.startsWith('/uploads/') || imageUrl.startsWith('uploads/')) {
                imageUrl = '/unipulse/public' + (imageUrl.startsWith('/') ? imageUrl : '/' + imageUrl);
            }
            // If it's already an absolute URL (http/https), use as is
        } else {
            // Fallback to placeholder image
            imageUrl = `https://images.unsplash.com/photo-${Math.floor(Math.random() * 10000000)}-${Math.floor(Math.random() * 10000000)}?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80`;
        }

        return {
            id: event.id,
            title: event.title,
            description: event.description || 'Join us for an amazing event experience!',
            category: event.category || 'Event',
            date: event.event_date,
            time: event.event_time,
            location: event.location || event.university_name,
            university: event.university_name,
            price: priceText,
            participants: event.current_participants || 0,
            maxParticipants: event.max_participants || 100,
            organizer: event.organizer_name || event.organizer || 'Event Organizer',
            publisherId: event.publisher_id,
            createdByType: event.created_by_type,
            image: imageUrl,
            isBoosted: true
        };
    });
}
// If no boosted events from DB, boostedEvents remains empty array and placeholder will show

// Upcoming events (loaded from backend)
let upcomingEvents = [];

let moreEvents = [];


// Global variables for carousel control
let currentSlide = 0;
let slideInterval;
let progressInterval;
const slideDuration = 6000; // 6 seconds per slide

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    createHeroCarousel();
    loadUpcomingEvents();
    loadMoreEvents();
    setupEventListeners();
    if (boostedEvents.length > 0) {
        startAutoSlide();
    }
});

// Create hero carousel
function createHeroCarousel() {
    const carousel = document.getElementById('heroCarousel');
    const indicators = document.getElementById('heroIndicators');
<<<<<<< HEAD
    const controls = document.querySelector('.hero-controls');
    const progressBar = document.querySelector('.hero-progress');
    
    carousel.innerHTML = '';
    indicators.innerHTML = '';
    
    const promoBanner = document.getElementById('boostPromoBanner');
    
    // Check if there are no boosted events
    if (boostedEvents.length === 0) {
        // Show promotional banner and hide carousel elements
        if (promoBanner) promoBanner.style.display = 'flex';
        carousel.style.display = 'none';
        if (controls) controls.style.display = 'none';
        if (indicators) indicators.style.display = 'none';
        if (progressBar) progressBar.style.display = 'none';
        return;
    }
    
    // Hide promotional banner when boosted events exist
    if (promoBanner) promoBanner.style.display = 'none';
    carousel.style.display = 'block';
    
    // Show controls, indicators, and progress bar when events exist
    if (controls) controls.style.display = 'flex';
    if (indicators) indicators.style.display = 'flex';
    if (progressBar) progressBar.style.display = 'block';
    
=======

    carousel.innerHTML = '';
    indicators.innerHTML = '';

>>>>>>> 2.9-merge(User_report_generation__&__publisher)
    boostedEvents.forEach((event, index) => {
        // Create slide
        const slide = createHeroSlide(event, index === 0);
        carousel.appendChild(slide);

        // Create indicator
        const indicator = createIndicator(index, index === 0);
        indicators.appendChild(indicator);
    });
}

// Create hero slide
function createHeroSlide(event, isActive) {
    const slide = document.createElement('div');
    slide.className = `hero-slide ${isActive ? 'active' : ''}`;
<<<<<<< HEAD
    
    // Set background image with proper handling
    if (event.image) {
        slide.style.backgroundImage = `url('${event.image}')`;
        slide.style.backgroundSize = 'cover';
        slide.style.backgroundPosition = 'center';
        slide.style.backgroundRepeat = 'no-repeat';
    } else {
        slide.style.background = 'linear-gradient(135deg, #1E3A8A, #F97316)';
    }
    
=======
    slide.style.backgroundImage = event.image ? `url(${event.image})` : 'linear-gradient(135deg, #1E3A8A, #F97316)';

>>>>>>> 2.9-merge(User_report_generation__&__publisher)
    slide.innerHTML = `
        <div class="hero-content">
            <h1 class="hero-event-title">${event.title}</h1>
            <p class="hero-event-description">${event.description}</p>
            <div class="hero-event-meta">
                <div class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${formatDate(event.date)}</span>
                </div>
                <div class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>${event.time}</span>
                </div>
                <div class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${event.location}</span>
                </div>
                <div class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>${event.university}</span>
                </div>
            </div>
            <div class="hero-event-actions">
                <a href="/unipulse/public/user/events/details/${event.id}" class="hero-btn hero-btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    View Event
                </a>
<<<<<<< HEAD
                <a href="${getOrganizerProfileUrl(event)}" class="hero-btn hero-btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    View Organizer
=======
                <a href="/unipulse/public/user/eventview?id=${event.id}" class="hero-btn hero-btn-secondary">
                    Learn More
>>>>>>> 2.9-merge(User_report_generation__&__publisher)
                </a>
            </div>
        </div>
    `;

    return slide;
}

// Create indicator
function createIndicator(index, isActive) {
    const indicator = document.createElement('div');
    indicator.className = `hero-indicator ${isActive ? 'active' : ''}`;
    indicator.onclick = () => goToSlide(index);
    return indicator;
}

// Start auto slide
function startAutoSlide() {
    startProgressBar();

    slideInterval = setInterval(() => {
        nextSlide();
    }, slideDuration);
}

// Stop auto slide
function stopAutoSlide() {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
    if (progressInterval) {
        clearInterval(progressInterval);
    }
}

// Start progress bar
function startProgressBar() {
    const progressBar = document.getElementById('progressBar');
    let progress = 0;

    if (progressInterval) {
        clearInterval(progressInterval);
    }

    progressInterval = setInterval(() => {
        progress += (100 / (slideDuration / 100));
        progressBar.style.width = `${progress}%`;

        if (progress >= 100) {
            progress = 0;
            progressBar.style.width = '0%';
        }
    }, 100);
}

// Next slide
function nextSlide() {
    const slides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.hero-indicator');

    slides[currentSlide].classList.remove('active');
    indicators[currentSlide].classList.remove('active');

    currentSlide = (currentSlide + 1) % slides.length;

    slides[currentSlide].classList.add('active');
    indicators[currentSlide].classList.add('active');

    startProgressBar();
}

// Previous slide
function previousSlide() {
    stopAutoSlide();

    const slides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.hero-indicator');

    slides[currentSlide].classList.remove('active');
    indicators[currentSlide].classList.remove('active');

    currentSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;

    slides[currentSlide].classList.add('active');
    indicators[currentSlide].classList.add('active');

    startAutoSlide();
}

// Go to specific slide
function goToSlide(index) {
    stopAutoSlide();

    const slides = document.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.hero-indicator');

    slides[currentSlide].classList.remove('active');
    indicators[currentSlide].classList.remove('active');

    currentSlide = index;

    slides[currentSlide].classList.add('active');
    indicators[currentSlide].classList.add('active');

    startAutoSlide();
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchEvents();
        }
    });

    // Pause carousel on hover
    const heroSection = document.querySelector('.hero-section');
    heroSection.addEventListener('mouseenter', () => {
        stopAutoSlide();
    });

    heroSection.addEventListener('mouseleave', () => {
        startAutoSlide();
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    heroSection.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    heroSection.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                previousSlide();
            }
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            previousSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // Add scroll animations
    setupScrollAnimations();
}

// Load upcoming events (next 24 hours)
function loadUpcomingEvents() {
    const grid = document.getElementById('upcomingEventsGrid');
    if (!grid) return;

    grid.innerHTML = '<div class="loading">Loading events...</div>';

    fetch('/unipulse/public/user/events/getEvents?limit=100')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load events');
            }
            return response.json();
        })
        .then(data => {
            const events = data.events || data || [];
            const now = new Date();
            const today = new Date(now);
            today.setHours(0, 0, 0, 0);
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            upcomingEvents = events.filter(event => {
                const eventDateStr = event.event_date || event.date;
                if (!eventDateStr) return false;
                const eventDate = new Date(eventDateStr);
                eventDate.setHours(0, 0, 0, 0);
                // Show events that are today or tomorrow (next 24 hours)
                return eventDate >= today && eventDate <= tomorrow;
            }).map(event => normalizeEvent(event)).slice(0, 3);

            grid.innerHTML = '';

            if (upcomingEvents.length === 0) {
                grid.innerHTML = '<div class="no-data">No events in the next 24 hours</div>';
                return;
            }

            upcomingEvents.forEach(event => {
                const eventCard = createEventCard(event);
                grid.appendChild(eventCard);
            });
        })
        .catch(error => {
            console.error('Error loading upcoming events:', error);
            grid.innerHTML = '<div class="no-data">Failed to load events</div>';
        });
}

// Normalize API event to landing page shape
function normalizeEvent(event) {
    const coverImage = event.cover_image || event.image_url || event.image || null;
    const image = coverImage
        ? (coverImage.startsWith('http') || coverImage.startsWith('/') ? coverImage : `/unipulse/public/${coverImage}`)
        : null;

    return {
        id: event.id,
        title: event.title,
        description: event.description || '',
        category: event.category || 'Event',
        date: event.event_date || event.date,
        time: event.event_time || event.time || '00:00',
        location: event.location || event.exact_location || event.university_name || 'Location TBA',
        university: event.university_name || '',
        price: event.price || 'Free',
        image,
        ticket_type: event.ticket_type,
        ticket_types: event.ticket_types,
        location_type: event.location_type,
        venue_name: event.venue_name,
        city: event.city
    };
}

// Load more events (upcoming + free)
function loadMoreEvents() {
    const grid = document.getElementById('moreEventsGrid');
    if (!grid) return;

    grid.innerHTML = '<div class="loading">Loading events...</div>';

    fetch('/unipulse/public/user/events/getEvents?limit=100')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load events');
            }
            return response.json();
        })
        .then(data => {
            const events = data.events || data || [];
            const now = new Date();
            const today = new Date(now);
            today.setHours(0, 0, 0, 0);
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            moreEvents = events.filter(event => {
                // Check if event is upcoming (future only, tomorrow or later)
                const eventDateStr = event.event_date || event.date;
                if (!eventDateStr) return false;
                const eventDate = new Date(eventDateStr);
                eventDate.setHours(0, 0, 0, 0);

                // Must be tomorrow or later (skip today and all past events)
                if (eventDate <= today) {
                    return false;
                }

                // Check if event is free - simple check
                if (event.ticket_type === 'free-all') {
                    return true;
                }

                return false;
            }).filter(event => {
                // Exclude events that are already shown in upcoming24h
                return !upcomingEvents.some(upEvent => upEvent.id === event.id);
            }).map(event => normalizeEvent(event)).slice(0, 3);

            grid.innerHTML = '';

            if (moreEvents.length === 0) {
                grid.innerHTML = '<div class="no-data">No free upcoming events</div>';
                return;
            }

            moreEvents.forEach(event => {
                const eventCard = createEventCard(event);
                grid.appendChild(eventCard);
            });
        })
        .catch(error => {
            console.error('Error loading more events:', error);
            grid.innerHTML = '<div class="no-data">Failed to load events</div>';
        });
}

// Create event card for upcoming/more events
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';

    // Event image or gradient
    const imageDiv = document.createElement('div');
    imageDiv.className = 'event-image';
    if (event.image) {
        const img = document.createElement('img');
        img.src = event.image;
        img.alt = event.title;
        imageDiv.appendChild(img);
    } else {
        const gradientDiv = document.createElement('div');
        gradientDiv.className = 'event-gradient';
        gradientDiv.textContent = event.title.charAt(0);
        imageDiv.appendChild(gradientDiv);
    }

    // Category badge
    const categoryDiv = document.createElement('div');
    categoryDiv.className = 'event-category';
    categoryDiv.textContent = event.category;
    imageDiv.appendChild(categoryDiv);

    // Price badge - Use ticket information
    const priceDiv = document.createElement('div');
    priceDiv.className = 'event-price';
    priceDiv.innerHTML = getTicketPriceBadge(event);
    imageDiv.appendChild(priceDiv);

    card.appendChild(imageDiv);

    // Event content
    const contentDiv = document.createElement('div');
    contentDiv.className = 'event-content';

    const titleDiv = document.createElement('div');
    titleDiv.className = 'event-title';
    titleDiv.textContent = event.title;
    contentDiv.appendChild(titleDiv);

    const metaDiv = document.createElement('div');
    metaDiv.className = 'event-meta';

    // Date
    const dateDiv = document.createElement('div');
    dateDiv.className = 'event-date';
    dateDiv.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> <span>${formatDate(event.date)} at ${event.time}</span>`;
    metaDiv.appendChild(dateDiv);

    // University
    const universityDiv = document.createElement('div');
    universityDiv.className = 'event-location';
    const universityName = event.university || 'University TBA';
    universityDiv.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> <span><strong>University:</strong> ${universityName}</span>`;
    metaDiv.appendChild(universityDiv);

    // Faculty/Department (if available)
    const facultyDepartment = event.faculty_department || event.facultyDepartment;
    if (facultyDepartment) {
        const facultyDiv = document.createElement('div');
        facultyDiv.className = 'event-location';
        facultyDiv.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> <span><strong>Faculty/Department:</strong> ${facultyDepartment}</span>`;
        metaDiv.appendChild(facultyDiv);
    }

    contentDiv.appendChild(metaDiv);
    card.appendChild(contentDiv);

    // Card click: go to event details
    card.onclick = () => {
        window.location.href = `/unipulse/public/user/eventview?id=${event.id}`;
    };

    return card;
}

// Get ticket price badge for event card
function getTicketPriceBadge(event) {
    const ticketType = event.ticket_type || event.ticketType || 'free-all';

    if (ticketType === 'free-all') {
        return 'Free';
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
                    return `LKR ${minPrice}`;
                } else {
                    return `LKR ${minPrice} - ${maxPrice}`;
                }
            }
        }
    }

    // Fallback for paid events
    if (ticketType === 'paid-all') {
        return 'Paid';
    } else if (ticketType === 'mixed') {
        return 'Mixed';
    }

    return event.price || 'Free';
}

// Format date utility
function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString(undefined, options);
}

// Search events
function searchEvents() {
    const query = document.querySelector('.search-input').value.toLowerCase();
    const location = document.querySelector('.location-select').value;

    // Combine all events
    const allEvents = [...boostedEvents, ...upcomingEvents, ...moreEvents];
    let filtered = allEvents.filter(event => {
        const matchesQuery = event.title.toLowerCase().includes(query) || (event.category && event.category.toLowerCase().includes(query));
        const matchesLocation = location === 'All Universities' || (event.university && event.university === location) || (event.location && event.location.includes(location));
        return matchesQuery && matchesLocation;
    });

    // Show results in upcomingEventsGrid and moreEventsGrid
    const upcomingGrid = document.getElementById('upcomingEventsGrid');
    const moreGrid = document.getElementById('moreEventsGrid');
    upcomingGrid.innerHTML = '';
    moreGrid.innerHTML = '';

    filtered.slice(0, 3).forEach(event => {
        upcomingGrid.appendChild(createEventCard(event));
    });
    filtered.slice(3).forEach(event => {
        moreGrid.appendChild(createEventCard(event));
    });
}

// Filter by category
function filterByCategory(category) {
    const allEvents = [...boostedEvents, ...upcomingEvents, ...moreEvents];
    const filtered = allEvents.filter(event => event.category && event.category.toLowerCase() === category.toLowerCase());

    const upcomingGrid = document.getElementById('upcomingEventsGrid');
    const moreGrid = document.getElementById('moreEventsGrid');
    upcomingGrid.innerHTML = '';
    moreGrid.innerHTML = '';

    filtered.slice(0, 3).forEach(event => {
        upcomingGrid.appendChild(createEventCard(event));
    });
    filtered.slice(3).forEach(event => {
        moreGrid.appendChild(createEventCard(event));
    });
}

// Scroll animations (simple fade-in)
function setupScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
<<<<<<< HEAD
        } else {
            // Inside university: show exact location
            locationText = event.location || 'Location TBA';
        }
        
        const locationDiv = document.createElement('div');
        locationDiv.className = 'event-location';
        locationDiv.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> <span>${locationText}</span>`;
        metaDiv.appendChild(locationDiv);

        contentDiv.appendChild(metaDiv);
        card.appendChild(contentDiv);

        // Card click: go to event details
        card.onclick = () => {
            window.location.href = `event-details.html?id=${event.id}`;
        };

        return card;
    }

    // Get ticket price badge for event card
    function getTicketPriceBadge(event) {
        const ticketType = event.ticket_type || event.ticketType || 'free-all';
        
        if (ticketType === 'free-all') {
            return 'Free';
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
                        return `LKR ${minPrice}`;
                    } else {
                        return `LKR ${minPrice} - ${maxPrice}`;
                    }
                }
            }
        }
        
        // Fallback for paid events
        if (ticketType === 'paid-all') {
            return 'Paid';
        } else if (ticketType === 'mixed') {
            return 'Mixed';
        }
        
        return event.price || 'Free';
    }

    // Format date utility
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString(undefined, options);
    }
    // Get organizer profile URL based on type
    function getOrganizerProfileUrl(event) {
        if (event.publisherId && event.createdByType === 'publisher') {
            return `/unipulse/public/publisher/public?id=${event.publisherId}`;
        }
        return '#'; // Fallback if no publisher
    }
    // Search events
    function searchEvents() {
        const query = document.querySelector('.search-input').value.toLowerCase();
        const location = document.querySelector('.location-select').value;

        // Combine all events
        const allEvents = [...boostedEvents, ...upcomingEvents, ...moreEvents];
        let filtered = allEvents.filter(event => {
            const matchesQuery = event.title.toLowerCase().includes(query) || (event.category && event.category.toLowerCase().includes(query));
            const matchesLocation = location === 'All Universities' || (event.university && event.university === location) || (event.location && event.location.includes(location));
            return matchesQuery && matchesLocation;
=======
>>>>>>> 2.9-merge(User_report_generation__&__publisher)
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.event-card, .category-card').forEach(el => {
        observer.observe(el);
    });
}
