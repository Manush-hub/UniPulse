class ClubProfile {
    constructor() {
        this.currentTab = 'about';
        this.isPublicView = false;
        this.organizationData = {
            name: 'Tech Innovation Society',
            type: 'student-org',
            university: 'University of California, Berkeley',
            faculty: 'School of Engineering',
            department: 'Computer Science & Engineering',
            contactEmail: 'contact@techinnovationsociety.org',
            contactPhone: '+1 (510) 642-1000',
            establishedYear: 2018,
            memberCount: 245,
            bio: 'Leading student organization dedicated to fostering innovation and technological advancement. We organize workshops, hackathons, and networking events to bridge the gap between academia and industry.',
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
                description: 'Annual summit featuring AI innovations and industry partnerships.',
                attendees: 150
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
                description: 'Monthly pitch competition for student entrepreneurs.',
                attendees: 80
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
                description: 'Connect with industry professionals and fellow students.',
                attendees: 120
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
                description: '48-hour hackathon with $10,000 in prizes.',
                attendees: 200
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
                description: 'Learn modern web development with React and Node.js.',
                attendees: 60
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
                description: 'Meet with top tech companies for internships and jobs.',
                attendees: 300
            }
        ];
        
        this.focusAreas = ['Technology', 'Innovation', 'Entrepreneurship', 'Networking'];
        this.notifications = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadEvents();
        this.loadUserData();
        this.setupAnimations();
        this.categorizeEvents();
        this.setupImageUploads();
    }

    setupImageUploads() {
        // Setup cover photo upload
        this.setupCoverPhotoUpload();
        // Setup profile photo upload
        this.setupProfilePhotoUpload();
    }

    setupCoverPhotoUpload() {
        const coverOverlay = document.querySelector('.cover-overlay');
        const coverInput = document.getElementById('coverInput');

        if (coverOverlay && coverInput) {
            coverOverlay.addEventListener('click', () => {
                coverInput.click();
            });

            coverInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const coverImg = document.getElementById('coverPhoto');
                        if (coverImg) {
                            coverImg.src = e.target.result;
                            this.showNotification('Cover photo updated successfully!', 'success');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    setupProfilePhotoUpload() {
        const avatarEditBtn = document.querySelector('.avatar-edit-btn');
        const fileInput = document.getElementById('fileInput');

        // Create file input if it doesn't exist
        if (!fileInput) {
            const input = document.createElement('input');
            input.type = 'file';
            input.id = 'fileInput';
            input.accept = 'image/*';
            input.style.display = 'none';
            document.body.appendChild(input);
        }

        if (avatarEditBtn) {
            avatarEditBtn.addEventListener('click', () => {
                document.getElementById('fileInput').click();
            });
        }
    }

    categorizeEvents() {
        const currentDate = new Date('2024-08-28'); // Using the current date
        
        this.events.forEach(event => {
            const eventDate = new Date(event.date);
            event.category = eventDate > currentDate ? 'upcoming' : 'past';
        });
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = item.dataset.tab;
                this.showTab(tab);
            });
        });

        this.bindFormEvents();
        this.bindToggleEvents();
        this.bindModalEvents();
    }

    bindFormEvents() {
        // Organization info form
        const organizationForm = document.getElementById('organization-form');
        if (organizationForm) {
            organizationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveOrganizationInfo();
            });
        }

        // Event preference buttons
        document.querySelectorAll('.preference-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.togglePreference(e.target);
            });
        });

        // Event filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.filterEvents(e.target.dataset.filter);
            });
        });

        // Settings form
        const settingsForm = document.querySelector('#settings form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        // File upload
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0]);
            });
        }

        // Event search
        const eventSearch = document.getElementById('eventSearch');
        if (eventSearch) {
            eventSearch.addEventListener('input', (e) => {
                this.searchEvents(e.target.value);
            });
        }

        // Member search
        const memberSearch = document.getElementById('memberSearch');
        if (memberSearch) {
            memberSearch.addEventListener('input', (e) => {
                this.searchMembers(e.target.value);
            });
        }
    }

    bindToggleEvents() {
        // Privacy toggles
        document.querySelectorAll('.toggle input').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.updatePrivacySetting(e.target.id, e.target.checked);
            });
        });

        // Preference checkboxes
        document.querySelectorAll('.checkbox-item input').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateEventPreference(e.target.parentElement.textContent.trim(), e.target.checked);
            });
        });
    }

    bindModalEvents() {
        // Modal close events
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                this.closeModal(modal.id);
            });
        });

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
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

        document.querySelectorAll('.card').forEach((card, index) => {
            card.dataset.delay = index * 100;
            observer.observe(card);
        });
    }

    loadUserData() {
        // Load organization data into form fields
        Object.keys(this.organizationData).forEach(key => {
            const element = document.getElementById(key) || document.getElementById('org' + key.charAt(0).toUpperCase() + key.slice(1));
            if (element) {
                element.value = this.organizationData[key];
            }
        });

        // Update profile display
        const profileName = document.getElementById('profileName');
        const profileBio = document.getElementById('profileBio');
        const profileImage = document.getElementById('profileImage');

        if (profileName) profileName.textContent = this.organizationData.name;
        if (profileBio) profileBio.textContent = this.organizationData.bio;
        if (profileImage) profileImage.src = this.organizationData.logo;

        // Load focus areas
        this.loadFocusAreas();
        this.updateStats();
    }

    loadFocusAreas() {
        const preferenceContainer = document.getElementById('preferenceContainer');
        if (!preferenceContainer) return;

        // Set active states for existing buttons based on focus areas
        document.querySelectorAll('.preference-btn').forEach(btn => {
            const preference = btn.dataset.preference;
            const shouldBeActive = this.focusAreas.some(area => 
                area.toLowerCase().includes(preference) || preference.includes(area.toLowerCase())
            );
            
            if (shouldBeActive) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    updateStats() {
        const upcomingCount = this.events.filter(e => e.category === 'upcoming').length;
        const pastCount = this.events.filter(e => e.category === 'past').length;
        const totalCount = this.events.length;

        const totalEvents = document.getElementById('totalEvents');
        const upcomingEvents = document.getElementById('upcomingEvents');
        const pastEvents = document.getElementById('pastEvents');

        if (totalEvents) totalEvents.textContent = totalCount;
        if (upcomingEvents) upcomingEvents.textContent = upcomingCount;
        if (pastEvents) pastEvents.textContent = pastCount;
    }

    togglePreference(button) {
        button.classList.toggle('active');
        const preference = button.dataset.preference;
        const isActive = button.classList.contains('active');
        
        if (isActive) {
            this.showNotification(`${preference.charAt(0).toUpperCase() + preference.slice(1)} preference added`, 'success');
        } else {
            this.showNotification(`${preference.charAt(0).toUpperCase() + preference.slice(1)} preference removed`, 'info');
        }
    }

    selectRole(role) {
        // Remove active class from all role buttons
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active class to selected button
        document.querySelector(`[data-role="${role}"]`).classList.add('active');

        // Update hidden input value
        document.getElementById('role').value = role;

        this.showNotification(`Role set to ${role.charAt(0).toUpperCase() + role.slice(1)}`, 'info');
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
        document.getElementById(tabName).classList.add('active');

        this.currentTab = tabName;
        this.addTransitionEffect();
    }

    addTransitionEffect() {
        const activeContent = document.querySelector('.tab-content.active');
        activeContent.style.opacity = '0';
        activeContent.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            activeContent.style.transition = 'all 0.3s ease';
            activeContent.style.opacity = '1';
            activeContent.style.transform = 'translateY(0)';
        }, 50);
    }

    loadEvents() {
        const container = document.getElementById('eventsContainer');
        if (!container) return;

        container.innerHTML = '';
        
        // Sort events: upcoming first (by date), then past events
        const sortedEvents = this.events.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            const currentDate = new Date('2024-08-25');
            
            // If both are upcoming or both are past, sort by date
            if ((dateA > currentDate && dateB > currentDate) || (dateA <= currentDate && dateB <= currentDate)) {
                return dateA - dateB;
            }
            // Upcoming events come first
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

        const badgeClass = event.category === 'upcoming' ? 'upcoming' : 'past';
        const badgeText = event.type === 'organized' ? 
            (event.category === 'upcoming' ? 'Organizing' : 'Organized') :
            (event.category === 'upcoming' ? 'Registered' : 'Attended');

        card.innerHTML = `
            <div class="event-image">
                <img src="${event.image}" alt="${event.title}" loading="lazy">
                <span class="event-badge ${badgeClass}">${badgeText}</span>
                ${event.featured ? '<span class="event-badge featured" style="top: 15px; left: 15px; background: rgba(255, 193, 7, 0.9);">Featured</span>' : ''}
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
                    <button class="btn btn-small btn-primary" onclick="clubProfile.viewEventDetails(${event.id})">
                        View Details
                    </button>
                    ${event.type === 'organized' ? 
                        '<button class="btn btn-small btn-secondary" onclick="clubProfile.manageEvent(' + event.id + ')">Manage Event</button>' : 
                        (event.category === 'past' ? 
                            '<button class="btn btn-small btn-secondary" onclick="clubProfile.rateEvent(' + event.id + ')">Rate Event</button>' : 
                            '<button class="btn btn-small btn-secondary" onclick="clubProfile.viewTicket(' + event.id + ')">View Ticket</button>')
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
            day: 'numeric'
        };
        return date.toLocaleDateString('en-US', options);
    }

    toggleMode() {
        this.isPublicView = !this.isPublicView;
        const overlay = document.getElementById('publicViewOverlay');
        const modeIcon = document.getElementById('modeIcon');
        const modeText = document.getElementById('modeText');

        if (this.isPublicView) {
            overlay.classList.add('active');
            modeIcon.className = 'fas fa-user-edit';
            modeText.textContent = 'Edit Mode';
            this.enablePublicView();
        } else {
            overlay.classList.remove('active');
            modeIcon.className = 'fas fa-eye';
            modeText.textContent = 'Public View';
            this.disablePublicView();
        }
    }

    enablePublicView() {
        document.querySelectorAll('.btn:not(.btn-primary):not(.filter-btn):not(.nav-item)').forEach(btn => {
            if (!btn.closest('.profile-actions') && !btn.closest('.event-actions')) {
                btn.style.display = 'none';
            }
        });

        const settingsTab = document.querySelector('[data-tab="settings"]');
        const preferencesTab = document.querySelector('[data-tab="preferences"]');
        if (settingsTab) settingsTab.style.display = 'none';
        if (preferencesTab) preferencesTab.style.display = 'none';

        if (this.currentTab === 'settings' || this.currentTab === 'preferences') {
            this.showTab('personal');
        }

        this.applyPrivacySettings();
    }

    disablePublicView() {
        document.querySelectorAll('[style*="display: none"]').forEach(el => {
            el.style.display = '';
        });
    }

    applyPrivacySettings() {
        const showContact = document.getElementById('showContact')?.checked;
        const showEventHistory = document.getElementById('showEventHistory')?.checked;

        if (!showContact) {
            document.querySelectorAll('#email, #phone').forEach(input => {
                const originalValue = input.value;
                input.value = 'Hidden for privacy';
                input.style.fontStyle = 'italic';
                input.dataset.originalValue = originalValue;
            });
        }

        if (!showEventHistory) {
            document.getElementById('events').style.display = 'none';
            document.querySelector('[data-tab="events"]').style.display = 'none';
        }
    }

    toggleEdit(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input, textarea');
        const actions = form.querySelector('.form-actions');
        const isEditing = !inputs[0].readOnly;

        inputs.forEach(input => {
            input.readOnly = isEditing;
        });

        if (actions) {
            actions.style.display = isEditing ? 'none' : 'flex';
        }
    }

    saveOrganizationInfo() {
        const formData = {
            name: document.getElementById('orgName').value,
            type: document.getElementById('orgType').value,
            university: document.getElementById('university').value,
            faculty: document.getElementById('faculty').value,
            department: document.getElementById('department').value,
            contactEmail: document.getElementById('contactEmail').value,
            contactPhone: document.getElementById('contactPhone').value,
            establishedYear: document.getElementById('establishedYear').value,
            memberCount: document.getElementById('memberCount').value,
            bio: document.getElementById('bio').value,
            mission: document.getElementById('mission').value
        };

        // Update organizationData
        Object.assign(this.organizationData, formData);

        this.showNotification('Organization information updated successfully!', 'success');
        
        // Update profile display
        document.getElementById('profileName').textContent = formData.name;
        const profileBioElements = document.querySelectorAll('#profileBio');
        profileBioElements.forEach(el => el.textContent = formData.bio);

        // Exit edit mode
        this.toggleEdit('organization-form');
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
                        (filter === 'featured' && card.querySelector('.event-badge.featured'));
            
            card.style.display = show ? 'block' : 'none';
        });

        this.showNotification(`Showing ${filter} events`, 'info');
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

    searchMembers(query) {
        const memberCards = document.querySelectorAll('.member-card');
        const searchTerm = query.toLowerCase();

        memberCards.forEach(card => {
            const name = card.querySelector('h4').textContent.toLowerCase();
            const role = card.querySelector('.member-role').textContent.toLowerCase();
            const details = card.querySelector('.member-details').textContent.toLowerCase();
            
            const matches = name.includes(searchTerm) || 
                          role.includes(searchTerm) || 
                          details.includes(searchTerm);
            
            card.style.display = matches ? 'block' : 'none';
        });
    }

    manageLeadership() {
        this.showNotification('Opening leadership management panel...', 'info');
        // Implement leadership management functionality
    }

    addAdmin() {
        this.showNotification('Opening add administrator dialog...', 'info');
        // Implement add admin functionality
    }

    removeAdmin(adminId) {
        if (confirm('Are you sure you want to remove this administrator?')) {
            this.showNotification(`Administrator ${adminId} removed successfully`, 'success');
            // Implement remove admin functionality
        }
    }

    exportData() {
        this.showNotification('Preparing organization data export...', 'info');
        // Implement data export functionality
    }

    exportMembers() {
        this.showNotification('Preparing member list export...', 'info');
        // Implement member export functionality
    }

    exportEvents() {
        this.showNotification('Preparing event history export...', 'info');
        // Implement event export functionality
    }

    deactivateOrganization() {
        if (confirm('Are you sure you want to deactivate this organization? This action can be reversed.')) {
            this.showNotification('Organization deactivation process started', 'warning');
            // Implement deactivation logic
        }
    }

    transferOwnership() {
        if (confirm('Are you sure you want to transfer ownership of this organization?')) {
            this.showNotification('Opening ownership transfer dialog...', 'info');
            // Implement ownership transfer logic
        }
    }

    deleteOrganization() {
        if (confirm('Are you sure you want to delete this organization? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all organization data.')) {
                this.showNotification('Organization deletion process started', 'error');
                // Implement deletion logic
            }
        }
    }

    saveSocialLinks() {
        const socialData = {
            instagram: document.getElementById('instagram').value,
            facebook: document.getElementById('facebook').value,
            linkedin: document.getElementById('linkedin').value
        };

        this.showNotification('Social links updated successfully!', 'success');
        this.toggleEdit('social-form');
    }

    saveSettings() {
        const settings = {
            username: document.getElementById('username').value,
            timezone: document.getElementById('timezone').value,
            language: document.getElementById('language').value
        };

        this.showNotification('Settings saved successfully!', 'success');
    }

    addTag() {
        const newTagInput = document.getElementById('newTag');
        const tagValue = newTagInput.value.trim();

        if (tagValue && !this.interests.includes(tagValue)) {
            this.interests.push(tagValue);
            this.loadInterests();
            newTagInput.value = '';
            this.showNotification('Interest added successfully!', 'success');
        }
    }

    removeInterest(interest) {
        const index = this.interests.indexOf(interest);
        if (index > -1) {
            this.interests.splice(index, 1);
            this.loadInterests();
            this.showNotification('Interest removed successfully!', 'info');
        }
    }

    updatePrivacySetting(settingId, value) {
        console.log(`Privacy setting ${settingId} set to ${value}`);
        this.showNotification(`Privacy setting updated!`, 'info');
    }

    updateEventPreference(preference, isChecked) {
        console.log(`Event preference ${preference} set to ${isChecked}`);
        this.showNotification('Preferences updated!', 'success');
    }

    uploadImage() {
        this.openModal('imageUploadModal');
    }

    handleImageUpload(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('profileImage').src = e.target.result;
                this.userData.avatar = e.target.result;
                this.showNotification('Profile picture updated!', 'success');
                this.closeModal('imageUploadModal');
            };
            reader.readAsDataURL(file);
        }
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

    viewEventDetails(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Opening details for ${event.title}`, 'info');
            // Implement event details modal or navigation
        }
    }

    manageEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Managing ${event.title}`, 'info');
            // Implement event management functionality
        }
    }

    rateEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Rating ${event.title}`, 'info');
            // Implement rating modal
        }
    }

    viewTicket(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.showNotification(`Viewing ticket for ${event.title}`, 'info');
            // Implement ticket view
        }
    }

    deactivateAccount() {
        if (confirm('Are you sure you want to deactivate your account? This action can be reversed.')) {
            this.showNotification('Account deactivation process started', 'warning');
            // Implement deactivation logic
        }
    }

    deleteAccount() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all your data.')) {
                this.showNotification('Account deletion process started', 'error');
                // Implement deletion logic
            }
        }
    }

    showNotification(message, type = 'info') {
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
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 2000;
            animation: slideInRight 0.3s ease;
            max-width: 400px;
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
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
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
            success: '#4CAF50',
            error: '#f44336',
            warning: '#ff9800',
            info: '#2196F3'
        };
        return colors[type] || '#2196F3';
    }

    editProfile() {
        this.showTab('personal');
        this.toggleEdit('personal-form');
    }

    cancelEdit(formId) {
        // Restore original values
        this.loadUserData();
        this.toggleEdit(formId);
    }
}

// Global functions for onclick handlers
function toggleMode() {
    clubProfile.toggleMode();
}

function uploadImage() {
    clubProfile.uploadImage();
}

function editProfile() {
    clubProfile.editProfile();
}

function toggleEdit(formId) {
    clubProfile.toggleEdit(formId);
}

function saveOrganizationInfo() {
    clubProfile.saveOrganizationInfo();
}

function saveSocialLinks() {
    clubProfile.saveSocialLinks();
}

function cancelEdit(formId) {
    clubProfile.cancelEdit(formId);
}

function addTag() {
    clubProfile.addTag();
}

function closeModal(modalId) {
    clubProfile.closeModal(modalId);
}

function deactivateOrganization() {
    clubProfile.deactivateOrganization();
}

function transferOwnership() {
    clubProfile.transferOwnership();
}

function deleteOrganization() {
    clubProfile.deleteOrganization();
}

function manageLeadership() {
    clubProfile.manageLeadership();
}

function addAdmin() {
    clubProfile.addAdmin();
}

function removeAdmin(adminId) {
    clubProfile.removeAdmin(adminId);
}

function exportData() {
    clubProfile.exportData();
}

function exportMembers() {
    clubProfile.exportMembers();
}

function exportEvents() {
    clubProfile.exportEvents();
}

// Cover photo upload functions
function uploadCover() {
    document.getElementById('coverInput').click();
}

function changeCover(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const coverImg = document.getElementById('coverPhoto');
            if (coverImg) {
                coverImg.src = e.target.result;
                clubProfile.showNotification('Cover photo updated successfully!', 'success');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Profile photo upload functions
function uploadProfileImage() {
    document.getElementById('profileInput').click();
}

function changeProfileImage(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const profileImg = document.getElementById('profileImage');
            if (profileImg) {
                profileImg.src = e.target.result;
                clubProfile.showNotification('Organization logo updated successfully!', 'success');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Initialize the club profile manager when DOM is loaded
let clubProfile;
document.addEventListener('DOMContentLoaded', () => {
    clubProfile = new ClubProfile();
    // Add some extra interactivity
    document.addEventListener('keydown', (e) => {
        // ESC key to close modals
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                clubProfile.closeModal(modal.id);
            });
        }
    });

    // Add loading states to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('loading')) {
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

    // Add smooth scrolling to internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Privacy Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize privacy toggles
    const privacyToggles = [
        { toggleId: 'emailPrivacy', labelId: 'email' },
        { toggleId: 'phonePrivacy', labelId: 'phone' },
        { toggleId: 'currentCityPrivacy', labelId: 'currentCity' },
        { toggleId: 'homeTownPrivacy', labelId: 'homeTown' }
    ];

    privacyToggles.forEach(({ toggleId, labelId }) => {
        const toggle = document.getElementById(toggleId);
        const label = document.querySelector(`label[for="${labelId}"]`);
        
        if (toggle && label) {
            const statusText = label.querySelector('small');
            
            // Update status text on toggle change
            toggle.addEventListener('change', function() {
                if (statusText) {
                    statusText.textContent = this.checked ? 'Public' : 'Private';
                    statusText.style.color = this.checked ? '#4A5BCC' : '#666';
                }
            });
            
            // Initialize status text color
            if (statusText) {
                statusText.style.color = toggle.checked ? '#4A5BCC' : '#666';
            }
        }
    });
});

// Add CSS for loading states
const loadingStyle = document.createElement('style');
loadingStyle.textContent = `
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(loadingStyle);