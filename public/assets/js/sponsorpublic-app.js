class SponsorPublicProfile {
    constructor() {
        this.sponsorData = {
            name: 'Tech Innovation Corp',
            type: 'Technology Company',
            industry: 'Software & Technology',
            contactEmail: 'partnerships@techinnovation.com',
            phone: '+1 (555) 123-4567',
            establishedYear: 2015,
            totalSponsored: 125000,
            eventsSponsored: 45,
            studentsReached: 15000,
            description: 'Leading technology company committed to fostering innovation and education. We partner with universities and organizations to support the next generation of tech leaders.',
            mission: 'To bridge the gap between academia and industry by providing meaningful sponsorship opportunities that create lasting impact in the technology and education sectors.',
            website: 'https://techinnovation.com',
            linkedin: 'https://linkedin.com/company/tech-innovation-corp',
            twitter: 'https://twitter.com/TechInnovCorp',
            instagram: 'https://instagram.com/techinnovcorp',
            facebook: 'https://facebook.com/TechInnovationCorp',
            logo: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80',
            rating: 4.8,
            reviewCount: 24
        };
        
        this.sponsorshipHistory = [
            {
                id: 1,
                eventName: 'Annual Tech Conference 2024',
                organizerName: 'Computer Science Society',
                university: 'UC Berkeley',
                date: '2024-03-15',
                amount: 15000,
                type: 'Cash Sponsor',
                status: 'completed',
                image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop',
                description: 'Major sponsorship for annual technology conference featuring industry leaders and student innovations.',
                attendees: 500,
                impact: 'Enabled 3 keynote speakers and provided scholarship funds for 10 students'
            },
            {
                id: 2,
                eventName: 'Startup Pitch Competition',
                organizerName: 'Entrepreneurship Club',
                university: 'Stanford University',
                date: '2024-02-20',
                amount: 8000,
                type: 'Prize Sponsor',
                status: 'completed',
                image: 'https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop',
                description: 'Sponsored prize money for student startup competition.',
                attendees: 200,
                impact: 'Awarded $5,000 to winning startup, provided mentorship opportunities'
            },
            {
                id: 3,
                eventName: 'Women in Tech Symposium',
                organizerName: 'Women in Engineering Society',
                university: 'MIT',
                date: '2024-01-12',
                amount: 12000,
                type: 'Title Sponsor',
                status: 'completed',
                image: 'https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?w=400&h=250&fit=crop',
                description: 'Title sponsorship for symposium promoting women in technology fields.',
                attendees: 300,
                impact: 'Supported scholarship program for 15 female STEM students'
            },
            {
                id: 4,
                eventName: 'AI Innovation Summit 2024',
                organizerName: 'AI Research Institute',
                university: 'Carnegie Mellon',
                date: '2024-06-10',
                amount: 20000,
                type: 'Presenting Sponsor',
                status: 'upcoming',
                image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=250&fit=crop',
                description: 'Presenting sponsor for premier AI research and innovation summit.',
                attendees: 600,
                impact: 'Supporting research showcase and networking opportunities'
            }
        ];

        this.testimonials = [
            {
                id: 1,
                organizerName: 'Dr. Sarah Johnson',
                position: 'Dean of Engineering',
                university: 'State University',
                eventName: 'Annual Tech Conference 2024',
                rating: 5,
                text: 'Tech Innovation Corp has been an incredible partner in our educational initiatives. Their support has enabled us to reach thousands of students.',
                date: '2024-03-20',
                avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=60&h=60&fit=crop&crop=face'
            },
            {
                id: 2,
                organizerName: 'Michael Chen',
                position: 'Event Director',
                university: 'TechFest 2024',
                rating: 5,
                text: 'The partnership with Tech Innovation Corp transformed our annual tech conference. Their expertise and funding made it a huge success.',
                date: '2024-02-25',
                avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face'
            },
            {
                id: 3,
                organizerName: 'Prof. Emily Rodriguez',
                position: 'Research Director',
                university: 'Innovation Lab',
                rating: 5,
                text: 'Outstanding support for our research project. Tech Innovation Corp\'s mentorship program connected our students with industry leaders.',
                date: '2024-01-18',
                avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=60&h=60&fit=crop&crop=face'
            }
        ];

        this.focusAreas = [
            'Technology', 'Education', 'Innovation', 
            'AI & Machine Learning', 'Entrepreneurship', 
            'STEM Education', 'Research', 'Diversity in Tech'
        ];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAnimations();
        this.loadSponsorData();
        this.loadTestimonials();
    }

    bindEvents() {
        // Event card scroll functionality
        document.querySelectorAll('.scroll-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const direction = btn.classList.contains('scroll-left') ? 'left' : 'right';
                this.scrollEvents('recent', direction);
            });
        });
    }

    setupAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = `${entry.target.dataset.delay || 0}ms`;
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe cards and elements for animation
        document.querySelectorAll('.info-card, .event-card').forEach((element, index) => {
            element.dataset.delay = index * 100;
            observer.observe(element);
        });
    }

    loadSponsorData() {
        // Update page title
        document.title = `${this.sponsorData.name} - UniPulse`;
        
        // Update sponsor info in DOM
        const orgName = document.getElementById('orgName');
        const sponsorMission = document.getElementById('sponsorMission');
        
        if (orgName) orgName.textContent = this.sponsorData.name;
        if (sponsorMission) sponsorMission.textContent = this.sponsorData.mission;
    }

    loadTestimonials() {
        const container = document.getElementById('testimonialsContainer');
        if (!container) return;

        container.innerHTML = '';
        
        this.testimonials.forEach(testimonial => {
            const testimonialCard = this.createTestimonialCard(testimonial);
            container.appendChild(testimonialCard);
        });
    }

    createTestimonialCard(testimonial) {
        const card = document.createElement('div');
        card.className = 'testimonial-card';

        const stars = '★'.repeat(testimonial.rating) + '☆'.repeat(5 - testimonial.rating);

        card.innerHTML = `
            <div class="testimonial-header">
                <div class="testimonial-avatar">
                    <img src="${testimonial.avatar}" alt="${testimonial.organizerName}">
                </div>
                <div class="testimonial-info">
                    <h4>${testimonial.organizerName}</h4>
                    <p class="testimonial-position">${testimonial.position}</p>
                    <p class="testimonial-university">${testimonial.university}</p>
                </div>
                <div class="testimonial-rating">
                    <div class="stars">${stars}</div>
                    <span class="rating-text">${testimonial.rating}/5</span>
                </div>
            </div>
            <div class="testimonial-content">
                <p class="testimonial-text">"${testimonial.text}"</p>
                <span class="testimonial-date">${this.formatDate(testimonial.date)}</span>
            </div>
        `;

        return card;
    }

    scrollEvents(type, direction) {
        const container = document.getElementById('recentSponsorshipsContainer');
        
        if (container) {
            const scrollAmount = 375; // Width of card + gap
            const currentScroll = container.scrollLeft;
            
            if (direction === 'left') {
                container.scrollTo({
                    left: currentScroll - scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollTo({
                    left: currentScroll + scrollAmount,
                    behavior: 'smooth'
                });
            }
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            weekday: 'long'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Gallery Carousel Functions
    changeCarouselImage(galleryId, direction) {
        const gallery = document.querySelector(`[data-gallery-id="${galleryId}"]`);
        if (!gallery) return;

        const carousel = gallery.querySelector('.gallery-image-carousel');
        const images = carousel.querySelectorAll('.carousel-image');
        const currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
        
        let newIndex = currentIndex + direction;
        
        if (newIndex >= images.length) newIndex = 0;
        if (newIndex < 0) newIndex = images.length - 1;
        
        this.setCarouselImage(galleryId, newIndex);
    }

    setCarouselImage(galleryId, index) {
        const gallery = document.querySelector(`[data-gallery-id="${galleryId}"]`);
        if (!gallery) return;

        const images = gallery.querySelectorAll('.carousel-image');
        const indicators = gallery.querySelectorAll('.indicator');
        
        // Remove active class from all images and indicators
        images.forEach(img => img.classList.remove('active'));
        indicators.forEach(ind => ind.classList.remove('active'));
        
        // Add active class to selected image and indicator
        if (images[index]) images[index].classList.add('active');
        if (indicators[index]) indicators[index].classList.add('active');
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;

        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            z-index: 3000;
            animation: slideInRight 0.3s ease;
            max-width: 400px;
            font-weight: 500;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease forwards';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    getNotificationColor(type) {
        const colors = {
            success: '#10B981',
            error: '#EF4444',
            warning: '#F59E0B',
            info: '#3B82F6'
        };
        return colors[type] || '#3B82F6';
    }
}

// Global functions
function scrollEvents(type, direction) {
    if (window.sponsorProfile) {
        window.sponsorProfile.scrollEvents(type, direction);
    }
}

function changeCarouselImage(galleryId, direction) {
    if (window.sponsorProfile) {
        window.sponsorProfile.changeCarouselImage(galleryId, direction);
    }
}

function setCarouselImage(galleryId, index) {
    if (window.sponsorProfile) {
        window.sponsorProfile.setCarouselImage(galleryId, index);
    }
}

// Initialize the sponsor public profile when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.sponsorProfile = new SponsorPublicProfile();
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add loading states to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('loading') && !this.onclick) {
                this.classList.add('loading');
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.innerHTML = originalText;
                }, 1000);
            }
        });
    });

    // Add parallax effect to cover photo
    const coverPhoto = document.querySelector('.cover-photo img');
    if (coverPhoto) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = scrolled * 0.5;
            coverPhoto.style.transform = `translate3d(0, ${parallax}px, 0)`;
        });
    }

    console.log('Sponsor Public Profile initialized successfully!');
});
