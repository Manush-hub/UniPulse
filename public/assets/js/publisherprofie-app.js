class ClubProfile {
    constructor() {
        this.currentTab = 'about';
        this.organizationData = {
            name: 'Tech Innovation Society',
            type: 'student-org',
            university: 'University of California, Berkeley',
            faculty: 'School of Engineering',
            officialEmail: 'contact@techinnovationsociety.org',
            contactNumber: '+1 (510) 642-1000',
            address: '123 Tech Lane, Berkeley, CA 94720',
            establishedYear: 2018,
            memberCount: 245,
            adminisName: 'Sarah Johnson',
            adminisContact: '+1 (510) 123-4567',
            adminRole: 'President',
            adminEmail: 'sarah.johnson@berkeley.edu',
            headline: 'Student Organization',
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
        this.initializeEventsFilter();
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
                            // this.showNotification('Cover photo updated successfully!', 'success');
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

    initializeEventsFilter() {
        // Set default filter to upcoming events
        this.filterEvents('upcoming');
    }

    togglePreference(button) {
        button.classList.toggle('active');
        const preference = button.dataset.preference;
        const isActive = button.classList.contains('active');
        
        if (isActive) {
            // this.showNotification(`${preference.charAt(0).toUpperCase() + preference.slice(1)} preference added`, 'success');
        } else {
            // this.showNotification(`${preference.charAt(0).toUpperCase() + preference.slice(1)} preference removed`, 'info');
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

        // this.showNotification(`Role set to ${role.charAt(0).toUpperCase() + role.slice(1)}`, 'info');
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
        const badgeText = event.category === 'upcoming' ? 'Upcoming' : 'Past Event';

        card.innerHTML = `
            <div class="event-image">
                <img src="${event.image}" alt="${event.title}" loading="lazy">
                <span class="event-badge ${badgeClass}">${badgeText}</span>
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
                    ${event.category === 'upcoming' ? 
                        '<button class="btn btn-small btn-secondary" onclick="clubProfile.manageEvent(' + event.id + ')">Manage Event</button>' : 
                        '<button class="btn btn-small btn-secondary" onclick="clubProfile.viewReport(' + event.id + ')">View Report</button>'
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

    saveOrganizationInfo() {
        const formData = {
            name: document.getElementById('orgName').value,
            type: document.getElementById('orgType').value,
            university: document.getElementById('university').value,
            faculty: document.getElementById('faculty').value,
            officialEmail: document.getElementById('officialEmail').value,
            contactNumber: document.getElementById('contactNumber').value,
            address: document.getElementById('address').value,
            establishedYear: document.getElementById('establishedYear').value,
            memberCount: document.getElementById('memberCount').value,
            adminisName: document.getElementById('adminisName').value,
            adminisContact: document.getElementById('adminisContact').value,
            adminRole: document.getElementById('adminRole').value,
            adminEmail: document.getElementById('adminEmail').value,
            headline: document.getElementById('headline').value,
            bio: document.getElementById('bio').value,
            mission: document.getElementById('mission').value
        };

        // Update organizationData
        Object.assign(this.organizationData, formData);

        // this.showNotification('Organization information updated successfully!', 'success');
        
        // Update profile display
        document.getElementById('profileName').textContent = formData.name;
        const profileBioElements = document.querySelectorAll('#profileBio');
        profileBioElements.forEach(el => el.textContent = formData.bio);
    }

    cancelOrganizationInfo() {
        // Reset form to original values
        const form = document.getElementById('organization-form');
        const inputs = form.querySelectorAll('input, textarea, select');
        
        // Reset to original values (you can implement actual reset logic here)
        // this.showNotification('Changes cancelled', 'info');
    }

    cancelSocialLinks() {
        // Reset social links form to original values
        const form = document.getElementById('social-form');
        const inputs = form.querySelectorAll('input');
        
        // Reset to original values (you can implement actual reset logic here)
        // this.showNotification('Changes cancelled', 'info');
    }

    cancelSecuritySettings() {
        // Reset security form to original values
        const form = document.getElementById('security-form');
        const inputs = form.querySelectorAll('input');
        
        // Clear password fields
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        
        // this.showNotification('Changes cancelled', 'info');
    }

    filterEvents(filter) {
        // Remove active class from all filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

        // Filter events - only show upcoming or past events
        const eventCards = document.querySelectorAll('.event-card');
        eventCards.forEach(card => {
            const show = card.dataset.category === filter;
            card.style.display = show ? 'block' : 'none';
        });

        const filterText = filter === 'upcoming' ? 'upcoming events' : 'past events';
        // Removed notification popup
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

    manageExecutiveCommittee() {
        document.getElementById('executiveCommitteeModal').style.display = 'flex';
        document.getElementById('executiveCommitteeModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Load existing committee data if available
        this.loadExistingCommitteeData();
    }

    loadExistingCommitteeData() {
        // This function would load existing committee member data from the database
        // For now, it's a placeholder for future implementation
        console.log('Loading existing committee data...');
    }

    addAdmin() {
        document.getElementById('addAdminModal').style.display = 'flex';
        document.getElementById('addAdminModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Reset form
        this.clearAddAdminForm();
    }

    clearAddAdminForm() {
        // Clear all input fields
        const form = document.getElementById('addAdminForm');
        if (form) {
            form.reset();
        }
        
        // Clear photo preview
        const preview = document.getElementById('adminPhotoPreview');
        const uploadPlaceholder = document.querySelector('.admin-photo-upload .upload-placeholder');
        
        if (preview) {
            preview.style.display = 'none';
            preview.src = '';
        }
        
        if (uploadPlaceholder) {
            uploadPlaceholder.style.display = 'flex';
        }
    }

    removeAdmin(adminId) {
        if (confirm('Are you sure you want to remove this administrator?')) {
            // this.showNotification(`Administrator ${adminId} removed successfully`, 'success');
            // Implement remove admin functionality
        }
    }

    deactivateOrganization() {
        if (confirm('Are you sure you want to deactivate this organization? This action can be reversed.')) {
            // this.showNotification('Organization deactivation process started', 'warning');
            // Implement deactivation logic
        }
    }

    transferOwnership() {
        if (confirm('Are you sure you want to transfer ownership of this organization?')) {
            // this.showNotification('Opening ownership transfer dialog...', 'info');
            // Implement ownership transfer logic
        }
    }

    deleteOrganization() {
        if (confirm('Are you sure you want to delete this organization? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all organization data.')) {
                // this.showNotification('Organization deletion process started', 'error');
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

        // this.showNotification('Social links updated successfully!', 'success');
    }

    saveSettings() {
        const settings = {
            username: document.getElementById('username').value,
            timezone: document.getElementById('timezone').value,
            language: document.getElementById('language').value
        };

        // this.showNotification('Settings saved successfully!', 'success');
    }

    addTag() {
        const newTagInput = document.getElementById('newTag');
        const tagValue = newTagInput.value.trim();

        if (tagValue && !this.interests.includes(tagValue)) {
            this.interests.push(tagValue);
            this.loadInterests();
            newTagInput.value = '';
            // this.showNotification('Interest added successfully!', 'success');
        }
    }

    removeInterest(interest) {
        const index = this.interests.indexOf(interest);
        if (index > -1) {
            this.interests.splice(index, 1);
            this.loadInterests();
            // this.showNotification('Interest removed successfully!', 'info');
        }
    }

    updateEventPreference(preference, isChecked) {
        console.log(`Event preference ${preference} set to ${isChecked}`);
        // this.showNotification('Preferences updated!', 'success');
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
                // this.showNotification('Profile picture updated!', 'success');
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
            // this.showNotification(`Opening details for ${event.title}`, 'info');
            // Implement event details modal or navigation
        }
    }

    manageEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            // this.showNotification(`Managing ${event.title}`, 'info');
            // Implement event management functionality
        }
    }

    viewReport(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            // this.showNotification(`Viewing report for ${event.title}`, 'info');
            // Implement event report functionality
        }
    }

    rateEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            // this.showNotification(`Rating ${event.title}`, 'info');
            // Implement rating modal
        }
    }

    viewTicket(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            // this.showNotification(`Viewing ticket for ${event.title}`, 'info');
            // Implement ticket view
        }
    }

    deactivateAccount() {
        if (confirm('Are you sure you want to deactivate your account? This action can be reversed.')) {
            // this.showNotification('Account deactivation process started', 'warning');
            // Implement deactivation logic
        }
    }

    deleteAccount() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all your data.')) {
                // this.showNotification('Account deletion process started', 'error');
                // Implement deletion logic
            }
        }
    }

    // Notification functions removed
}

// Global functions for onclick handlers
function uploadImage() {
    clubProfile.uploadImage();
}

function saveOrganizationInfo() {
    clubProfile.saveOrganizationInfo();
}

function saveSocialLinks() {
    clubProfile.saveSocialLinks();
}

function cancelOrganizationInfo() {
    clubProfile.cancelOrganizationInfo();
}

function cancelSocialLinks() {
    clubProfile.cancelSocialLinks();
}

function cancelSecuritySettings() {
    clubProfile.cancelSecuritySettings();
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

function manageExecutiveCommittee() {
    clubProfile.manageExecutiveCommittee();
}

function addAdmin() {
    clubProfile.addAdmin();
}

function removeAdmin(adminId) {
    clubProfile.removeAdmin(adminId);
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
                // clubProfile.showNotification('Cover photo updated successfully!', 'success');
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
                // clubProfile.showNotification('Organization logo updated successfully!', 'success');
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

// Gallery Functionality
let galleryPhotos = [
    {
        id: 1,
        title: 'Organization Events',
        description: 'Highlights from our recent tech conferences and networking events',
        images: [
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=600&h=400&fit=crop'
        ]
    },
    {
        id: 2,
        title: 'Team Building',
        description: 'Building stronger connections through collaborative activities',
        images: [
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=600&h=400&fit=crop'
        ]
    },
    {
        id: 3,
        title: 'Community Outreach',
        description: 'Making a positive impact in our local community',
        images: [
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&h=400&fit=crop'
        ]
    }
];

let currentEditingGalleryId = null;
const MAX_GALLERY_ENTRIES = 5;
const MAX_PHOTOS_PER_ENTRY = 10;

// Add Gallery Photo
function addGalleryPhoto() {
    if (galleryPhotos.length >= MAX_GALLERY_ENTRIES) {
        // clubProfile.showNotification('You can only create a maximum of 5 gallery entries.', 'warning');
        return;
    }
    
    currentEditingGalleryId = null;
    document.getElementById('galleryModalTitle').textContent = 'Add Photo Gallery';
    document.getElementById('galleryTitle').value = '';
    document.getElementById('galleryDescription').value = '';
    
    // Reset all file inputs and previews
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const fileInput = document.getElementById(`galleryFile${i}`);
        const preview = document.getElementById(`galleryPreview${i}`);
        const uploadContent = document.querySelector(`#galleryFile${i}`)?.parentElement?.querySelector('.upload-content');
        
        if (fileInput) fileInput.value = '';
        if (preview) {
            preview.style.display = 'none';
            preview.src = '';
        }
        if (uploadContent) uploadContent.style.display = 'flex';
    }
    
    // Show modal
    document.getElementById('galleryPhotoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Edit Gallery Item
function editGalleryItem(galleryId) {
    const photo = galleryPhotos.find(p => p.id === galleryId);
    if (!photo) return;
    
    currentEditingGalleryId = galleryId;
    document.getElementById('galleryModalTitle').textContent = 'Edit Photo';
    document.getElementById('galleryTitle').value = photo.title;
    document.getElementById('galleryDescription').value = photo.description;
    
    // Show modal
    document.getElementById('galleryPhotoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Delete Gallery Item
function deleteGalleryItem(galleryId) {
    if (confirm('Are you sure you want to delete this photo?')) {
        galleryPhotos = galleryPhotos.filter(p => p.id !== galleryId);
        // clubProfile.showNotification('Photo deleted successfully!', 'success');
    }
}

// Preview Gallery Image
function previewGalleryImage(event, photoIndex) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        // clubProfile.showNotification('File size must be less than 5MB', 'error');
        event.target.value = '';
        return;
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        // clubProfile.showNotification('Please select a valid image file', 'error');
        event.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById(`galleryPreview${photoIndex}`);
        const uploadContent = event.target.parentElement.querySelector('.upload-content');
        
        preview.src = e.target.result;
        preview.style.display = 'block';
        uploadContent.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

// Save Gallery Photo
function saveGalleryPhoto() {
    const title = document.getElementById('galleryTitle').value.trim();
    const description = document.getElementById('galleryDescription').value.trim();
    
    // Validation
    if (!title) {
        // clubProfile.showNotification('Please enter a title for the gallery', 'error');
        return;
    }
    
    if (!description) {
        // clubProfile.showNotification('Please enter a description for the gallery', 'error');
        return;
    }
    
    if (title.length > 50) {
        // clubProfile.showNotification('Title must be 50 characters or less', 'error');
        return;
    }
    
    if (description.length > 150) {
        // clubProfile.showNotification('Description must be 150 characters or less', 'error');
        return;
    }
    
    // Collect uploaded images
    const images = [];
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const fileInput = document.getElementById(`galleryFile${i}`);
        const preview = document.getElementById(`galleryPreview${i}`);
        
        if (fileInput && fileInput.files[0] && preview && preview.src) {
            images.push(preview.src);
        }
    }
    
    if (images.length === 0 && !currentEditingGalleryId) {
        // clubProfile.showNotification('Please upload at least one image', 'error');
        return;
    }
    
    if (currentEditingGalleryId) {
        // Update existing gallery entry
        const photoIndex = galleryPhotos.findIndex(p => p.id === currentEditingGalleryId);
        if (photoIndex !== -1) {
            galleryPhotos[photoIndex].title = title;
            galleryPhotos[photoIndex].description = description;
            if (images.length > 0) {
                galleryPhotos[photoIndex].images = images;
            }
        }
        // clubProfile.showNotification('Gallery updated successfully!', 'success');
    } else {
        // Add new gallery entry
        if (galleryPhotos.length >= MAX_GALLERY_ENTRIES) {
            // clubProfile.showNotification('You can only create a maximum of 5 gallery entries.', 'warning');
            return;
        }
        
        const newGallery = {
            id: Date.now(),
            title: title,
            description: description,
            images: images
        };
        
        galleryPhotos.push(newGallery);
        // clubProfile.showNotification('Gallery added successfully!', 'success');
    }
    
    closeGalleryModal();
}

// Close Gallery Modal
function closeGalleryModal() {
    document.getElementById('galleryPhotoModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('galleryTitle').value = '';
    document.getElementById('galleryDescription').value = '';
    
    // Reset all file inputs and previews
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const fileInput = document.getElementById(`galleryFile${i}`);
        const preview = document.getElementById(`galleryPreview${i}`);
        const uploadContent = document.querySelector(`#galleryFile${i}`)?.parentElement?.querySelector('.upload-content');
        
        if (fileInput) fileInput.value = '';
        if (preview) {
            preview.style.display = 'none';
            preview.src = '';
        }
        if (uploadContent) uploadContent.style.display = 'flex';
    }
    
    currentEditingGalleryId = null;
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

// Close gallery modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('galleryPhotoModal');
    if (event.target === modal) {
        closeGalleryModal();
    }
});

// Close gallery modal with escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('galleryPhotoModal');
        if (modal && modal.style.display === 'flex') {
            closeGalleryModal();
        }
        
        const committeeModal = document.getElementById('executiveCommitteeModal');
        if (committeeModal && committeeModal.style.display === 'flex') {
            closeExecutiveCommitteeModal();
        }
        
        const addAdminModal = document.getElementById('addAdminModal');
        if (addAdminModal && addAdminModal.style.display === 'flex') {
            closeAddAdminModal();
        }
    }
});

// Executive Committee Management Functions
function closeExecutiveCommitteeModal() {
    document.getElementById('executiveCommitteeModal').style.display = 'none';
    document.getElementById('executiveCommitteeModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    clearCommitteeForm();
}

function clearCommitteeForm() {
    // Clear all input fields
    const positions = ['president', 'vicePresident', 'secretary', 'treasurer'];
    
    positions.forEach(position => {
        const firstNameInput = document.getElementById(`${position}FirstName`);
        const lastNameInput = document.getElementById(`${position}LastName`);
        
        if (firstNameInput) firstNameInput.value = '';
        if (lastNameInput) lastNameInput.value = '';
        
        // Clear photo preview
        const preview = document.getElementById(`${position}Preview`);
        const uploadPlaceholder = document.querySelector(`#${position}Photo`)?.parentElement?.querySelector('.upload-placeholder');
        
        if (preview) {
            preview.style.display = 'none';
            preview.src = '';
        }
        
        if (uploadPlaceholder) {
            uploadPlaceholder.style.display = 'flex';
        }
        
        // Reset file input
        const fileInput = document.getElementById(`${position}Photo`);
        if (fileInput) {
            fileInput.value = '';
        }
    });
}

function previewCommitteePhoto(event, position) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            // clubProfile.showNotification('Please select a valid image file (JPG, PNG)', 'error');
            event.target.value = '';
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            // clubProfile.showNotification('File size must be less than 5MB', 'error');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(`${position}Preview`);
            const uploadPlaceholder = event.target.parentElement.querySelector('.upload-placeholder');
            
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (uploadPlaceholder) {
                uploadPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

function saveExecutiveCommittee() {
    // Collect data from all committee member forms
    const committeeData = {
        president: getCommitteeMemberData('president'),
        vicePresident: getCommitteeMemberData('vicePresident'),
        secretary: getCommitteeMemberData('secretary'),
        treasurer: getCommitteeMemberData('treasurer')
    };
    
    // Validate that at least one member is filled
    const hasData = Object.values(committeeData).some(member => 
        member.firstName.trim() !== '' || member.lastName.trim() !== '' || member.hasPhoto
    );
    
    if (!hasData) {
        // clubProfile.showNotification('Please add at least one committee member', 'error');
        return;
    }
    
    // Show loading state
    const saveBtn = document.querySelector('#executiveCommitteeModal .btn-primary');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    // Simulate API call (replace with actual implementation)
    setTimeout(() => {
        console.log('Committee data to save:', committeeData);
        
        // Update the executive committee display
        updateExecutiveCommitteeDisplay(committeeData);
        
        // Reset button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
        
        // Close modal
        closeExecutiveCommitteeModal();
        
        // Show success message
        showSuccessMessage('Executive committee updated successfully!');
    }, 1000);
}

function getCommitteeMemberData(position) {
    const firstNameInput = document.getElementById(`${position}FirstName`);
    const lastNameInput = document.getElementById(`${position}LastName`);
    const photoInput = document.getElementById(`${position}Photo`);
    
    const firstName = firstNameInput ? firstNameInput.value.trim() : '';
    const lastName = lastNameInput ? lastNameInput.value.trim() : '';
    const hasPhoto = photoInput && photoInput.files.length > 0;
    
    return {
        firstName,
        lastName,
        photo: hasPhoto ? photoInput.files[0] : null,
        hasPhoto
    };
}

function updateExecutiveCommitteeDisplay(committeeData) {
    // Update the committee cards on the main page
    const positions = [
        { key: 'president', title: 'President' },
        { key: 'vicePresident', title: 'Vice President' },
        { key: 'secretary', title: 'Secretary' },
        { key: 'treasurer', title: 'Treasurer' }
    ];
    
    const leadershipGrid = document.querySelector('.leadership-grid');
    if (!leadershipGrid) return;
    
    // Clear existing cards
    leadershipGrid.innerHTML = '';
    
    positions.forEach(position => {
        const memberData = committeeData[position.key];
        
        // Only create card if member has data
        if (memberData.firstName || memberData.lastName || memberData.hasPhoto) {
            const card = createCommitteeMemberCard(memberData, position.title);
            leadershipGrid.appendChild(card);
        }
    });
    
    // If no members, show placeholder
    if (leadershipGrid.children.length === 0) {
        leadershipGrid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">No executive committee members added yet.</p>';
    }
}

function createCommitteeMemberCard(memberData, position) {
    const card = document.createElement('div');
    card.className = 'member-card leadership';
    
    const fullName = `${memberData.firstName} ${memberData.lastName}`.trim() || 'No Name';
    const photoSrc = memberData.hasPhoto ? URL.createObjectURL(memberData.photo) : '/Applications/MAMP/htdocs/UniPulse/public/assets/images/logo.png';
    
    card.innerHTML = `
        <div class="member-avatar">
            <img src="${photoSrc}" alt="${fullName}">
        </div>
        <div class="member-info">
            <h4>${fullName}</h4>
            <p class="member-role">${position}</p>
        </div>
    `;
    
    return card;
}

function showSuccessMessage(message) {
    // Create and show a temporary success message
    const messageDiv = document.createElement('div');
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 1001;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
    `;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(messageDiv)) {
                document.body.removeChild(messageDiv);
            }
        }, 300);
    }, 3000);
}

// Close executive committee modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('executiveCommitteeModal');
    if (event.target === modal) {
        closeExecutiveCommitteeModal();
    }
    
    const addAdminModal = document.getElementById('addAdminModal');
    if (event.target === addAdminModal) {
        closeAddAdminModal();
    }
});

// Add Admin Modal Functions
function closeAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'none';
    document.getElementById('addAdminModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    clearAddAdminForm();
}

function clearAddAdminForm() {
    // Clear all input fields
    document.getElementById('adminFirstName').value = '';
    document.getElementById('adminLastName').value = '';
    document.getElementById('adminEmail').value = '';
    document.getElementById('adminRole').value = '';
    
    // Clear photo preview
    const preview = document.getElementById('adminPhotoPreview');
    const uploadPlaceholder = document.querySelector('.admin-photo-upload .upload-placeholder');
    
    if (preview) {
        preview.style.display = 'none';
        preview.src = '';
    }
    
    if (uploadPlaceholder) {
        uploadPlaceholder.style.display = 'flex';
    }
    
    // Reset file input
    const fileInput = document.getElementById('adminPhoto');
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Clear all checkboxes
    document.querySelectorAll('#addAdminModal input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function previewAdminPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            // clubProfile.showNotification('Please select a valid image file (JPG, PNG)', 'error');
            event.target.value = '';
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            // clubProfile.showNotification('File size must be less than 5MB', 'error');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('adminPhotoPreview');
            const uploadPlaceholder = document.querySelector('.admin-photo-upload .upload-placeholder');
            
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (uploadPlaceholder) {
                uploadPlaceholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

function saveNewAdmin() {
    // Get form data
    const firstName = document.getElementById('adminFirstName').value.trim();
    const lastName = document.getElementById('adminLastName').value.trim();
    const email = document.getElementById('adminEmail').value.trim();
    const role = document.getElementById('adminRole').value;
    const photoInput = document.getElementById('adminPhoto');
    
    // Validation
    if (!firstName || !lastName || !email || !role) {
        // clubProfile.showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        // clubProfile.showNotification('Please enter a valid email address', 'error');
        return;
    }
    
    // Get selected permissions
    const permissions = [];
    document.querySelectorAll('#addAdminModal input[type="checkbox"]:checked').forEach(checkbox => {
        permissions.push(checkbox.value);
    });
    
    // Create admin data object
    const adminData = {
        firstName,
        lastName,
        email,
        role,
        permissions,
        photo: photoInput.files.length > 0 ? photoInput.files[0] : null,
        hasPhoto: photoInput.files.length > 0
    };
    
    // Show loading state
    const saveBtn = document.querySelector('#addAdminModal .btn-primary');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    saveBtn.disabled = true;
    
    // Simulate API call (replace with actual implementation)
    setTimeout(() => {
        console.log('Admin data to save:', adminData);
        
        // Add admin to the list (in real implementation, this would update the DOM)
        addAdminToList(adminData);
        
        // Reset button state
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        // Close modal
        closeAddAdminModal();
        
        // Show success message
        // clubProfile.showNotification(`${firstName} ${lastName} has been added as an administrator!`, 'success');
    }, 1000);
}

function addAdminToList(adminData) {
    // This function would add the new admin to the admin list in the UI
    // In a real implementation, you would update the DOM here
    console.log('Adding admin to list:', adminData);
    
    // For demonstration, you could dynamically add to the admin list
    const adminList = document.querySelector('.admin-list');
    if (adminList) {
        const newAdminHTML = createAdminItemHTML(adminData);
        adminList.insertAdjacentHTML('beforeend', newAdminHTML);
    }
}

function createAdminItemHTML(adminData) {
    const fullName = `${adminData.firstName} ${adminData.lastName}`;
    const photoSrc = adminData.hasPhoto ? URL.createObjectURL(adminData.photo) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face';
    const roleDisplayMap = {
        'co-admin': 'Co-Administrator',
        'moderator': 'Moderator',
        'event-manager': 'Event Manager'
    };
    const roleDisplay = roleDisplayMap[adminData.role] || adminData.role;
    
    return `
        <div class="admin-item">
            <div class="admin-info">
                <div class="admin-avatar">
                    <img src="${photoSrc}" alt="${fullName}">
                </div>
                <div class="admin-details">
                    <h4>${fullName}</h4>
                    <p>${roleDisplay}</p>
                    <small>${adminData.email}</small>
                </div>
            </div>
            <div class="admin-actions">
                <button class="btn btn-small btn-danger" onclick="removeAdmin('${adminData.email}')">
                    <i class="fas fa-user-minus"></i>
                </button>
            </div>
        </div>
    `;
}