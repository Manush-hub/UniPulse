// Initialize sponsors page on load
document.addEventListener('DOMContentLoaded', function () {
    console.log('Sponsors page JavaScript loaded successfully!');
    initializeSponsorsPage();
    setupEventListeners();
});

let allSponsors = [];

function initializeSponsorsPage() {
    // Store all sponsor cards for filtering
    allSponsors = Array.from(document.querySelectorAll('.sponsor-card'));
    
    // Check for success message from redirect
    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        // Remove the message from storage
        sessionStorage.removeItem('successMessage');
        
        // Show success popup
        showSuccessPopup(successMessage);
    }
    
    // Set up search functionality
    setupSearch();
    setupFilters();
}

function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchSponsors');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterSponsors, 300));
    }
    
    // Filter selects
    const activityFilter = document.getElementById('activityFilter');
    const sortBy = document.getElementById('sortBy');
    
    if (activityFilter) {
        activityFilter.addEventListener('change', filterSponsors);
    }
    
    if (sortBy) {
        sortBy.addEventListener('change', sortSponsors);
    }
    
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
}

function setupSearch() {
    const searchInput = document.getElementById('searchSponsors');
    if (searchInput) {
        // Add search icon click functionality
        const searchIcon = searchInput.previousElementSibling;
        if (searchIcon && searchIcon.tagName === 'svg') {
            searchIcon.style.cursor = 'pointer';
            searchIcon.addEventListener('click', function() {
                searchInput.focus();
            });
        }
    }
}

function setupFilters() {
    // Initialize filters with current values
    filterSponsors();
}

function filterSponsors() {
    const searchTerm = document.getElementById('searchSponsors').value.toLowerCase();
    const activityFilter = document.getElementById('activityFilter').value;
    
    const sponsorsGrid = document.getElementById('sponsorsGrid');
    const sponsorCards = sponsorsGrid.querySelectorAll('.sponsor-card');
    
    let visibleCount = 0;
    
    sponsorCards.forEach(card => {
        const name = card.dataset.name || '';
        const email = card.dataset.email || '';
        const activity = card.dataset.activity || '';
        
        // Check search match
        const searchMatch = !searchTerm || 
            name.includes(searchTerm) || 
            email.includes(searchTerm);
        
        // Check activity filter
        const activityMatch = !activityFilter || activity === activityFilter;
        
        // Show/hide card
        if (searchMatch && activityMatch) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show no results message if needed
    showNoResultsMessage(visibleCount === 0 && (searchTerm || activityFilter));
    
    // Update URL with current filters (optional)
    updateURLFilters(searchTerm, activityFilter);
}

function sortSponsors() {
    const sortBy = document.getElementById('sortBy').value;
    const sponsorsGrid = document.getElementById('sponsorsGrid');
    const sponsorCards = Array.from(sponsorsGrid.querySelectorAll('.sponsor-card'));
    
    sponsorCards.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return new Date(b.dataset.created) - new Date(a.dataset.created);
            case 'oldest':
                return new Date(a.dataset.created) - new Date(b.dataset.created);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'name_desc':
                return b.dataset.name.localeCompare(a.dataset.name);
            default:
                return 0;
        }
    });
    
    // Reorder cards in the grid
    sponsorCards.forEach(card => {
        sponsorsGrid.appendChild(card);
    });
}

function showNoResultsMessage(show) {
    let noResultsElement = document.getElementById('noResultsMessage');
    
    if (show && !noResultsElement) {
        noResultsElement = document.createElement('div');
        noResultsElement.id = 'noResultsMessage';
        noResultsElement.className = 'no-sponsors';
        noResultsElement.innerHTML = `
            <div class="no-sponsors-content">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
                <h3>No Sponsors Found</h3>
                <p>No sponsors match your current search criteria.</p>
                <button class="btn btn-secondary" onclick="clearFilters()">
                    Clear Filters
                </button>
            </div>
        `;
        document.getElementById('sponsorsGrid').appendChild(noResultsElement);
    } else if (!show && noResultsElement) {
        noResultsElement.remove();
    }
}

function clearFilters() {
    document.getElementById('searchSponsors').value = '';
    document.getElementById('activityFilter').value = '';
    document.getElementById('sortBy').value = 'newest';
    filterSponsors();
    sortSponsors();
}

function updateURLFilters(search, activity) {
    const url = new URL(window.location);
    const params = new URLSearchParams(url.search);
    
    if (search) {
        params.set('search', search);
    } else {
        params.delete('search');
    }
    
    if (activity) {
        params.set('activity', activity);
    } else {
        params.delete('activity');
    }
    
    const newUrl = params.toString() ? `${url.pathname}?${params.toString()}` : url.pathname;
    window.history.replaceState({}, '', newUrl);
}

// Sponsor interaction functions
function viewSponsor(sponsorId) {
    window.location.href = `/unipulse/public/publisher/sponsors/details/${sponsorId}`;
}

function contactSponsor(sponsorId) {
    console.log('Contact sponsor called with ID:', sponsorId);
    
    // Store sponsor ID for form submission
    const form = document.getElementById('contactForm');
    if (form) {
        form.dataset.sponsorId = sponsorId;
    }
    
    // Find sponsor name for better UX - look for the sponsor card that contains the button
    let sponsorName = 'this sponsor';
    const sponsorCards = document.querySelectorAll('.sponsor-card');
    
    for (let card of sponsorCards) {
        const contactBtn = card.querySelector(`button[onclick*="contactSponsor(${sponsorId})"]`);
        if (contactBtn) {
            const nameElement = card.querySelector('.sponsor-name');
            if (nameElement) {
                sponsorName = nameElement.textContent;
            }
            break;
        }
    }
    
    // Update modal title
    const modalTitle = document.querySelector('#contactModal .modal-header h3');
    if (modalTitle) {
        modalTitle.textContent = `Contact ${sponsorName}`;
    }
    
    openContactModal();
}

function openContactModal() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        // Show modal with proper state management
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-open');
        
        // Clear form
        const form = document.getElementById('contactForm');
        if (form) {
            form.reset();
            // Clear any previous error states
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.classList.remove('error', 'valid');
            });
        }
        
        // Focus on subject input after modal is fully visible
        setTimeout(() => {
            const subjectInput = document.getElementById('subject');
            if (subjectInput) {
                subjectInput.focus();
            }
        }, 300);
        
        console.log('Contact modal opened successfully');
    }
}

function closeContactModal() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        // Hide modal with animation
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        document.body.classList.remove('modal-open');
        
        // Reset form
        const form = document.getElementById('contactForm');
        if (form) {
            form.reset();
            // Clear any form validation states
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.classList.remove('error', 'valid');
            });
        }
        
        // Clear any stored sponsor ID
        if (form) {
            delete form.dataset.sponsorId;
        }
        
        console.log('Contact modal closed successfully');
    }
}

// Export functionality
function exportSponsors() {
    const visibleSponsors = Array.from(document.querySelectorAll('.sponsor-card'))
        .filter(card => card.style.display !== 'none')
        .map(card => {
            return {
                name: card.querySelector('.sponsor-name').textContent,
                email: card.querySelector('.sponsor-email').textContent,
                phone: card.querySelector('.sponsor-phone').textContent,
                status: card.dataset.activity,
                joined: card.querySelector('.sponsor-joined').textContent
            };
        });
    
    if (visibleSponsors.length === 0) {
        alert('No sponsors to export with current filters.');
        return;
    }
    
    // Create CSV content
    const csvContent = [
        ['Company Name', 'Email', 'Phone', 'Status', 'Joined Date'],
        ...visibleSponsors.map(sponsor => [
            sponsor.name,
            sponsor.email,
            sponsor.phone,
            sponsor.status,
            sponsor.joined
        ])
    ].map(row => row.map(field => `"${field}"`).join(',')).join('\n');
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `sponsors_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Load filters from URL on page load
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search');
    const activity = urlParams.get('activity');
    
    if (search) {
        document.getElementById('searchSponsors').value = search;
    }
    
    if (activity) {
        document.getElementById('activityFilter').value = activity;
    }
    
    // Apply filters
    if (search || activity) {
        filterSponsors();
    }
});

// Contact form submission handling
document.addEventListener('submit', function(event) {
    if (event.target.id === 'contactForm') {
        console.log('Contact form submitted!');
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Add sponsor ID to form data
        const sponsorId = form.dataset.sponsorId;
        
        if (sponsorId) {
            formData.append('sponsor_id', sponsorId);
            console.log('Added sponsor_id to formData:', sponsorId);
        } else {
            console.error('No sponsor ID found in form dataset');
            alert('Error: No sponsor ID found. Please try again.');
            return;
        }
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Sending...';
        submitButton.disabled = true;
        
        // Debug: Log form data
        console.log('Submitting form with sponsor ID:', sponsorId);
        
        // Submit form
        fetch('/unipulse/public/publisher/sponsors/contact', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal immediately
                closeContactModal();
                
                // Store success message in sessionStorage for popup on next page
                sessionStorage.setItem('successMessage', data.message || 'Message sent successfully!');
                
                // Redirect to sponsors page
                window.location.href = '/unipulse/public/publisher/sponsors';
            } else {
                throw new Error(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage(error.message || 'Failed to send message. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    }
});

function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showSuccessPopup(message) {
    // Create a modern success notification popup
    const successNotification = document.createElement('div');
    successNotification.className = 'success-popup';
    successNotification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 20px 25px;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        z-index: 1001;
        font-size: 16px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 400px;
        animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid #fff;
    `;
    
    successNotification.innerHTML = `
        <div style="
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <i class="fas fa-check" style="font-size: 16px;"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 600; margin-bottom: 2px;">Success!</div>
            <div style="font-size: 14px; opacity: 0.9;">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.2)'" onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'">
            &times;
        </button>
    `;
    
    // Add CSS animation if not already added
    if (!document.querySelector('#successAnimationStyle')) {
        const style = document.createElement('style');
        style.id = 'successAnimationStyle';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(120%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(120%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(successNotification);
    
    // Auto remove after 6 seconds with slide out animation
    setTimeout(() => {
        if (successNotification.parentElement) {
            successNotification.style.animation = 'slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => {
                if (successNotification.parentElement) {
                    successNotification.remove();
                }
            }, 400);
        }
    }, 6000);
}

// This function has been removed as it's no longer needed
// The success popup is now handled directly in the form submission handler

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    alertElement.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-size: 16px;
        z-index: 1001;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 400px;
        animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #dc3545, #c82333)'};
        box-shadow: 0 8px 25px ${type === 'success' ? 'rgba(40, 167, 69, 0.3)' : 'rgba(220, 53, 69, 0.3)'};
    `;
    
    alertElement.innerHTML = `
        <div style="
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'}" style="font-size: 16px;"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 600; margin-bottom: 2px;">${type === 'success' ? 'Success!' : 'Error!'}</div>
            <div style="font-size: 14px; opacity: 0.9;">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.2)'" onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'">
            &times;
        </button>
    `;
    
    document.body.appendChild(alertElement);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertElement.parentElement) {
            alertElement.style.animation = 'slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => {
                if (alertElement.parentElement) {
                    alertElement.remove();
                }
            }, 400);
        }
    }, 5000);
}