class UniPulseProfile {
    constructor() {
        this.currentTab = 'personal';
        this.userData = {
            firstName: 'Vinuja',
            lastName: 'Wakishta',
            email: 'vinuja@unipulse.com',
            phone: '+1 (555) 123-4567',
            university: 'University of Example',
            faculty: 'Faculty of Engineering',
            dob: '1995-06-15',
            gender: 'male',
            currentCity: 'San Francisco, CA',
            homeTown: 'Los Angeles, CA',
            role: 'student',
            headline: 'Uni Student',
            bio: 'Passionate about creating amazing events and connecting people through technology. Love organizing tech meetups and networking events.',
            location: 'San Francisco, CA',
            website: 'https://vinuja.dev',
            avatar: 'https://avatars.githubusercontent.com/u/vinujawakishta?v=4',
            // Social Links
            personalWebsite: '',
            facebook: 'https://facebook.com/vinujawakishta',
            instagram: 'https://instagram.com/vinujawakishta',
            telegram: '',
            linkedin: 'https://linkedin.com/in/vinujawakishta',
            github: '',
            xTwitter: '',
            discord: ''
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

        // Gender button selection
        document.querySelectorAll('.gender-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.selectGender(e.target.dataset.gender);
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

    selectGender(gender) {
        // Remove active class from all gender buttons
        document.querySelectorAll('.gender-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active class to selected button
        document.querySelector(`[data-gender="${gender}"]`).classList.add('active');

        // Update hidden input value
        document.getElementById('gender').value = gender;

        // this.showNotification(`Gender set to ${gender.charAt(0).toUpperCase() + gender.slice(1)}`, 'info');
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

        // this.showNotification('Personal information updated successfully!', 'success');
        
        // Update profile display
        document.getElementById('profileName').textContent = `${formData.firstName} ${formData.lastName}`;
        document.getElementById('profileBio').textContent = formData.bio;

        // Exit edit mode
        this.toggleEdit('personal-form');
    }

    cancelPersonalInfo() {
        // Restore original values from userData
        const fields = [
            { id: 'firstname', key: 'firstName' },
            { id: 'lastname', key: 'lastName' },
            { id: 'email', key: 'email' },
            { id: 'phone', key: 'phone' },
            { id: 'university', key: 'university' },
            { id: 'faculty', key: 'faculty' },
            { id: 'dob', key: 'dob' },
            { id: 'currentCity', key: 'currentCity' },
            { id: 'homeTown', key: 'homeTown' },
            { id: 'headline', key: 'headline' },
            { id: 'bio', key: 'bio' }
        ];

        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element && this.userData[field.key] !== undefined) {
                element.value = this.userData[field.key];
            }
        });

        // Reset gender selection
        const defaultGender = this.userData.gender || 'male';
        document.querySelectorAll('.gender-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-gender="${defaultGender}"]`)?.classList.add('active');
        document.getElementById('gender').value = defaultGender;

        // Reset role selection
        const defaultRole = this.userData.role || 'student';
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-role="${defaultRole}"]`)?.classList.add('active');
        document.getElementById('role').value = defaultRole;

        // this.showNotification('Changes cancelled, form restored to original values', 'info');
    }

    saveSocialLinks() {
        const socialData = {
            personalWebsite: document.getElementById('personal-website')?.value || '',
            facebook: document.getElementById('facebook')?.value || '',
            instagram: document.getElementById('instagram')?.value || '',
            telegram: document.getElementById('telegram')?.value || '',
            linkedin: document.getElementById('linkedin')?.value || '',
            github: document.getElementById('github')?.value || '',
            xTwitter: document.getElementById('x-twitter')?.value || '',
            discord: document.getElementById('discord')?.value || ''
        };

        // Update userData
        Object.assign(this.userData, socialData);

        // this.showNotification('Social links updated successfully!', 'success');
    }

    cancelSocialLinks() {
        // Restore original values from userData
        document.getElementById('personal-website').value = this.userData.personalWebsite || '';
        document.getElementById('facebook').value = this.userData.facebook || '';
        document.getElementById('instagram').value = this.userData.instagram || '';
        document.getElementById('telegram').value = this.userData.telegram || '';
        document.getElementById('linkedin').value = this.userData.linkedin || '';
        document.getElementById('github').value = this.userData.github || '';
        document.getElementById('x-twitter').value = this.userData.xTwitter || '';
        document.getElementById('discord').value = this.userData.discord || '';

        // this.showNotification('Changes cancelled, social links restored to original values', 'info');
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

    updatePrivacySetting(settingId, value) {
        console.log(`Privacy setting ${settingId} set to ${value}`);
        // this.showNotification(`Privacy setting updated!`, 'info');
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

function cancelPersonalInfo() {
    profileManager.cancelPersonalInfo();
}

function saveSocialLinks() {
    profileManager.saveSocialLinks();
}

function cancelSocialLinks() {
    profileManager.cancelSocialLinks();
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

// Gallery Functionality
let galleryPhotos = [
    {
        id: 1,
        title: 'Hackathon Victory',
        description: 'Celebrating 2nd place win at Berkeley Hackathon 2024',
        images: [
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop'
        ]
    },
    {
        id: 2,
        title: 'Research Presentation',
        description: 'Presenting climate prediction research at symposium',
        images: [
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=400&fit=crop'
        ]
    },
    {
        id: 3,
        title: 'Team Collaboration',
        description: 'Working with fellow students on group projects',
        images: [
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=400&fit=crop'
        ]
    }
];

let currentEditingGalleryId = null;
const MAX_GALLERY_ENTRIES = 5;
const MAX_PHOTOS_PER_ENTRY = 5;

// Add Gallery Photo
function addGalleryPhoto() {
    if (galleryPhotos.length >= MAX_GALLERY_ENTRIES) {
        showNotification('You can only create a maximum of 5 gallery entries.', 'warning');
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
        const uploadContent = document.querySelector(`#galleryFile${i}`).parentElement.querySelector('.upload-content');
        
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
    
    // Show current image in preview
    const preview = document.getElementById('galleryPreview');
    const uploadContent = document.querySelector('.upload-content');
    preview.src = photo.image;
    preview.style.display = 'block';
    uploadContent.style.display = 'none';
    
    // Hide file upload section for editing
    document.getElementById('galleryImageUpload').style.display = 'none';
    
    // Show modal
    document.getElementById('galleryPhotoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Delete Gallery Item
function deleteGalleryItem(galleryId) {
    if (confirm('Are you sure you want to delete this photo?')) {
        galleryPhotos = galleryPhotos.filter(p => p.id !== galleryId);
        renderGallery();
        showNotification('Photo deleted successfully!', 'success');
    }
}

// Preview Gallery Image
function previewGalleryImage(event, photoIndex) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('File size must be less than 5MB', 'error');
        event.target.value = '';
        return;
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showNotification('Please select a valid image file', 'error');
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
        showNotification('Please enter a title for the gallery', 'error');
        return;
    }
    
    if (!description) {
        showNotification('Please enter a description for the gallery', 'error');
        return;
    }
    
    if (title.length > 50) {
        showNotification('Title must be 50 characters or less', 'error');
        return;
    }
    
    if (description.length > 150) {
        showNotification('Description must be 150 characters or less', 'error');
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
    
    if (images.length === 0) {
        showNotification('Please upload at least one image', 'error');
        return;
    }
    
    if (currentEditingGalleryId) {
        // Update existing gallery entry
        const photoIndex = galleryPhotos.findIndex(p => p.id === currentEditingGalleryId);
        if (photoIndex !== -1) {
            galleryPhotos[photoIndex].title = title;
            galleryPhotos[photoIndex].description = description;
            // Note: In a real app, you'd handle image updates differently
        }
        showNotification('Gallery updated successfully!', 'success');
    } else {
        // Add new gallery entry
        if (galleryPhotos.length >= MAX_GALLERY_ENTRIES) {
            showNotification('You can only create a maximum of 5 gallery entries.', 'warning');
            return;
        }
        
        const newGallery = {
            id: Date.now(),
            title: title,
            description: description,
            images: images
        };
        
        galleryPhotos.push(newGallery);
        showNotification('Gallery added successfully!', 'success');
    }
    
    renderGallery();
    closeGalleryModal();
}

// Close Gallery Modal
function closeGalleryModal() {
    document.getElementById('galleryPhotoModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('galleryTitle').value = '';
    document.getElementById('galleryDescription').value = '';
    document.getElementById('galleryFileInput').value = '';
    
    // Reset preview
    const preview = document.getElementById('galleryPreview');
    const uploadContent = document.querySelector('.upload-content');
    preview.style.display = 'none';
    uploadContent.style.display = 'flex';
    
    // Show file upload section again
    document.getElementById('galleryImageUpload').style.display = 'block';
    
    currentEditingGalleryId = null;
}

// Render Gallery
function renderGallery() {
    const galleryGrid = document.getElementById('galleryGrid');
    
    if (galleryPhotos.length === 0) {
        galleryGrid.innerHTML = `
            <div class="gallery-empty">
                <i class="fas fa-images"></i>
                <h4>No Photos Yet</h4>
                <p>Add some photos to showcase your experiences and memories!</p>
            </div>
        `;
        return;
    }
    
    galleryGrid.innerHTML = galleryPhotos.map(photo => `
        <div class="gallery-item editable" data-gallery-id="${photo.id}">
            <div class="gallery-image">
                <img src="${photo.image}" alt="${photo.title}">
                <div class="gallery-actions-overlay">
                    <button type="button" class="gallery-action-btn edit" onclick="editGalleryItem(${photo.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(${photo.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="gallery-content">
                <h4 class="gallery-title">${escapeHtml(photo.title)}</h4>
                <p class="gallery-description">${escapeHtml(photo.description)}</p>
            </div>
        </div>
    `).join('');
}

// Character counters for gallery inputs
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('galleryTitle');
    const descriptionInput = document.getElementById('galleryDescription');
    
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            updateCharacterCounter(this, 50);
        });
    }
    
    if (descriptionInput) {
        descriptionInput.addEventListener('input', function() {
            updateCharacterCounter(this, 150);
        });
    }
});

function updateCharacterCounter(input, maxLength) {
    const currentLength = input.value.length;
    const remainingChars = maxLength - currentLength;
    
    // Find or create counter element
    let counter = input.parentElement.querySelector('.character-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'character-counter';
        input.parentElement.appendChild(counter);
    }
    
    counter.textContent = `${currentLength}/${maxLength} characters`;
    
    // Add warning/danger classes
    counter.classList.remove('warning', 'danger');
    if (remainingChars <= 10 && remainingChars > 0) {
        counter.classList.add('warning');
    } else if (remainingChars <= 0) {
        counter.classList.add('danger');
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
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
        if (modal.style.display === 'flex') {
            closeGalleryModal();
        }
    }
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