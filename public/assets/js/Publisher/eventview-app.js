// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
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
                    displayEventDetails(currentEvent);
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
    const locationType = event.location_type || 'inside-university';
    
    document.getElementById('eventDateTime').textContent = `${formatDate(eventDate)} at ${eventTime}`;
    
    // Display location fields based on location type
    if (locationType === 'outside-university') {
        // Outside university: show venue and city
        const venueName = event.venue_name || event.venueName || '';
        const city = event.city || '';
        let venueCity = '';
        
        if (venueName && city) {
            venueCity = `${venueName}, ${city}`;
        } else if (venueName) {
            venueCity = venueName;
        } else if (city) {
            venueCity = city;
        } else {
            venueCity = 'Location TBA';
        }
        
        document.getElementById('venueInfo').style.display = 'flex';
        document.getElementById('eventVenueCity').textContent = venueCity;
        
        // Hide inside university fields
        document.getElementById('universityInfo').style.display = 'none';
        document.getElementById('facultyInfo').style.display = 'none';
        document.getElementById('exactLocationInfo').style.display = 'none';
    } else {
        // Inside university: show university, faculty, and exact location
        document.getElementById('universityInfo').style.display = 'flex';
        document.getElementById('eventUniversity').textContent = universityName;
        
        // Show faculty/department if available
        if (event.faculty_department) {
            document.getElementById('facultyInfo').style.display = 'flex';
            document.getElementById('eventFaculty').textContent = event.faculty_department;
        }
        
        document.getElementById('exactLocationInfo').style.display = 'flex';
        document.getElementById('eventLocation').textContent = event.location;
        
        // Hide outside university field
        document.getElementById('venueInfo').style.display = 'none';
    }
    
    // Target audience
    document.getElementById('eventAudience').textContent = formatAudience(targetAudience);
    
    // Ticket type - Always show with appropriate display
    document.getElementById('ticketInfo').style.display = 'block';
    if (ticketType === 'free-all') {
        document.getElementById('eventTicketType').innerHTML = '<span style="color: #10B981; font-weight: 600;">Free Event</span>';
    } else {
        document.getElementById('eventTicketType').textContent = formatTicketType(ticketType);
    }
    
    // Full description
    document.getElementById('eventDescription').textContent = event.description;
    
    // Registration & Ticket Periods
    displayRegistrationTicketPeriods(event);
    
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
    
    // Handle registration and ticketing sections
    displayRegistrationTicketing(event);
    
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
    
    // Show volunteer/donation section if either is available
    const hasVolunteer = event.needs_volunteers && event.needs_volunteers == 1;
    const hasDonation = event.accepts_donations && event.accepts_donations == 1;
    if (hasVolunteer || hasDonation) {
        document.getElementById('volunteerDonationHeader').style.display = 'block';
        document.getElementById('volunteerDonationGrid').style.display = 'grid';
    } else {
        document.getElementById('volunteerDonationHeader').style.display = 'none';
        document.getElementById('volunteerDonationGrid').style.display = 'none';
    }
    
    // Organizer info
    document.getElementById('organizerName').textContent = event.organizer;
    
    // Set organizer role if available, otherwise use default
    const organizerRoleElement = document.getElementById('organizerRole');
    if (organizerRoleElement) {
        organizerRoleElement.textContent = event.organizer_role || 'Event Organizer';
    }
    
    // Display organizer profile photo if available
    const organizerAvatar = document.getElementById('organizerAvatar');
    if (event.organizer_photo) {
        organizerAvatar.innerHTML = `<img src="${event.organizer_photo}" alt="${event.organizer}" />`;
    } else {
        organizerAvatar.innerHTML = '<i class="fas fa-user-circle"></i>';
    }
    
    // Store organizer data for contact functions
    currentEvent.organizerEmail = organizerEmail;
    currentEvent.organizerId = event.created_by;
    currentEvent.organizerPhone = event.organizer_phone;
    
    // Setup phone button
    const callBtn = document.getElementById('callOrganizerBtn');
    if (event.organizer_phone) {
        callBtn.onclick = () => window.location.href = `tel:${event.organizer_phone}`;
        callBtn.setAttribute('title', `Call: ${event.organizer_phone}`);
    } else {
        callBtn.disabled = true;
        callBtn.style.opacity = '0.5';
        callBtn.setAttribute('title', 'Phone not available');
    }
    
    // Statistics - only show if max_participants is set
    if (maxParticipants !== null && maxParticipants !== undefined) {
        // Show in Registration Section
        const statsRegSection = document.getElementById('eventStatsRegistration');
        if (statsRegSection) {
            statsRegSection.style.display = 'block';
            document.getElementById('totalParticipantsReg').textContent = currentParticipants;
            document.getElementById('availableSpotsReg').textContent = maxParticipants - currentParticipants;
            document.getElementById('maxCapacityReg').textContent = maxParticipants;
            
            // Participation percentage
            const percentage = maxParticipants > 0 ? Math.round((currentParticipants / maxParticipants) * 100) : 0;
            document.getElementById('capacityPercentage').textContent = `${percentage}%`;
            document.getElementById('capacityFill').style.width = `${percentage}%`;
            
            // Update capacity fill color based on percentage
            const capacityFill = document.getElementById('capacityFill');
            if (percentage >= 90) {
                capacityFill.style.background = 'linear-gradient(90deg, #ef4444 0%, #dc2626 100%)';
            } else if (percentage >= 70) {
                capacityFill.style.background = 'linear-gradient(90deg, #f59e0b 0%, #d97706 100%)';
            } else {
                capacityFill.style.background = 'linear-gradient(90deg, #10b981 0%, #059669 100%)';
            }
            
            // Show ticket type breakdown for non-free events
            displayTicketTypeBreakdown(event, currentParticipants, maxParticipants);
        }
    } else {
        if (document.getElementById('eventStatsRegistration')) {
            document.getElementById('eventStatsRegistration').style.display = 'none';
        }
    }
    
    // Set event link for sharing
    if (document.getElementById('shareLink')) {
        document.getElementById('shareLink').value = window.location.href;
    }
    
    // Update status styling
    updateStatusStyling(event.status, currentParticipants, maxParticipants);
    
    // Initialize comments for completed events
    initializeComments();
}

// Display registration period
// Display registration and ticket periods
function displayRegistrationTicketPeriods(event) {
    const ticketType = event.ticket_type || 'free-all';
    const hasRegistrationDates = event.registration_start_date && event.registration_end_date;
    const hasTicketDates = event.ticket_sale_start_date && event.ticket_sale_end_date;
    
    const periodCard = document.getElementById('registrationTicketPeriodCard');
    const freeRegSection = document.getElementById('freeRegistrationPeriod');
    const ticketBuySection = document.getElementById('ticketBuyingPeriod');
    const divider = document.getElementById('periodDivider');
    
    let showAnyPeriod = false;
    
    // For free-all events with registration
    if (ticketType === 'free-all' && hasRegistrationDates) {
        showAnyPeriod = true;
        freeRegSection.style.display = 'block';
        ticketBuySection.style.display = 'none';
        
        const startDate = formatDate(event.registration_start_date);
        const endDate = formatDate(event.registration_end_date);
        const status = getRegistrationStatus(event.registration_start_date, event.registration_end_date);
        
        document.getElementById('freeRegPeriodDates').innerHTML = `
            <span class="period-date-item">
                <i class="fas fa-calendar-plus"></i>
                <strong>Opens:</strong> ${startDate}
            </span>
            <span class="period-date-separator">→</span>
            <span class="period-date-item">
                <i class="fas fa-calendar-times"></i>
                <strong>Closes:</strong> ${endDate}
            </span>
        `;
        
        document.getElementById('freeRegPeriodStatus').innerHTML = `
            <i class="fas fa-${status.icon}"></i>
            ${status.text}
        `;
        document.getElementById('freeRegPeriodStatus').className = `period-status status-${status.class}`;
    } else {
        freeRegSection.style.display = 'none';
    }
    
    // For paid or mixed events with ticket sales period
    if ((ticketType === 'paid-all' || ticketType === 'mixed') && hasTicketDates) {
        showAnyPeriod = true;
        ticketBuySection.style.display = 'block';
        
        const startDate = formatDate(event.ticket_sale_start_date);
        const endDate = formatDate(event.ticket_sale_end_date);
        const status = getRegistrationStatus(event.ticket_sale_start_date, event.ticket_sale_end_date);
        
        document.getElementById('ticketPeriodDates').innerHTML = `
            <span class="period-date-item">
                <i class="fas fa-calendar-plus"></i>
                <strong>Opens:</strong> ${startDate}
            </span>
            <span class="period-date-separator">→</span>
            <span class="period-date-item">
                <i class="fas fa-calendar-times"></i>
                <strong>Closes:</strong> ${endDate}
            </span>
        `;
        
        document.getElementById('ticketPeriodStatus').innerHTML = `
            <i class="fas fa-${status.icon}"></i>
            ${status.text}
        `;
        document.getElementById('ticketPeriodStatus').className = `period-status status-${status.class}`;
        
        // Show both periods for mixed events if both dates exist
        if (ticketType === 'mixed' && hasRegistrationDates) {
            freeRegSection.style.display = 'block';
            
            const freeStartDate = formatDate(event.registration_start_date);
            const freeEndDate = formatDate(event.registration_end_date);
            const freeStatus = getRegistrationStatus(event.registration_start_date, event.registration_end_date);
            
            document.getElementById('freeRegPeriodDates').innerHTML = `
                <span class="period-date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <strong>Opens:</strong> ${freeStartDate}
                </span>
                <span class="period-date-separator">→</span>
                <span class="period-date-item">
                    <i class="fas fa-calendar-times"></i>
                    <strong>Closes:</strong> ${freeEndDate}
                </span>
            `;
            
            document.getElementById('freeRegPeriodStatus').innerHTML = `
                <i class="fas fa-${freeStatus.icon}"></i>
                ${freeStatus.text}
            `;
            document.getElementById('freeRegPeriodStatus').className = `period-status status-${freeStatus.class}`;
        }
    }
    
    // Show/hide the entire period card
    if (showAnyPeriod) {
        periodCard.style.display = 'block';
        // Show divider if both periods are visible
        if (freeRegSection.style.display !== 'none' && ticketBuySection.style.display !== 'none') {
            divider.style.display = 'block';
        } else {
            divider.style.display = 'none';
        }
    } else {
        periodCard.style.display = 'none';
    }
}

// Helper function to determine registration status
function getRegistrationStatus(startDate, endDate) {
    const now = new Date();
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (now < start) {
        return {
            text: 'Opening Soon',
            icon: 'clock',
            class: 'upcoming'
        };
    } else if (now >= start && now <= end) {
        return {
            text: 'Open Now',
            icon: 'check-circle',
            class: 'open'
        };
    } else {
        return {
            text: 'Closed',
            icon: 'times-circle',
            class: 'closed'
        };
    }
}

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
            
            // Update participant count in registration section if visible
            if (maxParticipants !== null && maxParticipants !== undefined) {
                document.getElementById('totalParticipantsReg').textContent = newCurrentParticipants;
                document.getElementById('availableSpotsReg').textContent = maxParticipants - newCurrentParticipants;
                
                // Update capacity percentage
                const percentage = maxParticipants > 0 ? Math.round((newCurrentParticipants / maxParticipants) * 100) : 0;
                document.getElementById('capacityPercentage').textContent = `${percentage}%`;
                document.getElementById('capacityFill').style.width = `${percentage}%`;
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

function visitPublisherProfile() {
    console.log('=== visitPublisherProfile DEBUG ===');
    console.log('currentEvent:', currentEvent);
    console.log('currentEvent.created_by:', currentEvent?.created_by);
    console.log('currentEvent.organizerId:', currentEvent?.organizerId);
    console.log('currentEvent.created_by_type:', currentEvent?.created_by_type);
    
    const publisherId = currentEvent?.organizerId || currentEvent?.created_by;
    console.log('Final publisherId to use:', publisherId);
    
    if (publisherId) {
        const url = `/unipulse/public/publisher/public?id=${publisherId}`;
        console.log('Redirecting to:', url);
        window.location.href = url;
    } else {
        console.error('No publisher ID found in event data');
        console.error('Event object:', JSON.stringify(currentEvent, null, 2));
        alert('Publisher profile not available.');
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
        'free-students': 'Free for University Students',
        'free-all': 'Free for University Students', // backward compatibility
        'paid-all': 'Paid Tickets Required',
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
        locationHTML = '';
        
        if (universityName) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="location-content">
                        <strong>University</strong>
                        <span>${universityName}</span>
                    </div>
                </div>
            `;
        }
        
        if (event.faculty_department) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="location-content">
                        <strong>Faculty/Department</strong>
                        <span>${event.faculty_department}</span>
                    </div>
                </div>
            `;
        }
        
        if (event.location) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="location-content">
                        <strong>Exact Location</strong>
                        <span>${event.location}</span>
                    </div>
                </div>
            `;
        }
        
        // Only show if there's actual content
        if (universityName || event.faculty_department || event.location) {
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
        }
    }
}

function displayTicketDetails(event) {
    const ticketType = event.ticket_type || 'free-all';
    const ticketDetailsCard = document.getElementById('ticketDetailsCard');
    const ticketDetailsDiv = document.getElementById('ticketDetails');
    
    if (ticketType === 'free-all') {
        // Show "Free Event" information
        ticketDetailsCard.style.display = 'block';
        ticketDetailsDiv.innerHTML = `
            <div class="ticket-detail-item" style="text-align: center; padding: 20px;">
                <div style="font-size: 48px; color: #10B981; margin-bottom: 10px;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div style="font-size: 24px; font-weight: 600; color: #10B981; margin-bottom: 10px;">
                    Free Event
                </div>
                <div style="color: #666; font-size: 14px;">
                    No ticket purchase required. Everyone is welcome to attend!
                </div>
            </div>
        `;
        return;
    }
    
    // For paid or mixed events
    let ticketHTML = '<div class="ticket-detail-item">';
    ticketHTML += `<div style="margin-bottom: 15px;"><strong>Ticket Type:</strong> <span style="color: #3b82f6; font-weight: 600;">${formatTicketType(ticketType)}</span></div>`;
    
    if (event.registration_start_date && event.registration_end_date) {
        ticketHTML += `<div style="margin-bottom: 10px;"><strong>Registration Period:</strong> ${formatDate(event.registration_start_date)} to ${formatDate(event.registration_end_date)}</div>`;
    }
    
    if (event.registration_limit) {
        ticketHTML += `<div style="margin-bottom: 15px;"><strong>Registration Limit:</strong> ${event.registration_limit} participants</div>`;
    }
    
    // Display ticket types and prices
    if (event.ticket_types) {
        let tickets = event.ticket_types;
        
        // Parse if it's a JSON string
        if (typeof tickets === 'string') {
            try {
                tickets = JSON.parse(tickets);
            } catch (e) {
                console.error('Error parsing ticket_types:', e);
                tickets = [];
            }
        }
        
        if (Array.isArray(tickets) && tickets.length > 0) {
            ticketHTML += '<div style="margin-top: 25px; margin-bottom: 20px;"><strong style="font-size: 22px; color: #1f2937; letter-spacing: 0.5px;">Available Tickets:</strong></div>';
            ticketHTML += '<div class="ticket-types-list" style="margin-top: 15px;">';
            
            tickets.forEach((ticket, index) => {
                // Calculate capacity percentage and determine colors
                const totalCapacity = parseInt(ticket.total_capacity || ticket.quantity); // Use total_capacity from backend
                const available = parseInt(ticket.quantity);
                const sold = totalCapacity - available;
                const soldPercentage = totalCapacity > 0 ? ((sold / totalCapacity) * 100).toFixed(1) : '0.0';
                const availablePercentage = totalCapacity > 0 ? ((available / totalCapacity) * 100).toFixed(1) : '100.0';
                
                // Determine progress bar color based on availability
                let progressColor, progressBg, statusText;
                if (availablePercentage >= 50) {
                    progressColor = '#10B981'; // Green - plenty available
                    progressBg = '#D1FAE5';
                    statusText = 'Good Availability';
                } else if (availablePercentage >= 25) {
                    progressColor = '#F59E0B'; // Amber - selling fast
                    progressBg = '#FEF3C7';
                    statusText = 'Selling Fast';
                } else if (availablePercentage > 0) {
                    progressColor = '#EF4444'; // Red - almost sold out
                    progressBg = '#FEE2E2';
                    statusText = 'Almost Sold Out';
                } else {
                    progressColor = '#6B7280'; // Gray - sold out
                    progressBg = '#F3F4F6';
                    statusText = 'Sold Out';
                }
                
                ticketHTML += `
                    <div class="ticket-option" data-ticket-index="${index}" data-ticket-name="${ticket.name}" data-ticket-price="${ticket.price}" data-ticket-quantity="${ticket.quantity}" style="background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border: 3px solid #d1d5db; border-radius: 16px; padding: 24px; margin-bottom: 18px; transition: all 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.08); cursor: pointer;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 24px; color: #1f2937; margin-bottom: 10px; letter-spacing: 0.5px;">${ticket.name}</div>
                                ${ticket.description ? `<div style="color: #6b7280; font-size: 16px; margin-bottom: 12px; line-height: 1.5;">${ticket.description}</div>` : ''}
                                
                                <!-- Capacity Indicator -->
                                <div style="margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <span style="font-size: 13px; font-weight: 600; color: ${progressColor};">${statusText}</span>
                                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">${soldPercentage}% Sold</span>
                                    </div>
                                    <div style="width: 100%; height: 12px; background: ${progressBg}; border-radius: 6px; overflow: hidden; position: relative;">
                                        <div style="height: 100%; background: ${progressColor}; width: ${soldPercentage}%; transition: width 0.5s ease; border-radius: 6px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 12px; color: #9ca3af;">
                                        <span>${sold} sold</span>
                                        <span>${available} available</span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 16px; font-size: 15px; color: #6b7280; margin-top: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 8px; background: #e0f2fe; padding: 8px 14px; border-radius: 8px; font-weight: 600; color: #0369a1;"><i class="fas fa-users" style="font-size: 16px;"></i> Available: ${ticket.quantity}</span>
                                    ${ticket.benefits ? `<span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; color: #f59e0b;"><i class="fas fa-star" style="font-size: 14px;"></i> ${ticket.benefits}</span>` : ''}
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                                <span style="font-size: 26px; font-weight: 800; color: #3b82f6; white-space: nowrap; letter-spacing: 0.5px;">LKR ${parseFloat(ticket.price).toFixed(2)}</span>
                                <div class="quantity-selector" style="display: flex; align-items: center; gap: 6px;" onclick="event.stopPropagation();">
                                    <button type="button" class="quantity-btn" onclick="updateTicketQuantity(${index}, -1)" style="width: 36px; height: 36px; border: 2px solid #d1d5db; background: white; border-radius: 8px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0; font-weight: 600;">-</button>
                                    <input type="number" id="ticket-quantity-${index}" value="0" min="0" max="${ticket.quantity}" style="width: 60px; text-align: center; padding: 8px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 16px; height: 36px; font-weight: 600;" onchange="validateTicketQuantity(${index})" />
                                    <button type="button" class="quantity-btn" onclick="updateTicketQuantity(${index}, 1)" style="width: 36px; height: 36px; border: 2px solid #d1d5db; background: white; border-radius: 8px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0; font-weight: 600;">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            ticketHTML += '</div>';
        }
    }
    
    ticketHTML += '</div>';
    
    ticketDetailsCard.style.display = 'block';
    ticketDetailsDiv.innerHTML = ticketHTML;
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
    
    // if (event.volunteer_positions && Array.isArray(event.volunteer_positions)) {
    //     volunteerHTML += '<div><strong>Available Positions:</strong></div>';
    //     volunteerHTML += '<ul class="volunteer-positions-list">';
    //     event.volunteer_positions.forEach(position => {
    //         volunteerHTML += `<li>${position}</li>`;
    //     });
    //     volunteerHTML += '</ul>';
    // }
    
    volunteerHTML += '<div style="margin-top: 15px;">';
    volunteerHTML += '<button class="btn btn-primary" onclick="applyAsVolunteer()">Apply as Volunteer</button>';
    volunteerHTML += '</div>';
    
    volunteerHTML += '</div>';
    
    document.getElementById('volunteerCard').style.display = 'block';
    document.getElementById('volunteerInfo').innerHTML = volunteerHTML;
}

// Display Registration and Ticketing Section
function displayRegistrationTicketing(event) {
    const ticketType = event.ticket_type || 'free-students';
    const requiresRegistration = event.requires_registration === 1 || event.requires_registration === '1' || event.requires_registration === true;
    
    const freeSection = document.getElementById('freeRegistrationSection');
    const paidSection = document.getElementById('paidTicketingSection');
    const mixedSection = document.getElementById('mixedTicketingSection');
    
    // Hide all sections first
    freeSection.style.display = 'none';
    paidSection.style.display = 'none';
    mixedSection.style.display = 'none';
    
    // Scenario 1: Free for University Students (free-students or free-all for backward compatibility)
    if (ticketType === 'free-students' || ticketType === 'free-all') {
        freeSection.style.display = 'block';
        
        if (requiresRegistration) {
            // Free WITH registration required
            document.getElementById('freeRegRequired').style.display = 'block';
            document.getElementById('freeNoRegRequired').style.display = 'none';
            document.getElementById('freeEntrySubtitle').textContent = 'Free entry with registration required';
        } else {
            // Free WITHOUT registration (open entry/walk-in)
            document.getElementById('freeRegRequired').style.display = 'none';
            document.getElementById('freeNoRegRequired').style.display = 'block';
            document.getElementById('freeEntrySubtitle').textContent = 'Open entry - no registration needed';
        }
        
    } else if (ticketType === 'paid-all') {
        // Scenario 2: Paid for Everyone - all must buy tickets
        paidSection.style.display = 'block';
        
        // Set initial ticket price to zero
        const ticketPriceElement = document.getElementById('ticketPrice');
        if (ticketPriceElement) {
            ticketPriceElement.textContent = 'LKR 0.00';
        }
        
    } else if (ticketType === 'mixed') {
        // Scenario 3: Mixed - Free for uni students, Paid for others
        mixedSection.style.display = 'block';
        
        if (requiresRegistration) {
            // Free WITH registration for students
            document.getElementById('studentRegRequired').style.display = 'block';
            document.getElementById('studentNoRegRequired').style.display = 'none';
        } else {
            // Free WITHOUT registration for students (walk-in with student ID)
            document.getElementById('studentRegRequired').style.display = 'none';
            document.getElementById('studentNoRegRequired').style.display = 'block';
        }
        
        // Display public tickets in the details card
        displayMixedPublicTickets(event);
    }
}

// Display tickets for General Public in mixed events - same as paid tickets
function displayMixedPublicTickets(event) {
    const ticketDetailsCard = document.getElementById('mixedTicketDetailsCard');
    const ticketDetailsDiv = document.getElementById('mixedTicketDetails');
    
    if (!ticketDetailsCard || !ticketDetailsDiv) return;
    
    let ticketHTML = '';
    
    // Add Ticket Type and Registration Period at the top (inside scrollable area)
    ticketHTML += '<div style="margin-bottom: 20px;">';
    ticketHTML += '<div style="margin-bottom: 15px;">';
    ticketHTML += '<strong style="font-size: 16px;">Ticket Type:</strong> ';
    ticketHTML += '<span style="color: #3b82f6; font-weight: 600; font-size: 16px;">Paid Tickets Required</span>';
    ticketHTML += '</div>';
    
    if (event.registration_start_date && event.registration_end_date) {
        ticketHTML += '<div style="margin-bottom: 15px;">';
        ticketHTML += '<strong style="font-size: 16px;">Registration Period:</strong> ';
        ticketHTML += `<span style="font-size: 16px;">${formatDate(event.registration_start_date)} to ${formatDate(event.registration_end_date)}</span>`;
        ticketHTML += '</div>';
    }
    ticketHTML += '</div>';
    
    // Check if we have ticket types
    if (event.ticket_types) {
        let tickets = event.ticket_types;
        
        // Parse if it's a JSON string
        if (typeof tickets === 'string') {
            try {
                tickets = JSON.parse(tickets);
            } catch (e) {
                console.error('Error parsing ticket_types:', e);
                tickets = [];
            }
        }
        
        if (Array.isArray(tickets) && tickets.length > 0) {
            ticketHTML += '<div style="margin-top: 25px; margin-bottom: 20px;"><strong style="font-size: 22px; color: #1f2937; letter-spacing: 0.5px;">Available Tickets:</strong></div>';
            ticketHTML += '<div class="ticket-types-list" style="margin-top: 15px;">';
            
            tickets.forEach((ticket, index) => {
                // Calculate capacity percentage and determine colors
                const totalCapacity = parseInt(ticket.total_capacity || ticket.quantity);
                const available = parseInt(ticket.quantity);
                const sold = totalCapacity - available;
                const soldPercentage = totalCapacity > 0 ? ((sold / totalCapacity) * 100).toFixed(1) : '0.0';
                const availablePercentage = totalCapacity > 0 ? ((available / totalCapacity) * 100).toFixed(1) : '100.0';
                
                // Determine progress bar color based on availability
                let progressColor, progressBg, statusText;
                if (availablePercentage >= 50) {
                    progressColor = '#10B981';
                    progressBg = '#D1FAE5';
                    statusText = 'Good Availability';
                } else if (availablePercentage >= 25) {
                    progressColor = '#F59E0B';
                    progressBg = '#FEF3C7';
                    statusText = 'Selling Fast';
                } else if (availablePercentage > 0) {
                    progressColor = '#EF4444';
                    progressBg = '#FEE2E2';
                    statusText = 'Almost Sold Out';
                } else {
                    progressColor = '#6B7280';
                    progressBg = '#F3F4F6';
                    statusText = 'Sold Out';
                }
                
                ticketHTML += `
                    <div class="ticket-option" data-ticket-index="${index}" data-ticket-name="${ticket.name}" data-ticket-price="${ticket.price}" data-ticket-quantity="${ticket.quantity}" style="background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border: 3px solid #d1d5db; border-radius: 16px; padding: 24px; margin-bottom: 18px; transition: all 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.08); cursor: pointer;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 24px; color: #1f2937; margin-bottom: 10px; letter-spacing: 0.5px;">${ticket.name}</div>
                                ${ticket.description ? `<div style="color: #6b7280; font-size: 16px; margin-bottom: 12px; line-height: 1.5;">${ticket.description}</div>` : ''}
                                
                                <!-- Capacity Indicator -->
                                <div style="margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <span style="font-size: 13px; font-weight: 600; color: ${progressColor};">${statusText}</span>
                                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">${soldPercentage}% Sold</span>
                                    </div>
                                    <div style="width: 100%; height: 12px; background: ${progressBg}; border-radius: 6px; overflow: hidden; position: relative;">
                                        <div style="height: 100%; background: ${progressColor}; width: ${soldPercentage}%; transition: width 0.5s ease; border-radius: 6px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 12px; color: #9ca3af;">
                                        <span>${sold} sold</span>
                                        <span>${available} available</span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 16px; font-size: 15px; color: #6b7280; margin-top: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 8px; background: #e0f2fe; padding: 8px 14px; border-radius: 8px; font-weight: 600; color: #0369a1;"><i class="fas fa-users" style="font-size: 16px;"></i> Available: ${ticket.quantity}</span>
                                    ${ticket.benefits ? `<span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; color: #f59e0b;"><i class="fas fa-star" style="font-size: 14px;"></i> ${ticket.benefits}</span>` : ''}
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                                <span style="font-size: 26px; font-weight: 800; color: #3b82f6; white-space: nowrap; letter-spacing: 0.5px;">LKR ${parseFloat(ticket.price).toFixed(2)}</span>
                                <div class="quantity-selector" style="display: flex; align-items: center; gap: 6px;" onclick="event.stopPropagation();">
                                    <button type="button" class="quantity-btn" onclick="updateMixedTicketQuantity(${index}, -1)" style="width: 36px; height: 36px; border: 2px solid #d1d5db; background: white; border-radius: 8px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0; font-weight: 600;">-</button>
                                    <input type="number" id="mixed-ticket-quantity-${index}" value="0" min="0" max="${ticket.quantity}" style="width: 60px; text-align: center; padding: 8px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 16px; height: 36px; font-weight: 600;" onchange="validateMixedTicketQuantity(${index})" />
                                    <button type="button" class="quantity-btn" onclick="updateMixedTicketQuantity(${index}, 1)" style="width: 36px; height: 36px; border: 2px solid #d1d5db; background: white; border-radius: 8px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0; font-weight: 600;">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            ticketHTML += '</div>';
        }
    }
    
    ticketDetailsDiv.innerHTML = ticketHTML;
}

// Display Ticket Type Breakdown for capacity
function displayTicketTypeBreakdown(event, currentParticipants, maxParticipants) {
    const ticketType = event.ticket_type || 'free-students';
    const breakdownSection = document.getElementById('ticketTypeBreakdown');
    const ticketStatsDiv = document.getElementById('ticketTypeStats');
    
    // Only show breakdown for paid-all and mixed events (not for free-students)
    if (ticketType === 'free-students' || ticketType === 'free-all') {
        breakdownSection.style.display = 'none';
        return;
    }
    
    // Check if we have ticket types with quantities
    if (!event.ticket_types) {
        breakdownSection.style.display = 'none';
        return;
    }
    
    let tickets = event.ticket_types;
    if (typeof tickets === 'string') {
        try {
            tickets = JSON.parse(tickets);
        } catch (e) {
            console.error('Error parsing ticket_types:', e);
            breakdownSection.style.display = 'none';
            return;
        }
    }
    
    if (!Array.isArray(tickets) || tickets.length === 0) {
        breakdownSection.style.display = 'none';
        return;
    }
    
    // Display breakdown
    breakdownSection.style.display = 'block';
    
    let breakdownHTML = '';
    tickets.forEach(ticket => {
        const quantity = parseInt(ticket.quantity) || 0;
        const price = parseFloat(ticket.price) || 0;
        const isFree = price === 0;
        
        // For now, showing total quantity as available (TODO: track actual registrations per ticket type)
        const available = quantity;
        
        breakdownHTML += `
            <div class="ticket-stat-item ${isFree ? 'free' : 'paid'}">
                <div class="ticket-stat-left">
                    <div class="ticket-stat-name">${ticket.name}</div>
                    <div class="ticket-stat-price ${isFree ? 'free-price' : ''}">
                        ${isFree ? 'FREE' : 'LKR ' + price.toFixed(2)}
                    </div>
                </div>
                <div class="ticket-stat-right">
                    <div class="ticket-stat-available">${available}</div>
                    <div class="ticket-stat-total">of ${quantity} available</div>
                </div>
            </div>
        `;
    });
    
    ticketStatsDiv.innerHTML = breakdownHTML;
}

// Functions for registration and ticket purchase
function registerForEvent() {
    alert('Registration functionality will be implemented here');
    // You can redirect to registration page or open a modal
}

function selectTicket(index) {
    // This function is no longer needed as selection is based on quantity > 0
    // Kept for backward compatibility
    updateTotalPrice();
}

// Mixed ticket quantity functions
function updateMixedTicketQuantity(index, change) {
    const input = document.getElementById(`mixed-ticket-quantity-${index}`);
    const currentValue = parseInt(input.value) || 0;
    const maxValue = parseInt(input.max);
    const newValue = Math.max(0, Math.min(maxValue, currentValue + change));
    input.value = newValue;
    
    // Update visual selection based on quantity
    const ticketOption = input.closest('.ticket-option');
    if (ticketOption) {
        if (newValue > 0) {
            ticketOption.style.border = '3px solid #3b82f6';
            ticketOption.style.background = 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)';
        } else {
            ticketOption.style.border = '3px solid #d1d5db';
            ticketOption.style.background = 'linear-gradient(135deg, #f9fafb 0%, #ffffff 100%)';
        }
    }
    
    updateMixedTotalPrice();
}

function validateMixedTicketQuantity(index) {
    const input = document.getElementById(`mixed-ticket-quantity-${index}`);
    const value = parseInt(input.value) || 0;
    const maxValue = parseInt(input.max);
    input.value = Math.max(0, Math.min(maxValue, value));
    
    // Update visual selection based on quantity
    const ticketOption = input.closest('.ticket-option');
    if (ticketOption) {
        if (input.value > 0) {
            ticketOption.style.border = '3px solid #3b82f6';
            ticketOption.style.background = 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)';
        } else {
            ticketOption.style.border = '3px solid #d1d5db';
            ticketOption.style.background = 'linear-gradient(135deg, #f9fafb 0%, #ffffff 100%)';
        }
    }
    
    updateMixedTotalPrice();
}

function updateMixedTotalPrice() {
    // Get all mixed ticket options
    const mixedTicketsContainer = document.getElementById('mixedTicketDetails');
    if (!mixedTicketsContainer) return;
    
    const allTickets = mixedTicketsContainer.querySelectorAll('.ticket-option');
    
    let totalPrice = 0;
    const selectedItems = [];
    
    allTickets.forEach(ticket => {
        const index = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`mixed-ticket-quantity-${index}`);
        const quantity = parseInt(quantityInput?.value) || 0;
        
        // Only count tickets with quantity > 0
        if (quantity > 0) {
            const price = parseFloat(ticket.dataset.ticketPrice);
            totalPrice += price * quantity;
            
            selectedItems.push({
                name: ticket.dataset.ticketName,
                quantity: quantity,
                price: price
            });
        }
    });
    
    const mixedTicketPriceElement = document.getElementById('mixedTicketPrice');
    if (mixedTicketPriceElement) {
        mixedTicketPriceElement.textContent = `LKR ${totalPrice.toFixed(2)}`;
    }
}

function updateTicketQuantity(index, change) {
    const input = document.getElementById(`ticket-quantity-${index}`);
    const currentValue = parseInt(input.value) || 0;
    const maxValue = parseInt(input.max);
    const newValue = Math.max(0, Math.min(maxValue, currentValue + change));
    input.value = newValue;
    
    // Update visual selection based on quantity
    const ticketOption = document.querySelector(`[data-ticket-index="${index}"]`);
    if (ticketOption) {
        if (newValue > 0) {
            ticketOption.style.border = '2px solid #3b82f6';
            ticketOption.style.background = '#eff6ff';
        } else {
            ticketOption.style.border = '2px solid #e5e7eb';
            ticketOption.style.background = '#f9fafb';
        }
    }
    
    updateTotalPrice();
}

function validateTicketQuantity(index) {
    const input = document.getElementById(`ticket-quantity-${index}`);
    const value = parseInt(input.value) || 0;
    const maxValue = parseInt(input.max);
    input.value = Math.max(0, Math.min(maxValue, value));
    
    // Update visual selection based on quantity
    const ticketOption = document.querySelector(`[data-ticket-index="${index}"]`);
    if (ticketOption) {
        if (input.value > 0) {
            ticketOption.style.border = '2px solid #3b82f6';
            ticketOption.style.background = '#eff6ff';
        } else {
            ticketOption.style.border = '2px solid #e5e7eb';
            ticketOption.style.background = '#f9fafb';
        }
    }
    
    updateTotalPrice();
}

function updateTotalPrice() {
    // Get all ticket options
    const allTickets = document.querySelectorAll('.ticket-option');
    
    let totalPrice = 0;
    const selectedItems = [];
    
    allTickets.forEach(ticket => {
        const index = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`ticket-quantity-${index}`);
        const quantity = parseInt(quantityInput?.value) || 0;
        
        // Only count tickets with quantity > 0
        if (quantity > 0) {
            const price = parseFloat(ticket.dataset.ticketPrice);
            totalPrice += price * quantity;
            
            selectedItems.push({
                name: ticket.dataset.ticketName,
                quantity: quantity,
                price: price
            });
        }
    });
    
    const ticketPriceElement = document.getElementById('ticketPrice');
    const mixedTicketPriceElement = document.getElementById('mixedTicketPrice');
    
    if (ticketPriceElement) {
        ticketPriceElement.textContent = `LKR ${totalPrice.toFixed(2)}`;
    }
    if (mixedTicketPriceElement) {
        mixedTicketPriceElement.textContent = `LKR ${totalPrice.toFixed(2)}`;
    }
}

function purchaseTicket() {
    // Check if registration/sale period is active
    if (!isRegistrationPeriodActive()) {
        const periodInfo = getRegistrationPeriodInfo();
        if (periodInfo.status === 'upcoming') {
            alert(`Ticket sales have not started yet.\n\nSales will open on ${periodInfo.startDate}`);
        } else if (periodInfo.status === 'closed') {
            alert(`Ticket sales have ended.\n\nSales closed on ${periodInfo.endDate}`);
        } else {
            alert('Ticket sales are not currently available for this event.');
        }
        return;
    }
    
    const allTickets = document.querySelectorAll('.ticket-option');
    const ticketSelections = [];
    let totalPrice = 0;
    
    allTickets.forEach(ticket => {
        const ticketIndex = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`ticket-quantity-${ticketIndex}`);
        const quantity = parseInt(quantityInput?.value) || 0;
        
        // Only include tickets with quantity > 0
        if (quantity > 0) {
            const ticketName = ticket.dataset.ticketName;
            const ticketPrice = parseFloat(ticket.dataset.ticketPrice);
            const subtotal = ticketPrice * quantity;
            
            ticketSelections.push({
                index: ticketIndex,
                name: ticketName,
                price: ticketPrice,
                quantity: quantity,
                subtotal: subtotal
            });
            
            totalPrice += subtotal;
        }
    });
    
    if (ticketSelections.length === 0) {
        alert('Please select at least one ticket by setting quantity greater than 0');
        return;
    }
    
    // Store ticket selections for payment
    sessionStorage.setItem('selectedTickets', JSON.stringify({
        tickets: ticketSelections,
        total: totalPrice,
        eventId: currentEventData.id
    }));
    
    // Build confirmation message
    let message = 'Selected Tickets:\n\n';
    ticketSelections.forEach(ticket => {
        message += `${ticket.quantity}x ${ticket.name} - LKR ${ticket.subtotal.toFixed(2)}\n`;
    });
    message += `\nTotal: LKR ${totalPrice.toFixed(2)}\n\nProceeding to payment...`;
    
    alert(message);
    // Redirect to payment gateway
    // window.location.href = '/unipulse/public/payment?event_id=' + currentEventData.id;
}

// Check if registration/sale period is active
function isRegistrationPeriodActive() {
    if (!currentEventData) return false;
    
    const now = new Date();
    const startDate = currentEventData.registration_start_date;
    const startTime = currentEventData.registration_start_time;
    const endDate = currentEventData.registration_end_date;
    const endTime = currentEventData.registration_end_time;
    
    // If no registration period is set, allow purchase
    if (!startDate && !endDate) return true;
    
    // Build start datetime
    let startDateTime = null;
    if (startDate) {
        startDateTime = new Date(startDate);
        if (startTime) {
            const [hours, minutes] = startTime.split(':');
            startDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
        }
    }
    
    // Build end datetime
    let endDateTime = null;
    if (endDate) {
        endDateTime = new Date(endDate);
        if (endTime) {
            const [hours, minutes] = endTime.split(':');
            endDateTime.setHours(parseInt(hours), parseInt(minutes), 59, 999);
        } else {
            // If no end time, set to end of day
            endDateTime.setHours(23, 59, 59, 999);
        }
    }
    
    // Check if current time is within the period
    if (startDateTime && now < startDateTime) return false;
    if (endDateTime && now > endDateTime) return false;
    
    return true;
}

// Get registration period information
function getRegistrationPeriodInfo() {
    if (!currentEventData) return { status: 'unknown' };
    
    const now = new Date();
    const startDate = currentEventData.registration_start_date;
    const startTime = currentEventData.registration_start_time;
    const endDate = currentEventData.registration_end_date;
    const endTime = currentEventData.registration_end_time;
    
    // Build start datetime
    let startDateTime = null;
    if (startDate) {
        startDateTime = new Date(startDate);
        if (startTime) {
            const [hours, minutes] = startTime.split(':');
            startDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
        }
    }
    
    // Build end datetime
    let endDateTime = null;
    if (endDate) {
        endDateTime = new Date(endDate);
        if (endTime) {
            const [hours, minutes] = endTime.split(':');
            endDateTime.setHours(parseInt(hours), parseInt(minutes), 59, 999);
        } else {
            endDateTime.setHours(23, 59, 59, 999);
        }
    }
    
    if (startDateTime && now < startDateTime) {
        return {
            status: 'upcoming',
            startDate: startDateTime.toLocaleDateString() + ' ' + (startTime || '00:00')
        };
    }
    
    if (endDateTime && now > endDateTime) {
        return {
            status: 'closed',
            endDate: endDateTime.toLocaleDateString() + ' ' + (endTime || '23:59')
        };
    }
    
    return { status: 'open' };
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

// Ticket Purchase Function
function buyTickets() {
    console.log('buyTickets called');
    
    if (!currentEvent) {
        console.error('currentEvent is not defined');
        alert('Event data not available. Please refresh the page and try again.');
        return;
    }
    
    console.log('Current event:', currentEvent);
    
    const allTickets = document.querySelectorAll('.ticket-option');
    console.log('Found tickets:', allTickets.length);
    
    if (allTickets.length === 0) {
        console.error('No ticket options found in the page');
        alert('No tickets available. Please refresh the page.');
        return;
    }
    
    const ticketSelections = [];
    let totalPrice = 0;
    
    allTickets.forEach(ticket => {
        const index = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`ticket-quantity-${index}`) || 
                            document.getElementById(`mixed-ticket-quantity-${index}`);
        const quantity = parseInt(quantityInput?.value) || 0;
        
        if (quantity > 0) {
            const ticketName = ticket.dataset.ticketName;
            const ticketPrice = parseFloat(ticket.dataset.ticketPrice);
            const subtotal = ticketPrice * quantity;
            
            ticketSelections.push({
                index: index,
                name: ticketName,
                price: ticketPrice,
                quantity: quantity,
                subtotal: subtotal
            });
            
            totalPrice += subtotal;
        }
    });
    
    console.log('Ticket selections:', ticketSelections);
    console.log('Total price:', totalPrice);
    
    if (ticketSelections.length === 0) {
        alert('Please select at least one ticket by setting quantity greater than 0');
        return;
    }
    
    const paymentData = {
        eventId: currentEvent.id,
        eventTitle: currentEvent.title,
        tickets: ticketSelections,
        totalAmount: totalPrice,
        timestamp: Date.now()
    };
    
    sessionStorage.setItem('paymentData', JSON.stringify(paymentData));
    console.log('Payment data saved to sessionStorage');
    
    const publisherId = currentEvent.created_by || currentEvent.organizerId;
    const paymentUrl = `/unipulse/public/payment?` +
        `amount=${totalPrice.toFixed(2)}` +
        `&type=ticket` +
        `&event_id=${currentEvent.id}` +
        (publisherId ? `&publisher_id=${publisherId}` : '') +
        `&description=${encodeURIComponent('Ticket for ' + currentEvent.title)}`;
    
    console.log('Redirecting to:', paymentUrl);
    window.location.href = paymentUrl;
}

// Expose functions globally for inline event handlers
window.buyTickets = buyTickets;
window.editEvent = editEvent;
window.deleteEvent = deleteEvent;
window.visitPublisherProfile = visitPublisherProfile;
window.contactOrganizer = contactOrganizer;
window.registerForEvent = registerForEvent;
window.openDonationModal = openDonationModal;
window.closeDonationModal = closeDonationModal;
window.processDonation = processDonation;
window.closeJoinModal = closeJoinModal;
window.confirmJoinEvent = confirmJoinEvent;
window.closeShareModal = closeShareModal;
window.showCommentForm = showCommentForm;
window.updateComment = updateComment;
window.confirmDeleteComment = confirmDeleteComment;
window.closeEditCommentModal = closeEditCommentModal;
window.closeDeleteCommentModal = closeDeleteCommentModal;
