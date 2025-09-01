class OrganizationPublicProfile {
    constructor() {
        this.organizationData = {
            name: 'Tech Innovation Society',
            type: 'Student Organization',
            university: 'University of California, Berkeley',
            faculty: 'School of Engineering',
            department: 'Computer Science & Engineering',
            contactEmail: 'contact@techinnovationsociety.org',
            establishedYear: 2018,
            memberCount: 245,
            description: 'Leading student organization dedicated to fostering innovation and technological advancement. We organize workshops, hackathons, and networking events to bridge the gap between academia and industry.',
            mission: 'To create a vibrant community of tech enthusiasts, fostering innovation, collaboration, and professional development through hands-on learning experiences and industry partnerships.',
            website: 'https://techinnovationsociety.berkeley.edu',
            instagram: 'https://instagram.com/berkeley_tech_society',
            facebook: 'https://facebook.com/BerkeleyTechSociety',
            linkedin: 'https://linkedin.com/company/berkeley-tech-innovation-society',
            twitter: 'https://twitter.com/BerkeleyTechSoc',
            discord: 'https://discord.gg/berkeley-tech',
            logo: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80'
        };
        
        this.events = [
            {
                id: 1,
                title: 'AI Innovation Summit 2024',
                date: '2024-12-15',
                location: 'Berkeley Campus Center',
                image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'organized',
                featured: true,
                description: 'Annual summit featuring AI innovations and industry partnerships. Join us for keynote speeches, workshops, and networking with leading tech professionals.',
                attendees: 150,
                registrationOpen: true
            },
            {
                id: 2,
                title: 'Startup Pitch Night',
                date: '2024-11-20',
                location: 'Student Innovation Lab',
                image: 'https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'organized',
                featured: false,
                description: 'Monthly pitch competition for student entrepreneurs. Present your ideas and compete for funding opportunities.',
                attendees: 80,
                registrationOpen: true
            },
            {
                id: 3,
                title: 'Tech Networking Mixer',
                date: '2024-10-25',
                location: 'Berkeley Engineering Building',
                image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'organized',
                featured: true,
                description: 'Connect with industry professionals and fellow students in a relaxed networking environment.',
                attendees: 120,
                registrationOpen: true
            },
            {
                id: 4,
                title: 'Hackathon 2024',
                date: '2024-08-15',
                location: 'Berkeley Computer Science Building',
                image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=400&h=250&fit=crop',
                category: 'past',
                type: 'organized',
                featured: true,
                description: '48-hour hackathon with $10,000 in prizes. Students built innovative solutions to real-world problems.',
                attendees: 200,
                registrationOpen: false
            },
            {
                id: 5,
                title: 'Web Development Workshop',
                date: '2024-07-20',
                location: 'Online & Berkeley Lab',
                image: 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=400&h=250&fit=crop',
                category: 'past',
                type: 'organized',
                featured: false,
                description: 'Comprehensive workshop covering modern web development with React and Node.js.',
                attendees: 60,
                registrationOpen: false
            },
            {
                id: 6,
                title: 'Industry Career Fair',
                date: '2024-06-10',
                location: 'Berkeley Memorial Stadium',
                image: 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=400&h=250&fit=crop',
                category: 'past',
                type: 'organized',
                featured: true,
                description: 'Connect with top tech companies for internships and full-time opportunities.',
                attendees: 300,
                registrationOpen: false
            }
        ];

        this.galleryItems = [
            {
                id: 1,
                image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop',
                title: 'Hackathon 2024 Winners',
                description: 'Celebrating our winning teams at the annual hackathon'
            },
            {
                id: 2,
                image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
                title: 'Tech Workshop Session',
                description: 'Students learning cutting-edge technologies'
            },
            {
                id: 3,
                image: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop',
                title: 'Industry Networking Event',
                description: 'Connecting with tech industry professionals'
            },
            {
                id: 4,
                image: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop',
                title: 'Innovation Lab Session',
                description: 'Working on cutting-edge projects'
            },
            {
                id: 5,
                image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop',
                title: 'Team Building Activities',
                description: 'Building stronger connections within our community'
            },
            {
                id: 6,
                image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop',
                title: 'Guest Speaker Series',
                description: 'Learning from industry experts and thought leaders'
            }
        ];
        
        this.focusAreas = [
            'Technology', 'Innovation', 'Entrepreneurship', 
            'AI & Machine Learning', 'Web Development', 
            'Networking', 'Research', 'Startups'
        ];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAnimations();
        this.loadOrganizationData();
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

        // Event search (if needed)
        const eventSearch = document.getElementById('eventSearch');
        if (eventSearch) {
            eventSearch.addEventListener('input', (e) => {
                this.searchEvents(e.target.value);
            });
        }

        // Event filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.filterEvents(e.target.dataset.filter);
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

    loadOrganizationData() {
        // Update page title
        document.title = `${this.organizationData.name} - UniPulse`;
        
        // Update organization info in DOM
        const orgName = document.getElementById('orgName');
        const orgDescription = document.getElementById('orgDescription');
        
        if (orgName) orgName.textContent = this.organizationData.name;
        if (orgDescription) orgDescription.textContent = this.organizationData.description;
    }

    categorizeEvents() {
        const currentDate = new Date('2024-08-28');
        
        this.events.forEach(event => {
            const eventDate = new Date(event.date);
            event.category = eventDate > currentDate ? 'upcoming' : 'past';
        });
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

        // Load tab-specific content
        if (tabName === 'gallery') {
            this.loadGallery();
        }
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

    loadEvents() {
        const container = document.getElementById('eventsContainer');
        if (!container) return;

        container.innerHTML = '';
        
        // Sort events: upcoming first (by date), then past events
        const sortedEvents = this.events.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            const currentDate = new Date('2024-08-28');
            
            if ((dateA > currentDate && dateB > currentDate) || (dateA <= currentDate && dateB <= currentDate)) {
                return dateA - dateB;
            }
            return dateA > currentDate ? -1 : 1;
        });
        
        sortedEvents.forEach(event => {
            const eventCard = this.createEventCard(event);
            container.appendChild(eventCard);
        });
    }

    createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'event-card';
        card.dataset.category = event.category;
        card.dataset.type = event.type;
        card.dataset.title = event.title.toLowerCase();
        card.dataset.featured = event.featured ? 'true' : 'false';

        const badgeClass = event.category === 'upcoming' ? 'upcoming' : 'past';
        const badgeText = event.category === 'upcoming' ? 'Upcoming' : 'Past Event';

        card.innerHTML = `
            <div class="event-image">
                <img src="${event.image}" alt="${event.title}" loading="lazy">
                <span class="event-badge ${badgeClass}">${badgeText}</span>
                ${event.featured ? '<span class="event-badge featured" style="top: 15px; left: 15px;">Featured</span>' : ''}
            </div>
            <div class="event-info">
                <h4>${event.title}</h4>
                <p class="event-date">
                    <i class="fas fa-calendar"></i> 
                    ${this.formatDate(event.date)}
                </p>
                <p class="event-location">
                    <i class="fas fa-map-marker-alt"></i> 
                    ${event.location}
                </p>
                <p class="event-attendees">
                    <i class="fas fa-users"></i> 
                    ${event.attendees} attendees
                </p>
                <p class="event-description">${event.description}</p>
                <div class="event-actions">
                    <button class="btn btn-primary btn-small" onclick="orgProfile.viewEventDetails(${event.id})">
                        View Details
                    </button>
                    ${event.category === 'upcoming' && event.registrationOpen ? 
                        '<button class="btn btn-secondary btn-small" onclick="orgProfile.registerForEvent(' + event.id + ')">Register</button>' : 
                        ''
                    }
                </div>
            </div>
        `;

        return card;
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

    filterEvents(filter) {
        // Remove active class from all filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

        // Filter events
        const eventCards = document.querySelectorAll('.event-card');
        eventCards.forEach(card => {
            const show = filter === 'all' || 
                        card.dataset.category === filter || 
                        card.dataset.type === filter ||
                        (filter === 'featured' && card.dataset.featured === 'true');
            
            card.style.display = show ? 'block' : 'none';
        });

        this.showNotification(`Showing ${filter === 'all' ? 'all' : filter} events`, 'info');
    }

    searchEvents(query) {
        const eventCards = document.querySelectorAll('.event-card');
        const searchTerm = query.toLowerCase();

        eventCards.forEach(card => {
            const title = card.dataset.title;
            const description = card.querySelector('.event-description').textContent.toLowerCase();
            const location = card.querySelector('.event-location').textContent.toLowerCase();
            
            const matches = title.includes(searchTerm) || 
                          description.includes(searchTerm) || 
                          location.includes(searchTerm);
            
            card.style.display = matches ? 'block' : 'none';
        });
    }

    loadGallery() {
        const container = document.getElementById('galleryContainer');
        if (!container) return;

        container.innerHTML = '';
        
        this.galleryItems.forEach(item => {
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item';
            galleryItem.onclick = () => this.openGalleryModal(item);
            
            galleryItem.innerHTML = `
                <img src="${item.image}" alt="${item.title}" loading="lazy">
                <div class="gallery-overlay">
                    <h4>${item.title}</h4>
                    <p>${item.description}</p>
                </div>
            `;
            
            container.appendChild(galleryItem);
        });
    }

    openGalleryModal(item) {
        // Create and show gallery modal
        this.showNotification(`Opening ${item.title}`, 'info');
        // Implement gallery modal functionality
    }

    viewEventDetails(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Opening details for ${event.title}`, 'info');
            // Implement event details modal or redirect
        }
    }

    registerForEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Opening registration for ${event.title}`, 'success');
            // Implement event registration functionality
        }
    }

    submitContactForm() {
        const formData = new FormData(document.getElementById('contactForm'));
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!data.name || !data.email || !data.subject || !data.message) {
            this.showNotification('Please fill in all required fields.', 'error');
            return;
        }

        // Simulate form submission
        this.showNotification('Message sent successfully! We will get back to you soon.', 'success');
        document.getElementById('contactForm').reset();
        
        // In a real app, this would send data to the server
        console.log('Contact form data:', data);
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
function scrollEvents(section, direction) {
    const containerId = section === 'upcoming' ? 'upcomingEventsContainer' : 'pastEventsContainer';
    const container = document.getElementById(containerId);
    
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
    orgProfile.closeModal(modalId);
}

function verifyOrganization() {
    orgProfile.showNotification('Verification request submitted! Our team will review and contact you within 48 hours.', 'success');
    
    // Add some visual feedback to the button
    const button = document.querySelector('.btn-verify');
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check-circle"></i> Request Submitted';
            button.style.background = 'linear-gradient(135deg, #6c757d 0%, #495057 100%)';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.style.background = '';
            }, 3000);
        }, 2000);
    }
}

// Carousel functionality for gallery items
function changeCarouselImage(galleryId, direction) {
    const galleryItem = document.querySelector(`[data-gallery-id="${galleryId}"]`);
    if (!galleryItem) return;
    
    const images = galleryItem.querySelectorAll('.carousel-image');
    const indicators = galleryItem.querySelectorAll('.indicator');
    let currentIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
    
    // Remove active class from current image and indicator
    images[currentIndex].classList.remove('active');
    indicators[currentIndex].classList.remove('active');
    
    // Calculate new index
    currentIndex += direction;
    if (currentIndex >= images.length) currentIndex = 0;
    if (currentIndex < 0) currentIndex = images.length - 1;
    
    // Add active class to new image and indicator
    images[currentIndex].classList.add('active');
    indicators[currentIndex].classList.add('active');
}

function setCarouselImage(galleryId, index) {
    const galleryItem = document.querySelector(`[data-gallery-id="${galleryId}"]`);
    if (!galleryItem) return;
    
    const images = galleryItem.querySelectorAll('.carousel-image');
    const indicators = galleryItem.querySelectorAll('.indicator');
    
    // Remove active class from all
    images.forEach(img => img.classList.remove('active'));
    indicators.forEach(ind => ind.classList.remove('active'));
    
    // Add active class to selected
    if (images[index]) images[index].classList.add('active');
    if (indicators[index]) indicators[index].classList.add('active');
}

// Initialize the organization public profile when DOM is loaded
let orgProfile;
document.addEventListener('DOMContentLoaded', () => {
    orgProfile = new OrganizationPublicProfile();
    
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

    console.log('Organization Public Profile initialized successfully!');
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
