class UniPulseProfile {
    constructor() {
        this.currentTab = 'personal';
        this.isPublicView = false;
        this.userData = {
            firstName: 'Vinuja',
            lastName: 'Wakishta',
            email: 'vinuja@unipulse.com',
            phone: '+1 (555) 123-4567',
            bio: 'Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.',
            location: 'San Francisco, CA',
            website: 'https://vinuja.dev',
            avatar: 'https://avatars.githubusercontent.com/u/vinujawakishta?v=4'
        };
        
        this.events = [
            {
                id: 1,
                title: 'AI & Machine Learning Summit',
                date: '2024-10-20',
                location: 'Berkeley, CA',
                image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'registered',
                description: 'Deep dive into the latest AI and ML technologies with industry experts.'
            },
            {
                id: 2,
                title: 'Startup Pitch Competition',
                date: '2024-09-25',
                location: 'Oakland, CA',
                image: 'https://images.unsplash.com/photo-1559223607-b4d0555ae227?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'registered',
                description: 'Exciting competition featuring innovative startup pitches and networking opportunities.'
            },
            {
                id: 3,
                title: 'Networking Meetup',
                date: '2024-09-01',
                location: 'San Jose, CA',
                image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop',
                category: 'upcoming',
                type: 'registered',
                description: 'Monthly networking event for professionals in the tech industry.'
            }
        ];
        
        this.interests = ['Event Planning', 'Web Development', 'Networking', 'Public Speaking', 'JavaScript', 'Project Management'];
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
        const currentDate = new Date('2024-08-25'); // Using the provided current date
        
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
        // Personal info form
        const personalForm = document.getElementById('personal-form');
        if (personalForm) {
            personalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.savePersonalInfo();
            });
        }

        // Role button selection
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.selectRole(e.target.dataset.role);
            });
        });

        // Event preference buttons
        document.querySelectorAll('.preference-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.togglePreference(e.target);
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
        // Load user data into form fields
        Object.keys(this.userData).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                element.value = this.userData[key];
            }
        });

        // Update profile display
        const profileName = document.getElementById('profileName');
        const profileBio = document.getElementById('profileBio');
        const profileImage = document.getElementById('profileImage');

        if (profileName) profileName.textContent = `${this.userData.firstName} ${this.userData.lastName}`;
        if (profileBio) profileBio.textContent = this.userData.bio;
        if (profileImage) profileImage.src = this.userData.avatar;

        // Load interests
        this.loadInterests();
        this.updateStats();
    }

    loadInterests() {
        const tagsContainer = document.getElementById('tagsContainer');
        if (!tagsContainer) return;

        tagsContainer.innerHTML = '';
        this.interests.forEach(interest => {
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.textContent = interest;
            tag.addEventListener('click', () => this.removeInterest(interest));
            tagsContainer.appendChild(tag);
        });
    }

    updateStats() {
        const attendedCount = this.events.filter(e => e.type === 'attended').length;
        const organizedCount = this.events.filter(e => e.type === 'organized').length;
        const totalConnections = 42; // This would come from your backend

        const eventsAttended = document.getElementById('eventsAttended');
        const eventsOrganized = document.getElementById('eventsOrganized');
        const connections = document.getElementById('connections');

        if (eventsAttended) eventsAttended.textContent = attendedCount;
        if (eventsOrganized) eventsOrganized.textContent = organizedCount;
        if (connections) connections.textContent = totalConnections;
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
        const badgeText = event.category === 'upcoming' ? 'Registered' : 'Attended';

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
                <p class="event-description">${event.description}</p>
                <div class="event-actions">
                    <button class="btn btn-small btn-primary" onclick="profileManager.viewEventDetails(${event.id})">
                        View Details
                    </button>
                    ${event.category === 'past' ? 
                        '<button class="btn btn-small btn-secondary" onclick="profileManager.rateEvent(' + event.id + ')">Rate Event</button>' : 
                        '<button class="btn btn-small btn-secondary" onclick="profileManager.viewTicket(' + event.id + ')">View Ticket</button>'
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

    savePersonalInfo() {
        const formData = {
            firstName: document.getElementById('firstName').value,
            lastName: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            bio: document.getElementById('bio').value,
            location: document.getElementById('location').value,
            website: document.getElementById('website').value
        };

        // Update userData
        Object.assign(this.userData, formData);

        this.showNotification('Personal information updated successfully!', 'success');
        
        // Update profile display
        document.getElementById('profileName').textContent = `${formData.firstName} ${formData.lastName}`;
        document.getElementById('profileBio').textContent = formData.bio;

        // Exit edit mode
        this.toggleEdit('personal-form');
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
    profileManager.toggleMode();
}

function uploadImage() {
    profileManager.uploadImage();
}

function editProfile() {
    profileManager.editProfile();
}

function toggleEdit(formId) {
    profileManager.toggleEdit(formId);
}

function savePersonalInfo() {
    profileManager.savePersonalInfo();
}

function saveSocialLinks() {
    profileManager.saveSocialLinks();
}

function cancelEdit(formId) {
    profileManager.cancelEdit(formId);
}

function addTag() {
    profileManager.addTag();
}

function closeModal(modalId) {
    profileManager.closeModal(modalId);
}

function deactivateAccount() {
    profileManager.deactivateAccount();
}

function deleteAccount() {
    profileManager.deleteAccount();
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
                profileManager.showNotification('Cover photo updated successfully!', 'success');
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
                profileManager.showNotification('Profile photo updated successfully!', 'success');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Initialize the profile manager when DOM is loaded
let profileManager;
document.addEventListener('DOMContentLoaded', () => {
    profileManager = new UniPulseProfile();
    
    // Add some extra interactivity
    document.addEventListener('keydown', (e) => {
        // ESC key to close modals
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                profileManager.closeModal(modal.id);
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