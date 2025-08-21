// Boosted events data with detailed information
const boostedEvents = [
    {
        id: 1,
        title: 'Tech Innovation Summit 2025',
        description: 'Join the biggest technology conference in Sri Lanka featuring AI, blockchain, and emerging technologies. Network with industry leaders and showcase your innovations.',
        category: 'Technology',
        date: '2025-09-15',
        time: '09:00 AM',
        location: 'University of Moratuwa',
        university: 'University of Moratuwa',
        price: 'From LKR 2,500',
        participants: 450,
        maxParticipants: 500,
        organizer: 'IEEE Student Branch',
        image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
        isBoosted: true
    },
    {
        id: 2,
        title: 'Cultural Heritage Festival',
        description: 'Celebrate the rich cultural diversity of Sri Lanka with traditional dances, music, art exhibitions, and culinary experiences from all provinces.',
        category: 'Cultural',
        date: '2025-09-20',
        time: '06:00 PM',
        location: 'University of Colombo Arts Theatre',
        university: 'University of Colombo',
        price: 'Free Entry',
        participants: 320,
        maxParticipants: 400,
        organizer: 'Cultural Society',
        image: 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
        isBoosted: true
    },
    {
        id: 3,
        title: 'Inter-University Sports Championship',
        description: 'The ultimate sports showdown between top universities. Compete in cricket, football, basketball, and more. Show your university spirit!',
        category: 'Sports',
        date: '2025-08-25',
        time: '08:00 AM',
        location: 'University of Peradeniya Sports Complex',
        university: 'University of Peradeniya',
        price: 'From LKR 1,000',
        participants: 280,
        maxParticipants: 600,
        organizer: 'Sports Council',
        image: 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
        isBoosted: true
    },
    {
        id: 4,
        title: 'AI & Machine Learning Workshop Series',
        description: 'Comprehensive hands-on workshops covering the latest in artificial intelligence and machine learning. From beginners to advanced practitioners.',
        category: 'Workshop',
        date: '2025-08-22',
        time: '02:00 PM',
        location: 'University of Moratuwa IT Faculty',
        university: 'University of Moratuwa',
        price: 'From LKR 3,000',
        participants: 85,
        maxParticipants: 100,
        organizer: 'Computer Science Department',
        image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
        isBoosted: true
    },
    {
        id: 5,
        title: 'Entrepreneurship & Innovation Expo',
        description: 'Showcase your startup ideas, connect with investors, and learn from successful entrepreneurs. The future of business starts here.',
        category: 'Business',
        date: '2025-08-28',
        time: '09:00 AM',
        location: 'University of Sri Jayewardenepura',
        university: 'University of Sri Jayewardenepura',
        price: 'From LKR 1,500',
        participants: 180,
        maxParticipants: 250,
        organizer: 'Entrepreneurship Club',
        image: 'https://images.unsplash.com/photo-1556761175-4b46a572b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
        isBoosted: true
    }
];

// Other events data
const upcomingEvents = [
    {
        id: 6,
        title: 'Photography Workshop',
        category: 'Workshop',
        date: '2025-08-30',
        time: '10:00 AM',
        location: 'University of Kelaniya',
        price: 'From LKR 500',
        image: null
    },
    {
        id: 7,
        title: 'Music Festival 2025',
        category: 'Cultural',
        date: '2025-09-05',
        time: '07:00 PM',
        location: 'University of Ruhuna',
        price: 'From LKR 2,000',
        image: null
    }
];

const moreEvents = [
    {
        id: 8,
        title: 'Research Symposium',
        category: 'Academic',
        date: '2025-09-12',
        time: '08:30 AM',
        location: 'University of Peradeniya',
        price: 'From LKR 1,200',
        image: null
    },
    {
        id: 9,
        title: 'Environmental Awareness Campaign',
        category: 'Social',
        date: '2025-09-18',
        time: '10:00 AM',
        location: 'University of Kelaniya',
        price: 'Free Entry',
        image: null
    },
    {
        id: 10,
        title: 'Gaming Tournament',
        category: 'Sports',
        date: '2025-09-25',
        time: '02:00 PM',
        location: 'University of Colombo',
        price: 'From LKR 800',
        image: null
    }
];

// Global variables for carousel control
let currentSlide = 0;
let slideInterval;
let progressInterval;
const slideDuration = 6000; // 6 seconds per slide

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    createHeroCarousel();
    loadUpcomingEvents();
    loadMoreEvents();
    setupEventListeners();
    startAutoSlide();
});

// Create hero carousel
function createHeroCarousel() {
    const carousel = document.getElementById('heroCarousel');
    const indicators = document.getElementById('heroIndicators');
    
    carousel.innerHTML = '';
    indicators.innerHTML = '';
    
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
    slide.style.backgroundImage = event.image ? `url(${event.image})` : 'linear-gradient(135deg, #1E3A8A, #F97316)';
    
    slide.innerHTML = `
        <div class="hero-content">
            <div class="hero-event-category">${event.category}</div>
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
                    <span>${formatDate(event.date)} at ${event.time}</span>
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
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>${event.participants}/${event.maxParticipants} participants</span>
                </div>
            </div>
            <div class="hero-event-actions">
                <a href="event-details.html?id=${event.id}" class="hero-btn hero-btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    Join Event - ${event.price}
                </a>
                <a href="event-details.html?id=${event.id}" class="hero-btn hero-btn-secondary">
                    Learn More
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
    searchInput.addEventListener('keypress', function(e) {
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

// Load upcoming events
function loadUpcomingEvents() {
    const grid = document.getElementById('upcomingEventsGrid');
    grid.innerHTML = '';
    
    upcomingEvents.forEach(event => {
        const eventCard = createEventCard(event);
        grid.appendChild(eventCard);
    });
}

// Load more events
function loadMoreEvents() {
    const grid = document.getElementById('moreEventsGrid');
    grid.innerHTML = '';
    
    moreEvents.forEach(event => {
        const eventCard = createEventCard(event);
        grid.appendChild(eventCard);
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

        // Price badge
        const priceDiv = document.createElement('div');
        priceDiv.className = 'event-price';
        priceDiv.textContent = event.price;
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

        // Location
        const locationDiv = document.createElement('div');
        locationDiv.className = 'event-location';
        locationDiv.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> <span>${event.location}</span>`;
        metaDiv.appendChild(locationDiv);

        contentDiv.appendChild(metaDiv);
        card.appendChild(contentDiv);

        // Card click: go to event details
        card.onclick = () => {
            window.location.href = `event-details.html?id=${event.id}`;
        };

        return card;
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
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.event-card, .category-card').forEach(el => {
            observer.observe(el);
        });
    }

    // Dummy notification toggle
    function toggleNotifications() {
        alert('Notifications feature coming soon!');
    }