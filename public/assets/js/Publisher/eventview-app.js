// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
let similarEvents = window.serverData?.similarEvents || [];
const hasError = window.serverData?.error || null;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/eventview/getEvent';
const joinEndpoint = window.serverData?.joinEndpoint || '/unipulse/public/user/eventview/joinEvent';

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadEventDetails();
});

// Get event ID from URL parameters
function getEventIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load event details
function loadEventDetails() {
    if (hasError) {
        hideLoading();
        showError();
        return;
    }
    
    if (currentEvent) {
        // Use server data directly
        displayEventDetails(currentEvent);
        loadSimilarEvents(similarEvents);
        hideLoading();
        showEventContainer();
    } else {
        // Fallback to AJAX if no server data
        const eventId = getEventIdFromURL();
        
        if (!eventId) {
            showError();
            return;
        }

        showLoading();

        fetch(`${apiEndpoint}?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentEvent = data.event;
                    similarEvents = data.similarEvents || [];
                    displayEventDetails(currentEvent);
                    loadSimilarEvents(similarEvents);
                    hideLoading();
                    showEventContainer();
                } else {
                    hideLoading();
                    showError();
                }
            })
            .catch(error => {
                console.error('Error loading event:', error);
                hideLoading();
                showError();
            });
    }
}

// Display event details
function displayEventDetails(event) {
    // Handle different field names from database vs JavaScript
    const universityName = event.university_name || event.universityName;
    const maxParticipants = event.max_participants || event.maxParticipants;
    const organizerEmail = event.organizer_email || event.organizerEmail;
    const targetAudience = event.target_audience || event.targetAudience;
    const ticketType = event.ticket_type || event.ticketType;
    
    // Basic event info
    document.getElementById('eventCategory').textContent = capitalizeFirstLetter(event.category);
    document.getElementById('eventStatus').textContent = event.status;
    document.getElementById('eventTitle').textContent = event.title;
    document.getElementById('eventSummary').textContent = event.description.substring(0, 150) + '...';
    
    // Event details grid
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    document.getElementById('eventDateTime').textContent = `${formatDate(eventDate)} at ${eventTime}`;
    document.getElementById('eventLocation').textContent = event.location;
    document.getElementById('eventUniversity').textContent = universityName;
    document.getElementById('eventParticipants').textContent = `${event.participants}/${maxParticipants}`;
    
    // Target audience
    document.getElementById('eventAudience').textContent = formatAudience(targetAudience);
    
    // Ticket type (show if not free-all)
    if (ticketType && ticketType !== 'free-all') {
        document.getElementById('ticketInfo').style.display = 'block';
        document.getElementById('eventTicketType').textContent = formatTicketType(ticketType);
    }
    
    // Full description
    document.getElementById('eventDescription').textContent = event.description;
    
    // Schedule
    if (event.schedule) {
        displaySchedule(event.schedule);
    }
    
    // Requirements
    if (event.requirements) {
        displayRequirements(event.requirements);
    }
    
    // Location details
    displayLocationDetails(event);
    
    // Ticket details
    displayTicketDetails(event);
    
    // Custom fields
    if (event.custom_fields) {
        displayCustomFields(event.custom_fields);
    }
    
    // Volunteer information
    if (event.needs_volunteers) {
        displayVolunteerInfo(event);
    }
    
    // Donation information
    if (event.accepts_donations) {
        document.getElementById('donationCard').style.display = 'block';
    }
    
    // Organizer info
    document.getElementById('organizerName').textContent = event.organizer;
    
    // Store organizer email for contact function
    currentEvent.organizerEmail = organizerEmail;
    
    // Statistics
    document.getElementById('totalParticipants').textContent = event.participants;
    document.getElementById('availableSpots').textContent = maxParticipants - event.participants;
    
    // Participation percentage
    const percentage = Math.round((event.participants / maxParticipants) * 100);
    document.getElementById('participationPercentage').textContent = `${percentage}%`;
    document.getElementById('participationFill').style.width = `${percentage}%`;
    
    // Set event link for sharing
    if (document.getElementById('shareLink')) {
        document.getElementById('shareLink').value = window.location.href;
    }
    
    // Update status styling
    updateStatusStyling(event.status, event.participants, maxParticipants);
    
    // Initialize comments for completed events
    initializeComments();
}

// Display event schedule
function displaySchedule(schedule) {
    const scheduleContainer = document.getElementById('eventSchedule');
    scheduleContainer.innerHTML = '';
    
    schedule.forEach(item => {
        const scheduleItem = document.createElement('div');
        scheduleItem.className = 'schedule-item';
        scheduleItem.innerHTML = `
            <span class="time">${item.time}</span>
            <span class="activity">${item.activity}</span>
        `;
        scheduleContainer.appendChild(scheduleItem);
    });
}

// Display event requirements
function displayRequirements(requirements) {
    const requirementsContainer = document.getElementById('eventRequirements');
    const requirementsList = document.createElement('ul');
    requirementsList.className = 'requirements-list';
    
    requirements.forEach(requirement => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <i class="fas fa-check"></i>
            <span>${requirement}</span>
        `;
        requirementsList.appendChild(listItem);
    });
    
    requirementsContainer.innerHTML = '';
    requirementsContainer.appendChild(requirementsList);
}

// Load similar events
function loadSimilarEvents(events) {
    const similarEventsContainer = document.getElementById('similarEvents');
    
    if (!events || events.length === 0) {
        similarEventsContainer.innerHTML = '<p>No similar events found.</p>';
        return;
    }
    
    similarEventsContainer.innerHTML = '';
    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'similar-event-card';
        eventCard.onclick = () => viewEvent(event.id);
        eventCard.innerHTML = `
            <div class="similar-event-info">
                <h5>${event.title}</h5>
                <p class="similar-event-date">${formatDate(event.date)}</p>
                <span class="similar-event-category">${capitalizeFirstLetter(event.category)}</span>
            </div>
        `;
        similarEventsContainer.appendChild(eventCard);
    });
}

// Update status styling
function updateStatusStyling(status, participants, maxParticipants) {
    const statusElement = document.getElementById('eventStatus');
    statusElement.className = `event-status ${status}`;
    
    const joinBtn = document.getElementById('joinBtn');
    if (status === 'completed' || status === 'cancelled') {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Event Ended';
    } else if (participants >= maxParticipants) {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-users"></i> Event Full';
    }
}

// Navigation functions
function viewEvent(eventId) {
    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
}

// Modal functions
function openJoinModal() {
    document.getElementById('joinModal').style.display = 'flex';
}

function closeJoinModal() {
    document.getElementById('joinModal').style.display = 'none';
}

function openShareModal() {
    document.getElementById('shareModal').style.display = 'flex';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

// Event actions
function confirmJoinEvent() {
    const notes = document.getElementById('participantNotes').value;
    
    if (!currentEvent) {
        alert('Event data not available');
        return;
    }
    
    // Show loading state
    const confirmBtn = document.querySelector('#joinModal .btn-primary');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Joining...';
    confirmBtn.disabled = true;
    
    // Make API call to join event
    const formData = new FormData();
    formData.append('id', currentEvent.id);
    formData.append('notes', notes);
    
    fetch(joinEndpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Successfully joined "${currentEvent.title}"!`);
            
            // Update UI with new participant count
            currentEvent.participants = data.participants;
            const maxParticipants = currentEvent.max_participants || currentEvent.maxParticipants;
            
            document.getElementById('eventParticipants').textContent = `${data.participants}/${maxParticipants}`;
            document.getElementById('totalParticipants').textContent = data.participants;
            document.getElementById('availableSpots').textContent = data.availableSpots;
            
            // Update participation percentage
            const percentage = Math.round((data.participants / maxParticipants) * 100);
            document.getElementById('participationPercentage').textContent = `${percentage}%`;
            document.getElementById('participationFill').style.width = `${percentage}%`;
            
            closeJoinModal();
        } else {
            alert('Failed to join event: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error joining event:', error);
        alert('Failed to join event. Please try again.');
    })
    .finally(() => {
        // Reset button state
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

function contactOrganizer() {
    const organizerEmail = currentEvent?.organizerEmail || currentEvent?.organizer_email;
    if (organizerEmail) {
        window.location.href = `mailto:${organizerEmail}?subject=Inquiry about ${currentEvent.title}`;
    } else {
        alert('Organizer contact information not available.');
    }
}

// Share functions
function shareViaFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareViaTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this event: ${currentEvent.title}`);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareViaWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this event: ${currentEvent.title} - ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function copyEventLink() {
    const eventLink = document.getElementById('eventLink');
    eventLink.select();
    document.execCommand('copy');
    alert('Event link copied to clipboard!');
}

// UI state management
function showLoading() {
    document.getElementById('loadingContainer').style.display = 'flex';
    document.getElementById('errorContainer').style.display = 'none';
    document.getElementById('eventContainer').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loadingContainer').style.display = 'none';
}

function showError() {
    document.getElementById('errorContainer').style.display = 'flex';
    document.getElementById('eventContainer').style.display = 'none';
}

// New helper functions for additional fields

function formatAudience(audience) {
    const audienceMap = {
        'university-students': 'University Students',
        'public-users': 'Public Users',
        'both': 'University Students & Public'
    };
    return audienceMap[audience] || audience;
}

function formatTicketType(ticketType) {
    const ticketMap = {
        'free-all': 'Free for All',
        'paid-all': 'Paid for All',
        'mixed': 'Free for Students, Paid for Others'
    };
    return ticketMap[ticketType] || ticketType;
}

function displayLocationDetails(event) {
    const locationType = event.location_type || 'inside-university';
    let locationHTML = '';
    
    if (locationType === 'outside-university') {
        locationHTML = '<div class="location-detail-item">';
        if (event.venue_name) {
            locationHTML += `<div><strong>Venue:</strong> ${event.venue_name}</div>`;
        }
        if (event.street_address) {
            locationHTML += `<div><strong>Address:</strong> ${event.street_address}</div>`;
        }
        if (event.city) {
            locationHTML += `<div><strong>City:</strong> ${event.city}</div>`;
        }
        if (event.district_province) {
            locationHTML += `<div><strong>District/Province:</strong> ${event.district_province}</div>`;
        }
        locationHTML += '</div>';
        
        if (locationHTML !== '<div class="location-detail-item"></div>') {
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
        }
    } else if (event.faculty_department) {
        locationHTML = `<div class="location-detail-item">
            <div><strong>Faculty/Department:</strong> ${event.faculty_department}</div>
        </div>`;
        document.getElementById('locationDetailsCard').style.display = 'block';
        document.getElementById('locationDetails').innerHTML = locationHTML;
    }
}

function displayTicketDetails(event) {
    const ticketType = event.ticket_type || 'free-all';
    
    if (ticketType === 'free-all') {
        return; // No special ticket details to show
    }
    
    let ticketHTML = '<div class="ticket-detail-item">';
    ticketHTML += `<div><strong>Ticket Type:</strong> ${formatTicketType(ticketType)}</div>`;
    
    if (event.registration_start_date && event.registration_end_date) {
        ticketHTML += `<div><strong>Registration Period:</strong> ${formatDate(event.registration_start_date)} to ${formatDate(event.registration_end_date)}</div>`;
    }
    
    if (event.registration_limit) {
        ticketHTML += `<div><strong>Registration Limit:</strong> ${event.registration_limit} participants</div>`;
    }
    
    if (event.ticket_types && Array.isArray(event.ticket_types)) {
        ticketHTML += '<div><strong>Available Tickets:</strong></div>';
        ticketHTML += '<ul class="ticket-types-list">';
        event.ticket_types.forEach(ticket => {
            ticketHTML += `<li>${ticket.name} - LKR ${ticket.price} (${ticket.quantity} available)</li>`;
        });
        ticketHTML += '</ul>';
    }
    
    ticketHTML += '</div>';
    
    document.getElementById('ticketDetailsCard').style.display = 'block';
    document.getElementById('ticketDetails').innerHTML = ticketHTML;
}

function displayCustomFields(customFields) {
    if (!Array.isArray(customFields) || customFields.length === 0) {
        return;
    }
    
    let fieldsHTML = '<div class="custom-fields-list">';
    customFields.forEach(field => {
        fieldsHTML += `<div class="custom-field-item">
            <strong>${field.label}:</strong> 
            <span>${field.type === 'select' ? field.options.join(', ') : field.type}</span>
        </div>`;
    });
    fieldsHTML += '</div>';
    
    document.getElementById('customFieldsCard').style.display = 'block';
    document.getElementById('customFields').innerHTML = fieldsHTML;
}

function displayVolunteerInfo(event) {
    let volunteerHTML = '<div class="volunteer-detail-item">';
    
    if (event.volunteers_needed) {
        volunteerHTML += `<div><strong>Volunteers Needed:</strong> ${event.volunteers_needed}</div>`;
    }
    
    if (event.volunteer_sources && Array.isArray(event.volunteer_sources)) {
        volunteerHTML += '<div><strong>Recruiting From:</strong></div>';
        volunteerHTML += '<ul class="volunteer-sources-list">';
        event.volunteer_sources.forEach(source => {
            const sourceMap = {
                'faculty': 'Faculty Members',
                'university': 'University Students',
                'public': 'Public Users'
            };
            volunteerHTML += `<li>${sourceMap[source] || source}</li>`;
        });
        volunteerHTML += '</ul>';
    }
    
    if (event.volunteer_positions && Array.isArray(event.volunteer_positions)) {
        volunteerHTML += '<div><strong>Available Positions:</strong></div>';
        volunteerHTML += '<ul class="volunteer-positions-list">';
        event.volunteer_positions.forEach(position => {
            volunteerHTML += `<li>${position}</li>`;
        });
        volunteerHTML += '</ul>';
    }
    
    volunteerHTML += '<div style="margin-top: 15px;">';
    volunteerHTML += '<button class="btn btn-outline" onclick="applyAsVolunteer()">Apply as Volunteer</button>';
    volunteerHTML += '</div>';
    
    volunteerHTML += '</div>';
    
    document.getElementById('volunteerCard').style.display = 'block';
    document.getElementById('volunteerInfo').innerHTML = volunteerHTML;
}

// Modal functions
function openDonationModal() {
    document.getElementById('donationModal').style.display = 'flex';
}

function closeDonationModal() {
    document.getElementById('donationModal').style.display = 'none';
}

function processDonation() {
    const selectedAmount = document.querySelector('.donation-amount.selected');
    const customAmount = document.getElementById('customDonationAmount').value;
    
    const amount = selectedAmount ? selectedAmount.dataset.amount : customAmount;
    
    if (!amount || amount < 100) {
        alert('Please select or enter a valid donation amount (minimum LKR 100)');
        return;
    }
    
    // Here you would integrate with payment gateway
    alert(`Thank you for your donation of LKR ${amount}! Payment integration would be implemented here.`);
    closeDonationModal();
}

function applyAsVolunteer() {
    // Here you would redirect to volunteer application form or open a modal
    alert('Volunteer application functionality would be implemented here.');
}

// Event listeners for donation amounts
document.addEventListener('DOMContentLoaded', function() {
    const donationAmounts = document.querySelectorAll('.donation-amount');
    donationAmounts.forEach(button => {
        button.addEventListener('click', function() {
            donationAmounts.forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('customDonationAmount').value = '';
        });
    });
});

function showEventContainer() {
    document.getElementById('eventContainer').style.display = 'block';
}

// Event listeners
document.getElementById('joinBtn').addEventListener('click', openJoinModal);
document.getElementById('shareBtn').addEventListener('click', openShareModal);

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const joinModal = document.getElementById('joinModal');
    const shareModal = document.getElementById('shareModal');
    
    if (event.target === joinModal) {
        closeJoinModal();
    }
    if (event.target === shareModal) {
        closeShareModal();
    }
});

// Utility functions
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Edit event function for publisher's own events
function editEvent() {
    if (!currentEvent || !currentEvent.id) {
        showMessage('Error: Event ID not found', 'error');
        return;
    }
    
    window.location.href = `/unipulse/public/publisher/editevent?id=${currentEvent.id}`;
}

// Delete event function for publisher's own events
function deleteEvent() {
    if (!currentEvent || !currentEvent.id) {
        showMessage('Error: Event ID not found', 'error');
        return;
    }
    
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        // Show loading state
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        }

        fetch('/unipulse/public/publisher/deleteevent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: currentEvent.id })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('Event deleted successfully!', 'success');
                
                // Redirect to events page after a short delay
                setTimeout(() => {
                    window.location.href = '/unipulse/public/publisher/events';
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to delete event');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('Error deleting event: ' + error.message, 'error');
            
            // Reset button state
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Event';
            }
        });
    }
}

// Show message function for notifications
function showMessage(message, type = 'info') {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.innerHTML = `
        <div class="message-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        max-width: 400px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, 4000);
}

// ======================
// Comment Functionality
// ======================

let currentRating = 0;
let editingCommentId = null;
let editingRating = 0;

// Initialize comment functionality when event is displayed
function initializeComments() {
    if (!currentEvent) return;
    
    // Show comments section for completed events
    if (currentEvent.status === 'completed') {
        document.getElementById('commentsSection').style.display = 'block';
        loadComments();
        setupCommentForm();
    }
}

// Setup comment form functionality
function setupCommentForm() {
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitCommentBtn');
    const cancelBtn = document.getElementById('cancelCommentBtn');
    const ratingStars = document.querySelectorAll('#ratingInput .star');
    
    // Character count
    if (commentText && charCount) {
        commentText.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Rating stars
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.rating);
            updateRatingDisplay(ratingStars, currentRating);
        });
        
        star.addEventListener('mouseover', function() {
            const hoverRating = parseInt(this.dataset.rating);
            updateRatingDisplay(ratingStars, hoverRating);
        });
    });
    
    document.getElementById('ratingInput').addEventListener('mouseleave', function() {
        updateRatingDisplay(ratingStars, currentRating);
    });
    
    // Submit button
    if (submitBtn) {
        submitBtn.addEventListener('click', submitComment);
    }
    
    // Cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideCommentForm);
    }
}

// Update rating display
function updateRatingDisplay(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.classList.remove('active');
        }
    });
    
    const ratingText = document.getElementById('ratingText');
    if (ratingText) {
        const ratingTexts = ['Click stars to rate', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        ratingText.textContent = ratingTexts[rating] || ratingTexts[0];
    }
}

// Show comment form
function showCommentForm() {
    document.getElementById('addCommentTrigger').style.display = 'none';
    document.getElementById('addCommentSection').style.display = 'block';
    document.getElementById('commentText').focus();
}

// Hide comment form
function hideCommentForm() {
    document.getElementById('addCommentTrigger').style.display = 'block';
    document.getElementById('addCommentSection').style.display = 'none';
    
    // Reset form
    document.getElementById('commentText').value = '';
    document.getElementById('charCount').textContent = '0';
    currentRating = 0;
    updateRatingDisplay(document.querySelectorAll('#ratingInput .star'), 0);
}

// Submit comment
function submitComment() {
    const commentText = document.getElementById('commentText').value.trim();
    
    if (!commentText) {
        showMessage('Please enter a comment', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submitCommentBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    submitBtn.disabled = true;
    
    const commentData = {
        event_id: currentEvent.id,
        comment: commentText,
        rating: currentRating
    };
    
    fetch('/unipulse/public/publisher/comments/addComment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(commentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Comment posted successfully!', 'success');
            hideCommentForm();
            loadComments(); // Reload comments
        } else {
            showMessage(data.errors ? Object.values(data.errors)[0] : 'Failed to post comment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Network error occurred', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Load comments
function loadComments() {
    if (!currentEvent) return;
    
    const commentsList = document.getElementById('commentsList');
    
    commentsList.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading comments...</p>
        </div>
    `;
    
    fetch(`/unipulse/public/publisher/comments/getComments?event_id=${currentEvent.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComments(data.comments, data.statistics);
                
                // Show add comment button if user is logged in and event is completed
                if (data.canComment && currentEvent.status === 'completed') {
                    document.getElementById('addCommentTrigger').style.display = 'block';
                } else if (!data.canComment) {
                    document.getElementById('loginPrompt').style.display = 'block';
                }
            } else {
                commentsList.innerHTML = `
                    <div class="error-message">
                        <p>Failed to load comments</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            commentsList.innerHTML = `
                <div class="error-message">
                    <p>Failed to load comments</p>
                </div>
            `;
        });
}

// Display comments
function displayComments(comments, statistics) {
    const commentsList = document.getElementById('commentsList');
    const totalCommentsCount = document.getElementById('totalCommentsCount');
    const averageRatingDisplay = document.getElementById('averageRatingDisplay');
    const averageRatingValue = document.getElementById('averageRatingValue');
    
    // Update statistics
    if (totalCommentsCount) {
        totalCommentsCount.textContent = statistics.total || 0;
    }
    
    if (statistics.averageRating && statistics.averageRating > 0) {
        averageRatingDisplay.style.display = 'inline-block';
        averageRatingValue.textContent = statistics.averageRating.toFixed(1);
    }
    
    if (comments.length === 0) {
        commentsList.innerHTML = `
            <div class="no-comments">
                <i class="fas fa-comments"></i>
                <h4>No comments yet</h4>
                <p>Be the first to share your experience with this event!</p>
            </div>
        `;
        return;
    }
    
    let commentsHTML = '';
    
    comments.forEach(comment => {
        const userAvatar = comment.user_name ? comment.user_name.charAt(0).toUpperCase() : 'U';
        const ratingStars = comment.rating ? '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating) : '';
        
        commentsHTML += `
            <div class="comment-item" data-comment-id="${comment.id}">
                <div class="comment-header">
                    <div class="user-info">
                        <div class="user-avatar">${userAvatar}</div>
                        <div class="user-details">
                            <span class="user-name">${comment.user_name || 'Anonymous'}</span>
                            <span class="user-type">${comment.user_type || 'user'}</span>
                            <span class="comment-date">${formatCommentDate(comment.created_at)}</span>
                        </div>
                    </div>
                    ${comment.rating ? `<div class="comment-rating">${ratingStars}</div>` : ''}
                </div>
                <div class="comment-content">
                    <p>${comment.comment}</p>
                </div>
                ${comment.canEdit ? `
                    <div class="comment-actions">
                        <button class="action-btn edit-btn" onclick="editComment(${comment.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteComment(${comment.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    commentsList.innerHTML = commentsHTML;
}

// Format comment date
function formatCommentDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}

// Edit comment
function editComment(commentId) {
    // Find the comment
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!commentCard) return;
    
    const commentContent = commentCard.querySelector('.comment-content').textContent.trim();
    const ratingElement = commentCard.querySelector('.rating-value');
    const currentRating = ratingElement ? parseInt(ratingElement.textContent.split('/')[0]) : 0;
    
    // Set editing state
    editingCommentId = commentId;
    editingRating = currentRating;
    
    // Populate edit form (if edit modal exists)
    const editCommentText = document.getElementById('editCommentText');
    const editCharCount = document.getElementById('editCharCount');
    
    if (editCommentText) {
        editCommentText.value = commentContent;
        editCharCount.textContent = commentContent.length;
    }
    
    // Show modal (if exists)
    const editModal = document.getElementById('editCommentModal');
    if (editModal) {
        editModal.style.display = 'flex';
    }
}

// Update comment
async function updateComment() {
    const commentText = document.getElementById('editCommentText').value.trim();
    
    if (!commentText) {
        showMessage('Please enter a comment', 'error');
        return;
    }
    
    if (commentText.length < 5) {
        showMessage('Comment must be at least 5 characters long', 'error');
        return;
    }
    
    const updateBtn = document.getElementById('updateCommentBtn');
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    updateBtn.disabled = true;
    
    try {
        const response = await fetch(`/unipulse/public/publisher/comments/updateComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                comment_text: commentText,
                rating: editingRating || null
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('Comment updated successfully!', 'success');
            closeEditCommentModal();
            loadComments(); // Reload comments
        } else {
            showMessage(data.error || 'Failed to update comment', 'error');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showMessage('Failed to update comment', 'error');
    } finally {
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    }
}

// Delete comment
function deleteComment(commentId) {
    editingCommentId = commentId;
    const deleteModal = document.getElementById('deleteCommentModal');
    if (deleteModal) {
        deleteModal.style.display = 'flex';
    } else {
        // If no modal, confirm directly
        if (confirm('Are you sure you want to delete this comment?')) {
            confirmDeleteComment();
        }
    }
}

// Confirm delete comment
async function confirmDeleteComment() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (deleteBtn) {
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        deleteBtn.disabled = true;
    }
    
    try {
        const response = await fetch(`/unipulse/public/publisher/comments/deleteComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('Comment deleted successfully!', 'success');
            closeDeleteCommentModal();
            loadComments(); // Reload comments
        } else {
            showMessage(data.error || 'Failed to delete comment', 'error');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showMessage('Failed to delete comment', 'error');
    } finally {
        if (deleteBtn) {
            deleteBtn.innerHTML = 'Delete Comment';
            deleteBtn.disabled = false;
        }
    }
}

// Modal functions for comments
function closeEditCommentModal() {
    const editModal = document.getElementById('editCommentModal');
    if (editModal) {
        editModal.style.display = 'none';
    }
    editingCommentId = null;
    editingRating = 0;
}

function closeDeleteCommentModal() {
    const deleteModal = document.getElementById('deleteCommentModal');
    if (deleteModal) {
        deleteModal.style.display = 'none';
    }
    editingCommentId = null;
}
