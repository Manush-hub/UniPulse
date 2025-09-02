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
                organizerName: 'Sarah Johnson',
                position: 'President, Computer Science Society',
                university: 'UC Berkeley',
                eventName: 'Annual Tech Conference 2024',
                rating: 5,
                text: 'Tech Innovation Corp has been an incredible partner. Their support went beyond financial - they provided mentorship and real-world insights that made our conference truly impactful.',
                date: '2024-03-20',
                avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=60&h=60&fit=crop&crop=face'
            },
            {
                id: 2,
                organizerName: 'Michael Chen',
                position: 'Director, Entrepreneurship Club',
                university: 'Stanford University',
                rating: 5,
                text: 'Working with Tech Innovation Corp was seamless. They understood our vision and provided exactly what we needed to make our startup competition a huge success.',
                date: '2024-02-25',
                avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face'
            },
            {
                id: 3,
                organizerName: 'Dr. Lisa Rodriguez',
                position: 'Faculty Advisor, Women in Engineering',
                university: 'MIT',
                rating: 4,
                text: 'Tech Innovation Corp\'s commitment to diversity in tech is genuine. Their sponsorship enabled us to create meaningful opportunities for women in STEM.',
                date: '2024-01-18',
                avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=60&h=60&fit=crop&crop=face'
            }
        ];

        this.focusAreas = [
            'Technology', 'Education', 'Innovation', 
            'AI & Machine Learning', 'Entrepreneurship', 
            'STEM Education', 'Research', 'Diversity in Tech'
        ];

        this.partnershipTypes = [
            {
                type: 'Event Sponsorship',
                description: 'Support for conferences, workshops, and educational events',
                icon: 'fas fa-calendar-alt'
            },
            {
                type: 'Scholarship Programs',
                description: 'Direct financial support for student education',
                icon: 'fas fa-graduation-cap'
            },
            {
                type: 'Research Funding',
                description: 'Supporting innovative research projects and initiatives',
                icon: 'fas fa-flask'
            },
            {
                type: 'Mentorship Programs',
                description: 'Connecting students with industry professionals',
                icon: 'fas fa-users'
            }
        ];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAnimations();
        this.loadSponsorData();
        this.loadSponsorshipHistory();
        this.loadTestimonials();
    }

    bindEvents() {
        // Contact form
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitContactForm();
            });
        }

        // Sponsorship search
        const sponsorshipSearch = document.getElementById('sponsorshipSearch');
        if (sponsorshipSearch) {
            sponsorshipSearch.addEventListener('input', (e) => {
                this.searchSponsorships(e.target.value);
            });
        }

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.filterSponsorships(e.target.dataset.filter);
            });
        });

        // Tab navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const tabName = e.target.dataset.tab;
                if (tabName) {
                    this.showTab(tabName);
                }
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
        document.querySelectorAll('.info-card, .event-card, .leader-card').forEach((element, index) => {
            element.dataset.delay = index * 100;
            observer.observe(element);
        });

        // Animate statistics on scroll
        this.animateStatistics();
    }

    animateStatistics() {
        const stats = document.querySelectorAll('.stat-number');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateNumber(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });

        stats.forEach(stat => observer.observe(stat));
    }

    animateNumber(element) {
        const target = parseInt(element.textContent);
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }

    loadSponsorData() {
        // Update page title
        document.title = `${this.sponsorData.name} - UniPulse`;
        
        // Update sponsor info in DOM
        const sponsorName = document.getElementById('sponsorName');
        const sponsorDescription = document.getElementById('sponsorDescription');
        const sponsorMission = document.getElementById('sponsorMission');
        
        if (sponsorName) sponsorName.textContent = this.sponsorData.name;
        if (sponsorDescription) sponsorDescription.textContent = this.sponsorData.description;
        if (sponsorMission) sponsorMission.textContent = this.sponsorData.mission;

        // Update statistics
        this.updateStatistics();
        
        // Update focus areas
        this.updateFocusAreas();
    }

    updateStatistics() {
        const totalSponsoredEl = document.getElementById('totalSponsored');
        const eventsSponsoredEl = document.getElementById('eventsSponsored');
        const studentsReachedEl = document.getElementById('studentsReached');
        const yearsActiveEl = document.getElementById('yearsActive');

        if (totalSponsoredEl) totalSponsoredEl.textContent = `$${(this.sponsorData.totalSponsored / 1000)}K+`;
        if (eventsSponsoredEl) eventsSponsoredEl.textContent = `${this.sponsorData.eventsSponsored}+`;
        if (studentsReachedEl) studentsReachedEl.textContent = `${(this.sponsorData.studentsReached / 1000)}K+`;
        if (yearsActiveEl) yearsActiveEl.textContent = `${new Date().getFullYear() - this.sponsorData.establishedYear}`;
    }

    updateFocusAreas() {
        const focusContainer = document.getElementById('focusAreasContainer');
        if (!focusContainer) return;

        focusContainer.innerHTML = '';
        this.focusAreas.forEach(area => {
            const areaElement = document.createElement('span');
            areaElement.className = 'focus-area-tag';
            areaElement.textContent = area;
            focusContainer.appendChild(areaElement);
        });
    }

    loadSponsorshipHistory() {
        const container = document.getElementById('sponsorshipContainer');
        if (!container) return;

        container.innerHTML = '';
        
        // Sort sponsorships by date (recent first)
        const sortedSponsorships = this.sponsorshipHistory.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        sortedSponsorships.forEach(sponsorship => {
            const sponsorshipCard = this.createSponsorshipCard(sponsorship);
            container.appendChild(sponsorshipCard);
        });
    }

    createSponsorshipCard(sponsorship) {
        const card = document.createElement('div');
        card.className = 'sponsorship-card';
        card.dataset.status = sponsorship.status;
        card.dataset.type = sponsorship.type.toLowerCase().replace(' ', '-');

        const statusClass = sponsorship.status === 'completed' ? 'completed' : 'upcoming';
        const statusText = sponsorship.status === 'completed' ? 'Completed' : 'Upcoming';

        card.innerHTML = `
            <div class="sponsorship-image">
                <img src="${sponsorship.image}" alt="${sponsorship.eventName}" loading="lazy">
                <span class="sponsorship-badge ${statusClass}">${statusText}</span>
                <span class="sponsorship-type-badge">${sponsorship.type}</span>
            </div>
            <div class="sponsorship-info">
                <h4>${sponsorship.eventName}</h4>
                <p class="sponsorship-organizer">
                    <i class="fas fa-users"></i> 
                    ${sponsorship.organizerName}
                </p>
                <p class="sponsorship-university">
                    <i class="fas fa-university"></i> 
                    ${sponsorship.university}
                </p>
                <p class="sponsorship-date">
                    <i class="fas fa-calendar"></i> 
                    ${this.formatDate(sponsorship.date)}
                </p>
                <p class="sponsorship-amount">
                    <i class="fas fa-dollar-sign"></i> 
                    $${sponsorship.amount.toLocaleString()} sponsored
                </p>
                <p class="sponsorship-attendees">
                    <i class="fas fa-user-friends"></i> 
                    ${sponsorship.attendees} attendees reached
                </p>
                <p class="sponsorship-description">${sponsorship.description}</p>
                <div class="sponsorship-impact">
                    <h5><i class="fas fa-heart"></i> Impact:</h5>
                    <p>${sponsorship.impact}</p>
                </div>
            </div>
        `;

        return card;
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
                    <p class="testimonial-event">${testimonial.eventName}</p>
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

    showTab(tabName) {
        // Update active tab
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update active content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        const targetTab = document.getElementById(tabName);
        if (targetTab) {
            targetTab.classList.add('active');
        }

        this.currentTab = tabName;
        this.addTransitionEffect();
    }

    addTransitionEffect() {
        const activeContent = document.querySelector('.tab-content.active');
        if (activeContent) {
            activeContent.style.opacity = '0';
            activeContent.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                activeContent.style.transition = 'all 0.3s ease';
                activeContent.style.opacity = '1';
                activeContent.style.transform = 'translateY(0)';
            }, 50);
        }
    }

    filterSponsorships(filter) {
        // Remove active class from all filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

        // Filter sponsorships
        const sponsorshipCards = document.querySelectorAll('.sponsorship-card');
        sponsorshipCards.forEach(card => {
            const show = filter === 'all' || 
                        card.dataset.status === filter || 
                        card.dataset.type === filter;
            
            card.style.display = show ? 'block' : 'none';
        });

        this.showNotification(`Showing ${filter === 'all' ? 'all' : filter} sponsorships`, 'info');
    }

    searchSponsorships(query) {
        const sponsorshipCards = document.querySelectorAll('.sponsorship-card');
        const searchTerm = query.toLowerCase();

        sponsorshipCards.forEach(card => {
            const eventName = card.querySelector('h4').textContent.toLowerCase();
            const organizer = card.querySelector('.sponsorship-organizer').textContent.toLowerCase();
            const university = card.querySelector('.sponsorship-university').textContent.toLowerCase();
            const description = card.querySelector('.sponsorship-description').textContent.toLowerCase();
            
            const matches = eventName.includes(searchTerm) || 
                          organizer.includes(searchTerm) || 
                          university.includes(searchTerm) ||
                          description.includes(searchTerm);
            
            card.style.display = matches ? 'block' : 'none';
        });
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

    submitContactForm() {
        const formData = new FormData(document.getElementById('contactForm'));
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!data.name || !data.email || !data.organization || !data.message) {
            this.showNotification('Please fill in all required fields.', 'error');
            return;
        }

        // Simulate form submission
        this.showNotification('Sponsorship inquiry sent successfully! We will get back to you within 24 hours.', 'success');
        document.getElementById('contactForm').reset();
        
        // In a real app, this would send data to the server
        console.log('Sponsorship inquiry data:', data);
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
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

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.2em;
                cursor: pointer;
                margin-left: auto;
                opacity: 0.8;
                transition: opacity 0.3s ease;
            }
            .notification-close:hover {
                opacity: 1;
            }
        `;
        
        if (!document.querySelector('style[data-notification]')) {
            style.setAttribute('data-notification', 'true');
            document.head.appendChild(style);
        }

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

// Global functions for onclick handlers
function scrollSponsorships(direction) {
    const container = document.getElementById('sponsorshipContainer');
    
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

function closeModal(modalId) {
    sponsorProfile.closeModal(modalId);
}

function sendSponsorshipInquiry() {
    sponsorProfile.showNotification('Opening sponsorship inquiry form...', 'info');
    // This would open a contact form modal or redirect to contact page
    const contactSection = document.getElementById('contact');
    if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' });
    }
}

function requestPartnership() {
    sponsorProfile.showNotification('Partnership request form opened! Please fill out your details.', 'success');
    // This would open a partnership request form
}

// Initialize the sponsor public profile when DOM is loaded
let sponsorProfile;
document.addEventListener('DOMContentLoaded', () => {
    sponsorProfile = new SponsorPublicProfile();
    
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

    // Add intersection observer for animating elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -10% 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards for animation
    document.querySelectorAll('.info-card, .event-card, .leader-card, .stat-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    console.log('Sponsor Public Profile initialized successfully!');
});

// Add slideOutRight animation
const slideOutStyle = document.createElement('style');
slideOutStyle.textContent = `
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(slideOutStyle);
