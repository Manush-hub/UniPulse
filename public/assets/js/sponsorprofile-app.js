class SponsorProfileApp {
    constructor() {
        this.currentTab = 'about';
        this.originalFormData = {};
        this.init();
    }

    init() {
        this.setupTabNavigation();
        this.loadProfileData();
        this.setupEventListeners();
    }

    setupTabNavigation() {
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = item.dataset.tab;
                this.switchTab(tab);
            });
        });
    }

    switchTab(tabName) {
        // Update navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.tab === tabName) {
                item.classList.add('active');
            }
        });

        // Update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            if (content.id === tabName) {
                content.classList.add('active');
            }
        });

        this.currentTab = tabName;
    }

    loadProfileData() {
        // Load data from PHP-passed sponsorData variable
        if (typeof sponsorData === 'undefined' || !sponsorData) {
            console.warn('No sponsor data available');
            return;
        }

        console.log('Loading sponsor data:', sponsorData);

        // Map database fields to form fields
        const formFieldMapping = {
            'company_name': 'sponsorName',
            'email': 'sponsorEmail',
            'phone': 'sponsorPhone',
            'sponsor_type': 'sponsorType',
            'industry': 'industry',
            'company_size': 'companySize',
            'address': 'sponsorAddress',
            'headline': 'headline',
            'about': 'sponsorAbout',
            'website': 'website',
            'facebook': 'facebook',
            'instagram': 'instagram',
            'linkedin': 'linkedin',
            'twitter': 'twitter',
            'youtube': 'youtube'
        };

        // Populate form fields and store original values for cancel
        Object.keys(formFieldMapping).forEach(dbField => {
            const formFieldId = formFieldMapping[dbField];
            const element = document.getElementById(formFieldId);
            if (element) {
                const value = sponsorData[dbField] || '';
                element.value = value;
                // Store original value for cancel functionality
                this.originalFormData[formFieldId] = value;
            }
        });

        // Load interests/preferences
        if (sponsorData.interests) {
            let interests = [];
            try {
                interests = typeof sponsorData.interests === 'string'
                    ? JSON.parse(sponsorData.interests)
                    : sponsorData.interests;
            } catch (e) {
                console.error('Error parsing interests:', e);
            }

            // Mark active preferences
            document.querySelectorAll('.preference-btn').forEach(btn => {
                const preference = btn.dataset.preference;
                if (interests.includes(preference)) {
                    btn.classList.add('active');
                }
            });
        }

        // Update profile images
        if (sponsorData.logo_url) {
            const profileImage = document.getElementById('profileImage');
            if (profileImage) {
                profileImage.src = sponsorData.logo_url;
            }
        }

        if (sponsorData.cover_photo_url) {
            const coverPhoto = document.getElementById('coverPhoto');
            if (coverPhoto) {
                coverPhoto.src = sponsorData.cover_photo_url;
            }
        }

        // Update profile name display
        const profileName = document.querySelector('.profile-name');
        if (profileName && sponsorData.company_name) {
            profileName.textContent = sponsorData.company_name;
        }

        const profileEmail = document.querySelector('.profile-email');
        if (profileEmail && sponsorData.email) {
            profileEmail.textContent = sponsorData.email;
        }
    }

    setupEventListeners() {
        // Preference buttons toggle with immediate DB update
        document.querySelectorAll('.preference-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                btn.classList.toggle('active');
                // Update interests in database immediately
                await this.updateInterests();
            });
        });

        // Make sure global functions are available
        window.saveSponsorInfo = () => this.saveSponsorInfo();
        window.cancelSponsorInfo = () => this.cancelSponsorInfo();
        window.saveContactInfo = () => this.saveContactInfo();
        window.cancelContactInfo = () => this.cancelContactInfo();
        window.changePassword = () => this.changePassword();
        window.uploadProfileImage = () => this.uploadProfileImage();
        window.uploadCover = () => this.uploadCover();
        window.changeProfileImage = (event) => this.changeProfileImage(event);
        window.changeCover = (event) => this.changeCover(event);
        window.deleteAccount = () => this.deleteAccount();
    }

    // Save Basic Information
    async saveSponsorInfo() {
        const form = document.getElementById('sponsor-form');
        const formData = {
            sponsorName: document.getElementById('sponsorName').value,
            sponsorPhone: document.getElementById('sponsorPhone').value,
            sponsorType: document.getElementById('sponsorType').value,
            industry: document.getElementById('industry').value,
            companySize: document.getElementById('companySize').value,
            sponsorAddress: document.getElementById('sponsorAddress').value,
            headline: document.getElementById('headline').value,
            about: document.getElementById('sponsorAbout').value
        };

        // Collect active preferences
        const interests = [];
        document.querySelectorAll('.preference-btn.active').forEach(btn => {
            interests.push(btn.dataset.preference);
        });
        formData.interests = JSON.stringify(interests);

        // Validate required fields (email is readonly and pre-filled from registration)
        if (!formData.sponsorName || !formData.sponsorName.trim()) {
            this.showNotification('Company Name is required', 'error');
            return;
        }
        if (!formData.sponsorType) {
            this.showNotification('Company Type is required', 'error');
            return;
        }
        if (!formData.industry || !formData.industry.trim()) {
            this.showNotification('Industry / Sector is required', 'error');
            return;
        }
        if (!formData.sponsorPhone || !formData.sponsorPhone.trim()) {
            this.showNotification('Phone Number is required', 'error');
            return;
        }
        if (!formData.about || !formData.about.trim()) {
            this.showNotification('About section is required', 'error');
            return;
        }

        try {
            const response = await fetch('/UniPulse/public/sponsor/profile/updateSponsorInfo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Update displayed profile name if changed
                if (formData.sponsorName) {
                    const profileName = document.querySelector('.profile-name');
                    if (profileName) {
                        profileName.textContent = formData.sponsorName;
                    }

                    // Update header bar username
                    const headerUsername = document.getElementById('username');
                    if (headerUsername) {
                        headerUsername.textContent = formData.sponsorName;
                    }
                }

                // Update stored original data so cancel works with new values
                const fieldMapping = {
                    'sponsorName': 'sponsorName',
                    'sponsorPhone': 'sponsorPhone',
                    'sponsorType': 'sponsorType',
                    'industry': 'industry',
                    'companySize': 'companySize',
                    'sponsorAddress': 'sponsorAddress',
                    'headline': 'headline',
                    'about': 'about'
                };

                Object.keys(formData).forEach(key => {
                    if (fieldMapping[key]) {
                        this.originalFormData[fieldMapping[key]] = formData[key];
                    }
                });

                this.showNotification('Profile updated successfully!', 'success');
            } else {
                this.showNotification(result.message || 'Failed to update profile', 'error');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            this.showNotification('An error occurred while updating profile', 'error');
        }
    }

    // Update interests/focus areas immediately
    async updateInterests() {
        try {
            // Collect active preferences
            const interests = [];
            document.querySelectorAll('.preference-btn.active').forEach(btn => {
                interests.push(btn.dataset.preference);
            });

            const response = await fetch('/UniPulse/public/sponsor/profile/updateInterests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ interests: interests })
            });

            const result = await response.json();

            if (result.success) {
                console.log('Interests updated successfully');
                // Update sponsorData so cancel works correctly
                if (this.sponsorData) {
                    this.sponsorData.interests = JSON.stringify(interests);
                }
            } else {
                console.error('Failed to update interests:', result.message);
            }
        } catch (error) {
            console.error('Error updating interests:', error);
        }
    }

    // Cancel editing and restore original values
    cancelSponsorInfo() {
        const form = document.getElementById('sponsor-form');

        // Restore original values
        Object.keys(this.originalFormData).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = this.originalFormData[fieldId];
            }
        });

        // Restore original preferences
        if (sponsorData.interests) {
            let interests = [];
            try {
                interests = typeof sponsorData.interests === 'string'
                    ? JSON.parse(sponsorData.interests)
                    : sponsorData.interests;
            } catch (e) {
                console.error('Error parsing interests:', e);
            }

            // Reset all preferences first
            document.querySelectorAll('.preference-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Mark original active preferences
            document.querySelectorAll('.preference-btn').forEach(btn => {
                const preference = btn.dataset.preference;
                if (interests.includes(preference)) {
                    btn.classList.add('active');
                }
            });
        }

        this.showNotification('Changes discarded', 'info');
    }

    // Save Contact Information
    async saveContactInfo() {
        const formData = {
            website: document.getElementById('website').value,
            facebook: document.getElementById('facebook').value,
            instagram: document.getElementById('instagram').value,
            linkedin: document.getElementById('linkedin').value,
            twitter: document.getElementById('twitter').value,
            youtube: document.getElementById('youtube').value
        };

        try {
            const response = await fetch('/UniPulse/public/sponsor/profile/updateContactInfo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Contact information updated successfully!', 'success');

                // Update stored original data
                Object.keys(formData).forEach(key => {
                    this.originalFormData[key] = formData[key];
                });
            } else {
                this.showNotification(result.message || 'Failed to update contact information', 'error');
            }
        } catch (error) {
            console.error('Error updating contact info:', error);
            this.showNotification('An error occurred while updating contact information', 'error');
        }
    }

    // Cancel editing contact information
    cancelContactInfo() {
        const form = document.getElementById('contact-form');

        // Restore original values
        form.querySelectorAll('input').forEach(field => {
            if (this.originalFormData[field.id] !== undefined) {
                field.value = this.originalFormData[field.id];
            }
        });

        this.showNotification('Changes discarded', 'info');
    }

    // Change Password
    async changePassword() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validation
        if (!currentPassword || !newPassword || !confirmPassword) {
            this.showNotification('Please fill in all password fields', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            this.showNotification('New passwords do not match', 'error');
            return;
        }

        if (newPassword.length < 8) {
            this.showNotification('Password must be at least 8 characters long', 'error');
            return;
        }

        try {
            const response = await fetch('/UniPulse/public/sponsor/profile/changePassword', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    currentPassword: currentPassword,
                    newPassword: newPassword,
                    confirmPassword: confirmPassword
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Password changed successfully!', 'success');
                // Clear password fields
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
            } else {
                this.showNotification(result.message || 'Failed to change password', 'error');
            }
        } catch (error) {
            console.error('Error changing password:', error);
            this.showNotification('An error occurred while changing password', 'error');
        }
    }

    // Upload Profile Image (Logo)
    uploadProfileImage() {
        document.getElementById('profileInput').click();
    }

    changeProfileImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Preview the image
        const reader = new FileReader();
        reader.onload = (e) => {
            const profileImage = document.getElementById('profileImage');
            if (profileImage) {
                profileImage.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);

        // Upload to server
        this.uploadImageToServer(file, 'logo');
    }

    // Upload Cover Photo
    uploadCover() {
        document.getElementById('coverInput').click();
    }

    changeCover(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Preview the image
        const reader = new FileReader();
        reader.onload = (e) => {
            const coverPhoto = document.getElementById('coverPhoto');
            if (coverPhoto) {
                coverPhoto.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);

        // Upload to server
        this.uploadImageToServer(file, 'cover');
    }

    async uploadImageToServer(file, type) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', type);

        console.log('Uploading image:', { fileName: file.name, fileType: file.type, uploadType: type });

        try {
            const response = await fetch('/UniPulse/public/sponsor/profile/uploadImage', {
                method: 'POST',
                body: formData
            });

            console.log('Upload response status:', response.status);

            const result = await response.json();
            console.log('Upload result:', result);

            if (result.success) {
                // Update header avatar if logo was uploaded
                if (type === 'logo' && result.url) {
                    const headerAvatar = document.getElementById('headerAvatar');
                    if (headerAvatar) {
                        headerAvatar.src = result.url;
                    }
                }
                this.showNotification(`${type === 'logo' ? 'Logo' : 'Cover photo'} updated successfully!`, 'success');
            } else {
                console.error('Upload failed:', result.message);
                this.showNotification(result.message || 'Failed to upload image', 'error');
            }
        } catch (error) {
            console.error('Error uploading image:', error);
            this.showNotification('An error occurred while uploading image', 'error');
        }
    }

    // Delete Account
    async deleteAccount() {
        if (confirm('Deactivate your sponsor account now? Signing in again will reactivate it.')) {
            try {
                const response = await fetch('/UniPulse/public/sponsor/profile/deleteAccount', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    this.showNotification('Could not process server response. Please try again later.', 'error');
                    return;
                }

                if (!response.ok) {
                    this.showNotification(result.message || 'Account deactivation failed. Please try again.', 'error');
                    return;
                }

                if (result.success) {
                    window.location.href = '/UniPulse/public/signin?message=logout_success';
                } else {
                    this.showNotification(result.message || 'Failed to deactivate account', 'error');
                }
            } catch (error) {
                console.error('Error deactivating account:', error);
                this.showNotification('Unable to deactivate account right now. Please check your connection and try again.', 'error');
            }
        }
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
            color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new SponsorProfileApp();
});
