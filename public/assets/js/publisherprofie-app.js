class ClubProfile {
    constructor() {
        this.currentTab = 'about';
        this.organizationData = {
            name: '',
            type: '',
            university: '',
            faculty: '',
            officialEmail: '',
            contactNumber: '',
            address: '',
            establishedYear: '',
            memberCount: '',
            headline: '',
            bio: '',
            mission: '',
            website: '',
            instagram: '',
            facebook: '',
            linkedin: '',
            twitter: '',
            discord: '',
            youtube: '',
            telegram: '',
            github: '',
            logo: ''
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

    // setupCoverPhotoUpload() {
    //     const coverOverlay = document.querySelector('.cover-overlay');
    //     const coverInput = document.getElementById('coverInput');

    //     if (coverOverlay && coverInput) {
    //         coverOverlay.addEventListener('click', () => {
    //             coverInput.click();
    //         });

    //         coverInput.addEventListener('change', (e) => {
    //             const file = e.target.files[0];
    //             if (file && file.type.startsWith('image/')) {
    //                 const reader = new FileReader();
    //                 reader.onload = (e) => {
    //                     const coverImg = document.getElementById('coverPhoto');
    //                     if (coverImg) {
    //                         coverImg.src = e.target.result;
    //                         // this.showNotification('Cover photo updated successfully!', 'success');
    //                     }
    //                 };
    //                 reader.readAsDataURL(file);
    //             }
    //         });
    //     }
    // }

    setupCoverPhotoUpload() {
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
        // Check if publisherData is available from PHP
        if (typeof publisherData !== 'undefined' && publisherData) {
            // Map database fields to form fields
            const formFieldMapping = {
                'society_name': 'orgName',
                'email': 'officialEmail',
                'phone': 'contactNumber',
                'university': 'university',
                'faculty': 'faculty',
                'org_type': 'orgType',
                'address': 'address',
                'established_year': 'establishedYear',
                'member_count': 'memberCount',
                'headline': 'headline',
                'bio': 'bio',
                'mission': 'mission',
                'website': 'website',
                'facebook': 'facebook',
                'instagram': 'instagram',
                'linkedin': 'linkedin',
                'twitter': 'twitter',
                'discord': 'discord',
                'youtube': 'youtube'
            };

            // Populate form fields with database data
            Object.keys(formFieldMapping).forEach(dbField => {
                const formFieldId = formFieldMapping[dbField];
                const element = document.getElementById(formFieldId);
                if (element && publisherData[dbField]) {
                    element.value = publisherData[dbField];
                }
            });

            // Update organization data object with real data
            if (publisherData.society_name) {
                this.organizationData.name = publisherData.society_name;
            }
            if (publisherData.email) {
                this.organizationData.officialEmail = publisherData.email;
            }
            if (publisherData.phone) {
                this.organizationData.contactNumber = publisherData.phone;
            }
            if (publisherData.university) {
                this.organizationData.university = publisherData.university;
            }
            if (publisherData.faculty) {
                this.organizationData.faculty = publisherData.faculty;
            }

            // Update profile display with real data
            const profileName = document.getElementById('profileName');
            if (profileName && publisherData.society_name) {
                profileName.textContent = publisherData.society_name;
            }

            const profileBio = document.getElementById('profileBio');
            if (profileBio && publisherData.bio) {
                profileBio.textContent = publisherData.bio;
            }

            // Update profile image if available
            const profileImage = document.getElementById('profileImage');
            if (profileImage && publisherData.logo_url) {
                profileImage.src = publisherData.logo_url;
            }

            // Update cover photo if available
            const coverPhoto = document.getElementById('coverPhoto');
            if (coverPhoto && publisherData.cover_photo_url) {
                coverPhoto.src = publisherData.cover_photo_url;
            }

            // Load preferences if available (for checkbox display)
            if (publisherData.preferences) {
                try {
                    const preferences = JSON.parse(publisherData.preferences);
                    // Preferences are now handled by PHP form checkboxes
                    // This code is kept for backward compatibility
                    document.querySelectorAll('.preference-btn').forEach(btn => {
                        const preference = btn.dataset.preference;
                        if (preferences.includes(preference)) {
                            btn.classList.add('active');
                        }
                    });
                } catch (e) {
                    console.error('Error parsing preferences:', e);
                }
            }
        }
        // If no publisherData, fields will remain empty (just showing placeholders)

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
        // Pure PHP implementation - no JS auto-save needed
        // This function is kept for backward compatibility with button-based UI
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
            orgName: document.getElementById('orgName').value,
            orgType: document.getElementById('orgType').value,
            university: document.getElementById('university').value,
            faculty: document.getElementById('faculty').value,
            contactNumber: document.getElementById('contactNumber').value,
            address: document.getElementById('address').value,
            establishedYear: document.getElementById('establishedYear').value,
            memberCount: document.getElementById('memberCount').value,
            headline: document.getElementById('headline').value,
            bio: document.getElementById('bio').value,
            mission: document.getElementById('mission').value
        };

        // Show loading state
        const saveBtn = document.querySelector('#organization-form .btn-primary');
        const originalText = saveBtn.textContent;
        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;

        // Make AJAX call to update profile
        fetch('/UniPulse/public/publisher/profile/updateOrganizationInfo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update organizationData
                this.organizationData.name = formData.orgName;
                this.organizationData.type = formData.orgType;
                this.organizationData.university = formData.university;
                this.organizationData.faculty = formData.faculty;
                this.organizationData.contactNumber = formData.contactNumber;
                this.organizationData.address = formData.address;
                this.organizationData.establishedYear = formData.establishedYear;
                this.organizationData.memberCount = formData.memberCount;
                this.organizationData.headline = formData.headline;
                this.organizationData.bio = formData.bio;
                this.organizationData.mission = formData.mission;
                
                // Update profile display
                const profileName = document.getElementById('profileName');
                if (profileName) profileName.textContent = formData.orgName;
                
                const profileBio = document.getElementById('profileBio');
                if (profileBio) profileBio.textContent = formData.bio;
                
                this.showNotification('Organization information updated successfully!', 'success');
            } else {
                this.showNotification('Failed to update: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error updating organization info:', error);
            this.showNotification('An error occurred while updating', 'error');
        })
        .finally(() => {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        });
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
            website: document.getElementById('website').value,
            instagram: document.getElementById('instagram').value,
            facebook: document.getElementById('facebook').value,
            linkedin: document.getElementById('linkedin').value,
            twitter: document.getElementById('twitter').value,
            discord: document.getElementById('discord').value,
            youtube: document.getElementById('youtube').value
        };

        // Show loading state
        const saveBtn = document.querySelector('#social-form .btn-primary');
        if (saveBtn) {
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            // Make AJAX call to update social links
            fetch('/UniPulse/public/publisher/profile/updateSocialLinks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(socialData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Social links updated successfully!', 'success');
                } else {
                    this.showNotification('Failed to update: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error updating social links:', error);
                this.showNotification('An error occurred while updating', 'error');
            })
            .finally(() => {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            });
        }
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

    showNotification(message, type = 'info') {
        // Create notification element if it doesn't exist
        let notification = document.getElementById('notification-toast');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification-toast';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease-out;
                max-width: 350px;
            `;
            document.body.appendChild(notification);
        }

        // Set background color based on type
        const colors = {
            'success': '#10b981',
            'error': '#ef4444',
            'warning': '#f59e0b',
            'info': '#3b82f6'
        };
        
        notification.style.backgroundColor = colors[type] || colors['info'];
        notification.textContent = message;
        notification.style.display = 'block';

        // Auto-hide after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 3000);
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
    console.log('changeCover called, file:', file);
    
    if (file && file.type.startsWith('image/')) {
        // Show preview immediately
        const reader = new FileReader();
        reader.onload = (e) => {
            const coverImg = document.getElementById('coverPhoto');
            if (coverImg) {
                coverImg.src = e.target.result;
                console.log('Preview set');
            }
        };
        reader.readAsDataURL(file);

        // Upload to server
        const formData = new FormData();
        formData.append('image', file);

        console.log('Starting upload to /UniPulse/public/publisher/profile/uploadCoverPhoto');
        
        if (typeof clubProfile !== 'undefined') {
            clubProfile.showNotification('Uploading cover photo...', 'info');
        }

        fetch('/UniPulse/public/publisher/profile/uploadCoverPhoto', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                if (typeof clubProfile !== 'undefined') {
                    clubProfile.showNotification('Cover photo updated successfully!', 'success');
                }
                // Update with server URL
                const coverImg = document.getElementById('coverPhoto');
                if (coverImg && data.imageUrl) {
                    coverImg.src = data.imageUrl;
                    console.log('Cover photo updated with URL:', data.imageUrl);
                }
            } else {
                console.error('Upload failed:', data.message);
                if (typeof clubProfile !== 'undefined') {
                    clubProfile.showNotification('Failed to upload: ' + data.message, 'error');
                }
                alert('Failed to upload: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error uploading cover photo:', error);
            if (typeof clubProfile !== 'undefined') {
                clubProfile.showNotification('Error uploading cover photo', 'error');
            }
            alert('Error uploading cover photo: ' + error.message);
        });
    }
}

// Profile photo upload functions
function uploadProfileImage() {
    document.getElementById('profileInput').click();
}

function changeProfileImage(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        // Show preview immediately
        const reader = new FileReader();
        reader.onload = (e) => {
            const profileImg = document.getElementById('profileImage');
            if (profileImg) {
                profileImg.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);

        // Upload to server
        const formData = new FormData();
        formData.append('image', file);

        clubProfile.showNotification('Uploading profile logo...', 'info');

        fetch('/UniPulse/public/publisher/profile/uploadProfileImage', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clubProfile.showNotification('Profile logo updated successfully!', 'success');
                // Update with server URL
                const profileImg = document.getElementById('profileImage');
                if (profileImg && data.imageUrl) {
                    profileImg.src = data.imageUrl;
                }
            } else {
                clubProfile.showNotification('Failed to upload: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error uploading profile image:', error);
            clubProfile.showNotification('Error uploading profile image', 'error');
        });
    }
}

// Initialize the club profile manager when DOM is loaded
let clubProfile;
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Initializing...');
    clubProfile = new ClubProfile();
    
    // Load galleries on page load
    console.log('About to call loadGalleries()...');
    loadGalleries();
    
    // Load events on page load
    loadEvents();
    
    // Add event filter listeners
    setupEventFilters();
    
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
const MAX_GALLERY_ENTRIES = 100; // No practical limit
const MAX_PHOTOS_PER_ENTRY = 10;

// Load galleries from API on page load
async function loadGalleries() {
    console.log('=== loadGalleries() function called ===');
    try {
        console.log('Loading galleries...');
        console.log('Fetching from: /UniPulse/public/publisher/profile/getGalleries');
        const response = await fetch('/UniPulse/public/publisher/profile/getGalleries');
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            console.error('Response not OK:', response.status, response.statusText);
            const text = await response.text();
            console.error('Response text:', text);
            return;
        }
        
        // Get response as text first to see what we're getting
        const text = await response.text();
        console.log('Raw response text:', text);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError);
            console.error('First 500 chars of response:', text.substring(0, 500));
            return;
        }
        
        console.log('Gallery data received:', data);
        
        if (data.success && data.data) {
            console.log('Displaying', data.data.length, 'galleries');
            displayGalleries(data.data);
        } else {
            console.error('Failed to load galleries:', data.message);
        }
    } catch (error) {
        console.error('Error loading galleries:', error);
    }
}

// Display galleries in the grid
function displayGalleries(galleries) {
    console.log('displayGalleries called with:', galleries);
    const galleryGrid = document.getElementById('galleryGrid');
    const noGalleriesMsg = document.getElementById('noGalleriesMessage');
    
    if (!galleryGrid) {
        console.error('Gallery grid element not found!');
        return;
    }
    
    if (!galleries || galleries.length === 0) {
        console.log('No galleries to display');
        galleryGrid.innerHTML = '';
        if (noGalleriesMsg) {
            noGalleriesMsg.style.display = 'block';
        } else {
            // Create the message if it doesn't exist
            galleryGrid.innerHTML = `
                <p class="text-center" style="padding: 20px; color: #999;" id="noGalleriesMessage">
                    <i class="fas fa-images" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                    No galleries yet. Click "Add Photo" to create your first gallery.
                </p>
            `;
        }
        return;
    }
    
    console.log('Hiding no galleries message and rendering', galleries.length, 'galleries');
    
    // Clear existing content completely
    galleryGrid.innerHTML = '';
    
    galleries.forEach((gallery, index) => {
        console.log(`Creating gallery ${index + 1}:`, gallery);
        const galleryItem = createGalleryElement(gallery);
        galleryGrid.appendChild(galleryItem);
    });
    
    console.log('Galleries rendered successfully');
}

// Create gallery element
function createGalleryElement(gallery) {
    console.log('createGalleryElement called with:', gallery);
    const item = document.createElement('div');
    item.className = 'gallery-item editable';
    item.setAttribute('data-gallery-id', gallery.id);
    
    const images = gallery.images || [];
    console.log('Gallery images:', images);
    
    if (images.length === 0) {
        console.warn('No images in gallery!');
    }
    
    // Create carousel images
    let carouselImagesHTML = '';
    images.forEach((imageUrl, index) => {
        console.log(`Creating image ${index + 1}:`, imageUrl);
        carouselImagesHTML += `
            <div class="carousel-image ${index === 0 ? 'active' : ''}">
                <img src="${imageUrl}" 
                     alt="${escapeHtml(gallery.title)} - Photo ${index + 1}"
                     onerror="console.error('Failed to load image:', this.src); this.style.border='2px solid red';"
                     onload="console.log('Image loaded successfully:', this.src)">
            </div>
        `;
    });
    
    // Create indicators
    let indicatorsHTML = '';
    images.forEach((_, index) => {
        indicatorsHTML += `<span class="indicator ${index === 0 ? 'active' : ''}" onclick="setCarouselImage(${gallery.id}, ${index})"></span>`;
    });
    
    item.innerHTML = `
        <div class="gallery-images-container">
            <div class="gallery-image-carousel">
                ${carouselImagesHTML}
            </div>
            ${images.length > 1 ? `
            <div class="carousel-controls">
                <button class="carousel-btn prev" onclick="changeCarouselImage(${gallery.id}, -1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn next" onclick="changeCarouselImage(${gallery.id}, 1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            ` : ''}
            ${images.length > 1 ? `
            <div class="carousel-indicators">
                ${indicatorsHTML}
            </div>
            ` : ''}
            <div class="gallery-actions-overlay">
                <button type="button" class="gallery-action-btn edit" onclick="editGalleryItem(${gallery.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(${gallery.id})" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="gallery-content">
            <h4 class="gallery-title">${escapeHtml(gallery.title)}</h4>
            <p class="gallery-description">${escapeHtml(gallery.description || '')}</p>
        </div>
    `;
    
    console.log('Gallery element HTML:', item.outerHTML.substring(0, 500));
    return item;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
}

// Add Gallery Photo
function addGalleryPhoto() {
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
async function editGalleryItem(galleryId) {
    try {
        const response = await fetch(`/UniPulse/public/publisher/profile/getGallery/${galleryId}`);
        const data = await response.json();
        
        if (!data.success || !data.data) {
            alert('Failed to load gallery');
            return;
        }
        
        const gallery = data.data;
        currentEditingGalleryId = galleryId;
        
        document.getElementById('galleryModalTitle').textContent = 'Edit Photo Gallery';
        document.getElementById('galleryTitle').value = gallery.title;
        document.getElementById('galleryDescription').value = gallery.description || '';
        
        // Show existing images in preview
        const images = gallery.images || [];
        for (let i = 0; i < images.length && i < MAX_PHOTOS_PER_ENTRY; i++) {
            const preview = document.getElementById(`galleryPreview${i + 1}`);
            const uploadContent = document.querySelector(`#galleryFile${i + 1}`)?.parentElement?.querySelector('.upload-content');
            
            if (preview) {
                preview.src = images[i];
                preview.style.display = 'block';
                preview.setAttribute('data-existing-url', images[i]);
            }
            if (uploadContent) {
                uploadContent.style.display = 'none';
            }
        }
        
        // Show modal
        document.getElementById('galleryPhotoModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
    } catch (error) {
        console.error('Error loading gallery:', error);
        alert('Failed to load gallery');
    }
}

// Delete Gallery Item
async function deleteGalleryItem(galleryId) {
    if (!confirm('Are you sure you want to delete this gallery? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/UniPulse/public/publisher/profile/deleteGallery/${galleryId}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Gallery deleted successfully');
            loadGalleries(); // Reload galleries
        } else {
            alert('Failed to delete gallery: ' + data.message);
        }
    } catch (error) {
        console.error('Error deleting gallery:', error);
        alert('Error deleting gallery');
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
async function saveGalleryPhoto() {
    const title = document.getElementById('galleryTitle').value.trim();
    const description = document.getElementById('galleryDescription').value.trim();
    
    // Validation
    if (!title) {
        alert('Please enter a title for the gallery');
        return;
    }
    
    // Collect uploaded photos
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    
    let hasFiles = false;
    let fileCount = 0;
    const existingPhotos = []; // Track existing photos
    
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const fileInput = document.getElementById(`galleryFile${i}`);
        const preview = document.getElementById(`galleryPreview${i}`);
        
        if (fileInput && fileInput.files && fileInput.files[0]) {
            // New photo uploaded
            formData.append(`photos[]`, fileInput.files[0]);
            hasFiles = true;
            fileCount++;
        } else if (preview && preview.getAttribute('data-existing-url')) {
            // Keep existing image URL for edit mode
            existingPhotos.push(preview.getAttribute('data-existing-url'));
            fileCount++;
        }
    }
    
    // Add existing photos as JSON string for update
    if (currentEditingGalleryId && existingPhotos.length > 0) {
        formData.append('keep_photos', JSON.stringify(existingPhotos));
    }
    
    if (fileCount === 0) {
        alert('Please upload at least one photo');
        return;
    }
    
    if (fileCount > MAX_PHOTOS_PER_ENTRY) {
        alert(`You can upload a maximum of ${MAX_PHOTOS_PER_ENTRY} photos per gallery`);
        return;
    }
    
    // Determine endpoint
    let endpoint;
    if (currentEditingGalleryId) {
        endpoint = `/UniPulse/public/publisher/profile/updateGallery/${currentEditingGalleryId}`;
        formData.append('gallery_id', currentEditingGalleryId);
    } else {
        endpoint = '/UniPulse/public/publisher/profile/createGallery';
    }
    
    try {
        console.log('Sending request to:', endpoint);
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0], ':', pair[1].name, '(', pair[1].size, 'bytes)');
            } else {
                console.log(pair[0], ':', pair[1]);
            }
        }
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            alert('Server returned invalid JSON:\n\n' + text.substring(0, 500));
            return;
        }
        
        console.log('Parsed data:', data);
        
        if (data.success) {
            // Close modal first
            closeGalleryModal();
            
            // Wait a bit then reload galleries
            setTimeout(() => {
                console.log('Reloading galleries after save...');
                loadGalleries();
            }, 300);
        } else {
            console.error('API Error:', data);
            alert('Failed to save gallery: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Catch block error:', error);
        console.error('Error type:', error.constructor.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        alert('Error saving gallery: ' + error.message + '\n\nCheck browser console (F12) for details.');
    }
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
        
        const addAdminModal = document.getElementById('addAdminModal');
        if (addAdminModal && addAdminModal.style.display === 'flex') {
            closeAddAdminModal();
        }
    }
});

// ==================== EVENT FUNCTIONS ====================

let currentEventFilter = 'upcoming';

// Load events based on filter
async function loadEvents(filter = 'upcoming') {
    try {
        currentEventFilter = filter;
        const endpoint = filter === 'upcoming' 
            ? '/UniPulse/public/publisher/profile/getUpcomingEvents'
            : '/UniPulse/public/publisher/profile/getPastEvents';
        
        console.log('Loading events:', filter);
        const response = await fetch(endpoint);
        const data = await response.json();
        
        if (data.success && data.data) {
            console.log(`Loaded ${data.data.length} ${filter} events`);
            displayEvents(data.data);
        } else {
            console.error('Failed to load events:', data.message);
            displayEvents([]);
        }
    } catch (error) {
        console.error('Error loading events:', error);
        displayEvents([]);
    }
}

// Display events in the grid
function displayEvents(events) {
    const container = document.getElementById('eventsContainer');
    
    if (!container) {
        console.error('Events container not found');
        return;
    }
    
    if (!events || events.length === 0) {
        container.innerHTML = `
            <div class="no-events">
                <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                <p>No ${currentEventFilter} events found.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';
    
    events.forEach(event => {
        const eventCard = createEventCard(event);
        container.appendChild(eventCard);
    });
}

// Create event card element
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    
    // Format date
    const eventDate = new Date(event.event_date);
    const formatDate = (date) => {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    };
    
    // Format time
    const eventTime = event.event_time ? event.event_time.substring(0, 5) : '';
    
    // Capitalize first letter helper
    const capitalizeFirstLetter = (str) => {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    };
    
    // Determine location display based on location_type
    let locationDisplay = '';
    let secondaryInfo = '';
    
    const locationType = event.location_type || 'inside-university';
    const venueName = event.venue_name;
    const city = event.city;
    const exactLocation = event.exact_location;
    const universityName = event.university_name;
    const facultyDepartment = event.faculty_department;
    
    if (locationType === 'outside-university') {
        // Outside university: show "Venue Name, City"
        if (venueName && city) {
            locationDisplay = `${venueName}, ${city}`;
        } else if (venueName) {
            locationDisplay = venueName;
        } else if (city) {
            locationDisplay = city;
        } else {
            locationDisplay = 'Location TBA';
        }
        secondaryInfo = '';
    } else {
        // Inside university: show "Exact Location, University"
        if (exactLocation && universityName) {
            locationDisplay = `${exactLocation}, ${universityName}`;
        } else if (exactLocation) {
            locationDisplay = exactLocation;
        } else if (universityName) {
            locationDisplay = universityName;
        } else {
            locationDisplay = 'Location TBA';
        }
        // Secondary info shows "Faculty, University"
        if (facultyDepartment && universityName) {
            secondaryInfo = `${facultyDepartment}, ${universityName}`;
        } else if (facultyDepartment) {
            secondaryInfo = facultyDepartment;
        } else if (universityName) {
            secondaryInfo = universityName;
        }
    }
    
    // Build correct image path
    let imagePath = '';
    const coverImage = event.image_url || event.cover_image;
    if (coverImage) {
        if (coverImage.startsWith('http')) {
            imagePath = coverImage;
        } else if (coverImage.startsWith('/')) {
            imagePath = coverImage;
        } else {
            imagePath = `/UniPulse/public/${coverImage}`;
        }
    }
    
    // Ticket price display
    const getTicketPriceDisplay = (event) => {
        const ticketType = event.ticket_type;
        if (ticketType === 'free-all') {
            return '<div class="event-price free">Free</div>';
        } else if (ticketType === 'paid-all') {
            const price = parseFloat(event.ticket_price_paid) || 0;
            return `<div class="event-price paid">Rs. ${price.toFixed(2)}</div>`;
        } else if (ticketType === 'both') {
            const freePrice = parseFloat(event.ticket_price_free) || 0;
            const paidPrice = parseFloat(event.ticket_price_paid) || 0;
            return `<div class="event-price paid">Rs. ${freePrice.toFixed(2)} - Rs. ${paidPrice.toFixed(2)}</div>`;
        }
        return '<div class="event-price">TBA</div>';
    };
    
    const currentParticipants = event.current_participants || 0;
    const maxParticipants = event.max_participants;
    
    card.innerHTML = `
        <div class="event-image">
            ${imagePath ? 
                `<img src="${imagePath}" alt="${event.title}">` : 
                `<svg class="placeholder-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>`
            }
            <div class="event-category">${capitalizeFirstLetter(event.category)}</div>
            <div class="event-status ${event.status}">${event.status}</div>
        </div>
        <div class="event-content">
            <h3 class="event-title">${event.title}</h3>
            <p class="event-description">${event.description}</p>
            <div class="event-meta">
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${formatDate(eventDate)} at ${eventTime}</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${locationDisplay}</span>
                </div>
                ${secondaryInfo ? `
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9,22 9,12 15,12 15,22"></polyline>
                    </svg>
                    <span>${secondaryInfo}</span>
                </div>
                ` : ''}
            </div>
            <div class="event-footer">
                <div class="event-organizer">
                    Organized by ${event.organizer_name || event.organizer || 'You'}
                </div>
                <div class="event-footer-right">
                    ${getTicketPriceDisplay(event)}
                    ${maxParticipants !== null && maxParticipants !== undefined ? `
                    <div class="event-participants">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>${currentParticipants}/${maxParticipants}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    // Make card clickable to view event details
    card.style.cursor = 'pointer';
    card.addEventListener('click', () => {
        window.location.href = `/UniPulse/public/publisher/eventview/${event.id}`;
    });
    
    return card;
}

// Setup event filter buttons
function setupEventFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Load events based on filter
            const filter = this.getAttribute('data-filter');
            loadEvents(filter);
        });
    });
    
    // Setup search functionality
    const searchInput = document.getElementById('eventSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterEventsSearch(this.value);
        });
    }
}

// Filter events by search term
function filterEventsSearch(searchTerm) {
    const eventCards = document.querySelectorAll('.event-card');
    const term = searchTerm.toLowerCase();
    
    eventCards.forEach(card => {
        const title = card.querySelector('.event-title').textContent.toLowerCase();
        const description = card.querySelector('.event-description').textContent.toLowerCase();
        
        if (title.includes(term) || description.includes(term)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

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
// ==================== ACCESS CONTROL & SECURITY ====================

function changePassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Validation
    if (!currentPassword) {
        alert('Please enter your current password');
        return;
    }

    if (!newPassword) {
        alert('Please enter a new password');
        return;
    }

    if (newPassword.length < 8) {
        alert('New password must be at least 8 characters long');
        return;
    }

    if (newPassword !== confirmPassword) {
        alert('New passwords do not match');
        return;
    }

    // Send request to backend
    fetch('/UniPulse/public/publisher/profile/changePassword', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while changing password');
    });
}

function cancelSecurityForm() {
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}
