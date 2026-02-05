// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
let similarEvents = window.serverData?.similarEvents || [];
const hasError = window.serverData?.error || null;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/eventview/getEvent';
const joinEndpoint = window.serverData?.joinEndpoint || '/unipulse/public/user/eventview/joinEvent';
let isUserRegistered = window.serverData?.isRegistered || false;

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
    const currentParticipants = event.current_participants || event.currentParticipants || 0;
    const organizerEmail = event.organizer_email || event.organizerEmail;
    const targetAudience = event.target_audience || event.targetAudience;
    const ticketType = event.ticket_type || event.ticketType;
    const imageUrl = event.image_url || event.imageUrl || event.cover_image || event.image;
    
    // Display hero image if available
    if (imageUrl) {
        const heroImageContainer = document.getElementById('heroImageContainer');
        const heroImage = document.getElementById('heroImage');
        if (heroImageContainer && heroImage) {
            // Build correct image path
            let imagePath = '';
            if (imageUrl.startsWith('http')) {
                imagePath = imageUrl;
            } else if (imageUrl.startsWith('/unipulse/')) {
                imagePath = imageUrl;
            } else if (imageUrl.startsWith('/')) {
                imagePath = imageUrl;
            } else {
                // Relative path from database - add /unipulse/public/ prefix
                imagePath = `/unipulse/public/${imageUrl}`;
            }
            
            console.log('Image URL from DB:', imageUrl);
            console.log('Constructed image path:', imagePath);
            
            heroImage.src = imagePath;
            heroImage.alt = event.title + ' Cover Image';
            heroImageContainer.style.display = 'block';
            
            // Add error handler
            heroImage.onerror = function() {
                console.error('Failed to load image:', imagePath);
                heroImageContainer.style.display = 'none';
            };
            
            // Add load handler for debugging
            heroImage.onload = function() {
                console.log('Image loaded successfully:', imagePath);
            };
        }
    }
    
    // Basic event info
    document.getElementById('eventCategory').textContent = capitalizeFirstLetter(event.category);
    document.getElementById('eventStatus').textContent = event.status;
    document.getElementById('eventTitle').textContent = event.title;
    
    // Event details grid
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    document.getElementById('eventDateTime').textContent = `${formatDate(eventDate)} at ${eventTime}`;
    document.getElementById('eventUniversity').textContent = universityName;
    
    // Show faculty/department if available
    if (event.faculty_department) {
        document.getElementById('facultyInfo').style.display = 'flex';
        document.getElementById('eventFaculty').textContent = event.faculty_department;
    }
    
    document.getElementById('eventLocation').textContent = event.location;
    
    // Show participants info only if max_participants is set
    if (maxParticipants !== null && maxParticipants !== undefined) {
        document.getElementById('participantsInfo').style.display = 'flex';
        document.getElementById('eventParticipants').textContent = `${currentParticipants}/${maxParticipants}`;
    } else {
        document.getElementById('participantsInfo').style.display = 'none';
    }
    
    // Target audience
    document.getElementById('eventAudience').textContent = formatAudience(targetAudience);
    
    // Ticket type (show if not free-all)
    if (ticketType && ticketType !== 'free-all') {
        document.getElementById('ticketInfo').style.display = 'block';
        document.getElementById('eventTicketType').textContent = formatTicketType(ticketType);
    }
    
    // Full description
    document.getElementById('eventDescription').textContent = event.description;
    
    // Registration Period
    displayRegistrationPeriod(event);
    
    // Schedule - hide card if no schedule data
    if (event.schedule && Array.isArray(event.schedule) && event.schedule.length > 0) {
        displaySchedule(event.schedule);
        document.getElementById('scheduleCard').style.display = 'block';
    } else {
        document.getElementById('scheduleCard').style.display = 'none';
    }
    
    // Requirements - hide card if no requirements
    if (event.requirements && Array.isArray(event.requirements) && event.requirements.length > 0) {
        displayRequirements(event.requirements);
        document.getElementById('requirementsCard').style.display = 'block';
    } else {
        document.getElementById('requirementsCard').style.display = 'none';
    }
    
    // Location details
    displayLocationDetails(event);
    
    // Ticket details
    displayTicketDetails(event);
    
    // Custom fields - hide card if no custom fields
    if (event.custom_fields && Array.isArray(event.custom_fields) && event.custom_fields.length > 0) {
        displayCustomFields(event.custom_fields);
    } else {
        if (document.getElementById('customFieldsCard')) {
            document.getElementById('customFieldsCard').style.display = 'none';
        }
    }
    
    // Volunteer information - hide card if not accepting volunteers
    if (event.needs_volunteers && event.needs_volunteers == 1) {
        displayVolunteerInfo(event);
    } else {
        if (document.getElementById('volunteerCard')) {
            document.getElementById('volunteerCard').style.display = 'none';
        }
    }
    
    // Donation information - hide card if not accepting donations
    if (event.accepts_donations && event.accepts_donations == 1) {
        if (document.getElementById('donationCard')) {
            document.getElementById('donationCard').style.display = 'block';
        }
    } else {
        if (document.getElementById('donationCard')) {
            document.getElementById('donationCard').style.display = 'none';
        }
    }
    
    // Organizer info
    document.getElementById('organizerName').textContent = event.organizer;
    
    // Set organizer role if available, otherwise use default
    const organizerRoleElement = document.getElementById('organizerRole');
    if (organizerRoleElement) {
        organizerRoleElement.textContent = event.organizer_role || 'Event Organizer';
    }
    
    // Store organizer email for contact function
    currentEvent.organizerEmail = organizerEmail;
    
    // Statistics - only show if max_participants is set
    if (maxParticipants !== null && maxParticipants !== undefined) {
        document.getElementById('eventStatsCard').style.display = 'block';
        document.getElementById('totalParticipants').textContent = currentParticipants;
        document.getElementById('availableSpots').textContent = maxParticipants - currentParticipants;
        
        // Participation percentage
        const percentage = maxParticipants > 0 ? Math.round((currentParticipants / maxParticipants) * 100) : 0;
        document.getElementById('participationPercentage').textContent = `${percentage}%`;
        document.getElementById('participationFill').style.width = `${percentage}%`;
    } else {
        document.getElementById('eventStatsCard').style.display = 'none';
    }
    
    // Set event link for sharing
    if (document.getElementById('shareLink')) {
        document.getElementById('shareLink').value = window.location.href;
    }
    
    // Update status styling
    updateStatusStyling(event.status, currentParticipants, maxParticipants);
}

// Display registration period
function displayRegistrationPeriod(event) {
    const registrationCard = document.getElementById('registrationPeriodCard');
    const registrationContainer = document.getElementById('registrationPeriod');
    
    // Only show if registration dates exist
    if (event.registration_start_date && event.registration_end_date) {
        registrationCard.style.display = 'block';
        
        let html = '<div class="registration-info">';
        
        // Registration start
        html += '<div class="registration-item">';
        html += '<i class="fas fa-calendar-plus"></i>';
        html += '<div class="registration-details">';
        html += '<strong>Registration Opens:</strong> ';
        html += formatDate(event.registration_start_date);
        if (event.registration_start_time) {
            html += ` at ${event.registration_start_time}`;
        }
        html += '</div>';
        html += '</div>';
        
        // Registration end
        html += '<div class="registration-item">';
        html += '<i class="fas fa-calendar-times"></i>';
        html += '<div class="registration-details">';
        html += '<strong>Registration Closes:</strong> ';
        html += formatDate(event.registration_end_date);
        if (event.registration_end_time) {
            html += ` at ${event.registration_end_time}`;
        }
        html += '</div>';
        html += '</div>';
        
        // Registration limit
        if (event.registration_limit) {
            html += '<div class="registration-item">';
            html += '<i class="fas fa-users"></i>';
            html += '<div class="registration-details">';
            html += `<strong>Registration Limit:</strong> ${event.registration_limit} participants`;
            html += '</div>';
            html += '</div>';
        }
        
        html += '</div>';
        registrationContainer.innerHTML = html;
    } else {
        registrationCard.style.display = 'none';
    }
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
    if (isUserRegistered) {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
        joinBtn.style.cursor = 'not-allowed';
        joinBtn.style.opacity = '0.6';
    } else if (status === 'completed' || status === 'cancelled') {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Event Ended';
    } else if (maxParticipants !== null && maxParticipants !== undefined && participants >= maxParticipants) {
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
            
            // Mark user as registered
            isUserRegistered = true;
            
            // Update join button state
            const joinBtn = document.getElementById('joinBtn');
            if (joinBtn) {
                joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
                joinBtn.classList.add('disabled');
                joinBtn.style.cursor = 'not-allowed';
                joinBtn.style.opacity = '0.6';
                joinBtn.disabled = true;
                joinBtn.removeEventListener('click', openJoinModal);
            }
            
            // Update UI with new participant count
            const newCurrentParticipants = data.current_participants || data.participants || 0;
            const maxParticipants = currentEvent.max_participants || currentEvent.maxParticipants;
            currentEvent.current_participants = newCurrentParticipants;
            
            // Update participant count in detail section if visible
            if (maxParticipants !== null && maxParticipants !== undefined) {
                document.getElementById('eventParticipants').textContent = `${newCurrentParticipants}/${maxParticipants}`;
                document.getElementById('totalParticipants').textContent = newCurrentParticipants;
                document.getElementById('availableSpots').textContent = maxParticipants - newCurrentParticipants;
                
                // Update participation percentage
                const percentage = maxParticipants > 0 ? Math.round((newCurrentParticipants / maxParticipants) * 100) : 0;
                document.getElementById('participationPercentage').textContent = `${percentage}%`;
                document.getElementById('participationFill').style.width = `${percentage}%`;
            }
            
            closeJoinModal();
        } else if (data.alreadyRegistered) {
            // User is already registered
            alert('You have already registered for this event.');
            isUserRegistered = true;
            
            // Update join button state
            const joinBtn = document.getElementById('joinBtn');
            if (joinBtn) {
                joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
                joinBtn.classList.add('disabled');
                joinBtn.style.cursor = 'not-allowed';
                joinBtn.style.opacity = '0.6';
                joinBtn.disabled = true;
                joinBtn.removeEventListener('click', openJoinModal);
            }
            
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
    const universityName = event.university_name || event.universityName;
    let locationHTML = '';
    
    if (locationType === 'outside-university') {
        locationHTML = '';
        
        if (event.venue_name) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="location-content">
                        <strong>Venue</strong>
                        <span>${event.venue_name}</span>
                    </div>
                </div>
            `;
        }
        
        if (event.street_address) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-road"></i>
                    </div>
                    <div class="location-content">
                        <strong>Address</strong>
                        <span>${event.street_address}</span>
                    </div>
                </div>
            `;
        }
        
        if (event.city) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="location-content">
                        <strong>City</strong>
                        <span>${event.city}</span>
                    </div>
                </div>
            `;
        }
        
        if (event.district_province) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="location-content">
                        <strong>District/Province</strong>
                        <span>${event.district_province}</span>
                    </div>
                </div>
            `;
        }
        
        if (locationHTML) {
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
        }
    } else {
        // Inside university - show university, faculty/department, and exact location
        locationHTML = '<div class="location-detail-item">';
        
        if (universityName) {
            locationHTML += `<div><strong>University:</strong> ${universityName}</div>`;
        }
        
        if (event.faculty_department) {
            locationHTML += `<div><strong>Faculty/Department:</strong> ${event.faculty_department}</div>`;
        }
        
        if (event.location) {
            locationHTML += `<div><strong>Exact Location:</strong> ${event.location}</div>`;
        }
        
        locationHTML += '</div>';
        
        // Only show if there's actual content
        if (universityName || event.faculty_department || event.location) {
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
        }
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
    volunteerHTML += '<button class="btn btn-primary" onclick="applyAsVolunteer()">Apply as Volunteer</button>';
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
    // Get the event ID from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('id');
    
    // Redirect to volunteer registration page with event ID
    if (eventId) {
        window.location.href = `/unipulse/public/volunteerreg?event_id=${eventId}`;
    } else {
        alert('Event ID not found');
    }
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
const joinBtn = document.getElementById('joinBtn');
if (joinBtn && !isUserRegistered) {
    joinBtn.addEventListener('click', openJoinModal);
}
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
