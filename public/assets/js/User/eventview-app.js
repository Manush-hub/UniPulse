// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
let similarEvents = window.serverData?.similarEvents || [];
let isUserRegistered = window.serverData?.isRegistered || false;
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
    document.getElementById('eventSummary').textContent = event.description.substring(0, 150) + '...';
    
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
        
        // Show buy ticket button for paid events
        const buyTicketBtn = document.getElementById('buyTicketBtn');
        if (buyTicketBtn) {
            buyTicketBtn.style.display = 'inline-flex';
        }
    } else {
        // Hide buy ticket button for free events
        const buyTicketBtn = document.getElementById('buyTicketBtn');
        if (buyTicketBtn) {
            buyTicketBtn.style.display = 'none';
        }
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
    
    // Show comments section if event is completed
    if (event.status === 'completed') {
        const commentsSection = document.getElementById('commentsSection');
        if (commentsSection) {
            commentsSection.style.display = 'block';
            loadComments(); // Load comments for completed events
        }
    }
}

// Display registration period
function displayRegistrationPeriod(event) {
    const registrationPeriodCard = document.getElementById('registrationPeriodCard');
    const registrationPeriodContainer = document.getElementById('registrationPeriod');
    
    // Check if registration dates are available
    if (event.registration_start_date && event.registration_end_date) {
        let registrationHTML = '<div class="registration-period-item">';
        
        // Registration Start
        registrationHTML += '<div style="margin-bottom: 15px;">';
        registrationHTML += '<div><strong>Registration Opens:</strong></div>';
        registrationHTML += `<div style="color: #666; margin-top: 5px;">`;
        registrationHTML += `<i class="fas fa-calendar"></i> ${formatDate(event.registration_start_date)}`;
        if (event.registration_start_time) {
            registrationHTML += ` <i class="fas fa-clock" style="margin-left: 15px;"></i> ${event.registration_start_time}`;
        }
        registrationHTML += '</div></div>';
        
        // Registration End
        registrationHTML += '<div style="margin-bottom: 15px;">';
        registrationHTML += '<div><strong>Registration Closes:</strong></div>';
        registrationHTML += `<div style="color: #666; margin-top: 5px;">`;
        registrationHTML += `<i class="fas fa-calendar"></i> ${formatDate(event.registration_end_date)}`;
        if (event.registration_end_time) {
            registrationHTML += ` <i class="fas fa-clock" style="margin-left: 15px;"></i> ${event.registration_end_time}`;
        }
        registrationHTML += '</div></div>';
        
        // Registration limit if available
        if (event.registration_limit) {
            registrationHTML += '<div>';
            registrationHTML += '<div><strong>Registration Limit:</strong></div>';
            registrationHTML += `<div style="color: #666; margin-top: 5px;"><i class="fas fa-users"></i> ${event.registration_limit} participants</div>`;
            registrationHTML += '</div>';
        }
        
        registrationHTML += '</div>';
        
        registrationPeriodContainer.innerHTML = registrationHTML;
        registrationPeriodCard.style.display = 'block';
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

// Make modal functions globally accessible
window.openJoinModal = openJoinModal;
window.closeJoinModal = closeJoinModal;
window.openShareModal = openShareModal;
window.closeShareModal = closeShareModal;

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
            
            // Update current event data
            const newCurrentParticipants = data.current_participants || data.participants || 0;
            const maxParticipants = data.max_participants || currentEvent.max_participants || currentEvent.maxParticipants;
            
            currentEvent.current_participants = newCurrentParticipants;
            currentEvent.participants = data.participants; // Legacy
            
            // Update UI with new participant count only if max_participants is set
            if (maxParticipants !== null && maxParticipants !== undefined) {
                // Update participants in detail section
                const participantsInfo = document.getElementById('participantsInfo');
                if (participantsInfo) {
                    participantsInfo.style.display = 'flex';
                    document.getElementById('eventParticipants').textContent = `${newCurrentParticipants}/${maxParticipants}`;
                }
                
                // Update statistics card
                const statsCard = document.getElementById('eventStatsCard');
                if (statsCard && statsCard.style.display !== 'none') {
                    document.getElementById('totalParticipants').textContent = newCurrentParticipants;
                    document.getElementById('availableSpots').textContent = data.availableSpots !== null ? data.availableSpots : maxParticipants - newCurrentParticipants;
                    
                    // Update participation percentage
                    const percentage = maxParticipants > 0 ? Math.round((newCurrentParticipants / maxParticipants) * 100) : 0;
                    document.getElementById('participationPercentage').textContent = `${percentage}%`;
                    document.getElementById('participationFill').style.width = `${percentage}%`;
                }
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

// Make function globally accessible
window.contactOrganizer = contactOrganizer;

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

function copyShareLink() {
    const shareLink = document.getElementById('shareLink');
    if (shareLink) {
        shareLink.select();
        shareLink.setSelectionRange(0, 99999); // For mobile devices
        
        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareLink.value)
                .then(() => {
                    showToast('Link copied to clipboard!', 'success');
                })
                .catch(() => {
                    // Fallback to execCommand
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!', 'success');
                });
        } else {
            // Fallback for older browsers
            document.execCommand('copy');
            showToast('Link copied to clipboard!', 'success');
        }
    }
}

// Make functions globally accessible
window.copyEventLink = copyEventLink;
window.copyShareLink = copyShareLink;
window.confirmJoinEvent = confirmJoinEvent;

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

// Make functions globally accessible
window.openDonationModal = openDonationModal;
window.closeDonationModal = closeDonationModal;
window.processDonation = processDonation;

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

// Comments System Variables
let currentUserRating = 0;
let editingCommentId = null;
let editingCommentRating = 0;

// Event listeners
const joinBtn = document.getElementById('joinBtn');
if (joinBtn) {
    if (isUserRegistered) {
        joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
        joinBtn.classList.add('disabled');
        joinBtn.style.cursor = 'not-allowed';
        joinBtn.style.opacity = '0.6';
        joinBtn.disabled = true;
    } else {
        joinBtn.addEventListener('click', openJoinModal);
    }
}

// Buy Ticket button handler
const buyTicketBtn = document.getElementById('buyTicketBtn');
if (buyTicketBtn) {
    buyTicketBtn.addEventListener('click', function() {
        const eventId = getEventIdFromURL();
        if (eventId) {
            window.location.href = `/unipulse/public/user/paymentgateway?event_id=${eventId}`;
        }
    });
}

const shareBtn = document.getElementById('shareBtn');
if (shareBtn) {
    shareBtn.addEventListener('click', openShareModal);
}

// Comments event listeners - check if DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setupCommentsEventListeners();
    });
} else {
    // DOM is already loaded
    setupCommentsEventListeners();
}

function setupCommentsEventListeners() {
    // Character count for comment text
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');
    
    if (commentText && charCount) {
        commentText.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count > 900) {
                charCount.classList.add('danger');
                charCount.classList.remove('warning');
            } else if (count > 800) {
                charCount.classList.add('warning');
                charCount.classList.remove('danger');
            } else {
                charCount.classList.remove('warning', 'danger');
            }
        });
    }
    
    // Rating input
    setupRatingInput('ratingInput', (rating) => {
        currentUserRating = rating;
        updateRatingText('ratingText', rating);
    });
    
    // Edit comment modal rating
    setupRatingInput('editRatingInput', (rating) => {
        editingCommentRating = rating;
        updateRatingText('editRatingText', rating);
    });
    
    // Edit comment character count
    const editCommentText = document.getElementById('editCommentText');
    const editCharCount = document.getElementById('editCharCount');
    
    if (editCommentText && editCharCount) {
        editCommentText.addEventListener('input', function() {
            const count = this.value.length;
            editCharCount.textContent = count;
            
            if (count > 900) {
                editCharCount.classList.add('danger');
                editCharCount.classList.remove('warning');
            } else if (count > 800) {
                editCharCount.classList.add('warning');
                editCharCount.classList.remove('danger');
            } else {
                editCharCount.classList.remove('warning', 'danger');
            }
        });
    }
    
    // Form buttons
    const submitCommentBtn = document.getElementById('submitCommentBtn');
    const cancelCommentBtn = document.getElementById('cancelCommentBtn');
    const updateCommentBtn = document.getElementById('updateCommentBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (submitCommentBtn) {
        submitCommentBtn.addEventListener('click', submitComment);
    }
    
    if (cancelCommentBtn) {
        cancelCommentBtn.addEventListener('click', hideCommentForm);
    }
    
    if (updateCommentBtn) {
        updateCommentBtn.addEventListener('click', updateComment);
    }
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', confirmDeleteComment);
    }
}

// Setup rating input functionality
function setupRatingInput(containerId, callback) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const stars = container.querySelectorAll('.star');
    
    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            highlightStars(container, index + 1);
        });
        
        star.addEventListener('mouseleave', function() {
            const currentRating = containerId === 'editRatingInput' ? editingCommentRating : currentUserRating;
            highlightStars(container, currentRating);
        });
        
        star.addEventListener('click', function() {
            const rating = index + 1;
            callback(rating);
            highlightStars(container, rating);
        });
    });
}

// Highlight stars up to a certain rating
function highlightStars(container, rating) {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.classList.remove('active');
        }
    });
}

// Update rating text display
function updateRatingText(textElementId, rating) {
    const textElement = document.getElementById(textElementId);
    if (!textElement) return;
    
    const ratingTexts = {
        1: 'Poor',
        2: 'Fair', 
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    
    if (rating > 0) {
        textElement.textContent = `${ratingTexts[rating]} (${rating}/5)`;
    } else {
        textElement.textContent = 'Click stars to rate';
    }
}

// Load comments for the current event
async function loadComments() {
    if (!currentEvent) return;
    
    try {
        const response = await fetch(`/unipulse/public/user/comments/getComments?event_id=${currentEvent.id}`, {
            method: 'GET',
            credentials: 'same-origin' // Include session cookies
        });
        const data = await response.json();
        
        if (data.success) {
            displayComments(data.comments);
            updateCommentsStats(data.stats);
            
            // Check if user can comment
            checkUserCommentStatus();
        } else {
            console.error('Failed to load comments:', data.error);
            displayEmptyComments();
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        displayEmptyComments();
    }
}

// Check if current user can comment
async function checkUserCommentStatus() {
    if (!currentEvent) {
        console.log('No current event found');
        return;
    }
    
    // Check if user is logged in by looking for user info in the page
    const userElement = document.querySelector('.username, #username');
    const isLoggedInByPage = userElement && userElement.textContent.trim() !== '' && userElement.textContent.trim() !== 'Guest';
    
    console.log('User element found:', userElement);
    console.log('Is logged in by page check:', isLoggedInByPage);
    
    if (!isLoggedInByPage) {
        // User not logged in - show login prompt
        console.log('User not logged in - showing login prompt');
        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');
        if (addCommentTrigger) addCommentTrigger.style.display = 'none';
        if (loginPrompt) loginPrompt.style.display = 'block';
        return;
    }
    
    try {
        console.log(`Checking comment status for event ${currentEvent.id}`);
        const response = await fetch(`/unipulse/public/user/comments/checkUserComment/${currentEvent.id}`, {
            method: 'GET',
            credentials: 'same-origin' // Include session cookies
        });
        const data = await response.json();
        
        console.log('Comment status response:', data);
        
        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');
        
        console.log('Elements found:', {
            addCommentTrigger: !!addCommentTrigger,
            loginPrompt: !!loginPrompt
        });
        
        // If API says not logged in but page shows logged in, force show comment button
        if (!data.can_comment && data.debug === 'not_logged_in' && isLoggedInByPage) {
            console.log('API session issue detected - forcing comment button based on page auth');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
            return;
        }
        
        if (data.can_comment) {
            // User can comment
            console.log('User can comment - showing add button');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else if (data.has_commented) {
            // User already commented
            console.log('User already commented - hiding both');
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else {
            // User not logged in or can't comment
            console.log('User cannot comment - showing login prompt');
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'block';
        }
        
    } catch (error) {
        console.error('Error checking user comment status:', error);
        // Fallback: if logged in by page, show comment button
        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');
        
        if (isLoggedInByPage) {
            console.log('API error but user is logged in - showing comment button');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else {
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'block';
        }
    }
}

// Display comments
function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;
    
    if (comments.length === 0) {
        displayEmptyComments();
        return;
    }
    
    const commentsHTML = comments.map(comment => createCommentHTML(comment)).join('');
    commentsList.innerHTML = commentsHTML;
}

// Create HTML for a single comment
function createCommentHTML(comment) {
    const userInitials = comment.user_name.split(' ').map(n => n[0]).join('').toUpperCase();
    const ratingStars = comment.rating ? '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating) : '';
    const editedBadge = comment.is_edited ? '<span class="edited-badge">Edited</span>' : '';
    
    const actionButtons = comment.can_edit || comment.can_delete ? `
        <div class="comment-actions">
            ${comment.can_edit ? `
                <button class="action-btn edit" onclick="editComment(${comment.id})">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
            ` : ''}
            ${comment.can_delete ? `
                <button class="action-btn delete" onclick="deleteComment(${comment.id})">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            ` : ''}
        </div>
    ` : '';
    
    return `
        <div class="comment-card" data-comment-id="${comment.id}">
            <div class="comment-header">
                <div class="comment-user">
                    <div class="user-avatar">${userInitials}</div>
                    <div class="user-info">
                        <h4>${escapeHtml(comment.user_name)}</h4>
                        <p>${comment.user_type.charAt(0).toUpperCase() + comment.user_type.slice(1)} User ${editedBadge}</p>
                    </div>
                </div>
                
                <div class="comment-meta">
                    ${comment.rating ? `
                        <div class="comment-rating">
                            <span class="stars">${ratingStars}</span>
                            <span class="rating-value">${comment.rating}/5</span>
                        </div>
                    ` : ''}
                    <p>${comment.formatted_date}</p>
                </div>
            </div>
            
            <div class="comment-content">
                ${escapeHtml(comment.comment_text)}
            </div>
            
            ${actionButtons}
        </div>
    `;
}

// Display empty comments state
function displayEmptyComments() {
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;
    
    commentsList.innerHTML = `
        <div class="empty-comments">
            <i class="fas fa-comments"></i>
            <h4>No comments yet</h4>
            <p>Be the first to share your experience about this event.</p>
        </div>
    `;
    
    // Check if user can comment when displaying empty state
    checkUserCommentStatus();
}

// Update comments statistics
function updateCommentsStats(stats) {
    const totalCommentsCount = document.getElementById('totalCommentsCount');
    const averageRatingDisplay = document.getElementById('averageRatingDisplay');
    const averageRatingValue = document.getElementById('averageRatingValue');
    
    if (totalCommentsCount) {
        totalCommentsCount.textContent = stats.total_comments;
    }
    
    if (stats.average_rating && averageRatingDisplay && averageRatingValue) {
        averageRatingValue.textContent = stats.average_rating;
        averageRatingDisplay.style.display = 'inline-flex';
    } else if (averageRatingDisplay) {
        averageRatingDisplay.style.display = 'none';
    }
}

// Show comment form
function showCommentForm() {
    const addCommentSection = document.getElementById('addCommentSection');
    const addCommentTrigger = document.getElementById('addCommentTrigger');
    
    if (addCommentSection) addCommentSection.style.display = 'block';
    if (addCommentTrigger) addCommentTrigger.style.display = 'none';
    
    // Focus on textarea
    const commentText = document.getElementById('commentText');
    if (commentText) {
        commentText.focus();
    }
}

// Make function globally accessible
window.showCommentForm = showCommentForm;

// Hide comment form
function hideCommentForm() {
    const addCommentSection = document.getElementById('addCommentSection');
    const addCommentTrigger = document.getElementById('addCommentTrigger');
    
    if (addCommentSection) addCommentSection.style.display = 'none';
    if (addCommentTrigger) addCommentTrigger.style.display = 'block';
    
    // Reset form
    resetCommentForm();
}

// Reset comment form
function resetCommentForm() {
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');
    const ratingText = document.getElementById('ratingText');
    
    if (commentText) commentText.value = '';
    if (charCount) {
        charCount.textContent = '0';
        charCount.classList.remove('warning', 'danger');
    }
    
    currentUserRating = 0;
    highlightStars(document.getElementById('ratingInput'), 0);
    updateRatingText('ratingText', 0);
}

// Submit comment
async function submitComment() {
    const commentText = document.getElementById('commentText').value.trim();
    
    if (!commentText) {
        showToast('Please enter a comment', 'error');
        return;
    }
    
    if (commentText.length < 5) {
        showToast('Comment must be at least 5 characters long', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submitCommentBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    submitBtn.disabled = true;
    
    try {
        // Get user info from the page as fallback for session issues
        const userElement = document.querySelector('.username, #username');
        const userName = userElement ? userElement.textContent.trim() : '';
        
        console.log('Submitting comment:', {
            event_id: currentEvent.id,
            comment_text: commentText,
            rating: currentUserRating,
            fallback_user_name: userName
        });
        
        const response = await fetch('/unipulse/public/user/comments/addComment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // Include session cookies
            body: JSON.stringify({
                event_id: currentEvent.id,
                comment_text: commentText,
                rating: currentUserRating || null,
                fallback_user_name: userName // Add fallback user identification
            })
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showToast('Comment posted successfully!', 'success');
            hideCommentForm();
            resetCommentForm();
            loadComments(); // Reload comments to show the new one
        } else {
            showToast(data.error || 'Failed to post comment', 'error');
            console.error('Comment submission error:', data);
        }
    } catch (error) {
        console.error('Error posting comment:', error);
        showToast('Failed to post comment', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
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
    editingCommentRating = currentRating;
    
    // Populate edit form
    const editCommentText = document.getElementById('editCommentText');
    const editCharCount = document.getElementById('editCharCount');
    
    if (editCommentText) {
        editCommentText.value = commentContent;
        editCharCount.textContent = commentContent.length;
    }
    
    // Set rating
    highlightStars(document.getElementById('editRatingInput'), currentRating);
    updateRatingText('editRatingText', currentRating);
    
    // Show modal
    document.getElementById('editCommentModal').style.display = 'flex';
}

// Make function globally accessible
window.editComment = editComment;

// Update comment
async function updateComment() {
    const commentText = document.getElementById('editCommentText').value.trim();
    
    if (!commentText) {
        showToast('Please enter a comment', 'error');
        return;
    }
    
    if (commentText.length < 5) {
        showToast('Comment must be at least 5 characters long', 'error');
        return;
    }
    
    const updateBtn = document.getElementById('updateCommentBtn');
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    updateBtn.disabled = true;
    
    try {
        const response = await fetch(`/unipulse/public/user/comments/updateComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // Include session cookies
            body: JSON.stringify({
                comment_text: commentText,
                rating: editingCommentRating || null
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Comment updated successfully!', 'success');
            closeEditCommentModal();
            loadComments(); // Reload comments
        } else {
            showToast(data.error || 'Failed to update comment', 'error');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showToast('Failed to update comment', 'error');
    } finally {
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    }
}

// Delete comment
function deleteComment(commentId) {
    editingCommentId = commentId;
    document.getElementById('deleteCommentModal').style.display = 'flex';
}

// Make function globally accessible
window.deleteComment = deleteComment;

// Confirm delete comment
async function confirmDeleteComment() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    deleteBtn.disabled = true;
    
    try {
        const response = await fetch(`/unipulse/public/user/comments/deleteComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Include session cookies
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Comment deleted successfully!', 'success');
            closeDeleteCommentModal();
            loadComments(); // Reload comments
        } else {
            showToast(data.error || 'Failed to delete comment', 'error');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showToast('Failed to delete comment', 'error');
    } finally {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}

// Modal functions for comments
function closeEditCommentModal() {
    document.getElementById('editCommentModal').style.display = 'none';
    editingCommentId = null;
    editingCommentRating = 0;
}

function closeDeleteCommentModal() {
    document.getElementById('deleteCommentModal').style.display = 'none';
    editingCommentId = null;
}

// Make functions globally accessible
window.closeEditCommentModal = closeEditCommentModal;
window.closeDeleteCommentModal = closeDeleteCommentModal;

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const joinModal = document.getElementById('joinModal');
    const shareModal = document.getElementById('shareModal');
    const editCommentModal = document.getElementById('editCommentModal');
    const deleteCommentModal = document.getElementById('deleteCommentModal');
    
    if (event.target === joinModal) {
        closeJoinModal();
    }
    if (event.target === shareModal) {
        closeShareModal();
    }
    if (event.target === editCommentModal) {
        closeEditCommentModal();
    }
    if (event.target === deleteCommentModal) {
        closeDeleteCommentModal();
    }
});

// Toast notification system
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
