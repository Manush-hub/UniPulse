// Initialize sponsor details page
document.addEventListener('DOMContentLoaded', function () {
    initializeSponsorDetails();
    setupEventListeners();
    handleURLMessages();
});

function initializeSponsorDetails() {
    // Any initialization logic for the sponsor details page
    console.log('Sponsor details page initialized');
}

function setupEventListeners() {
    // Modal close events
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('contactModal');
        if (event.target === modal) {
            closeContactModal();
        }
    });
    
    // Escape key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeContactModal();
        }
    });
    
    // Contact form submission
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactFormSubmission);
    }
}

function handleURLMessages() {
    // Handle success/error messages from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const contactStatus = urlParams.get('contact');
    
    if (contactStatus) {
        // Clean URL
        const url = new URL(window.location);
        url.searchParams.delete('contact');
        window.history.replaceState({}, '', url.toString());
        
        // Show message
        if (contactStatus === 'success') {
            showSuccessMessage('Your message has been sent successfully!');
        } else if (contactStatus === 'error') {
            showErrorMessage('Failed to send message. Please try again.');
        }
    }
}

// Modal functions
function openContactModal(sponsorId) {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Focus on subject field
        setTimeout(() => {
            const subjectField = document.getElementById('subject');
            if (subjectField) {
                subjectField.focus();
            }
        }, 100);
    }
}

function closeContactModal() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Reset form
        const form = document.getElementById('contactForm');
        if (form) {
            form.reset();
        }
    }
}

// Contact form handling
function handleContactFormSubmission(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate form
    const subject = formData.get('subject')?.trim();
    const message = formData.get('message')?.trim();
    
    if (!subject || !message) {
        showErrorMessage('Please fill in all required fields.');
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Sending...';
    submitButton.disabled = true;
    
    // Submit form
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            // Follow the redirect
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            closeContactModal();
            showSuccessMessageAndRedirect(data.message || 'Message sent successfully!');
            form.reset();
        } else {
            throw new Error(data?.message || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message || 'Failed to send message. Please try again.');
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}

// Navigation functions
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/unipulse/public/publisher/sponsors';
    }
}

function navigateToSponsors() {
    window.location.href = '/unipulse/public/publisher/sponsors';
}

function navigateToDashboard() {
    window.location.href = '/unipulse/public/publisher/dashboard';
}

// Utility functions for showing messages
function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    alertElement.innerHTML = `
        <div class="alert-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(alertElement);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertElement.parentElement) {
            alertElement.remove();
        }
    }, 5000);
}

// Email and phone click handlers
function handleEmailClick(email) {
    const subject = encodeURIComponent('Sponsorship Opportunity - UniPulse');
    const body = encodeURIComponent('Hello,\n\nI hope this email finds you well. I am reaching out regarding potential sponsorship opportunities for upcoming university events through UniPulse.\n\nBest regards,');
    window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
}

function handlePhoneClick(phone) {
    window.location.href = `tel:${phone}`;
}

// Add click handlers to email and phone links
document.addEventListener('DOMContentLoaded', function() {
    // Email links
    const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
    emailLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const email = this.href.replace('mailto:', '');
            handleEmailClick(email);
        });
    });
    
    // Phone links
    const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
    phoneLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const phone = this.href.replace('tel:', '');
            console.log(`Calling ${phone}`);
        });
    });
});

// Activity status monitoring (if needed for real-time updates)
function updateActivityStatus() {
    // This could be used to periodically check and update the sponsor's activity status
    // For now, it's just a placeholder for future functionality
    console.log('Activity status check');
}

// Export sponsor information
function exportSponsorInfo() {
    const sponsorName = document.querySelector('.profile-name')?.textContent || 'Unknown';
    const sponsorEmail = document.querySelector('.info-value a[href^="mailto:"]')?.textContent || '';
    const sponsorPhone = document.querySelector('.info-value a[href^="tel:"]')?.textContent || '';
    const joinDate = document.querySelector('.join-date')?.textContent || '';
    const activityStatus = document.querySelector('.sponsor-status')?.textContent || '';
    
    const sponsorData = {
        name: sponsorName,
        email: sponsorEmail,
        phone: sponsorPhone,
        joinDate: joinDate,
        activityStatus: activityStatus,
        exportedAt: new Date().toISOString()
    };
    
    // Create and download JSON file
    const dataStr = JSON.stringify(sponsorData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `sponsor_${sponsorName.replace(/\s+/g, '_').toLowerCase()}_${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Print sponsor details
function printSponsorDetails() {
    window.print();
}

// Share sponsor profile (if needed)
function shareSponsorProfile() {
    if (navigator.share) {
        const sponsorName = document.querySelector('.profile-name')?.textContent || 'Sponsor';
        navigator.share({
            title: `${sponsorName} - UniPulse Sponsor`,
            text: `Check out ${sponsorName} on UniPulse`,
            url: window.location.href
        });
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showSuccessMessage('Profile URL copied to clipboard');
        }).catch(() => {
            showErrorMessage('Failed to copy URL');
        });
    }
}

// Success message with redirect functionality
function showSuccessMessageAndRedirect(message) {
    // Create a custom success modal that will redirect
    const successModal = document.createElement('div');
    successModal.className = 'modal success-modal';
    successModal.style.cssText = `
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    `;
    
    successModal.innerHTML = `
        <div style="
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        ">
            <div style="
                color: #28a745;
                font-size: 48px;
                margin-bottom: 20px;
            ">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="
                color: #333;
                margin-bottom: 15px;
                font-size: 24px;
            ">Success!</h3>
            <p style="
                color: #666;
                margin-bottom: 25px;
                font-size: 16px;
                line-height: 1.5;
            ">${message}</p>
            <button onclick="redirectToSponsors()" style="
                background: #007bff;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s;
            " onmouseover="this.style.backgroundColor='#0056b3'" onmouseout="this.style.backgroundColor='#007bff'">
                Back to Sponsors
            </button>
        </div>
    `;
    
    document.body.appendChild(successModal);
    
    // Auto redirect after 3 seconds
    setTimeout(() => {
        redirectToSponsors();
    }, 3000);
}

function redirectToSponsors() {
    // Remove any success modals
    const successModals = document.querySelectorAll('.success-modal');
    successModals.forEach(modal => modal.remove());
    
    // Redirect to sponsors page
    window.location.href = '/unipulse/public/publisher/sponsors';
}