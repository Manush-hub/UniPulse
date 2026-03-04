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
        this.loadUserData();
        this.setupAnimations();
        this.setupImageUploads();
    }

    setupImageUploads() {
        this.setupCoverPhotoUpload();
        this.setupProfilePhotoUpload();
    }

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

    // categorizeEvents() - REMOVED: Event functionality removed
    // categorizeEvents() {
    //     const currentDate = new Date('2024-08-25');
    //     this.events.forEach(event => {
    //         const eventDate = new Date(event.date);
    //         event.category = eventDate > currentDate ? 'upcoming' : 'past';
    //     });
    // }

    bindEvents() {
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
        const personalForm = document.getElementById('personal-form');
        if (personalForm) {
            personalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.savePersonalInfo();
            });
        }

        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.selectRole(e.target.dataset.role);
            });
        });

        document.querySelectorAll('.gender-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.selectGender(e.target.dataset.gender);
            });
        });

        document.querySelectorAll('.preference-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.togglePreference(e.target);
            });
        });

        const settingsForm = document.querySelector('#settings form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0]);
            });
        }
    }

    bindToggleEvents() {
        document.querySelectorAll('.toggle input').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.updatePrivacySetting(e.target.id, e.target.checked);
            });
        });

        document.querySelectorAll('.checkbox-item input').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateEventPreference(e.target.parentElement.textContent.trim(), e.target.checked);
            });
        });
    }

    bindModalEvents() {
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                this.closeModal(modal.id);
            });
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });
    }

    setupAnimations() {
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

    async loadUserData() {
        try {
            const url = (window.profileApi && window.profileApi.get) || '/unipulse/public/user/profile/getProfile';
            const res = await fetch(url, { credentials: 'same-origin' });
            const json = await res.json();

            if (!json.success) {
                console.error('Failed to load profile:', json.error, json.message);
                this.showNotification('Failed to load profile: ' + (json.message || json.error), 'error');
                return;
            }

            const d = json.data || {};
            console.log('Profile data loaded:', d); // Debug log

            const map = ['full_name', 'email', 'phone', 'country_code', 'university', 'faculty', 'student_staff_id', 'academic_year', 'date_of_birth', 'current_city', 'home_town', 'headline', 'bio', 'nic'];
            map.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const value = d[id] ?? '';
                    el.value = value;
                    console.log(`Set ${id} to:`, value); // Debug log
                }
            });

            // Gender dropdown
            if (d.gender) {
                const genderSelect = document.getElementById('gender');
                if (genderSelect) genderSelect.value = d.gender;
            }

            // Academic year dropdown
            if (d.academic_year) {
                const academicYearSelect = document.getElementById('academic_year');
                if (academicYearSelect) academicYearSelect.value = d.academic_year;
            }



            // Role buttons (student/staff/public)
            if (d.role) {
                document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
                const rbtn = document.querySelector(`[data-role="${d.role}"]`);
                if (rbtn) rbtn.classList.add('active');
                const rHidden = document.getElementById('role');
                if (rHidden) rHidden.value = d.role;
            }

            // Update banner name if exists
            const profileName = document.getElementById('profileName');
            if (profileName) profileName.textContent = d.full_name || '';

            // Update display name and email in header
            const displayName = document.getElementById('displayName');
            if (displayName) displayName.textContent = d.full_name || '';
            
            const displayEmail = document.getElementById('displayEmail');
            if (displayEmail) displayEmail.textContent = d.email || '';

            // Load profile photo if exists
            if (d.profile_photo) {
                const profileImg = document.getElementById('profilePhoto');
                if (profileImg) {
                    profileImg.src = d.profile_photo;
                    profileImg.style.display = 'block';
                }
            }

            // Load cover photo if exists
            if (d.cover_photo) {
                const coverImg = document.getElementById('coverPhoto');
                if (coverImg) {
                    coverImg.src = d.cover_photo;
                    coverImg.style.display = 'block';
                }
            }

            // Load event preferences (interests)
            if (d.interests) {
                let interestsArray = [];
                if (typeof d.interests === 'string') {
                    try {
                        interestsArray = JSON.parse(d.interests);
                    } catch (e) {
                        console.error('Failed to parse interests:', e);
                    }
                } else if (Array.isArray(d.interests)) {
                    interestsArray = d.interests;
                }
                
                // Mark active preference buttons
                document.querySelectorAll('.preference-btn').forEach(btn => {
                    btn.classList.remove('active');
                    const pref = btn.dataset.preference;
                    if (interestsArray.includes(pref)) {
                        btn.classList.add('active');
                    }
                });
                
                this.userInterests = interestsArray;
                console.log('Loaded interests:', interestsArray);
            } else {
                this.userInterests = [];
            }

            // Store in userData for cancel operation
            this.userData = {
                full_name: d.full_name || '',
                email: d.email || '',
                phone: d.phone || '',
                country_code: d.country_code || '+94',
                university: d.university || '',
                faculty: d.faculty || '',
                student_staff_id: d.student_staff_id || '',
                academic_year: d.academic_year || '',
                gender: d.gender || '',
                date_of_birth: d.date_of_birth || '',
                current_city: d.current_city || '',
                home_town: d.home_town || '',
                headline: d.headline || '',
                nic: d.nic || '',
                bio: d.bio || '',
                profile_photo: d.profile_photo || '',
                cover_photo: d.cover_photo || '',
                interests: this.userInterests || []
            };

        } catch (e) {
            console.error('Failed to load profile:', e);
            this.showNotification('Error loading profile: ' + e.message, 'error');
        }
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
        const totalConnections = 42;

        const eventsAttended = document.getElementById('eventsAttended');
        const eventsOrganized = document.getElementById('eventsOrganized');
        const connections = document.getElementById('connections');

        if (eventsAttended) eventsAttended.textContent = attendedCount;
        if (eventsOrganized) eventsOrganized.textContent = organizedCount;
        if (connections) connections.textContent = totalConnections;
    }

    async togglePreference(button) {
        button.classList.toggle('active');
        const preference = button.dataset.preference;
        const isActive = button.classList.contains('active');
        
        // Update local interests array
        if (!this.userInterests) {
            this.userInterests = [];
        }
        
        if (isActive) {
            // Add preference if not already in array
            if (!this.userInterests.includes(preference)) {
                this.userInterests.push(preference);
            }
        } else {
            // Remove preference from array
            const index = this.userInterests.indexOf(preference);
            if (index > -1) {
                this.userInterests.splice(index, 1);
            }
        }
        
        // Save to database
        await this.saveInterests();
    }
    
    async saveInterests() {
        try {
            const url = (window.profileApi && window.profileApi.update) || '/unipulse/public/user/profile/updateProfile';
            const payload = {
                interests: JSON.stringify(this.userInterests)
            };
            
            console.log('Saving interests:', this.userInterests);
            
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            
            const json = await res.json();
            if (json.success) {
                console.log('Interests saved successfully');
                this.showNotification('Event preferences updated!', 'success');
            } else {
                console.error('Failed to save interests:', json);
                this.showNotification('Failed to update preferences: ' + (json.error || 'Unknown error'), 'error');
            }
        } catch (e) {
            console.error('Error saving interests:', e);
            this.showNotification('Error updating preferences: ' + e.message, 'error');
        }
    }

    selectRole(role) {
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        document.querySelector(`[data-role="${role}"]`).classList.add('active');
        document.getElementById('role').value = role;
    }

    selectGender(gender) {
        document.querySelectorAll('.gender-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        document.querySelector(`[data-gender="${gender}"]`).classList.add('active');
        document.getElementById('gender').value = gender;
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

    // loadEvents() - REMOVED: Event functionality removed
    // loadEvents() {
    //     const container = document.getElementById('eventsContainer');
    //     if (!container) return;
    //     // ... code removed ...
    // }

    // createEventCard() - REMOVED: Event functionality removed
    // createEventCard(event) {
    //     // ... code removed ...
    // }

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

        if (!showContact) {
            document.querySelectorAll('#email, #phone').forEach(input => {
                const originalValue = input.value;
                input.value = 'Hidden for privacy';
                input.style.fontStyle = 'italic';
                input.dataset.originalValue = originalValue;
            });
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

    async savePersonalInfo() {
        // Collect field values
        const full_name = document.getElementById('full_name')?.value?.trim() || '';
        const phone = document.getElementById('phone')?.value?.trim() || '';
        const country_code = document.getElementById('country_code')?.value || '';
        const gender = document.getElementById('gender')?.value || '';
        const date_of_birth = document.getElementById('date_of_birth')?.value || '';
        const headline = document.getElementById('headline')?.value?.trim() || '';
        const academic_year = document.getElementById('academic_year')?.value || '';
        const current_city = document.getElementById('current_city')?.value?.trim() || '';
        const home_town = document.getElementById('home_town')?.value?.trim() || '';
        const bio = document.getElementById('bio')?.value?.trim() || '';

        // Build payload - only include fields that have values
        const payload = {};

        if (full_name) payload.full_name = full_name;
        if (phone) payload.phone = phone;
        if (country_code) payload.country_code = country_code;
        if (gender) payload.gender = gender;
        if (date_of_birth) payload.date_of_birth = date_of_birth;
        if (headline) payload.headline = headline;
        if (academic_year) payload.academic_year = academic_year;
        if (current_city) payload.current_city = current_city;
        if (home_town) payload.home_town = home_town;
        if (bio) payload.bio = bio;

        // If no fields to update, show error
        if (Object.keys(payload).length === 0) {
            this.showNotification('Please fill in at least one field to update', 'error');
            return;
        }

        try {
            const url = (window.profileApi && window.profileApi.update) || '/unipulse/public/user/profile/updateProfile';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                // Update local userData to reflect saved changes
                if (full_name) this.userData.full_name = full_name;
                if (phone) this.userData.phone = phone;
                if (country_code) this.userData.country_code = country_code;
                if (gender) this.userData.gender = gender;
                if (date_of_birth) this.userData.date_of_birth = date_of_birth;
                if (headline) this.userData.headline = headline;
                if (academic_year) this.userData.academic_year = academic_year;
                if (current_city) this.userData.current_city = current_city;
                if (home_town) this.userData.home_town = home_town;
                if (bio) this.userData.bio = bio;

                // Update profile name display if name was changed
                if (full_name) {
                    const profileName = document.getElementById('profileName');
                    if (profileName) profileName.textContent = full_name;
                    const displayName = document.getElementById('displayName');
                    if (displayName) displayName.textContent = full_name;
                }

                // Show success message
                this.showNotification('Profile updated successfully!', 'success');
            } else {
                console.error('Update failed', json);
                let errorMsg = 'Unknown error';
                if (json.error) {
                    errorMsg = json.error;
                } else if (json.errors && Array.isArray(json.errors)) {
                    errorMsg = json.errors.join(', ');
                } else if (json.message) {
                    errorMsg = json.message;
                }
                this.showNotification('Failed to update profile: ' + errorMsg, 'error');
            }
        } catch (e) {
            console.error('Failed to update profile:', e);
            this.showNotification('Error updating profile: ' + e.message, 'error');
        }
    }

    cancelPersonalInfo() {
        // Only restore editable fields to their original values
        const editableFields = [
            { id: 'full_name', key: 'full_name' },
            { id: 'phone', key: 'phone' },
            { id: 'country_code', key: 'country_code' },
            { id: 'gender', key: 'gender' },
            { id: 'date_of_birth', key: 'date_of_birth' },
            { id: 'headline', key: 'headline' },
            { id: 'academic_year', key: 'academic_year' },
            { id: 'current_city', key: 'current_city' },
            { id: 'home_town', key: 'home_town' },
            { id: 'bio', key: 'bio' }
        ];

        editableFields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element && this.userData[field.key] !== undefined) {
                element.value = this.userData[field.key];
            }
        });

        // Restore gender and academic year dropdowns
        const genderSelect = document.getElementById('gender');
        if (genderSelect) genderSelect.value = this.userData.gender || '';

        const academicYearSelect = document.getElementById('academic_year');
        if (academicYearSelect) academicYearSelect.value = this.userData.academic_year || '';
        document.getElementById('gender').value = defaultGender;

        this.showNotification('Changes cancelled', 'info');
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

        Object.assign(this.userData, socialData);
    }

    cancelSocialLinks() {
        document.getElementById('personal-website').value = this.userData.personalWebsite || '';
        document.getElementById('facebook').value = this.userData.facebook || '';
        document.getElementById('instagram').value = this.userData.instagram || '';
        document.getElementById('telegram').value = this.userData.telegram || '';
        document.getElementById('linkedin').value = this.userData.linkedin || '';
        document.getElementById('github').value = this.userData.github || '';
        document.getElementById('x-twitter').value = this.userData.xTwitter || '';
        document.getElementById('discord').value = this.userData.discord || '';
    }

    saveSettings() {
        const settings = {
            username: document.getElementById('username').value,
            timezone: document.getElementById('timezone').value,
            language: document.getElementById('language').value
        };
    }

    addTag() {
        const newTagInput = document.getElementById('newTag');
        const tagValue = newTagInput.value.trim();

        if (tagValue && !this.interests.includes(tagValue)) {
            this.interests.push(tagValue);
            this.loadInterests();
            newTagInput.value = '';
        }
    }

    removeInterest(interest) {
        const index = this.interests.indexOf(interest);
        if (index > -1) {
            this.interests.splice(index, 1);
            this.loadInterests();
        }
    }

    updatePrivacySetting(settingId, value) {
        console.log(`Privacy setting ${settingId} set to ${value}`);
    }

    updateEventPreference(preference, isChecked) {
        console.log(`Event preference ${preference} set to ${isChecked}`);
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
            console.log(`Opening details for ${event.title}`);
        }
    }

    manageEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            console.log(`Managing ${event.title}`);
        }
    }

    rateEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            console.log(`Rating ${event.title}`);
        }
    }

    viewTicket(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            console.log(`Viewing ticket for ${event.title}`);
        }
    }

    deactivateAccount() {
        if (confirm('Are you sure you want to deactivate your account? This action can be reversed.')) {
            console.log('Account deactivation process started');
        }
    }

    deleteAccount() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all your data.')) {
                console.log('Account deletion process started');
            }
        }
    }

    editProfile() {
        this.showTab('personal');
        this.toggleEdit('personal-form');
    }

    cancelEdit(formId) {
        this.loadUserData();
        this.toggleEdit(formId);
    }
    
    showNotification(message, type = 'info') {
        // Call the global showNotification function
        showNotification(message, type);
    }
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

        console.log('Starting upload to /UniPulse/public/user/profile/uploadCoverPhoto');
        
        if (typeof clubProfile !== 'undefined') {
            clubProfile.showNotification('Uploading cover photo...', 'info');
        }

        fetch('/UniPulse/public/user/profile/uploadCoverPhoto', {
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

function showImageUploadStatus(message, type = 'info', duration = 3000) {
    let statusDiv = document.getElementById('imageUploadStatus');
    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'imageUploadStatus';
        statusDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 10000;
            max-width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(statusDiv);
    }

    statusDiv.textContent = message;
    statusDiv.style.display = 'block';

    if (type === 'success') {
        statusDiv.style.backgroundColor = '#4CAF50';
        statusDiv.style.color = 'white';
    } else if (type === 'error') {
        statusDiv.style.backgroundColor = '#f44336';
        statusDiv.style.color = 'white';
    } else {
        statusDiv.style.backgroundColor = '#2196F3';
        statusDiv.style.color = 'white';
    }

    if (duration > 0) {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, duration);
    }
}

function changeCoverImage(event) {
    console.log('[changeCoverImage] Function called');
    const file = event.target.files[0];
    console.log('[changeCoverImage] File selected:', file ? file.name : 'NO FILE');
    
    if (!file) {
        console.log('[changeCoverImage] No file selected, returning');
        return;
    }

    console.log('[changeCoverImage] File details:', {
        name: file.name,
        size: file.size,
        type: file.type
    });

    // Validate file type
    if (!file.type.startsWith('image/')) {
        console.error('[changeCoverImage] Invalid file type:', file.type);
        showImageUploadStatus('Please select a valid image file', 'error');
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        console.error('[changeCoverImage] File too large:', file.size);
        showImageUploadStatus('Image size must be less than 5MB', 'error');
        return;
    }

    console.log('[changeCoverImage] File validated, reading...');
    
    // Show preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        console.log('[changeCoverImage] File read successfully');
        const coverImg = document.getElementById('coverPhoto');
        if (coverImg) {
            coverImg.src = e.target.result;
            coverImg.style.display = 'block';
            console.log('[changeCoverImage] Preview updated');
        }
        // Show saving status
        showImageUploadStatus('Saving cover photo...', 'info', 0);
        console.log('[changeCoverImage] Calling saveCoverImageFormData...');
        // Save to database using FormData
        saveCoverImageFormData(file);
    };
    reader.onerror = () => {
        console.error('[changeCoverImage] Error reading file');
        showImageUploadStatus('Error reading file', 'error');
    };
    reader.readAsDataURL(file);
}

function uploadProfileImage() {
    document.getElementById('profileInput').click();
}

function changeProfileImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        showImageUploadStatus('Please select a valid image file', 'error');
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showImageUploadStatus('Image size must be less than 5MB', 'error');
        return;
    }

    // Show preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        const profileImg = document.getElementById('profilePhoto');
        if (profileImg) {
            profileImg.src = e.target.result;
            profileImg.style.display = 'block';
        }
        // Show saving status
        showImageUploadStatus('Saving profile photo...', 'info', 0);
        // Save to database using FormData
        saveProfileImageFormData(file);
    };
    reader.onerror = () => {
        showImageUploadStatus('Error reading file', 'error');
    };
    reader.readAsDataURL(file);
}

async function saveCoverImageFormData(file) {
    try {
        console.log('[saveCoverImageFormData] Starting upload, file size:', file.size, 'bytes');
        const formData = new FormData();
        formData.append('cover_photo', file);
        
        console.log('[saveCoverImageFormData] FormData created, sending to server...');

        const response = await fetch('/unipulse/public/user/profile/updateProfile', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        console.log('[saveCoverImageFormData] Response status:', response.status);
        const result = await response.json();
        console.log('[saveCoverImageFormData] Result:', result);
        
        if (result.success) {
            showImageUploadStatus('Cover photo saved successfully!', 'success');
            console.log('Cover photo saved successfully');
        } else {
            // Handle different error formats
            let errorMsg = result.error || result.message;
            if (result.errors && Array.isArray(result.errors)) {
                errorMsg = result.errors.join(', ');
            }
            showImageUploadStatus('Failed to save cover photo: ' + (errorMsg || 'Unknown error'), 'error');
            console.error('Failed to save cover photo:', result);
        }
    } catch (error) {
        showImageUploadStatus('Error saving cover photo', 'error');
        console.error('Error saving cover photo:', error);
    }
}

async function saveProfileImageFormData(file) {
    try {
        const formData = new FormData();
        formData.append('profile_photo', file);

        const response = await fetch('/unipulse/public/user/profile/updateProfile', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            showImageUploadStatus('Profile photo saved successfully!', 'success');
            console.log('Profile photo saved successfully');
        } else {
            // Handle different error formats
            let errorMsg = result.error || result.message;
            if (result.errors && Array.isArray(result.errors)) {
                errorMsg = result.errors.join(', ');
            }
            showImageUploadStatus('Failed to save profile photo: ' + (errorMsg || 'Unknown error'), 'error');
            console.error('Failed to save profile photo:', result);
        }
    } catch (error) {
        showImageUploadStatus('Error saving profile photo', 'error');
        console.error('Error saving profile photo:', error);
    }
}

// Keep backward compatibility with old function names
async function saveCoverImage(imageData) {
    try {
        const response = await fetch('/unipulse/public/user/profile/updateProfile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ cover_photo: imageData })
        });
        const result = await response.json();
        if (result.success) {
            console.log('Cover photo saved successfully');
        } else {
            console.error('Failed to save cover photo:', result.error);
        }
    } catch (error) {
        console.error('Error saving cover photo:', error);
    }
}

async function saveProfileImage(imageData) {
    try {
        const response = await fetch('/unipulse/public/user/profile/updateProfile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ profile_photo: imageData })
        });
        const result = await response.json();
        if (result.success) {
            console.log('Profile photo saved successfully');
        } else {
            console.error('Failed to save profile photo:', result.error);
        }
    } catch (error) {
        console.error('Error saving profile photo:', error);
    }
}

let profileManager;
document.addEventListener('DOMContentLoaded', () => {
    profileManager = new UniPulseProfile();

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                profileManager.closeModal(modal.id);
            });
        }
    });

    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function () {
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

document.addEventListener('DOMContentLoaded', function () {
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

            toggle.addEventListener('change', function () {
                if (statusText) {
                    statusText.textContent = this.checked ? 'Public' : 'Private';
                    statusText.style.color = this.checked ? '#4A5BCC' : '#666';
                }
            });

            if (statusText) {
                statusText.style.color = toggle.checked ? '#4A5BCC' : '#666';
            }
        }
    });
});

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

let galleryPhotos = [];

let currentEditingGalleryId = null;
const MAX_GALLERY_ENTRIES = 5;
const MAX_PHOTOS_PER_ENTRY = 5;
function addGalleryPhoto() {
    if (galleryPhotos.length >= MAX_GALLERY_ENTRIES) {
        showNotification('You can only create a maximum of 5 gallery entries.', 'warning');
        return;
    }

    currentEditingGalleryId = null;
    document.getElementById('galleryModalTitle').textContent = 'Add Photo Gallery';
    document.getElementById('galleryTitle').value = '';
    document.getElementById('galleryDescription').value = '';

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

    document.getElementById('galleryPhotoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function editGalleryItem(galleryId) {
    const photo = galleryPhotos.find(p => p.id === galleryId);
    if (!photo) return;

    currentEditingGalleryId = galleryId;
    document.getElementById('galleryModalTitle').textContent = 'Edit Photo Album';
    document.getElementById('galleryTitle').value = photo.title;
    document.getElementById('galleryDescription').value = photo.description;

    // Load existing images into previews
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const preview = document.getElementById(`galleryPreview${i}`);
        const uploadContent = document.querySelector(`#galleryFile${i}`).parentElement.querySelector('.upload-content');

        if (photo.images && photo.images[i - 1]) {
            preview.src = photo.images[i - 1];
            preview.style.display = 'block';
            if (uploadContent) uploadContent.style.display = 'none';
        } else {
            preview.style.display = 'none';
            preview.src = '';
            if (uploadContent) uploadContent.style.display = 'flex';
        }
    }

    document.getElementById('galleryPhotoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function deleteGalleryItem(galleryId) {
    if (confirm('Are you sure you want to delete this photo album? This action cannot be undone.')) {
        // Remove the specific gallery item
        const indexToRemove = galleryPhotos.findIndex(p => p.id === galleryId);
        if (indexToRemove === -1) {
            showNotification('Gallery item not found!', 'error');
            return;
        }

        galleryPhotos.splice(indexToRemove, 1);

        // Re-render the gallery
        renderGallery();

        // Save to backend
        saveGalleryToBackend();

        showNotification('Photo album deleted successfully!', 'success');
    }
}

function previewGalleryImage(event, photoIndex) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        showNotification('File size must be less than 5MB', 'error');
        event.target.value = '';
        return;
    }

    if (!file.type.startsWith('image/')) {
        showNotification('Please select a valid image file', 'error');
        event.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById(`galleryPreview${photoIndex}`);
        const uploadContent = event.target.parentElement.querySelector('.upload-content');

        preview.src = e.target.result;
        preview.style.display = 'block';
        uploadContent.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function saveGalleryPhoto() {
    const title = document.getElementById('galleryTitle').value.trim();
    const description = document.getElementById('galleryDescription').value.trim();

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

    // Collect all images (both existing previews and new uploads)
    const images = [];
    for (let i = 1; i <= MAX_PHOTOS_PER_ENTRY; i++) {
        const preview = document.getElementById(`galleryPreview${i}`);

        if (preview && preview.src && preview.style.display !== 'none' && !preview.src.includes('data:,')) {
            images.push(preview.src);
        }
    }

    if (images.length === 0) {
        showNotification('Please upload at least one image', 'error');
        return;
    }

    if (currentEditingGalleryId) {
        // Update existing gallery
        const photoIndex = galleryPhotos.findIndex(p => p.id === currentEditingGalleryId);
        if (photoIndex !== -1) {
            galleryPhotos[photoIndex].title = title;
            galleryPhotos[photoIndex].description = description;
            galleryPhotos[photoIndex].images = images;
            showNotification('Gallery updated successfully!', 'success');
        } else {
            showNotification('Gallery not found!', 'error');
            return;
        }
    } else {
        // Add new gallery
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

    // Re-render gallery
    renderGallery();

    // Save to backend
    saveGalleryToBackend();

    closeGalleryModal();
}

function closeGalleryModal() {
    document.getElementById('galleryPhotoModal').style.display = 'none';
    document.body.style.overflow = 'auto';

    // Reset form fields
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

    currentEditingGalleryId = null;
}

function renderGallery() {
    const galleryGrid = document.getElementById('galleryGrid');

    if (!galleryGrid) return;

    if (galleryPhotos.length === 0) {
        galleryGrid.innerHTML = `
            <div class="gallery-empty" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-images" style="font-size: 48px; margin-bottom: 16px; color: #ddd;"></i>
                <h4>No Photo Albums Yet</h4>
                <p>Click the "Add Album" button to create your first photo album!</p>
            </div>
        `;
        return;
    }

    galleryGrid.innerHTML = galleryPhotos.map(photo => {
        const images = photo.images || [];
        if (images.length === 0) return '';

        const carouselImages = images.map((img, index) => `
            <div class="carousel-image ${index === 0 ? 'active' : ''}">
                <img src="${img}" alt="${escapeHtml(photo.title)} - Photo ${index + 1}">
            </div>
        `).join('');

        const indicators = images.map((_, index) => `
            <span class="indicator ${index === 0 ? 'active' : ''}" onclick="setCarouselImage(${photo.id}, ${index})"></span>
        `).join('');

        const showControls = images.length > 1;

        return `
            <div class="gallery-item editable" data-gallery-id="${photo.id}">
                <div class="gallery-images-container">
                    <div class="gallery-image-carousel">
                        ${carouselImages}
                    </div>
                    ${showControls ? `
                        <div class="carousel-controls">
                            <button class="carousel-btn prev" onclick="changeCarouselImage(${photo.id}, -1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="carousel-btn next" onclick="changeCarouselImage(${photo.id}, 1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    ` : ''}
                    ${images.length > 1 ? `
                        <div class="carousel-indicators">
                            ${indicators}
                        </div>
                    ` : ''}
                    <div class="gallery-actions-overlay">
                        <button type="button" class="gallery-action-btn edit" onclick="editGalleryItem(${photo.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="gallery-action-btn delete" onclick="deleteGalleryItem(${photo.id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="gallery-content">
                    <h4 class="gallery-title">${escapeHtml(photo.title)}</h4>
                    <p class="gallery-description">${escapeHtml(photo.description)}</p>
                </div>
            </div>
        `;
    }).filter(html => html !== '').join('');
}

document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('galleryTitle');
    const descriptionInput = document.getElementById('galleryDescription');

    if (titleInput) {
        titleInput.addEventListener('input', function () {
            updateCharacterCounter(this, 50);
        });
    }

    if (descriptionInput) {
        descriptionInput.addEventListener('input', function () {
            updateCharacterCounter(this, 150);
        });
    }
});

function updateCharacterCounter(input, maxLength) {
    const currentLength = input.value.length;
    const remainingChars = maxLength - currentLength;

    let counter = input.parentElement.querySelector('.character-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'character-counter';
        input.parentElement.appendChild(counter);
    }

    counter.textContent = `${currentLength}/${maxLength} characters`;

    counter.classList.remove('warning', 'danger');
    if (remainingChars <= 10 && remainingChars > 0) {
        counter.classList.add('warning');
    } else if (remainingChars <= 0) {
        counter.classList.add('danger');
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

// Global notification function for gallery operations
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.gallery-notification').forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `gallery-notification notification-${type}`;

    const iconMap = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };

    const colorMap = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#3b82f6'
    };

    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${iconMap[type] || iconMap['info']}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${colorMap[type] || colorMap['info']};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
        font-weight: 500;
    `;

    // Add animation styles if not already present
    if (!document.querySelector('style[data-gallery-notification]')) {
        const style = document.createElement('style');
        style.setAttribute('data-gallery-notification', 'true');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
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
        document.head.appendChild(style);
    }

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Backend integration for gallery
function saveGalleryToBackend() {
    console.log('Saving gallery to backend...', galleryPhotos);
    console.log('Gallery array length:', galleryPhotos.length);
    console.log('Gallery to save:', JSON.stringify(galleryPhotos).substring(0, 200) + '...');

    // Always save to localStorage as backup
    try {
        localStorage.setItem('galleryPhotos', JSON.stringify(galleryPhotos));
        console.log('✓ Gallery saved to localStorage');
    } catch (e) {
        console.warn('Failed to save to localStorage:', e);
    }

    // Prepare the request body
    const requestBody = JSON.stringify({ gallery: galleryPhotos });
    console.log('Request body size:', requestBody.length, 'bytes');

    // Save gallery data to backend
    fetch('/unipulse/public/user/profile/updateGallery', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: requestBody
    })
        .then(async (response) => {
            console.log('✓ Fetch completed with status:', response.status);
            const contentType = response.headers.get('content-type');
            console.log('Response Content-Type:', contentType);

            let text = '';
            try {
                text = await response.text();
                console.log('Response body length:', text.length);
                console.log('Response text:', text.substring(0, 300));
            } catch (e) {
                console.error('Failed to read response text:', e);
                showNotification('Gallery saved locally. Sync status unclear.', 'warning');
                return;
            }

            if (!text || text.trim() === '') {
                console.error('Backend returned empty response');
                showNotification('Gallery saved locally. Server response empty.', 'warning');
                return;
            }

            let data = null;
            try {
                data = JSON.parse(text);
                console.log('✓ Response parsed as JSON:', data);
            } catch (e) {
                console.error('Failed to parse response as JSON:', e);
                console.error('Attempted to parse:', text);
                showNotification('Gallery saved locally. Server response format issue.', 'warning');
                return;
            }

            if (response.status !== 200 && response.status !== 201) {
                const msg = data?.message || data?.error || `HTTP ${response.status}`;
                console.warn('Backend returned non-success status:', response.status, msg);
                showNotification(`Server error ${response.status}. Saved locally.`, 'warning');
                return;
            }

            if (data?.success === true) {
                console.log('✓ Gallery saved to backend successfully');
                showNotification('Gallery saved successfully!', 'success');
            } else {
                const msg = data?.message || data?.error || 'Unknown error';
                console.warn('Backend returned success=false:', msg);
                showNotification(`Server error. Saved locally.`, 'warning');
            }
        })
        .catch(error => {
            console.error('Fetch failed with error:', error);
            console.error('Error type:', error.name);
            console.error('Error message:', error.message);
            showNotification('Gallery saved locally (offline mode)', 'warning');
        });
}

function loadGalleryFromBackend() {
    // Load gallery data from backend
    console.log('Loading gallery from backend...');

    fetch('/unipulse/public/user/profile/getGallery', {
        credentials: 'same-origin'
    })
        .then(async (response) => {
            console.log('Backend response status:', response.status);
            let data = null;
            try {
                const text = await response.text();
                console.log('Backend response:', text);
                data = JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse response:', e);
                throw e;
            }

            if (data.success && data.gallery && Array.isArray(data.gallery) && data.gallery.length > 0) {
                console.log('✓ Gallery loaded from backend:', data.gallery.length, 'albums');
                galleryPhotos = data.gallery;
                renderGallery();
            } else {
                console.log('No gallery data from backend, checking localStorage...');
                const stored = localStorage.getItem('galleryPhotos');
                if (stored) {
                    try {
                        const parsed = JSON.parse(stored);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            console.log('✓ Gallery loaded from localStorage:', parsed.length, 'albums');
                            galleryPhotos = parsed;
                            renderGallery();
                            return;
                        }
                    } catch (e) {
                        console.warn('Failed to parse localStorage:', e);
                    }
                }
                console.log('No gallery found, showing empty state');
                renderGallery();
            }
        })
        .catch(error => {
            console.error('Error loading gallery from backend:', error);
            console.log('Falling back to localStorage...');

            const stored = localStorage.getItem('galleryPhotos');
            if (stored) {
                try {
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        console.log('✓ Gallery loaded from localStorage:', parsed.length, 'albums');
                        galleryPhotos = parsed;
                        renderGallery();
                        return;
                    }
                } catch (e) {
                    console.warn('Failed to parse localStorage:', e);
                }
            }
            renderGallery();
        });
}

// Initialize gallery on page load
document.addEventListener('DOMContentLoaded', function () {
    // Load gallery from backend
    loadGalleryFromBackend();
});

// Close gallery modal when clicking outside
document.addEventListener('click', function (event) {
    const modal = document.getElementById('galleryPhotoModal');
    if (event.target === modal) {
        closeGalleryModal();
    }
});

// Close gallery modal with escape key
document.addEventListener('keydown', function (event) {
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