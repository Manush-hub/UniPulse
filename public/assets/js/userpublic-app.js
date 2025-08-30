class UserPublicProfile {
    constructor() {
        this.userData = {
            name: 'Lakmal',
            studentId: 'CS2024-001',
            university: 'University of California, Berkeley',
            college: 'College of Engineering',
            major: 'Computer Science',
            minor: 'Mathematics',
            graduationYear: 'Spring 2025',
            academicYear: 'Senior (4th Year)',
            gpa: '3.85/4.0',
            location: 'Berkeley, CA',
            email: 'john.smith@berkeley.edu',
            phone: '+1 (510) 486-1234',
            description: 'Passionate computer science student with a focus on artificial intelligence and machine learning. Actively involved in research projects and student organizations, seeking opportunities to apply technology for social impact.',
            careerGoals: 'Aspiring to become a machine learning engineer working on cutting-edge AI applications that can make a positive impact on society. Particularly interested in healthcare technology and sustainable development solutions.',
            website: 'https://johnsmith.dev',
            github: 'https://github.com/johnsmith',
            linkedin: 'https://linkedin.com/in/johnsmith-berkeley',
            twitter: 'https://twitter.com/johnsmith_dev',
            instagram: 'https://instagram.com/johnsmith.codes',
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=cover&w=400&q=80'
        };
        
        this.events = [
            {
                id: 1,
                title: 'AI Innovation Summit 2024',
                date: '2024-12-15',
                location: 'Berkeley Campus Center',
                organizer: 'Tech Innovation Society',
                image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop',
                category: 'attending',
                featured: true,
                description: 'Annual summit featuring AI innovations and industry partnerships.',
                role: 'Attendee'
            },
            {
                id: 2,
                title: 'Web Development Workshop',
                date: '2024-11-20',
                location: 'Student Innovation Lab',
                organizer: 'CS Student Association',
                image: 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=400&h=250&fit=crop',
                category: 'attending',
                featured: false,
                description: 'Comprehensive workshop covering modern web development.',
                role: 'Attendee'
            },
            {
                id: 3,
                title: 'Industry Career Fair',
                date: '2024-10-25',
                location: 'Berkeley Engineering Building',
                organizer: 'Career Services',
                image: 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=400&h=250&fit=crop',
                category: 'attending',
                featured: true,
                description: 'Connect with top tech companies for internship and job opportunities.',
                role: 'Attendee'
            },
            {
                id: 4,
                title: 'Hackathon 2024',
                date: '2024-08-15',
                location: 'Berkeley Computer Science Building',
                organizer: 'Tech Innovation Society',
                image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=400&h=250&fit=crop',
                category: 'past',
                featured: false,
                description: '48-hour hackathon - Won 2nd place for AI Healthcare Solution.',
                role: 'Participant',
                achievement: '2nd Place Winner'
            },
            {
                id: 5,
                title: 'Undergraduate Research Symposium',
                date: '2024-07-20',
                location: 'Berkeley Research Hall',
                organizer: 'UC Berkeley Research Office',
                image: 'https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop',
                category: 'past',
                featured: false,
                description: 'Presented research on "Machine Learning for Climate Prediction".',
                role: 'Presenter',
                achievement: 'Research Presenter'
            },
            {
                id: 6,
                title: 'Tech Networking Mixer',
                date: '2024-06-10',
                location: 'Berkeley Memorial Stadium',
                organizer: 'Alumni Network',
                image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop',
                category: 'past',
                featured: false,
                description: 'Connected with alumni and industry professionals.',
                role: 'Participant'
            }
        ];

        this.projects = [
            {
                id: 1,
                title: 'AI Healthcare Diagnosis Tool',
                technologies: ['Python', 'TensorFlow', 'React', 'Node.js'],
                description: 'Machine learning application for early disease detection using medical imaging. Won 2nd place at Berkeley Hackathon 2024.',
                image: 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=250&fit=crop',
                githubLink: '#',
                liveLink: '#',
                featured: true
            },
            {
                id: 2,
                title: 'Climate Change Prediction Model',
                technologies: ['Python', 'scikit-learn', 'Pandas', 'Matplotlib'],
                description: 'Research project developing ML models to predict climate patterns. Presented at Undergraduate Research Symposium.',
                image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=250&fit=crop',
                githubLink: '#',
                paperLink: '#',
                featured: true
            },
            {
                id: 3,
                title: 'UniPulse Student Portal',
                technologies: ['React', 'Node.js', 'MongoDB', 'Express'],
                description: 'Full-stack web application connecting students with campus events and organizations. Currently serving 500+ active users.',
                image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop',
                githubLink: '#',
                liveLink: '#',
                featured: true
            }
        ];

        this.achievements = [
            {
                id: 1,
                title: "Dean's List",
                description: "Fall 2023, Spring 2024 - Academic Excellence Recognition",
                icon: 'medal'
            },
            {
                id: 2,
                title: "Berkeley Hackathon 2024 - 2nd Place",
                description: "AI Healthcare Solution - $5,000 prize",
                icon: 'trophy'
            },
            {
                id: 3,
                title: "Research Excellence Award",
                description: "Outstanding Undergraduate Research in Computer Science",
                icon: 'certificate'
            },
            {
                id: 4,
                title: "Academic Scholarship Recipient",
                description: "Merit-based scholarship for academic performance",
                icon: 'star'
            }
        ];

        this.galleryItems = [
            {
                id: 1,
                image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop',
                title: 'Hackathon Victory',
                description: 'Celebrating 2nd place win at Berkeley Hackathon 2024'
            },
            {
                id: 2,
                image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
                title: 'Research Presentation',
                description: 'Presenting climate prediction research at symposium'
            },
            {
                id: 3,
                image: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop',
                title: 'Team Collaboration',
                description: 'Working with fellow students on group projects'
            },
            {
                id: 4,
                image: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop',
                title: 'Lab Work',
                description: 'Conducting research in the AI laboratory'
            },
            {
                id: 5,
                image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop',
                title: 'Networking Events',
                description: 'Building professional connections at tech events'
            },
            {
                id: 6,
                image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop',
                title: 'Study Sessions',
                description: 'Collaborative learning with classmates'
            }
        ];
        
        this.skills = [
            'Python', 'JavaScript', 'Machine Learning', 'React.js', 
            'Node.js', 'Data Science', 'Research', 'AI Ethics'
        ];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAnimations();
        this.loadUserData();
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

        // Event search
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

        // Project links
        document.querySelectorAll('.project-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.showNotification('Opening project link...', 'info');
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
        document.querySelectorAll('.info-card, .event-card, .project-card, .achievement-item').forEach((element, index) => {
            element.dataset.delay = index * 100;
            observer.observe(element);
        });

        // Animate GPA and other stats
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
        const text = element.textContent;
        if (text.includes('.')) {
            // Handle GPA animation
            const target = parseFloat(text);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = current.toFixed(2);
            }, 16);
        } else {
            // Handle integer animation
            const target = parseInt(text);
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
    }

    loadUserData() {
        // Update page title
        document.title = `${this.userData.name} - UniPulse`;
        
        // Update user info in DOM
        // const userName = document.getElementById('userName');
        // const userDescription = document.getElementById('userDescription');
        
        // if (userName) userName.textContent = this.userData.name;
        // if (userDescription) userDescription.textContent = this.userData.description;
    }

    categorizeEvents() {
        const currentDate = new Date('2024-08-28');
        
        this.events.forEach(event => {
            const eventDate = new Date(event.date);
            if (event.category !== 'past') {
                event.category = eventDate > currentDate ? 'attending' : 'past';
            }
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
        } else if (tabName === 'projects') {
            this.loadProjects();
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
        const attendingContainer = document.getElementById('attendingEventsContainer');
        const pastContainer = document.getElementById('pastEventsContainer');
        
        if (attendingContainer) {
            attendingContainer.innerHTML = '';
            this.events.filter(event => event.category === 'attending').forEach(event => {
                const eventCard = this.createEventCard(event);
                attendingContainer.appendChild(eventCard);
            });
        }
        
        if (pastContainer) {
            pastContainer.innerHTML = '';
            this.events.filter(event => event.category === 'past').forEach(event => {
                const eventCard = this.createEventCard(event);
                pastContainer.appendChild(eventCard);
            });
        }
    }

    createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'event-card';
        card.dataset.category = event.category;
        card.dataset.title = event.title.toLowerCase();
        card.dataset.featured = event.featured ? 'true' : 'false';

        let badgeClass, badgeText;
        let extraBadge = '';
        
        if (event.category === 'attending') {
            badgeClass = 'attending';
            badgeText = 'Attending';
        } else {
            badgeClass = 'past';
            badgeText = 'Participated';
            
            if (event.achievement) {
                if (event.achievement.includes('Winner') || event.achievement.includes('Place')) {
                    extraBadge = '<span class="event-badge winner" style="top: 15px; left: 15px;">Winner</span>';
                } else if (event.achievement.includes('Presenter')) {
                    extraBadge = '<span class="event-badge presenter" style="top: 15px; left: 15px;">Presenter</span>';
                }
            }
        }

        card.innerHTML = `
            <div class="event-image">
                <img src="${event.image}" alt="${event.title}" loading="lazy">
                <span class="event-badge ${badgeClass}">${badgeText}</span>
                ${event.featured ? '<span class="event-badge featured" style="top: 15px; right: 15px;">Featured</span>' : ''}
                ${extraBadge}
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
                <p class="event-organizer">
                    <i class="fas fa-users"></i> 
                    ${event.organizer}
                </p>
                <p class="event-description">${event.description}</p>
                <div class="event-actions">
                    <button class="btn btn-primary btn-small" onclick="userProfile.viewEventDetails(${event.id})">
                        View Details
                    </button>
                    ${event.achievement ? 
                        '<button class="btn btn-outline btn-small">View ' + (event.achievement.includes('Winner') ? 'Project' : 
                        event.achievement.includes('Presenter') ? 'Presentation' : 'Results') + '</button>' : 
                        ''
                    }
                </div>
            </div>
        `;

        return card;
    }

    loadProjects() {
        const container = document.querySelector('.projects-showcase');
        if (!container) return;

        container.innerHTML = '';
        
        this.projects.forEach(project => {
            const projectCard = this.createProjectCard(project);
            container.appendChild(projectCard);
        });
    }

    createProjectCard(project) {
        const card = document.createElement('div');
        card.className = 'project-card';
        
        const techTags = project.technologies.join(' • ');
        
        card.innerHTML = `
            <div class="project-image">
                <img src="${project.image}" alt="${project.title}">
            </div>
            <div class="project-info">
                <h4>${project.title}</h4>
                <p class="project-tech">${techTags}</p>
                <p class="project-description">${project.description}</p>
                <div class="project-links">
                    <a href="${project.githubLink}" class="project-link">
                        <i class="fab fa-github"></i> GitHub
                    </a>
                    ${project.liveLink ? 
                        `<a href="${project.liveLink}" class="project-link">
                            <i class="fas fa-external-link-alt"></i> Live Demo
                        </a>` : ''
                    }
                    ${project.paperLink ? 
                        `<a href="${project.paperLink}" class="project-link">
                            <i class="fas fa-file-pdf"></i> Research Paper
                        </a>` : ''
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
            const organizer = card.querySelector('.event-organizer').textContent.toLowerCase();
            
            const matches = title.includes(searchTerm) || 
                          description.includes(searchTerm) || 
                          location.includes(searchTerm) ||
                          organizer.includes(searchTerm);
            
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

    viewProject(projectId) {
        const project = this.projects.find(p => p.id === projectId);
        if (project) {
            this.showNotification(`Opening ${project.title}`, 'info');
            // Implement project details modal or redirect
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
        this.showNotification('Message sent successfully! John will get back to you soon.', 'success');
        document.getElementById('contactForm').reset();
        
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
    const containerId = section === 'attending' ? 'attendingEventsContainer' : 'pastEventsContainer';
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
    userProfile.closeModal(modalId);
}

// Initialize the user public profile when DOM is loaded
let userProfile;
document.addEventListener('DOMContentLoaded', () => {
    userProfile = new UserPublicProfile();
    
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
    document.querySelectorAll('.info-card, .event-card, .project-card, .achievement-item').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    console.log('User Public Profile initialized successfully!');
});

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

// Add slideOutRight animation
const slideOutStyle = document.createElement('style');
slideOutStyle.textContent = `
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(slideOutStyle);
