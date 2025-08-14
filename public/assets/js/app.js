// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initMobileMenu();
    initScrollAnimations();
    initSmoothScrolling();
    initStatCounter();
    initEventSlider();
    initCategoryFilters();
    initFormValidation();
    initScrollToTop();
});

// Mobile Menu Toggle
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const body = document.body;
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navMenu.classList.contains('active')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = 'auto';
            }
        });
        
        // Close menu when clicking on nav links
        const navLinks = document.querySelectorAll('.nav-menu a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                body.style.overflow = 'auto';
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                body.style.overflow = 'auto';
            }
        });
        
        // Close menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                body.style.overflow = 'auto';
            }
        });
    }
}

// Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    // Add fade-in class to elements and observe them
    const animateElements = document.querySelectorAll('.feature-card, .user-type-card, .event-card, .category-card, .stat-item');
    animateElements.forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });
}

// Smooth Scrolling for Navigation Links
function initSmoothScrolling() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetSection.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Animated Counter for Statistics
function initStatCounter() {
    const statNumbers = document.querySelectorAll('.stat-number');
    let counted = false;
    
    const countObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !counted) {
                counted = true;
                animateCounters();
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.statistics');
    if (statsSection) {
        countObserver.observe(statsSection);
    }
    
    function animateCounters() {
        statNumbers.forEach(stat => {
            const target = parseInt(stat.textContent.replace(/[^\d]/g, ''));
            const suffix = stat.textContent.replace(/[\d]/g, '');
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(current) + suffix;
            }, 20);
        });
    }
}

// Event Slider/Carousel
function initEventSlider() {
    const dots = document.querySelectorAll('.dot');
    const eventsGrid = document.querySelector('.events-grid');
    
    if (dots.length > 0 && eventsGrid) {
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                // Remove active class from all dots
                dots.forEach(d => d.classList.remove('active'));
                // Add active class to clicked dot
                this.classList.add('active');
                
                // Here you could implement actual sliding logic
                // For now, we'll just change the active dot
                console.log(`Switched to slide ${index + 1}`);
            });
        });
    }
}

// Category Filter (if implementing filtering)
function initCategoryFilters() {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.querySelector('h3').textContent;
            
            // Remove active class from all categories
            categoryCards.forEach(c => c.classList.remove('active'));
            // Add active class to clicked category
            this.classList.add('active');
            
            // Here you would filter events by category
            console.log(`Filtering by category: ${category}`);
            
            // Example: You could show/hide events based on category
            filterEventsByCategory(category);
        });
    });
}

// Filter events by category
function filterEventsByCategory(category) {
    const eventCards = document.querySelectorAll('.event-card');
    
    eventCards.forEach(card => {
        // This is a basic example - you'd need to add data attributes to events
        // or implement a more sophisticated filtering system
        if (category === 'Academic') {
            card.style.display = 'block';
        } else {
            // Show all events for now
            card.style.display = 'block';
        }
    });
}

// Form Validation (for future contact forms)
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Basic validation
            if (validateForm(data)) {
                console.log('Form is valid:', data);
                // Here you would send the data to your PHP backend
                submitForm(data);
            } else {
                console.log('Form validation failed');
            }
        });
    });
}

// Form validation helper
function validateForm(data) {
    // Add your validation logic here
    const required = ['email', 'name'];
    
    for (let field of required) {
        if (!data[field] || data[field].trim() === '') {
            alert(`${field} is required`);
            return false;
        }
    }
    
    // Email validation
    if (data.email && !isValidEmail(data.email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    return true;
}

// Email validation helper
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Submit form to backend
function submitForm(data) {
    // This would send data to your PHP backend
    fetch('contact.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log('Success:', result);
        alert('Thank you for your message!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Sorry, there was an error sending your message.');
    });
}

// Scroll to Top Button
function initScrollToTop() {
    // Create scroll to top button
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.className = 'scroll-to-top';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #FF6B35;
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(scrollBtn);
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.style.opacity = '1';
            scrollBtn.style.visibility = 'visible';
        } else {
            scrollBtn.style.opacity = '0';
            scrollBtn.style.visibility = 'hidden';
        }
    });
    
    // Scroll to top when clicked
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Button Click Handlers
document.addEventListener('click', function(e) {
    // Handle CTA buttons
    if (e.target.classList.contains('btn-cta') || e.target.closest('.btn-cta')) {
        const button = e.target.classList.contains('btn-cta') ? e.target : e.target.closest('.btn-cta');
        const userType = button.querySelector('.btn-title').textContent;
        
        console.log(`${userType} button clicked`);
        
        // Redirect based on user type
        switch(userType) {
            case 'Students':
                // Redirect to student dashboard/registration
                window.location.href = '#student-signup';
                break;
            case 'Event Organizers':
                // Redirect to organizer dashboard
                window.location.href = '#organizer-signup';
                break;
            case 'Sponsors':
                // Redirect to sponsor information
                window.location.href = '#sponsor-info';
                break;
        }
    }
    
    // Handle feature cards
    if (e.target.closest('.feature-card')) {
        const feature = e.target.closest('.feature-card').querySelector('h3').textContent;
        console.log(`${feature} feature clicked`);
        // You could show more information about the feature
    }
    
    // Handle event cards
    if (e.target.classList.contains('btn-outline')) {
        e.preventDefault();
        const eventCard = e.target.closest('.event-card');
        const eventTitle = eventCard.querySelector('h3').textContent;
        console.log(`View details for: ${eventTitle}`);
        
        // You could open a modal or redirect to event details page
        showEventDetails(eventTitle);
    }
});

// Show event details (placeholder function)
function showEventDetails(eventTitle) {
    // Create a simple modal or redirect to event page
    alert(`Showing details for: ${eventTitle}\n\nThis would normally open a detailed event page or modal.`);
    
    // In a real application, you might:
    // - Open a modal with event details
    // - Redirect to a detailed event page
    // - Load content via AJAX
}

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 100) {
        header.style.background = 'rgba(255, 255, 255, 0.95)';
        header.style.backdropFilter = 'blur(10px)';
    } else {
        header.style.background = '#fff';
        header.style.backdropFilter = 'none';
    }
});

// Add loading animation
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
    
    // Add any loading animations here
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.style.animation = 'fadeInUp 1s ease-out';
    }
});

// Utility Functions
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

// Resize handler
window.addEventListener('resize', debounce(function() {
    // Handle responsive changes
    console.log('Window resized');
}, 250));

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
});

// Console welcome message
console.log('%c🎉 Welcome to UniPulse!', 'color: #FF6B35; font-size: 20px; font-weight: bold;');
console.log('%cBuilt with ❤️ by Manush-hub', 'color: #4A5BCC; font-size: 14px;');