class SponsorProfile {
    constructor() {
        this.currentTab = 'about';
        this.sponsorData = {
            name: 'Tech Innovation Corp',
            type: 'company',
            tagline: 'Empowering innovation through strategic partnerships and technology advancement.',
            industry: 'Technology & Software',
            email: 'partnerships@techinnovation.com',
            phone: '+1 (555) 123-4567',
            website: 'https://techinnovation.com',
            companySize: '51-200',
            address: '123 Innovation Drive, Tech Valley, CA 94043',
            mission: 'To drive technological advancement and foster innovation through strategic partnerships with educational institutions and emerging talent.',
            profession: '',
            achievements: '',
            awards: 'Best Corporate Partner 2024 - University Tech Awards',
            linkedin: 'https://linkedin.com/company/tech-innovation-corp',
            instagram: 'https://instagram.com/tech_innovation_corp',
            twitter: 'https://twitter.com/TechInnovCorp',
            facebook: 'https://facebook.com/TechInnovationCorp',
            youtube: '',
            github: '',
            logo: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=cover&w=400&q=80'
        };
        
        this.sponsorshipRecords = [
            {
                id: 1,
                eventName: 'Annual Tech Conference 2024',
                organizer: 'Berkeley Computer Science Society',
                date: '2024-03-15',
                type: 'cash',
                details: 'Cash Sponsorship + Venue',
                amount: 15000,
                status: 'completed'
            },
            {
                id: 2,
                eventName: 'Startup Pitch Competition 2024',
                organizer: 'Entrepreneurship Club',
                date: '2024-06-08',
                type: 'product',
                details: 'Product Prizes + Mentorship',
                amount: 8500,
                status: 'completed'
            },
            {
                id: 3,
                eventName: 'Women in Tech Workshop Series',
                organizer: 'Women Engineers Association',
                date: '2023-09-01',
                type: 'media',
                details: 'Media Coverage + Platform',
                amount: 5000,
                status: 'completed'
            }
        ];
        
        this.testimonials = [
            {
                id: 1,
                organizerName: 'Sarah Johnson',
                organizerRole: 'President, Computer Science Society',
                organizerAvatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=60&h=60&fit=crop&crop=face',
                eventName: 'Annual Tech Conference 2024',
                rating: 5,
                content: 'Tech Innovation Corp was an exceptional partner for our annual conference. Their support went beyond financial contribution - they provided mentors, technical expertise, and valuable networking opportunities for our students. Highly recommend!',
                date: '2024-08-20'
            },
            {
                id: 2,
                organizerName: 'Michael Chen',
                organizerRole: 'Director, Entrepreneurship Hub',
                organizerAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face',
                eventName: 'Startup Pitch Competition',
                rating: 5,
                content: 'Outstanding partnership! Not only did they provide generous prizes, but their team also served as judges and mentors. The students gained invaluable insights from industry professionals.',
                date: '2024-06-15'
            },
            {
                id: 3,
                organizerName: 'Emily Rodriguez',
                organizerRole: 'Coordinator, Women Engineers Association',
                organizerAvatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face',
                eventName: 'Women in Tech Workshop Series',
                rating: 4,
                content: 'Great collaboration for our workshop series. Their media platform helped us reach a wider audience, and their commitment to supporting women in tech is genuine and impactful.',
                date: '2024-03-10'
            }
        ];
        
        this.pressReleases = [
            {
                id: 1,
                title: 'Tech Innovation Corp Partners with University for Annual Tech Summit',
                date: '2024-08-15',
                excerpt: 'Major sponsorship announcement for the largest student technology conference...',
                link: '#'
            },
            {
                id: 2,
                title: 'Local Company Receives "Best Corporate Partner" Award',
                date: '2024-06-22',
                excerpt: 'Recognition for outstanding contribution to student development programs...',
                link: '#'
            }
        ];
        
        this.focusAreas = ['Technology', 'Education', 'Innovation', 'Entrepreneurship', 'Research & Development'];
        this.analytics = {
            eventsSponsored: 25,
            totalContribution: 125000,
            studentsImpacted: 50000,
            partnerOrganizations: 12
        };
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSponsorData();
        this.setupAnimations();
        this.setupImageUploads();
        this.loadSponsorshipRecords();
        this.loadTestimonials();
        this.loadPressReleases();
        this.loadAnalytics();
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
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    setupProfilePhotoUpload() {
        const avatarOverlay = document.querySelector('.avatar-overlay');
        const profileInput = document.getElementById('profileInput');

        if (avatarOverlay && profileInput) {
            avatarOverlay.addEventListener('click', () => {
                profileInput.click();
            });

            profileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const profileImg = document.getElementById('profileImage');
                        if (profileImg) {
                            profileImg.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
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
        // Sponsor info form
        const sponsorForm = document.getElementById('sponsor-form');
        if (sponsorForm) {
            sponsorForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSponsorInfo();
            });
        }

        // Contact info form
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveContactInfo();
            });
        }

        // Preference buttons
        document.querySelectorAll('.preference-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.togglePreference(e.target);
            });
        });

        // Security form
        const securityForm = document.getElementById('security-form');
        if (securityForm) {
            securityForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSecuritySettings();
            });
        }
    }

    bindToggleEvents() {
        // Privacy settings toggles
        document.querySelectorAll('.toggle input').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.updatePrivacySetting(e.target.id, e.target.checked);
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

    loadSponsorData() {
        // Load sponsor data into form fields
        Object.keys(this.sponsorData).forEach(key => {
            const element = document.getElementById(key) || 
                           document.getElementById('sponsor' + key.charAt(0).toUpperCase() + key.slice(1));
            if (element) {
                element.value = this.sponsorData[key];
            }
        });

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

    loadSponsorshipRecords() {
        // This would load sponsorship records from the backend
        console.log('Loading sponsorship records:', this.sponsorshipRecords);
    }

    loadTestimonials() {
        // This would load testimonials from the backend
        console.log('Loading testimonials:', this.testimonials);
    }

    loadPressReleases() {
        // This would load press releases from the backend
        console.log('Loading press releases:', this.pressReleases);
    }

    loadAnalytics() {
        // This would load analytics from the backend
        console.log('Loading analytics:', this.analytics);
    }

    togglePreference(button) {
        button.classList.toggle('active');
        const preference = button.dataset.preference;
        const isActive = button.classList.contains('active');
        
        if (isActive && !this.focusAreas.includes(preference)) {
            this.focusAreas.push(preference);
        } else if (!isActive) {
            const index = this.focusAreas.indexOf(preference);
            if (index > -1) {
                this.focusAreas.splice(index, 1);
            }
        }
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

    saveSponsorInfo() {
        const formData = {
            name: document.getElementById('sponsorName').value,
            type: document.getElementById('sponsorType').value,
            tagline: document.getElementById('tagline').value,
            industry: document.getElementById('industry').value,
            email: document.getElementById('sponsorEmail').value,
            phone: document.getElementById('sponsorPhone').value,
            website: document.getElementById('sponsorWebsite').value,
            companySize: document.getElementById('companySize').value,
            address: document.getElementById('sponsorAddress').value,
            mission: document.getElementById('mission').value,
            profession: document.getElementById('profession').value,
            achievements: document.getElementById('achievements').value,
            awards: document.getElementById('awards').value
        };

        // Update sponsorData
        Object.assign(this.sponsorData, formData);
        console.log('Sponsor information saved:', formData);
    }

    saveContactInfo() {
        const contactData = {
            linkedin: document.getElementById('linkedin').value,
            instagram: document.getElementById('instagram').value,
            twitter: document.getElementById('twitter').value,
            facebook: document.getElementById('facebook').value,
            youtube: document.getElementById('youtube').value,
            github: document.getElementById('github').value
        };

        // Update sponsorData
        Object.assign(this.sponsorData, contactData);
        console.log('Contact information saved:', contactData);
    }

    saveSecuritySettings() {
        const securityData = {
            primaryEmail: document.getElementById('primaryEmail').value,
            currentPassword: document.getElementById('currentPassword').value,
            newPassword: document.getElementById('newPassword').value,
            confirmPassword: document.getElementById('confirmPassword').value
        };

        // Validate passwords match
        if (securityData.newPassword !== securityData.confirmPassword) {
            alert('New passwords do not match');
            return;
        }

        console.log('Security settings saved');
        // Clear password fields
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    updatePrivacySetting(settingId, isEnabled) {
        console.log(`Privacy setting ${settingId} set to:`, isEnabled);
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Sponsorship record management
    addSponsorshipRecord() {
        this.openModal('sponsorshipRecordModal');
        document.getElementById('recordModalTitle').textContent = 'Add Sponsorship Record';
        this.clearSponsorshipRecordForm();
    }

    editSponsorshipRecord(recordId) {
        const record = this.sponsorshipRecords.find(r => r.id === recordId);
        if (record) {
            this.openModal('sponsorshipRecordModal');
            document.getElementById('recordModalTitle').textContent = 'Edit Sponsorship Record';
            this.populateSponsorshipRecordForm(record);
        }
    }

    deleteSponsorshipRecord(recordId) {
        if (confirm('Are you sure you want to delete this sponsorship record?')) {
            this.sponsorshipRecords = this.sponsorshipRecords.filter(r => r.id !== recordId);
            console.log('Sponsorship record deleted:', recordId);
        }
    }

    saveSponsorshipRecord() {
        const formData = {
            eventName: document.getElementById('eventName').value,
            organizer: document.getElementById('eventOrganizer').value,
            date: document.getElementById('eventDate').value,
            type: document.getElementById('sponsorshipType').value,
            details: document.getElementById('contributionDetails').value,
            amount: parseFloat(document.getElementById('contributionAmount').value),
            status: document.getElementById('eventStatus').value
        };

        // Validate required fields
        if (!formData.eventName || !formData.organizer || !formData.date || !formData.type) {
            alert('Please fill in all required fields');
            return;
        }

        // Add or update record
        const newRecord = {
            id: Date.now(),
            ...formData
        };

        this.sponsorshipRecords.push(newRecord);
        console.log('Sponsorship record saved:', newRecord);
        this.closeModal('sponsorshipRecordModal');
    }

    clearSponsorshipRecordForm() {
        document.getElementById('sponsorshipRecordForm').reset();
    }

    populateSponsorshipRecordForm(record) {
        document.getElementById('eventName').value = record.eventName;
        document.getElementById('eventOrganizer').value = record.organizer;
        document.getElementById('eventDate').value = record.date;
        document.getElementById('sponsorshipType').value = record.type;
        document.getElementById('contributionDetails').value = record.details;
        document.getElementById('contributionAmount').value = record.amount;
        document.getElementById('eventStatus').value = record.status;
    }

    closeSponsorshipRecordModal() {
        this.closeModal('sponsorshipRecordModal');
    }

    // Press release management
    addPressRelease() {
        this.openModal('pressReleaseModal');
        this.clearPressReleaseForm();
    }

    savePressRelease() {
        const formData = {
            title: document.getElementById('pressTitle').value,
            date: document.getElementById('pressDate').value,
            excerpt: document.getElementById('pressExcerpt').value,
            link: document.getElementById('pressLink').value
        };

        // Validate required fields
        if (!formData.title || !formData.date || !formData.excerpt) {
            alert('Please fill in all required fields');
            return;
        }

        const newRelease = {
            id: Date.now(),
            ...formData
        };

        this.pressReleases.push(newRelease);
        console.log('Press release saved:', newRelease);
        this.closeModal('pressReleaseModal');
    }

    clearPressReleaseForm() {
        document.getElementById('pressReleaseForm').reset();
    }

    closePressReleaseModal() {
        this.closeModal('pressReleaseModal');
    }

    // Utility functions
    downloadProfile() {
        console.log('Downloading sponsor profile PDF...');
        alert('Profile download feature will be implemented soon!');
    }

    downloadResource(resourceType) {
        console.log('Downloading resource:', resourceType);
        alert(`${resourceType} download feature will be implemented soon!`);
    }

    exportSponsorshipData() {
        console.log('Exporting sponsorship data...');
        alert('Data export feature will be implemented soon!');
    }

    generateReport() {
        console.log('Generating sponsorship report...');
        alert('Report generation feature will be implemented soon!');
    }

    deactivateSponsor() {
        if (confirm('Are you sure you want to deactivate your sponsor profile?')) {
            console.log('Sponsor profile deactivated');
            alert('Profile deactivated successfully');
        }
    }

    deleteSponsorAccount() {
        if (confirm('Are you sure you want to delete your sponsor account? This action cannot be undone.')) {
            if (confirm('Please confirm again. This will permanently delete all your data.')) {
                console.log('Sponsor account deleted');
                alert('Account deleted successfully');
            }
        }
    }
}

// Global functions for onclick handlers
function uploadImage() {
    sponsorProfile.uploadImage();
}

function saveSponsorInfo() {
    sponsorProfile.saveSponsorInfo();
}

function saveContactInfo() {
    sponsorProfile.saveContactInfo();
}

function saveSecuritySettings() {
    sponsorProfile.saveSecuritySettings();
}

function closeModal(modalId) {
    sponsorProfile.closeModal(modalId);
}

function addSponsorshipRecord() {
    sponsorProfile.addSponsorshipRecord();
}

function editSponsorshipRecord(recordId) {
    sponsorProfile.editSponsorshipRecord(recordId);
}

function deleteSponsorshipRecord(recordId) {
    sponsorProfile.deleteSponsorshipRecord(recordId);
}

function saveSponsorshipRecord() {
    sponsorProfile.saveSponsorshipRecord();
}

function closeSponsorshipRecordModal() {
    sponsorProfile.closeSponsorshipRecordModal();
}

function addPressRelease() {
    sponsorProfile.addPressRelease();
}

function savePressRelease() {
    sponsorProfile.savePressRelease();
}

function closePressReleaseModal() {
    sponsorProfile.closePressReleaseModal();
}

function downloadProfile() {
    sponsorProfile.downloadProfile();
}

function downloadResource(resourceType) {
    sponsorProfile.downloadResource(resourceType);
}

function exportSponsorshipData() {
    sponsorProfile.exportSponsorshipData();
}

function generateReport() {
    sponsorProfile.generateReport();
}

function deactivateSponsor() {
    sponsorProfile.deactivateSponsor();
}

function deleteSponsorAccount() {
    sponsorProfile.deleteSponsorAccount();
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
            }
        };
        reader.readAsDataURL(file);
    }
}

// Initialize the sponsor profile manager when DOM is loaded
let sponsorProfile;
document.addEventListener('DOMContentLoaded', () => {
    sponsorProfile = new SponsorProfile();
    
    // Add extra interactivity
    document.addEventListener('keydown', (e) => {
        // ESC key to close modals
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                sponsorProfile.closeModal(modal.id);
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